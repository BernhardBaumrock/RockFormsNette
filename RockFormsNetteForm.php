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

  private $form;

  /**
   * Class constructor.
   *
   * @param string $name
   */
  public function __construct($name) {
    // create nette form
    $this->nette = new Form($name);
  }
  
  /**
   * Proxy for properties.
   *
   * @param mixed $var
   * @return void
   */
  public function __get($var) {
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
    return $this->nette->{$method}(...$args);
  }

  /**
   * Render the form as html markup.
   *
   * @return string
   */
  public function __toString() {
    return (string)$this->nette;
  }
}
