<?php
/**
* @version $Id: wrapper.menu.php 85 2005-09-15 23:12:03Z eddieajau $
* @package Joomla RE
* @subpackage Menus
* @localized Авторские права (C) 2005 Joom.Ru - Русский дом Joomla!
* @copyright Авторские права (C) 2005 Open Source Matters. Все права защищены.
* @license Лицензия http://www.gnu.org/copyleft/gpl.html GNU/GPL, смотрите LICENSE.php
* Joomla! - свободное программное обеспечение. Эта версия может быть изменена
* в соответствии с Генеральной Общественной Лицензией GNU, поэтому возможно
* её дальнейшее распространение в составе результата работы, лицензированного
* согласно Генеральной Общественной Лицензией GNU или других лицензий свободных 
* программ или программ с открытым исходным кодом.
* Для просмотра подробностей и замечаний об авторском праве, смотрите файл COPYRIGHT.php.
* 
* @translator Oleg A. Myasnikov aka Sourpuss (sourpuss@mamboteam.ru)
*/

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ ограничен' );

mosAdminMenus::menuItem( $type );

switch ( $task ) {
	case 'wrapper':
		// this is the new item, ie, the same name as the menu `type`
		wrapper_menu::edit( 0, $menutype, $option );
		break;

	case 'edit':
		wrapper_menu::edit( $cid[0], $menutype, $option );
		break;

	case 'save':
	case 'apply':
		wrapper_menu::saveMenu( $option, $task );
		break;
}
?>