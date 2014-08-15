<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ ограничен' );

require_once( igetPath( 'toolbar_html' ) );

switch ( $task ) {
	case 'cfg':
		mosMenuBar::startTable();
		mosMenuBar::save('savecfg');
		mosMenuBar::spacer();
		mosMenuBar::endTable();
		break;
            
	case 'new':
	case 'edit':
	case 'editA':
		TOOLBAR_users::_EDIT();
		break;
	case 'filecat_edit':
		mosMenuBar::startTable();
		mosMenuBar::save('filecat_save');
		mosMenuBar::spacer();
		mosMenuBar::apply('filecat_apply');
		mosMenuBar::spacer();
		mosMenuBar::cancel( 'filecat_cancel_edit', 'Закрыть' );
		mosMenuBar::endTable();
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