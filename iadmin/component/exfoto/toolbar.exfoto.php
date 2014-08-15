<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ ограничен' );

require_once( igetPath( 'toolbar_html' ) );

switch ( $task ) {
	case 'cfg':
		mosMenuBar::startTable();
		mosMenuBar::save('savecfg');
		mosMenuBar::spacer();
		mosMenuBar::customG( "Пересчитать", "javascript: submitbutton('fotorecalc'); ");
		mosMenuBar::spacer();		
		mosMenuBar::endTable();
		break;

	case 'new':
	case 'edit':
	case 'editA':
		TOOLBAR_users::_EDIT();
		break;

	default:
		TOOLBAR_users::_DEFAULT();
		break;
}
?>