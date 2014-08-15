<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my, $task, $id, $reg; 
require_once( site_path.'/component/ex/ex_lib.php' );

$cid = josGetArrayInts( 'cid' );
switch ($task) {
	case 'cancel':		$msg = 'Изменения не были сохранены: ';
						mosRedirect( 'index2.php?ca=excfg&task=cfg', $msg );
						break;
	case 'save':		saveexcfg( $task );
						break;
	case 'removecfg':	$adminlog = new adminlog(); $adminlog->logme('delcfg', $reg['ex_name'], "", "" );
						load_adminclass('config'); $conf = new config($reg['db']); $conf->remove($_REQUEST['conf_values'], $_REQUEST['id']); 
						mosRedirect( 'index2.php?ca='.$reg['ca'].'&task=cfg', "Настройки удалены" );
						break;

	case 'exrecalc':	exrecalc();
						break;
	case 'cfg':			
	default:			cfgexcfg( $option );
						break;
}
function exrecalc() { 
	global $reg; 
	// необходимо пройтись по всем директориям рукурсией
	$recalc = new seorecalc();
	$recalc->good_table	= "#__exgood";
	$recalc->cat_table = "#__excat";
	$recalc->good_parent_field = "parent";
	$recalc->cat_parent_field = "parent";
	$sefurl = "/".$reg['ex_seoname']; $recalc->recalc_req($sefurl, 0, 0);
	
//	$sefurl = "/".$reg['ex_seoname']; exrecalc_req($sefurl, 0, 0);
	$msg = 'Пересчет SEO-путей и количества товаров в категориях завершен'; mosRedirect( 'index2.php?ca='.$reg['ca'].'&task=cfg', $msg );
	return ;
}

function cfgexcfg(){
global $reg;
load_adminclass('config');	$conf = new config($reg['db']);
// ggtr5 ($conf);
$exgfg = ggo (1, "#__excfg");
?><form <? ctrlEnterCtrlAS (' '.$reg['submit_save_event'], ' '.$reg['submit_save_event']) ?> name="adminForm" action="index2.php" method="post"><input type="hidden"  name="iuse" id="iuse" value="0" />
		<table class="adminheading"><tr><td width="100%"><?
			$iway[0]->name=$reg['ex_name'];
			$iway[0]->url="index2.php?ca=excat";
			$iway[1]->name="настройка";
			$iway[1]->url="";

			i24pwprint_admin ($iway);
			?></td></tr></table>
<table border="0" cellpadding="4" cellspacing="0" width="100%" align="center">
	<tr class="workspace">
		<td><strong>Внимание: </strong></td>
		<td>- Если некоторые товары не открываются, значит необходимо <a class="bright" href="javascript: submitbutton('exrecalc'); ">пересчитать</a> их SEO-пути.</td>
	</tr>
	<tr class="workspace">
		<td></td>
		<td>- Если указано не верное количество товаров в категориях - также необходимо выполнить функцию <a class="bright" href="javascript: submitbutton('exrecalc'); ">пересчитать</a>.</td>
	</tr>
</table>

<?  
	$conf->show_config('ex', "<br />Дополнительные настройки") ?>
<input type="hidden" name="task" value="save"  />
<input type="hidden" name="ca" value="excfg" />
<input type="submit" style="display:none;" /></form><?
}
function saveexcfg( $task ) {
	global $database, $my, $reg;
/*	$i24r = new mosDBTable( "#__excfg", "id", $database );
	$i24r->id = 1;
	$i24r->exname = safelySqlStr($_REQUEST['exname']);
	$i24r->thanku_mail = safelySqlStr($_REQUEST['thanku_mail']);
	$i24r->thanku = safelySqlStr($_REQUEST['thanku']);
	$i24r->order_mail_to = safelySqlStr($_REQUEST['order_mail_to']);
	$i24r->order_mail_from = safelySqlStr($_REQUEST['order_mail_from']);
	$i24r->order_mail_from_name = safelySqlStr($_REQUEST['order_mail_from_name']);
	$i24r->order_mail_subject = safelySqlStr($_REQUEST['order_mail_subject']);

	if (!$i24r->check()) { echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n"; } else $i24r->store();   */
	load_adminclass('config');	$config = new config($reg['db']);	$config->save_config();
	
	$adminlog = new adminlog(); $adminlog->logme('cfg', $reg['ex_name'], "", "" );

	switch ( $task ) {
		case 'save':
		default:
			$msg = 'Изменения сохранены: ';	mosRedirect( 'index2.php?ca=excfg&task=cfg', $msg );	break;
	}
}


?>