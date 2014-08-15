<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ ограничен' );
global $mainframe;

require_once( $mainframe->getPath( 'toolbar_html' ) );

switch ($task){
	default:
		TOOLBAR_checkin::_DEFAULT();
		break;
}
?>