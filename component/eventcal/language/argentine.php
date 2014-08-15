<?php
/**
 * eventCal
 *
 * Spanish language file
 *
 * @version		$Id: argentine.php 62 2006-09-03 01:34:12Z kay_messers $
 * @package		eventCal
 * @author		Marcelo Selada <marceloselada@hotmail.com> Mendoza - Argentina
 * @website		http://www.aspsimza.org.ar
 * @copyright	Copyright (C) 2006 Kay Messerschmidt. All rights reserved.
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link		http://forge.joomla.org/sf/projects/eventcal
 *
 * Thanks to Marcelo Selada for this translation
 */

// no direct access
defined( '_VALID_MOS' ) or die( 'Acceso Restringido' );

//Traducciтn para de la interface de frente del sitio.
//Mesanges del errores
define('_UNVALID_ACCESS','EventCal fue llamado ilegalmente.<br />El proceso fue abortado.<br />Si este error ocurre nuevamente contacte al administrador.');
define('_NO_ACCESS_CAP','Acceso denegado');
//define('','');

//Dialogos de redirecciуn
define('_MISSING_PARAM_CAP','Proceso no disponible');
define('_MISSING_PARAM_TEXT','Un error ocurriу al pasar los parбmetros.<br />El proceso fue abortado.');
define('_SUCCESSFULL_ACTION_CAP', 'Acciуn realizada con йxito!');
define('_SUCCESSFULL_POSTED', _SUBMIT_SUCCESS_DESC);
define('_ACTION_ABORTED_CAP', 'Acciуn Abortada');

//Lista de categorнa
define('_CATLIST_HEADER','Categorнas');
define('_SELECT_ALL','Seleccionar Todas');

//Detalles de contacto
define('_CONTACT_CONTACT','Persona de contacto:');
define('_CONTACT_URL','Homepage:');

//Formulario del editor
define('_EDITOR_CAPTION','Desde');
define('_FORM_START_DATE','Fecha inicio:');
define('_FORM_END_DATE','Fecha de fin:');
define('_SUBMIT_BUTTON','Enviar');
define('_RESET_BUTTON','Rehacer');
define('_FORM_EXCEPTIONS','Fechas excepcionales:');
define('_FORM_REPEATCAP','Repetir');
define('_FORM_CATEGORY','Categorнa');
define('_EVENT','Eventos');
define('_COUNTER_DESC', 'Ingrese cuenta de repeticiуn (Excepciones no contadas). <i>La fecha final serб ignorada.</i>');

//TIPOS DE REPETICIONES
define('_REP_NONE','Nunca');
define('_REP_DAYLY','Diariamente');
define('_REP_WEEKLY','Semananlmente');
define('_REP_MONTHLY','Mensualmente');
define('_REP_YEARLY','Anualmente');

//VISTAS SEMANALES
define('_WEEK_SHOW_VIEW','Ver semana');
define('_WEEK_FROM','Desde semana');
define('_WEEK_TILL','Hasta');
define('_LAST_WEEK','&lt; Ъltima semana');
define('_NEXT_WEEK','Prуxima semana &gt;');
define('_WEEK_WEEK','. Semana');

//VISTAS MENSUALES
define('_MONTH_SHOW_VIEW','Ver mes');

//VISTAS DIARIAS
define('_DAY_SHOW_VIEW','Ver dнa');
define('_DAY_SHOW_NEXT','Prуximo dнa &gt;');
define('_DAY_SHOW_LAST','&lt; Ъltimo dнa');
define('_DAY_TODAY','Hoy');
define('_DAY_YESTERDAY','&lt; Ayer');
define('_DAY_TOMORROW','Maсana &gt;');

//Informaciуn general
define('_EVENT_CAL','event calendar');

//Conversiуn de constantes de Fecha y tiempo:
define('_DATE_SPLITTER','-');
define('_TIME_SPLITTER',':');
define('_DATE_MONTH_POS', 1); //position -beginning with 0- the month is situated at in the current date-format
define('_DATE_DAY_POS', 2);
define('_DATE_YEAR_POS', 0);

//Formatos de fecha
define('_DAYVIEW_CAPTION','%A, %x');
define('_DAYVIEW_EVENT_START','%H:%M');
define('_DAYVIEW_EVENT_END',' - %H:%M');
define('_OVERLIB_STARTDATE', 'Inicio: %x');
define('_OVERLIB_ENDDATE', 'Fin: %x');
define('_OVERLIB_STARTTIME', 'Inicio: %H:%M');
define('_OVERLIB_ENDTIME', 'fin: %H:%M');
define('_OVERLIB_TIME_FROM', 'de %H:%M');
define('_OVERLIB_TIME_TO', ' hasta %H:%M');
define('_OVERLIB_SINGLETIME', ' %H:%M');
define('_OVERLIB_SINGLEDATE', ' %x');
define('_OVERLIB_SINGLETIME_FROM', ' Desde %H:%M');

define('_OVERLIB_CALENDAR','mm/dd/y');
define('_NORMAL_DATE_FORMAT','%m/%d/%Y');
define('_NORMAL_TIME_FORMAT','%H:%M');
define('_DATE_TIME_FORMAT', '02/14/2009 00:31');

//Traducciуn de la la interface de administraciуn
define('_ALL_EVENTS','Mostrar todos los eventos');
define('_OLD_EVENTS','Ver eventos expirados');

?>