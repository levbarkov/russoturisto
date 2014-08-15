<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $mainframe;

require_once( $mainframe->getPath( 'toolbar_html' ) );
switch ($task) {

	case 'editA':
	case 'edit':
		$cid = mosGetParam( $_POST, 'cid', 0 );
		if ( !is_array( $cid ) ){
			$mid = intval( mosGetParam( $_POST, 'id', 0 ) );
		} else {
			$mid = $cid[0];
		}

		$published = 0;
		if ( $mid ) {
			$query = "SELECT published"
			. "\n FROM #__modules"
			. "\n WHERE id = " . (int) $mid
			;
			$database->setQuery( $query );
			$published = $database->loadResult();
		}
//		$cur_template = $mainframe->getTemplate();
		TOOLBAR_modules::_EDIT( $cur_template, $published );
		break;

	case 'new':
		TOOLBAR_modules::_NEW();
		break;

	default:
		TOOLBAR_modules::_DEFAULT();
		break;
}
?>