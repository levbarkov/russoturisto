<?php
// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ ограничен' );

mosAdminMenus::menuItem( $type );

switch ($task) {
	case 'content_archive_category':
		// this is the new item, ie, the same name as the menu `type`
		content_archive_category_menu::editCategory( 0, $menutype, $option );
		break;

	case 'edit':
		content_archive_category_menu::editCategory( $cid[0], $menutype, $option );
		break;

	case 'save':
	case 'apply':
		saveMenu( $option, $task );
		break;
}
?>