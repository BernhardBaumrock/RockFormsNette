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
  private $nette;

  /**
   * RockFormsNette instance.
   *
   * @var RockFormsNette
   */
  private $rf;

  /**
   * Class constructor.
   *
   * @param string $name
   */
  public function __construct($name, $rf) {
    // create nette form
    $this->nette = new Form($name);
    $this->created($name, $this->nette);

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
    if($method == 'render') {
      // apply custom renderer
      $this->beforeRender($this->nette->name, $this->nette);

      // if the form was submitted we execute the hookable processInput method
      if($this->nette->isSubmitted()) {
        $this->processInput($this->nette->name, $this->nette);
      }
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
  public function ___processInput($name, $form) {}
    
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

    // todo: add honeypots
    $this->addHoneypots($name, $form);

    // apply framework specific styling
    // todo: make this hookable or editable via config setting
    $this->wire->files->include(__DIR__ . "/renderers/uikit2.php", [
      'rf' => $this->rf,
      'form' => $form,
    ]);
  }

  /**
   * Add honeypot fields to this form.
   *
   * @param string $name
   * @param Form $form
   * @return void
   */
  public function ___addHoneypots($name, $form) {
    bd("add honey to $name");
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
