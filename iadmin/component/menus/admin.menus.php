<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $mainframe, $option, $task, $id, $mosConfig_absolute_path, $database;
require_once( $mainframe->getPath( 'admin_html' ) );

$path 		= $mosConfig_absolute_path .'/iadmin/component/menus/';

$menutype 	= stripslashes( strval( mosGetParam( $_REQUEST, 'menutype', 'mainmenu' ) ) );
$type 		= stripslashes( strval( mosGetParam( $_REQUEST, 'type', false ) ) );
$menu 		= stripslashes( strval( mosGetParam( $_POST, 'menu', '' ) ) );

$cid 		= josGetArrayInts( 'cid' );

switch ($task) {
	case 'new':
		addMenuItem( $cid, $menutype, $option, $task );
		break;

	case 'edit':
		$cid[0]	= ( $id ? $id : intval( $cid[0] ) );
		$menu = new mosMenu( $database );
		if ( $cid[0] ) {
			$menu->load( $cid[0]  );
		} else {
			$menu->type = $type;
		}
		if ( $menu->type ) { 
			$type = $menu->type;
			require_once( $path . $menu->type .'/'. $menu->type .'.menu.php' );
		}
		break;

	case 'save':
	case 'apply':
	      
		// clean any existing cache files
		mosCache::cleanCache( 'content' );
		require_once( $path . $type .'/'. $type .'.menu.php' );
		break;

	case 'publish':
	case 'unpublish':
		publishMenuSection( $cid, ($task == 'publish'), $menutype );
		break;

	case 'remove':
		TrashMenusection( $cid, $menutype );
		break;

	case 'cancel':
		cancelMenu( $option );
		break;

	case 'orderup':
		orderMenu( intval( $cid[0] ), -1, $option );
		break;

	case 'orderdown':
		orderMenu( intval( $cid[0] ), 1, $option );
		break;

	case 'accesspublic':
		accessMenu( intval( $cid[0] ), 0, $option, $menutype );
		break;

	case 'accessregistered':
		accessMenu( intval( $cid[0] ), 1, $option, $menutype );
		break;

	case 'accessspecial':
		accessMenu( intval( $cid[0] ), 2, $option, $menutype );
		break;

	case 'movemenu':
		moveMenu( $option, $cid, $menutype );
		break;

	case 'movemenusave':
		moveMenuSave( $option, $cid, $menu, $menutype );
		break;

	case 'copymenu':
		copyMenu( $option, $cid, $menutype );
		break;

	case 'copymenusave':
		copyMenuSave( $option, $cid, $menu, $menutype );
		break;

	case 'cancelcopymenu':
	case 'cancelmovemenu':
		viewMenuItems( $menutype, $option );
		break;

	case 'saveorder':
		saveOrder( $cid, $menutype );
		break;

	default:
		$type = stripslashes( strval( mosGetParam( $_REQUEST, 'type' ) ) );
		if ($type) {
			// adding a new item - type selection form
			require_once( $path . $type .'/'. $type .'.menu.php' );
		} else {
			viewMenuItems( $menutype, $option );
		}
		break;
}

/**
* Shows a list of items for a menu
*/
function viewMenuItems( $menutype, $option ) {
	global $database, $mainframe, $mosConfig_list_limit;

	$limit 		= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
	$limitstart = intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart$menutype", 'limitstart', 0 ) );
	$levellimit = intval( $mainframe->getUserStateFromRequest( "view{$option}limit$menutype", 'levellimit', 10 ) );
	$search 	= $mainframe->getUserStateFromRequest( "search{$option}$menutype", 'search', '' );
	if (get_magic_quotes_gpc()) {
		$search	= stripslashes( $search );
	}

	if ($search) {
		$query = "SELECT m.id"
		. "\n FROM #__menu AS m"
		. "\n WHERE menutype = " . $database->Quote( $menutype )
		. "\n AND LOWER( m.name ) LIKE '%" . $database->getEscaped( trim( mb_strtolower( $search,"UTF-8" ) ) ) . "%'"
		;
		$database->setQuery( $query );
		$search_rows = $database->loadResultArray();
	}

	$query = "SELECT m.*, u.name AS editor, c.publish_up, c.publish_down, com.name AS com_name"
	. "\n FROM #__menu AS m"
	. "\n LEFT JOIN #__users AS u ON u.id = m.checked_out"
	. "\n LEFT JOIN #__content AS c ON c.id = m.componentid AND m.type = 'content_typed'"
	. "\n LEFT JOIN #__components AS com ON com.id = m.componentid AND m.type = 'components'"
	. "\n WHERE m.menutype = " . $database->Quote( $menutype )
	. "\n AND m.published != -2"
	. "\n ORDER BY parent, ordering"
	;
	$database->setQuery( $query );
	$rows = $database->loadObjectList();

	// создание иерархии меню
	$children = array();
	// first pass - collect children
	foreach ($rows as $v ) {
		$pt = $v->parent;
		$list = @$children[$pt] ? $children[$pt] : array();
		array_push( $list, $v );
		$children[$pt] = $list;
	}
	// second pass - get an indent list of the items
	$list = mosTreeRecurse( 0, '', array(), $children, max( 0, $levellimit-1 ) );
	// eventually only pick out the searched items.
	if ($search) {
		$list1 = array();

		foreach ($search_rows as $sid ) {
			foreach ($list as $item) {
				if ($item->id == $sid) {
					$list1[] = $item;
				}
			}
		}
		// replace full list with found items
		$list = $list1;
	}

	$total = count( $list );

	require_once( $GLOBALS['mosConfig_absolute_path'] . '/iadmin/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );

	$levellist = mosHTML::integerSelectList( 1, 20, 1, 'levellimit', 'size="1" onchange="document.adminForm.submit();"', $levellimit, '', ' class="inputtop" ' );

	// slice out elements based on limits
	$list = array_slice( $list, $pageNav->limitstart, $pageNav->limit );

	$i = 0;
	foreach ( $list as $mitem ) {
		$edit = '';
		switch ( $mitem->type ) {
			case 'separator':
			case 'component_item_link':
				break;

			case 'url':
				if ( eregi( 'index.php\?', $mitem->link ) ) {
					if ( !eregi( 'Itemid=', $mitem->link ) ) {
						$mitem->link .= '&Itemid='. $mitem->id;
					}
				}
				break;

			case 'newsfeed_link':
				$edit = 'index2.php?ca=icanewsfeeds&task=edit&hidemainmenu=1A&id=' . $mitem->componentid;
				$list[$i]->descrip 	= 'Изменить эту ленту новостей';
				$mitem->link .= '&Itemid='. $mitem->id;
				break;

			case 'contact_item_link':
				$edit = 'index2.php?ca=icacontact&task=editA&hidemainmenu=1&id=' . $mitem->componentid;
				$list[$i]->descrip 	= 'Изменить этот контакт';
				$mitem->link .= '&Itemid='. $mitem->id;
				break;

			case 'content_item_link':
				$edit = 'index2.php?ca=content&task=edit&hidemainmenu=1&id=' . $mitem->componentid;
				$list[$i]->descrip 	= 'Изменить это содержимое';
				break;

			case 'content_typed':
				$edit = 'index2.php?ca=typedcontent&task=edit&hidemainmenu=1&id='. $mitem->componentid;
				$list[$i]->descrip 	= 'Изменить это статичное содержимое';
				break;

			default:
				$mitem->link .= '&Itemid='. $mitem->id;
				break;
		}
		$list[$i]->link = $mitem->link;
		$list[$i]->edit = $edit;
		$i++;
	}

	$i = 0;
	foreach ( $list as $row ) {
		// pulls name and description from menu type xml
		$row = ReadMenuXML( $row->type, $row->com_name );
		$list[$i]->type 	= $row[0];
		if (!isset($list[$i]->descrip)) $list[$i]->descrip = $row[1];
		$i++;
	}

	HTML_menusections::showMenusections( $list, $pageNav, $search, $levellist, $menutype, $option );
}

/**
* Displays a selection list for menu item types
*/
function addMenuItem( &$cid, $menutype, $option, $task ) {
	global $mosConfig_absolute_path;

	$types 	= array();

	// list of directories
	$dirs 	= mosReadDirectory( $mosConfig_absolute_path .'/iadmin/component/menus' );

	// load files for menu types
	foreach ( $dirs as $dir ) {
		// needed within menu type .php files
		$type 	= $dir;
		$dir 	= $mosConfig_absolute_path .'/iadmin/component/menus/'. $dir;
		if ( is_dir( $dir ) ) {
			$files = mosReadDirectory( $dir, ".\.menu\.php$" );
			foreach ($files as $file) {
				require_once( "$dir/$file" );
				$types[]->type = $type;
			}
		}
	}
	$i = 0;
	foreach ( $types as $type ) {
		// pulls name and description from menu type xml
		$row = ReadMenuXML( $type->type );
		$types[$i]->name 	= $row[0];
		$types[$i]->descrip = $row[1];
		$types[$i]->group 	= $row[2];
		$i++;
	}
	// sort array of objects alphabetically by name of menu type
	SortArrayObjects( $types, 'name', 1 );

	// split into Content
	$i = 0;
	foreach ( $types as $type ) {
		if ( strstr( $type->group, 'Content' ) ) {
			$types_content[] = $types[$i];
		}
		$i++;
	}

	// split into Links
	$i = 0;
	foreach ( $types as $type ) {
		if ( strstr( $type->group, 'Link' ) ) {
			$types_link[] = $types[$i];
		}
		$i++;
	}

	// split into Component
	$i = 0;
	foreach ( $types as $type ) {
		if ( strstr( $type->group, 'Component' ) ) {
			$types_component[] = $types[$i];
		}
		$i++;
	}

	// split into Other
	$i = 0;
	foreach ( $types as $type ) {
		if ( strstr( $type->group, 'Other' ) || !$type->group ) {
			$types_other[] = $types[$i];
		}
		$i++;
	}

	// split into Submit
	$i = 0;
	foreach ( $types as $type ) {
		if ( strstr( $type->group, 'Submit' ) || !$type->group ) {
			$types_submit[] = $types[$i];
		}
		$i++;
	}

	HTML_menusections::addMenuItem( $cid, $menutype, $option, $types_content, $types_component, $types_link, $types_other, $types_submit );
}


/**
* Generic function to save the menu
*/
function saveMenu( $option, $task='save' ) {
	global $database;

	$params = mosGetParam( $_POST, 'params', '' );
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

	$row->name = ampReplace( $row->name );
	
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

	$msg = 'Пункт меню сохранен';
	switch ( $task ) {
		case 'apply':
			mosRedirect( 'index2.php?ca='. $option .'&menutype='. $row->menutype .'&task=edit&id='. $row->id . '&hidemainmenu=1' , $msg );
			break;

		case 'save':
		default:
			mosRedirect( 'index2.php?ca='. $option .'&menutype='. $row->menutype, $msg );
			break;
	}
}

/**
* Publishes or Unpublishes one or more menu sections
* @param database A database connector object
* @param string The name of the category section
* @param array An array of id numbers
* @param integer 0 if unpublishing, 1 if publishing
*/
function publishMenuSection( $cid=null, $publish=1, $menutype ) {
	global $database, $mosConfig_absolute_path;

	if (!is_array( $cid ) || count( $cid ) < 1) {
		return 'Выберите объект для ' . ($publish ? 'публикации' : 'сокрытия');
	}

	$menu = new mosMenu( $database );
	foreach ($cid as $id) {
		$menu->load( $id );
		$menu->published = $publish;

		if (!$menu->check()) {
			return $menu->getError();
		}
		if (  $menu->published==''  ) $menu->published=0;
		if (!$menu->store()) {
			return $menu->getError();
		}

		if ($menu->type) {
			$database = &$database;
			$task = $publish ? 'publish' : 'unpublish';
			// $type value is used in *.menu.php
			$type = $menu->type;
			require_once( $mosConfig_absolute_path . '/iadmin/component/menus/' . $type . '/' . $type . '.menu.php' );
		}
	}
	
	// clean any existing cache files
	mosCache::cleanCache( 'content' );

	mosRedirect( 'index2.php?ca=menus&menutype='. $menutype );
}

/**
* Trashes a menu record
*/
function TrashMenuSection( $cid=NULL, $menutype='mainmenu' ) {
	global $database;

	$nullDate	= $database->getNullDate();
	$state		= -2;
	
	$query = "SELECT *"
	. "\n FROM #__menu"
	. "\n WHERE menutype = " . $database->Quote( $menutype )
	. "\n AND published != " . (int) $state
	. "\n ORDER BY menutype, parent, ordering"
	;
	$database->setQuery( $query );
	$mitems = $database->loadObjectList();	
	
	// determine if selected item has an child items
	$children = array();
	foreach ( $cid as $id ) {
		foreach ( $mitems as $item ) {
			if ( $item->parent == $id ) {
				$children[] = $item->id;
			}		
		}
	}	
	$list 	= josMenuChildrenRecurse( $mitems, $children, $children );
	$list 	= array_merge( $cid, $list );
	
	mosArrayToInts( $list );
	$ids = 'id=' . implode( ' OR id=', $list );
	
	$query = "DELETE FROM #__menu"
	. "\n WHERE ( $ids )"
	;
	$database->setQuery( $query );
	if ( !$database->query() ) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}
	$total = count( $list );

	mosCache::cleanCache( 'content' );
	$msg =  'Удалено ссылок: '.$total;
	mosRedirect( 'index2.php?ca=menus&menutype='. $menutype, $msg );
}

/**
* Cancels an edit operation
*/
function cancelMenu( $option ) {
	global $database;

	$menu = new mosMenu( $database );
	$menu->bind( $_POST );
	$menuid = intval( mosGetParam( $_POST, 'menuid', 0 ) );
        if (  $menuid  ){
            $menu->id = $menuid;
            $menu->checkin();
        }
	mosRedirect( 'index2.php?ca='. $option .'&menutype='. $menu->menutype );
}

/**
* Moves the order of a record
* @param integer The increment to reorder by
*/
function orderMenu( $uid, $inc, $option ) {
	global $database;

	$row = new mosMenu( $database );
	$row->load( $uid );
	$row->move( $inc, "menutype = " . $database->Quote( $row->menutype ) . " AND parent = " . (int) $row->parent );

	// clean any existing cache files
	mosCache::cleanCache( 'content' );

	mosRedirect( 'index2.php?ca='. $option .'&menutype='. $row->menutype );
}


/**
* changes the access level of a record
* @param integer The increment to reorder by
*/
function accessMenu( $uid, $access, $option, $menutype ) {
	global $database;

	$menu = new mosMenu( $database );
	$menu->load( $uid );
	$menu->access = $access;

	if (!$menu->check()) {
		return $menu->getError();
	}
	if (!$menu->store()) {
		return $menu->getError();
	}

	// clean any existing cache files
	mosCache::cleanCache( 'content' );

	mosRedirect( 'index2.php?ca='. $option .'&menutype='. $menutype );
}

/**
* Form for moving item(s) to a specific menu
*/
function moveMenu( $option, $cid, $menutype ) {
	global $database;

	if (!is_array( $cid ) || count( $cid ) < 1) {
		echo "<script> alert('Выберите объект для перемещения'); window.history.go(-1);</script>\n";
		exit;
	}

	## query to list selected menu items
	mosArrayToInts( $cid );
	$cids = 'a.id=' . implode( ' OR a.id=', $cid );
	$query = "SELECT a.name"
	. "\n FROM #__menu AS a"
	. "\n WHERE ( $cids )"
	;
	$database->setQuery( $query );
	$items = $database->loadObjectList();

	## query to choose menu
	$query = "SELECT a.params"
	. "\n FROM #__modules AS a"
	. "\n WHERE a.module = 'mod_mainmenu'"
	. "\n ORDER BY a.title"
	;
	$database->setQuery( $query );
	$modules = $database->loadObjectList();

	foreach ( $modules as $module) {
		$params = mosParseParams( $module->params );
		// adds menutype to array
		$type = trim( @$params->menutype );
		$menu[] = mosHTML::makeOption( $type, $type );
	}
	// build the html select list
	$MenuList = mosHTML::selectList( $menu, 'menu', 'class="inputbox" size="10"', 'value', 'text', null );

	HTML_menusections::moveMenu( $option, $cid, $MenuList, $items, $menutype );
}

/**
* Add all descendants to list of meni id's
*/
function addDescendants($id, &$cid) {
	global $database;

	$query = "SELECT id"
	. "\n FROM #__menu"
	. "\n WHERE parent = " . (int) $id
	;
	$database->setQuery( $query );
	$rows = $database->loadObjectList();
	if ($database->getErrorNum()) {
		echo "<script> alert('". $database->getErrorMsg() ."'); window.history.go(-1); </script>\n";
		exit();
	} // if
	foreach ($rows as $row) {
		$found = false;
		foreach ($cid as $idx)
			if ($idx == $row->id) {
				$found = true;
				break;
			} // if
		if (!$found) $cid[] = $row->id;
		addDescendants($row->id, $cid);
	} // foreach
} // addDescendants

/**
* Save the item(s) to the menu selected
*/
function moveMenuSave( $option, $cid, $menu, $menutype ) {
	global $database;

	// add all decendants to the list
	foreach ($cid as $id) addDescendants($id, $cid);

	$row = new mosMenu( $database );
	$ordering = 1000000;
	$firstroot = 0;
	foreach ($cid as $id) {
		$row->load( $id );

		// is it moved together with his parent?
		$found = false;
		if ($row->parent != 0)
			foreach ($cid as $idx)
				if ($idx == $row->parent) {
					$found = true;
					break;
				} // if
		if (!$found) {
			$row->parent = 0;
			$row->ordering = $ordering++;
			if (!$firstroot) $firstroot = $row->id;
		} // if

		$row->menutype = $menu;
		if ( !$row->store() ) {
			echo "<script> alert('". $database->getErrorMsg() ."'); window.history.go(-1); </script>\n";
			exit();
		} // if
	} // foreach

	if ($firstroot) {
		$row->load( $firstroot );
		$row->updateOrder( 'menutype = ' . $database->Quote( $row->menutype ) . ' AND parent = ' . (int) $row->parent );
	} // if
 
	// clean any existing cache files
	mosCache::cleanCache( 'content' );
	
	$msg = count($cid) .' пунктов меню перемещено в '. $menu;
	mosRedirect( 'index2.php?ca='. $option .'&menutype='. $menutype .'&mosmsg='. $msg );
} // moveMenuSave

/**
* Form for copying item(s) to a specific menu
*/
function copyMenu( $option, $cid, $menutype ) {
	global $database;

	if (!is_array( $cid ) || count( $cid ) < 1) {
		echo "<script> alert('Выберите объект для перемещения'); window.history.go(-1);</script>\n";
		exit;
	}

	## query to list selected menu items
	mosArrayToInts( $cid );
	$cids = 'a.id=' . implode( ' OR a.id=', $cid );
	$query = "SELECT a.name"
	. "\n FROM #__menu AS a"
	. "\n WHERE ( $cids )"
	;
	$database->setQuery( $query );
	$items = $database->loadObjectList();

	$menuTypes 	= mosAdminMenus::menutypes();

	foreach ( $menuTypes as $menuType ) {
		$menu[] = mosHTML::makeOption( $menuType, $menuType );
	}
	// build the html select list
	$MenuList = mosHTML::selectList( $menu, 'menu', 'class="inputbox" size="10"', 'value', 'text', null );

	HTML_menusections::copyMenu( $option, $cid, $MenuList, $items, $menutype );
}

/**
* Save the item(s) to the menu selected
*/
function copyMenuSave( $option, $cid, $menu, $menutype ) {
	global $database;

	$curr = new mosMenu( $database );
	$cidref = array();
	foreach( $cid as $id ) {
		$curr->load( $id );
		$curr->id = NULL;
		if ( !$curr->store() ) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		$cidref[] = array($id, $curr->id);
	}
	foreach ( $cidref as $ref ) {
		$curr->load( $ref[1] );
		if ($curr->parent!=0) {
			$found = false;
			foreach ( $cidref as $ref2 )
				if ($curr->parent == $ref2[0]) {
					$curr->parent = $ref2[1];
					$found = true;
					break;
				} // if
			if (!$found && $curr->menutype!=$menu)
				$curr->parent = 0;
		} // if
		$curr->menutype = $menu;
		$curr->ordering = '9999';
		if ( !$curr->store() ) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		$curr->updateOrder( 'menutype = ' . $database->Quote( $curr->menutype ) . ' AND parent = ' . (int) $curr->parent );
	} // foreach  
	
	// clean any existing cache files
	mosCache::cleanCache( 'content' );

	$msg = count( $cid ) .' пунктов меню скопировано в '. $menu;
	mosRedirect( 'index2.php?ca='. $option .'&menutype='. $menutype .'&mosmsg='. $msg );
}

function ReadMenuXML( $type, $component=-1 ) {
	global $mosConfig_absolute_path;

	// XML library
	require_once( $mosConfig_absolute_path . '/includes/domit/xml_domit_lite_include.php' );
	// xml file for module
	$xmlfile = $mosConfig_absolute_path .'/iadmin/component/menus/'. $type .'/'. $type .'.xml';
	$xmlDoc = new DOMIT_Lite_Document();
	$xmlDoc->resolveErrors( true );

	if ($xmlDoc->loadXML( $xmlfile, false, true )) {
		$root = &$xmlDoc->documentElement;

		if ( $root->getTagName() == 'mosinstall' && ( $root->getAttribute( 'type' ) == 'component' || $root->getAttribute( 'type' ) == 'menu' ) ) {
			// Menu Type Name
			$element 	= &$root->getElementsByPath( 'name', 1 );
			$name 		= $element ? trim( $element->getText() ) : '';
			// Menu Type Description
			$element 	= &$root->getElementsByPath( 'description', 1 );
			$descrip 	= $element ? trim( $element->getText() ) : '';
			// Menu Type Group
			$element 	= &$root->getElementsByPath( 'group', 1 );
			$group 		= $element ? trim( $element->getText() ) : '';
		}
	}

	if ( ( $component != -1 ) && ( $name == 'Component') ) {
			$name .= ' - '. $component;
	}

	$row[0]	= $name;
	$row[1] = $descrip;
	$row[2] = $group;

	return $row;
}

function saveOrder( &$cid, $menutype ) {
	global $database;

	$total		= count( $cid );
	$order 		= josGetArrayInts( 'order' );
	
	$row		= new mosMenu( $database );
	$conditions = array();

	// update ordering values
	for( $i=0; $i < $total; $i++ ) {
		$row->load( (int) $cid[$i] );
		if ($row->ordering != $order[$i]) {
			$row->ordering = $order[$i];
			if (!$row->store()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
				exit();
			}
			// remember to updateOrder this group
			$condition = "menutype = " . $database->Quote( $menutype ) . " AND parent = " . (int) $row->parent . " AND published >= 0";
			$found = false;
			foreach ( $conditions as $cond )
				if ($cond[1]==$condition) {
					$found = true;
					break;
				} 
			if (!$found) $conditions[] = array($row->id, $condition);
		} 
	} 

	// execute updateOrder for each group
	foreach ( $conditions as $cond ) {
		$row->load( $cond[0] );
		$row->updateOrder( $cond[1] );
	} 

	// clean any existing cache files
	mosCache::cleanCache( 'content' );
	
	$msg 	= 'Новый порядок сохранен';
	mosRedirect( 'index2.php?ca=menus&menutype='. $menutype, $msg );
} 

/**
* Returns list of child items for a given set of ids from menu items supplied
*
*/
function josMenuChildrenRecurse( $mitems, $parents, $list, $maxlevel=20, $level=0 ) {
	// check to reduce recursive processing
	if ( $level <= $maxlevel && count( $parents ) ) {
		$children = array();
		foreach ( $parents as $id ) {			
			foreach ( $mitems as $item ) {
				if ( $item->parent == $id ) {
					$children[] = $item->id;
				}		
			}
		}	
		
		// check to reduce recursive processing
		if ( count( $children ) ) {
			$list = josMenuChildrenRecurse( $mitems, $children, $list, $maxlevel, $level+1 );
			
			$list = array_merge( $list, $children );
		}
	}
	
	return $list;
}
?>