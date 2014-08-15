<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ ограничен' );

require_once( igetPath( 'toolbar_html' ) );

switch ( $task ) {
	case 'cfg':
	default:
		mosMenuBar::startTable();
		mosMenuBar::save(); 
		mosMenuBar::spacer();
		mosMenuBar::customG( "Пересчитать", "javascript: submitbutton('contentrecalc'); ");
		mosMenuBar::spacer();
		mosMenuBar::endTable();
		break;
}
?>