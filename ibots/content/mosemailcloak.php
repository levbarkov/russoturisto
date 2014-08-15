<?php
/**
* @version $Id: mosemailcloak.php 4562 2006-08-18 23:26:32Z stingrey $
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

$_MAMBOTS->registerFunction( 'onPrepareContent', 'botMosEmailCloak' );

/**
* Сокрытие от спамботов адресов электронной почты в содержимом, используя javascript
*/
function botMosEmailCloak( $published, &$row, &$params, $page=0 ) {
	global $database, $_MAMBOTS;

	// check whether mambot has been unpublished
	if ( !$published ) {
		return true;
	}

	// simple performance check to determine whether bot should process further
	if ( strpos( $row->text, '@' ) === false ) {
		return true;
	}
	
	// simple check to allow disabling of bot
	$regex = '{emailcloak=off}';
	if ( strpos( $row->text, $regex ) !== false ) {
		$row->text = str_replace( $regex, '', $row->text );
		return true;
	}
	
	// check if param query has previously been processed
	if ( !isset($_MAMBOTS->_content_mambot_params['mosemailcloak']) ) {
	// загрузка информации о параметрах мамбота
		$query = "SELECT params"
	. "\n FROM #__mambots"
	. "\n WHERE element = 'mosemailcloak'"
	. "\n AND folder = 'content'"
	;
	$database->setQuery( $query );
		$database->loadObject($mambot);	
			
		// save query to class variable
		$_MAMBOTS->_content_mambot_params['mosemailcloak'] = $mambot;
	}
	
	// pull query data from class variable
	$mambot = $_MAMBOTS->_content_mambot_params['mosemailcloak'];
	
 	$botParams 	= new mosParameters( $mambot->params );
 	$mode		= $botParams->def( 'mode', 1 );

	// any@email.address.com
 	$search_email		= "([[:alnum:]_\.\-]+)(\@[[:alnum:]\.\-]+\.+)([[:alnum:]\.\-]+)";
	// any@email.address.com?subject=anyText
	$search_email_msg   = "([[:alnum:]_\.\-]+)(\@[[:alnum:]\.\-]+\.+)([[:alnum:]\.\-]+)([[:alnum:][:space:][:punct:]][^\"<>]+)";
	// anyText
 	$search_text 	= "([[:alnum:][:space:][:punct:]][^<>]+)";

	// поиск кода ссылок вида <a href="mailto:email@amail.com">email@amail.com</a>
	$pattern = botMosEmailCloak_searchPattern( $search_email, $search_email );
	while( eregi( $pattern, $row->text, $regs ) ) {		
		$mail 		= $regs[2] . $regs[3] . $regs[4];
		$mail_text 	= $regs[5] . $regs[6] . $regs[7];

		// проверка, отличается ли адрес почты от адреса почты в текстовом виде
		if ( $mail_text ) {
			$replacement 	= mosHTML::emailCloaking( $mail, $mode, $mail_text );
		} else {
			$replacement 	= mosHTML::emailCloaking( $mail, $mode );
		}

		// заменить найденный адрес e-mail замаскированным адресом
		$row->text 	= str_replace( $regs[0], $replacement, $row->text );
	}

	// search for derivativs of link code <a href="mailto:email@amail.com">anytext</a>
	$pattern = botMosEmailCloak_searchPattern( $search_email, $search_text );
	while( eregi( $pattern, $row->text, $regs ) ) {		
		$mail 		= $regs[2] . $regs[3] . $regs[4];
		$mail_text 	= $regs[5];

		$replacement 	= mosHTML::emailCloaking( $mail, $mode, $mail_text, 0 );

		// заменить найденный адрес e-mail замаскированным адресом
		$row->text 	= str_replace( $regs[0], $replacement, $row->text );
	}

	// search for derivativs of link code <a href="mailto:email@amail.com?subject=Text&body=Text">email@amail.com</a>
	$pattern = botMosEmailCloak_searchPattern( $search_email_msg, $search_email );
	while( eregi( $pattern, $row->text, $regs ) ) {		
		$mail		= $regs[2] . $regs[3] . $regs[4] . $regs[5];
		$mail_text	= $regs[6] . $regs[7]. $regs[8];
		//needed for handling of Body parameter
		$mail 		= str_replace( '&amp;', '&', $mail );

		// check to see if mail text is different from mail addy
		if ( $mail_text ) {
			$replacement = mosHTML::emailCloaking( $mail, $mode, $mail_text );
		} else {
			$replacement = mosHTML::emailCloaking( $mail, $mode );
		}
		
		// replace the found address with the js cloacked email
		$row->text     = str_replace( $regs[0], $replacement, $row->text );
	}
	
	// search for derivativs of link code <a href="mailto:email@amail.com?subject=Text&body=Text">anytext</a>
	$pattern = botMosEmailCloak_searchPattern( $search_email_msg, $search_text );
	while( eregi( $pattern, $row->text, $regs ) ) {		
		$mail		= $regs[2] . $regs[3] . $regs[4] . $regs[5];
		$mail_text	= $regs[6];
		//needed for handling of Body parameter
		$mail 		= str_replace( '&amp;', '&', $mail );
		
		$replacement = mosHTML::emailCloaking( $mail, $mode, $mail_text, 0 );
		
		// replace the found address with the js cloacked email
		$row->text     = str_replace( $regs[0], $replacement, $row->text );
	}
	
	// search for plain text email@amail.com
	while( eregi( $search_email, $row->text, $regs ) ) {
		$mail = $regs[0];

		$replacement = mosHTML::emailCloaking( $mail, $mode );

		// replace the found address with the js cloacked email
		$row->text = str_replace( $regs[0], $replacement, $row->text );
	}
}

function botMosEmailCloak_searchPattern ( $link, $text ) {	
	// <a href="mailto:anyLink">anyText</a>
	$pattern = "(<a [[:alnum:] _\"\'=\@\.\-]*href=[\"\']mailto:". $link	."[\"\'][[:alnum:] _\"\'=\@\.\-]*)>". $text ."</a>";
	
	return $pattern;
}
?>