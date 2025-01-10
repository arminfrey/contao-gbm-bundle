<?php


/**
 * BirthdayMailer defaults
 */
$GLOBALS['TL_LANG']['BirthdayMailer']['mail']['default']['salutation']        = 'Dear Ms/Mr';
$GLOBALS['TL_LANG']['BirthdayMailer']['mail']['default']['salutation_female'] = 'Dear Ms'; 
$GLOBALS['TL_LANG']['BirthdayMailer']['mail']['default']['salutation_male']   = 'Dear Mr';
$GLOBALS['TL_LANG']['BirthdayMailer']['mail']['default']['subject']           = 'Happy Birthday';
$GLOBALS['TL_LANG']['BirthdayMailer']['mail']['default']['text'] = '
{{birthdaychild::salutation}} {{birthdaychild::name}},

as a member of group {{birthdaychild::groupname}}, we heartily congratulate you on your {{birthdaychild::age}}th birthday and
wish you all the very best, good luck and health in particular.
Enjoy your very special day.

Best regards, {{birthdaymailer::name}} (mailto:{{birthdaymailer::email}})
';
$GLOBALS['TL_LANG']['BirthdayMailer']['mail']['default']['html'] = '
{{birthdaychild::salutation}} <b>{{birthdaychild::firstname}} {{birthdaychild::lastname}}</b>,
<br/><br/>
as a member of group <i>{{birthdaychild::groupname}}</i>, we heartily congratulate you on your {{birthdaychild::age}}th birthday and
wish you all the very best, good luck and health in particular.<br/>
Enjoy your very special day.
<br/><br/>
Best regards, <a href="mailto:{{birthdaymailer::email}}">{{birthdaymailer::name}}</a>
';

?>
