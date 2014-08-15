<?php
// no direct access
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

//Translations for front_end interface
//error messages
define('_UNVALID_ACCESS','eventCal was called illegally<br />The process was aborted<br />If this error occurs again please contact your website administrator');
define('_NO_ACCESS_CAP','Access denied');
//define('','');

//redirect-dialog
define('_MISSING_PARAM_CAP','Not allowed Process');
define('_MISSING_PARAM_TEXT','An error occured on passing the params.<br />Process was aborted');
define('_SUCCESSFULL_ACTION_CAP', 'Action successful!');
define('_SUCCESSFULL_POSTED', _SUBMIT_SUCCESS_DESC);
define('_ACTION_ABORTED_CAP', 'Action aborted');

//category-list
define('_CATLIST_HEADER','категории');
define('_SELECT_ALL','Выбрать все');

//Contact Details
define('_CONTACT_CONTACT','контактное лицо:');
define('_CONTACT_URL','дом.страница:');

//editor Form
define('_EDITOR_CAPTION','форма');
define('_FORM_START_DATE','дата начала:');
define('_FORM_END_DATE','дата конца:');
define('_EC_SUBMIT_BUTTON','сохранить');
define('_RESET_BUTTON','сбросить');
define('_FORM_EXCEPTIONS','исключая даты:');
define('_FORM_REPEATCAP','повтор');
define('_FORM_CATEGORY','категория');
define('_EVENT','Событие');
define('_COUNTER_DESC', 'Введение количество повторов (исключения не считаются). <i>Конечная дата игнорируется</i>');

//repetition types
define('_REP_NONE','никогда');
define('_REP_DAYLY','ежедневно');
define('_REP_WEEKLY','еженедельно');
define('_REP_MONTHLY','ежемесячно');
define('_REP_YEARLY','ежегодно');
define('_REP_EVENT1','Повтор события:');
define('_REP_EVENT2','');

//week view
define('_WEEK_SHOW_VIEW','показать неделю');
define('_WEEK_FROM','неделя с');
define('_WEEK_TILL','до');
define('_LAST_WEEK','&lt; предыдущая неделя');
define('_NEXT_WEEK','следующая неделя &gt;');
define('_WEEK_WEEK','. неделя');

//month view
define('_MONTH_SHOW_VIEW','показать месяц');

//dayly view
define('_DAY_SHOW_VIEW','показать день');
define('_DAY_SHOW_NEXT','следующий день &gt;');
define('_DAY_SHOW_LAST','&lt; предыдущий день');
define('_DAY_TODAY','сегодня');
define('_DAY_YESTERDAY','&lt; вчера');
define('_DAY_TOMORROW','завтра &gt;');

//general information
define('_EVENT_CAL','Календарь событий');

//Date and Time Conversion Constants:
define('_DATE_SPLITTER','/');
define('_TIME_SPLITTER',':');
define('_DATE_MONTH_POS', 0); //position -beginning with 0- the month is situated at in the current date-format
define('_DATE_DAY_POS', 1);
define('_DATE_YEAR_POS', 2);

//time-formats for strftime
define('_DAYVIEW_CAPTION','%A, %x');
define('_DAYVIEW_EVENT_START','%H:%M');
define('_DAYVIEW_EVENT_END',' - %H:%M');
define('_OVERLIB_STARTDATE', 'с: %x');
define('_OVERLIB_ENDDATE', 'по: %x');
define('_OVERLIB_STARTTIME', 'с: %H:%M');
define('_OVERLIB_ENDTIME', 'по: %H:%M');
define('_OVERLIB_TIME_FROM', 'с %H:%M');
define('_OVERLIB_TIME_TO', ' до %H:%M');
define('_OVERLIB_SINGLETIME', ' %H:%M');
define('_OVERLIB_SINGLEDATE', ' %x');
define('_OVERLIB_SINGLETIME_FROM', ' с %H:%M');

define('_OVERLIB_CALENDAR','mm/dd/y');
define('_NORMAL_DATE_FORMAT','%m/%d/%Y');
define('_NORMAL_TIME_FORMAT','%H:%M');
define ('_DATE_TIME_FORMAT', '02/14/2009 00:31');

//translation for admin-interface
define('_ALL_EVENTS','показать все события');
define('_OLD_EVENTS','показать прошедшие события');

?>