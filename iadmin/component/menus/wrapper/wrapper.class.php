<?php
/**
* @version $Id: wrapper.class.php 4542 2006-08-15 13:49:12Z predator $
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

/**
* Wrapper class
* @package Joomla RE
* @subpackage Menus
*/
class wrapper_menu {

	function edit( &$uid, $menutype, $option ) {
		global $database, $my, $mainframe;

		$menu = new mosMenu( $database );
		$menu->load( (int)$uid );

		// fail if checked out not by 'me'
		if ($menu->checked_out && $menu->checked_out != $my->id) {
			mosErrorAlert( "Модуль ".$menu->title." в настоящее время редактируется другим администратором" );
		}

		if ( $uid ) {
			$menu->checkout( $my->id );
		} else {
			$menu->type 		= 'wrapper';
			$menu->menutype 	= $menutype;
			$menu->ordering 	= 9999;
			$menu->parent 		= intval( mosGetParam( $_POST, 'parent', 0 ) );
			$menu->published 	= 1;
			$menu->link 		= 'index.php?c=iframe';
		}

		// build the html select list for ordering
		$lists['ordering'] 		= mosAdminMenus::Ordering( $menu, $uid );
		// build the html select list for the group access
		$lists['access'] 		= mosAdminMenus::Access( $menu );
		// build the html select list for paraent item
		$lists['parent'] 		= mosAdminMenus::Parent( $menu );
		// build published button option
		$lists['published'] 	= mosAdminMenus::Published( $menu );
		// build the url link output
		$lists['link'] 		= mosAdminMenus::Link( $menu, $uid );

		// get params definitions
		$params = new mosParameters( $menu->params, $mainframe->getPath( 'menu_xml', $menu->type ), 'menu' );
		if ( $uid ) {
			$menu->url = $params->def( 'url', '' );
		}

		wrapper_menu_html::edit( $menu, $lists, $params, $option );
	}


	function saveMenu( $option, $task ) {
		global $database;

		$params = mosGetParam( $_POST, 'params', '' );
		$params[url] = mosGetParam( $_POST, 'url', '' );

		if (is_array( $params )) {
			$txt = array();
			foreach ($params as $k=>$v) {
				$txt[] = "$k=$v";
			}
 			$_POST['params'] = mosParameters::textareaHandling( $txt );
		}

		$row = new mosMenu( $database );

		if (!$row->bind( $_POST )) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}

		if (!$row->check()) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		if (!$row->store()) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		$row->checkin();
		$row->updateOrder( 'menutype = ' . $database->Quote( $row->menutype ) . ' AND parent = ' . (int) $row->parent );
		// необходимо добавить индекс для параметров
		$i24r = new mosDBTable( "#__menu", "id", $database );
		$i24r->id = $row->id;
		$i24r->link = "index.php?c=iframe&mid=".$row->id;
		if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();



		$msg = 'Пункт меню сохранен';
		switch ( $task ) {
			case 'apply':
				mosRedirect( 'index2.php?ca='. $option .'&menutype='. $row->menutype .'&task=edit&id='. $row->id, $msg );
				break;

			case 'save':
			default:
				mosRedirect( 'index2.php?ca='. $option .'&menutype='. $row->menutype, $msg );
			break;
		}
	}
}
?>