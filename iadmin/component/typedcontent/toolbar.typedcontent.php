<?php
// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

require_once( igetPath( 'toolbar_html' ) );

switch ($task) {
	case 'cfg':
		mosMenuBar::startTable();
		mosMenuBar::save('savecfg');
		mosMenuBar::spacer();
		mosMenuBar::endTable();
		break;

	case 'new':
	case 'edit':
	case 'editA':
		TOOLBAR_typedcontent::_EDIT( );
		break;

	default:
		TOOLBAR_typedcontent::_DEFAULT();
		break;
}
?>