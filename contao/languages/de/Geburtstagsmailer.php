<?php

/**
 * BirthdayMailer defaults
 */
$GLOBALS['TL_LANG']['Geburtstagsmailer']['mail']['default']['salutation']        = 'Lieber';
$GLOBALS['TL_LANG']['Geburtstagsmailer']['mail']['default']['salutation_female'] = 'Sehr geehrte Frau';
$GLOBALS['TL_LANG']['Geburtstagsmailer']['mail']['default']['salutation_male']   = 'Sehr geehrter Herr';
$GLOBALS['TL_LANG']['Geburtstagsmailer']['mail']['default']['subject']           = 'Herzliche Glückwünsche zum Geburtstag';
$GLOBALS['TL_LANG']['Geburtstagsmailer']['mail']['default']['text'] = '
{{birthdaychild::salutation}} {{birthdaychild::firstname}},

ich gratuliere dir ganz herzlich zu deinem {{birthdaychild::age}} Geburtstag. Ich wünsche dir alles Gute, 
Glück und Gesundheit. Außerdem wünsche ich dir, dass du noch lange an unseren Aktivitäten teilnehmen kannst.

Genieße diesen Tag und natürlich auch alle kommenden.

Liebe Grüße, 
{{birthdaymailer::name}} (mailto:{{birthdaymailer::email}})
Vorstand der Krettenweiber- und Narrenbüttelgruppe
';
$GLOBALS['TL_LANG']['Geburtstagsmailer']['mail']['default']['html'] = '
{{birthdaychild::salutation}} <b>{{birthdaychild::firstname}} </b>,
<br/><br/>
ich gratuliere dir ganz herzlich zu deinem {{birthdaychild::age}} Geburtstag. Ich wünsche dir alles Gute, Glück und Gesundheit.
Außerdem wünsche ich dir, dass du noch lange an unseren Aktivitäten teilnehmen kannst.<br/><br/>
Genieße diesen Tag und natürlich auch alle kommenden.
<br/><br/>
Liebe Grüße, <br/>
<a href="mailto:{{birthdaymailer::email}}">{{birthdaymailer::name}}</a> <br/>
Vorstand der Krettenweiber- und Narrenbüttelgruppe
';
