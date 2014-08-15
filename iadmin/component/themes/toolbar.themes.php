<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ ограничен' );
global $mainframe;

require_once( $mainframe->getPath( 'toolbar_html' ) );

$client = strval( mosGetParam( $_REQUEST, 'client', '' ) );

switch ($task) {

	case 'view':
		TOOLBAR_templates::_VIEW();
		break;

	case 'edit_source':
		TOOLBAR_templates::_EDIT_SOURCE();
		break;

	case 'edit_css':
		TOOLBAR_templates::_EDIT_CSS();
		break;

	case 'assign':
		TOOLBAR_templates::_ASSIGN();
		break;

	case 'positions':
		TOOLBAR_templates::_POSITIONS();
		break;

	default:
		TOOLBAR_templates::_DEFAULT($client);
		break;
}
?>