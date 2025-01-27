<?php
//namespace Arminfrey\ContaoGbmBundle\contao;

use Arminfrey\ContaoGbmBundle\ContaoGbmBundle;
use Arminfrey\ContaoGbmBundle\Model\ContaoGbmModel;
use Arminfrey\ContaoGbmBundle\SendMail\SendMail;

/**
 * -------------------------------------------------------------------------
 * BACK END MODULES
 * -------------------------------------------------------------------------
 */

// Add configuration to Backend
$GLOBALS['BE_MOD']['Geburtstagsmail']['Geburtstagsmail'] = [
	'tables'		=> ['tl_geburtstagsmail'],
	'icon'             	=> '/../assets/icon.png',
	'sendBirthdayMail'	=> [SendMail::class, 'sendBirthdayMailManually']
];


$GLOBALS['TL_MODELS']['tl_geburtstagsmail'] = ContaoGbmModel::class;

/**
 * -------------------------------------------------------------------------
 * CRON vor 4.13
 * -------------------------------------------------------------------------
$GLOBALS['TL_CRON']['daily'][] = [SendMail::class, 'sendBirthdayMail']; */
