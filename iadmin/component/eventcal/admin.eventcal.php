<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

	require_once( 'admin.eventcal.html.php' );
	require_once( $mosConfig_absolute_path .'/component/eventcal/eventcal.class.php' );
	require_once( $mosConfig_absolute_path . '/iadmin/includes/pageNavigation.php' );

	if (file_exists( $mosConfig_absolute_path . "/component/eventcal/language/$mosConfig_lang.php" )) {
		require_once( $mosConfig_absolute_path . "/component/eventcal/language/$mosConfig_lang.php" );
	} else {
		require_once( $mosConfig_absolute_path . "/component/eventcal/language/english.php" );
	}

	$task		=	trim( mosGetParam( $_REQUEST, 'task', '' ) );
	$event_id	=	mosGetParam( $_REQUEST, 'cid', array(0) );
	$cat_id		=	mosGetParam( $_REQUEST, 'catid', '0' );
	$cid		=	$event_id;

	$field = mosGetParam($_REQUEST, 'field', false);
	$order = mosGetParam($_REQUEST, 'order', 'none');

switch ($task) {
	//the following cases are lend from com_categories
	//just by copy-paste
		case 'accesspublic':
		accessMenu( $cid[0], 0, 'com_eventcal' );
		mosRedirect( 'index2.php?ca=eventcal&task=categories' );
		break;
	case 'accessregistered':
		accessMenu( $cid[0], 1, 'com_eventcal' );
		mosRedirect( 'index2.php?ca=eventcal&task=categories' );
		break;
	case 'accessspecial':
		accessMenu( $cid[0], 2, 'com_eventcal' );
		mosRedirect( 'index2.php?ca=eventcal&task=categories' );
		break;
	case 'orderup':
		orderCategory( $cid[0], -1 );
		mosRedirect( 'index2.php?ca=eventcal&task=categories' );
		break;
	case 'orderdown':
		orderCategory( $cid[0], 1 );
		mosRedirect( 'index2.php?ca=eventcal&task=categories' );
		break;
	case 'saveorder':
		$msg = saveOrder( $cid, 'com_eventcal' );
		mosRedirect( 'index2.php?ca=eventcal&task=categories' . $section, $msg );
		break;		
	case 'publishcategory':
		publishCategories( $id, $cid, 1 );
		mosRedirect( 'index2.php?ca=eventcal&task=categories' );
		break;
	case 'unpublishcategory':
		publishCategories( $id, $cid, 0 );
		mosRedirect( 'index2.php?ca=eventcal&task=categories' );
		break;		

	//these are the own programs
	case "edit":
		$event_id = (is_array($event_id))?$event_id[0]:$event_id;
		newEventForm($event_id);
		break;
	case "new":
		newEventForm();
		break;
	case "save":
		saveEvent();
		StandardListView();
		break;
	case "unpublished":
 		StandardListView();
		break;
	case "publish":
		publishItem($event_id,1);
		//returnMessage("Publishing successfull");
		StandardListView();
		break;
	case "storeconfig":
		$error = storeConfigFile();
		if ( $error === true ) {
			mosRedirect( "index2.php?ca=eventcal&task=config", "new settings stored");
		} else {
			mosRedirect( "index2.php?ca=eventcal&task=config", "$error");
		}
		break;
	case "config":
		mkConfigForm();
		break;
	case "apply":
		$colors = mosGetParam($_REQUEST, "color", array());
		saveCategoryParams($event_id,$colors);
	//these two entries are here for downwards compatibility
	//the old version is "colorcategories"
	case "categories":
	case "colorcategories":
		CategoryView();
		break;
	case "unpublish":
		publishItem($event_id,0);
		StandardListView();
		break;
	case "remove":
		deleteItems($event_id);
		StandardListView();
		break;
	case "cancel":
		$event = new mosEventCal_Event();
		$event->checkin($id); //the id is defined globally
		$cat_id = 0;
	default:
		StandardListView();
		break;
}


/**
* changes the access level of a record
* @param integer The increment to reorder by
*/
function accessMenu( $uid, $access, $section ) {
	global $database;

	$row = new mosCategory( $database );
	$row->load( $uid );
	$row->access = $access;

	if ( !$row->check() ) {
		return $row->getError();
	}
	if ( !$row->store() ) {
		return $row->getError();
	}
}

/**
* Moves the order of a record
* @param integer The increment to reorder by
*/
function orderCategory( $uid, $inc ) {
	global $database;

	$row = new mosCategory( $database );
	$row->load( $uid );
	$row->move( $inc, "section='com_eventcal'" );
}

function saveOrder( &$cid, $section ) {
	global $database;

	$total		= count( $cid );
	$order 		= mosGetParam( $_POST, 'order', array(0) );
	$row		= new mosCategory( $database );
	$conditions = array();

    // update ordering values
	for( $i=0; $i < $total; $i++ ) {
		$row->load( $cid[$i] );
		if ($row->ordering != $order[$i]) {
			$row->ordering = $order[$i];
	        if (!$row->store()) {
	            echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
	            exit();
	        } // if
	        // remember to updateOrder this group
	        $condition = "section='$row->section'";
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

	return 'New ordering saved';
}


/**
* Publishes or Unpublishes one or more categories
* @param integer A unique category id (passed from an edit form)
* @param array An array of unique category id numbers
* @param integer 0 if unpublishing, 1 if publishing
* @param string The name of the current user
*/
function publishCategories( $categoryid=null, $cid=null, $publish=1 ) {
	global $database, $my;

	if (!is_array( $cid )) {
		$cid = array();
	}
	if ($categoryid) {
		$cid[] = $categoryid;
	}

	if (count( $cid ) < 1) {
		$action = $publish ? 'publish' : 'unpublish';
		echo "<script> alert('Select a category to $action'); window.history.go(-1);</script>\n";
		exit;
	}

	$cids = implode( ',', $cid );

	$query = "UPDATE #__categories SET published='$publish'"
	. "\nWHERE id IN ($cids) AND (checked_out=0 OR (checked_out='$my->id'))"
	;
	$database->setQuery( $query );
	if (!$database->query()) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}

	if (count( $cid ) == 1) {
		$row = new mosCategory( $database );
		$row->checkin( $cid[0] );
	}
}





//function to display the form for editing or adding an event
function newEventForm($existing = NULL) {
  global $my;

  if (is_numeric($existing)) {
	  $event = new mosEventCal_Event();
		$event->load($existing);	
		$existing = $event;
		$existing->checkout( $my->id );
	}
	HTML_admin_eventcal::mkEventForm($existing);	
}

	function saveEvent() {
		$event = new mosEventCal_Event();
		$event->bind( $_REQUEST, '' );
		if ($event->check($error)) {
			if ($event->store()) {
				return true;
			} else return false;
		} else {
			echo $error;
			return false;
		}
	}


// deletes all passed events from database

function deleteItems($itemids) {
  $event = new mosEventCal_Event();
	foreach($itemids AS $itemid) {
	  $event->delete($itemid);	
	}
}


//this function creates the standard list-view like the other components do
function StandardListView( $searchstring = NULL ) {
  global $mainframe, $database, $mosConfig_list_limit, $cat_id, $field, $order, $task;

  if ( $field && $order <> "none" ) {
    $ordering = "\n ORDER BY e.$field " . $order;
  } else {
    $ordering = "\n ORDER BY e.start_date DESC, e.recur_week";
  }

  if (isset($searchstring)) $searcharray[] = $searchstring;
  
  if(($cat_id <> 0) && is_numeric($cat_id) ) {
	  $searcharray[] = " e.catid=" . $cat_id;
  } else if ($cat_id === "old") {
    $today = getDate();
    $searcharray[] = "e.end_date < $today[0]";
  }

  if ($task == "unpublished") {
    $searcharray[] = "e.published = '0'";
  }

    //filter-options
      $search = mosGetParam($_REQUEST, "search", false);
      if ($search)
        $searcharray[] = "(e.title LIKE '%$search%' OR e.description LIKE '%$search%')";
        
  $searchstring = (@$searcharray)?implode(" AND ", $searcharray):"1";
    
  $database->setQuery( "SELECT COUNT(*) FROM #__eventcal AS e WHERE $searchstring");

  // page navigation
  $total = $database->loadResult();
  $limitstart = $mainframe->getUserStateFromRequest( "view{eventcal}", 'limitstart', 0 );
  $limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit );    
  $pageNav = new mosPageNav( $total, $limitstart, $limit );

  $database->setQuery(
    "SELECT e.*, u.name AS editor, c.title AS cat_name, c.params AS cat_params FROM #__eventcal AS e" .
    "\n LEFT JOIN #__users AS u ON u.id = e.checked_out" .
		"\n LEFT JOIN #__categories AS c ON c.id = e.catid" .
    "\n WHERE " . $searchstring .
    $ordering .
    "\n LIMIT $pageNav->limitstart, $pageNav->limit"
  );
  $results = $database->loadObjectList();
	
 	$categories[] = mosHTML::makeOption( "0", _ALL_EVENTS );
  $categories[] = mosHTML::makeOption( "old", _OLD_EVENTS );
  
 	$query = "SELECT id AS value, name AS text"
         . "\n FROM #__categories"
         . "\n WHERE section = 'com_eventcal'"
	       . "\n AND published = '1'";
  $database->setQuery( $query );
  $categories = array_merge( $categories, $database->loadObjectList() );

	$catlist = mosHTML::selectList( $categories, 'catid', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $cat_id );

	HTML_admin_eventcal::mkStandardTable($results, $pageNav, $catlist );
}



	function saveCategoryParams($cat_ids, $colors) {
		global $database;
		$autopublish = mosGetParam($_REQUEST, "autopublish", array());
		$category = new mosCategory( $database );
		foreach($cat_ids As $catid) {
			$category->load($catid);
			if (!$category->id) {
				continue;
			}
			$parameters =& new mosParameters( $category->params );
			if (in_array($catid, $autopublish)) {
				$parameters->set("autopublish","true");
			}
			else {
				$parameters->set("autopublish","false");
			}
			if(@$colors[$catid] != "")
				$parameters->set("color",$colors[$catid]);
	
			$txt = array();
			foreach ($parameters->_params as $k=>$v) {
				$txt[] = "$k=$v";
			}
			$category->params = mosParameters::textareaHandling( $txt );
			$category->store();
		}
	}


	/**
	 * displays teh Configuration / Start-Page of eventCal's admin interface
	*/
	function mkConfigForm() {
		global $database;

		$database->setQuery(	"SELECT lft, rgt FROM #__core_acl_aro_groups WHERE name = 'Public Backend'" );
		$borders			=	$database->loadRow();
		$database->setQuery(	"SELECT group_id AS id, name" .
								"\n FROM #__core_acl_aro_groups" .
								"\n WHERE lft > $borders[0]" .
								"\n AND  rgt < $borders[1]" .
								"\n ORDER BY lft DESC"
		);
		$groups = $database->loadObjectList();

		//load config values
			$database->setQuery(	"SELECT params FROM #__components WHERE link = 'ca=eventcal'" );
			$text			=	$database->loadResult();
			$config_values	=	new mosParameters( $text );
			

		HTML_admin_eventcal::configPage( $config_values, $groups );
	}


	function storeConfigFile() {
		global $mosConfig_absolute_path, $database;

		$params = new mosParameters('');

		//get group_role_permissions from Post-Data
			$group_roles_new_auto		=	mosGetParam(	$_REQUEST,	'newauto',		array('') );
			$group_roles_new_all		=	mosGetParam(	$_REQUEST,	'newall',		array('') );
			$group_roles_changes_auto	=	mosGetParam(	$_REQUEST,	'changesauto',	array('') );
			$group_roles_changes_all	=	mosGetParam(	$_REQUEST,	'changesall',	array('') );

			$params->set( 'newauto',				implode( ',', $group_roles_new_auto ) );
			$params->set( 'newall',					implode( ',', $group_roles_new_all ) );
			$params->set( 'changesauto',			implode( ',', $group_roles_changes_auto ) );
			$params->set( 'changesall',				implode( ',', $group_roles_changes_all ) );

		//get timetable from Post Data and implode it to an array
			$timetable					=	mosGetParam(	$_REQUEST,	'timetable',	array() );
			$timetable					=	serialize( $timetable );
			
			$params->set( 'timetable',				$timetable);

		//get other settings
			$who_can_post_events		=	intval(mosGetParam( $_REQUEST, "who_can_post_events", 2 ));
			$who_can_edit_events		=	intval(mosGetParam( $_REQUEST, "who_can_edit_events", 2 ));
			$week_startingday			=	intval(mosGetParam( $_REQUEST, "week_startingday", 0 ));
			$show_weeknumber			=	intval(mosGetParam( $_REQUEST, "show_weeknumber", 1 ));
			$showBackButton				=	intval(mosGetParam( $_REQUEST, "showBackButton", 1 ));
			$week_number_links			=	intval(mosGetParam( $_REQUEST, "week_number_links", 0 ));
			$default_view				=	mosGetParam( $_REQUEST, "default_view", "month" );

			$params->set( 'who_can_post_events',	$who_can_post_events );
			$params->set( 'who_can_edit_events',	$who_can_edit_events );
			$params->set( 'week_startingday',		$week_startingday );
			$params->set( 'show_weeknumber',		$show_weeknumber );
			$params->set( 'showBackButton',			$showBackButton );
			$params->set( 'week_number_links',		$week_number_links );
			$params->set( 'default_view',			$default_view);

		//store params
			$params_as_text = array();
			foreach ($params->_params as $k=>$v) {
				$params_as_text[] = "$k=$v";
			}
			$database->setQuery(	"UPDATE #__components SET params = \n'" .
									mosParameters::textareaHandling( $params_as_text ) .
									"'\n WHERE link = 'ca=eventcal'" );
			if ($database->query()) {
				return true;
			} else {
				return false;
			}
	}


function CategoryView() {
  global $database, $mainframe, $mosConfig_list_limit;
   
	$section_name = "com_eventcal";

// page navigation
	$query = "SELECT count(*) FROM #__categories WHERE section='$section_name'";
	$database->setQuery( $query );
	$total = $database->loadResult();
  $limitstart = $mainframe->getUserStateFromRequest( "view{eventcal}", 'limitstart', 0 );
  $limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit );
	$pageNav = new mosPageNav($total,$limitstart, $limit);

//load categories
  $query = "SELECT  c.*, c.checked_out AS checked_out, g.name AS groupname, u.name AS editor"
	. "\n FROM #__categories AS c"
	. "\n LEFT JOIN #__users AS u ON u.id = c.checked_out"
	. "\n LEFT JOIN #__groups AS g ON g.id = c.access"
	. "\n WHERE section = '$section_name'"
	. "\n AND c.published != -2"
	. "\n GROUP BY c.id"
	. "\n ORDER BY c.ordering"					
	. "\n LIMIT $pageNav->limitstart, $pageNav->limit";
	$database->setQuery( $query );
	$categories = $database->loadObjectList(); 
	
  HTML_admin_eventcal::categoryParams($categories, $section_name, $pageNav);
}



function publishItem($event_id, $value) {
	$event = new mosEventCal_Event();
	foreach($event_id AS $id) {
	  $event->load($id);
      $event->published = $value;
      $event->store();
	}
}


//function that allows to get single values from Params-String
function getParam($key, $parameter) {
  $params =& new mosParameters( $parameter );
  $return = $params->get( $key, false );
	unset( $params );
	return $return;

  /*
	if (!$params) return false;
	$params = split("\n",$params);
	foreach ($params AS $param) {
	  list($keyn,$value) = split('=', $param);
	  if ($keyn == $key) return $value;
  }
  return false;
	*/
}




?>