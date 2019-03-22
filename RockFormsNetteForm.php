<?php namespace ProcessWire;
use \Nette\Forms\Form;
/**
 * This class extends the Nette Forms base class and acts as a proxy for it.
 * You can call all Nette Forms methods directly on this class and also access
 * properties. That way we can extend the forms to our needs (eg custom
 * renderers) and also to add hookable methods to have PW magic in Nette Forms :)
 * 
 * @author Bernhard Baumrock, 13.03.2019
 * @license Licensed under MIT
 * @link https://www.baumrock.com
 */
class RockFormsNetteForm extends Wire {

  /**
   * Nette form instance.
   *
   * @var Form
   */
  public $nette;

  /**
   * RockFormsNette instance.
   *
   * @var RockFormsNette
   */
  public $rf;

  /**
   * Class constructor.
   *
   * @param string $name
   */
  public function __construct($name, $rf) {
    // create nette form
    $form = $this->nette = new Form($name);
    $this->created($name, $form);
    $this->addHoneypots($name, $form, $rf);
    $this->rf = $rf;
  }

  // ########## magic methods to proxy everything to nette forms ##########
  
  /**
   * Proxy for properties.
   *
   * @param mixed $var
   * @return void
   */
  public function __get($var) {
    // if property "wire" is accessed we proxy to the wire instance
    if($var == "wire") return parent::__get($var);
    return $this->nette->{$var};
  }

  /**
   * Proxy for method calls.
   *
   * @param string $method
   * @param array $args
   * @return void
   */
  public function __call($method, $args) {
    if(method_exists($this, "___$method")) return parent::__call($method, $args);
    
    // apply hooks
    elseif($method == 'render') {
      // apply custom renderer
      $this->beforeRender($this->nette->name, $this->nette);
    }

    // call the requested method on the nette form object
    return $this->nette->{$method}(...$args);
  }

  /**
   * Render the form as html markup.
   *
   * @return string
   */
  public function __toString() {
    return (string)$this->render();
  }

  // ########## hookable methods ##########

  /**
   * Process input of submitted form.
   * Does not do anything. Only exists to be hooked.
   *
   * @param string $name
   * @param Form $form
   * @return void
   */
  public function ___processInput($data) {
    return $data;
  }
    
  /**
   * Hook when form is created.
   *
   * @param string $name
   * @param Form $form
   * @return void
   */
  public function ___created($name, $form) {}

  /**
   * Modify the rendering of the form.
   *
   * @param string $name
   * @param Form $form
   * @return void
   */
  public function ___beforeRender($name, $form) {
    
    // save the default renderer for later
    $renderer = $form->getRenderer();
    // $renderer->wrappers['controls']['container'] = null;
    // $renderer->wrappers['pair']['container'] = 'div';
    // $renderer->wrappers['error']['container'] = 'div class="uk-alert-danger" uk-alert';
    // $renderer->wrappers['error']['item'] = 'div';

    $renderer->wrappers['control']['errorcontainer'] = 'span class="uk-alert-danger uk-margin-small-left"';
    $renderer->wrappers['control']['erroritem'] = '';

    // $renderer->wrappers['label']['container'] = 'div class=uk-form-label';
    // $renderer->wrappers['control']['container'] = 'div class="uk-form-controls uk-margin-small"';

    // add class to all forms
    $form->getElementPrototype()->addClass('RockFormsNette');

    // add CSRF protection to this form
    $form->addProtection('Security token has expired, please submit the form again');

    // apply framework specific styling
    // todo: make this hookable or editable via config setting
    $this->wire->files->include(__DIR__ . "/renderers/uikit2.php", [
      'rf' => $this->rf,
      'form' => $form,
    ]);
  }

  /**
   * Render summary of this form (eg in E-Mails).
   *
   * @param array $options
   * @return string
   */
  public function ___renderSummary($options = []) {
    $form = $this->nette;
    $out = '';
    
    // skip
    $skip = @$options['skip'] ?: [];
    $skip = array_merge(['_form_'], $this->rf->getHoneypots(), $skip);

    // loop all controls
    foreach ($form->getControls() as $control) {
      $type = $control->getOption('type');
      $row = '';

      // skip this control?
      if(in_array($control->name, $skip)) continue;

      // add row
      if($type == 'text') {
        if(!$control->value AND @!$options['showEmpty']) continue;
        $row .= "<td style='padding-right: 15px;'>{$control->caption}</td>";
        $row .= "<td>{$control->value}</td>";
      }
      elseif($type == 'checkbox') {
        $row .= "<td colspan=2>" . $control->caption . ": " . ($control->value ? 'Ja' : 'Nein') . "</td>";
      }
      elseif($type == 'radio') {
        $row .= "<td style='padding-right: 15px;'>{$control->caption}</td>";
        $row .= "<td>{$control->items[$control->value]}</td>";
      }
      elseif($type == 'textarea') {
        if(!$control->value AND @!$options['showEmpty']) continue;
        $row .= "<td style='padding-right: 15px; vertical-align: top;'>{$control->caption}</td>";
        $row .= "<td>" . nl2br($control->value) . "</td>";
      }
      else {
        continue;
      }
      $out .= "<tr data-name='{$control->name}'>$row</tr>";
    }

    return "<table>$out</table>";
  }

  /**
   * Add honeypot fields to this form.
   *
   * @param string $name
   * @param Form $form
   * @param RockFormsNette $rf
   * @return void
   */
  public function addHoneypots($name, $form, $rf) {
    // add honeypot fields
    // this is now done manually
    $honeypots = $rf->getHoneypots();
    if(!count($honeypots)) return;

    // add fields
    foreach($honeypots as $item) {
      if(!$item) continue;
      $form->addText($item)
        ->addRule($form::BLANK, __('Leave this field empty!'))
        ->setOmitted(true)
        ->setAttribute('class', 'nettehny')
        ->setAttribute('autocomplete', 'off')
        ;
    };
  }

  // ########## other ##########

  /**
   * debugInfo
   *
   * @return array
   */
  public function __debugInfo() {
    return (array)$this->nette;
  }
}
