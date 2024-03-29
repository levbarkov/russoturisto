<?php
/**
* @version $Id: mosvote.php 3500 2006-05-15 04:31:11Z stingrey $
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

$_MAMBOTS->registerFunction( 'onBeforeDisplayContent', 'botVoting' );

function botVoting( &$row, &$params, $page=0 ) {
	global $Itemid, $task;

	$id 	= $row->id;
	$option = 'com_content';

	$html = '';
	if ($params->get( 'rating' ) && !$params->get( 'popup' )){
		$html .= '<form method="post" action="' . sefRelToAbs( 'index.php' ) . '">';
		$img = '';

		// искать в шаблоне доступные изображения
		$starImageOn 	= mosAdminMenus::ImageCheck( 'rating_star.png', '/images/M_images/' );
		$starImageOff 	= mosAdminMenus::ImageCheck( 'rating_star_blank.png', '/images/M_images/' );

		for ($i=0; $i < $row->rating; $i++) {
			$img .= $starImageOn;
		}
		for ($i=$row->rating; $i < 5; $i++) {
			$img .= $starImageOff;
		}
		$html .= '<span class="content_rating">';
		$html .= _USER_RATING . ':' . $img . '&nbsp;/&nbsp;';
		$html .= intval( $row->rating_count );
		$html .= "</span>\n<br />\n";
		$url = @$_SERVER['REQUEST_URI'];
		$url = ampReplace( $url );

		if (!$params->get( 'intro_only' ) && $task != "blogsection") {
			$html .= '<span class="content_vote">';
			$html .= _VOTE_POOR;
			$html .= '<input type="radio" alt="vote 1 star" name="user_rating" value="1" />';
			$html .= '<input type="radio" alt="vote 2 star" name="user_rating" value="2" />';
			$html .= '<input type="radio" alt="vote 3 star" name="user_rating" value="3" />';
			$html .= '<input type="radio" alt="vote 4 star" name="user_rating" value="4" />';
			$html .= '<input type="radio" alt="vote 5 star" name="user_rating" value="5" checked="checked" />';
			$html .= _VOTE_BEST;
			$html .= '&nbsp;<input class="button" type="submit" name="submit_vote" value="'. _RATE_BUTTON .'" />';
			$html .= '<input type="hidden" name="task" value="vote" />';
			$html .= '<input type="hidden" name="pop" value="0" />';
			$html .= '<input type="hidden" name="option" value="com_content" />';
			$html .= '<input type="hidden" name="Itemid" value="'. $Itemid .'" />';
			$html .= '<input type="hidden" name="cid" value="'. $id .'" />';
			$html .= '<input type="hidden" name="url" value="'. $url .'" />';
			$html .= '</span>';
		}
		$html .= '</form>';
	}
	return $html;
}
?>