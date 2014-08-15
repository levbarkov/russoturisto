<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my, $task, $id, $reg; 
require_once( site_path.'/component/ex/ex_lib.php' );

$cid = josGetArrayInts( 'cid' );
switch ($task) {
	case 'cancel':		$msg = 'Изменения не были сохранены: ';
						mosRedirect( 'index2.php?ca=shopcfg&task=cfg', $msg );
						break;
	case 'save':		saveshopcfg( $task );
						break;
	case 'removecfg':	$adminlog = new adminlog(); $adminlog->logme('delcfg', $reg['ex_name'], "", "" );
						load_adminclass('config'); $conf = new config($reg['db']); $conf->remove($_REQUEST['conf_values'], $_REQUEST['id']); 
						mosRedirect( 'index2.php?ca='.$reg['ca'].'&task=cfg', "Настройки удалены" );
						break;
	case 'cfg':			
	default:			cfgshopcfg( $option );
						break;
}
function cfgshopcfg(){
global $reg;
load_adminclass('config');	$conf = new config($reg['db']);
// ggtr5 ($conf);
$exgfg = ggo (1, "#__excfg");
?><form <? ctrlEnterCtrlAS (' '.$reg['submit_save_event'], ' '.$reg['submit_save_event']) ?> name="adminForm" action="index2.php" method="post"><input type="hidden"  name="iuse" id="iuse" value="0" />
		<table class="adminheading"><tr><td width="100%"><?
			$iway[0]->name=$reg['shop_name'];
			$iway[0]->url="index2.php?ca=excat";
			$iway[1]->name="настройка";
			$iway[1]->url="";

			i24pwprint_admin ($iway);
			?></td></tr></table>
<table border="0" cellpadding="4" cellspacing="0" width="100%" align="center">

	<tr class="workspace">
		<td><strong>СМС-уведомления: </strong></td>
		<td>- Для получения СМС о сделанном заказе необходимо подключить бесплатную услугу mail2sms. <a class="bright" href="http://man.krasinsite.ru/mail2sms?4ajax" target="_blank">Инструкция по подключению</a></td>
	</tr>
	<? itable_hr(2); ?>
	<tr class="workspace">
		<td>Название магазина: </td>
		<td><input name="exname" size="104" mosreq="1" moslabel="Название" value="<? print ($exgfg->exname); ?>" /></td>
	</tr>
	<tr class="workspace">
		<td>Заголовок письма: </td>
		<td colspan="2"><? editorArea( 'editor1',  ($exgfg->header_mail) , 'header_mail', '100%;', '50', '75', '5' ) ; ?></td>
	</tr>

	<tr class="workspace">
		<td>Благодарственный текст вконце письма: </td>
		<td colspan="2"><? editorArea( 'editor1',  ($exgfg->thanku_mail) , 'thanku_mail', '100%;', '50', '75', '5' ) ; ?></td>
	</tr>
	<tr class="workspace">
		<td>Благодарность после покупки: </td>
		<td colspan="2"><? editorArea( 'editor1',  ($exgfg->thanku) , 'thanku', '100%;', '50', '75', '5' ) ; ?></td>
	</tr>
	<tr class="workspace">
		<td>Адрес электронной почты<br />для уведомления о сделанных заказах: </td>
		<td><input name="order_mail_to" size="104" mosreq="1" moslabel="Название" value="<? print ($exgfg->order_mail_to); ?>" /></td>
	</tr>
	<tr class="workspace">
		<td>Адрес электронной почты<br />с которого будут отправляться письма клиентам: </td>
		<td><input name="order_mail_from" size="104" mosreq="1" moslabel="Название" value="<? print ($exgfg->order_mail_from); ?>" /></td>
	</tr>
	<tr class="workspace">
		<td>Название отправителя<br />(отправка почты клиентам): </td>
		<td><input name="order_mail_from_name" size="104" mosreq="1" moslabel="Название" value="<? print ($exgfg->order_mail_from_name); ?>" /></td>
	</tr>

	<tr class="workspace">
		<td>Тема письма<br />(отправка почты клиентам): </td>
		<td><input name="order_mail_subject" size="104" mosreq="1" moslabel="Название" value="<? print ($exgfg->order_mail_subject); ?>" /></td>
	</tr>

</table>

<?  
	$conf->show_config('shop', "<br />Дополнительные настройки") ?>
<input type="hidden" name="task" value="save"  />
<input type="hidden" name="ca" value="shopcfg" />
<input type="submit" style="display:none;" /></form><?
}
function saveshopcfg( $task ) {
	global $database, $my, $reg;
	$i24r = new mosDBTable( "#__excfg", "id", $database );
	$i24r->id = 1;
	$i24r->exname = safelySqlStr($_REQUEST['exname']);
	$i24r->header_mail = safelySqlStr($_REQUEST['header_mail']);
	$i24r->thanku_mail = safelySqlStr($_REQUEST['thanku_mail']);
	$i24r->thanku = safelySqlStr($_REQUEST['thanku']);
	$i24r->order_mail_to = safelySqlStr($_REQUEST['order_mail_to']);
	$i24r->order_mail_from = safelySqlStr($_REQUEST['order_mail_from']);
	$i24r->order_mail_from_name = safelySqlStr($_REQUEST['order_mail_from_name']);
	$i24r->order_mail_subject = safelySqlStr($_REQUEST['order_mail_subject']);

	if (!$i24r->check()) { echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n"; } else $i24r->store();
	load_adminclass('config');	$config = new config($reg['db']);	$config->save_config();
	
	$adminlog = new adminlog(); $adminlog->logme('cfg', $reg['ex_name'], "", "" );

	switch ( $task ) {
		case 'save':
		default:
			$msg = 'Изменения сохранены: ';	mosRedirect( 'index2.php?ca=shopcfg&task=cfg', $msg );	break;
	}
}


?>