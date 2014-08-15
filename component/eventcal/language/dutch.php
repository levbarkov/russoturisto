<?php
/**
 * eventCal
 *
 * Dutch language file
 *
 * @version		$Id: dutch.php 62 2006-09-03 01:34:12Z kay_messers $
 * @package		eventCal
 * @author		Paul ???
 * @author		Kay Messerschmidt <kay_messers@email.de>
 * @copyright	Copyright (C) 2006 Kay Messerschmidt. All rights reserved.
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link		http://forge.joomla.org/sf/projects/eventcal
 *
 * Many thanks to Paul from the Netherlands for this translation
 */

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

//Translations for front_end interface
//error messages
define('_UNVALID_ACCESS','EventCal heeft een ongeldig verzoek gehad<br />Het proces is afgebroken<br />Indien deze fout weer plaatsvindt, neemt u dan a.u.b. contact op met uw webbeheerder');
define('_NO_ACCESS_CAP','Toegang afgewezen');
//define('','');

//redirect-dialog	
define('_MISSING_PARAM_CAP','Illegaal Proces');
define('_MISSING_PARAM_TEXT','Er heeft een verwerkingsfout plaatsgevonden.<br />Proces is geannuleerd');
define('_SUCCESSFULL_ACTION_CAP', 'Actie succesvol!');
define('_SUCCESSFULL_POSTED', _SUBMIT_SUCCESS_DESC);
define('_ACTION_ABORTED_CAP', 'Actie geannuleerd');

//category-list
define('_CATLIST_HEADER','Categorieлn');
define('_SELECT_ALL','selecteer alles');		

//Contact Details
define('_CONTACT_CONTACT','contactpersoon:');
define('_CONTACT_URL','homepage:');

//editor Form
define('_EDITOR_CAPTION','form');
define('_FORM_START_DATE','startdatum:');
define('_FORM_END_DATE','einddatum:');
define('_SUBMIT_BUTTON','versturen');
define('_RESET_BUTTON','reset');
define('_FORM_EXCEPTIONS','uitgezonderde datums:');
define('_FORM_REPEATCAP','herhaling');
define('_FORM_CATEGORY','categorie');
/***********added in this version, please mail transaltions to kay_messers@web.de*************/
define('_EVENT','Event');
define('_COUNTER_DESC', 'Enter repetition count (Exceptions not counted). <i>Enddate will be ignored</i>');

//repetition types
define('_REP_NONE','never');
define('_REP_DAYLY','dayly');
define('_REP_WEEKLY','weekly');
define('_REP_MONTHLY','monthly');
define('_REP_YEARLY','yearly');
/*********************************************************************************************/

//week view	
define('_WEEK_SHOW_VIEW','weekoverzicht');
define('_WEEK_FROM','week van');
define('_WEEK_TILL','tot');
define('_LAST_WEEK','&lt; vorige week');
define('_NEXT_WEEK','volgende week &gt;');
define('_WEEK_WEEK','. week');		

//month view	
define('_MONTH_SHOW_VIEW','maandoverzicht');

//dayly view	
define('_DAY_SHOW_VIEW','dagoverzicht');
define('_DAY_SHOW_NEXT','volgende &gt;');
define('_DAY_SHOW_LAST','&lt; laatste');
define('_DAY_TODAY','vandaag');
define('_DAY_YESTERDAY','&lt; gisteren');
define('_DAY_TOMORROW','morgen &gt;');

//general information
define('_EVENT_CAL','event calendar');

//Date and Time Conversion Constants:	
define('_DATE_SPLITTER','-');
define('_TIME_SPLITTER',':');
define('_DATE_MONTH_POS', 1); //positie -starten met 0- van de maand is afgeleid van het huidige datum-formaat
define('_DATE_DAY_POS', 0);
define('_DATE_YEAR_POS', 2);

//time-formats for strftime
define('_DAYVIEW_CAPTION','%A, %x');
define('_DAYVIEW_EVENT_START','%H:%M');	
define('_DAYVIEW_EVENT_END',' - %H:%M');
define('_OVERLIB_STARTDATE', 'start: %x');
define('_OVERLIB_ENDDATE', 'eind: %x');
define('_OVERLIB_STARTTIME', 'start: %H:%M');
define('_OVERLIB_ENDTIME', 'eind: %H:%M');
define('_OVERLIB_TIME_FROM', 'van %H:%M');
define('_OVERLIB_TIME_TO', ' tot %H:%M');
define('_OVERLIB_SINGLETIME', ' %H:%M');
define('_OVERLIB_SINGLEDATE', ' %x');
define('_OVERLIB_SINGLETIME_FROM', ' van %H:%M');

define('_OVERLIB_CALENDAR','dd-mm-y');
define('_NORMAL_DATE_FORMAT','%d-%m-%Y');
define('_NORMAL_TIME_FORMAT','%H:%M');
define('_DATE_TIME_FORMAT', '14-02-2009 00:31');

//translation for admin-interface
define('_ALL_EVENTS','overzicht van alle evenementen');
define('_OLD_EVENTS','overzicht van vorige evenementen');

?>