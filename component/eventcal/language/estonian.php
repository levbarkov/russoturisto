<?php
/**
 * eventCal
 *
 * Estonian language file
 *
 * @version		$Id: estonian.php 62 2006-09-03 01:34:12Z kay_messers $
 * @package		eventCal
 * @author		Martin Tigasson <martin.tigasson@gmail.com>
 * @copyright	Copyright (C) 2006 Kay Messerschmidt. All rights reserved.
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link		http://forge.joomla.org/sf/projects/eventcal
 *
 * Thanks to Martin Tigasson for this translation
 */

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

//Translations for front_end interface
//error messages
define('_UNVALID_ACCESS','EventCal laeti valesti<br />Toiming kaktkestati<br />Kui see peaks veel juhtuma, siis vхtke ьhendust administraatoriga');
define('_NO_ACCESS_CAP','Juurdepддs keelatud');
//define('','');

//redirect-dialog
define('_MISSING_PARAM_CAP','Toiming pole lubatud');
define('_MISSING_PARAM_TEXT','Parameetrite lдbitццtamine ebaхnnestus.<br />Toiming katkestati');
define('_SUCCESSFULL_ACTION_CAP', 'Toiming edukas!');
define('_SUCCESSFULL_POSTED', _SUBMIT_SUCCESS_DESC);
define('_ACTION_ABORTED_CAP', 'Toiming katkestatud');

//category-list
define('_CATLIST_HEADER','Kategooriad');
define('_SELECT_ALL','Vali kхik');

//Contact Details
define('_CONTACT_CONTACT','Kontaktisik:');
define('_CONTACT_URL','Kodulehekьlg:');

//editor Form
define('_EDITOR_CAPTION','Vorm');
define('_FORM_START_DATE','Alguskuupдev:');
define('_FORM_END_DATE','Lхppkuupдev:');
define('_SUBMIT_BUTTON','Salvesta');
define('_RESET_BUTTON','Algseis');
define('_FORM_EXCEPTIONS','Vдljaarvatud kuupдevad:');
define('_FORM_REPEATCAP','Kordus');
define('_FORM_CATEGORY','Kategooria');
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
define('_WEEK_SHOW_VIEW','Nдdal');
define('_WEEK_FROM','nдdal alates');
define('_WEEK_TILL','kuni');
define('_LAST_WEEK','&lt; eelmine nдdal');
define('_NEXT_WEEK','jдrmine nдdal &gt;');
define('_WEEK_WEEK','. nдdal');

//month view
define('_MONTH_SHOW_VIEW','Kuu');

//dayly view
define('_DAY_SHOW_VIEW','Pдev');
define('_DAY_SHOW_NEXT','jдrgmine pдev &gt;');
define('_DAY_SHOW_LAST','&lt; eelmine pдev');
define('_DAY_TODAY','today');
define('_DAY_YESTERDAY','&lt; eile');
define('_DAY_TOMORROW','homme &gt;');

//general information
define('_EVENT_CAL','Kalender');

//Date and Time Conversion Constants:
define('_DATE_SPLITTER','-');
define('_TIME_SPLITTER',':');
define('_DATE_MONTH_POS', 1); //position -beginning with 0- the month is situated at in the current date-format
define('_DATE_DAY_POS', 2);
define('_DATE_YEAR_POS', 0);

//time-formats for strftime
define('_DAYVIEW_CAPTION','%A, %x');
define('_DAYVIEW_EVENT_START','%H:%M');
define('_DAYVIEW_EVENT_END',' - %H:%M');
define('_OVERLIB_STARTDATE', 'algus: %x');
define('_OVERLIB_ENDDATE', 'lхpp: %x');
define('_OVERLIB_STARTTIME', 'algus: %H:%M');
define('_OVERLIB_ENDTIME', 'lхpp: %H:%M');
define('_OVERLIB_TIME_FROM', 'alates %H:%M');
define('_OVERLIB_TIME_TO', ' kuni %H:%M');
define('_OVERLIB_SINGLETIME', ' %H:%M');
define('_OVERLIB_SINGLEDATE', ' %x');
define('_OVERLIB_SINGLETIME_FROM', ' alates %H:%M');

define('_OVERLIB_CALENDAR','y-mm-dd');
define('_NORMAL_DATE_FORMAT','%Y-%m-%d');
define('_NORMAL_TIME_FORMAT','%H:%M');
define ('_DATE_TIME_FORMAT', '2009-02-14 00:31');

//translation for admin-interface
define('_ALL_EVENTS','Nдita kхiki sьndmusi');
define('_OLD_EVENTS','Nдita aegunud sьndmusi');

?>