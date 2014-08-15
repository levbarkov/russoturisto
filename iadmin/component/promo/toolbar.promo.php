<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ ограничен' );

require_once( igetPath( 'toolbar_html' ) );

switch ( $task ) {
	case 'cfg':
	default:
		mosMenuBar::startTable();
//		mosMenuBar::save(); 
//		mosMenuBar::spacer();
//		mosMenuBar::customG( "Пересчитать", "javascript: submitbutton('adrecalc'); ");
//		mosMenuBar::spacer();
//		if ( $id ) {
//			mosMenuBar::cancel( 'cancel', 'Закрыть' );
//		} else {
//			mosMenuBar::cancel();
//		}
		mosMenuBar::endTable();
		break;
}
?>