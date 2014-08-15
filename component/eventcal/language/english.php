<?php

defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

//Translations for front_end interface
//error messages
define('_UNVALID_ACCESS','eventCal was called illegaly<br />The process was aborted<br />If this error occurs again please contact your website administrator');
define('_NO_ACCESS_CAP','Access denied');
//define('','');

//redirect-dialog
define('_MISSING_PARAM_CAP','Not allowed Process');
define('_MISSING_PARAM_TEXT','An error occured on passing the params.<br />Process was aborted');
define('_SUCCESSFULL_ACTION_CAP', 'Action successful!');
define('_SUCCESSFULL_POSTED', _SUBMIT_SUCCESS_DESC);
define('_ACTION_ABORTED_CAP', 'Action aborted');

//category-list
define('_CATLIST_HEADER','categories');
define('_SELECT_ALL','Select all');

//Contact Details
define('_CONTACT_CONTACT','contact-person:');
define('_CONTACT_URL','homepage:');

//editor Form
define('_EDITOR_CAPTION','form');
define('_FORM_START_DATE','starting date:');
define('_FORM_END_DATE','ending date:');
define('_SUBMIT_BUTTON','submit');
define('_RESET_BUTTON','reset');
define('_FORM_EXCEPTIONS','exception dates:');
define('_FORM_REPEATCAP','repetition');
define('_FORM_CATEGORY','category');
define('_EVENT','Event');
define('_COUNTER_DESC', 'Enter repetition count (Exceptions not counted). <i>Enddate will be ignored</i>');

//repetition types
define('_REP_NONE','never');
define('_REP_DAYLY','dayly');
define('_REP_WEEKLY','weekly');
define('_REP_MONTHLY','monthly');
define('_REP_YEARLY','yearly');

//week view
define('_WEEK_SHOW_VIEW','show week-view');
define('_WEEK_FROM','week from');
define('_WEEK_TILL','until');
define('_LAST_WEEK','&lt; last week');
define('_NEXT_WEEK','next week &gt;');
define('_WEEK_WEEK','. week');

//month view
define('_MONTH_SHOW_VIEW','show month-view');

//dayly view
define('_DAY_SHOW_VIEW','show day-view');
define('_DAY_SHOW_NEXT','next day &gt;');
define('_DAY_SHOW_LAST','&lt; last day');
define('_DAY_TODAY','today');
define('_DAY_YESTERDAY','&lt; yesterday');
define('_DAY_TOMORROW','tomorrow &gt;');

//general information
define('_EVENT_CAL','event calendar');

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
define('_OVERLIB_STARTDATE', 'start: %x');
define('_OVERLIB_ENDDATE', 'end: %x');
define('_OVERLIB_STARTTIME', 'start: %H:%M');
define('_OVERLIB_ENDTIME', 'end: %H:%M');
define('_OVERLIB_TIME_FROM', 'from %H:%M');
define('_OVERLIB_TIME_TO', ' until %H:%M');
define('_OVERLIB_SINGLETIME', ' %H:%M');
define('_OVERLIB_SINGLEDATE', ' %x');
define('_OVERLIB_SINGLETIME_FROM', ' from %H:%M');

define('_OVERLIB_CALENDAR','mm/dd/y');
define('_NORMAL_DATE_FORMAT','%m/%d/%Y');
define('_NORMAL_TIME_FORMAT','%H:%M');
define ('_DATE_TIME_FORMAT', '02/14/2009 00:31');

//translation for admin-interface
define('_ALL_EVENTS','show all events');
define('_OLD_EVENTS','show expired events');

?>