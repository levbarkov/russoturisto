<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $task, $option, $id, $my, $mainframe;
if (  $my->gid<24  ) {
	mosRedirect( 'index2.php', _NOT_AUTH );
}
require_once( $mainframe->getPath( 'admin_html' ) );
$menu 		= stripslashes( strval( mosGetParam( $_GET, 'menu', '' ) ) );
$type 		= stripslashes( strval( mosGetParam( $_POST, 'type', '' ) ) );
$cid 		= mosGetParam( $_POST, 'cid', '' );
if (isset( $cid[0] ) && get_magic_quotes_gpc()) {
	$cid[0] = stripslashes( $cid[0] );
}
switch ($task) {
	case 'new':
		editMenu( $option, '' );
		break;
	case 'edit':
		if ( !$menu ) {
			$menu = $cid[0];
		}
		editMenu( $option, $menu );
		break;
	case 'savemenu':
		saveMenu();
		break;
	case 'deleteconfirm':
		deleteconfirm( $option, $cid[0] );
		break;
	case 'deletemenu':
		deleteMenu( $option, $cid, $type );
		break;
	case 'copyconfirm':
		copyConfirm( $option, $cid[0] );
		break;

	case 'copymenu':
		copyMenu( $option, $cid, $type );
		break;

	case 'cancel':
		cancelMenu( $option );
		break;

	default:
		showMenu( $option );
		break;
}

function showMenu( $option ) {
	global $database, $mainframe, $mosConfig_list_limit;

	$limit 		= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
	$limitstart = intval( $mainframe->getUserStateFromRequest( "view{". $option ."}limitstart", 'limitstart', 0 ) );
	
	$menuTypes 	= mosAdminMenus::menutypes();
	$total		= count( $menuTypes );
	$i			= 0; 
	foreach ( $menuTypes as $a ) {
		$menus[$i]->type 		= $a;

		// query to get number of modules for menutype
		$query = "SELECT count( id )"
		. "\n FROM #__modules"
		. "\n WHERE module = 'mod_mainmenu'"
		. "\n AND params LIKE '%" . $database->getEscaped( $a ) . "%'"
		;
		$database->setQuery( $query );
		$modules = $database->loadResult();

		if ( !$modules ) {
			$modules = '-';
		}
		$menus[$i]->modules = $modules;

		$i++;
	}

	// Query to get published menu item counts
	$query = "SELECT a.menutype, count( a.menutype ) as num"
	. "\n FROM #__menu AS a"
	. "\n WHERE a.published = 1"
	. "\n GROUP BY a.menutype"
	. "\n ORDER BY a.menutype"
	;
	$database->setQuery( $query );
	$published = $database->loadObjectList();

	// Query to get unpublished menu item counts
	$query = "SELECT a.menutype, count( a.menutype ) as num"
	. "\n FROM #__menu AS a"
	. "\n WHERE a.published = 0"
	. "\n GROUP BY a.menutype"
	. "\n ORDER BY a.menutype"
	;
	$database->setQuery( $query );
	$unpublished = $database->loadObjectList();

	// Query to get trash menu item counts
	$query = "SELECT a.menutype, count( a.menutype ) as num"
	. "\n FROM #__menu AS a"
	. "\n WHERE a.published = -2"
	. "\n GROUP BY a.menutype"
	. "\n ORDER BY a.menutype"
	;
	$database->setQuery( $query );
	$trash = $database->loadObjectList();

	for( $i = 0; $i < $total; $i++ ) {
		// adds published count
		foreach ( $published as $count ) {
			if ( $menus[$i]->type == $count->menutype ) {
				$menus[$i]->published = $count->num;
			}
		}
		if ( @!$menus[$i]->published ) {
			$menus[$i]->published = '-';
		}
		// adds unpublished count
		foreach ( $unpublished as $count ) {
			if ( $menus[$i]->type == $count->menutype ) {
				$menus[$i]->unpublished = $count->num;
			}
		}
		if ( @!$menus[$i]->unpublished ) {
			$menus[$i]->unpublished = '-';
		}
		// adds trash count
		foreach ( $trash as $count ) {
			if ( $menus[$i]->type == $count->menutype ) {
				$menus[$i]->trash = $count->num;
			}
		}
		if ( @!$menus[$i]->trash ) {
			$menus[$i]->trash = '-';
		}
	}

	require_once( $GLOBALS['mosConfig_absolute_path'] . '/iadmin/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );

	HTML_menumanager::show( $option, $menus, $pageNav );
}


/**
* Edits a mod_mainmenu module
*
* @param option	options for the edit mode
* @param cid	menu id
*/
function editMenu( $option, $menu ) {
	global $database;

	if( $menu ) {
		$row->menutype 	= $menu;
	} else {
		$row = new mosModule( $database );
		// setting default values
		$row->menutype 	= '';
		$row->iscore 	= 0;
		$row->published = 0;
		$row->position 	= 'left';
		$row->module 	= 'mod_mainmenu';
	}

	HTML_menumanager::edit( $row, $option );
}

/**
* Creates a new mod_mainmenu module, which makes the menu visible
* this is a workaround until a new dedicated table for menu management can be created
*/
function saveMenu() {
	global $database;
	$menutype 		= stripslashes( strval( mosGetParam( $_POST, 'menutype', '' ) ) );
	$old_menutype 	= stripslashes( strval( mosGetParam( $_POST, 'old_menutype', '' ) ) );
	$new			= intval( mosGetParam( $_POST, 'new', 1 ) );

	// block to stop renaming of 'mainmenu' menutype
	/* if ( $old_menutype == 'mainmenu' ) {
		if ( $menutype != 'mainmenu' ) {
			echo "<script> alert('Вы не можете переименовать меню \'mainmenu\', т.к.  это нарушит правильное функционирование Joomla'); window.history.go(-1); </script>\n";
			exit;
		}
	} */

	// check for ' in menu name
	if (strstr($menutype, '\'')) {
		echo "<script> alert('Название меню не должно содержать \''); window.history.go(-1); </script>\n";
		exit;
	}

	// check for unique menutype for new menus
	$query = "SELECT params FROM #__modules WHERE module = 'mod_mainmenu'";
	$database->setQuery( $query );
	$menus = $database->loadResultArray();
	foreach ( $menus as $menu ) {
		$params = mosParseParams( $menu );
		if ( $params->menutype == $menutype ) {
			echo "<script> alert('Меню с таким именем уже существует. Вы должны ввести уникальное имя меню'); window.history.go(-1); </script>\n";
			exit;
		}
	}
	switch ( $new ) {
		case 1:
			$i24r = new mosDBTable( "#__modules", "id", $database );
			$i24r->id = 0;
			$i24r->title = $menutype;
			$i24r->content = "";
			$i24r->ordering = 1;
			$i24r->position = "left";
			$i24r->checked_out = 0;
			$i24r->checked_out_time = "0000-00-00 00:00:00";
			$i24r->published = 1;
			$i24r->module = "mod_mainmenu";
			$i24r->numnews = 0;
			$i24r->access = 1;
			$i24r->showtitle = 1;
			$i24r->params = 'menutype='. $menutype;
			$i24r->iscore = 1;
			$i24r->client_id = 0;		
			if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
			$i24r->checkin();
			$i24r->updateOrder( "position=". $database->Quote( $i24r->position ) );
			$msg = 'Создано новое меню [ '. $menutype .' ]';
			break;
		default:
		// change menutype being of all mod_mainmenu modules calling old menutype
			$query = "SELECT id"
			. "\n FROM #__modules"
			. "\n WHERE module = 'mod_mainmenu'"
			. "\n AND params LIKE '%" . $database->getEscaped( $old_menutype ) . "%'"
			;
			$database->setQuery( $query );
			$modules = $database->loadResultArray();

			foreach ( $modules as $module ) {
				$row = new mosModule( $database );
				$row->load( $module );

				$save = 0;
				$params = mosParseParams( $row->params );
				if ( $params->menutype == $old_menutype ) {
					$params->menutype 	= $menutype;
					$save 				= 1;
				}

				// save changes to module 'menutype' param
				if ( $save ) {
					$txt = array();
					foreach ( $params as $k=>$v) {
						$txt[] = "$k=$v";
					}
					$row->params = implode( "\n", $txt );

					// check then store data in db
					if ( !$row->check() ) {
						echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
						exit();
					}
					if ( !$row->store() ) {
						echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
						exit();
					}

					$row->checkin();
				}
			}

		// change menutype of all menuitems using old menutype
			if ( $menutype != $old_menutype ) {
				$query = "UPDATE #__menu"
				. "\n SET menutype = " . $database->Quote( $menutype )
				. "\n WHERE menutype = " . $database->Quote( $old_menutype )
				;
				$database->setQuery( $query );
				$database->query();
			}

			$msg = 'Пункты меню и модули обновлены';
			break;
	}

	mosRedirect( 'index2.php?ca=menumanager', $msg );
}

/**
* Compiles a list of the items you have selected to permanently delte
*/
function deleteConfirm( $option, $type ) {
	global $database;

	if ( $type == 'mainmenu' ) {
		echo "<script> alert('Вы не можете удалить меню \'mainmenu\', т.к. оно является меню ядра'); window.history.go(-1); </script>\n";
		exit();
	}

	// list of menu items to delete
	$query = "SELECT a.name, a.id"
	. "\n FROM #__menu AS a"
	. "\n WHERE a.menutype = " . $database->Quote( $type )
	. "\n ORDER BY a.name"
	;
	$database->setQuery( $query );
	$items = $database->loadObjectList();

	// list of modules to delete
	$query = "SELECT id"
	. "\n FROM #__modules"
	. "\n WHERE module = 'mod_mainmenu'"
	. "\n AND params LIKE '%" . $database->getEscaped( $type ) . "%'"
	;
	$database->setQuery( $query );
	$mods = $database->loadResultArray();

	foreach ( $mods as $module ) {
		$row = new mosModule( $database );
		$row->load( $module );

		$params = mosParseParams( $row->params );
		if ( $params->menutype == $type ) {
			$mid[] = $module;
		}
	}

	mosArrayToInts( $mid );
	if (count( $mid )) {
		$mids = 'id=' . implode( ' OR id=', $mid );
	$query = "SELECT id, title"
	. "\n FROM #__modules"
		. "\n WHERE ( $mids )"
	;
	$database->setQuery( $query );
		$modules = $database->loadObjectList();
	} else {
		$modules = null;
	}

	HTML_menumanager::showDelete( $option, $type, $items, $modules );
}

/**
* Deletes menu items(s) you have selected
*/
function deleteMenu( $option, $cid, $type ) {
	global $database;
	if ( $type == 'mainmenu' ) {
		echo "<script> alert('Вы не можете удалить меню \'mainmenu\', т.к. оно является меню ядра'); window.history.go(-1); </script>\n";
		exit();
	}
	$mid = mosGetParam( $_POST, 'mids' );
	mosArrayToInts( $mid );
	if (count( $mid )) {
	// delete menu items
		$mids = 'id=' . implode( ' OR id=', $mid );
	$query = "DELETE FROM #__menu"
		. "\n WHERE ( $mids )"
	;
	$database->setQuery( $query );
	if ( !$database->query() ) {
		echo "<script> alert('". $database->getErrorMsg() ."');</script>\n";
		exit;
	}
	}
	mosArrayToInts( $cid );
	// checks whether any modules to delete
	if (count( $cid )) {
		// delete modules
		$cids = 'id=' . implode( ' OR id=', $cid );
		$query = "DELETE FROM #__modules"
		. "\n WHERE ( $cids )"
		;
		$database->setQuery( $query );
		if ( !$database->query() ) {
			echo "<script> alert('". $database->getErrorMsg() ."'); window.history.go(-1); </script>\n";
			exit;
		}
		// reorder modules after deletion
		$mod = new mosModule( $database );
		$mod->ordering = 0;
		$mod->updateOrder( "position='left'" );
		$mod->updateOrder( "position='right'" );
	}
	// clean any existing cache files
	mosCache::cleanCache( 'content' );
	$msg = 'Меню удалено';
	mosRedirect( 'index2.php?ca=' . $option, $msg );
}

/**
* Compiles a list of the items you have selected to Copy
*/
function copyConfirm( $option, $type ) {
	global $database;

	// Content Items query
	$query = 	"SELECT a.name, a.id"
	. "\n FROM #__menu AS a"
	. "\n WHERE a.menutype = " . $database->Quote( $type )
	. "\n ORDER BY a.name"
	;
	$database->setQuery( $query );
	$items = $database->loadObjectList();

	HTML_menumanager::showCopy( $option, $type, $items );
}


/**
* Copies a complete menu, all its items and creates a new module, using the name speified
*/
function copyMenu( $option, $cid, $type ) {
	global $database;

	$menu_name 		= stripslashes( strval( mosGetParam( $_POST, 'menu_name', 'Новое меню' ) ) );
	$module_name 	= stripslashes( strval( mosGetParam( $_POST, 'module_name', 'Новый модуль' ) ) );

	// check for unique menutype for new menu copy
	$query = "SELECT params"
	. "\n FROM #__modules"
	. "\n WHERE module = 'mod_mainmenu'"
	;
	$database->setQuery( $query );
	$menus = $database->loadResultArray();
	foreach ( $menus as $menu ) {
		$params = mosParseParams( $menu );
		if ( $params->menutype == $menu_name ) {
			echo "<script> alert('Меню с таким именем уже существует. Вы должны ввести уникальное имя меню'); window.history.go(-1); </script>\n";
			exit;
		}
	}

	// copy the menu items
	$mids 		= josGetArrayInts( 'mids' );
	$total 		= count( $mids );
	$copy 		= new mosMenu( $database );
	$original 	= new mosMenu( $database );
	sort( $mids );
	$a_ids 		= array();

	foreach( $mids as $mid ) {
		$original->load( $mid );
		$copy 			= $original;
		$copy->id 		= NULL;
		$copy->parent 	= $a_ids[$original->parent];
		$copy->menutype = $menu_name;

		if ( !$copy->check() ) {
			echo "<script> alert('".$copy->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		if ( !$copy->store() ) {
			echo "<script> alert('".$copy->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		$a_ids[$original->id] = $copy->id;
	}

	// create the module copy
	$row = new mosModule( $database );
	$row->load( 0 );
	$row->title 	= $module_name;
	$row->iscore 	= 0;
	$row->published = 1;
	$row->position 	= 'left';
	$row->module 	= 'mod_mainmenu';
	$row->params 	= 'menutype='. $menu_name;

	if (!$row->check()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	$row->checkin();
	$row->updateOrder( 'position=' . $database->Quote( $row->position ) );
	  
	// clean any existing cache files
	mosCache::cleanCache( 'content' );

	$msg = 'Создана копия меню `'. $type .'`, состоящая из '. $total .' пунктов';
	mosRedirect( 'index2.php?ca=' . $option, $msg );
}

/**
* Cancels an edit operation
* @param option	options for the operation
*/
function cancelMenu( $option ) {
	mosRedirect( 'index2.php?ca=' . $option . '&task=view' );
}
?>