<?php

use Arminfrey\ContaoGbmBundle\ContaoGbmBundle;
use Arminfrey\ContaoGbmBundle\Model\ContaoGbmModel;
//use Arminfrey\ContaoGbmBundle\contao\classes\SendMail;

/**
 * -------------------------------------------------------------------------
 * BACK END MODULES
 * -------------------------------------------------------------------------
 */

// Add configuration to Backend
$GLOBALS['BE_MOD']['Geburtstagsmail']['Geburtstagsmail'] = [
	'tables'		=> ['tl_geburtstagsmail'],
	'icon'             => \dirname(__DIR__) . '/../assets/icon.png',
	'sendBirthdayMail'	=> [\dirname(__DIR__) . /../contao/classes/SendMail::class, 'sendBirthdayMailManually']
];


$GLOBALS['TL_MODELS']['tl_geburtstagsmail'] = ContaoGbmModel::class;

/**
 * -------------------------------------------------------------------------
 * CRON
 * -------------------------------------------------------------------------
 */
// Daily cron job to send birthday mails
$GLOBALS['TL_CRON']['daily'][] = [SendMail::class, 'sendBirthdayMail'];
