<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my, $task, $id, $reg;
require_once( site_path.'/component/ad/ad_lib.php' );

$cid = josGetArrayInts( 'cid' );
switch ($task) {
	case 'cancel':		$msg = 'Изменения не были сохранены: ';
						mosRedirect( 'index2.php?ca=adcfg&task=cfg', $msg );
						break;
	case 'save':		saveadcfg( $task );
						break;
	case 'removecfg':	$adminlog = new adminlog(); $adminlog->logme('delcfg', $reg['ad_name'], "", "" );
						load_adminclass('config'); $conf = new config($reg['db']); $conf->remove($_REQUEST['conf_values'], $_REQUEST['id']); 
						mosRedirect( 'index2.php?ca='.$reg['ca'].'&task=cfg', "Настройки удалены" );
						break;
	case 'adrecalc':	adrecalc();
						break;
	case 'cfg':			
	default:			cfgadcfg( $option );
						break;
}
function adrecalc() { 
	global $reg, $option;
	// необходимо пройтись по всем директориям рукурсией
	$recalc = new seorecalc();
	$recalc->good_table	= "#__adgood";
	$recalc->cat_table = "#__adcat";
	$recalc->good_parent_field = "parent";
	$recalc->cat_parent_field = "parent";
	$sefurl = "/".$reg['ad_seoname']; $recalc->recalc_req($sefurl, 0, 0);

	//$sefurl = "/".$reg['ad_seoname']; adrecalc_req($sefurl, 0, 0);
	$msg = 'Пересчет SEO-путей и количества объявлений в категориях завершен'; mosRedirect( 'index2.php?ca='.$option.'&task=cfg', $msg );
	return ;
}
function cfgadcfg(){
global $option, $reg;
load_adminclass('config');	$conf = new config($reg['db']);

$exgfg = ggo (1, "#__excfg");
?><form name="adminForm" action="index2.php" method="post"><input type="hidden"  name="iuse" id="iuse" value="0" />
		<table class="adminheading"><tr><td width="100%"><?
			$iway[0]->name=$reg['ad_name'];
			$iway[0]->url="index2.php?ca=adcat";
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
<? $conf->show_config('ad', "<br />Дополнительные настройки") ?>
<input type="hidden" name="task" value="save"  />
<input type="hidden" name="ca" value="<?=$option ?>" />
<input type="submit" style="display:none;" /></form><?
}
function saveadcfg( $task ) {
	global $reg, $my;
	$i24r = new mosDBTable( "#__adcfg", "id", $reg['db'] );
	$i24r->id = 1;
	$i24r->exname = ($_REQUEST['exname']);
	$i24r->thanku_mail = ($_REQUEST['thanku_mail']);
	$i24r->thanku = ($_REQUEST['thanku']);
	$i24r->order_mail_to = ($_REQUEST['order_mail_to']);
	$i24r->order_mail_from = ($_REQUEST['order_mail_from']);
	$i24r->order_mail_from_name = ($_REQUEST['order_mail_from_name']);
	$i24r->order_mail_subject = ($_REQUEST['order_mail_subject']);

	if (!$i24r->check()) { echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n"; } else $i24r->store();
	load_adminclass('config');	$config = new config($reg['db']); $config->save_config();
	
	$adminlog = new adminlog(); $adminlog->logme('cfg', $reg['ad_name'], "", "" );

	switch ( $task ) {
		case 'save':
		default:
			$msg = 'Изменения сохранены: ';
			mosRedirect( 'index2.php?ca=adcfg&task=cfg', $msg );
			break;
	}
}


?>