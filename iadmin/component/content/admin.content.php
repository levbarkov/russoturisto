<?php
// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $task, $id;
require_once( igetPath( 'admin_html' ) );
$cid 		= josGetArrayInts( 'cid' );

switch ($task) {
	case 'new':			editContent( 0, $option );							break;
	case 'edit':			editContent( $id, $option );						break;
	case 'editA':			editContent( intval( $cid[0] ), $option );			break;
	case 'go2menu':
	case 'go2menuitem':
	case 'menulink':
	case 'apply':
	case 'save':			saveContent( $task );								break;
	case 'remove':			removeContent( $cid, $option );						break;
	case 'publish':			changeContent( $cid, 1, $option );					break;
	case 'unpublish':		changeContent( $cid, 0, $option );					break;
	case 'toggle_frontpage':        toggleFrontPage( $cid, $option );					break;
	case 'archive':			changeContent( $cid, -1, $option );					break;
	case 'unarchive':		changeContent( $cid, 0, $option );					break;
	case 'cancel':			cancelContent();									break;
	case 'orderup':			orderContent( intval( $cid[0] ), -1, $option );		break;
	case 'orderdown':		orderContent( intval( $cid[0] ), 1, $option );		break;
	case 'showarchive':		viewArchive( $option );								break;
	case 'copy':			copyItem( $cid, $option );							break;
	case 'copysave':		copyItemSave( $cid, $option );						break;
	case 'movesect':		moveSection( $cid, $option );						break;
	case 'movesectsave':	moveSectionSave( $cid, $option );					break;
	case 'accesspublic':	accessMenu( intval( $cid[0] ), 0, $option );		break;
	case 'accessregistered': accessMenu( intval( $cid[0] ), 1, $option );		break;
	case 'accessspecial':	accessMenu( intval( $cid[0] ), 2, $option );		break;
	case 'saveorder':		saveOrder( $cid );									break;
	default:				viewContent( $option );								break;
}

/**
* Compiles a list of installed or defined modules
* @param database A database connector object
*/
function viewContent( $option ) {
	global $database, $iConfig_list_limit;

	$filter_authorid 	= intval( getUserStateFromRequest( 'filter_authorid', 0 ) );
	$limit 				= intval( getUserStateFromRequest( 'limit', $iConfig_list_limit ) );
	$limitstart 		= intval( getUserStateFromRequest( 'limitstart', 0 ) );
	
	$search				= icsmart('icsmart_content_search');
	if (get_magic_quotes_gpc()) {
		$search			= stripslashes( $search );
	}
	$filter 			= ''; //getting a undefined variable error

	$where = array(
	"state 	>= 0",
	);
	$order 		= "\n ORDER BY ordering, title";

	// used by filter
	if (  icsmart('icsmart_content_catid')  ) {
		$where[] = "catid = " . (int) icsmart('icsmart_content_catid');
	}
	if (  (int) icsmart('icsmart_content_catid')==0  )  $where[] = "catid >0 ";
	
	if (  $search  ) $where[] = "LOWER( #__content.title ) LIKE '%" . $database->getEscaped(  $search  ) . "%'";

	// get the total number of records
	$query = "SELECT COUNT(*)"
	. "\n FROM #__content "
	. ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )
	;
	$database->setQuery( $query );
	$total = $database->loadResult();
	require_once( site_path . '/iadmin/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit );

	$query = "SELECT * "
	. "\n FROM #__content "
	. ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : '' )
	. $order
	;
	$database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$rows = $database->loadObjectList();
//	ggtr ($database);
	if ($database->getErrorNum()) { echo $database->stderr(); return false; }

	// get list of categories for dropdown filter
	$vcats = array();
	$vcats[] = mosHTML::makeOption( 0, "- Выберите рубрику -");
	do_icatlist (0, $vcats, 0);
	$lists['catid'] 	= mosHTML::selectList( $vcats, 'icsmart_content_catid', 'class="inputtop" size="1" onchange="document.adminForm.submit();" ', 'value', 'text', (int) icsmart('icsmart_content_catid') );

	$javascript = 'onchange="document.adminForm.submit();"';
	HTML_content::showContent( $rows, $lists, $search, $pageNav, $all, $redirect );
}

/**
* Shows a list of archived content items
*/
function viewArchive( $option ) {
	global $database, $mainframe, $mosConfig_list_limit;

	$catid 				= (int) icsmart('icsmart_content_catid');
	$limit 				= intval( getUserStateFromRequest( 'limit', $mosConfig_list_limit ) );
	$limitstart 		= intval( getUserStateFromRequest( 'limitstart', 0 ) );
	$filter_authorid 	= intval( getUserStateFromRequest( 'filter_authorid', 0 ) );
	$search 			= icsmart('icsmart_content_search');
	if (get_magic_quotes_gpc()) {
		$search			= stripslashes( $search );
	}

		$where = array(
		"c.state 	= -1",
		"c.catid	= cc.id"
		);
		$filter = "\n ";
		$all = 1;

	// used by filter
	if ($catid > 0) {
		$where[] = "c.catid = " . $catid;
	}
	if ($search) {
		$where[] = "LOWER( c.title ) LIKE '%" . $database->getEscaped( trim( mb_strtolower( $search,"UTF-8" ) ) ) . "%'";
	}

	// get the total number of records
	$query = "SELECT COUNT(*)"
	. "\n FROM #__content AS c"
	. "\n LEFT JOIN #__icat AS cc ON cc.id = c.catid"
	. ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : '' )
	;
	$database->setQuery( $query );
	$total = $database->loadResult();
	require_once( site_path . '/iadmin/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );

	$query = "SELECT c.*, cc.name, v.name AS author"
	. "\n FROM #__content AS c"
	. "\n LEFT JOIN #__icat AS cc ON cc.id = c.catid"
	. "\n LEFT JOIN #__users AS v ON v.id = c.created_by"
	. ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : '' )
	. "\n ORDER BY c.catid, c.ordering"
	;
	$database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$rows = $database->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return;
	}

	// get list of categories for dropdown filter
	$vcats = array();
	$vcats[] = mosHTML::makeOption( 0, "- Выберите рубрику -");
	do_icatlist (0, $vcats, 0);
	$lists['catid'] 	= mosHTML::selectList( $vcats, 'icsmart_content_catid', 'onchange="document.adminForm.submit( );" size="1" class="inputbox"', 'value', 'text', $catid );

	

	HTML_content::showArchive( $rows, $lists, $search, $pageNav, $option, $all, $redirect );
}

/**
* Compiles information to add or edit the record
* @param database A database connector object
* @param integer The unique id of the record to edit (0 if new)
*/
function editContent( $uid=0, $option ) {
	global $database, $my, $mainframe;
	global  $iConfig_offset, $reg;

	$redirect = strval( mosGetParam( $_POST, 'redirect', '' ) );
	$nullDate = $database->getNullDate();

	// load the row from the db table
	$row = new mosContent( $database );
	$row->load( (int)$uid );

	if ($uid) {
		if ($row->state < 0) {
			mosRedirect( 'index2.php?ca=content', 'Вы не можете отредактировать архивный объект' );
		}
	}

	/* fail if checked out not by 'me'
	if ($row->checked_out && $row->checked_out != $my->id) {
		mosRedirect( 'index2.php?ca=content', 'Модуль '. $row->title .' в настоящее время редактируется другим администратором' );
	} */

	$selected_folders = NULL;
	if ($uid) {
		$row->checkout( $my->id );
		
		if (trim( $row->images )) {
			$row->images = $row->images;
		} else {
			$row->images = "";
		}

 		$row->created 		= mosFormatDate( $row->created, _CURRENT_SERVER_TIME_FORMAT );
		$row->modified 		= $row->modified == $nullDate ? '' : mosFormatDate( $row->modified, _CURRENT_SERVER_TIME_FORMAT );
		$row->publish_up 	= mosFormatDate( $row->publish_up, _CURRENT_SERVER_TIME_FORMAT );

 		if (trim( $row->publish_down ) == $nullDate || trim( $row->publish_down ) == '' || trim( $row->publish_down ) == '-' ) {
			$row->publish_down = 'Никогда';
		}
		$row->publish_down 	= mosFormatDate( $row->publish_down, _CURRENT_SERVER_TIME_FORMAT );

		$query = "SELECT name"
		. "\n FROM #__users"
		. "\n WHERE id = " . (int) $row->created_by
		;
		$database->setQuery( $query );
		$row->creator = $database->loadResult();

		// test to reduce unneeded query
		if ( $row->created_by == $row->modified_by ) {
			$row->modifier = $row->creator;
		} else {
		$query = "SELECT name"
		. "\n FROM #__users"
			. "\n WHERE id = " . (int) $row->modified_by
		;
		$database->setQuery( $query );
		$row->modifier = $database->loadResult();
		}

		$query = "SELECT content_id"
		. "\n FROM #__content_frontpage"
		. "\n WHERE content_id = " . (int) $row->id
		;
		$database->setQuery( $query );
		$row->frontpage = $database->loadResult();

		// get list of links to this item
		$and = "\n AND componentid = " . (int) $row->id;
		$menus = mosAdminMenus::Links2Menu( 'content_item_link', $and );
	} else {
		if ( !$sectionid && @$_POST['filter_sectionid'] ) {
			$sectionid = $_POST['filter_sectionid'];
		}
		if ( @$_POST['catid'] ) {
			$row->catid 	= (int)$_POST['catid'];
			$category = new mosCategory( $database );
			$category->load( (int)$_POST['catid'] );
			$sectionid = $category->section;
		} else {
			$row->catid 	= 0;
		}

		$row->sectionid 	= $sectionid;
		$row->version 		= 0;
		$row->state 		= 1;
		$row->ordering 		= 0;
		$row->images 		= "";
		$row->publish_up 	= date( 'Y-m-d H:i:s', time() + ( $iConfig_offset * 60 * 60 ) );
		$row->publish_down 	= 'Никогда';
		$row->creator 		= '';
		$row->modified 		= $nullDate;
		$row->modifier 		= '';
		$row->frontpage 	= 0;
		$menus = array();
	}
	$vcats = array();
	$vcats[] = mosHTML::makeOption( 0, "- Выберите рубрику -");
	do_icatlist (0, $vcats, 0);
	if (  $row->id==0  ) $row->catid = icsmarti(icsmart_content_catid);
	$lists['catid'] 	= mosHTML::selectList( $vcats, 'catid', 'class="inputbox" size="1"', 'value', 'text', intval( $row->catid ) );

	// build the html select list for ordering
	$query = "SELECT ordering AS value, title AS text"
	. "\n FROM #__content"
	. "\n WHERE catid = " . (int) $row->catid
	. "\n AND state >= 0"
	. "\n ORDER BY ordering"
	;
	$lists['ordering'] = mosAdminMenus::SpecificOrdering( $row, $uid, $query, 1 );

	// get params definitions
	$params = new mosParameters( $row->attribs, igetPath( 'com_xml', 'content' ), 'component' );
	//ggr ($params);
	HTML_content::editContent( $row, $lists, $images, $params, $option, $redirect, $menus );
}

/*
* @param database A database connector object
*/
function saveContent( $task ) {
	global $database, $my, $iConfig_offset, $reg;
	if (  ggri('id')>0  )  $exgood = ggo (ggri('id'), "#__content"); //ggd ($exgood);
	
	$menu 		= strval( mosGetParam( $_POST, 'menu', 'mainmenu' ) );
	$menuid		= intval( mosGetParam( $_POST, 'menuid', 0 ) );
	$nullDate 	= $database->getNullDate();


	$row = new mosContent( $database );
	if (!$row->bind( $_POST )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	// sanitise id field
	$row->id = (int) $row->id;

	if ($row->id) {
		$row->modified 		= date( 'Y-m-d H:i:s' );
		$row->modified_by 	= $my->id;
	}

		$row->created_by 	= $row->created_by ? $row->created_by : $my->id;
	
	if ($row->created && strlen(trim( $row->created )) <= 10) {
		$row->created 	.= ' 00:00:00';
	}
	$row->created 		= $row->created ? mosFormatDate( $row->created, '%Y-%m-%d %H:%M:%S', $iConfig_offset ) : date( 'Y-m-d H:i:s' );

	if (strlen(trim( $row->publish_up )) <= 10) {
		$row->publish_up .= ' 00:00:00';
	}
	$row->publish_up = mosFormatDate( $row->publish_up, _CURRENT_SERVER_TIME_FORMAT, $iConfig_offset );

	if (trim( $row->publish_down ) == 'Никогда' || trim( $row->publish_down ) == '') {
		$row->publish_down = $nullDate;
	} else {
		if (strlen(trim( $row->publish_down )) <= 10) {
			$row->publish_down .= ' 00:00:00';
	}
		$row->publish_down = mosFormatDate( $row->publish_down, _CURRENT_SERVER_TIME_FORMAT, $iConfig_offset );
	}

	$row->state = intval( mosGetParam( $_REQUEST, 'published', 0 ) );

	$params = mosGetParam( $_POST, 'params', '' );
	if (is_array( $params )) {
		$txt = array();
		foreach ( $params as $k=>$v) {
			if (get_magic_quotes_gpc()) {
				$v = stripslashes( $v );
			}
			$txt[] = "$k=$v";
		}
		$row->attribs = implode( "\n", $txt );
	}

	// code cleaner for xhtml transitional compliance
	$row->introtext = str_replace( '<br>', '<br />', $row->introtext );
	$row->fulltext 	= str_replace( '<br>', '<br />', $row->fulltext );

 	// remove <br /> take being automatically added to empty fulltext
 	$length	= strlen( $row->fulltext ) < 9;
 	$search = strstr( $row->fulltext, '<br />');
 	if ( $length && $search ) {
 		$row->fulltext = NULL;
 	}

	$row->title = ampReplace( $row->title );
 	if (!$row->check()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	$row->version++;
	if (  ggrr('sefname')!=''  ) $row->sefname = sefname( ggrr('sefname') );
	else $row->sefname = sefname( $row->title );

	
	if (  ggri('id')==0  or  $exgood->catid!=$_REQUEST['catid']  ){	// поменяли родителя - необходимо обновить информацию sefnamefullcat	
		if (  $_REQUEST['catid']==0  ) $exgood->sefnamefullcat = '';
		else { $papa = ggo (  $_REQUEST['catid'], "#__icat"  );   $row->sefnamefullcat = $papa->sefnamefull.'/'.$papa->sefname; }
	}
	
	
	if (!$row->store()) { echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n"; exit(); }
	
	if (  !isset($exgood->catid)  )  $exgood->catid=-1;	// возможно переменная $exgood - не определена - делаем ее не нулевой
	if (  ggri('id')==0  or  $exgood->catid!=$_REQUEST['catid']  ){ 	// поменяли родителя - необходимо обновить информацию о количестве детей у родителей
		load_lib('icontent'); $icat = new icat;
		if (  $exgood->catid>0  &&  $exgood->catid!=''  ) { $icat->id = $exgood->catid; $icat->update_goods (  ); }
		if (  ggri('catid')>0   &&  ggri('catid')!=''   ) { $icat->id = ggri('catid');  $icat->update_goods (  ); }
	}


	// manage frontpage items
	require_once( igetPath( 'class', 'frontpage' ) );
	$fp = new mosFrontPage( $database );

	if (intval( mosGetParam( $_REQUEST, 'frontpage', 0 ) )) {

		// toggles go to first place
		if (!$fp->load( (int)$row->id )) {
			// new entry
			$query = "INSERT INTO #__content_frontpage"
			. "\n VALUES ( " . (int) $row->id . ", 1 )"
			;
			$database->setQuery( $query );
			if (!$database->query()) {
				echo "<script> alert('".$database->stderr()."');</script>\n";
				exit();
			}
			$fp->ordering = 1;
		}
	} else {
		// no frontpage mask
		if (!$fp->delete( (int)$row->id )) {
			$msg .= $fp->stderr();
		}
		$fp->ordering = 0;
	}
	$fp->updateOrder();
	$row->checkin();
	$row->updateOrder( "catid = " . (int) $row->catid . " AND state >= 0" );

	/* Сохраняем тэг */
	if($reg["contentAllowTags"] == 1){
                $tag = new tags("content", $database, $reg);
                $tag->id=$row->id;
                if($row->state <= 0)  $tag->delete($row->id);
                else                  $tag->apply_tag( $_REQUEST['_tag_field'] );
	}
	
	// сохраняем NAMES
        $names = new names($row->id, $reg['ca'], $reg);
        $names->apply_names($_REQUEST['_names_field']);

	$adminlog = new adminlog();	
	if (  ggri('id')==0  )	$adminlog->logme('new', $reg['content_name'], $row->title, $row->id ); else $adminlog->logme('save', $reg['content_name'], $row->title, $row->id );

	/*
	 * СОХРАНЯЕМ ИНДИВИДУАЛЬНЫЙ КОНФИГ
	 */	
	load_adminclass('config');	 
	$conf = new config($reg['db']);
	$conf->prefix_id = '#__content'."_ID".$row->id."__";
	$conf->save_config();

	// УДАЛЯЕМ ОСНОВНОЕ ФОТО, Если пользователь поставил галочку - Удалить изображение
	$component_foto = new component_foto( 0 );
	$component_foto->init( 'content_main' );
	$component_foto->parent = $row->id;
	$component_foto->delmainfoto_ifUserSetChackBox();

	if (  $_FILES["newfoto"]['tmp_name']  ){	// ВЫБРАНО НОВОЕ ФОТО - РЕДИРЕКТ НА ФОТОГАЛЕРЕЮ
		switch ( $task ) {
			case 'apply':	$ret_url = 'index2.php?ca=content&task=edit&hidemainmenu=1&id='.$row->id;  
							$ret_msg = 'Изменения успешно сохранены в: '. $row->title; break;
			case 'save':
			default:		$ret_url = 'index2.php?ca=content';	  
							$ret_msg = 'Успешно сохранено: '. $row->title; break;
		}
                $component_foto->publish = 'dont_save_publish';  // так как у объекта content - publish не актуален
                $component_foto->delmainfoto();
		$component_foto->external_foto($ret_url, $ret_msg); return;
	}	

	switch ( $task ) {
		case 'go2menu':
			mosRedirect( 'index2.php?ca=menus&menutype='. $menu );
			break;

		case 'go2menuitem':
			mosRedirect( 'index2.php?ca=menus&menutype='. $menu .'&task=edit&hidemainmenu=1&id='. $menuid );
			break;

		case 'menulink':
			menuLink( $redirect, $row->id );
			break;

		case 'apply':
			$msg = 'Изменения успешно сохранены в: '. $row->title;
			mosRedirect( 'index2.php?ca=content&task=edit&hidemainmenu=1&id='. $row->id, $msg );
			break;

		case 'save':
		default:
			$msg = 'Успешно сохранено: '. $row->title;
			global $option;
			$limitstart_pref = ""; if (  icsmarti("icsmart_".$option."_limitstart")>0  ) $limitstart_pref = "&limitstart=".icsmarti("icsmart_".$option."_limitstart");
			$limit_pref = ""; if (  icsmarti("icsmart_".$option."_limit")>0  ) $limit_pref = "&limit=".icsmarti("icsmart_".$option."_limit");
			mosRedirect( 'index2.php?ca=content'.$limitstart_pref.$limit_pref, $msg );

			break;
	}
}

/**
* Changes the state of one or more content pages
* @param integer A unique category id (passed from an edit form)
* @param array An array of unique category id numbers
* @param integer 0 if unpublishing, 1 if publishing
* @param string The name of the current user
*/
function changeContent( $cid=null, $state=0, $option ) {
	global $database, $my, $task;

	if (count( $cid ) < 1) {
		$action = $state == 1 ? 'publish' : ($state == -1 ? 'archive' : 'unpublish');
		echo "<script> alert('Select an item to $action'); window.history.go(-1);</script>\n";
		exit;
	}

	mosArrayToInts( $cid );
	$total = count ( $cid );
	$cids = 'id=' . implode( ' OR id=', $cid );

	$query = "UPDATE #__content"
	. "\n SET state = " . (int) $state . ", modified = " . $database->Quote( date( 'Y-m-d H:i:s' ) )
	. "\n WHERE ( $cids ) AND ( checked_out = 0 OR (checked_out = " . (int) $my->id . ") )"
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
	
	switch ( $state ) {
		case -1:				
		$msg = $total .' Объект(ы) успешно архивирован(ы)';
			break;
		
		case 1:				
			$msg = $total .' Объект(ы) успешно опубликован(ы)';
			break;   

		case 0:				
		default:
			if ( $task == 'unarchive' ) {
				$msg = $total .' Объект(ы) успешно извлечен(ы) из архива';
			} else {
				$msg = $total .' Объект(ы) успешно снят(ы) с публикации';
			}
			break;
	}

	$rtask 		= strval( mosGetParam( $_POST, 'returntask', '' ) );
	if ( $rtask ) {
		$rtask = '&task='. $rtask;
	} else {
		$rtask = '';
	}

	mosRedirect( 'index2.php?ca='. $option . $rtask .'&mosmsg='. $msg );
}

/**
* Changes the state of one or more content pages
* @param integer A unique category id (passed from an edit form)
* @param array An array of unique category id numbers
* @param integer 0 if unpublishing, 1 if publishing
* @param string The name of the current user
*/
function toggleFrontPage( $cid, $option ) {
	global $database, $mainframe;

	if (count( $cid ) < 1) {
		echo "<script> alert('Выберите объект для переключения'); window.history.go(-1);</script>\n";
		exit;
	}

	$msg = '';
	require_once( igetPath( 'class', 'frontpage' ) );

	$fp = new mosFrontPage( $database );
	foreach ($cid as $id) {
		// toggles go to first place
		$fp->load( $id );
		if (  $fp->content_id  ) {
			if (!$fp->delete( $id )) {
				$msg .= $fp->stderr();
			}
			$fp->ordering = 0;
		} else {
			// new entry
			$query = "INSERT INTO #__content_frontpage"
			. "\n VALUES ( " . (int) $id . ", 0 )"
			;
			$database->setQuery( $query );
			if (!$database->query()) {
				echo "<script> alert('".$database->stderr()."');</script>\n";
				exit();
			}
			$fp->ordering = 0;
		}
		$fp->updateOrder();
	}

	// clean any existing cache files
	mosCache::cleanCache( 'content' );
	mosRedirect( 'index2.php?ca='. $option, $msg );
}

function removeContent( &$cid, $option ) {
	global $database, $reg;

	$total = count( $cid );
	if ( $total < 1) {  echo "<script> alert('Выберите объект для удаления'); window.history.go(-1);</script>\n";   exit;   }
        
	$state = '-2';
	$ordering = '0';
        
        $content = new content();
	foreach ($_REQUEST['cid'] as $dfgd){
		$content->id = $dfgd;
                $content->delme( 1, 'content' );
	}
	// clean any existing cache files
	mosCache::cleanCache( 'content' );

	$msg = "Удалено: ".$total." объект (ов)";
	$return = strval( mosGetParam( $_POST, 'returntask', '' ) );
	mosRedirect( 'index2.php?ca='. $option, $msg );
}

/**
* Cancels an edit operation
*/
function cancelContent( ) {
	global $database;

	// $row = new mosContent( $database );
	// $row->bind( $_POST );
	// $row->checkin();

	mosRedirect( 'index2.php?ca=content' );
}

/**
* Moves the order of a record
* @param integer The increment to reorder by
*/
function orderContent( $uid, $inc, $option ) {
	global $database;

	$row = new mosContent( $database );
	$row->load( (int)$uid );
	$row->move( $inc, "catid = " . (int) $row->catid . " AND state >= 0" );

	// clean any existing cache files
	mosCache::cleanCache( 'content' );

	mosRedirect( 'index2.php?ca='. $option );
}

/**
* Form for copying item(s)
**/
function copyItem( $cid, $option ) {
	global $database;

	if (!is_array( $cid ) || count( $cid ) < 1) {
		echo "<script> alert('Выберите объект для перемещения'); window.history.go(-1);</script>\n";
		exit;
	}

	//seperate contentids
	mosArrayToInts( $cids );
	$cids = 'a.id=' . implode( ' OR a.id=', $cid );
	## Content Items query
	$query = "SELECT a.title"
	. "\n FROM #__content AS a"
	. "\n WHERE ( $cids )"
	. "\n ORDER BY a.title"
	;
	$database->setQuery( $query );
	$items = $database->loadObjectList();

	$vcats = array();
	do_icatlist (0, $vcats, 0);
	$sectCatList = mosHTML::selectList( $vcats, 'sectcat', 'class="inputbox" size="10"', 'value', 'text', NULL );

	HTML_content::copySection( $option, $cid, $sectCatList, $items );
}
/**
* Form for moving item(s) to a different section and category
*/
function moveSection( $cid, $option ) {
	global $database;

	if (!is_array( $cid ) || count( $cid ) < 1) {
		echo "<script> alert('Выберите объект для перемещения'); window.history.go(-1);</script>\n";
		exit;
	}

	//seperate contentids
	mosArrayToInts( $cids );
	$cids = 'a.id=' . implode( ' OR a.id=', $cid );
	// Content Items query
	$query = 	"SELECT a.title"
	. "\n FROM #__content AS a"
	. "\n WHERE ( $cids )"
	. "\n ORDER BY a.title"
	;
	$database->setQuery( $query );
	$items = $database->loadObjectList();

	$vcats = array();
	do_icatlist (0, $vcats, 0);
	$sectCatList = mosHTML::selectList( $vcats, 'sectcat', 'class="inputbox" size="8"', 'value', 'text', NULL );

	HTML_content::moveSection( $cid, $sectCatList, $option, $items );
}

/**
* saves Copies of items
**/
function copyItemSave( $cid, $option ) {
	global $database, $reg;

	$sectcat = (int) mosGetParam( $_POST, 'sectcat', '' );
	$newcat = $sectcat;

	// find category name
	$category = ggo (  (int) $newcat, "#__icat"  );	//ggd ($category);
	$total = count( $cid );
	for ( $i = 0; $i < $total; $i++ ) {
		$row = new mosContent( $database );
		// main query
		$query = "SELECT a.* FROM #__content AS a WHERE a.id = " . (int) $cid[$i];	$database->setQuery( $query );
		$item = $database->loadObjectList();
		if (  ggrr('copyprefix')==''  )	$copyprefix_sefname = "_copy";
		else 							$copyprefix_sefname = sefname(  ggrr('copyprefix')  );

		// values loaded into array set for store
		$row->id 				= NULL;
		$row->catid 			= (int) $newcat;
		$row->ordering			= '0';
		$row->title 			= $item[0]->title.ggrr('copyprefix');
		$row->sefname 			= $item[0]->sefname.$copyprefix_sefname;
		$row->title_alias 		= $item[0]->title_alias;
		$row->introtext 		= $item[0]->introtext;
		$row->fulltext 			= $item[0]->fulltext;
		$row->state 			= $item[0]->state;
		$row->mask 				= $item[0]->mask;
		$row->created 			= $item[0]->created;
		$row->created_by 		= $item[0]->created_by;
		$row->created_by_alias 	= $item[0]->created_by_alias;
		$row->modified 			= $item[0]->modified;
		$row->modified_by 		= $item[0]->modified_by;
		$row->checked_out 		= $item[0]->checked_out;
		$row->checked_out_time 	= $item[0]->checked_out_time;
		$row->publish_up 		= $item[0]->publish_up;
		$row->publish_down 		= $item[0]->publish_down;
		$row->images 			= $item[0]->images;
		$row->attribs 			= $item[0]->attribs;
		$row->version 			= $item[0]->parentid;
		$row->parentid 			= $item[0]->parentid;
		$row->metakey 			= $item[0]->metakey;
		$row->metadesc 			= $item[0]->metadesc;
		$row->access 			= $item[0]->access;
		$row->sefnamefullcat 	= $category->sefnamefull.'/'.$category->sefname;
		if (!$row->check()) {			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";			exit();		}
		if (!$row->store()) {			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";			exit();		}
		// пересчет количества новостей / статей в рубриках
		load_lib('icontent'); $icat = new icat;
		$icat->id = (int)$newcat;  		$icat->update_goods (  );
				
		$row->updateOrder( "catid='". (int) $row->catid ."' AND state >= 0" );
		// находим новый ID
		$iexgoodnewID = $row->id;

		// теперь необходимо сделать копию основного фото
		$component_foto = new component_foto ( 0 );
		$component_foto->init($reg['ca'].'_main');
		$component_foto->parent = $cid[$i];
		$component_foto->load_parent();
		$component_foto->copy_main_foto( $iexgoodnewID );

		// теперь необходимо организовать копию фото 
		$component_foto2 = new component_foto ( 0 ); 
		$component_foto2->init($reg['ca']);
		$component_foto2->parent = $cid[$i];
		$component_foto2->copy_fotos( $iexgoodnewID );
	}		 
	//return;
	$msg = $total. ' объект(ы) успешно скопированы в рубрику : '. $category->name;
	mosRedirect( 'index2.php?ca='. $option .'&mosmsg='. $msg );
}

/**
* Save the changes to move item(s) to a different section and category
*/
function moveSectionSave( &$cid, $option ) {
	global $database, $my;

	$sectcat = (int) mosGetParam( $_POST, 'sectcat', '' );
	$newcat = $sectcat;

	// find category name
	$query = "SELECT a.name"
	. "\n FROM #__icat AS a"
	. "\n WHERE a.id = " . (int) $newcat
	;
	$database->setQuery( $query );
	$category = $database->loadResult();

	$total = count( $cid );

	$row = new mosContent( $database );
	// update old orders - put existing items in last place
	foreach ($cid as $id) {
		$row->load( intval( $id ) );
		$row->ordering = 0;
		$row->store();
		$row->updateOrder( "catid = " . (int) $row->catid . " AND state >= 0" );
	}

	mosArrayToInts( $cids );
	$cids = 'id=' . implode( ' OR id=', $cid );
	$query = "UPDATE #__content SET catid = " . (int) $newcat
	. "\n WHERE ( $cids )"
	. "\n AND ( checked_out = 0 OR ( checked_out = " . (int) $my->id . " ) )"
	;
	$database->setQuery( $query );
	if ( !$database->query() ) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}
//	ggd ($database);

	// update new orders - put items in last place
	foreach ($cid as $id) {
		$row->load( intval( $id ) );
		$row->ordering = 0;
		$row->store();
		$row->updateOrder( "catid = " . (int) $row->catid . " AND state >= 0" );
	}
	   
	// clean any existing cache files
	mosCache::cleanCache( 'content' );

	$msg = $total. ' объект(ы) успешно перемещен(ы) в рубрику: '. $category;
	mosRedirect( 'index2.php?ca='. $option .'&sectionid='. $sectionid .'&mosmsg='. $msg );
}

/**
* @param integer The id of the content item
* @param integer The new access level
* @param string The URL option
*/
function accessMenu( $uid, $access, $option ) {
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

	// clean any existing cache files
	mosCache::cleanCache( 'content' );
	
	mosRedirect( 'index2.php?ca='. $option );
}

function filterCategory( $query, $active=NULL ) {
	global $database;

	$categories[] = mosHTML::makeOption( '0', _SEL_CATEGORY );
	$database->setQuery( $query );
	$categories = array_merge( $categories, $database->loadObjectList() );
	$category = mosHTML::selectList( $categories, 'icsmart_content_catid', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', icsmart('icsmart_content_catid') );

	return $category;
}

function menuLink( $redirect, $id ) {
	global $database;

	$menu = strval( mosGetParam( $_POST, 'menuselect', '' ) );
	$link = strval( mosGetParam( $_POST, 'link_name', '' ) );

	$link	= stripslashes( ampReplace($link) );

	$row = new mosMenu( $database );
	$row->menutype 		= $menu;
	$row->name 			= $link;
	$row->type 			= 'content_item_link';
	$row->published		= 1;
	$row->componentid	= $id;
	$row->link			= 'index.php?ca=content&task=view&id='. $id;
	$row->ordering		= 9999;

	if (!$row->check()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	$row->checkin();
	$row->updateOrder( "menutype = " . $database->Quote( $row->menutype ) . " AND parent = " . (int) $row->parent );
	
	// clean any existing cache files
	mosCache::cleanCache( 'content' );

	$msg = $link .' (Ссылка - Объект содержимого) в меню: '. $menu .' successfully created';
	mosRedirect( 'index2.php?ca=content&task=edit&hidemainmenu=1&id='. $id, $msg );
}

function go2menu() {
	$menu = strval( mosGetParam( $_POST, 'menu', 'mainmenu' ) );

	mosRedirect( 'index2.php?ca=menus&menutype='. $menu );
}

function go2menuitem() {
	$menu 	= strval( mosGetParam( $_POST, 'menu', 'mainmenu' ) );
	$id		= intval( mosGetParam( $_POST, 'menuid', 0 ) );

	mosRedirect( 'index2.php?ca=menus&menutype='. $menu .'&task=edit&hidemainmenu=1&id='. $id );
}

function saveOrder( &$cid ) {
	global $database;

	$total		= count( $cid );
	$redirect 	= mosGetParam( $_POST, 'redirect', 0 );
	$rettask	= strval( mosGetParam( $_POST, 'returntask', '' ) );
	$order 		= $_REQUEST[ 'order' ];
	
	$row 		= new mosContent( $database );
	$conditions = array();

	// update ordering values
	for( $i=0; $i < $total; $i++ ) {
		$row->load( (int) $cid[$i] );
		if ($row->ordering != $order[$i]) { 
			$row->ordering = $order[$i]; ggri ($row->ordering);
			if (!$row->store()) { echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n"; exit(); } 
			// remember to updateOrder this group
			$condition = "catid = " . (int) $row->catid . " AND state >= 0";
			$found = false;
			foreach ( $conditions as $cond )
				if ($cond[1]==$condition) {
					$found = true; break;
				} // if
			if (!$found) $conditions[] = array($row->id, $condition);
		} // if
	} // for

	// execute updateOrder for each group
	foreach ( $conditions as $cond ) {
		$row->load( $cond[0] );
		$row->updateOrder( $cond[1] );
	} // foreach
	
	// clean any existing cache files
	mosCache::cleanCache( 'content' );

	$msg 	= 'Новый порядок сохранен';
	switch ( $rettask ) {
		case 'showarchive':
			mosRedirect( 'index2.php?ca=content&task=showarchive' , $msg );
			break;

		default:
			mosRedirect( 'index2.php?ca=content', $msg );
			break;
	} // switch
} // saveOrder
?>