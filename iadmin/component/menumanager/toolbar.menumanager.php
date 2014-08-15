<?php


defined( '_VALID_INSITE' ) or die( 'Доступ ограничен' );
global $mainframe;
require_once( $mainframe->getPath( 'toolbar_html' ) );


switch ($task) {
	case 'new':
	case 'edit':
		TOOLBAR_menumanager::_NEWMENU();
		break;

	case 'copyconfirm':
		TOOLBAR_menumanager::_COPYMENU();
		break;

	case 'deleteconfirm':
		TOOLBAR_menumanager::_DELETE();
		break;

	default:
		TOOLBAR_menumanager::_DEFAULT();
		break;
}
?>