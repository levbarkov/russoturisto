<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ ограничен' );

$_MAMBOTS->registerFunction( 'onSearch', 'botSearchContent' );

/**
* Content Search method
*
* запрос sql должен возвратить поля, используются в обычной операции 
* отображения: href, title, section, created, text, browsernav
* @param определяет цель поиска
* @param сопоставляет параметры: exact|any|all
* @param определяет параметр сортировки: newest|oldest|popular|alpha|category
*/
function botSearchContent( $text, $phrase='', $ordering='' ) {
	global $database, $my, $_MAMBOTS, $reg;

	// check if param query has previously been processed
	if ( !isset($_MAMBOTS->_search_mambot_params['content']) ) {
	// load mambot params info
	$query = "SELECT params"
	. "\n FROM #__mambots"
	. "\n WHERE element = 'content.searchbot'"
	. "\n AND folder = 'search'"
	;
	$database->setQuery( $query );
	$database->loadObject($mambot);
	
		// save query to class variable
		$_MAMBOTS->_search_mambot_params['content'] = $mambot;
	}

	// pull query data from class variable
	$mambot = $_MAMBOTS->_search_mambot_params['content'];	

	$botParams = new mosParameters( $mambot->params );
	
	$limit 		= $botParams->def( 'search_limit', 50 );
	$nonmenu	= $botParams->def( 'nonmenu', 1 );

	$nullDate = $database->getNullDate();
	$now 		= _CURRENT_SERVER_TIME;

	$text = trim( $text );
	if ($text == '') {
		return array();
	}

	$wheres = array();
	switch ($phrase) {
		case 'exact':
			$wheres2 = array();
			$wheres2[] 	= "LOWER(a.title) LIKE LOWER('%$text%')";
			$wheres2[] 	= "LOWER(a.introtext) LIKE LOWER('%$text%')";
			$wheres2[] 	= "LOWER(a.fulltext) LIKE LOWER('%$text%')";
			$wheres2[] 	= "LOWER(a.metakey) LIKE LOWER('%$text%')";
			$wheres2[] 	= "LOWER(a.metadesc) LIKE LOWER('%$text%')";
			$where = '(' . implode( ') OR (', $wheres2 ) . ')';
			break;
			
		case 'all':
		case 'any':
		default:
			$words = explode( ' ', $text );
			$wheres = array();
			foreach ($words as $word) {
				$wheres2 = array();
				$wheres2[] 	= "LOWER(a.title) LIKE LOWER('%$word%')";
				$wheres2[] 	= "LOWER(a.introtext) LIKE LOWER('%$word%')";
				$wheres2[] 	= "LOWER(a.fulltext) LIKE LOWER('%$word%')";
				$wheres2[] 	= "LOWER(a.metakey) LIKE LOWER('%$word%')";
				$wheres2[] 	= "LOWER(a.metadesc) LIKE LOWER('%$word%')";
				$wheres[] = implode( ' OR ', $wheres2 );
			}
			$where = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres ) . ')';
			break;
	}
	$morder = '';
/*	switch ($ordering) {
		case 'oldest':
			$order = 'a.created ASC'; break;
		case 'popular':
			$order = 'a.hits DESC'; break;			
		case 'alpha':
			$order = 'a.title ASC'; break;
		case 'category':
			$order = 'b.title ASC, a.title ASC';
			$morder = 'a.title ASC'; break;
		case 'newest':
		default:
			$order = 'a.created DESC'; break;		
	}*/

	// search content items
	$query = "SELECT a.title AS title,"
	. "\n a.created AS created,"
	. "\n CONCAT(a.introtext, a.fulltext) AS text,"
	. "\n CONCAT( '', a.sefnamefullcat, '/', a.sefname, '.html' ) AS href,"
	. "\n '2' AS browsernav, " . $database->Quote( $reg['content_name'] ) . " AS section,"
	. "\n 'content' AS type"
 	. "\n, b.id as cat_id"
	. "\n FROM #__content AS a"
	. "\n INNER JOIN #__icat AS b ON b.id=a.catid"
	. "\n WHERE ( $where )"
	. "\n AND a.state = 1"
	. "\n AND b.publish = 1"
	. "\n AND a.access <= " . (int) $my->gid
	. "\n AND ( a.publish_up = " . $database->Quote( $nullDate ) . " OR a.publish_up <= " . $database->Quote( $now ) . " )"
	. "\n AND ( a.publish_down = " . $database->Quote( $nullDate ) . " OR a.publish_down >= " . $database->Quote( $now ) . " )"
	. "\n GROUP BY a.id"
	;
	$database->setQuery( $query, 0, $limit );
	$list = $database->loadObjectList();

	// search all static content
	$query = "SELECT a.title AS title,"
	. "\n a.created AS created,"
	. "\n a.introtext AS text,"
	. "\n '2' as browsernav, " . $database->Quote( _STATIC_CONTENT ) . " AS section,"
	. "\n CONCAT( '/', a.sefname ) AS href,"
	. "\n a.id"
	. "\n FROM #__content AS a"
	. "\n WHERE ($where)"
	. (($ids) ? "\n AND ( $ids )" : '')
	. "\n AND a.state = 1"
	. "\n AND a.access <= " . (int) $my->gid
	. "\n AND a.catid = 0"
	. "\n AND ( a.publish_up = " . $database->Quote( $nullDate ) . " OR a.publish_up <= " . $database->Quote( $now ) . " )"
	. "\n AND ( a.publish_down = " . $database->Quote( $nullDate ) . " OR a.publish_down >= " . $database->Quote( $now ) . " )"
	;
	$database->setQuery( $query, 0, $limit );
	$list4 = $database->loadObjectList();

/*
	// поиск архивного содержимого
	$query = "SELECT a.title AS title,"
	. "\n a.created AS created,"
	. "\n a.introtext AS text,"
	. "\n CONCAT_WS( '/', " . $database->Quote( _SEARCH_ARCHIVED ) . ", u.title, b.title ) AS section,"
	. "\n CONCAT('index.php?option=com_content&task=view&id=',a.id) AS href,"
	. "\n '2' AS browsernav,"
	. "\n 'content' AS type"
	. "\n FROM #__content AS a"
	. "\n INNER JOIN #__categories AS b ON b.id=a.catid"
	. "\n INNER JOIN #__sections AS u ON u.id = a.sectionid"
	. "\n WHERE ( $where )"
	. "\n AND a.state = -1"
	. "\n AND u.published = 1"
	. "\n AND b.published = 1"
	. "\n AND a.access <= " . (int) $my->gid
	. "\n AND b.access <= " . (int) $my->gid
	. "\n AND u.access <= " . (int) $my->gid
	. "\n AND ( a.publish_up = " . $database->Quote( $nullDate ) . " OR a.publish_up <= " . $database->Quote( $now ) . " )"
	. "\n AND ( a.publish_down = " . $database->Quote( $nullDate ) . " OR a.publish_down >= " . $database->Quote( $now ) . " )"
	;
	$database->setQuery( $query, 0, $limit );
	$list3 = $database->loadObjectList();
*/





	// ПОИСК В КАТАЛОГЕ - ТОВАРЫ
	$wheres_exgood = array();
	switch ($phrase) {
		case 'exact':
			$wheres2_exgood = array();
			$wheres2_exgood[] 	= "LOWER(a.name) LIKE LOWER('%$text%')";
			$wheres2_exgood[] 	= "LOWER(a.sdesc) LIKE LOWER('%$text%')";
			$wheres2_exgood[] 	= "LOWER(a.fdesc) LIKE LOWER('%$text%')";
			$where_exgood = '(' . implode( ') OR (', $wheres2_exgood ) . ')';
			break;
		case 'all':
		case 'any':
		default:
			$words = explode( ' ', $text );
			$wheres_exgood = array();
			foreach ($words as $word) {
				$wheres2_exgood = array();
				$wheres2_exgood[] 	= "LOWER(a.name) LIKE LOWER('%$word%')";
				$wheres2_exgood[] 	= "LOWER(a.sdesc) LIKE LOWER('%$word%')";
				$wheres2_exgood[] 	= "LOWER(a.fdesc) LIKE LOWER('%$word%')";
				$wheres_exgood[] = implode( ' OR ', $wheres2_exgood );
			}
			$where_exgood = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres_exgood ) . ')';
			break;
	}
	$query = "SELECT a.name AS title,"
	. "\n CONCAT(a.sdesc, a.fdesc) AS text,"
	. "\n CONCAT( a.sefnamefullcat, '/', a.sefname, '.html' ) AS href,"
	. "\n '2' AS browsernav, " . $database->Quote( 'Каталог / товары' ) . " AS section,"
	. "\n 'content' AS type"
	. "\n FROM #__exgood AS a"
	. "\n WHERE ( $where_exgood )"
	. "\n AND a.publish = 1"
	. "\n GROUP BY a.id"
	;
	$database->setQuery( $query, 0, $limit );
	$list5 = $database->loadObjectList();

	// ПОИСК В КАТАЛОГЕ - КАТЕГОРИИ
	$query = "SELECT a.name AS title,"
	. "\n CONCAT(a.sdesc, a.fdesc) AS text,"
	. "\n CONCAT( a.sefnamefull, '/', a.sefname ) AS href,"
	. "\n '2' AS browsernav, " . $database->Quote( 'Каталог / категория' ) . " AS section,"
	. "\n 'content' AS type"
	. "\n FROM #__excat AS a"
	. "\n WHERE ( $where_exgood )"
	. "\n AND a.publish = 1"
	. "\n GROUP BY a.id"
	;
	$database->setQuery( $query, 0, $limit );
	$list7 = $database->loadObjectList();
//	return $list;
	return array_merge( $list, $list4, $list5, $list7 );
	#return array_merge( $list, $list2, $list3, (array)$list4 );
}
?>