<?php
/**
* @version $Id: logout.php 5608 2006-10-30 22:24:43Z facedancer $
* @package Joomla RE
* @copyright Авторские права (C) 2005 Open Source Matters. Все права защищены.
* @license Лицензия http://www.gnu.org/copyleft/gpl.html GNU/GPL, смотрите LICENSE.php
* Joomla! - свободное программное обеспечение. Эта версия может быть изменена
* в соответствии с Генеральной Общественной Лицензией GNU, поэтому возможно
* её дальнейшее распространение в составе результата работы, лицензированного
* согласно Генеральной Общественной Лицензией GNU или других лицензий свободных 
* программ или программ с открытым исходным кодом.
* Для просмотра подробностей и замечаний об авторском праве, смотрите файл COPYRIGHT.php.
* 
* @localized (C) 2005 Joom.Ru
* @translator Sourpuss (sourpuss@mail.ru)
*/

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

global $database, $_VERSION;

// check to see if site is a production site
// allows multiple logins with same user for a demo site
if ( $_VERSION->SITE == 1 ) {
	// update db user last visit record corresponding to currently logged in user
	if ( isset( $_SESSION['session_user_id'] ) && $_SESSION['session_user_id'] != '' ) {
$currentDate = date( "Y-m-d\TH:i:s" );

	$query = "UPDATE #__users"
		. "\n SET lastvisitDate = " . $database->Quote( $currentDate )
		. "\n WHERE id = " . (int) $_SESSION['session_user_id']
	;
	$database->setQuery( $query );

	if (!$database->query()) {
		echo $database->stderr();
	}
}

	// delete db session record corresponding to currently logged in user
if ( isset( $_SESSION['session_id'] ) && $_SESSION['session_id'] != '' ) {
	$query = "DELETE FROM #__session"
		. "\n WHERE session_id = " . $database->Quote( $_SESSION['session_id'] )
	;
	$database->setQuery( $query );

	if (!$database->query()) {
		echo $database->stderr();
	}
}
}

$name 		= '';
$fullname 	= '';
$id 		= '';
$session_id = '';

// destroy PHP session
	session_destroy();

// return to site homepage
mosRedirect( 'index.php' );
?>