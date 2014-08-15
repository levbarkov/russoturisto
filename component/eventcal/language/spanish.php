<?php
/**
 * eventCal
 *
 * Spanish language file
 *
 * @version		$Id: spanish.php 62 2006-09-03 01:34:12Z kay_messers $
 * @package		eventCal
 * @author		Matias Garcia De Weert
 * @author		Kay Messerschmidt <kay_messers@email.de>
 * @copyright	Copyright (C) 2006 Kay Messerschmidt. All rights reserved.
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link		http://forge.joomla.org/sf/projects/eventcal
 *
 * Thanks to Matias Garcia De Weert for this translation
 */

// no direct access
defined( '_VALID_MOS' ) or die( 'Acceso restringido' );

//Translations for front_end interface
//error messages
define('_UNVALID_ACCESS','Se intento acceder de manera ilegal<br />El proceso fue abortado<br />Por favor si este error ocurre nuevamente contacte al administrador');
define('_NO_ACCESS_CAP','Acceso denegado');
//define('','');

//redirect-dialog
define('_MISSING_PARAM_CAP','Proceso no permitido');
define('_MISSING_PARAM_TEXT','Ocurrio un error con el envio.<br />El proceso fue abortado');
define('_SUCCESSFULL_ACTION_CAP', 'Envio satisfactorio!');
define('_SUCCESSFULL_POSTED', _SUBMIT_SUCCESS_DESC);
define('_ACTION_ABORTED_CAP', 'Accion abortada');

//category-list
define('_CATLIST_HEADER','categorias');
define('_SELECT_ALL','Ver todo tipo de eventos');

//Contact Details
define('_CONTACT_CONTACT','nombre:');
define('_CONTACT_URL','pagina:');

//editor Form
define('_EDITOR_CAPTION','Envie su evento');
define('_FORM_START_DATE','Fecha de comienzo:');
define('_FORM_END_DATE','Fecha de finalizacion:');
define('_SUBMIT_BUTTON','Enviar');
define('_RESET_BUTTON','Resetear');
define('_FORM_EXCEPTIONS','Fechas a exceptuar (ej. cerrado x vacaciones):');
define('_FORM_REPEATCAP','Repeticion');
define('_FORM_CATEGORY','Categoria');
define('_EVENT','Opciones');
define('_COUNTER_DESC', 'Ingresa las veces a repetir (Excepciones no contadas). <i>La fecha de finalizacion sera ignorada</i>');

//repetition types
define('_REP_NONE','nunca');
define('_REP_DAYLY','diariamente');
define('_REP_WEEKLY','semanalmente');
define('_REP_MONTHLY','mensualmente');
define('_REP_YEARLY','anualmente');

//week view
define('_WEEK_SHOW_VIEW','mostrar mensualmente');
define('_WEEK_FROM','semana desde');
define('_WEEK_TILL','hasta');
define('_LAST_WEEK','&lt; ultima semana');
define('_NEXT_WEEK','proxima semana &gt;');
define('_WEEK_WEEK','. semana');

//month view
define('_MONTH_SHOW_VIEW','mostrar mensualmente');

//dayly view
define('_DAY_SHOW_VIEW','mostrar diariamente');
define('_DAY_SHOW_NEXT','proximo dia &gt;');
define('_DAY_SHOW_LAST','&lt; dia anterior');
define('_DAY_TODAY','today');
define('_DAY_YESTERDAY','&lt; ayer');
define('_DAY_TOMORROW','manana &gt;');

//general information
define('_EVENT_CAL','Agenda');

//Date and Time Conversion Constants:
define('_DATE_SPLITTER','-');
define('_TIME_SPLITTER',':');
define('_DATE_MONTH_POS', 2); //position -beginning with 0- the month is situated at in the current date-format
define('_DATE_DAY_POS', 1);
define('_DATE_YEAR_POS', 0);

//time-formats for strftime
define('_DAYVIEW_CAPTION','%A, %x');
define('_DAYVIEW_EVENT_START','%H:%M');
define('_DAYVIEW_EVENT_END',' - %H:%M');
define('_OVERLIB_STARTDATE', 'fech de comienzo: %x');
define('_OVERLIB_ENDDATE', 'fecha de finalizacion: %x');
define('_OVERLIB_STARTTIME', 'hora de comienzo: %H:%M');
define('_OVERLIB_ENDTIME', 'hora de finalizacion: %H:%M');
define('_OVERLIB_TIME_FROM', 'desde %H:%M');
define('_OVERLIB_TIME_TO', ' hasta %H:%M');
define('_OVERLIB_SINGLETIME', ' %H:%M');
define('_OVERLIB_SINGLEDATE', ' %x');
define('_OVERLIB_SINGLETIME_FROM', ' desde %H:%M');

define('_OVERLIB_CALENDAR','dd/mm/y');
define('_NORMAL_DATE_FORMAT','%d/%m/%Y');
define('_NORMAL_TIME_FORMAT','%H:%M');
define('_DATE_TIME_FORMAT', '14/02/2009 00:31');

//translation for admin-interface
define('_ALL_EVENTS','Mostrar todos los eventos');
define('_OLD_EVENTS','Mostrar eventos pasados');

?>