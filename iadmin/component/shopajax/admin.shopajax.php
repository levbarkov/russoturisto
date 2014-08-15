<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my, $reg, $id;
require_once(site_path."/lib/saver.php");
$cid = josGetArrayInts( 'cid' );
require_once( site_path.'/component/ex/ex_lib.php' );

if (  $reg['task']==''  ) return;
$function_name = $reg['task'];
if(function_exists($function_name)) $function_name();
else print "Такая функция не найдена";

function renewManagerList(&$so)
{
      $man = "";
      if(is_array($so->managers))
      {
	    foreach($so->managers as $manager)
	    {
		$man .= "<a href=\"javascript: ins_ajax_load_target ('ca=shopajax&task=remmanager&orderid=".$so->id."&manager_id=".$manager."&4ajax=1', '#shop_".$so->id."'); void(0);\" ><img align='top' src='/iadmin/images/del.png' border='0' /></a>&nbsp;&nbsp;<a href='/iadmin/index2.php?ca=users&task=editA&id=".$manager."&hidemainmenu=1' target='_blank' alt='Перейти к редактированию пользователя'>". $so->getUserName($manager). "&nbsp;&nbsp;(".$so->getManagerOrders($manager).")</a> <br />";
	    }
      }
      return $man;
}

function manager2order( ) {
      global $_REQUEST, $reg;      
      $so = new shopOrder($reg);
      $so->load($_REQUEST['orderid']);
      if(!isset($so->id ) || $so->id == 0) return false;
      $so->addManager(ggri('manager_id'));
	  /*
	   * УВЕДОМЛЕНИЕ ПО СМС
	   */
	   if (  $reg['shop_note_sms_enable']==1  ){
		   $manager = ggo (ggri('manager_id'), "#__users");
		   if (  $manager->note_sms_tel2!=''  and  $manager->note_sms_enable==1  ){
				$order_text = short_surl().", Вам поступил новый заказ #".$so->id;
				
				$mail2sms = new mail2sms();
				$mail2sms->tel = $manager->note_sms_tel1.$manager->note_sms_tel2;
				$mail2sms->tel = preg_replace("/[- ]/", "", $mail2sms->tel);
				$mail2sms->oper = $manager->note_sms_oper;
				$mail2sms->text = $order_text;
				$mail2sms->sendSms();
		   }
	   }
      print renewManagerList($so);
      
}

function remmanager()
{
      global $_REQUEST, $reg;      
      $so = new shopOrder($reg);
      $so->load($_REQUEST['orderid']);
      if(!isset($so->id ) || $so->id == 0) return false;
      $so->removeManager($_REQUEST['manager_id']);
      print renewManagerList($so);
}
function newmanager( ) {
	global $reg;      
	$db = $reg['db'];
	$db->setQuery("select * from #__users where gid>19 and block=0");
  	$all_managers = $db->loadObjectList();

	$orderid = intval($_REQUEST['orderid']);
	//$db->setQuery("SELECT manager_id FROM #__ordermanagers WHERE"))
	$db->setQuery("SELECT uid from #__orders WHERE id = ".$orderid);
	$uid = $db->loadResult();
	$res = 0;

        /*
         * ОПРЕДЕЛЯЕМ ЛЮБИМОГО МЕНЕДЖЕРА (т.е. Если клиент совершает не первую покупку - предыдущего менеджера)
         */
	if($uid != 0){
                $db->setQuery("SELECT * FROM #__ordermanagers as om 
                               left join #__orders as o on (om.order_id = o.id)
                               WHERE o.uid = ".$uid." order by o.id DESC LIMIT 0,1");
		list($res) = $db->loadObjectList();
		$res = $res->manager_id;
	}
	
	print "Выберите менеджера: ";
	?><table > 
		<tr ><td>&nbsp;</td></tr>
		<?
		foreach ($all_managers as $recgood){
			$webuser_type= (  $recgood->gid == 25  ||  $recgood->gid == 24  ) ? "admin" : "manager" ;
			if (  $recgood->small  )  $img_src = "/images/cab/logo/".$recgood->small;
			else $img_src = "/iadmin/images/userbig.png";
			
			?><tr>
				<td style="padding-bottom:4px; padding-top:3px; border-bottom:1px dotted #787878;"><a href="  javascript:    $('#shop_<?=$_REQUEST['orderid'] ?>').append('<img src=<?=site_url ?>/iadmin/images/loading16.gif width=16 height=16 align=absmiddle /> Сохранение...');  ins_ajax_load_target ('ca=shopajax&task=manager2order&orderid=<?=$_REQUEST['orderid'] ?>&manager_id=<?=$recgood->id ?>&4ajax=1', '#shop_<?=$_REQUEST['orderid'] ?>'); void(0); $.fn.colorbox.close(); " class="nohover" ><img src='<?=$img_src ?>' border='0' align="left" style="padding-right:14px;" /></a><?
                                                                                                                 ?><a href="  javascript:    $('#shop_<?=$_REQUEST['orderid'] ?>').append('<img src=<?=site_url ?>/iadmin/images/loading16.gif width=16 height=16 align=absmiddle /> Сохранение...');  ins_ajax_load_target ('ca=shopajax&task=manager2order&orderid=<?=$_REQUEST['orderid'] ?>&manager_id=<?=$recgood->id ?>&4ajax=1', '#shop_<?=$_REQUEST['orderid'] ?>'); void(0); $.fn.colorbox.close(); " ><span style="font-size:16px"><?=desafelySqlStr($recgood->name.' '.$recgood->userparentname); ?></span><br /><?=$webuser_type ?></a><br />
				<? if($recgood->id == $res){ ?><img src="/iadmin/images/package_favorite.png" align="middle" />&nbsp;&nbsp;вел&nbsp;прошлый&nbsp;заказ<? } ?></td>
			</tr><?
		}
      ?></table><?
}

/** Окошко с предметами для заказа */
function showorderitems()
{
  global $_REQUEST, $reg;
  $id = intval($_REQUEST['orderid']);
  $so = new shopOrder($reg);
  $so->load($id);
  if(!isset($so->id ) || $so->id == 0) return false;
  $price = $so->recalcPrice();
  $expaymethod = ggo($so->payment_type, '#__expaymethod');
  if(count($so->items) > 0){
	  ?><table border = "0" cellspacing="5" cellpadding="1" class="showorderitems" >
	  <tr>
		<th nowrap="nowrap">Название</th>
		<th nowrap="nowrap">Количество</th>
		<th nowrap="nowrap">Цена за ед.</th>
		<th nowrap="nowrap">Общая цена</th>
	  </tr><?
	  foreach($so->items as $item)
	  {
	  ?><tr>
	  	<td><?=$item->name; ?> <?=$item->pack_name; ?></td>
		<td><?=$item->quantity; ?></td>
		<td style="white-space:nowrap;" nowrap="nowrap"><?=$item->price_offer; ?> <small>руб.</small></td>
		<td style="white-space:nowrap;" nowrap="nowrap"><?=$item->price_offer * $item->quantity; ?>  <small>руб.</small></td>
	  </tr><?
	  }  
	  ?>
	  <tr class="ex_trush_row_total"><?
		?><td></td><?
		?><td></td><?
		?><td style="text-align:left; white-space:nowrap;" nowrap="nowrap"><strong>Общая сумма:</strong></td><?
		?><td style="text-align:left; white-space:nowrap;" nowrap="nowrap"><? print $price.' <small>руб.</small>' ?></td><?
	?></tr>
	  </table><?
	  
	/* ВВОДИМ ДОПОЛНИТЕЛЬНУЮ ИНФОРМАЦИЮ */
	?><table align="left" border="0" cellpadding="1" cellspacing="5" class="showorderitems" ><?
		?><tr><?
			?><td nowrap="nowrap">Фамилия Имя Отчество</td><?
			?><td nowrap="nowrap"><?=$so->clientFIO; ?></td><?
		?></tr><?
		?><tr><?
			?><td nowrap="nowrap">Контактный телефон</td><?
			?><td align="left" nowrap="nowrap"><?=$so->clientPhone; ?></td><?
		?></tr><?
		?><tr><?
			?><td nowrap="nowrap">Адрес электронной почты</td><?
			?><td nowrap="nowrap"><?=$so->clientEmail; ?></td><?
		?></tr><?
		?><tr><?
			?><td nowrap="nowrap">Адрес доставки</td><?
			?><td nowrap="nowrap"><?=$so->clientAddress; ?></td><?
		?></tr><?
		?><tr><?
			?><td nowrap="nowrap">Способ оплаты</td><?
			?><td align="left" nowrap="nowrap"><?=$expaymethod->name; ?></td><?
		?></tr><?
		?><tr><?
			?><td nowrap="nowrap">Комментарий</td><?
			?><td nowrap="nowrap"><?=$so->note; ?></td><?
		?></tr><?
		?><tr><?
			?><td valign="top" style="vertical-align:top;">История работы с заказом</td><?
			?><td align="left">
				<table cellpadding="0" cellspacing="0" width="100%" style="padding:0px; margin:0px;">
					<?	
					  $db = $reg['db'];
					  $db->setQuery("SELECT osh.status_id, osh.time, osh.manager_id, osl.name, osh.note from #__orderstatushistory as osh left join #__orderstatuslist as osl on (osh.status_id = osl.id) where osh.order_id = ".$so->id." ORDER BY osh.time DESC");
					  $db->query();  
					  $objs = $db->loadObjectList();  
						if(count($objs) > 0){
							foreach($objs as $o) {
							?>
									<tr>
										<td valign="top" style="white-space:nowrap; padding-right:10px; vertical-align:top;" nowrap="nowrap"><?=date("d.m.Y H:i:s", $o->time);  ?></td>
										<td valign="top" style="white-space:nowrap; padding-right:10px; vertical-align:top;" nowrap="nowrap"><?=$o->name; ?></td>
										<td valign="top" width="100%" style="white-space:normal; vertical-align:top;" id="note<?=$o->id.$o->time ?>"><? if(strlen($o->note) > 0) print "<a href=\"javascript: ins_ajax_load_target('ca=shopajax&task=statusnote&orderid=".$so->id."&manager_id=".$o->manager_id."&time=".$o->time."&status=".$o->status_id."&4ajax=1', '#note".$o->id.$o->time."'); void(0);\">Примечание</a>"; ?> </td>
									</tr>
							<? }
						} ?>
				</table>
			</td><?
		?></tr><?
	?></table><?
	  
  }  
}

/** История статусов для менеджера */
function showStatusHistory()
{
  global $reg, $_REQUEST;
  $id = intval($_REQUEST['orderid']);
  $so = new shopOrder($reg);
  $db = $reg['db'];
  $db->setQuery("SELECT osh.status_id, osh.time, osh.manager_id, osl.name, osh.note from #__orderstatushistory as osh left join #__orderstatuslist as osl on (osh.status_id = osl.id) where osh.order_id = ".$id." ORDER BY osh.time DESC");
  $db->query();  
  $objs = $db->loadObjectList();
  ?> <table border="0" width="100%" align="center" > <tr><th>Статус</th><th>Установил</th><th>Дата</th><th>&nbsp;</th></tr><tr><td colspan="4">&nbsp;</td></tr><?  
  
  if(count($objs) > 0)
  {
      foreach($objs as $o)
      {
        ?><tr><td style='text-align:center'><?=$o->name; ?></td><td style='text-align:center'><?=$so->getUserName($o->manager_id); ?></td><td style='text-align:center'><?=date("d/m/Y H:i:s", $o->time); ?></td>
        <td style='text-align:center'><? if(strlen($o->note) > 0) { ?><a href="javascript: ins_ajax_load_target ('ca=shopajax&task=statusnote&orderid=<?=$id; ?>&manager_id=<?=$o->manager_id; ?>&time=<?=$o->time; ?>&status=<?=$o->status_id; ?>&4ajax=1', '#statusdiv'); void(0);">Примечание</a><? } ?> </td></tr> <?
      }
  }
  
  ?></table><br /><br />
  <div id = 'statusdiv'>&nbsp;&nbsp; &nbsp;</div>
  <?
}

function statusnote()
{
  global $reg, $_REQUEST;
  $time = intval($_REQUEST['time']);
  $manager = intval($_REQUEST['manager_id']);
  $order = intval($_REQUEST['orderid']);
  $status = intval($_REQUEST['status']);
  
  $db = $reg['db'];
  $query = sprintf("SELECT note FROM #__orderstatushistory WHERE order_id = %d AND status_id = %d AND time = %d AND manager_id = %d", $order, $status, $time, $manager);
  $db->setQuery($query);
  $db->query();  
  print $db->loadResult();
}

function search_order_form()
{
    ?>
	<form action="index2.php">
	<table border='1' class='adminlist'>
	  <tr><td>Номера заказов <input type="text" name="str" style="width:200px" /><input type="submit" value="Поиск" /></tr>

	</table>
		<input type="hidden" name="ca" value="shopmanager" />
		<input type="hidden" name="task" value="search" />		
	 </form>
<?
}


?>