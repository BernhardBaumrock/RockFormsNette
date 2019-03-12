<?php namespace ProcessWire;
use \Nette\Forms\Form;
use \Nette\Forms\IControl;
/**
 * Custom Form Renderer for RockFormsNette
 * 
 * This renderer adds the magic of ProcessWire Hooks to Nette Forms
 */
class RockFormsRenderer extends \Nette\Forms\Rendering\DefaultFormRenderer {

  /**
   * Reference to RockFormsNette instance.
   *
   * @var RockFormsNette
   */
  private $rf;

  /**
   * Class constructor
   *
   * @param RockFormsNette $rf
   */
  public function __construct($rf) {
    $this->rf = $rf;
  }

  // public function renderPair(IControl $control): string {
  //   return $this->rf->renderPair($control);
  // }
  // public function renderPairMulti(array $controls): string {
  //   return $this->rf->renderPairMulti($controls);
  // }

  public function render(Form $form, $mode = NULL): string {
    return $this->rf->render($form->name, $form);
  }
}
