<?
global $reg;
require_once(site_path."/lib/saver.php");

if (  isset($_REQUEST['order'])  )  {	/** Отображение страницы заказа */
	show_order_page($id);  
	return;	
} elseif( $_REQUEST['mycart_task'] == "note"){ /** Примечание к заказу */
	order_note($id); 
	return;
}

show_orders();
/*switch ($reg['task']) {
	default: show_orders(); break; 
}*/

function show_orders()
{
	global $reg, $my;
	$mid = intval($my->id);
	if($mid == 0) return false;
	
	$db = &$reg['db'];
	$db->setQuery("SELECT id FROM #__orders where uid = ".$my->id." order by create_time DESC");      
	$objs = $db->loadObjectList();
	
	if(count($objs) == 0) { print "На данный момент у Вас нет заказов"; return; }
	
	?><table class="zakazi" cellpadding="0" cellspacing="0" >
		<tr class="zakazi_row_title"><th colspan="5">Мои заказы</th></tr>
		<?
		foreach($objs as $o){
			$so = new shopOrder($reg);
			$so->load($o->id);
			$stat  = ($so->statusListpClass[$so->status-1] != "") ? $so->statusListpClass[$so->status-1] : "zakaz-status-yel";
			?>
			<tr>
				<td style="width:43px;" class="left"><?=$so->id; ?></td>
				<td style="width:95px;" ><?=date("d", $so->create_time); ?> <?=mb_strtolower(ru::GGgetMonthNames(intval(date("m", $so->create_time))), 'UTF-8'); ?>  <?=date("Y", $so->create_time); ?></td>
				<td style="width:250px;"><?=$so->statusList[$so->status-1]; ?></td>
				<td><?=bankir::money($so->price); ?> руб.</td>
				<td class="right"><a href="/cab_orders?order=<?=$so->code; ?>">детально</a></td>
			</tr>
			<tr>
				<td colspan="5" class="left right">
					<div style="padding:0px 0;">Менеджер<? if(count($so->managers) > 1) print "ы"; ?>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<? 
						$i = 0;
						foreach($so->managers as $manager){
							if($i > 0) print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";	$i++;	
							$name = $so->getUserName($manager);
							?><a class="dashed" href="javascript:void(0);" onclick="javascript: ins_ajax_open('/?c=manager_profile&4ajax=1&id=<?=$manager; ?>', 430, 240); void(0);"><?=$name; ?></a><?
						} ?>
					</div>
				</td>
			</tr>
			<tr><td colspan="5" ><hr class="dotted" /></td></tr>
		<? } ?>
		<tr class="order_button"><td colspan="5" align="right" class="down left right"></td></tr>
	</table><?

}	



 /*
  * Отображение страницы заказа
  */
function show_order_page($id){
	global $reg, $my, $mycart;
        $code = $_REQUEST['order'];
        $so = new shopOrder($reg);
        $so->loadByCode($code);

		/*  снимите комментарий, если необходимо закрывать заказ от просмотра незарегистрированным пользователям
		if (  $so->uid  ) if (  $so->uid!=$my->id  ){   ?>Для просмотра заказа необходимо <a href="javascript: ins_ajax_open('/?4ajax_module=login', 400, 280); void(0);">войти</a> или <a href="javascript: ins_ajax_open('/?4ajax_module=login&task=register', 680, 400); void(0);">зарегистрироваться</a>.<? return;   } */
        if($so->id == '') { print "Заказ не найден"; return; }
	      
		$expaymethod = ggo(  safelySqlInt($so->payment_type), "#__expaymethod"  );
		$cgood_pack = ggo ($mytovar['id'], "#__expack");
		$cgood = ggo ($cgood_pack->parent, "#__exgood");
		$price = $so->recalcPrice();
		
		$component_foto = new component_foto( 0 );
		$component_foto->init( 'exgood' );

   
		?><table align="left" width="90%" border="0" cellpadding="0" cellspacing="0" class="ex_trush"><?
			?><tr class="ex_trush_row_title"><?
				?><th width="20%" align="center" class="trush_left">Количество</th><?
				?><th align="center" class="trush_row" ></th><?
				?><th align="left" class="trush_row" style="text-align:left" >Наименование</th><?
				?><th width="14%" align="center" class="trush_row">Цена</th><?
				?><th width="14%" align="center" class="trush_row trush_right">Сумма</th><?
			?></tr><?
			foreach ($so->items as $item){
				$cgood_pack = ggo ($item->pack_id, "#__expack");
				$cgood = ggo ($cgood_pack->parent, "#__exgood");		//if ( $cgood->id==31 ) $cgood->images="";
				?><tr class="ex_trush_row"><?
					?><td class="trush_left" align="center"><?
						print $item->quantity;
					?></td><?
					?><td align="center" class="trush_row_img"><?
						shadow_effect(    $component_foto->createPreviewFotoLink ( 'small', 'org', $cgood, '', ' class="highslide fancy good1" title="'.desafelySqlStr($cgood->name.' '.$cgood_pack->name).'" ', ' border="0" hspace="0" '  )    );
					?></td><?
					?><td align="left" valign="top" class="trush_row_desc"><?
						?><a href="<?=$cgood->sefnamefullcat.'/'.$cgood->sefname ?>.html" target="_blank" class="incatgoodlink"><? print desafelySqlStr($item->name); ?></a><br><?
						?><br /><?=desafelySqlStr($item->pack_name); ?><br /><?
/*						?>Доставка — <?=$row->srok+14; ?> дней<br><?
						?>Артикул: <strong><?=num::fillzerro($cgood_pack->sku,8); ?></strong><br><? */
						//print $mycart->get_options_str (Array("options"=> $item->options));
					?></td><?
					?><td align="center" class="trush_row"><?=$item->price_offer; ?>&nbsp;<? print rub1(); ?></td><?
					?><td align="center" class="trush_row trush_right"><? print round($item->price_offer * $item->quantity,2) ; print '&nbsp;'.rub1(); ?></td><?
				?></tr><?
			}
			?><tr class="ex_trush_row_total"><?
				?><td align="left" class="trush_row trush_left">&nbsp;</td><?
				?><td align="left" class="trush_row">&nbsp;</td><?
				?><td align="left" class="trush_row">&nbsp;</td><?
				?><td align="right" class="trush_row"><nobr>Общая сумма:</nobr></td><?
				?><td align="center" class="trush_row trush_right"><nobr><? print $price.' '.rub1(); ?></nobr></td><?
			?></tr><?
			?><tr><td colspan="6" align="right" style="text-align:right">&nbsp;</td></tr><?
		?></table><?
		/* ВВОДИМ ДОПОЛНИТЕЛЬНУЮ ИНФОРМАЦИЮ */
		?><table align="left" width="90%" border="0" cellpadding="0" cellspacing="0" class="ex_order" id="ex_order_table" ><?
			?><tr class="ex_order_row_title" ><?
				?><th colspan="2" >Дополнительная информация</td><?
			?></tr><?
	
			?><tr height="35px"><?
				?><td class="left">Фамилия Имя Отчество</td><?
				?><td class="right"><?=desafelySqlStr($so->clientFIO); ?></td><?
			?></tr><?
			?><tr height="35px"><?
				?><td class="left">Контактный телефон</td><?
				?><td align="left" class="right"><?=$so->clientPhone; ?></td><?
			?></tr><?
			?><tr height="35px"><?
				?><td class="left">Адрес электронной почты</td><?
				?><td class="right"><?=$so->clientEmail; ?></td><?
			?></tr><?
			?><tr height="35px"><?
				?><td class="left">Адрес доставки</td><?
				?><td class="right"><?=$so->clientAddress; ?></td><?
			?></tr><?
			?><tr height="35px"><?
				?><td class="left last">Способ оплаты</td><?
				?><td align="left" class="right last"><?=$expaymethod->name; ?></td><?
			?></tr><?
			?><tr height="35px"><?
				?><td class="left last"></td><?
				?><td align="left" class="right last"><?
				switch($expaymethod->id){
					 case "1":	print "<a target='_blank' href='/".$reg['ex_seoname']."/shop.html?mycart_task=blank&bank=sber&code=".$_REQUEST['order']."&4ajax=1'>Распечатать квитанцию Сбербанка</a>";	break;
					 case "2":  print "<a target='_blank' href='/".$reg['ex_seoname']."/shop.html?mycart_task=blank&bank=com&code=".$_REQUEST['order']."&4ajax=1'>Распечатать счет в банке заказ</a>";		break;
					 case "3":	print "<a target='_blank' href='/".$reg['ex_seoname']."/shop.html?mycart_task=blank&bank=alfa&code=".$_REQUEST['order']."&4ajax=1'>Распечатать квитанцию Альфабанка</a>";	break;
					 case "4":	print "<a target='_blank' href='/".$reg['ex_seoname']."/shop.html?mycart_task=blank&bank=ros&code=".$_REQUEST['order']."&4ajax=1'>Распечатать квитанцию Росбанка</a>";		break;
	  			}
				?></td><?
			?></tr><?

			?><tr height="35px"><?
				?><td class="left">Комментарий</td><?
				?><td align="left" class="right"><?=$so->note; ?></td><?
			?></tr><?
			if (  $so->managers[0]  ){
				?><tr height="35px"><? 
					?><td class="left">Ответственный менеджер(ы):</td><?
					?><td align="left" class="right"><? 
						foreach (  $so->managers as $mymanager  ){
					 		$manager = ggo($mymanager, "#__users"); 
							?><a href="javascript: ins_ajax_open('/?c=manager_profile&4ajax=1&id=<?=$manager->id ?>', 0, 0); void(0);" class="dashed"><?=desafelySqlStr(  $manager->usersurname.' '.$manager->name.' '.$manager->userparentname  ); ?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?
						}
						?></td><?
				?></tr><?
			}
			?><tr height="35px"><?
				?><td class="left">История работы с заказом</td><?
				?><td align="left" class="right">
					<div id = "ajaxnote"> </div>
					<table width="90%">
						<tr>
							<td><strong>Время</strong></td>
							<td><strong>Статус</strong></td>
							<td></td>
						</tr><?	
						  $db = $reg['db'];
						  $db->setQuery("SELECT osh.status_id, osh.time, osh.manager_id, osl.name, osh.note from #__orderstatushistory as osh left join #__orderstatuslist as osl on (osh.status_id = osl.id) where osh.order_id = ".$so->id." ORDER BY osh.time DESC");
						  $db->query();  
						  $objs = $db->loadObjectList();  
							if(count($objs) > 0){
								foreach($objs as $o) {
								?>
										<tr>
											<td><?=date("d.m.Y H:i:s", $o->time);  ?></td>
											<td><?=$o->name; ?></td>
											<td><? if(strlen($o->note) > 0) print "<a href=\"javascript: ins_ajax_open('?c=cab_orders&mycart_task=note&code=".$_REQUEST['order']."&status=".$o->status_id."&manager=".$o->manager_id."&time=".$o->time."&4ajax=1', 480, 370); void(0);\">Примечание</a>"; ?> </td>
										</tr>
								<? }
							} ?>
					</table>
				</td><?
			?></tr><?
			?><tr class="order_button"><?
				?><td colspan="2" align="right" class="order_button" ></td><?
			?></tr><?
		
			if (  $so->uid>0  ){
				?><tr><?
					?><td colspan="2" align="right" ><a href="/cab">Вернуться в личный кабинет</a></td><?
				?></tr><?
			}


		?></table><div style="clear:both;"></div><?

                /*
                 * форма ЗАДАТЬ ВОПРОС МЕНЕДЖЕРУ
                 *
                 * так по сути это компонент - править в компоненте - class comments ( /lib/comments.php )
                 */
		$comments = new comments('order', $reg['db'], $reg);
		$comments->can_answer = 0; // мы не можем отвечать
		$comments->comments_here($so->id, 'say_question');

		//print $etmp;	      
		return;	
}

function order_note($id){
	global $reg, $my;
		    $order = $_REQUEST['code'];
		    $so = new shopOrder($reg);
		    $so->loadByCode($order);
		    if($so-> id == '') return;  
		    $status = intval($_REQUEST['status']);
		    $manager = intval($_REQUEST['manager']);
		    $time = intval($_REQUEST['time']);		    
		    $db = $reg['db'];
		    $db->setQuery("SELECT note FROM #__orderstatushistory WHERE order_id = ".$so->id." AND manager_id = ".$manager." AND status_id = ".$status." AND time = ".$time." LIMIT 0,1");
		    $db->query();
		    if($db->getNumRows() == 0) return;
		    else print "<br />".$db->loadResult();
		    return;
}

?>