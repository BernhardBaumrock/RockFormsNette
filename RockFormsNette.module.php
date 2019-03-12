<?php namespace ProcessWire;
use \Nette\Forms\Form;
/**
 * RockFormsNette
 *
 * @author Bernhard Baumrock, 12.03.2019
 * @license Licensed under MIT
 */
require_once('vendor/autoload.php');
require_once('RockFormsRenderer.php');
class RockFormsNette extends WireData implements Module {

  public $name;

  /**
   * Array of all forms.
   *
   * @var WireArray
   */
  public $forms;

  /**
   * Default Nette Forms Renderer
   */
  private $defaultRenderer;

  /**
   * Initialize the module (optional)
   */
  public function init() {
    $this->forms = $this->wire(new WireArray);
  }

  /**
   * Create and add a new form.
   * 
   * This will try to load a file located at /site/assets/RockFormsNette/$name.php
   * You can also specify a custom file as second argument.
   *
   * @param string $name
   * @param string $file
   * @return void
   */
  public function addForm($name = null, $file = null) {
    if(!$name) $name = uniqid();
    
    // check if a form with the current name already exists
    if($this->getForm($name)) throw new WireException("A form with name $name already exists");

    // create form
    $form = new Form($name);
    $this->defaultRenderer = $form->getRenderer();
    $form->setRenderer(new RockFormsRenderer($this));
    $form->addProtection('Security token has expired, please submit the form again');

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

  public function ___render($method, ...$variables): string {
    return $this->defaultRenderer->{$method}(...$variables);
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
