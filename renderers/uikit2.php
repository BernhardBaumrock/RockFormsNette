<?php namespace ProcessWire;
use \Nette\Forms\Form;
use \Nette\Utils\Html;

/** @var RockFormsNette $rf */
/** @var Form $form */

$form->getElementPrototype()->addClass('uk-form');

foreach($form->getComponents() as $component) {
  try {
    $component->getContainerPrototype()->addClass($component->name);
  } catch (\Throwable $th) {
    // nothing
  }
  
  switch(true) {
    case $component instanceof \Nette\Forms\Controls\RadioList:
      $component->getControlPrototype()->addClass('uk-radio');
      $component->getSeparatorPrototype()->setName('span');
      $component->getContainerPrototype()->setName('div');
      break;
    case $component instanceof \Nette\Forms\Controls\TextInput:
      $component->getControlPrototype()
        ->addClass('uk-input')
        ->addPlaceholder($component->label->getText())
        ;
      break;
    case $component instanceof \Nette\Forms\Controls\TextArea:
      $component->getControlPrototype()
        ->addClass('uk-textarea')
        ->addPlaceholder($component->label->getText())
        ;
      break;
    case $component instanceof \Nette\Forms\Controls\SubmitButton:
      $component->getControlPrototype()->addClass('uk-button uk-margin-top');
      break;
  }
}
