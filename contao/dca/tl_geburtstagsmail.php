<?php

use Contao\Backend;
use Contao\DataContainer;
use Contao\DC_Table;
use Contao\Image;

$GLOBALS['TL_DCA']['tl_geburtstagsmail'] = [
	// Config
	'config' => [
		'dataContainer'           => DC_Table::class,
		'enableVersioning'        => true,
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary',
			)
		)
	],
	// List
	'list' => [
		'sorting' => [
			'panelLayout'           => 'filter,limit',
			'fields'                => ['priority'],
			'flag'                  => 2,
			'mode'                  => 1,
			'disableGrouping'       => true
			]
	],
	'label' => [
		'fields'                => ['MemberGroup:tl_member_group.name', 'priority'],
		//'format'                => '%s <span style="color:#b3b3b3; padding-left:3px;">[' . $GLOBALS['TL_LANG']['tl_birthdaymail']['priority'][0] . ': %s]</span>',
		'label_callback'        => ['tl_geburtstagsmail', 'addIcon'] 
	],
	'global_operations' => [
		'sendBirthdayMail' => [
			'label'               => &$GLOBALS['TL_LANG']['tl_geburtstagsmail']['sendBirthdayMail'],
			'href'                => 'key=sendBirthdayMail',
			'attributes'          => 'onclick="Backend.getScrollOffset();" style="background: url(src//assets/sendBirthdayMail.png) no-repeat scroll left center transparent; margin-left: 15px; padding: 2px 0 3px 20px;"'
		],
		'all' => [
			'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
			'href'                => 'act=select',
			'class'               => 'header_edit_all',
			'attributes'          => 'onclick="Backend.getScrollOffset();"'
		]
	],
	'operations' => [
		'edit' => [
			'label'               => &$GLOBALS['TL_LANG']['tl_geburtstagsmail']['edit'],
			'href'                => 'act=edit',
			'icon'                => 'edit.gif'
		],
		'copy' => [
			'label'               => &$GLOBALS['TL_LANG']['tl_geburtstagsmail']['copy'],
			'href'                => 'act=copy',
			'icon'                => 'copy.gif'
		],
		'delete' => [
			'label'               => &$GLOBALS['TL_LANG']['tl_geburtstagsmail']['delete'],
			'href'                => 'act=delete',
			'icon'                => 'delete.gif',
			'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
		],
		'show' => [
			'label'               => &$GLOBALS['TL_LANG']['tl_geburtstagsmail']['show'],
			'href'                => 'act=show',
			'icon'                => 'show.gif'
		]
	],
  	// Palettes
	'palettes' => [
		'__selector__' => ['mailUseCustomText'],
		'default'      => '{config_legend},memberGroup,priority;{email_legend},sender,senderName,mailCopy,mailBlindCopy,mailUseCustomText'
	],
	// Subpalettes
	'subpalettes' => ['mailUseCustomText' => 'mailTextKey'],
	// Fields
	'fields' => [
 		'id' => [
 			'sql'		=> "int(10) unsigned NOT NULL auto_increment"
		],
		'tstamp' => [
			'sql'		=> "int(10) unsigned NOT NULL default '0'"
		],
		'memberGroup' => [
			'label'		=> &$GLOBALS['TL_LANG']['tl_geburtstagsmail']['memberGroup'],
			'exclude'	=> true,
			'inputType'	=> 'select',
			'foreignKey'	=> 'tl_member_group.name',
			'filter'	=> true,
			'eval'		=> ['mandatory'=>true, 'unique'=>true, 'includeBlankOption'=>true, 'tl_class'=>'w50'],
			'sql'		=> "int(10) unsigned NOT NULL default '0'"
		],
		'priority' => [
			'label'		=> &$GLOBALS['TL_LANG']['tl_geburtstagsmail']['priority'],
			'exclude'	=> true,
			'inputType'	=> 'text',
			'eval'		=> array('rgxp' => 'digit','maxlength'=>10, 'tl_class'=>'w50'),
			'sql'		=> "int(10) unsigned NOT NULL default '0'"
		],
		'sender' => [
			'label'		=> &$GLOBALS['TL_LANG']['tl_geburtstagsmail']['sender'],
			'exclude'	=> true,
			'inputType'	=> 'text',
			'eval'		=> ['mandatory'=>true, 'rgxp' => 'email','maxlength'=>128, 'tl_class'=>'w50'],
			'sql'		=> "varchar(128) NOT NULL default ''"
		],
		'senderName' => [
			'label'		=> &$GLOBALS['TL_LANG']['tl_geburtstagsmail']['senderName'],
			'exclude'	=> true,
			'inputType'	=> 'text',
			'eval'		=> array('rgxp' => 'extnd','maxlength'=>128, 'tl_class'=>'w50'),
			'sql'		=> "varchar(128) NOT NULL default ''"
		],
		'mailCopy' => [
			'label'		=> &$GLOBALS['TL_LANG']['tl_geburtstagsmail']['mailCopy'],
			'exclude'	=> true,
			'inputType'	=> 'text',
			'eval'		=> ['rgxp' => 'emails','maxlength'=>255, 'tl_class'=>'w50'],
			'sql'		=> "varchar(255) NOT NULL default ''"
		],
		'mailBlindCopy' => [
			'label'		=> &$GLOBALS['TL_LANG']['tl_geburtstagsmail']['mailBlindCopy'],
			'exclude'	=> true,
			'inputType'	=> 'text',
			'eval'		=> ['rgxp' => 'emails','maxlength'=>255, 'tl_class'=>'w50'],
			'sql'		=> "varchar(255) NOT NULL default ''"
		],
		'mailUseCustomText' => [
			'label'		=> &$GLOBALS['TL_LANG']['tl_geburtstagsmail']['mailUseCustomText'],
			'exclude'	=> true,
			'inputType'	=> 'checkbox',
			'eval'		=> array('tl_class'=>'w50', 'submitOnChange'=>true),
			'sql'		=> "char(1) NOT NULL default ''"
		],
		'mailTextKey' => [
			'label'		=> &$GLOBALS['TL_LANG']['tl_geburtstagsmail']['mailTextKey'],
			'exclude'	=> true,
			'inputType'	=> 'text',
			'eval'		=> array('mandatory'=>true, 'maxlength'=>20, 'spaceToUnderscore'=>true, 'tl_class'=>'w50'),
			'sql'		=> "varchar(20) NOT NULL default ''"
		]
	]
];


class tl_geburtstagsmail extends Backend
{
	public function addIcon($row, $label)
	{
		if (empty($row)) {
    			\Contao\Log::add('Row data is empty for icon generation.', 'ContaoGbmBundle addIcon()', TL_ERROR);
		}
    		// Check that row has necessary keys
    		if (!isset($row['memberGroup'], $row['start'], $row['stop'], $row['disable'])) {
        		return $label; // Or handle appropriately
    		}

    		$image = 'mgroup';
    		$disabled = ($row['start'] !== '' && $row['start'] > time()) || ($row['stop'] !== '' && $row['stop'] <= time());

    		if ($disabled || $row['disable']) {
			$image .= '--disabled';
    		}

    		return sprintf('<div class="list_icon" style="background-image:url(\'%s\')" data-icon="%s" data-icon-disabled="%s">%s</div>',
        	Image::getUrl($image),
        	Image::getUrl($image),
        	Image::getUrl($image . '--disabled'),
        	$label
    		);
	}
}
