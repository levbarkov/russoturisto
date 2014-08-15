<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ ограничен' );

require_once( igetPath( 'toolbar_html' ) );

switch ($task){
	case 'sysinfo':
		TOOLBAR_admin::_SYSINFO();
		break;
	case 'clean_gen_cont':
	case 'view_gen_cont':
		mosMenuBar::startTable();
		mosMenuBar::custom( 'clean_gen_cont', '', '', 'Очистить', false );
		mosMenuBar::spacer();
		mosMenuBar::custom( 'cpanel', '', '', 'Вернуться', false );
		mosMenuBar::endTable();
		break;
	case 'clean_gen_razd':
	case 'view_gen_razd':
		mosMenuBar::startTable();
		mosMenuBar::custom( 'clean_gen_razd', '', '', 'Очистить', false );
		mosMenuBar::spacer();
		mosMenuBar::custom( 'cpanel', '', '', 'Вернуться', false );
		mosMenuBar::endTable();
		break;
	case 'clean_gen_search':
	case 'view_gen_search':
		mosMenuBar::startTable();
		mosMenuBar::custom( 'clean_gen_search', '', '', 'Очистить', false );
		mosMenuBar::spacer();
		mosMenuBar::custom( 'cpanel', '', '', 'Вернуться', false );
		mosMenuBar::endTable();
		break;
	case 'clean_gen_sbot':
	case 'view_gen_sbot':
		mosMenuBar::startTable();
		mosMenuBar::custom( 'clean_gen_sbot', '', '', 'Очистить', false );
		mosMenuBar::spacer();
		mosMenuBar::custom( 'cpanel', '', '', 'Вернуться', false );
		mosMenuBar::endTable();
		break;
	case 'clean_gen_sbot_site':
	case 'view_gen_sbot_site':
		mosMenuBar::startTable();
		mosMenuBar::custom( 'clean_gen_sbot_site', '', '', 'Очистить', false );
		mosMenuBar::spacer();
		mosMenuBar::custom( 'cpanel', '', '', 'Вернуться', false );
		mosMenuBar::endTable();
		break;
	case 'clean_stat_sbot_days':
	case 'view_stat_sbot_days':
		mosMenuBar::startTable();
		mosMenuBar::custom( 'clean_stat_sbot_days', '', '', 'Очистить', false );
		mosMenuBar::spacer();
		mosMenuBar::custom( 'cpanel', '', '', 'Вернуться', false );
		mosMenuBar::endTable();
		break;
	case 'view_error_razd':
	case 'clean_error_razd':
		mosMenuBar::startTable();
		mosMenuBar::custom( 'clean_error_razd', '', '', 'Очистить', false );
		mosMenuBar::spacer();
		mosMenuBar::custom( 'cpanel', '', '', 'Вернуться', false );
		mosMenuBar::endTable();
		break;
	default:
		if ($GLOBALS['task']) {
			TOOLBAR_admin::_DEFAULT();
		} else {
			TOOLBAR_admin::_CPANEL();
		}
		break;
}
?>