<?php
/**
* @version $Id: mospaging.php 2574 2006-02-23 18:48:16Z stingrey $
* @package Joomla  RE
* @localized Авторские права (C) 2005 Joom.Ru - Русский дом Joomla!
* @copyright Авторские права (C) 2005 Open Source Matters. Все права защищены.
* @license Лицензия http://www.gnu.org/copyleft/gpl.html GNU/GPL, смотрите LICENSE.php
* Joomla! - свободное программное обеспечение. Эта версия может быть изменена
* в соответствии с Генеральной Общественной Лицензией GNU, поэтому возможно
* её дальнейшее распространение в составе результата работы, лицензированного
* согласно Генеральной Общественной Лицензией GNU или других лицензий свободных 
* программ или программ с открытым исходным кодом.
* Для просмотра подробностей и замечаний об авторском праве, смотрите файл COPYRIGHT.php.
* 
* @translator Oleg A. Myasnikov aka Sourpuss (sourpuss@mamboteam.ru)
*/

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ ограничен' );

$_MAMBOTS->registerFunction( 'onPrepareContent', 'botMosPaging' );

/**
* Мамбот разрыва страницы
*
* <b>Использование:</b>
* <code>{mospagebreak}</code>
* <code>{mospagebreak title=Заголовок страницы}</code>
* или
* <code>{mospagebreak heading=Первая страница}</code>
* или
* <code>{mospagebreak title=Заголовок страницы&heading=Первая страница}</code>
* или
* <code>{mospagebreak heading=Первая страница&title=Заголовок страницы}</code>
*
*/
function botMosPaging( $published, &$row, &$params, $page=0 ) {
	global $mainframe, $Itemid, $database, $_MAMBOTS;

	// simple performance check to determine whether bot should process further
	if ( strpos( $row->text, 'mospagebreak' ) === false ) {
		return true;
	}
	
 	// expression to search for
 	$regex = '/{(mospagebreak)\s*(.*?)}/i';

 	// check whether mambot has been unpublished
 	if (!$published || $params->get( 'intro_only' )|| $params->get( 'popup' )) {
		$row->text = preg_replace( $regex, '', $row->text );
		return;
	}

	// найти все образцы мамбота и вставить в $matches
	$matches = array();
	preg_match_all( $regex, $row->text, $matches, PREG_SET_ORDER );

	// мамбот разрыва текста
	$text = preg_split( $regex, $row->text );

	// подсчет числа страниц
	$n = count( $text );

	// если найден хоть еще один мамбот, то как минимум 2 страницы 
	if ($n > 1) {
		// check if param query has previously been processed
		if ( !isset($_MAMBOTS->_content_mambot_params['mospaging']) ) {
			// load mambot params info
			$query = "SELECT params"
		. "\n FROM #__mambots"
		. "\n WHERE element = 'mospaging'"
		. "\n AND folder = 'content'"
		;
		$database->setQuery( $query );
			$database->loadObject($mambot);		
			
			// save query to class variable
			$_MAMBOTS->_content_mambot_params['mospaging'] = $mambot;
		}
		
		// pull query data from class variable
		$mambot = $_MAMBOTS->_content_mambot_params['mospaging'];
		
	 	$botParams = new mosParameters( $mambot->params );

	 	$title	= $botParams->def( 'title', 1 );

	 	// добавление заголовка или названия в  название <site>
	 	if ( $title ) {
			$page_text = $page + 1;
			$row->page_title = _PN_PAGE .' '. $page_text;
			if ( !$page ) {
				// обработка первой страницы
				parse_str( html_entity_decode( $matches[0][2] ), $args );

				if ( @$args['heading'] ) {
					//$row->page_title = $args['heading'];
					$row->page_title = '';
				} else {
					$row->page_title = '';
				}
			} else if ( $matches[$page-1][2] ) {
				parse_str( html_entity_decode( $matches[$page-1][2] ), $args );

				if ( @$args['title'] ) {
					$row->page_title = ': '. stripslashes( $args['title'] );
				}
			}
	 	}

		// сброс текста, удержание его в массиве $text
		$row->text = '';

		$hasToc = $mainframe->getCfg( 'multipage_toc' );

		if ( $hasToc ) {
			// отображение содержания
			createTOC( $row, $matches, $page );
		} else {
			$row->toc = '';
		}

		// обычная навигация joomla по страницам
		require_once( $GLOBALS['mosConfig_absolute_path'] . '/includes/pageNavigation.php' );
		$pageNav = new mosPageNav( $n, $page, 1 );

		// счетчик страниц
		$row->text .= '<div class="pagenavcounter">';
		$row->text .= $pageNav->writeLeafsCounter();
		$row->text .= '</div>';

		// текст страницы
		$row->text .= $text[$page];

		$row->text .= '<br />';
		$row->text .= '<div class="pagenavbar">';

		// добавление навигации между страницами в конце текста
		if ( $hasToc ) {
			createNavigation( $row, $page, $n );
		}

		// отображение ссылок на страницы, если запрещено содержание
		if (!$hasToc) {
			$row->text .= $pageNav->writePagesLinks( 'index.php?option=com_content&amp;task=view&amp;id='. $row->id .'&amp;Itemid='. $Itemid );
		}

		$row->text .= '</div><br />';
	}

	return true;
}

function createTOC( &$row, &$matches, &$page ) {
	global $Itemid;

	$nonseflink = 'index.php?option=com_content&amp;task=view&amp;id='. $row->id .'&amp;Itemid='. $Itemid;
	$link = 'index.php?option=com_content&amp;task=view&amp;id='. $row->id .'&amp;Itemid='. $Itemid;
	$link = sefRelToAbs( $link );

	$heading = $row->title;
	// позволяет настройку названия первой страницы, проверяя атрибут `heading` в первом боте
	if ( @$matches[0][2] ) {
		parse_str( html_entity_decode( $matches[0][2] ), $args );

		if ( @$args['heading'] ) {
			$heading = $args['heading'];
			$row->title .= ' - '. $heading;
		}
	}

	// Заголовок содержания
	$row->toc = '
	<table cellpadding="0" cellspacing="0" class="contenttoc" align="right">
	<tr>
		<th>'
		. _TOC_JUMPTO .
		'</th>
	</tr>
	';

	// Содержание связи первой страницы
	$row->toc .= '
	<tr>
		<td>
		<a href="'. $link .'" class="toclink">'
		. $heading .
		'</a>
		</td>
	</tr>
	';

	$i = 2;
	$args2 = array();

	foreach ( $matches as $bot ) {
		$link = $nonseflink .'&amp;limit=1&amp;limitstart='. ($i-1);
		$link = sefRelToAbs( $link );

		if ( @$bot[2] ) {
			parse_str( html_entity_decode( $bot[2] ), $args2 );

			if ( @$args2['title'] ) {
				$row->toc .= '
				<tr>
					<td>
					<a href="'. $link .'" class="toclink">'
					. stripslashes( $args2['title'] ) .
					'</a>
					</td>
				</tr>
				';
			} else {
				$row->toc .= '
				<tr>
					<td>
					<a href="'. $link .'" class="toclink">'
					. _PN_PAGE .' '. $i .
					'</a>
					</td>
				</tr>
				';
			}
		} else {
			$row->toc .= '
			<tr>
				<td>
				<a href="'. $link .'" class="toclink">'
				. _PN_PAGE .' '. $i .
				'</a>
				</td>
			</tr>
			';
		}
		$i++;
	}

	$row->toc .= '</table>';
}

function createNavigation( &$row, $page, $n ) {
	global $Itemid;

	$link = 'index.php?option=com_content&amp;task=view&amp;id='. $row->id .'&amp;Itemid='. $Itemid;

	if ( $page < $n-1 ) {
		$link_next = $link .'&amp;limit=1&amp;limitstart='. ( $page + 1 );
		$link_next = sefRelToAbs( $link_next );

		$next = '<a href="'. $link_next .'">' ._CMN_NEXT . _CMN_NEXT_ARROW .'</a>';
	} else {
		$next = _CMN_NEXT;
	}

	if ( $page > 0 ) {
		$link_prev = $link .'&amp;limit=1&amp;limitstart='. ( $page - 1 );
		$link_prev = sefRelToAbs( $link_prev );

		$prev = '<a href="'. $link_prev .'">'. _CMN_PREV_ARROW . _CMN_PREV .'</a>';
	} else {
		$prev = _CMN_PREV;
	}

	$row->text .= '<div>' . $prev . ' - ' . $next .'</div>';
}
?>
