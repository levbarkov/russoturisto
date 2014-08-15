<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my, $task, $id, $reg;
$cid = josGetArrayInts( 'cid' );
switch ($task) {
	case 'cancel':		$msg = 'Изменения не были сохранены: ';
						mosRedirect( 'index2.php?ca=excfg&task=cfg', $msg );
						break;
	case 'save':		saveexcfg( $task );
						break;
	case 'removecfg':	$adminlog = new adminlog(); $adminlog->logme('delcfg', $reg['backlink_name'], "", "" );
						load_adminclass('config'); $conf = new config($reg['db']); $conf->remove($_REQUEST['conf_values'], $_REQUEST['id']); 
						mosRedirect( 'index2.php?ca='.$reg['ca'].'&task=cfg', "Настройки удалены" );
						break;
	case 'cfg':			
	default:			cfgexcfg( $option );
						break;
}

function cfgexcfg(){
global $reg;
$exgfg = ggo (1, "#__backlinkcfg");
?>
<style>.conf_vals {border: 0px;background: none;width: 280px;}</style>
<form <? ctrlEnterCtrlAS (' '.$reg['submit_apply_event'], ' '.$reg['submit_save_event']) ?> name="adminForm" action="index2.php" method="post"><input type="hidden"  name="iuse" id="iuse" value="0" />
<table class="adminheading"><tr><td class="edit"><?
			$iway[0]->name=$reg['backlink_name'];
			$iway[0]->url="";
			$iway[1]->name= 'Редактирование параметров';
			$iway[1]->url="";

			i24pwprint_admin ($iway,0);
?>
</tв></tr></table>

<table border="0" cellpadding="4" cellspacing="0" width="100%" align="center">
	<tr class="workspace">
		<td>Пояснительный текст в почтовой форме: </td>
		<td ><? editorArea( 'editor1',  ($exgfg->intro) , 'intro', '100%;', '50', '75', '5' ) ; ?></td>
	</tr>
	<tr class="workspace">
		<td>Благодарственный текст за отправленное письмо: </td>
		<td><input name="thanku" size="104" mosreq="1" moslabel="Название" value="<? print ($exgfg->thanku); ?>" /></td>
	</tr>
	<tr class="workspace">
		<td>Адрес электронной почты<br />для уведомления о сделанных заказах: </td>
		<td><input name="order_mail_to" size="104" mosreq="1" moslabel="Название" value="<? print ($exgfg->order_mail_to); ?>" /></td>
	</tr>
	<tr class="workspace">
		<td>Адрес электронной почты<br />с которого будут отправляться письмо: </td>
		<td><input name="order_mail_from" size="104" mosreq="1" moslabel="Название" value="<? print ($exgfg->order_mail_from); ?>" /></td>
	</tr>
	<tr class="workspace">
		<td>Название отправителя: </td>
		<td><input name="order_mail_from_name" size="104" mosreq="1" moslabel="Название" value="<? print ($exgfg->order_mail_from_name); ?>" /></td>
	</tr>

	<tr class="workspace">
		<td>Тема письма: </td>
		<td><input name="order_mail_subject" size="104" mosreq="1" moslabel="Название" value="<? print ($exgfg->order_mail_subject); ?>" /></td>
	</tr>
	<tr class="workspace">
		<td>Отправлять копию письма пользователю: </td>
		<td><input type="checkbox" name="do_make_copy" <? if (  $exgfg->do_make_copy==1 ) print 'checked="checked"'; ?> /></td>
	</tr>
	<tr class="workspace">
		<td>Тема копии письма: </td>
		<td><input name="copy_order_mail_subject" size="104" mosreq="1" moslabel="Название" value="<? print ($exgfg->copy_order_mail_subject); ?>" /></td>
	</tr>
	<tr class="workspace">
		<td>Примечание в конце копии письма: </td>
		<td><input name="copy_note" size="104" mosreq="1" moslabel="Название" value="<? print ($exgfg->copy_note); ?>" /></td>
	</tr>
</table>
<? 
load_adminclass('config');	$conf = new config($reg['db']);
$conf->show_config('backlink', "<br />Дополнительные настройки") ?>
<input type="hidden" name="task" value="save"  />
<input type="hidden" name="ca" value="backlinkcfg" />
</form><?
}
function saveexcfg( $task ) {
	global $database, $my, $reg;
	$i24r = new mosDBTable( "#__backlinkcfg", "id", $database );
	$i24r->id = 1;
	$i24r->intro = ($_REQUEST['intro']);
	$i24r->thanku = ($_REQUEST['thanku']);
	$i24r->order_mail_to = ($_REQUEST['order_mail_to']);
	$i24r->order_mail_from = ($_REQUEST['order_mail_from']);
	$i24r->order_mail_from_name = ($_REQUEST['order_mail_from_name']);
	$i24r->order_mail_subject = ($_REQUEST['order_mail_subject']);
	$i24r->do_make_copy = isset($_REQUEST['do_make_copy']) ? 1 : 0;
	$i24r->copy_order_mail_subject = ($_REQUEST['copy_order_mail_subject']);
	$i24r->copy_note = ($_REQUEST['copy_note']);
	
	if (!$i24r->check()) { echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n"; } else $i24r->store();
	load_adminclass('config');	 $conf = new config($reg['db']);   $conf->save_config(); 
	
	$adminlog = new adminlog(); $adminlog->logme('cfg', $reg['backlink_name'], "", "" );
	
	switch ( $task ) {
		case 'save':
		default:
			$msg = 'Изменения сохранены: ';  mosRedirect( 'index2.php?ca=backlinkcfg&task=cfg', $msg ); break;
	}
}


?>