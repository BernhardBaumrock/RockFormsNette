<?php namespace ProcessWire;
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

  /**
   * Renders single visual row.
   */
  public function renderPair(IControl $control): string {
    return $this->rf->render(__FUNCTION__, $control);
  }

  /**
   * Renders single visual row of multiple controls.
   * @param IControl[]  $controls
   */
  public function renderPairMulti(array $controls): string {
    return parent::renderPairMulti($controls);
  }
}
