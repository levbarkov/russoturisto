<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ ограничен' );

// // ensure user has access to this function
// if (!($acl->acl_check( 'administration', 'edit', 'users', $my->usertype, 'components', 'all' )| $acl->acl_check( 'administration', 'edit', 'users', $my->usertype, 'components', 'com_banners' ))) {
// 	mosRedirect( 'index2.php', _NOT_AUTH );
// }

//ggtr(igetPath( 'class' ));
require_once( igetPath( 'admin_html' ) );
$a="../component/banners/banners.class.php";

require_once($a);

$cid =  $_REQUEST['cid'];
$id=$_REQUEST['id'];

switch ($_REQUEST['task']) {
	case 'newclient':
		editBannerClient( 0, $option );
		break;

	case 'editclient':
		editBannerClient( intval( $cid[0] ), $option );
		break;

	case 'editclientA':
		editBannerClient( $id, $option );
		break;

	case 'saveclient':
		saveBannerClient( $option );
		break;

	case 'removeclients':
		removeBannerClients( $cid, $option );
		break;

	case 'cancelclient':
		cancelEditClient( $option );
		break;

	case 'listclients':
		viewBannerClients( $option );
		break;

	// BANNER EVENTS

	case 'new':
		editBanner( null, $option );
		break;

	case 'cancel':
		cancelEditBanner();
		break;

	case 'save':
	case 'resethits':
		saveBanner( $task );
		break;

	case 'edit':
		editBanner( $cid[0], $option );
		break;

	case 'editA':
		editBanner( $id, $option );
		break;

	case 'remove':
		removeBanner( $cid );
		break;

	case 'publish':
		publishBanner( $cid,1 );
		break;

	case 'unpublish':
		publishBanner( $cid, 0 );
		break;

	case 'breset':
		resetBanner( (int)$id, 0 );
		break;

	case 'upload':
		uploadBanner( (int)$id, 0 );
		break;

	default:
		viewBanners( $option );
		break;
}


function  uploadBanner($id, $option){
//	move_uploaded_file($_FILES['userfile']['name'], "/images/stories/");
	
if (is_uploaded_file($_FILES['uploadBanner']['tmp_name'])) {
	if ( (!strcmp($_FILES['uploadBanner']['type'],'image/jpeg')) or (!strcmp($_FILES['uploadBanner']['type'],'image/gif')) or (!strcmp($_FILES['uploadBanner']['type'],'application/x-shockwave-flash'))) 
		{ copy($_FILES['uploadBanner']['tmp_name'], site_path."/images/stories/".$_FILES['uploadBanner']['name']); 
		$msg = "файл загружен тип: ".$_FILES['uploadBanner']['type'];
		}
	else {
	$msg = "файл  не загружен, проверьте тип (допускаются jpg, gif, swf), название файла не должно содержать не латинских символов!".$_FILES['uploadBanner']['type'];
	}
	
} else {
    echo "Possible file upload attack. Filename: " . $_FILES['uploadBanner']['name']; 
}


	mosRedirect( "index2.php?ca=banners&task=editA&id=$id", $msg );
}


function  resetBanner($id, $option){
	global $database;
	$query = "UPDATE #__banner SET impmade=0, clicks=0 WHERE bid=$id";

	$database->setQuery( $query );

	if (!$database->query()) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}
	$msg = 'Число нажатий на баннер обнулено';
	mosRedirect( 'index2.php?ca=banners&task=view', $msg );
}



function viewBanners( $option ) {
	global $database, $mainframe, $mosConfig_list_limit;

	$limit 		= intval( 100 ) ;
	$limitstart = intval( $mainframe->getUserStateFromRequest( "viewban{$option}limitstart", 'limitstart', 0 ) );

	// get the total number of records
	$query = "SELECT COUNT(*)"
	. "\n FROM #__banner"
	;
	$database->setQuery( $query );
	$total = $database->loadResult();
	require_once( $_SERVER['DOCUMENT_ROOT'] . '/iadmin/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit );
	$query = "SELECT b.*, u.name AS editor"
	. "\n FROM #__banner AS b "
	. "\n LEFT JOIN #__users AS u ON u.id = b.checked_out"
	;
	$database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$rows = $database->loadObjectList();

	HTML_banners::showBanners( $rows, $pageNav, $option );
}

function editBanner( $bannerid, $option ) {

	global $database, $my;
	$lists = array();

	$row = new mosBanner($database);

	$row->load( (int)$bannerid );
  if ( $bannerid ){
	$row->checkout( $my->id );
  }

	// Build Client select list
	$sql	= "SELECT cid, name"
	. "\n FROM #__bannerclient"
	;
	$database->setQuery($sql);
	if (!$database->query()) {
		echo $database->stderr();
		return;
	}

	$clientlist[] 	= mosHTML::makeOption( '0', '-Выберите клиента-', 'cid', 'name' );
	$clientlist 	= array_merge( $clientlist, $database->loadObjectList() );
	$lists['cid'] 	= mosHTML::selectList( $clientlist, 'cid', 'class="inputbox" size="1"','cid', 'name', $row->cid);

	// Imagelist
	$javascript 	= 'onchange="changeDisplayImage();"';
	$directory 		= '/images/stories';
	$lists['imageurl'] = mosAdminMenus::Images( 'imageurl', $row->imageurl, $javascript, $directory );
	

	// make the select list for the image positions
	$yesno[] = mosHTML::makeOption( '0', 'Нет' );
  	$yesno[] = mosHTML::makeOption( '1', 'Да' );

  	$lists['showBanner'] = mosHTML::selectList( $yesno, 'showBanner', 'class="inputbox" size="1"' , 'value', 'text', $row->showBanner );
	
	HTML_banners::bannerForm( $row, $lists, $option );
}

function saveBanner( $task ) {
	global $database;

	$row = new mosBanner($database);
$bid=$_REQUEST['bid'];

	$msg = 'Информация о баннере сохранена';
	if (!$row->bind( $_REQUEST )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	// Resets clicks when `Reset Clicks` button is used instead of `Save` button
	if ( $task == 'resethits' ) {
		$row->clicks = 0;
		$msg = 'Число нажатий на баннер обнулено';
	}

	// Sets impressions to unlimited when `unlimited` checkbox ticked
	if ($bid!=="") {$row->bid=$bid;} else{$row->bid=null;}
	$unlimited = intval( mosGetParam( $_REQUEST, 'unlimited', 0 ) );
	if ( $unlimited ) {
		$row->imptotal = 0;
	}
		$row->impmade= 0;
	if (!$row->check()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	$row->checkin();

	mosRedirect( 'index2.php?ca=banners&task=view', $msg );
}

function cancelEditBanner() {
	global $database;

	$row = new mosBanner($database);
	$row->bind( $_REQUEST );
	$row->checkin();

	mosRedirect( 'index2.php?ca=banners&task=view' );
}

function publishBanner( $cid, $publish=1 ) {
	global $database, $my;

	if (!is_array( $cid ) || count( $cid ) < 1) {
		$action = $publish ? 'publish' : 'unpublish';
		echo "<script> alert('Выберите объект для $action'); window.history.go(-1);</script>\n";
		exit();
	}

	mosArrayToInts( $cid );
	$cids = 'bid=' . implode( ' OR bid=', $cid );

	$query = "UPDATE #__banner"
	. "\n SET showBanner = " . (int) $publish
	. "\n WHERE ( $cids )"
	. "\n AND ( checked_out = 0 OR ( checked_out = " . (int) $my->id . " ) )"
	;
	$database->setQuery( $query );
	if (!$database->query()) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}

	if (count( $cid ) == 1) {
		$row = new mosBanner( $database );
		$row->checkin( $cid[0] );
	}
	mosRedirect( 'index2.php?ca=banners&task=view' );

}

function removeBanner( $cid ) {
	global $database;
	
	if (count( $cid )) {
		mosArrayToInts( $cid );
		$cids = 'bid=' . implode( ' OR bid=', $cid );
		$query = "DELETE FROM #__banner"
		. "\n WHERE ( $cids )"
		;
		$database->setQuery( $query );
		if (!$database->query()) {
			echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		}
	}
	mosRedirect( 'index2.php?ca=banners&task=view' );
}

// ---------- BANNER CLIENTS ----------

function viewBannerClients( $option ) {
	global $database, $mainframe, $mosConfig_list_limit;

	$limit 		= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
	$limitstart = intval( $mainframe->getUserStateFromRequest( "viewcli{$option}limitstart", 'limitstart', 0 ) );

	// get the total number of records
	$query = "SELECT COUNT(*)"
	. "\n FROM #__bannerclient"
	;
	$database->setQuery( $query );
	$total = $database->loadResult();

	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit );

	$sql = "SELECT a.*,	count(b.bid) AS bid, u.name AS editor"
	. "\n FROM #__bannerclient AS a"
	. "\n LEFT JOIN #__banner AS b ON a.cid = b.cid"
	. "\n LEFT JOIN #__users AS u ON u.id = a.checked_out"
	. "\n GROUP BY a.cid";
	$database->setQuery($sql, $pageNav->limitstart, $pageNav->limit);
	$rows = $database->loadObjectList();

	HTML_bannerClient::showClients( $rows, $pageNav, $option );
}

function editBannerClient( $clientid, $option ) {
	global $database, $my;
	
	$row = new mosBannerClient($database);
	$row->load( (int)$clientid);

	// fail if checked out not by 'me'
	if ($row->checked_out && $row->checked_out != $my->id) {
		$msg = 'Клиент [ '. $row->name. ' ] в настоящее время редактируется другим пользователем.';
		mosRedirect( 'index2.php?ca='. $option .'&task=listclients', $msg );
	}

	if ($clientid) {
		// do stuff for existing record
		$row->checkout( $my->id );
	} else {
		// do stuff for new record
		$row->published = 0;
		$row->approved = 0;
	}

	HTML_bannerClient::bannerClientForm( $row, $option );
}

function saveBannerClient( $option ) {
	global $database;

	$row = new mosBannerClient( $database );
	if (!$row->bind( $_REQUEST )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	if (!$row->check()) {
		mosRedirect( "index2.php?ca=$option&task=editclient&cid[]=$row->cid", $row->getError() );
	}

	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	$row->checkin();

	mosRedirect( "index2.php?ca=$option&task=listclients" );
}

function cancelEditClient( $option ) {
	global $database;
	$row = new mosBannerClient( $database );
	$row->bind( $_REQUEST );
	$row->checkin();
	mosRedirect( "index2.php?ca=$option&task=listclients" );
}

function removeBannerClients( $cid, $option ) {
	global $database;

	for ($i = 0; $i < count($cid); $i++) {
		$query = "SELECT COUNT( bid )"
		. "\n FROM #__banner"
		. "\n WHERE cid = " . (int) $cid[$i]
		;
		$database->setQuery($query);

		if(($count = $database->loadResult()) == null) {
			echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		}

		if ($count != 0) {
			mosRedirect( "index2.php?ca=$option&task=listclients",
			"Невозможно сейчас удалить клиента, так как его баннеры используются на сайте" );
		} else {
			$query="DELETE FROM #__bannerfinish"
			. "\n WHERE cid = " . (int) $cid[$i]
			;
			$database->setQuery($query);
			$database->query();

			$query = "DELETE FROM #__bannerclient"
			. "\n WHERE cid = " . (int) $cid[$i]
			;
			$database->setQuery($query);
			$database->query();
		}
	}
	mosRedirect("index2.php?ca=$option&task=listclients");
}
?>