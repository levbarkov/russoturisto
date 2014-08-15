<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ ограничен' );

require_once( igetPath( 'toolbar_html' ) );

switch ( $task ){
	case 'new':
	case 'edit':
	case 'editA':
		TOOLBAR_sections::_EDIT();
		break;

	case 'copyselect':
		TOOLBAR_sections::_COPY();
		break;

	default:
		TOOLBAR_sections::_DEFAULT();
		break;
}
?>