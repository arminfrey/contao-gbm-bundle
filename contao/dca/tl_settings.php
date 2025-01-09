<?php

/**
 * Add to palette
 */
$GLOBALS['TL_DCA']['tl_settings']['palettes']['__selector__'][] = 'birthdayMailerDeveloperMode';
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{birthdayMailer_legend},birthdayMailerAllowDuplicates, birthdayMailerLogDebugInfo, birthdayMailerDeveloperMode;';
$GLOBALS['TL_DCA']['tl_settings']['subpalettes']['birthdayMailerDeveloperMode'] = 'birthdayMailerDeveloperModeEmail, birthdayMailerDeveloperModeIgnoreDate';


var_dump("add fields");
/**
 * Add fields
 */
$GLOBALS['TL_DCA']['tl_settings']['fields']['birthdayMailerAllowDuplicates'] = [
	'label'     => &$GLOBALS['TL_LANG']['tl_settings']['birthdayMailerAllowDuplicates'],
	'inputType' => 'checkbox',
	'eval'      => ['tl_class'=>'w50']
];

$GLOBALS['TL_DCA']['tl_settings']['fields']['birthdayMailerLogDebugInfo'] = [
	'label'     => &$GLOBALS['TL_LANG']['tl_settings']['birthdayMailerLogDebugInfo'],
	'inputType' => 'checkbox',
	'eval'      => ['tl_class'=>'w50']
];

$GLOBALS['TL_DCA']['tl_settings']['fields']['birthdayMailerDeveloperMode'] = [
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['birthdayMailerDeveloperMode'],
	'inputType'               => 'checkbox',
	'eval'                    => ['submitOnChange'=>true, 'tl_class'=>'w50 clr']
];

$GLOBALS['TL_DCA']['tl_settings']['fields']['birthdayMailerDeveloperModeEmail'] = [
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['birthdayMailerDeveloperModeEmail'],
	'inputType'               => 'text',
	'eval'                    => ['mandatory'=>true, 'rgxp'=>'email', 'tl_class'=>'w50 clr']
];

$GLOBALS['TL_DCA']['tl_settings']['fields']['birthdayMailerDeveloperModeIgnoreDate'] = [
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['birthdayMailerDeveloperModeIgnoreDate'],
	'inputType'               => 'checkbox',
	'eval'                    => ['tl_class'=>'w50']
];
