<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ ограничен' );

require_once( igetPath( 'toolbar_html' ) );

switch ( $task ) {
	case 'csv_step2':
	mosMenuBar::startTable();
	mosMenuBar::save();
	mosMenuBar::spacer();
	mosMenuBar::custom( 'csv_step3', '', '', 'Далее', false );
	mosMenuBar::endTable();
	break;
	case 'edit':
	case 'editA':
		TOOLBAR_users::_EDIT();
		break;

	default:
		TOOLBAR_users::_DEFAULT();
		break;
}
?>