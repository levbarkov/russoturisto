<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ ограничен' );
global $mainframe;

require_once( $mainframe->getPath( 'toolbar_html' ) );
switch ($task) {

	case 'new':
	case 'edit':
	case 'editA':
		TOOLBAR_modules::_EDIT();
		break;

	default:
		TOOLBAR_modules::_DEFAULT();
		break;
}
?>