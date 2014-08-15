<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ ограничен' );

require_once( igetPath( 'toolbar_html' ) );

switch ( $task ) {
	case 'new':
	case 'edit':
	case 'editA':
		TOOLBAR_users::_EDIT();
		break;

	case 'newfoto':
	case 'save':
	case 'apply':
	case 'newfoto_store':
	case 'save_store':
	case 'apply_store':
		break;

	default:
		TOOLBAR_users::_DEFAULT();
		break;
}
?>