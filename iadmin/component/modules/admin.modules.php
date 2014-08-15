<?php
// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my, $mainframe, $task, $id;

// ensure user has access to this function
if (  $my->gid<24  ) {
	mosRedirect( 'index2.php', _NOT_AUTH );
}

require_once( $mainframe->getPath( 'admin_html' ) );

$client 	= strval( mosGetParam( $_REQUEST, 'client', '' ) );
$moduleid 	= mosGetParam( $_REQUEST, 'moduleid', null );

$cid 		= josGetArrayInts( 'cid' );

if ($cid[0] == 0 && isset($moduleid) ) {
	$cid[0] = $moduleid;
}

switch ( $task ) {
	case 'copy':
		copyModule( $option, intval( $cid[0] ), $client );
		break;

	case 'new':
		editModule( $option, 0, $client );
		break;

	case 'edit':
		editModule( $option, intval( $cid[0] ), $client );
		break;

	case 'editA':
		editModule( $option, $id, $client );
		break;

	case 'save':
	case 'apply':
		saveModule( $option, $client, $task );
		break;

	case 'remove':
		removeModule( $cid, $option, $client );
		break;

	case 'cancel':
		cancelModule( $option, $client );
		break;

	case 'publish':
	case 'unpublish':
		publishModule( $cid, ($task == 'publish'), $option, $client );
		break;

	case 'orderup':
	case 'orderdown':
		orderModule( intval( $cid[0] ), ($task == 'orderup' ? -1 : 1), $option );
		break;

	case 'accesspublic':
	case 'accessregistered':
	case 'accessspecial':
		accessMenu( intval( $cid[0] ), $task, $option, $client );
		break;

	case 'saveorder':
		saveOrder( $cid, $client );
		break;

	default:
		viewModules( $option, $client );
		break;
}

/**
* Compiles a list of installed or defined modules
*/
function viewModules( $option, $client ) {
	global $database, $my, $mainframe, $mosConfig_list_limit, $mosConfig_absolute_path;

	//$filter_position 	= $mainframe->getUserStateFromRequest( "filter_position{$option}{$client}", 'filter_position', 0 );
	if (  icsmart('icsmart_modules_filter_position')  ) {
		$filter_position = $database->getEscaped(  icsmart('icsmart_modules_filter_position')  );
	}
	$filter_type	 	= $mainframe->getUserStateFromRequest( "filter_type{$option}{$client}", 'filter_type', 0 );
	$limit 				= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
	$limitstart 		= intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );
	//$search 			= $mainframe->getUserStateFromRequest( "search{$option}{$client}", 'search', '' );
	if (  icsmart('icsmart_modules_search')  ) {
		$search = $database->getEscaped(  icsmart('icsmart_modules_search')  );
	}

	if (get_magic_quotes_gpc()) {
		$search				= stripslashes( $search );
		$filter_position	= stripslashes( $filter_position );
		$filter_type		= stripslashes( $filter_type );
	}

	if ($client == 'admin') {
		$where[] 	= "m.client_id = 1";
		$client_id = 1;
	} else {
		$where[] 	= "m.client_id = 0";
		$client_id = 0;
		$client 	= '';
	}

	// used by filter
	if ( $filter_position ) {
		$where[] = "m.position = " . $database->Quote( $filter_position );
	}
	if ( $filter_type ) {
		$where[] = "m.module = " . $database->Quote( $filter_type );
	}
	if ( $search ) {
		$where[] = "LOWER( m.title ) LIKE '%" . $database->getEscaped( trim( mb_strtolower( $search,"UTF-8" ) ) ) . "%'";
	}

	// get the total number of records
	$query = "SELECT COUNT(*)"
	. "\n FROM #__modules AS m"
	. ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : '' )
	;
	$database->setQuery( $query );
	$total = $database->loadResult();

	require_once( site_path . '/iadmin/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit );

	$query = "SELECT m.*, u.name AS editor"
	. "\n FROM #__modules AS m"
	. "\n LEFT JOIN #__users AS u ON u.id = m.checked_out"
//	. "\n LEFT JOIN #__groups AS g ON g.id = m.access"
	. ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : '' )
	. "\n GROUP BY m.id"
	. "\n ORDER BY position ASC, ordering ASC"
	;
	$database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$rows = $database->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	// get list of Positions for dropdown filter
	$query = "SELECT t.position AS value, t.position AS text"
	. "\n FROM #__theme_positions as t"
	. "\n LEFT JOIN #__modules AS m ON m.position = t.position"
	. "\n WHERE m.client_id = " . (int) $client_id
	. "\n GROUP BY t.position"
	. "\n ORDER BY t.position"
	;
	$positions[] = mosHTML::makeOption( '0', _SEL_POSITION );
	$database->setQuery( $query );
	$positions = array_merge( $positions, $database->loadObjectList() );
	$lists['position']	= mosHTML::selectList( $positions, 'icsmart_modules_filter_position', 'class="inputtop" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', "$filter_position" );

	// get list of Positions for dropdown filter
	$query = "SELECT module AS value, module AS text"
	. "\n FROM #__modules"
	. "\n WHERE client_id = " . (int) $client_id
	. "\n GROUP BY module"
	. "\n ORDER BY module"
	;
	$types[] = mosHTML::makeOption( '0', _SEL_TYPE );
	$database->setQuery( $query );
	$types = array_merge( $types, $database->loadObjectList() );
	$lists['type']	= mosHTML::selectList( $types, 'filter_type', 'class="inputtop" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', "$filter_type" );

	HTML_modules::showModules( $rows, $my->id, $client, $pageNav, $option, $lists, $search );
}

/**
* Compiles information to add or edit a module
* @param string The current GET/POST option
* @param integer The unique id of the record to edit
*/
function copyModule( $option, $uid, $client ) {
	global $database, $my;

	$row = new mosModule( $database );
	// load the row from the db table
	$row->load( (int)$uid );
	$row->title 		= 'Копия '.$row->title;
	$row->id 			= 0;
	$row->iscore 		= 0;
	$row->published 	= 0;

	if (!$row->check()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	$row->checkin();
	if ($client == 'admin') {
		$where = "client_id='1'";
	} else {
		$where = "client_id='0'";
	}
	$row->updateOrder( 'position=' . $database->Quote( $row->position ) . " AND ($where)" );

	mosCache::cleanCache( 'com_content' );
	
	$msg = 'Module Copied ['. $row->title .']';
	mosRedirect( 'index2.php?ca='. $option .'&client='. $client, $msg );
}

/**
* Saves the module after an edit form submit
*/
function saveModule( $option, $client, $task ) {
	global $database;

	$params = mosGetParam( $_POST, 'params', '' );
	if (is_array( $params )) {
		$txt = array();
		foreach ($params as $k=>$v) {
			$txt[] = "$k=$v";
		}
		$_POST['params'] = mosParameters::textareaHandling( $txt );
	}

	$row = new mosModule( $database );
	if (!$row->bind( $_POST, 'selections' )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	if (!$row->check()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	if (  $row->id==""  )	$row->id=0; // корректировка специально для только созданных пунктов меню
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	$row->checkin();
	if ($client == 'admin') {
		$where = "client_id=1";
	} else {
		$where = "client_id=0";
	}
	$row->updateOrder( 'position=' . $database->Quote( $row->position ) . " AND ($where)" );

	$menus 	= josGetArrayInts( 'selections' );

	mosCache::cleanCache( 'com_content' );
	
	switch ( $task ) {
		case 'apply':
			$msg = 'Все изменения модуля  - '. $row->title.' - успешно сохранены';
			mosRedirect( 'index2.php?ca='. $option .'&client='. $client .'&task=editA&hidemainmenu=1&id='. $row->id, $msg );
			break;

		case 'save':
		default:
			$msg = 'Изменения сохранены';
			mosRedirect( 'index2.php?ca='. $option .'&client='. $client, $msg );
			break;
	}
}

/**
* Compiles information to add or edit a module
* @param string The current GET/POST option
* @param integer The unique id of the record to edit
*/
function editModule( $option, $uid, $client ) {
	global $database, $my, $mainframe;
	global $mosConfig_absolute_path;

	$lists = array(); 
	$row = new mosModule( $database );
	// load the row from the db table
	$row->load( (int)$uid );
	// fail if checked out not by 'me'
	//if ($row->isCheckedOut( $my->id )) {
	//	mosErrorAlert( "Модуль ".$row->title." в настоящее время редактируется другим администратором" );
	//}

	$row->content = desafelySqlStr( $row->content );

	if ( $uid ) {
		$row->checkout( $my->id );
	}
	// if a new record we must still prime the mosModule object with a default
	// position and the order; also add an extra item to the order list to
	// place the 'new' record in last position if desired
	if ($uid == 0) {
		$row->position 	= 'left';
		$row->showtitle = true;
		//$row->ordering = $l;
		$row->published = 1;
	}


	if ( $client == 'admin'  ) {
		$where 				= "client_id = 1";
		$lists['client_id'] = 1;
		$path				= 'mod1_xml';
	} else {
		$where 				= "client_id = 0";
		$lists['client_id'] = 0;
		$path				= 'mod0_xml';
	}
	$query = "SELECT position, ordering, showtitle, title"
	. "\n FROM #__modules"
	. "\n WHERE $where"
	. "\n ORDER BY ordering"
	;
	$database->setQuery( $query );
	if ( !($orders = $database->loadObjectList()) ) {
		echo $database->stderr();
		return false;
	}

	$query = "SELECT position, description"
	. "\n FROM #__theme_positions"
	. "\n WHERE position != ''"
	. "\n ORDER BY position"
	;
	$database->setQuery( $query );
	// hard code options for now
	$positions = $database->loadObjectList();

	$orders2 = array();
	$pos = array();
	foreach ($positions as $position) {
		$orders2[$position->position] = array();
		$pos[] = mosHTML::makeOption( $position->position, $position->description );
	}

	$l = 0;
	$r = 0;
	for ($i=0, $n=count( $orders ); $i < $n; $i++) {
		$ord = 0;
		if (array_key_exists( $orders[$i]->position, $orders2 )) {
			$ord =count( array_keys( $orders2[$orders[$i]->position] ) ) + 1;
		}

		$orders2[$orders[$i]->position][] = mosHTML::makeOption( $ord, $ord.'::'.addslashes( $orders[$i]->title ) );
	}
	
	// build the html select list
	$pos_select = 'onchange="changeDynaList(\'ordering\',orders,document.adminForm.position.options[document.adminForm.position.selectedIndex].value, originalPos, originalOrder)"';
	$active = ( $row->position ? $row->position : 'left' );
	$lists['position'] = mosHTML::selectList( $pos, 'position', 'class="inputbox" size="1" '. $pos_select, 'value', 'text', $active );

	// get selected pages for $lists['selections']
        $lookup = array( mosHTML::makeOption( 0, 'All' ) );

	if ( $row->access == 99 || $row->client_id == 1 || $lists['client_id'] ) {
		$lists['access'] 			= 'Administrator<input type="hidden" name="access" value="99" />';
		$lists['showtitle'] 		= 'N/A <input type="hidden" name="showtitle" value="1" />';
		$lists['selections'] 		= 'N/A';
	} else {
		if ( $client == 'admin' ) {
			$lists['access'] 		= 'N/A';
			$lists['selections'] 	= 'N/A';
		} else {
			$lists['access'] 		= mosAdminMenus::Access( $row );
			$lists['selections'] 	= mosAdminMenus::MenuLinks( $lookup, 1, 1 );
		}
		$lists['showtitle'] = mosHTML::yesnoRadioList( 'showtitle', 'class="inputbox"', $row->showtitle );
	}

	// build the html select list for published
	$lists['published'] 			= mosAdminMenus::Published( $row );

	$row->description = '';
	// XML library
	require_once( $mosConfig_absolute_path . '/includes/domit/xml_domit_lite_include.php' );
	// xml file for module
	$xmlfile = $mainframe->getPath( $path, $row->module );
	if($xmlfile != NULL){
		$xmlDoc = new DOMIT_Lite_Document();
		$xmlDoc->resolveErrors( true );
		if ($xmlDoc->loadXML( $xmlfile, false, true )) {
			$root = &$xmlDoc->documentElement;
			if ($root->getTagName() == 'mosinstall' && $root->getAttribute( 'type' ) == 'module' ) {
				$element = &$root->getElementsByPath( 'description', 1 );
				$row->description = $element ? trim( $element->getText() ) : '';
			}
		}
	}
	// get params definitions
	$params = new mosParameters( $row->params, $xmlfile, 'module' );

	HTML_modules::editModule( $row, $orders2, $lists, $params, $option );
}

/**
* Deletes one or more modules
*
* Also deletes associated entries in the #__module_menu table.
* @param array An array of unique category id numbers
*/
function removeModule( &$cid, $option, $client ) {
	global $database, $my;

	if (count( $cid ) < 1) {
		echo "<script> alert('Select a module to delete'); window.history.go(-1);</script>\n";
		exit;
	}

	mosArrayToInts( $cid );
	$cids = 'id=' . implode( ' OR id=', $cid );

	$query = "SELECT id, module, title, iscore, params"
	. "\n FROM #__modules WHERE ( $cids )"
	;
	$database->setQuery( $query );
	if (!($rows = $database->loadObjectList())) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit;
	}

	$err = array();
	$cid = array();
	foreach ($rows as $row) {
		if ($row->module == '' || $row->iscore == 0) {
			$cid[] = $row->id;
		} else {
			$err[] = $row->title;
		}
		// mod_mainmenu modules only deletable via Menu Manager
		if ( $row->module == 'mod_mainmenu' ) {
			if ( strstr( $row->params, 'mainmenu' ) ) {
				echo "<script> alert('Вы не можете удалить модуль mod_mainmenu, отображаемый как \'mainmenu\', т.к. это ядро меню'); window.history.go(-1); </script>\n";
				exit;
			}
		}
	}

	if (count( $cid )) {
		mosArrayToInts( $cid );
		$cids = 'id=' . implode( ' OR id=', $cid );
		$query = "DELETE FROM #__modules"
		. "\n WHERE ( $cids )"
		;
		$database->setQuery( $query );
		if (!$database->query()) {
			echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			exit;
		}
		// mosArrayToInts( $cid ); // just done a few lines earlier
		$cids = 'moduleid=' . implode( ' OR moduleid=', $cid );

		$mod = new mosModule( $database );
		$mod->ordering = 0;
		$mod->updateOrder( "position='left'" );
		$mod->updateOrder( "position='right'" );
	}

	if (count( $err )) {
		$cids = addslashes( implode( "', '", $err ) );
		echo "<script>alert('Модули: \'$cids\' не могут быть удалены, т.к. они могут быть только деинсталлированы, как все модули Joomla!');</script>\n";
	}

	mosCache::cleanCache( 'com_content' );

	mosRedirect( 'index2.php?ca='. $option .'&client='. $client );
}

/**
* Publishes or Unpublishes one or more modules
* @param array An array of unique record id numbers
* @param integer 0 if unpublishing, 1 if publishing
*/
function publishModule( $cid=null, $publish=1, $option, $client ) {
	global $database, $my;

	if (count( $cid ) < 1) {
		$action = $publish ? 'публикации' : 'сокрытия';
		echo "<script> alert('Выберите модуль для $action'); window.history.go(-1);</script>\n";
		exit;
	}

	mosArrayToInts( $cid );
	$cids = 'id=' . implode( ' OR id=', $cid );

	$query = "UPDATE #__modules"
	. "\n SET published = " . (int) $publish
	. "\n WHERE ( $cids )"
	. "\n AND ( checked_out = 0 OR ( checked_out = " . (int) $my->id . " ) )"
	;
	$database->setQuery( $query );
	if (!$database->query()) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}

	if (count( $cid ) == 1) {
		$row = new mosModule( $database );
		$row->checkin( $cid[0] );
	}

	mosCache::cleanCache( 'com_content' );
	
	mosRedirect( 'index2.php?ca='. $option .'&client='. $client );
}

/**
* Cancels an edit operation
*/
function cancelModule( $option, $client ) {
	global $database;

	//$row = new mosModule( $database );
	//// ignore array elements
	//$row->bind( $_POST, 'selections params' );
	//$row->checkin();

	mosRedirect( 'index2.php?ca='. $option .'&client='. $client );
}

/**
* Moves the order of a record
* @param integer The unique id of record
* @param integer The increment to reorder by
*/
function orderModule( $uid, $inc, $option ) {
	global $database;

	$client = strval( mosGetParam( $_POST, 'client', '' ) );

	$row = new mosModule( $database );
	$row->load( (int)$uid );
	if ($client == 'admin') {
		$where = "client_id = 1";
	} else {
		$where = "client_id = 0";
	}

	$row->move( $inc, "position = " . $database->Quote( $row->position ) . " AND ( $where )"  );
	if ( $client ) {
		$client = '&client=admin' ;
	} else {
		$client = '';
	}

	mosCache::cleanCache( 'com_content' );
	
	mosRedirect( 'index2.php?ca='. $option .'&client='. $client );
}

/**
* changes the access level of a record
* @param integer The increment to reorder by
*/
function accessMenu( $uid, $access, $option, $client ) {
	global $database;

	switch ( $access ) {
		case 'accesspublic':
			$access = 0;
			break;

		case 'accessregistered':
			$access = 1;
			break;

		case 'accessspecial':
			$access = 2;
			break;
	}

	$row = new mosModule( $database );
	$row->load( (int)$uid );
	$row->access = $access;

	if ( !$row->check() ) {
		return $row->getError();
	}
	if ( !$row->store() ) {
		return $row->getError();
	}

	mosCache::cleanCache( 'com_content' );
	
	mosRedirect( 'index2.php?ca='. $option .'&client='. $client );
}

function saveOrder( &$cid, $client ) {
	global $database;
	
	$total		= count( $cid );
	$order 		= josGetArrayInts( 'order' );
	
	$row 		= new mosModule( $database );
	$conditions = array();

	// update ordering values
	for( $i=0; $i < $total; $i++ ) {
		$row->load( (int) $cid[$i] );
		if ($row->ordering != $order[$i]) {
			$row->ordering = $order[$i];
			if (!$row->store()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
				exit();
			} // if
			// remember to updateOrder this group
			$condition = "position = " . $database->Quote( $row->position ) . " AND client_id = " . (int) $row->client_id;
			$found = false;
			foreach ( $conditions as $cond )
				if ($cond[1]==$condition) {
					$found = true;
					break;
				} // if
			if (!$found) $conditions[] = array($row->id, $condition);
		} // if
	} // for

	// execute updateOrder for each group
	foreach ( $conditions as $cond ) {
		$row->load( $cond[0] );
		$row->updateOrder( $cond[1] );
	} // foreach

	mosCache::cleanCache( 'com_content' );

	$msg 	= 'Порядок сохранен';
	mosRedirect( 'index2.php?ca=modules&client='. $client, $msg );
} // saveOrder
?>