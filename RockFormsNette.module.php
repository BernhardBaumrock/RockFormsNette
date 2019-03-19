<?php namespace ProcessWire;
use \Nette\Forms\Form;
use \Nette\Utils\Html;
/**
 * RockFormsNette
 *
 * @author Bernhard Baumrock, 12.03.2019
 * @license Licensed under MIT
 */
require_once('vendor/autoload.php');
require_once('RockFormsNetteForm.php');
class RockFormsNette extends WireData implements Module {

  /**
   * Array of all forms.
   *
   * @var WireArray
   */
  public $forms;

  /**
   * Initialize the module (optional)
   */
  public function init() {
    $this->forms = $this->wire(new WireArray);
  }

  /**
   * Get all honeypot fields as array.
   *
   * @return array
   */
  public function getHoneypots() {
    return explode("\n", $this->honeypots);
  }

  /**
   * API is ready.
   *
   * @return void
   */
  public function ready() {
    // apply form renderer early
    // $this->addHookBefore("render", $this, "applyFormRenderer", ['priority' => 50]);

    // apply hookable processInput method right before rendering
    $this->addHookBefore("render", function($event) {
      $this->processInput(
        $event->arguments(0), // form name
        $event->arguments(1) // form
      );
    }, ['priority' => 999]);
  }

  // /**
  //  * Apply renderer to this form.
  //  *
  //  * @param HookEvent $event
  //  * @return void
  //  */
  // public function applyFormRenderer($event) {
  //   $form = $event->arguments(1);
  //   $this->files->include(__DIR__ . "/renderers/uikit2.php", [
  //     'rf' => $this,
  //     'form' => $form,
  //   ]);
  // }

  /**
   * Create and add a new form.
   * 
   * This will try to load a file located at /site/assets/RockFormsNette/$name.php
   * You can also specify a custom file as second argument.
   *
   * @param string $name
   * @param string $file
   * @param array $properties
   * Here you can set runtime properties for this form. This can be necessary
   * if you use forms in other modules. Eg RockCommerce needs to apply the current
   * module instance to the form so that it is accessible in hooks.
   * 
   * @return void
   */
  public function addForm($name = null, $file = null, $properties = []) {
    if(!$name) $name = uniqid();
    
    // check if a form with the current name already exists
    if($this->getForm($name)) throw new WireException("A form with name $name already exists");

    // create form
    $form = new RockFormsNetteForm($name, $this);

    // apply runtime properties
    foreach($properties as $k=>$v) $form->{$k} = $v;

    // load the form setup file
    if(is_file($file)) $this->files->include($file, ['rf' => $this, 'form' => $form]);
    elseif($file) {
      // a file was specified but not found
      throw new WireException("File $file not found");
    }

    // save form to the forms array
    $this->forms->add($form);

    // return the created form
    return $form;
  }

  /**
   * Get form with given name.
   *
   * @param string $name
   * @return void
   */
  public function getForm($name = null) {
    if(!$name) return $this->forms->first();
    foreach($this->forms as $form) {
      if($form->name == $name) return $form;
    }
    return false;
  }

  /**
   * debugInfo
   *
   * @return array
   */
  public function __debugInfo() {
    return [
      'forms' => $this->forms,
    ];
  }
}
