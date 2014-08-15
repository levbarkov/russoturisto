<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

global $mosConfig_absolute_path, $database;
//require_once( $mainframe->getPath( 'front_html' ) );
require_once ($mosConfig_absolute_path .'/component/eventcal/eventcal.html.php');
require_once ($mosConfig_absolute_path .'/component/eventcal/eventcal.class.php');
//require_once( $mainframe->getPath( 'class' ) );
require_once( $mosConfig_absolute_path . '/component/eventcal/includes/recursion.php' );

//get config_variables for compatibility
	$database->setQuery( "SELECT params FROM #__components WHERE link = 'option=com_eventcal'" );
	$text		= $database->loadResult();
	$params		=& new mosParameters( $text );
	$who_can_post_events	= $params->get( 'who_can_post_events' , 2);
	$who_can_edit_events	= $params->get( 'who_can_edit_events' , 2);
	$week_startingday		= $params->get( 'week_startingday' , 0);
	$show_weeknumber		= $params->get( 'show_weeknumber', false );
	$showBackButton			= $params->get( 'showBackButton' , false );
	$week_number_links		= $params->get( 'week_number_links' , false );
	$default_view			= $params->get( 'default_view' , 'month' );
	$timetable				= unserialize( $params->get( 'timetable', 'a:1:{i:0;s:10:"0:00-23:59";}') );
	for ( $i = 0; $i <count($timetable); $i++ ) {
		$timetable[$i] = split( "-", $timetable[$i]);
	}


//for offset calculation I need english week-day-names
//I need sunday as reference to [0] and [7] to avoid problems with php-conversions
	$week_days	= Array("sunday","monday","tuesday","wednesday","thursday","friday","saturday","sunday");

// load Parameters from Menuitem
	$menu		=& new mosMenu( $database );
	$menu->load( $Itemid );
	$params		=& new mosParameters( $menu->params );


//include config-file if exists otherwise exit with errror-message
/*	if (file_exists($mosConfig_absolute_path . "/components/com_eventcal/eventcal_config.php"))
		require_once( $mosConfig_absolute_path . "/components/com_eventcal/eventcal_config.php" );
	else {
		HTML_EventCal::redirect_dialog(_NO_ACCESS_CAP, _UNVALID_ACCESS, "<input type=\"button\" onClick=\"history.back();\" value=\"" . _CMN_PREV . "\"/>");
		exit;
	}
*/

//include language-file if exists otherwise use german.php as default
	$language_file		= (string) $params->get( 'language_file', $mosConfig_lang . ".php");
	$language_file		= ( file_exists($mosConfig_absolute_path . "/components/com_eventcal/language/" . $language_file) )?$language_file:"english.php";
	require_once( $mosConfig_absolute_path . "/component/eventcal/language/" . $language_file);

//get values from menu-param or REQUEST-string
	$task				= (string) mosGetParam( $_REQUEST, 'task' , $params->get( 'view', $default_view));
	$event_id			= intval( mosGetParam( $_REQUEST, 'eventid' , false) );

//if the select-list is diplayed on top of the page or not
	$show_selectlist	= (boolean) $params->get( 'show_selectlist', false);
//if the category-list at the bottom of pages is diplayed or not
	$view_catlist		= (boolean) $params->get( 'view_catlist', true );
//if the weeknumbers in monthly-view are displayed or not
	$show_weeknumber	= (boolean) $params->get( 'show_weeknumber', $show_weeknumber);

//try to get catid, if not available have a look into the menu item params
//if empty but available do not substitute by menu-item-value
	if (isset($_REQUEST["catid"])) {
		$catid = mosGetParam( $_REQUEST, 'catid' , "");
	}
	else {
		$catid = $params->get( 'catid', false );
	}

	parseCatid( $catid );

switch($task) {
	//displays empty event-form
	case 'addevent' :
		print_event_form( false, $catid );
		$view_catlist = false;
	break;

	//displayes event-form with actual event
	case 'eventform' :
		print_event_form( $event_id, $catid );
		$view_catlist = false;
	break;

	//displayes single event after choosing it from a view
	case 'event' :
		print_event_view($event_id);
		$view_catlist = false;
	break;

	//shows daily view
	case 'day' :
		print_day_view( $catid );
	break;

	//shows weekly view
	case 'week' :
		print_week_view( $catid );
	break;

	//shows monthly view
	case 'month' :
		print_month_view( $catid );
	break;

	//shows event-list for one single category
	case 'cat' :
		print_category_list( $catid );
	break;

	case 'event_submit':
		doSubmitEntry();
	break;

	//should never be processed 'cause of $default_view
	default:
		mosRedirect( $mosConfig_live_site . "/index.php?option=com_eventcal&task=" . $default_view, _UNVALID_ACCESS );
	break;
}


//if the category-list is shown beneath the displayed calendar(-item)
/*if ($view_catlist) {     */ 
if (0) { // MAXX TURN OFF CATEGORIES
	if ( isset($catid) ) {
		$catids = (is_array($catid))?$catid:array($catid);
		HTML_EventCal::category_list(4,true,getCategories(),$catids);
	}
	else {
		HTML_EventCal::category_list(4,true,getCategories(),false);
	}
}





	/**
	 * function to display the editor-form for events
	* @author	Kay Messerschmidt
	* @param	integer			existing		for form with existing event set true
	* @param	integer			catid			if you want to preselect a category pass an existing cat_id
	*/
	function print_event_form ($existing, $catid = false) {
		global $event_id;

		if (hasAccess("editor_form")) { //Wenn ьberhaupt Zugriff auf das Editierformular besteht

			$date = new mosEventCal_DateTimeObject( mosGetParam( $_REQUEST, 'date' , time()) );

			if (is_numeric($existing) && hasAccess("edit_event")) { //wenn bearbeitet werden soll
				$event = new mosEventCal_Event();
				$event->load($existing);
				HTML_eventcal::event_form($date, getCategories(), $event);
			}
			else if (is_numeric($existing) && !hasAccess("edit_event")) { //wenn er das aber nicht darf
				mosNotAuth();
			}
			else if ($catid) {
				$cat->catid = $catid;
				HTML_eventcal::event_form($date, getCategories(), $cat);
			}
			else { //$existing == false
				HTML_eventcal::event_form($date, getCategories());
			}
		}
		else {
			mosNotAuth();
		}
	}



	/**
	 * function to display details on a specific event
	 * @author	Kay Messerschmidt
	 * @param	integer			event_id		integer specifying an event to be displayed
	*/

	function print_event_view($event_id = NULL)	{
		global $database, $my;

		$date = new mosEventCal_DateTimeObject( mosGetParam( $_REQUEST, 'date' , 0) );

		$event = new mosEventCal_Event();

		if (!is_numeric( $event_id ) || !$event->load( $event_id) || @!$event->published) {
			mosNotAuth();
		}
		else {
			//check if event spans more days or not
				$events = array();
				$events[] = $event;
				eventCal_Recursion::mkRecurrent( $events ); //now I've got them all

			//add ->type attribute to decode later on which way dates and times are displayed
			if ( $date->timestamp <> 0 )
				$events = eventCal_Recursion::getDateEvents($events, $date); //delete these not taking place the day we want to display
			$event = array_pop($events); //get the only element back (can't pass array!)

			$category = new mosCategory( $database );
			$category->load( $event->catid );
			if ($category->access <= $my->gid) {
				HTML_EventCal::view_event($event);
			}
			else mosNotAuth();
		}
	}



	/**
	 * function to display the dayly view
	 * @author	Kay Messerschmidt
	 * @param	integer			catid		id of a category restricting the events to be displayed
	*/

	function print_day_view ( $catid = NULL ) {
		global $database, $my;

		$date = new mosEventCal_DateTimeObject( mosGetParam( $_REQUEST, 'date' , time()) );
		$query = "SELECT e.id FROM #__eventcal AS e"
			   . "\n LEFT JOIN #__categories AS c ON c.id = e.catid"
			   . "\n WHERE e.published = '1' AND c.published = '1' AND c.access <= " . $my->gid;

		if (is_array( $catid ))
			$query .= " AND catid IN (" . implode( ",", $catid ) . ")";
		else if ($catid)
			$query .= " AND catid IN ($catid)";

		$database->setQuery( $query ); //in results laden
		$results = $database->loadResultArray();

		$events = array();
		foreach ( $results AS $event_id ) { //getting all events as objects for passing to HTML
			$event = new mosEventCal_Event();
			$event->load( $event_id );
			$events[] = $event;
		}

		//just doing this to improve speed for mkRecurrent
		//$events = eventCal_Recursion::getDateEvents( $events, $date );
		//We need to calculate the Recursion before we remove some events
		//Otherwise the recurring events are deleted before the recursion gets calculated
			eventCal_Recursion::mkRecurrent($events);
			sortEventsBy( $events, "start_date" );
		//need to do this a second time AFTER mkRecurrent to minimize data stored temporarily
			$events = eventCal_Recursion::getDateEvents( $events, $date );

		if ($catid) {
			$catids = (is_array( $catid ))?implode( "%2B", $catid ):$catid;
			HTML_eventcal::view_day( $events, $date->date, '&catid=' . $catids );
		}
		else {
			HTML_eventcal::view_day( $events, $date->date );
		}
	}



	/**
	 * function to display the weekly view
	 * @author Kay Messerschmidt
	 * @param 	integer		catid		 specifying the active categories
	*/

	function print_week_view( $catid = NULL ) {
		global $database, $week_startingday, $my, $timetable, $week_days;

		$date = new mosEventCal_DateTimeObject( mosGetParam( $_REQUEST, 'date' , time()) );

		$query = "SELECT e.id FROM #__eventcal AS e"  //ids aller Events ...
			   . " LEFT JOIN #__categories AS c ON c.id = e.catid"
			   . " WHERE e.published = '1' AND c.published = '1' AND c.access <= " . $my->gid;

		if (is_array($catid))
			$query .= " AND catid IN (" . implode( ",", $catid ) . ")";
		else if ($catid)
			$query .= " AND catid IN ($catid)";

		$database->setQuery($query); //in reuslts laden
		$results = $database->loadResultArray();

		$events = array();
		foreach($results AS $event_id) { //getting all events as objects for passing to HTML
			$event = new mosEventCal_Event();
			$event->load($event_id);
			$events[] = $event;
		}

		eventCal_Recursion::mkRecurrent($events);

		//searching for the first day of the current week
			if ($date->date["wday"] != $week_startingday)
				$date->offset( "last $week_days[$week_startingday]" );
		//norm to the first second of the day
			$date->clearTime();

		//delete the elements from $events not taking place in the shown week
			$events = eventCal_Recursion::getDateEvents($events,$date,$date->offset_object( "+ 1 week" ));

		if ( isset($catid) ) {
			$catids = (is_array($catid))?implode("%2B",$catid):$catid;
			HTML_eventcal::view_week($events, $timetable, $date, '&catid=' . $catids);
		}
		else {
			HTML_eventcal::view_week($events, $timetable, $date);
		}
	}



	/**
		* function to display the monthly view
		* @author	Kay Messerschmidt
		* @param	integer		catid		is specifying the active categories
	*/
	function print_month_view ($catid = NULL) {
		global $database, $week_startingday, $my;

		$date = new mosEventCal_DateTimeObject( mosGetParam( $_REQUEST, 'date' , time()) );

		$query = "SELECT e.id FROM #__eventcal AS e"  //ids aller Events ...
			   . " LEFT JOIN #__icat AS c ON c.id = e.catid"
			   . " WHERE e.published = '1' AND c.publish='1' ";

		if (is_array($catid)) {
			$query .= " AND catid IN (" . implode( ",", $catid ) . ")";
		}
		else if ($catid)
			$query .= " AND catid IN ($catid)";


		$database->setQuery( $query ); //in reuslts laden

		$results = $database->loadResultArray();

		$events = array();
		foreach($results AS $event_id) { //getting all events as objects for passing to HTML
			$event = new mosEventCal_Event();
			$event->load($event_id);
			$events[] = $event;
		}

		//timestamps for first and last day of month
			$firstDay_ofMonth = $date->startOfMonth();
			$lastDay_ofMonth = $date->endOfMonth();


		//how many days will be empty in the view before month starts?
			$oldMonth_days = intval( $firstDay_ofMonth->strftime("%w") ) - $week_startingday;
			$oldMonth_days = ($oldMonth_days < 0)?$oldMonth_days + 7:$oldMonth_days;
		//loading weeknumbers
			$workdate = new mosEventCal_DateTimeObject( $firstDay_ofMonth->timestamp );
			$weeknrs = array();
		//get amount of days to be displayed and divide them by seven.
		//Ciel to round and I've got the number of weeks in the view
		for($i=0;$i< ceil(($lastDay_ofMonth->strftime("%d")+$oldMonth_days)/7);$i++) {
			$weeknrs[] = $workdate->strftime("%V");
			$workdate->offset( "+ 1 week");
		}

		//creating calender matrix of dates for days of month
			$calendar = array();
			$actualMonthDays = $lastDay_ofMonth->strftime("%d") + $oldMonth_days;
		//the adition of 43200 seconds is necessary to prevent the calendar from displaying wrong dates
		//because of differences in the local time zone
			$Day_ofMonth = $firstDay_ofMonth->offset_object( "+ 5 hours" );
			for ($w=0;$w<count($weeknrs);$w++) {
				$days_ofWeek = array();
				for ($d=0;$d<7;$d++) {
					if ($w == 0 && $oldMonth_days > 0) { $days_ofWeek[] = NULL;} //Monatsanfang noch nicht erreicht
					elseif (($w*7+$d) >= $actualMonthDays) { $days_ofWeek[] = NULL;} //Monatsende erreicht
					else {
						$days_ofWeek[] = clone( $Day_ofMonth );
						$Day_ofMonth->offset( "+ 1 day" );
					} //mitten im Monat
					$oldMonth_days--;
				}
				$calendar[] = $days_ofWeek;
			}

		//add repeating events
			eventCal_Recursion::mkRecurrent($events);
		//sort events by starting date
			sortEventsBy( $events, "start_date" );

		//delete the elements from $events not taking place in the shown month
			$events = eventCal_Recursion::getDateEvents($events,$firstDay_ofMonth,$lastDay_ofMonth->offset_object( "+ 1 day" ));

		if ( isset($catid) ) {
			$catids = (is_array($catid))?implode("%2B",$catid):$catid;
			HTML_eventcal::view_month($events, $calendar, $weeknrs, $date, '&catid=' . $catids);
		}
		else {
			HTML_eventcal::view_month($events, $calendar, $weeknrs, $date);
		}
}



	/**
	 * function to view a seperated view of one category
	 * @author	Kay Messerschmidt
	 * @param	integer		catid		specifying the category the list will be displayed for
	 * @param	boolean		old			if passed entries (end_date < today) are shown or not
	*/
	function print_category_list( $catid, $old = false ) {
		global $database, $my;

		$category = new mosCategory( $database );
		$category->load( $catid );

		//check if this category may be seen by this user
		//check if this is a valid eventcal category, otherwise exit
			if ($category->section != "com_eventcal" || !$category->published || $category->access > $my->gid) {
				mosNotAuth();
			return false;
			}

		$database->setQuery(	"SELECT e.id FROM #__eventcal AS e" .
								"\n LEFT JOIN #__categories AS c ON c.id = $category->id" .
								"\n WHERE e.catid = $category->id" .
								"\n AND e.published = 1" .
								"\n AND c.published = 1" .
								"\n AND c.access <= $my->gid"
		);
		$event_ids = $database->loadResultArray();

		$event = new mosEventCal_Event( $database );
		foreach ( $event_ids AS $event_id ) {
			$event->load( $event_id );
			$events[] = $event;
		}

		//work on old events
		eventCal_Recursion::mkRecurrent($events);
		sortEventsBy( $events, "start_date" );
		$today = getdate();
		while ( count($events)) {
			if (count($events)) {
				$event = $events[0];
				if ($event->start_date < $today[0]) {
					array_shift($events);
				} else break;
			} else break;
		}

		HTML_EventCal::showCategoryView($category, $events);
	}






	/**
	 * function to submit the Entry in the database
	 * if an id is passed in $_REQUEST, event will be updated, otherwise it will be created
	 * @author	Kay Messerschmidt
	*/
	function doSubmitEntry() {
		global $my;

		$event = new mosEventCal_Event();
		$event->bind( $_REQUEST, '' );

		//create the back button for the redirect dialog:
			$back_button = '<input class="button" type="button" value="' . _CMN_PREV . '" onClick="history.back()">';
		//before I save I need to check some user rights
		//for example if this user may edit events        OR   this user may post events
			if (($event->id && hasAccess( "edit_event" ) && $event->checked_out == $my->id) || (!$event->id && hasAccess( "editor_form" ))) {
				if( $event->check( $error ) ) {
					if ($event->store()) {
						HTML_EventCal::redirect_dialog( _SUCCESSFULL_ACTION_CAP, _SUCCESSFULL_POSTED );
						HTML_EventCal::view_event($event);
					}
					else {
						HTML_EventCal::redirect_dialog( _ACTION_ABORTED_CAP, $event->_error , $back_button );
					}
				}
				else HTML_EventCal::redirect_dialog( _ACTION_ABORTED_CAP, $error , $back_button );
			}
			else
				mosNotAuth();
	}



//***************************************************************************************************************************
//							help-functions to make code more readable
//***************************************************************************************************************************

	/**
	 * function returning an array with event-categories selected by filter string
	 * @author	Kay Messerschmidt
	 * @param	string			filter			supporting an additional filter for the sql-query
	 * @return									a list of categoies or false if none is found
	*/
	function getCategories($filter = '') {
		global $database, $my;

		$query = "SELECT id FROM #__categories WHERE section = 'com_eventcal'"
			   . "\n AND published = '1'"
			   . "\n AND access <= " . $my->gid
			   . $filter
			   . "\n ORDER BY ordering";
		$database->setQuery ($query);
		$results = $database->loadResultArray();

		$category = new mosCategory ($database);

		$categories = array();

		foreach ($results AS $catid) {
			$category->load($catid);
			$category->color = getParam("color", $category->params);
			$categories[] = clone( $category );
		}
		return $categories;
	}

	/**
	 * function that allows to get single values from Params-String
	 * @author	Kay Messerschmidt
	 * @param	string			key				the key-value for this param (key=value)
	 * @param	text			params			text the complete params-text of the object
	 * @return 	mixed							the value to the selected key from params
	*/
	function getParam($key, $params) {
		$parameter = new mosParameters( $params );
		return ($parameter->get($key))?$parameter->get($key):false;
	}


	/**
	 * controls wether the user has access to a component or not
	 * @author	Kay Messerschmidt
	 * @param		text			to				a specified string linked to an action to perform
	 * @return	boolean								weather the user has access or not
	*/
	function hasAccess($to) {
		global $my, $who_can_post_events, $who_can_edit_events;

		if ($my->usertype == 'Administrator') return true;
		if ($to == "editor_form" && $my->gid >= $who_can_post_events) return true;
		if ($to == "edit_event" && $my->gid >= $who_can_edit_events) return true;
		return false;
	}



	/**
	 * sorts the passed array by the announced property
	 * @author	Kay Messerschmidt
	 * @param	array			&events
	 * @param	string			sort_by			sortstring the array is sorted by
	*/
	function sortEventsBy( &$events, $sort_by ) {
		$sortFunct = create_function('$a,$b','return $a->' . $sort_by .' - $b->' . $sort_by . ';');
		usort( $events, $sortFunct );
	}



	/**
	 * parses the catid-string from the $_REQUEST
	 * @author	Kay Messerschmidt
	 * @param	string			&catid			a string represending the catids or one single one
	*/

	function parseCatid( &$catid ) {
		if ($catid === false) return;
		$catid = split( "[(\+\s|=)]", $catid );

		$filterFunct = create_function('$elem','return (isset($elem))?is_numeric($elem):false;');
		$catid = array_filter( $catid, $filterFunct );

		switch (count($catid)) {
			case 0:
				$catid = "";
				break;
			case 1:
				$key = array_keys($catid);  //need to get the first valid key in the array
				$catid = $catid[$key[0]];
				break;
		}
	}

?>

