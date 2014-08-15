<?php



// запрет прямого доступа

defined( '_VALID_INSITE' ) or die( 'Доступ ограничен' );



require_once( igetPath( 'toolbar_html' ) );



switch ( $task ) {

	case 'new':

	case 'edit':

	case 'addnewstatus':

		TOOLBAR_users::_EDIT();

		break;



	default:

		TOOLBAR_users::_DEFAULT();

		break;

}

?>