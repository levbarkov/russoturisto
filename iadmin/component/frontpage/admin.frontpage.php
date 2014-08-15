<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $mainframe, $option, $id, $my, $task;
// ensure user has access to this function
if (  $my->gid<24  ) {
	mosRedirect( 'index2.php', _NOT_AUTH );
}

// call
require_once( $mainframe->getPath( 'admin_html' ) );
require_once( $mainframe->getPath( 'class' ) );

$cid = josGetArrayInts( 'cid' );

switch ($task) {
	case 'publish':
		changeFrontPage( $cid, 1, $option );
		break;

	case 'unpublish':
		changeFrontPage( $cid, 0, $option );
		break;

	case 'archive':
		changeFrontPage( $cid, -1, $option );
		break;

	case 'remove':
		removeFrontPage( $cid, $option );
		break;

	case 'orderup':
		orderFrontPage( intval( $cid[0] ), -1, $option );
		break;

	case 'orderdown':
		orderFrontPage( intval( $cid[0] ), 1, $option );
		break;

	case 'saveorder':
		saveOrder( $cid );
		break;

	case 'accesspublic':
		accessMenu( intval( $cid[0] ), 0 );
		break;

	case 'accessregistered':
		accessMenu( intval( $cid[0] ), 1 );
		break;

	case 'accessspecial':
		accessMenu( intval( $cid[0] ), 2 );
		break;

	default:
		viewFrontPage( $option );
		break;
}


/**
* Compiles a list of frontpage items
*/
function viewFrontPage( $option ) {
	global $database, $mainframe, $mosConfig_list_limit;

	$catid 				= (int) icsmart('icsmart_frontpage_catid');

	$limit 		= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
	$limitstart = intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );
	$search 	= icsmart('icsmart_frontpage_search');
	if (get_magic_quotes_gpc()) {
		$search	= stripslashes( $search );
	}

	$where = array(
	"c.state >= 0"
	);

	// used by filter
	if ( $catid > 0 ) {
		$where[] = "c.catid = " . (int) $catid;
	}

	if ($search) {
		$where[] = "LOWER( c.title ) LIKE '%" . $database->getEscaped( trim( mb_strtolower( $search,"UTF-8" ) ) ) . "%'";
	}

	// get the total number of records
	$query = "SELECT count(*)"
	. "\n FROM #__content AS c"
	. "\n INNER JOIN #__icat AS cc ON cc.id = c.catid"
	. "\n INNER JOIN #__content_frontpage AS f ON f.content_id = c.id"
	. (count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : '' )
	;
	$database->setQuery( $query );
	$total = $database->loadResult();

	require_once( $GLOBALS['mosConfig_absolute_path'] . '/iadmin/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit );

	$query = "SELECT c.*, cc.name, u.name AS editor, f.ordering AS fpordering, v.name AS author"
	. "\n FROM #__content AS c"
	. "\n INNER JOIN #__icat AS cc ON cc.id = c.catid"
	. "\n INNER JOIN #__content_frontpage AS f ON f.content_id = c.id"
	. "\n LEFT JOIN #__users AS u ON u.id = c.checked_out"
	. "\n LEFT JOIN #__users AS v ON v.id = c.created_by"
	. (count( $where ) ? "\nWHERE " . implode( ' AND ', $where ) : "")
	. "\n ORDER BY f.ordering"
	;
	$database->setQuery( $query, $pageNav->limitstart,$pageNav->limit );

	$rows = $database->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	// get list of categories for dropdown filter
	$vcats = array();
	$vcats[] = mosHTML::makeOption( 0, "- Выберите рубрику -");
	do_icatlist (0, $vcats, 0);
	$lists['catid'] 	= mosHTML::selectList( $vcats, 'icsmart_frontpage_catid', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', $catid );

	HTML_content::showList( $rows, $search, $pageNav, $option, $lists );
}

/**
* Changes the state of one or more content pages
* @param array An array of unique category id numbers
* @param integer 0 if unpublishing, 1 if publishing
*/
function changeFrontPage( $cid=null, $state=0, $option ) {
	global $database, $my;

	if (count( $cid ) < 1) {
		$action = $state == 1 ? 'публикации' : ($state == -1 ? 'архивирования' : 'сокрытия');
		echo "<script> alert('Выберите объект для $action'); window.history.go(-1);</script>\n";
		exit;
	}

	mosArrayToInts( $cid );
	$cids = 'id=' . implode( ' OR id=', $cid );

	$query = "UPDATE #__content"
	. "\n SET state = " . (int) $state
	. "\n WHERE ( $cids )"
	. "\n AND ( checked_out = 0 OR ( checked_out = " . (int) $my->id . " ) )"
	;
	$database->setQuery( $query );
	if (!$database->query()) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}

	if (count( $cid ) == 1) {
		$row = new mosContent( $database );
		$row->checkin( $cid[0] );
	}

	// clean any existing cache files
	mosCache::cleanCache( 'content' );

	mosRedirect( "index2.php?ca=$option" );
}

function removeFrontPage( &$cid, $option ) {
	global $database;

	if (!is_array( $cid ) || count( $cid ) < 1) {
		echo "<script> alert('Выберите объект для удаления'); window.history.go(-1);</script>\n";
		exit;
	}
	$fp = new mosFrontPage( $database );
	foreach ($cid as $id) {
		if (!$fp->delete( (int)$id )) {
			echo "<script> alert('".$fp->getError()."'); </script>\n";
			exit();
		}
		$obj = new mosContent( $database );
		$obj->load( (int)$id );
		$obj->mask = 0;
		if (!$obj->store()) {
			echo "<script> alert('".$fp->getError()."'); </script>\n";
			exit();
		}
	}
	$fp->updateOrder();

	// clean any existing cache files
	mosCache::cleanCache( 'content' );
	mosRedirect( "index2.php?ca=$option" );
}

/**
* Moves the order of a record
* @param integer The increment to reorder by
*/
function orderFrontPage( $uid, $inc, $option ) {
	global $database;

	$fp = new mosFrontPage( $database );
	$fp->load( (int)$uid );
	$fp->move( $inc );

	// clean any existing cache files
	mosCache::cleanCache( 'content' );

	mosRedirect( "index2.php?ca=$option" );
}
function accessMenu( $uid, $access ) {
	global $database;

	$row = new mosContent( $database );
	$row->load( (int)$uid );
	$row->access = $access;

	if ( !$row->check() ) {
		return $row->getError();
	}
	if ( !$row->store() ) {
		return $row->getError();
	}
	mosCache::cleanCache( 'content' );
	mosRedirect( 'index2.php?ca=frontpage' );
}

function saveOrder( &$cid ) {
	global $database;

	$total		= count( $cid );
	$order 		= josGetArrayInts( 'order' );
//	ggtr ($order);
//	ggd ($cid);

	for( $i=0; $i < $total; $i++ ) {
		$query = "UPDATE #__content_frontpage"
		. "\n SET ordering = " . (int) $order[$i]
		. "\n WHERE content_id = " . (int) $cid[$i];
		$database->setQuery( $query );
		ggtr ($database);
		if (!$database->query()) {
			echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			exit();
		}

	}
	
	// clean any existing cache files
	mosCache::cleanCache( 'content' );

	$msg 	= 'Новый порядок сохранен';
	mosRedirect( 'index2.php?ca=frontpage', $msg );
}
?>