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
	case 'save':		saveadcfg( $task );
						break;
	case 'removecfg':	$adminlog = new adminlog(); $adminlog->logme('delcfg', $reg['content_name'], "", "" );
						load_adminclass('config'); $conf = new config($reg['db']); $conf->remove($_REQUEST['conf_values'], $_REQUEST['id']); 
						mosRedirect( 'index2.php?ca='.$reg['ca'].'&task=cfg', "Настройки удалены" );
						break;
	case 'contentrecalc': contentrecalc();
						break;

	case 'cfg':			
	default:			cfgcontentcfg( $option );
						break;
}
function contentrecalc() { 
	global $reg, $option;

	$recalc = new seorecalc();
	$recalc->good_table	= "#__content";
	$recalc->cat_table = "#__icat";
	$recalc->good_parent_field = "catid";
	$recalc->cat_parent_field = "parent";
	$sefurl = ""; $recalc->recalc_req($sefurl, 0, 0);
	// необходимо пройтись по всем директориям рукурсией
	$msg = 'Пересчет SEO-путей и количества новостей/статей в рубриках завершен'; mosRedirect( 'index2.php?ca='.$reg['ca'].'&task=cfg', $msg );
	return ;
}
function cfgcontentcfg(){
global $option, $reg;
$exgfg = ggo (1, "#__excfg");
?><form name="adminForm" action="index2.php" method="post"><input type="hidden"  name="iuse" id="iuse" value="0" />
		<table class="adminheading"><tr><td width="100%"><?
			$iway[0]->name=$reg['content_name'];
			$iway[0]->url="index2.php?ca=contentcat";
			$iway[1]->name="настройка";
			$iway[1]->url="";

			i24pwprint_admin ($iway);
			?></td></tr></table>
<table border="0" cellpadding="4" cellspacing="0" width="100%" align="center">
	<tr class="workspace">
		<td><strong>Внимание: </strong></td>
		<td>- Если некоторые новости, статьи или рубрики не открываются, значит необходимо <a class="bright" href="javascript: submitbutton('contentrecalc'); ">пересчитать</a> их SEO-пути.</td>
	</tr>

</table>
<? load_adminclass('config');	$conf = new config($reg['db']);
$conf->show_config('icontent', "<br />Дополнительные настройки") ?>
<input type="hidden" name="task" value="save"  />
<input type="hidden" name="ca" value="<?=$option ?>" />
<input type="submit" style="display:none;" /></form><?
}
function saveadcfg( $task ) {
	global $reg, $my;

	load_adminclass('config');	$config = new config($reg['db']); $config->save_config();
	
	$adminlog = new adminlog(); $adminlog->logme('cfg', $reg['content_name'], "", "" );

	switch ( $task ) {
		case 'save':
		default:
			$msg = 'Изменения сохранены: ';	mosRedirect( 'index2.php?ca='.$reg['ca'].'&task=cfg', $msg );	break;
	}
}


?>