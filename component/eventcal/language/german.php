<?php
/**
 * eventCal
 *
 * German language file
 *
 * @version		$Id: german.php 62 2006-09-03 01:34:12Z kay_messers $
 * @package		eventCal
 * @author		Kay Messerschmidt <kay_messers@email.de>
 * @copyright	Copyright (C) 2006 Kay Messerschmidt. All rights reserved.
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link		http://forge.joomla.org/sf/projects/eventcal
 */

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

//Translations for front_end interface
//error messages
define('_UNVALID_ACCESS','Der Event Kalender wurde ungьltig aufgerufen.<br />Der Vorgang wurde abgebrochen!<br />Sollte dieser Fehler erneut auftreten wende Dich bitte an die Administratoren!<br /> Falls mцglich gib uns bitte eine genaue Fehlerbeschreibung, damit wir den Fehler rekonstruieren kцnnen. Danke!');
define('_NO_ACCESS_CAP','Unerlaubter Zugriff');
//define('','');

//redirect-dialog
define('_MISSING_PARAM_CAP','Ungьltiger Vorgang');
define('_MISSING_PARAM_TEXT','Bei der Ьbergabe der Parameter ist ein Fehler aufgetreten.<br />Der Vorgang wurde abgebrochen!');
define('_SUCCESSFULL_ACTION_CAP', 'Aktion Erfolgreich');
define('_SUCCESSFULL_POSTED', _SUBMIT_SUCCESS_DESC);
define('_ACTION_ABORTED_CAP', 'Aktion abgebrochen');

//category-list
define('_CATLIST_HEADER','Kategorien');
define('_SELECT_ALL','Alle auswдhlen');

//Contact Details
define('_CONTACT_CONTACT','Kontaktperson:');
define('_CONTACT_URL','Homepage:');

//editor Form
define('_EDITOR_CAPTION','Eingabeformular');
define('_FORM_START_DATE','Startdatum:');
define('_FORM_END_DATE','Enddatum:');
define('_SUBMIT_BUTTON','eintragen');
define('_RESET_BUTTON','reset');
define('_FORM_EXCEPTIONS','Ausnahmetermine:');
define('_FORM_REPEATCAP','Wiederholung');
define('_FORM_CATEGORY','Kategorie');
define('_EVENT','Veranstaltung');
define('_COUNTER_DESC', 'Anzahl an Wiederholungen (Ausnahmen ausgenommen) eingeben. <i>Enddatum wird ignoriert!</i>');

//repetition types
define('_REP_NONE','gar nicht');
define('_REP_DAYLY','tдglich');
define('_REP_WEEKLY','wцchentlich');
define('_REP_MONTHLY','monatlich');
define('_REP_YEARLY','jдhrlich');

//week view
define('_WEEK_SHOW_VIEW','Wochenьbersicht anzeigen');
define('_WEEK_FROM','Woche vom');
define('_WEEK_TILL','bis zum');
define('_LAST_WEEK','&lt; letzte Woche');
define('_NEXT_WEEK','nдchste Woche &gt;');
define('_WEEK_WEEK','. Woche');

//month view
define('_MONTH_SHOW_VIEW','Monatsьbersicht anzeigen');

//dayly view
define('_DAY_SHOW_VIEW','Tagesьbersicht anzeigen');
define('_DAY_SHOW_NEXT','nдchster Tag &gt;');
define('_DAY_SHOW_LAST','&lt; vorheriger Tag');
define('_DAY_TODAY','heute');
define('_DAY_YESTERDAY','&lt; gestern');
define('_DAY_TOMORROW','morgen &gt;');

//general information
define('_EVENT_CAL','Event Kalender');

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
define('_OVERLIB_STARTDATE', 'Beginn: %x');
define('_OVERLIB_ENDDATE', 'Ende: %x');
define('_OVERLIB_STARTTIME', 'Beginn: %H:%M');
define('_OVERLIB_ENDTIME', 'Ende: %H:%M');
define('_OVERLIB_TIME_FROM', 'von %H:%M');
define('_OVERLIB_TIME_TO', ' bis %H:%M');
define('_OVERLIB_SINGLETIME', ' %H:%M');
define('_OVERLIB_SINGLEDATE', ' %x');
define('_OVERLIB_SINGLETIME_FROM', ' ab %H:%M');

define('_OVERLIB_CALENDAR','dd.mm.y');
define('_NORMAL_DATE_FORMAT','%d.%m.%Y');
define('_NORMAL_TIME_FORMAT','%H:%M');
define ('_DATE_TIME_FORMAT', '14.02.2009 00:31');

//translation for admin-interface
define('_ALL_EVENTS','Alle Veranstaltungen anzeigen');
define('_OLD_EVENTS','Abgelaufene Veranstaltungen anzeigen');

?>
