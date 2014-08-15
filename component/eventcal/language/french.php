<?php
/**
 * eventCal
 *
 * French language file
 *
 * @version		$Id: french.php 62 2006-09-03 01:34:12Z kay_messers $
 * @package		eventCal
 * @author		Michael Ulrich
 * @author		Kay Messerschmidt <kay_messers@email.de>
 * @copyright	Copyright (C) 2006 Kay Messerschmidt. All rights reserved.
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link		http://forge.joomla.org/sf/projects/eventcal
 *
 * Thanks to Michael Ulrich for this translation
 */

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

//Translations for front_end interface
//error messages
define('_UNVALID_ACCESS','Der Event Kalender wurde ungltig aufgerufen.<br />Der Vorgang wurde abgebrochen!<br />Sollte dieser Fehler erneut auftreten wende Dich bitte an die Administratoren!<br /> Falls mпїЅlich gib uns bitte eine genaue Fehlerbeschreibung, damit wir den Fehler rekonstruieren kпїЅnen. Danke!');
define('_NO_ACCESS_CAP','AccГ©s non autorises');
//define('','');

//redirect-dialog
define('_MISSING_PARAM_CAP','proces interdit');
define('_MISSING_PARAM_TEXT','Il\'y Г  un problГЁme avec les paramГЁtres.<br />Le procГ©s est finit!');
define('_SUCCESSFULL_ACTION_CAP', 'SuccГ©s');
define('_SUCCESSFULL_POSTED', _SUBMIT_SUCCESS_DESC);
define('_ACTION_ABORTED_CAP', 'Echec');

//category-list
define('_CATLIST_HEADER','categories');
define('_SELECT_ALL','choisi tout');

//Contact Details
define('_CONTACT_CONTACT','personne Г  contacter:');
define('_CONTACT_URL','site personnel:');

//editor Form
define('_EDITOR_CAPTION','formulaire');
define('_FORM_START_DATE','date dГ©but:');
define('_FORM_END_DATE','date fin:');
define('_SUBMIT_BUTTON','eintragen');
define('_RESET_BUTTON','reset');
define('_FORM_EXCEPTIONS','dates exceptionel:');
define('_FORM_REPEATCAP','repetition');
define('_FORM_CATEGORY','categorie');

//week view
define('_WEEK_SHOW_VIEW','voi une semaine');
define('_WEEK_FROM','semaine de');
define('_WEEK_TILL',' Г  ');
define('_LAST_WEEK','&lt; sГ©maine derniere');
define('_NEXT_WEEK','semaine prochaine &gt;');
define('_WEEK_WEEK','. semaine');

//month view
define('_MONTH_SHOW_VIEW','voi une mois');

//dayly view
define('_DAY_SHOW_VIEW','afficher journГ©e en cours');
define('_DAY_SHOW_NEXT','afficher demain &gt;');
define('_DAY_SHOW_LAST','&lt; afficher hier');
define('_DAY_TODAY','aujourd-hui');
define('_DAY_YESTERDAY','&lt; hier');
define('_DAY_TOMORROW','demain &gt;');

//general information
define('_EVENT_CAL','Calendrier des Г©venements');

//Date and Time Conversion Constants:
define('_DATE_SPLITTER','.');
define('_TIME_SPLITTER',':');
define('_DATE_MONTH_POS', 1); //position -beginning with 0- the month is situated at in the current date-format
define('_DATE_DAY_POS', 0);
define('_DATE_YEAR_POS', 2);

//time-formats for strftime
define('_DAYVIEW_CAPTION','%A, %x');
define('_DAYVIEW_EVENT_START','%H:%M');
define('_DAYVIEW_EVENT_END',' - %H:%M');
define('_OVERLIB_STARTDATE', 'dГ©but: %x');
define('_OVERLIB_ENDDATE', 'fin: %x');
define('_OVERLIB_STARTTIME', 'dГ©but: %H:%M');
define('_OVERLIB_ENDTIME', 'fin: %H:%M');
define('_OVERLIB_TIME_FROM', 'de %H:%M');
define('_OVERLIB_TIME_TO', ' Г  %H:%M');
define('_OVERLIB_SINGLETIME', ' %H:%M');
define('_OVERLIB_SINGLEDATE', ' %x');
define('_OVERLIB_SINGLETIME_FROM', ' ab %H:%M');

define('_OVERLIB_CALENDAR','dd/mm/y');
define('_NORMAL_DATE_FORMAT','%d.%m.%Y');
define('_NORMAL_TIME_FORMAT','%H:%M');
define('_DATE_TIME_FORMAT', '14/02/2009 00:31');

//translation for admin-interface
define('_ALL_EVENTS','afficher toutes les Г©vГ©nements');
define('_OLD_EVENTS','Abgelaufene Veranstaltungen anzeigen');

?>