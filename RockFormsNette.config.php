<?php
/**
 * RockFormsNette Config
 *
 * @author Bernhard Baumrock, 12.03.2019
 * @license Licensed under MIT
 */
$config = [
  'honeypots' => [
    'type' => 'textarea',
    'label' => 'Honeypot Fields',
    'description' => 'After you have checked that everything works add .nettehny {display:none;} to your CSS to hide the honeypot fields',
    'notes' => 'Add one fieldname per line',
    'value' => "message\ncomment",
  ],
];
