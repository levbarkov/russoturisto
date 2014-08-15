<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $mainframe, $task;
require_once( $mainframe->getPath( 'toolbar_html' ) );
//require_once( $mainframe->getPath( 'toolbar_default' ) );

switch ($task) {
	case 'new':
		TOOLBAR_menus::_NEW();
		break;

	case 'movemenu':
		TOOLBAR_menus::_MOVEMENU();
		break;

	case 'copymenu':
		TOOLBAR_menus::_COPYMENU();
		break;

	case 'edit':
		$cid 	= josGetArrayInts( 'cid' );
		$path 	= $mosConfig_absolute_path .'/administrator/components/com_menus/';

		if ( $cid[0] ) {
			$query = "SELECT type"
			. "\n FROM #__menu"
			. "\n WHERE id = " . (int) $cid[0]
			;
			$database->setQuery( $query );
			$type = $database->loadResult();
			$item_path  = $path . $type .'/'. $type .'.menubar.php';

			if ( $type ) {
				if ( file_exists( $item_path  ) ) {
					require_once( $item_path  );
				} else {
					TOOLBAR_menus::_EDIT();
				}
			} else {
				echo $database->stderr();
			}
		} else {
			$type 		= strval( mosGetParam( $_REQUEST, 'type', null ) );
			$item_path  = $path . $type .'/'. $type .'.menubar.php';

			if ( $type ) {
				if ( file_exists( $item_path ) ) {
					require_once( $item_path  );
				} else {
					TOOLBAR_menus::_EDIT();
				}
			} else {
				TOOLBAR_menus::_EDIT();
			}
		}
		break;

	default:
		$type 		= strval( mosGetParam( $_REQUEST, 'type' ) );
		$item_path  = $path . $type .'/'. $type .'.menubar.php';

		if ( $type ) {
			if ( file_exists( $item_path ) ) {
				require_once( $item_path );
			} else {
				TOOLBAR_menus::_DEFAULT();
			}
		} else {
			TOOLBAR_menus::_DEFAULT();
		}
		break;
}
?>