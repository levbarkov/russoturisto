<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $mainframe, $task, $id;
$task = $_REQUEST['task'];
//ggtr ($task); die();
//require_once( $mainframe->getPath( 'front_html' ) );
//require_once( $mainframe->getPath( 'class' ) );
require_once( site_path."/component/poll/poll.html.php" );
require_once( site_path."/component/poll/poll.class.php" );

$tabclass 			= 'sectiontableentry2,sectiontableentry1';
$polls_graphwidth 	= 200;
$polls_barheight 	= 2;
$polls_maxcolors 	= 5;
$polls_barcolor 	= 0;

$id 	= intval( mosGetParam( $_REQUEST, 'id', 0 ) );

switch ($task) {
	case 'vote':
		pollAddVote( $id );
		break;

	default:
		pollresult( $id );
		break;
}

function pollAddVote( $uid ) {
	global $database;
//	ggtr (1); die();
	// simple spoof check security
	josSpoofCheck(0,'poll');	
	
	$redirect = 1;

//	$sessionCookieName 	= mosMainFrame::sessionCookieName();
//	$sessioncookie 		= mosGetParam( $_REQUEST, $sessionCookieName, '' );

/*	if (!$sessioncookie) {
		echo '<h3>'. _ALERT_ENABLED .'</h3>';
		echo '<input class="button" type="button" value="'. _CMN_CONTINUE .'" onClick="window.history.go(-1);">';
		return;
	}*/

	$poll = new mosPoll( $database );
	if (!$poll->load( (int)$uid )) {
		echo '<h3>'. _NOT_AUTH .'</h3>';
		echo '<input class="button" type="button" value="'. _CMN_CONTINUE .'" onClick="window.history.go(-1);">';
		return;
	}
	

	$cookiename = "voted$poll->id";
	//$voted = mosGetParam( $_COOKIE, $cookiename, '0' );

/*	if ($voted) {
		echo "<h3>"._ALREADY_VOTE."</h3>";
		echo "<input class=\"button\" type=\"button\" value=\""._CMN_CONTINUE."\" onClick=\"window.history.go(-1);\">";
		return;
	}*/

	$voteid = intval( mosGetParam( $_POST, 'voteid', 0 ) );
	if (!$voteid) {
		echo "<h3>"._NO_SELECTION."</h3>";
		echo '<input class="button" type="button" value="'. _CMN_CONTINUE .'" onClick="window.history.go(-1);">';
		return;
	}

	//setcookie( $cookiename, '1', time()+$poll->lag );

	$query = "UPDATE #__poll_data"
	. "\n SET hits = hits + 1"
	. "\n WHERE pollid = ".(int) $poll->id
	. "\n AND id = ". (int) $voteid
	;
	$database->setQuery( $query );
	$database->query();
//	ggtr($database); die();

	$query = "UPDATE #__polls"
	. "\n SET voters = voters + 1"
	. "\n WHERE id = ".(int) $poll->id
	;
	$database->setQuery( $query );

	$database->query();

	$now = _CURRENT_SERVER_TIME;
	
	$query = "INSERT INTO #__poll_date"
	. "\n SET date = " . $database->Quote( $now ) . ", vote_id = ". (int) $voteid .", poll_id = ".(int) $poll->id
	;
	$database->setQuery( $query );
	$database->query();

	if ( $redirect ) {
		mosRedirect( sefRelToAbs( 'index.php?c=poll&task=results&id='. $uid ), _THANKS );
	} else {
		echo '<h3>'. _THANKS .'</h3>';
		echo '<form action="" method="GET">';
		echo '<input class="button" type="button" value="'. _BUTTON_RESULTS .'" onClick="window.location=\''. sefRelToAbs( 'index.php?c=poll&task=results&id='. $uid ) .'\'">';
		echo '</form>';
	}
}

function pollresult( $uid ) {
	global $database, $Itemid;
	global $mainframe;

	$poll = new mosPoll( $database );
	$poll->load( (int)$uid );
//	ggtr ($poll); die();

	// if id value is passed and poll not published then exit
	if ($poll->id != '' && !$poll->published) {
		mosNotAuth();
		return;
	}

	$first_vote = '';
	$last_vote 	= '';
	$votes		= '';
	
	/*
	Check if there is a poll corresponding to id
	and if poll is published
	*/
	if (isset($poll->id) && $poll->id != '' && $poll->published == 1) {
		if (empty($poll->title)) {
			$poll->id = '';
			$poll->title = _SELECT_POLL;
		}

		$query = "SELECT MIN( date ) AS mindate, MAX( date ) AS maxdate"
		. "\n FROM #__poll_date"
		. "\n WHERE poll_id = " . (int) $poll->id
		;
		$database->setQuery( $query );
		$dates = $database->loadObjectList();

		if (isset($dates[0]->mindate)) {
			$first_vote = mosFormatDate( $dates[0]->mindate, _DATE_FORMAT_LC2 );
			$last_vote = mosFormatDate( $dates[0]->maxdate, _DATE_FORMAT_LC2 );
		}
		
		$query = "SELECT a.id, a.text, a.hits, b.voters"
		. "\n FROM #__poll_data AS a"
		. "\n INNER JOIN #__polls AS b ON b.id = a.pollid"
		. "\n WHERE a.pollid = " . (int) $poll->id
		. "\n AND a.text != ''"
		. "\n AND b.published = 1"
		;

		$database->setQuery( $query );
		$votes = $database->loadObjectList();		
	}

	// list of polls for dropdown selection
	$query = "SELECT id, title"
	. "\n FROM #__polls"
	. "\n WHERE published = 1"
	. "\n ORDER BY id"
	;
	$database->setQuery( $query );
	$polls = $database->loadObjectList();
//	ggtr ($polls); die();

	// Itemid for dropdown
	$_Itemid = '';
	global $pi;
	 $Itemid = $pi;
	if ( $Itemid && $Itemid != 99999999 ) {
		$_Itemid = '&amp;Itemid='. $Itemid;
	}  

	// dropdown output
	$link = sefRelToAbs( 'index.php?c=poll&amp;task=results&amp;id=\' + this.options[selectedIndex].value + \''. $_Itemid .'\' + \'' );
	$pollist = '<select name="id" class="inputbox" size="1" style="width:200px" onchange="if (this.options[selectedIndex].value != \'\') {document.location.href=\''. $link .'\'}">';
	$pollist .= '<option value="">'. _SELECT_POLL .'</option>';
	for ($i=0, $n=count( $polls ); $i < $n; $i++ ) {
		$k = $polls[$i]->id;
		$t = $polls[$i]->title;

		$sel = ($k == intval( $poll->id ) ? " selected=\"selected\"" : '');
		$pollist .= "\n\t<option value=\"".$k."\"$sel>" . $t . "</option>";
	}
	$pollist .= '</select>';

	// Adds parameter handling
	$menu = $mainframe->get( 'menu' );

	$params = new mosParameters( $menu->params );
	$params->def( 'page_title', 1 );
	$params->def( 'pageclass_sfx', '' );
	$params->def( 'back_button', $mainframe->getCfg( 'back_button' ) );
	$params->def( 'header', $menu->name );

//	$mainframe->SetPageTitle($poll->title);

	poll_html::showResults( $poll, $votes, $first_vote, $last_vote, $pollist, $params );
}
?>