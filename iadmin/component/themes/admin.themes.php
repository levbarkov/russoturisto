<?php
//ggd($_REQUEST);
// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my, $mosConfig_absolute_path, $mainframe, $task, $reg;

if (  $my->gid<24  ) {
	mosRedirect( 'index2.php', _NOT_AUTH );
}
require_once( $mainframe->getPath( 'admin_html' ) );
require_once( $mosConfig_absolute_path .'/iadmin/component/themes/admin.themes.class.php' );
// XML library
require_once( $mosConfig_absolute_path .'/includes/domit/xml_domit_lite_include.php' );

$client = strval( mosGetParam( $_REQUEST, 'client', '' ) );

$cid 	= mosGetParam( $_REQUEST, 'cid', array(0) );
if (!is_array( $cid )) {
	$cid = array(0);
}
if (get_magic_quotes_gpc()) {
	$cid[0] = stripslashes( $cid[0] );
}

switch ($task) {
	case 'new':
		mosRedirect ( 'index2.php?ca=com_installer&element=template&client='. $client );
		break;

	case 'edit_source':
		editTemplateSource( $cid[0], $option, $client );
		break;

	case 'save_source':
		saveTemplateSource( $option, $client );
		break;

	case 'edit_css':
		editTemplateCSS( $cid[0], $option, $client );
		break;

	case 'save_css':
		saveTemplateCSS( $option, $client );
		break;

	case 'remove':
		removeTemplate( $cid[0], $option, $client );
		break;

	case 'publish':
		defaultTemplate( $cid[0], $option, $client );
		break;

	case 'default':
		defaultTemplate( $cid[0], $option, $client );
		break;

	case 'assign':
		assignTemplate( $cid[0], $option, $client );
		break;

	case 'save_assign':
		saveTemplateAssign( $option, $client );
		break;

	case 'cancel':
		mosRedirect( 'index2.php?ca='. $option .'&client='. $client );
		break;

	case 'positions':
		editPositions( $option );
		break;

	case 'save_positions':
		savePositions( $option );
		break;
	case 'savecfg':		$conf = new template_config($reg['db']);   $conf->save_config();	$adminlog = new adminlog(); $adminlog->logme('cfg', 'Управление шаблонами', "", "" );
						mosRedirect( 'index2.php?ca='.$reg['ca'], "Настройки сохранены" );
						break;
	case 'removecfg':	$adminlog = new adminlog(); $adminlog->logme('del_themecfg', 'Управление шаблонами', "", "" );
						$conf = new template_config($reg['db']); $conf->remove($_REQUEST['conf_values'], $_REQUEST['id']); 
						mosRedirect( 'index2.php?ca='.$reg['ca'], "Настройки удалены" );
						break;

	default:
		viewTemplates( $option, $client );
		break;
}

function viewTemplates( $option, $client ) {
global $reg;
//load_adminclass('config');
?><form <? ctrlEnterCtrlAS (' '.$reg['submit_save_event'], ' '.$reg['submit_save_event']) ?> name="adminForm" action="index2.php" method="post"><input type="hidden"  name="iuse" id="iuse" value="0" />
		<table class="adminheading"><tr><td width="100%"><?
			$iway[0]->name="Управление шаблонами";
			$iway[0]->url="";
			i24pwprint_admin ($iway);
		?></td></tr></table>
<table border="0" cellpadding="4" cellspacing="0" width="100%" align="center">
	<tr class="workspace">
		<td><strong>Внимание: </strong></td>
		<td>- тип записи "Регулярное выражение для url" - вы можете прописать любое регулярное выражение для адресной строки и если оно выполнится, то автоматически загрузится данный шаблон.<br />
пример: ^\/catalogue\/auto\/subaru007 - загрузка другого шаблона для категории "auto/subaru007" каталога товаров </td>
	</tr>
	<tr class="workspace">
		<td></td>
		<td>- Если стандартных методов назначения шаблона не хватает, то можно указать php-файл, по завершению он определяет переменную $ext_fileresult, если true - выбираем шаблон, false - идем дальше.<br />
Все файлы расположены в директории /theme/theme_extfiles/</td>
	</tr>
</table>
<? $template_config = new template_config($reg['db']);  $template_config->show("<br />Список шаблонов сайта и их настройки") ?>
<input type="hidden" name="task" value="savecfg"  />
<input type="hidden" name="ca" value="<?=$reg['ca'] ?>" />
<input type="submit" style="display:none;" /></form><?
}


/**
* Publish, or make current, the selected template
*/
function defaultTemplate( $p_tname, $option, $client ) {
	global $database;

	if ($client=='admin') {
		$query = "DELETE FROM #__templates_menu"
		. "\n WHERE def = 1"
		. "\n AND pi = 0"
		;
		$database->setQuery( $query );
		$database->query();

		$query = "INSERT INTO #__templates_menu"
		. "\n SET def = 1, template = " . $database->Quote( $p_tname ) . ", pi = 0"
		;
		$database->setQuery( $query );
		$database->query();
	} else {
		$query = "DELETE FROM #__templates_menu"
		. "\n WHERE def = 0"
		. "\n AND pi = 0"
		;
		$database->setQuery( $query );
		$database->query();

		$query = "INSERT INTO #__theme_menu"
		. "\n SET def = 0, theme = " . $database->Quote( $p_tname ) . ", pi = 0"
		;
		$database->setQuery( $query );
		$database->query();

		$_SESSION['cur_template'] = $p_tname;
	}

	mosRedirect('index2.php?ca='. $option .'&client='. $client);
}

/**
* Remove the selected template
*/
function removeTemplate( $cid, $option, $client ) {
	global $database;

	$client_id = $client=='admin' ? 1 : 0;

	$query = "SELECT template"
	. "\n FROM #__templates_menu"
	. "\n WHERE def = " . (int) $client_id
	. "\n AND pi = 0"
	;
	$database->setQuery( $query );
	$cur_template = $database->loadResult();

	if ($cur_template == $cid) {
		mosErrorAlert( "Этот шаблон используется и не может быть удален" );
	}

	// Un-assign
	$query = "DELETE FROM #__templates_menu"
	. "\n WHERE template = " . $database->Quote( $cid )
	. "\n AND def = " . (int) $client_id
	. "\n AND pi != 0"
	;
	$database->setQuery( $query );
	$database->query();

	mosRedirect( 'index2.php?ca=com_installer&element=template&client='. $client .'&task=remove&cid[]='. $cid );
}

function editTemplateSource( $p_tname, $option, $client ) {
	global $mosConfig_absolute_path;

	if ( $client == 'admin' ) {
		$file = $mosConfig_absolute_path .'/iadministrator/templates/'. $p_tname .'/index.php';
	} else {
		$file = $mosConfig_absolute_path .'/templates/'. $p_tname .'/index.php';
	}

	if ( $fp = fopen( $file, 'r' ) ) {
		$content = fread( $fp, filesize( $file ) );
		$content = htmlspecialchars( $content );

		HTML_templates::editTemplateSource( $p_tname, $content, $option, $client );
	} else {
		mosRedirect( 'index2.php?ca='. $option .'&client='. $client, 'Операция неудачна: невозможно открыть '. $file );
	}
}


function saveTemplateSource( $option, $client ) {
	global $mosConfig_absolute_path;

	$template 		= strval( mosGetParam( $_POST, 'template', '' ) );
	$filecontent 	= mosGetParam( $_POST, 'filecontent', '', _MOS_ALLOWHTML );

	if ( !$template ) {
		mosRedirect( 'index2.php?ca='. $option .'&client='. $client, 'Операция неудачна: Не определен шаблон.' );
	}
	if ( !$filecontent ) {
		mosRedirect( 'index2.php?ca='. $option .'&client='. $client, 'Операция неудачна: Пустое содержимое.' );
	}

	if ( $client == 'admin' ) {
		$file = $mosConfig_absolute_path .'/administrator/templates/'. $template .'/index.php';
	} else {
		$file = $mosConfig_absolute_path .'/templates/'. $template .'/index.php';
	}

	$enable_write = mosGetParam($_POST,'enable_write',0);
	$oldperms = fileperms($file);
														  
	if ($enable_write) @chmod($file, $oldperms | 0222);

	clearstatcache();
	if ( is_writable( $file ) == false ) {
		mosRedirect( 'index2.php?ca='. $option , 'Операция неудачна: '. $file .' недоступен для записи.' );
	}

	if ( $fp = fopen ($file, 'w' ) ) {
		fputs( $fp, stripslashes( $filecontent ), strlen( $filecontent ) );
		fclose( $fp );
		if ($enable_write) {
			@chmod($file, $oldperms);
		} else {
			if (mosGetParam($_POST,'disable_write',0))
				@chmod($file, $oldperms & 0777555);
		} // if
		mosRedirect( 'index2.php?ca='. $option .'&client='. $client );
	} else {
		if ($enable_write) @chmod($file, $oldperms);
		mosRedirect( 'index2.php?ca='. $option .'&client='. $client, 'Операция неудачна: Ошибка открытия файла для записи.' );
	}

}

function editTemplateCSS( $p_tname, $option, $client ) {
	global $mosConfig_absolute_path;

	if ( $client == 'admin' ) {
		$file = $mosConfig_absolute_path .'/administrator/templates/'. $p_tname .'/css/template_css.css';
	} else {
		$file = $mosConfig_absolute_path .'/templates/'. $p_tname .'/css/template_css.css';
	}

	if ($fp = fopen( $file, 'r' )) {
		$content = fread( $fp, filesize( $file ) );
		$content = htmlspecialchars( $content );

		HTML_templates::editCSSSource( $p_tname, $content, $option, $client );
	} else {
		mosRedirect( 'index2.php?ca='. $option .'&client='. $client, 'Операция неудачна: невозможно открыть '. $file );
	}
}


function saveTemplateCSS( $option, $client ) {
	global $mosConfig_absolute_path;
	
	$template 		= strval( mosGetParam( $_POST, 'template', '' ) );
	$filecontent = mosGetParam( $_POST, 'filecontent', '', _MOS_ALLOWHTML );

	if ( !$template ) {
		mosRedirect( 'index2.php?ca='. $option .'&client='. $client, 'Операция неудачна: Не определен шаблон.' );
	}

	if ( !$filecontent ) {
		mosRedirect( 'index2.php?ca='. $option .'&client='. $client, 'Операция неудачна: Пустое содержимое.' );
	}

	if ( $client == 'admin' ) {
		$file = $mosConfig_absolute_path .'/administrator/templates/'. $template .'/css/template_css.css';
	} else {
		$file = $mosConfig_absolute_path .'/templates/'. $template .'/css/template_css.css';
	}

	$enable_write = mosGetParam($_POST,'enable_write',0);
	$oldperms = fileperms($file);
	
	if ($enable_write) {
		@chmod($file, $oldperms | 0222);
	}

	clearstatcache();
	if ( is_writable( $file ) == false ) {
		mosRedirect( 'index2.php?ca='. $option .'&client='. $client, 'Операция неудачна: Файл недоступен для записи.' );
	}

	if ($fp = fopen ($file, 'w')) {
		fputs( $fp, stripslashes( $filecontent ) );
		fclose( $fp );
		if ($enable_write) {
			@chmod($file, $oldperms);
		} else {
			if (mosGetParam($_POST,'disable_write',0))
				@chmod($file, $oldperms & 0777555);
		} // if
		mosRedirect( 'index2.php?ca='. $option );
	} else {
		if ($enable_write) @chmod($file, $oldperms);
		mosRedirect( 'index2.php?ca='. $option .'&client='. $client, 'Операция неудачна: Ошибка открытия файла для записи.' );
	}

}


function assignTemplate( $p_tname, $option, $client ) {
	global $database;
	// get selected pages for $menulist
	if ( $p_tname ) {

		$query = "SELECT pi AS value"
		. "\n FROM #__theme_menu"
		. "\n WHERE def = 0"
		. "\n AND theme = " . $database->Quote( $p_tname )
		;
		$database->setQuery( $query );
		$lookup = $database->loadObjectList();
	}

	// build the html select list
	$menulist = mosAdminMenus::MenuLinks( $lookup, 0, 1 );

	HTML_templates::assignTemplate( $p_tname, $menulist, $option, $client );
}


function saveTemplateAssign( $option, $client ) {
	global $database;

	$menus 		= josGetArrayInts( 'selections' );
	
	$template 	= stripslashes( strval( mosGetParam( $_POST, 'template', '' ) ) );

	$query = "DELETE FROM #__theme_menu"
	. "\n WHERE def = 0"
	. "\n AND theme = " . $database->Quote( $template )
	. "\n AND pi != 0"
	;
	$database->setQuery( $query );
	$database->query();

	if ( !in_array( '', $menus ) ) {
		foreach ( $menus as $menuid ){
			$menuid = (int) $menuid;

			// If 'None' is not in array
			if ( $menuid != -999 ) {
				// check if there is already a template assigned to this menu item
				$query = "DELETE FROM #__theme_menu"
				. "\n WHERE def = 0"
				. "\n AND pi = " . (int) $menuid
				;
				$database->setQuery( $query );
				$database->query();

				$query = "INSERT INTO #__theme_menu"
				. "\n SET def = 0, theme = " . $database->Quote( $template ) . ", pi = " . (int) $menuid
				;
				$database->setQuery( $query );
				$database->query();
			}
		}
	}

	mosRedirect( 'index2.php?ca='. $option .'&client='. $client );
}


/**
*/
function editPositions( $option ) {
	global $database;

	$query = "SELECT *"
	. "\n FROM #__template_positions"
	;
	$database->setQuery( $query );
	$positions = $database->loadObjectList();

	HTML_templates::editPositions( $positions, $option );
}

/**
*/
function savePositions( $option ) {
	global $database;

	$positions 		= mosGetParam( $_POST, 'position', array() );
	$descriptions 	= mosGetParam( $_POST, 'description', array() );

	$query = "DELETE FROM #__template_positions";
	$database->setQuery( $query );
	$database->query();

	foreach ($positions as $id=>$position) {
		$position 		= trim( $position );
		if (get_magic_quotes_gpc()) {
			$position = stripslashes( $position );
		}
		$description 	= stripslashes( strval( mosGetParam( $descriptions, $id, '' ) ) );
		if ($position != '') {
			$query = "INSERT INTO #__template_positions"
			. "\n VALUES ( " . (int) $id . ", " . $database->Quote( $position ) . ", " . $database->Quote( $description ) . " )"
			;
			$database->setQuery( $query );
			$database->query();
		}
	}
	mosRedirect( 'index2.php?ca='. $option .'&task=positions', 'Позиции сохранены' );
}
?>