<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
define( 'ICSEX_LIB', 1 );


function ex_get_real_f($fval){
	return $fval == '' ? '&nbsp;' : $fval;
}

$ex_value_print = "руб.";

function exrecalc_req($sefurl, $excatid, $realgoods) {
global $database;
	$excatgoods = ggsqlr ( "select count(id) from #__exgood where parent=$excatid " );
	if (  $realgoods!=$excatgoods  and  $excatid>0  ){
		$i24r = new mosDBTable( "#__excat", "id", $database );	
		$i24r->id = $excatid; $i24r->goods = $excatgoods; 
		if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	}
	if (  $excatgoods>0  and  $excatid>0  ){
		$exgoods = ggsql ( "select * from #__exgood where parent=$excatid " ); //ggtr ($exgoods);
		foreach ($exgoods as $exgood){
			if (  $exgood->sefnamefullcat!=$sefurl  ){	
				$i24r = new mosDBTable( "#__exgood", "id", $database );
				$i24r->id = $exgood->id; 	$i24r->sefnamefullcat = $sefurl;
				if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
			}
		}
	}	
	$excats = ggsql ( "select * from #__excat where parent=$excatid " ); // ggtr($excats);
	if (  count($excats)>0  )
		foreach ($excats as $excat){	//ggtr ($excat->id,1);
			// обновляем sefurlfull для категории
			$i24r = new mosDBTable( "#__excat", "id", $database );
			$i24r->id = $excat->id;
			$i24r->sefnamefull = $sefurl;  $excat->sefnamefull = $sefurl; // ggtr01 ($sefurl);
			if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
			// обновляем sefurlcat для объявлений
			exrecalc_req ($sefurl."/".$excat->sefname, $excat->id, $excat->goods);
		}
}

function excat_update_goods ($idcat){
	global $database;
	$i24r = new mosDBTable( "#__excat", "id", $database );
	$i24r->id = $idcat;
	$i24r->goods = ggsqlr ( "select count(id) from #__exgood where parent=$idcat " );
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	return ;
}

function excat_get__sefnamefull($idcat){
	global $reg;
	$thisfotocat = ggo($idcat, "#__excat");
	$icatway = array(); $iii = 0;
	$icatway[0]->name = ($thisfotocat->name); $icatway[0]->parent = $thisfotocat->parent; $icatway[0]->sefname = $thisfotocat->sefname;
	if (  $thisfotocat->id==0  ) return "";
	while ($icatway[$iii]->parent!=0){
		$icur_catfoto = ggo($icatway[$iii]->parent, "#__excat");
		$iii++;
		$icatway[$iii]->name = ($icur_catfoto->name); $icatway[$iii]->parent = $icur_catfoto->parent; $icatway[$iii]->sefname = $icur_catfoto->sefname;
	}
	$icatway = invert_array($icatway); $strret = ""; $maxcnt=count ($icatway);
	foreach ($icatway as $iii=>$icatway1){  if (  $iii==($maxcnt-1)  ) break; $strret .= $icatway1->sefname."/"; }
	return '/'.$reg['ex_seoname'].'/'.substr(  $strret, 0, (strlen($strret)-1)  );
}
function getAllSections($parent)
{
     global $reg;
     if (  !$parent  ) return;
     $db = &$reg['db'];

     $db->setQuery("SELECT * from #__excat WHERE parent = ".$parent." and publish = 1");
     $db->query();
     if($db->getNumRows() > 0)
     {
         $sect = $db->loadResultArray();
         $temp = Array();
         foreach($sect as $s) {
             $tmp = getAllSections($s);
             if(is_array($tmp)) $temp = array_merge($temp, $tmp);
         }
         if(is_array($temp)) $sect = array_merge($sect, $temp);
         return $sect;
     }
     else return;
}



function attrib_search($parent, $unit, $ids)
{
      global $reg, $_REQUEST;
     $db = &$reg['db'];
      if($unit == 0) $unit = "";
      else $unit =  "  AND unit = ".$unit;
	  
      /* Выбор тех паков, которые подходят */
      $where = "";
      $db->setQuery("SELECT DISTINCT ep.id FROM #__expack as ep LEFT JOIN #__expack_set_val as esv ON (ep.id = esv.pack_id) WHERE ep.parent = ".$parent.$unut);
      $db->query();
      $found = $db->loadResultArray();  

      foreach($ids as $key=>$value)
      {
		$where = " AND ( attrib = ".$key." AND attrib_val = ".$value." ) ";
		$db->setQuery("SELECT DISTINCT ep.id FROM #__expack as ep LEFT JOIN #__expack_set_val as esv ON (ep.id = esv.pack_id) WHERE ep.parent = ".$parent.$unit." ".$where);
		$db->query();
		$pack = $db->loadResultArray();  
		if(count($found) > 0)
		{
		      foreach($found as $k=>$v){
			    $f = false;
			    foreach($pack as $k1 =>$v1){
				  if($v == $v1) $f = true;
			    }
			    if(!$f) {$found[$k] = 0;}
		      }
		}  
      }

      $pack = Array();
      foreach($found as $key=>$value)
      {
	    if($value != 0) $pack[] = $value;
      }
      return $pack;
}

function show_news_goods ( $p ){
    global $reg;
    $sql_vars = "       exgood.id               as exgood_id,
                        exgood.name             as exgood_name,
                        exgood.sefname          as exgood_sefname,
                        exgood.sefnamefullcat   as exgood_sefnamefullcat,
                        exgood.sdesc            as exgood_sdesc,
                        exgood.fdesc            as exgood_fdesc,
                        exgood.small            as exgood_small,
                        exgood.org              as exgood_org,
                        exgood.brand            as exgood_brand,
                        exgood.expack_select_type    as exgood_expack_select_type,
                        exgood.expack_set       as exgood_expack_set,

                        expack.id               as expack_id,
                        expack.name             as expack_name,
                        expack.sku              as expack_sku,

                        exprice.val        as exprice_val,
                        exprice.cy         as exprice_cy
                    ";
    $query = "SELECT    $sql_vars
        FROM #__exgood as exgood
        LEFT JOIN #__expack as expack ON (expack.parent = exgood.id)
        LEFT JOIN #__exprice_good as exprice ON (  exprice.parent = $p->price_parent  AND  expack.id = exprice.expack  )
        WHERE exgood.small<>''
        GROUP BY(exgood.id)
        ORDER BY exgood.id DESC ";

        $p->rows = ggsql( $query, 0, 5 );
        //ggdd();
        //ggtr5 ($reg['db']->_sql);

        // формируем список товаров
        ?><span class="cnt-emed">Новинки</span><?
        html_showexcatGoodsList ( $p ); // в файле ex_html.php

}

function show_recent_purchased ( $p ){
    global $reg;
    $sql_vars = "       exgood.id               as exgood_id,
                        exgood.name             as exgood_name,
                        exgood.sefname          as exgood_sefname,
                        exgood.sefnamefullcat   as exgood_sefnamefullcat,
                        exgood.sdesc            as exgood_sdesc,
                        exgood.fdesc            as exgood_fdesc,
                        exgood.small            as exgood_small,
                        exgood.org              as exgood_org,
                        exgood.brand            as exgood_brand,
                        exgood.expack_select_type    as exgood_expack_select_type,
                        exgood.expack_set       as exgood_expack_set,

                        expack.id               as expack_id,
                        expack.name             as expack_name,
                        expack.sku              as expack_sku,

                        exprice.val        as exprice_val,
                        exprice.cy         as exprice_cy
                    ";
    $query = "SELECT    $sql_vars
        FROM #__orderitems as orderitems
        INNER JOIN #__expack as expack ON (  expack.id = orderitems.pack_id  )
        LEFT JOIN #__exgood as exgood ON (  expack.parent=exgood.id  AND  exgood.small<>''  )
        LEFT JOIN #__exprice_good as exprice ON (  exprice.parent = $p->price_parent  AND  expack.id = exprice.expack  )

        ORDER BY orderitems.order_id DESC ";

        $p->rows = ggsql( $query, 0, 5 );
        //ggdd();
        //ggtr5 ($reg['db']->_sql);

        // формируем список товаров
        ?><span class="cnt-emed">Недавно купленные</span><?
        html_showexcatGoodsList ( $p ); // в файле ex_html.php

}


/**
 * ЗАКАЗ СДЕЛАН - СОХРАНЯЕМ И ОТПРАВЛЯЕМ УВЕДОМЛЕНИЯ
 */
function submit_order($id){
	global $reg, $my;
	
		$exgfg = ggo (1, "#__excfg");
		
		if (   ggrr('u_fio')==''   ) 						{ ?><script language="javascript">$("#trushForm_contact_server_answer").html('— Заполните поле «Фамилия Имя Отчество»').jTypeWriter({duration:1.5}); </script><? return; }
		if (   ggrr('u_tel')==''   ) 						{ ?><script language="javascript">$("#trushForm_contact_server_answer").html('— Заполните поле «Контактный телефон»').jTypeWriter({duration:1.5});  </script><? return; }
		if (   ggrr('u_address')==''   ) 					{ ?><script language="javascript">$("#trushForm_contact_server_answer").html('— Заполните поле «Адрес доставки»').jTypeWriter({duration:1.5});  </script><? return; }

		$expaymethod = ggo(  safelySqlInt($_REQUEST['expaymethod']), "#__expaymethod"  );
		$exOrderList = '';

		if (  $_REQUEST['u_mail']=='(не обязательно)'  )  $_REQUEST['u_mail']="";
                if (  $_REQUEST['u_note']=='(не обязательно)'  )  $_REQUEST['u_note']="";

                // сохраняем введенную пользователем контактную информацию, чтобы потом при заказе ее заново не вводить
		$me = new user( );
		$me->saveContactDataFromOrder ( $my );

		$order = new shopOrder($reg, "new");

		if (  ggri('show_order_register_me')==1  &&  ggri('uid')>0  ) $order->uid = ggri('uid');
		else $order->uid      =	$my->id;
		$order->clientFIO     =	($_REQUEST['u_fio']);
		$order->clientPhone   =	($_REQUEST['u_tel']);
                $order->clientEmail   =	($_REQUEST['u_mail']);
		$order->clientAddress = ($_REQUEST['u_address']);
		$order->note = 		($_REQUEST['u_note']);
		$order->payment_type  = $expaymethod->id;

		$mycart = new mycart();
		$mycart->load();
		$mycart->recalcPrice();
                $items_relation = Array();  // для учета статистики - "с этим товаром покупают"

		foreach ($mycart->mycart as $index => $mytovar){
			$cgood_pack = ggo ($mytovar['id'], "#__expack");
			$cgood = ggo ($cgood_pack->parent, "#__exgood");
                        $items_relation[] = $cgood_pack->parent;    // для учета статистики - "с этим товаром покупают"
                        $item = &$order->addItem($mytovar['id'], $mytovar['qty'], $mytovar['options']);
			$exOrderList .= '<tr class="Stil1">
				<td>'.$mytovar['qty'].'</td>
				<td>'.$cgood->name.' '.$mycart->get_options_str ($mytovar).' '.$item->getRealUsePackname( $cgood_pack->name, $cgood->expack_select_type ).'</td>
				<td>'.$cgood_pack->sku.'</td>
				<td>'.$mytovar['price'].' руб.</td>
				<td>'.$mytovar['price_qty'].' руб.</td>
			</tr>';
			$item->name = $cgood->name.' '.$mycart->get_options_str ($mytovar);
			$item->pack_name = $item->getRealUsePackname( $cgood_pack->name, $cgood->expack_select_type );
			$item->price = $mytovar['price'];
			$item->price_offer = $mytovar['price'];
			$item->save;
		}
		$order->recalcPrice();
		$order->save();

                // учет статистики - "с этим товаром покупают"
                $rel = new relation($reg);
                $rel->set($items_relation);

		$etmp = file_get_contents(site_path."/component/ex/email_template.html");
		$etmp = str_replace("{exVendorImage}", 			'', $etmp);
		$etmp = str_replace("{header_mail}", 			desafelySqlStr($exgfg->header_mail), $etmp);
		$etmp = str_replace("{exOrderHeader}", 			'Заказ товаров с сайта', $etmp);
		$etmp = str_replace("{exOrderDate}", 			date( 'Y-m-d H:i:s' ), $etmp); 
		$etmp = str_replace("{exOrderNumber}", 			$order->id, $etmp);
		$etmp = str_replace("{exOrderStatus}", 			$order->statusList[0], $etmp);
		$etmp = str_replace("{exBTCompany}", '', 		$etmp);
		$etmp = str_replace("{exBTFIO}", 				desafelySqlStr($_REQUEST['u_fio']), $etmp);
		$etmp = str_replace("{exBTPhone}", 				desafelySqlStr($_REQUEST['u_tel']), $etmp);
		$etmp = str_replace("{exBTEmail}", 				desafelySqlStr($_REQUEST['u_mail']), $etmp);
		$etmp = str_replace("{exBTAdrress}", 			desafelySqlStr($_REQUEST['u_address']), $etmp);
		$etmp = str_replace("{exCustomerNote}", 		desafelySqlStr($_REQUEST['u_note']), $etmp);
		$etmp = str_replace("{exOrderClosingMsg}", 		($exgfg->thanku_mail), $etmp);
		$etmp = str_replace("{PAYMENT_INFO_DETAILS}", 	$expaymethod->name, $etmp);
		$etmp = str_replace("{SHIPPING_INFO_DETAILS}", 	desafelySqlStr($_REQUEST['u_address']), $etmp);
		$etmp = str_replace("{exOrderList}", 			$exOrderList, $etmp);
		$etmp = str_replace("{exOrderSubtotal}", 		$mycart->priceTotal.' руб.', $etmp);
		$etmp = str_replace("{exOrderLink}", 			site_url.'/cab_orders?order='.$order->code, $etmp);
		
		$mymail = new mymail();
		$mymail->add_address (  desafelySqlStr( $exgfg->order_mail_to )  );
		$mymail->set_subject (  desafelySqlStr( $exgfg->order_mail_subject )  );
		$mymail->set_body	 (  desafelySqlStr( $etmp )  );
		$mymail->send ();
		// do buyer copy 
		if (  $_REQUEST['u_mail']!='(не обязательно)'  &&  $_REQUEST['u_mail']!=''  ){
			$mymail->clear_addresses();
			$mymail->add_address ( $_REQUEST['u_mail'] );
			$mymail->send ();
		}

                /*
                 * НАЗНАЧЕНИЕ МЕНЕДЖЕРА НОВОМУ ЗАКАЗУ
                 */

                // назначаем любимого менеджера, если есть
                if (  $reg['shop_manager4new_order']==2  ||  $reg['shop_manager4new_order']==3  ){
                    $orderManager = 0;
                    if (  $my->id  ){
                        //определяем любимого менеджера (т.е. Если клиент совершает не первую покупку - предыдущего менеджера)
                        $orderManager = $order->getFavouriteManager($order->id, $my->id);
                        
                        if (  $orderManager>0  ){ // менеджер найден, ставим его на заказ
                              $order->my->gid = 100;
                              $order->addManager( $orderManager );
                        }
                    }
                    if (  $orderManager==0  and  $reg['shop_manager4new_order']==3  ) { // менеджер не найден, ставим любого
                        $allmanagers = ggsql ( 'select * from #__users where gid=23' );
                        mt_srand();
                        $orderManager = $allmanagers[mt_rand(0,count($allmanagers))]->id;
                        $order->my->gid = 100;
                        $order->addManager( $orderManager );
                    }



                    if (  $orderManager>0  ){
                      /*
                       * УВЕДОМЛЕНИЕ ПО СМС
                       */
                       if (  $reg['shop_note_sms_enable']==1  ){
                               $manager = ggo ($orderManager, "#__users");
                               if (  $manager->note_sms_tel2!=''  and  $manager->note_sms_enable==1  ){
                                            $order_text = short_surl().", Вам поступил новый заказ #".$order->id;

                                            $mail2sms = new mail2sms();
                                            $mail2sms->tel = $manager->note_sms_tel1.$manager->note_sms_tel2;
                                            $mail2sms->tel = preg_replace("/[- ]/", "", $mail2sms->tel);
                                            $mail2sms->oper = $manager->note_sms_oper;
                                            $mail2sms->text = $order_text;
                                            $mail2sms->sendSms();
                               }
                       }
                    }





                }

		/*
		 * ОТПРАВКА СМС-УВЕДОМЛЕНИЯ
		 */
		if (  $reg['shop_note_sms_enable']==1  ){
			$order_text = short_surl().", новый заказ #".$order->id;
			
			$mail2sms = new mail2sms();
			$mail2sms->tel = $reg['shop_note_sms_tel'];
			$mail2sms->tel = preg_replace("/[- ]/", "", $mail2sms->tel);
			$mail2sms->oper = $reg['shop_note_sms_operator'];
			$mail2sms->text = $order_text;
			$mail2sms->sendSms();
		}
		
		/*
		 * ОЧИСТКА КОРЗИНЫ
		 */
		$mycart->clear();
		// ОБНОВЛЕНИЕ МОДУЛЯ - КОРЗИНА
		?><script language="javascript">ins_ajax_load_target("4ajax_module=trash", "#shopCart");</script><?
		
		/*
		 * Заключительный этап - благодарим пользователя и выводим ссылку на страницу заказов
		 */		
		//$thank_txt = "";
		//$exgfg = ggo (1, "#__excfg");
		//$thank_txt = desafelySqlStr($exgfg->thanku)."<br /><br />";
                ?><script language="javascript"> ins_ajax_load_target("c=ex&task=viewtrush&show_order=thank&code=<?=$order->code ?>&4ajax=1&floating=1", "#mycart_allcart"); </script> <?
		/*if(  $order->code  ) { $thank_txt .= 'Вы можете посмотреть статус вашего заказа и историю его обработки онлайн <a href="/cab_orders?order='.$order->code.'">на&nbsp;странице&nbsp;заказа</a>'; }
		<script language="javascript">$("#mycart_allcart").html('<?=$thank_txt ?>'); </script> */

		/* ?>ins_ajax_open('/<?=$reg['ex_seoname'] ?>'/thank.html?4ajax=1&code=<?=$order->code ?>', 0, 0);<? */
		
		/*
		 * ЕСЛИ ОФОРМЛЕНИЕ ЗАКАЗА БЕЗ AJAX
		 */
		//mosRedirect( 'thank.html?code='.$order->code ); 
		return;
}


function payment_bank_bank($id){
		  global $reg;
		  $so = new shopOrder($reg);
		  $so->loadByCode($_REQUEST['code']);
		  if(strlen($so->id) == 0) return false;
		  $file = "";
		  if(strcmp($_REQUEST['bank'], "sber") == 0)	$file = site_path."/component/ex/templates/sber.html";
		  if(strcmp($_REQUEST['bank'], "com") ==0) $file = site_path."/component/ex/templates/bank.html";
		  if(strcmp($_REQUEST['bank'], "alfa") == 0) $file = site_path."/component/ex/templates/alfa.html";
		  if(strcmp($_REQUEST['bank'], "ros") == 0) $file = site_path."/component/ex/templates/ros.html";
		  if($file == "") return;
		  $sber = @file_get_contents($file);
		  $bank = new Bankir();
		  $sum = $bank->money($so->price);
		  list($amountRub, $amountKop) = explode(".", $sum);
		  $sber = str_replace("{exVendorName}", $reg['exVendorName'], $sber);
		  $sber = str_replace("{exVendorINN}", $reg['exVendorINN'], $sber);
		  $sber = str_replace("{exVendorAccount}", $reg['exVendorAccount'], $sber);
		  $sber = str_replace("{exVendorBank}", $reg['exVendorBank'], $sber);
		  $sber = str_replace("{exVendorKorAccount}", $reg['exVendorKorAccount'], $sber);
		  $sber = str_replace("{exVendorBankReq}", $reg['exVendorBankReq'], $sber);
		  $sber = str_replace("{exVendorUrAddress}", $reg['exVendorUrAddress'], $sber);
		  $sber = str_replace("{exVendorMailAddress}", $reg['exVendorMailAddress'], $sber);
		  $sber = str_replace("{exVendorKPP}", $reg['exVendorKPP'], $sber);
		  $sber = str_replace("{exBTFIO}", htmlspecialchars($so->clientFIO), $sber);
		  $sber = str_replace("{exBTAddress}", htmlspecialchars($so->clientAddress), $sber);
		  $sber = str_replace("{orderID}", $so->id, $sber);
		  $sber = str_replace("{exPayer}", htmlspecialchars($so->clientFIO), $sber);
		  $sber = str_replace("{amountRub}", $amountRub, $sber);
		  $sber = str_replace("{amountKop}", $amountKop, $sber);
		  $sber = str_replace("{orderSum}", $sum, $sber);
		

		  if(strcmp($_REQUEST['bank'], "sber") == 0)
		  {			  
			  $sign = sprintf("Оплата заказа №%d от %s", $so->id, date("d.m.Y", $so->create_time));
	  
		  }
		  else if(strcmp($_REQUEST['bank'], "com") ==0)
		  {		
			  $sign = sprintf("Счет&nbsp;№%d &nbsp;от &nbsp;%s", $so->id, date("d-m-Y   H:i", $so->create_time));
			  $items = "";
			  $c = 1;
			  foreach($so->items as $item)
			  {
				$items .= '<TR class="tablerow">
				<TD class="tablerow">'.$c++.'</TD>
				<TD class="tablerow">'.$item->name." ".$item->pack_name.'</TD>
				<TD class="tablerow" align="center">шт.&nbsp;</TD>
				<TD align="right" class="tablerow">'.$item->quantity.'</TD>
				<TD align="right" class="tablerow" nowrap="">'.$item->price_offer.'</TD>
				<TD class="tableright">'.$bank->money(round($item->price_offer * $item->quantity,2)).'</TD>
				</TR>';  
			  }
			  $sber = str_replace("{orderItems}", $items, $sber);
			  $sber = str_replace("{orderDiscount}", "0", $sber);			  
			  $sber = str_replace("{orderNDS}", round($sum * 0.18, 2), $sber);
			  $sber = str_replace("{orderItemsCount}", count($so->items), $sber);
			  $sber = str_replace("{orderSumWords}", $bank->toWords($sum), $sber);
                          ggtr ($bank->toWords($sum));
				      
		  }
		    
		  else if(strcmp($_REQUEST['bank'], "alfa") == 0)
		      {
		      }
		  else if(strcmp($_REQUEST['bank'], "ros") == 0)
		    {
		    }
		  $sber = str_replace("{orderSign}", $sign, $sber);
		  print $sber;
		  return;
}


?>