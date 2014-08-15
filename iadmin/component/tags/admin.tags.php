<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my, $task, $id, $reg;
require_once( site_path.'/component/ad/ad_lib.php' );
$cid = josGetArrayInts( 'cid' );
switch ($task) {
	case 'cancel':		$msg = 'Изменения не были сохранены: ';
						mosRedirect( 'index2.php?ca='.$reg['ca'].'&task=cfg', $msg );
						break;
	case 'save':		savetagscfg( $task );
						break;
	case 'removecfg':	$adminlog = new adminlog(); $adminlog->logme('delcfg', 'Теги', "", "" );
						load_adminclass('config'); $conf = new config($reg['db']); $conf->remove($_REQUEST['conf_values'], $_REQUEST['id']); 
						mosRedirect( 'index2.php?ca='.$reg['ca'].'&task=cfg', "Настройки удалены" );
						break;
	case 'cfg':			
	default:			cfgtagscfg( $option );
						break;
}

function cfgtagscfg(){
global $option, $reg;
load_adminclass('config');	$conf = new config($reg['db']);

$exgfg = ggo (1, "#__excfg");
?><form name="adminForm" action="index2.php" method="post"><input type="hidden"  name="iuse" id="iuse" value="0" />
		<table class="adminheading"><tr><td width="100%"><?
			$iway[0]->name="Теги";
			$iway[0]->url="index2.php?ca=tags";
			$iway[1]->name="настройка";
			$iway[1]->url="";

			i24pwprint_admin ($iway);
			?></td></tr></table>
<table border="0" cellpadding="4" cellspacing="0" width="100%" align="center">
	<tr class="workspace">
		<td><strong>Внимание: </strong></td>
		<td>- Если некоторые объявления не открываются, значит необходимо <a class="bright" href="javascript: submitbutton('adrecalc'); ">пересчитать</a> их SEO-пути.</td>
	</tr>
	<tr class="workspace">
		<td></td>
		<td>- Если указано не верное количество объявлений в категориях - также необходимо выполнить функцию <a class="bright" href="javascript: submitbutton('adrecalc'); ">пересчитать</a>.</td>
	</tr>

</table>
<? $conf->show_config('tags', "<br />Дополнительные настройки") ?>
<input type="hidden" name="task" value="save"  />
<input type="hidden" name="ca" value="<?=$option ?>" />
<input type="submit" style="display:none;" /></form><?
}
function savetagscfg( $task ) {
	global $reg, $my;
	load_adminclass('config');	$config = new config($reg['db']); $config->save_config();
	$adminlog = new adminlog(); $adminlog->logme('cfg', 'Тэги', "", "" );
	switch ( $task ) {
		case 'save':
		default:
			$msg = 'Изменения сохранены: ';
			mosRedirect( 'index2.php?ca='.$reg['ca'].'&task=cfg', $msg );
			break;
	}
}


?>