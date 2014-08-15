<?php
defined( '_VALID_INSITE' ) or die( 'Доступ ограничен' );
require_once( site_path."/component/login/login.html.php" );
global $database, $my, $mainframe;
global $mosConfig_live_site, $mosConfig_frontend_login, $mosConfig_db;

if ( $mosConfig_frontend_login != NULL && ($mosConfig_frontend_login === 0 || $mosConfig_frontend_login === '0')) {	header( "HTTP/1.0 403 Forbidden" );  echo _NOT_AUTH;  return;  }

$params = new mosParameters( $menu->params );
$params->def( 'page_title', 0 );
$params->def( 'header_login', $menu->name );
$params->def( 'header_logout', $menu->name );
$params->def( 'pageclass_sfx', '' );
$params->def( 'back_button', 1 );
$params->def( 'login', $mosConfig_live_site );
$params->def( 'logout', $mosConfig_live_site );
$params->def( 'login_message', 0 );
$params->def( 'logout_message', 0 );
$params->def( 'description_login', 1 );
$params->def( 'description_logout', 1 );
$params->def( 'description_login_text', _LOGIN_DESCRIPTION );
$params->def( 'description_logout_text', _LOGOUT_DESCRIPTION );
$params->def( 'image_login', 'key.jpg' );
$params->def( 'image_logout', 'key.jpg' );
$params->def( 'image_login_align', 'right' );
$params->def( 'image_logout_align', 'right' );
$params->def( 'registration', $mainframe->getCfg( 'allowUserRegistration' ) );

$image_login = '';
$image_logout = '';
if ( $params->get( 'image_login' ) != -1 ) {
	$image = $mosConfig_live_site .'/images/stories/'. $params->get( 'image_login' );
	$image_login = '<img src="'. $image  .'" align="'. $params->get( 'image_login_align' ) .'" hspace="10" alt="" />';
}
if ( $params->get( 'image_logout' ) != -1 ) {
	$image = $mosConfig_live_site .'/images/stories/'. $params->get( 'image_logout' );
	$image_logout = '<img src="'. $image .'" align="'. $params->get( 'image_logout_align' ) .'" hspace="10" alt="" />';
}

if ( $my->id ) {
	loginHTML::logoutpage( $params, $image_logout );
} else {
	loginHTML::loginpage( $params, $image_login );
}
?>