<?php
//namespace Arminfrey\ContaoGbmBundle\contao;

use Arminfrey\ContaoGbmBundle\ContaoGbmBundle;
use Arminfrey\ContaoGbmBundle\Model\ContaoGbmModel;
use Arminfrey\ContaoGbmBundle\Services\SendMail;

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
 * CRON
 * -------------------------------------------------------------------------
 */
// Daily cron job to send birthday mails
$GLOBALS['TL_CRON']['daily'][] = [SendMail::class, 'sendBirthdayMail'];
