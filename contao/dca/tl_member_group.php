<?php

use Arminfrey\ContaoGbmBundle;

/**
 * Delete an according BirthdayMailer configuration, if the member group is deleted.
 */
$GLOBALS['TL_DCA']['tl_member_group']['config']['ondelete_callback'][] = ['SendMail', 'deleteConfiguration'];

?>
