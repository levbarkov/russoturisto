<?php
// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

require_once( igetPath( 'admin_html' ) );
global $task, $id, $reg;
$cid = josGetArrayInts( 'cid' );
switch ( $task ) {
	case 'cancel':
		cancel( $option );
		break;

	case 'new':
		edit( 0, $option );
		break;
/*	case 'removecfg1':	$adminlog = new adminlog(); $adminlog->logme('delcfg', 'Статичное содержимое', "", "" );
						load_adminclass('config'); $conf = new config($reg['db']); $conf->remove($_REQUEST['conf_values'], $_REQUEST['id']); 
						edit( $id, $option );
						break;
						*/
	case 'edit':
		edit( $id, $option );
		break;

	case 'editA':
		edit( intval( $cid[0] ), $option );
		break;

	case 'go2menu':
	case 'go2menuitem':
	case 'menulink':
	case 'save':
	case 'apply':
		save( $option, $task );
		break;

	case 'remove':
		trash( $cid, $option );
		break;

	case 'publish':
		changeState( $cid, 1, $option );
		break;

	case 'unpublish':
		changeState( $cid, 0, $option );
		break;

	case 'accesspublic':
		changeAccess( intval( $cid[0] ), 0, $option );
		break;

	case 'accessregistered':
		changeAccess( intval( $cid[0] ), 1, $option );
		break;

	case 'accessspecial':
		changeAccess( intval( $cid[0] ), 2, $option );
		break;

	case 'saveorder':
		saveOrder( $cid );
		break;
	case 'copysave':
		copyStatItemSave( $cid, $option );
		break;
	case 'cfg':			cfg();
						break;
	case 'savecfg':		load_adminclass('config');	 $conf = new config($reg['db']);   $conf->save_config();	$adminlog = new adminlog(); $adminlog->logme('cfg', 'Статичное содержимое', "", "" );
						mosRedirect( 'index2.php?ca='.$reg['ca'].'&task=cfg', "Настройки сохранены" );
						break;
	case 'removecfg':	$adminlog = new adminlog(); $adminlog->logme('delcfg', 'Статичное содержимое', "", "" );
						load_adminclass('config'); $conf = new config($reg['db']); $conf->remove($_REQUEST['conf_values'], $_REQUEST['id']); 
						mosRedirect( 'index2.php?ca='.$reg['ca'].'&task=cfg', "Настройки удалены" );
						break;
	default:
		view( $option );
		break;
}

function cfg(){
	global $reg;
	?><form name="adminForm" action="index2.php" method="post"><input type="hidden"  name="iuse" id="iuse" value="0" />
	<? load_adminclass('config');	$conf = new config($reg['db']);
	$conf->show_config('istatcontent', "Настройки параметров отображения статичного содержимого"); ?>
	<input type="hidden" name="task" value="savecfg"  />
	<input type="hidden" name="ca" value="<?=$reg['ca'] ?>" />
	<input type="submit" style="display:none;" /></form><?
}

/**
* Compiles a list of installed or defined modules
* @param database A database connector object
*/
function view( $option ) {
	global $database, $iConfig_list_limit;

	$filter_authorid 	= intval( getUserStateFromRequest( 'filter_authorid', 0 ) );
	$order 				= getUserStateFromRequest( 'zorder', 'c.ordering DESC' );
	$limit 				= intval( getUserStateFromRequest( 'limit', $iConfig_list_limit ) );
	$limitstart 		= intval( getUserStateFromRequest( 'limitstart', 0 ) );
	$search 			= getUserStateFromRequest( 'search', '' );
	if (get_magic_quotes_gpc()) {
		$search			= stripslashes( $search );
	}
	// used by filter
	if ( $search ) {
		$searchEscaped = $database->getEscaped( trim( mb_strtolower( $search,"UTF-8" ) ) );
		$search_query = "\n AND ( LOWER( c.title ) LIKE '%$searchEscaped%' OR LOWER( c.title_alias ) LIKE '%$searchEscaped%' )";
	} else {
		$search_query = '';
	}
	if (  icsmart('icsmart_typedcontent_search')  ) {
		$searchEscaped = $database->getEscaped(  icsmart('icsmart_typedcontent_search')  );
		$search_query = "\n AND ( LOWER( c.title ) LIKE '%$searchEscaped%' OR LOWER( c.title_alias ) LIKE '%$searchEscaped%' )";
	}
	else	$search_query = '';
	
	$filter = '';
	if ( $filter_authorid > 0 ) {
		$filter = "\n AND c.created_by = " . (int) $filter_authorid;
	}

	$orderAllowed = array( 'c.ordering ASC', 'c.ordering DESC', 'c.id ASC', 'c.id DESC', 'c.title ASC', 'c.title DESC', 'c.created ASC', 'c.created DESC', 'z.name ASC', 'z.name DESC', 'c.state ASC', 'c.state DESC', 'c.access ASC', 'c.access DESC' );
	if (!in_array( $order, $orderAllowed )) {
		$order = 'c.ordering DESC';
	}

	// sdneo -- . "\n AND c.title != ''"
	// get the total number of records
	$query = "SELECT count(*)"
	. "\n FROM #__content AS c"
	. "\n WHERE c.catid = 0"
	. "\n AND c.title != ''"
	. "\n AND c.state != -2"
	. $search_query
	. $filter
	;
	$database->setQuery( $query );
	$total = $database->loadResult();
	require_once( site_path . '/iadmin/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit );

	$query = "SELECT c.*, v.name AS author, u.name AS editor "
	. "\n FROM #__content AS c"
	. "\n LEFT JOIN #__users AS v ON v.id = c.created_by"
	. "\n LEFT JOIN #__users AS u ON u.id = c.checked_out"
	. "\n WHERE c.catid = 0"
	. "\n AND c.title != ''"
	. "\n AND c.state != -2"
	. $search_query
	. $filter
	. "\n ORDER BY ". $order
	;
	$database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$rows = $database->loadObjectList();
//ggtr ($database);
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	$count = count( $rows );
	for( $i = 0; $i < $count; $i++ ) {
		$query = "SELECT COUNT( id )"
		. "\n FROM #__menu"
		. "\n WHERE componentid = " . (int) $rows[$i]->id
		. "\n AND type = 'content_typed'"
		. "\n AND published != -2"
		;
		$database->setQuery( $query );
		$rows[$i]->links = $database->loadResult();
	}

	$ordering[] = mosHTML::makeOption( 'c.ordering ASC', 'Порядок по возрастанию' );
	$ordering[] = mosHTML::makeOption( 'c.ordering DESC', 'Порядок по убыванию' );
	$ordering[] = mosHTML::makeOption( 'c.id ASC', 'По возрастанию ID' );
	$ordering[] = mosHTML::makeOption( 'c.id DESC', 'По убыванию ID' );
	$ordering[] = mosHTML::makeOption( 'c.title ASC', 'Заголовки по алфавиту' );
	$ordering[] = mosHTML::makeOption( 'c.title DESC', 'Заголовки против алфавита' );
	$ordering[] = mosHTML::makeOption( 'c.created ASC', 'Дата по возрастанию' );
	$ordering[] = mosHTML::makeOption( 'c.created DESC', 'Дата по убыванию' );
	$ordering[] = mosHTML::makeOption( 'z.name ASC', 'Авторы по алфавиту' );
	$ordering[] = mosHTML::makeOption( 'z.name DESC', 'Авторы против алфавита' );
	$ordering[] = mosHTML::makeOption( 'c.state ASC', 'Сначала неопубликованные' );
	$ordering[] = mosHTML::makeOption( 'c.state DESC', 'Сначала опубликованные' );
	$ordering[] = mosHTML::makeOption( 'c.access ASC', 'Доступ по возрастанию' );
	$ordering[] = mosHTML::makeOption( 'c.access DESC', 'Доступ по убыванию' );
	$javascript = 'onchange="document.adminForm.submit();"';
	$lists['order'] = mosHTML::selectList( $ordering, 'zorder', 'class="inputtop" size="1"'. $javascript, 'value', 'text', $order );

	// get list of Authors for dropdown filter
	$query = "SELECT c.created_by AS value, u.name AS text"
	. "\n FROM #__content AS c"
	. "\n LEFT JOIN #__users AS u ON u.id = c.created_by"
	. "\n GROUP BY u.name"
	. "\n ORDER BY u.name"
	;
	$authors[] = mosHTML::makeOption( '0', _SEL_AUTHOR );
	$database->setQuery( $query );
	$authors = array_merge( $authors, $database->loadObjectList() );
	$lists['authorid']	= mosHTML::selectList( $authors, 'filter_authorid', 'class="inputtop" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', $filter_authorid );

	HTML_typedcontent::showContent( $rows, $pageNav, $option, $search, $lists );
}

/**
* Compiles information to add or edit content
* @param database A database connector object
* @param string The name of the category section
* @param integer The unique id of the category to edit (0 if new)
*/
function edit( $uid, $option ) {
	global $database, $my;
	global  $iConfig_offset;

	$row = new mosContent( $database );
	$row->load( (int)$uid );
	
	$lists = array();
	$nullDate 	= $database->getNullDate();
	if ($uid) {
		// fail if checked out not by 'me'
		//if ($row->isCheckedOut( $my->id )) {
		//	mosErrorAlert( "Модуль ".$row->title." в настоящее время редактируется другим администратором" );
		//}

		$row->checkout( $my->id );
	
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

		// get list of links to this item
		$and 	= "\n AND componentid = " . (int) $row->id;
		$menus 	= mosAdminMenus::Links2Menu( 'content_typed', $and );
	} else {
		// initialise values for a new item
		$row->version 		= 0;
		$row->state 		= 1;
		$row->images 		= "";
		$row->publish_up 	= date( 'Y-m-d H:i:s', time() + ( $iConfig_offset * 60 * 60 ) );
		$row->publish_down 	= 'Никогда';
		$row->catid 		= 0;
		$row->creator 		= '';
		$row->modified 		= $nullDate;
		$row->modifier 		= '';
		$row->ordering 		= 0;
		$menus = array();
	}

	// calls function to read image from directory
	$pathA 		= site_path .'/images/stories';
	$pathL 		= site_url .'/images/stories';
	$images 	= array();
	$folders 	= array();
	$folders[] 	= mosHTML::makeOption( '/' );
	mosAdminMenus::ReadImages( $pathA, '/', $folders, $images );
	// list of folders in images/stories/
	$lists['folders'] 		= "";//mosAdminMenus::GetImageFolders( $folders, $pathL );
	// list of images in specfic folder in images/stories/
	$lists['imagefiles']	= "";//mosAdminMenus::GetImages( $images, $pathL );
	// list of saved images
	$lists['imagelist'] 	= "";//mosAdminMenus::GetSavedImages( $row, $pathL );

	// build list of users
	$active = ( intval( $row->created_by ) ? intval( $row->created_by ) : $my->id );
	$lists['created_by'] 	= mosAdminMenus::UserSelect( 'created_by', $active );
	// build the html select list for the group access
	$lists['access'] 		= mosAdminMenus::Access( $row );
	// build the html select list for menu selection
	$lists['menuselect']	= mosAdminMenus::MenuSelect( );
	// build the select list for the image positions
	$lists['_align'] 		= mosAdminMenus::Positions( '_align' );
	// build the select list for the image caption alignment
	$lists['_caption_align'] 	= mosAdminMenus::Positions( '_caption_align' );
	// build the select list for the image caption position
	$pos[] = mosHTML::makeOption( 'bottom', _CMN_BOTTOM );
	$pos[] = mosHTML::makeOption( 'top', _CMN_TOP );
	$lists['_caption_position'] = mosHTML::selectList( $pos, '_caption_position', 'class="inputbox" size="1"', 'value', 'text' );

	// get params definitions
	$params = new mosParameters( $row->attribs, igetPath( 'com_xml', 'typedcontent' ), 'component' );
	HTML_typedcontent::edit( $row, $images, $lists, $params, $option, $menus );
}

/**
* Saves the typed content item
*/
function save( $option, $task ) {
	global $database, $my, $iConfig_offset, $reg;

	$nullDate = $database->getNullDate();
	$menu 		= strval( mosGetParam( $_POST, 'menu', 'mainmenu' ) );
	$menuid		= intval( mosGetParam( $_POST, 'menuid', 0 ) );
	
	$row = new mosContent( $database );
//	$row = new mosDBTable( "#__content", "id", $reg['db'] );
	
	if (!$row->bind( $_POST )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	//ggd ($row);
	if ( $row->id ) {
		$row->modified = date( 'Y-m-d H:i:s' );
		$row->modified_by = $my->id;
	}
	
	$row->created_by 	= $row->created_by ? $row->created_by : $my->id;
	
	if ($row->created && strlen(trim( $row->created )) <= 10) {
		$row->created 	.= ' 00:00:00';
	}
	$row->created 		= $row->created ? mosFormatDate( $row->created, _CURRENT_SERVER_TIME_FORMAT, -$iConfig_offset ) : date( 'Y-m-d H:i:s' );
	
	if (strlen(trim( $row->publish_up )) <= 10) {
		$row->publish_up .= ' 00:00:00';
	}
	$row->publish_up = mosFormatDate($row->publish_up, _CURRENT_SERVER_TIME_FORMAT, -$iConfig_offset );
	
	if (trim( $row->publish_down ) == 'Никогда' || trim( $row->publish_down ) == '') {
		$row->publish_down = $nullDate;
	} else {
		if (strlen(trim( $row->publish_down )) <= 10) {
			$row->publish_down .= ' 00:00:00';
	}
		$row->publish_down = mosFormatDate( $row->publish_down, _CURRENT_SERVER_TIME_FORMAT, -$iConfig_offset );
	}

	$row->state = intval( mosGetParam( $_REQUEST, 'published', 0 ) );

	// Save Parameters
	$params = mosGetParam( $_POST, 'params', '' );
	if (is_array( $params )) {
		$txt = array();
		foreach ( $params as $k=>$v) {
			$txt[] = "$k=$v";
		}
		$row->attribs = implode( "\n", $txt );
	}

	// code cleaner for xhtml transitional compliance
	$row->introtext = str_replace( '<br>', '<br />', $row->introtext );

	if (  ggrr('sefname')!=''  ) $row->sefname = sefname( ggrr('sefname') );
	else $row->sefname = sefname( $row->title );
	
	if (!$row->check()) {		echo "<script> alert('==CHECK==".$row->getError()."'); window.history.go(-1); </script>\n";		exit();	}
	if (!$row->store()) {		echo "<script> alert('==STORE==".$row->getError()."'); window.history.go(-1); </script>\n";		exit();	}
//	ggdd();
	$row->checkin();

	// clean any existing cache files
	mosCache::cleanCache( 'content' );
	
	$adminlog = new adminlog();	
	if (  ggri('id')==0  )	$adminlog->logme('new', "Статичное содержимое", $row->title, $row->id ); else $adminlog->logme('save', "Статичное содержимое", $row->title, $row->id );

	/*
	 * СОХРАНЯЕМ ИНДИВИДУАЛЬНЫЙ КОНФИГ
	 */	
	load_adminclass('config');	 
	$conf = new config($reg['db']);
	$conf->prefix_id = '#__content'."_ID".$row->id."__";
	$conf->save_config();

	// УДАЛЯЕМ ОСНОВНОЕ ФОТО, Если пользователь поставил галочку - Удалить изображение
	$component_foto = new component_foto( 0 );
	$component_foto->init( 'typedcontent_main' );
	$component_foto->parent = $row->id;
	$component_foto->delmainfoto_ifUserSetChackBox();

	if (  $_FILES["newfoto"]['tmp_name']  ){	// ВЫБРАНО НОВОЕ ФОТО - РЕДИРЕКТ НА ФОТОГАЛЕРЕЮ
		switch ( $task ) {
			case 'apply':	$ret_url = 'index2.php?ca=typedcontent&task=edit&hidemainmenu=1&id='.$row->id;  
							$ret_msg = 'Изменения успешно сохранены в: '. $row->title; break;
			case 'save':
			default:		$ret_url = 'index2.php?ca=typedcontent';	  
							$ret_msg = 'Успешно сохранено: '. $row->title; break;
		}
                $component_foto->publish = 'dont_save_publish';  // так как у объекта typedcontent - publish не актуален
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
			menuLink( $option, $row->id );
			break;

		case 'save':
			$msg = 'Содержимое сохранено';
			global $option;
			$limitstart_pref = ""; if (  icsmarti("icsmart_".$option."_limitstart")>0  ) $limitstart_pref = "&limitstart=".icsmarti("icsmart_".$option."_limitstart");
			$limit_pref = ""; if (  icsmarti("icsmart_".$option."_limit")>0  ) $limit_pref = "&limit=".icsmarti("icsmart_".$option."_limit");
			mosRedirect( 'index2.php?ca='. $option.$limitstart_pref.$limit_pref, $msg );
			break;

		case 'apply':
		default:
			$msg = 'Все изменения содержимого сохранены';
			mosRedirect( 'index2.php?ca='. $option .'&task=edit&hidemainmenu=1&id='. $row->id, $msg );
			break;
	}
}

/**
* Trashes the typed content item
*/
function trash( &$cid, $option ) {
	global $database, $reg;

	$total = count( $cid );
	if ( $total < 1) {
		echo "<script> alert('Выберите объект для удаления'); window.history.go(-1);</script>\n";
		exit;
	}

	$state = '-2';
	$ordering = '0';
	foreach ($_REQUEST['cid'] as $dfgd){
		// удаляем фото
		$component_foto = new component_foto ( 0 );
		$component_foto->init($reg['ca']);
		$component_foto->parent = $dfgd;
		$component_foto->load_parent();
		$component_foto->del_fotos();

		// удаляем комменты
		$comments = new comments('content', $reg['db'], $reg);
		$comments->deleteAllComments( $dfgd );

		$adminlog_obg = $component_foto->parent_obj;	$adminlog = new adminlog(); $adminlog->logme('del', 'Статичное содержимое', $adminlog_obg->title, $adminlog_obg->id );
		ggsqlq ("DELETE FROM #__content WHERE id=".$dfgd);

                // удаление индивидуальных настроек
                load_adminclass('config');
                $conf = new config($reg['db']);
                $conf->prefix_id = '#__content'."_ID".$dfgd."__";
                $conf->remove_addition_config();
	}
	// clean any existing cache files
	mosCache::cleanCache( 'content' );

	$msg = "Удалено: ".$total." объект (ов)";
	$return = strval( mosGetParam( $_POST, 'returntask', '' ) );
	mosRedirect( 'index2.php?ca='. $option .'&task='. $return, $msg );
}

/**
* Changes the state of one or more content pages
* @param string The name of the category section
* @param integer A unique category id (passed from an edit form)
* @param array An array of unique category id numbers
* @param integer 0 if unpublishing, 1 if publishing
* @param string The name of the current user
*/
function changeState( $cid=null, $state=0, $option ) {
	global $database, $my;

	if (count( $cid ) < 1) {
		$action = $state == 1 ? 'публикации' : ($state == -1 ? 'архивирования' : 'сокрытия');
		echo "<script> alert('Выберите объект для $action'); window.history.go(-1);</script>\n";
		exit;
	}

	mosArrayToInts( $cid );
	$total 	= count ( $cid );
	$cids	= 'id=' . implode( ' OR id=', $cid );

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
										  
	if ( $state == "1" ) {
		$msg = " Объектов успешно опубликовано - ".$total;
	} else if ( $state == "0" ) {
		$msg = " Объектов успешно скрыто - ".$total;
	}
	mosRedirect( 'index2.php?ca='. $option .'&msg='. $msg );
}

/**
* changes the access level of a record
* @param integer The increment to reorder by
*/
function changeAccess( $id, $access, $option  ) {
	global $database;

	$row = new mosContent( $database );
	$row->load( (int)$id );
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



/**
* Cancels an edit operation
* @param database A database connector object
*/
function cancel( $option ) {
	global $database;

	//$row = new mosContent( $database );
	//$row->bind( $_POST );
	//$row->checkin();
	mosRedirect( 'index2.php?ca='. $option );
}

function menuLink( $option, $id ) {
	global $database;

	$menu 	= strval( mosGetParam( $_POST, 'menuselect', '' ) );
	$link 	= strval( mosGetParam( $_POST, 'link_name', '' ) );

	$link	= stripslashes( ampReplace($link) );

	$row 				= new mosMenu( $database );
	$row->menutype 		= $menu;
	$row->name 			= $link;
	$row->type 			= 'content_typed';
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
	$row->updateOrder( "menutype=" . $database->Quote( $row->menutype ) . " AND parent=" . (int) $row->parent );
 
	// clean any existing cache files
	mosCache::cleanCache( 'content' );

	$msg = $link .' - (Ссылка - Статичное содержимое) в меню: '. $menu .' успешно создано';
	mosRedirect( 'index2.php?ca='. $option .'&task=edit&hidemainmenu=1&id='. $id, $msg );
}

function go2menu() {
	global $database;

	// checkin content
	$row = new mosContent( $database );
	$row->bind( $_POST );
	$row->checkin();

	$menu = strval( mosGetParam( $_POST, 'menu', 'mainmenu' ) );

	mosRedirect( 'index2.php?ca=menus&menutype='. $menu );
}

function go2menuitem() {
	global $database;

	// checkin content
	$row = new mosContent( $database );
	$row->bind( $_POST );
	$row->checkin();

	$menu 	= strval( mosGetParam( $_POST, 'menu', 'mainmenu' ) );
	$id		= intval( mosGetParam( $_POST, 'menuid', 0 ) );

	mosRedirect( 'index2.php?ca=menus&menutype='. $menu .'&task=edit&hidemainmenu=1&id='. $id );
}

function saveOrder( &$cid ) {
	global $database;

	$total		= count( $cid );
	$order 		= josGetArrayInts( 'order' );
	
	$row 		= new mosContent( $database );
	$conditions = array();

	// update ordering values
	for ( $i=0; $i < $total; $i++ ) {
		$row->load( (int) $cid[$i] );
		if ($row->ordering != $order[$i]) {
			$row->ordering = $order[$i];
			if (!$row->store()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
				exit();
			} // if
			// remember to updateOrder this group
			$condition = "catid=" . (int) $row->catid . " AND state >= 0";
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
							  
	// clean any existing cache files
	mosCache::cleanCache( 'content' );

	$msg 	= 'Новый порядок сохранен';
	mosRedirect( 'index2.php?ca=typedcontent', $msg );
} // saveOrder

function copyStatItemSave( $cid, $option ) {
	global $database;
	
	$total = count( $cid );
	for ( $i = 0; $i < $total; $i++ ) {
		$row = new mosContent( $database );
		// main query
		$query = "SELECT a.*"
		. "\n FROM #__content AS a"
		. "\n WHERE a.id = " . (int) $cid[$i]
		;
		$database->setQuery( $query );
		$item = $database->loadObjectList();

		// values loaded into array set for store
		$row->id 				= NULL;
		$row->catid 			=  0;
		$row->ordering			= '0';
		$row->title 			= $item[0]->title."_копия";
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
		if (!$row->check()) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		if (!$row->store()) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		$row->updateOrder( "catid=0 AND state >= 0" );
		// находим новый ID
		$iexgoodnewID = $row->id;
		
		// теперь необходимо организовать копию фото товаров
		$exgoodfotos = ggsql( "SELECT * FROM #__content_foto WHERE content_id=".(int) $cid[$i] );
		foreach ($exgoodfotos as $exgoodfoto){
			$iexuni = md5(uniqid("content"));
			$iexuni_old = cut_element ($exgoodfoto->small, "___", ".");
			$small_new_name = str_replace($iexuni_old, $iexuni, $exgoodfoto->small);
			$org_new_name = str_replace($iexuni_old, $iexuni, $exgoodfoto->org);
			$foto_path = site_path."/images/icat/icont/";
			copy($foto_path.$exgoodfoto->small, $foto_path.$small_new_name);
			copy($foto_path.$exgoodfoto->org, $foto_path.$org_new_name);
			// сохраняем новое фото
			$i24r = new mosDBTable( "#__content_foto", "id", $database );
			$exgoodid	= $exgoodfoto->content_id;
			$i24r->id = 0;
			$i24r->content_id = $iexgoodnewID;
			$i24r->small = $small_new_name;
			$i24r->org = $org_new_name;
			$i24r->order = $exgoodfoto->order;
			$i24r->desc = $exgoodfoto->desc;
			if (!$i24r->check()) {
				echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";
			} else $i24r->store();
		}
	}		 
	$msg = $total. ' объект(ы) успешно скопированы ';
	mosRedirect( 'index2.php?ca='. $option .'&mosmsg='. $msg );
}
?>