<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ ограничен' );

require_once( igetPath( 'toolbar_html' ) );
switch ($task) {
	case 'new':
	case 'new_content_typed':
	case 'new_content_section':
	case 'edit':
	case 'editA':
	case 'edit_content_typed':
		TOOLBAR_content::_EDIT( );
		break;

	case 'showarchive':
		TOOLBAR_content::_ARCHIVE();
		break;

	case 'movesect':
		TOOLBAR_content::_MOVE();
		break;

	case 'copy':
		TOOLBAR_content::_COPY();
		break;
		
	case 'save':
	case 'apply':
		break;

	case 'view':
	case '':
		TOOLBAR_content::_DEFAULT();
		break;
}
?>