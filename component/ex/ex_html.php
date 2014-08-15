<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
define( 'ICSEX_LIB', 1 );


class tplCatalog {
	
	public function __construct(){
		
	}
	
	public function tpl($name, $params){
		$tpl_name = 'html' . mb_ucfirst($name);
		if(method_exists($this, $tpl_name))
			return $this->$tpl_name($params);
			
		return false;
	}
	
	private function htmlCategoryList($p)
	{
		if(!$p->rows) return;
		?>
		<div class="holst">
			<div class="inner_content">
				<?ipathway();?>
				<div class="stranu unl">
					<? foreach($p->rows as $row){ echo "<a href='{$row->sefnamefull}/{$row->sefname}/'>{$row->name}</a><br><br>\n"; } ?>
					<div class="clear"></div>
				</div>
			</div>
		</div>
		<?
	}
	
	private function htmlProductsList(&$p)
	{
		?>
		<div class="holst">
			<div class="inner_content wide">
				<?ipathway();?>
				<h4><?=$p->icars->name ?></h4>
				<div class="item_list">
					<?
					if($p->rows)
					{ 
						foreach($p->rows as $row)
						{ 
							$row->small = $row->small ? $row->small : '12.jpg';
							echo "<a href='{$row->sefnamefullcat}/{$row->sefname}.html'><img src='/images/ex/good/{$row->small}' width='206' height='206' alt='фото {$row->name}'> <div><span>{$row->name}</span></div></a>\n"; 
						}
					}
					else
					{
						echo 'Список товаров пуст.'; 
					}
					?>
				</div>
			</div>
		</div>
		<?
	}
	
	private function htmlShowProduct($p)
	{
		$row = $p->exgood->vars;
		#xmp($p->component_foto);
	/*	?>
		<div class="holst">
			<div class="inner_content fs14_24">
				<?ipathway();?> 
				<h1><?=$row->name ?></h1>
				<div id="tabs" class="sub_link">
					<a href="#0" class=active>Причины посетить страну</a>
					<a href="#1">Информация по визе</a>
					<a href="#2">Фотогалерея</a>
				</div>
				<div class="clear"></div>
				<div class="tabx"><?=$row->sdesc ? desafelySqlStr($row->sdesc) : "<h3 class='tc blue3'>В данное время нет информации.</h3>"; ?></div>
				<div class="tabx none"><?=$row->fdesc ? desafelySqlStr($row->fdesc) : "<h3 class='tc blue3'>В данное время нет информации по визе.</h3>"; ?></div>
				<div class="tabx"><? $this->gallery($p->component_foto); ?></div>
			</div>
		</div>
		<?		
		*/
		?> 
		
		<div class="holst">
			<div class="inner_content fs14_24">
				<?ipathway();?> 
				<h1><?=$row->name ?></h1>
				
				<div class="clear"></div>
				<div class="tabx"><?=$row->sdesc ? desafelySqlStr($row->sdesc) : "<h3 class='tc blue3'>В данное время нет информации.</h3>"; ?>
				
				
				</div>	
						
				
				
			</div>
				
				
		</div>

		<?
	}
	

	
	private function gallery($p)
	{
		$rows = $p->get_fotos();
		if(!$rows){ echo "<h3 class='tc blue3'>В данное время нет фотографий.</h3>"; return;}
		
		?>
		<h3 class="tc blue3">Фотогалерея</h3>

		<div id='top' >
		<div id='wrap_all' class='stretched'>
		
			<div class="wrapper wrapper_shadow" id='wrapper_featured_area'>
				<div class='overlay_top'></div>
				<div class='overlay_bottom'></div>
				<div class="center">
					<div class="feature_wrap">
						<ul class='slideshow aviaslider'>
						<?
						if($rows) foreach($rows as $row)
						{
							?>
							<li class='featured'>
								<span>
									<? if($row->name || $row->desc): ?>
									<span class='feature_excerpt'>
										<?=$row->name ? "<strong class='sliderheading'>{$row->name}</strong>":''; ?>
										<?=$row->desc ? "<span class='slidercontent'>{$row->desc}</span>":''; ?>
									</span>
									<? endif; ?>
									<img  src='<?=$p->url_prefix.$row->org ?>' alt='image' title='<?=$row->name ?>' height='440 ' width='940' />
								</span>
							</li>
							<?
						}
						?>
						</ul>
					</div>
				</div>
			</div>

			<div class="wrapper" id='wrapper_featured_stripe'>
				<div class="center">
					<ul class='slideshowThumbs'>
					<?
					if($rows) foreach($rows as $row)
					{
						?>
						<li class='slideThumb'>
							<span class='slideThumWrap'>
								<?=$row->name ? "<span class='slideThumbTitle'><strong class='slideThumbHeading rounded'>{$row->name}</strong></span>":''; ?>
								<span class='fancy'></span><img src='<?=$p->url_prefix.$row->small ?>' alt='img' height='50' width='70' />
							</span>
						</li>
						<?
					}
					?>
					</ul>
				</div>
			</div>
			
		</div>
		</div>
		<?
	}
	
	
	
	
	
	
	private function htmlSearchFrom(&$p){
		global $reg;
					
		$category = $p->icars;
								
		$expack_set = intval($category->expack_set);
		if(empty($expack_set))
			return false;
		
		$ids = getAllExpackSet($expack_set);
		$expack_ids = implode(',', $ids);
			
		$sql = "select a.id as group_id, a.name as group_name, a.type as group_type, b.id as item_id, b.val as item_name from #__expack_attrib a inner join #__expack_attrib_val b on (b.parent = a.id) where a.parent in ($expack_ids) and a.filter = 1 order by a.name, b.val";
				
		$attributes = ggsql($sql);		
		if(!count($attributes))
			return false;
				
		$group_id = -1;
		$prev = null;
		foreach ($_REQUEST as $k => $v){
			if ((preg_match('/^group_id_/', $k) || preg_match('/^price_(from|till)$/', $k)) && $v != '') {
								
				break;
			}
		}
		
		$price_from = ggrr('price_from');
		$price_till = ggrr('price_till');
		
		$search_html = '';
		foreach($attributes as $i => $attribute){		
			if ($attribute->group_id != $group_id){
				$start = true;
				
				$group_id = $attribute->group_id;
				if ($i > 0) {
					if ($prev->group_type == 4)
						$search_html .= '</select></td></tr>';
					else
						$search_html .= '</td></tr>';
				}
				$prev = $attribute;
				
				$search_html .= "<tr><td>{$attribute->group_name}</td><td>";
			}
			
			switch($attribute->group_type) {
				case '1': #text				
					$group_from_val = ggrr("group_id_{$attribute->group_id}_from");
					$group_till_val = ggrr("group_id_{$attribute->group_id}_till");
					
					$search_html .= <<<HTML
						<span>от</span><input name="group_id_{$attribute->group_id}_from" type="text" value="{$group_from_val}" />
						<span>до</span><input name="group_id_{$attribute->group_id}_till" type="text" value="{$group_till_val}" />				
HTML;
					break;
				case '2': #checkbox		
					$data = $_REQUEST["group_id_{$attribute->group_id}"];				
					$checked = is_array($data) && in_array($attribute->item_id, $data) ? 'checked="checked"' : '';
					
					$search_html .= <<<HTML
						<input type="checkbox" id="group_id_{$attribute->group_id}_{$i}" name="group_id_{$attribute->group_id}[]" value="{$attribute->item_id}" {$checked} />
						<label for="group_id_{$attribute->group_id}_{$i}">{$attribute->item_name}</label>				
HTML;
					break;
				case '3': #radio
					$data = $_REQUEST["group_id_{$attribute->group_id}"];
					
					$checked = ($attribute->item_id == $data) ? 'checked="checked"' : '';
					
					$search_html .= <<<HTML
						<input type="radio" id="group_id_{$attribute->group_id}_{$i}" name="group_id_{$attribute->group_id}" value="{$attribute->item_id}" {$checked}/>
						<label for="group_id_{$attribute->group_id}_{$i}">{$attribute->item_name}</label>				
HTML;
					break;
				case '4': #select
					if ($start) {
						$start = false;
						$search_html .= <<<HTML
							<select name='group_id_{$attribute->group_id}' >
								<option value='' style='color:silver'>----</option>	
HTML;
					}				
					$data = $_REQUEST["group_id_{$attribute->group_id}"];
					$selected = $attribute->item_id == $data ? 'selected="selected"' : '';
					
					$search_html .= "<option value='{$attribute->item_id}' {$selected}>{$attribute->item_name}</option>";				
					break;			
			}	
		}
	
		if ($prev->group_type == 4)
			$search_html .= '</select><br />';
					
		$html = <<<HTML
			<div class="extsearch">
				<h2>Расширенный поиск</h2>
				<nav>
				<form action="" method="get">
					<table>
						<tr>
							<td>Цена</td>
							<td>
								<span>от</span> <input name="price_from" type="text" value="{$price_from}" />
								<span>до</span> <input name="price_till" type="text" value="{$price_till}" />								
							</td>
						</tr>
						{$search_html}
					</table>
					<input type="submit" value='Искать'/>
				</nav>
				</form>
			</div>	
HTML;
		echo $html;		
	}
	
}



/*
 * КОРЗИНЫ ЭТАПЫ ОФОРМЛЕНИЯ ЗАКАЗА
 */

/*
 * ЭТАП 1 - ОТОБРАЖАЕМ КОРЗИНУ - СПИСОК КУПЛЕННЫХ ТОВАРОВ
 */
function lib_show_trush_list(){
		global $reg;
		$mycart = new mycart();
		$mycart->load();

		$component_foto = new component_foto( 0 );
		$component_foto->init( 'exgood' );

		if ($mycart->mycart_index == 0){
			echo <<<HTML
				<div class="popup-small">
					<p style="text-align: center"><img src="/images/shopcart.png" width="128" height="128" title="Корзина пуста" alt="Корзина пуста" border="0" style="border:none" /></p>
				</div>
				<script language="javascript">
					setTimeout(function(){
						$.colorbox.close();
					}, 3500);
				</script>
				<script language="javascript">ins_ajax_load_target("4ajax_module=trash", "#shopCart");</script>
HTML;
			return;
		}
		?>
		<script language="javascript">
			function DelItem(productId) {
				if($("#cnt-cart-chboximg-"+productId).attr("checked")) {
					$("#cnt-cart-chboximg-"+productId).attr("checked","");
					$("#ex_trush_count"+productId).val(   $("#cnt-cart-chboximg-"+productId).attr("last_val")   );
					$("#cnt-cart-chboximg-"+productId).removeClass("cnt-cart-chboximg-y"); 	$("#cnt-cart-chboximg-"+productId).addClass("cnt-cart-chboximg-n");
					return;
				}
				if(!$("#cnt-cart-chbox-"+productId).attr("checked")) {
					$("#cnt-cart-chboximg-"+productId).attr("checked","1");
					$("#cnt-cart-chboximg-"+productId).attr("last_val",$("#ex_trush_count"+productId).val());
					$("#ex_trush_count"+productId).val(   0   );
					$("#cnt-cart-chboximg-"+productId).addClass("cnt-cart-chboximg-y"); 	$("#cnt-cart-chboximg-"+productId).removeClass("cnt-cart-chboximg-n");
                                        // удаление элемента и обновление карзины
                                        document.getElementById('mycart_task').value='recalc';
                                        $('#trushForm').ajaxSubmit(options_recalc);
					return;
				}
			}
			<? if (  ggri('floating')==1  ) { ?>
				// ОБНОВЛЕНИЕ МОДУЛЯ - КОРЗИНА
				ins_ajax_load_target("4ajax_module=trash", "#shopCart");
				var options_recalc={	target:  null,
										success: function(){ ins_ajax_open("/?c=ex&task=viewtrush&id=<?=$_REQUEST['id'] ?>&4ajax=1&floating=1&show_order="+$('#show_order').val(), 0, 0 ); }
									};
				<? } else { ?>
				var options_recalc={	target:  "#mycart_allcart"
									};
				<? } ?>
		</script><?
		?><form name="trushForm" id="trushForm" method="post" action="shop.html"  onsubmit=" $(this).ajaxSubmit(options_recalc); return false; "><?
		?><input type="hidden" name="c" value="ex" /><?
		?><input type="hidden" name="task" value="viewtrush" /><?
		?><input type="hidden" name="mycart_task" id="mycart_task" value="recalc" /><?
		?><input type="hidden" name="show_order" id="show_order" value="" /><?
		?><input type="hidden" name="id" value="<? print $id; ?>" /><?
		?><input type="hidden" name="floating" value="<?=ggri('floating'); ?>" /><?
		?><table align="left" width="100%" border="0" cellpadding="0" cellspacing="0" class="ex_trush"><?
			?><tr class="ex_trush_row_title"><?
				?><th width="20%" align="center" class="trush_left">Количество</th><?
				?><th align="center" class="trush_row" ></th><?
				?><th align="left" class="trush_row" style="text-align:left" >Наименование</th><?
				?><th width="14%" align="center" class="trush_row">Цена</th><?
				?><th width="14%" align="center" class="trush_row">Сумма</th><?
				?><th class="trush_right">&nbsp;Удалить&nbsp;&nbsp;</th><?
			?></tr><?
		foreach ($mycart->mycart as $index => $mytovar){
			$cgood_pack = ggo ($mytovar['id'], "#__expack");
			$cgood = ggo ($cgood_pack->parent, "#__exgood");		//if ( $cgood->id==31 ) $cgood->images="";
			?><tr class="ex_trush_row"><?
				?><td class="trush_left" align="center"><?
					?><input type="text" maxlength="5" value="<? print $mytovar['qty']; ?>" name="ex_trush_count[]" id="ex_trush_count<? print $mytovar['cartid']; ?>" border="1" class="input_ajax input_gray ex_trush_count"  /><?
				?></td><?
				?><td align="center" class="trush_row_img"><?
					shadow_effect(    $component_foto->createPreviewFotoLink ( 'small', 'org', $cgood, '', ' class="highslide fancy good1" title="'.desafelySqlStr($cgood->name.' '.$cgood_pack->name).'" ', ' border="0" hspace="0" ', 'nolink'  )    );
				?></td><?
				?><td align="left" valign="top" class="trush_row_desc"><?
					?><a href="<?=$cgood->sefnamefullcat.'/'.$cgood->sefname ?>.html" target="_blank" class="incatgoodlink"><? print desafelySqlStr($cgood->name); ?></a><br><?
					?><br /><?=orderItem::getRealUsePackname(  desafelySqlStr($cgood_pack->name),  $cgood->expack_select_type  ); ?><br /><?
					?>Доставка — <?=$row->srok+14; ?> дней<br><?
					?>Артикул: <strong><?=num::fillzerro($cgood_pack->sku,8); ?></strong><?
					print $mycart->get_options_str ($mytovar);
				?><br /><br /></td><?
				?><td align="center" class="trush_row"><?=$mytovar['price'] ?>&nbsp;<? print rub1(); ?></td><?
				?><input type="hidden"  value="<? print $mytovar['cartid']; ?>" name="ex_trush_id[]" /><?
				?><td align="center" class="trush_row"><? print round( $mytovar['price_qty'] ); print '&nbsp;'.rub1(); ?></td><?
				?><td align="center" class="trush_right"><?
					?><div onclick=" DelItem('<? print $mytovar['cartid']; ?>')" class="cnt-cart-chboximg cnt-cart-chboximg-n" id="cnt-cart-chboximg-<? print $mytovar['cartid']; ?>">&nbsp;</div><?
				?></td><?
			?></tr><?
		}
			?><tr class="ex_trush_row_total"><?
				?><td align="left" class="trush_row trush_left">&nbsp;</td><?
				?><td align="left" class="trush_row">&nbsp;</td><?
				?><td align="left" class="trush_row">&nbsp;</td><?
				?><td align="right" class="trush_row"><nobr>Общая сумма:</nobr></td><?
				?><td align="center" class="trush_row"><nobr><? print $mycart->priceTotal.'&nbsp;'.rub1(); ?></nobr></td><?
				?><td align="right" class="trush_row trush_right">&nbsp;</td><?
			?></tr><?
			?><tr class="ex_trush_row_buttons"><?
				?><td colspan="6" align="right" style="text-align:right"><?
					?><a class="incatsellerlink" href="javascript: document.getElementById('mycart_task').value='recalc'; $('#trushForm').ajaxSubmit(options_recalc);  void(0);  ">Пересчитать</a>&nbsp;&nbsp;&nbsp;&nbsp;<?
					if (  $_REQUEST['show_order']!='order'  &&  $_REQUEST['show_order']!='order_contact_form'  ) { ?><a class="incatsellerlink" href="javascript: document.getElementById('show_order').value='order'; $('#trushForm').ajaxSubmit(options_recalc);  void(0); <? /* $('#ex_order_table').show(); void(0);   **  document.getElementById('mycart_task').value='order'; document.trushForm.submit(); */ ?> ">Оформить заказ</a><? }
				?></td><?
			?></tr><?
			?><tr><td colspan="6" align="right" style="text-align:right">&nbsp;</td></tr><?
		?></table><?
		?><input type="submit" style="display:none"  /><?
		?></form><?
}


/*
 * ЭТАП 2 - ВЫБОР ОФОРМЛЕНИЯ ЗАКАЗА: С РЕГИСТРАЦИЕЙ ИЛИ БЫСТРОЕ ОФОРМЛЕНИЕ БЕЗ РЕГИСТРАЦИИ
 */
function lib_select_order_type(){
global $my, $reg;
	if (  !$my->id  ){
		?><table align="left" width="100%" border="0" cellpadding="0" cellspacing="0" class="ex_order" id="ex_order_table_select" ><?
			?><tr><?
				?><td style="padding-bottom:4px;"  ><a href="javascript: ins_ajax_open('/<?=$reg['ex_seoname'] ?>/shop.html?4ajax=1&floating=1&show_order=order_register_form&show_order_register_me=1', 0, 0); void(0); ">Оформить заказ и зарегистрироваться</a></td><?
			?></tr><?
			?><tr><?
				?><td style="padding-bottom:4px;"  >Процедура регистрации не отнимет много времени.<br />После регистрации Вы сможете просматривать состояние сделанных Вами заказов через личный кабинет.</td><?
			?></tr><?

			?><tr><?
				?><td style="padding-bottom:4px; padding-top:10px;"  ><a href="javascript: ins_ajax_open('/<?=$reg['ex_seoname'] ?>/shop.html?4ajax=1&floating=1&show_order=order_contact_form&show_order_register_me=0', 0, 0); void(0); ">Оформить заказ без регистрации</a></td><?
			?></tr><?
			?><tr><?
				?><td style="padding-bottom:4px;"  >Для заказа будет создана страница в которой можно будет посмотреть статус заказа и историю его обработки.</td><?
			?></tr><?
			?><tr height="10"><?
				?><td>&nbsp;</td><?
			?></tr><?
		?></table><?
	} else {
		$_REQUEST['show_order'] = 'order_contact_form';
		$_REQUEST['show_order_register_me'] = 0;
	}
}

/*
 * ЭТАП 3 - РЕГИСТРИРУЕМ НОВОГО ПОЛЬЗОВАТЕЛЯ - ФОРМА
 */
function lib_order_register_form(){
		global $reg;
		$captcha = new captcha();    $captcha->img_id="insite_order_register_code"; 	$captcha->codeid_id="insite_order_register_codeid";		$captcha->init();
		$validate = josSpoofValue();
		?><script language="javascript">
			var options_submit_register={	target:  '#trushForm_register_server_javascript',
											beforeSubmit:  function(){	over_fade('#mycart_allcart', '#mycart_allcart', '', 0.5, 'popup'); },
											success: function(){ over_fade_hide(); }
										};
		</script><?
		?><form name="trushForm_register" id="trushForm_register" method="post" action="shop.html"  onsubmit=" $(this).ajaxSubmit(options_submit_register); return false; "><?
		?><input type="hidden" name="c" value="ex" /><?
		?><input type="hidden" name="task" value="viewtrush" /><?
		?><input type="hidden" name="mycart_task" id="mycart_task" value="order_register" /><?
		?><input type="hidden" name="show_order_register_me" id="show_order_register_me" value="<?=ggri('show_order_register_me'); ?>" /><?
		?><input type="hidden" name="id" value="<? print $id; ?>" /><?
		?><input type="hidden" name="4ajax" value="1" /><?
		?><input type="hidden" name="floating" value="<?=ggri('floating'); ?>" /><?
		?><input type="hidden" name="<?php echo $validate; ?>" id="insite_register_validate" value="1" /><?
		?><table align="center" width="100%" border="0" cellpadding="0" cellspacing="0" class="ex_order" id="ex_order_register_table" ><?
			if (  ggrr('show_order_register_me')  ){
				?><tr height="30"><?
					?><td colspan="2" style="padding-bottom:4px;" id="insite_register_server_answer" ></td><?
				?></tr><?

				?><tr class="ex_order_row_title" ><?
					?><th colspan="2" >Регистрация на сайте</th><?
				?></tr><?

				?><tr height="35px"><?
					?><td class="left">Логин</td><?
					?><td class="right"><input type="text" name="username" id="insite_register_username" size="40" value="" class="input_ajax ex_order_input input_light" maxlength="25" />&nbsp;&nbsp;</td><?
				?></tr><?
				?><tr height="35px"><?
					?><td class="left">Фамилия,&nbsp;Имя,&nbsp;Отчество&nbsp;&nbsp;</td><?
					?><td class="right"><?
						?><table cellpadding="0" cellspacing="0" width="430px"><tr>
							<td width="40%"><input type="text" name="usersurname" id="insite_register_usersurname" size="30" value="" class="input_ajax input_light" style="width:100%;" maxlength="50" /></td>
							<td width="30%"><input type="text" name="name" id="insite_register_name" size="30" value="" class="input_ajax input_light" style="width:100%;" maxlength="50" /></td>
							<td width="40%"><input type="text" name="userparentname" id="insite_register_userparentname" size="30" value="" class="input_ajax input_light" style="width:100%;" maxlength="50" /></td>
						</tr></table><?
					?></td><?
				?></tr><?
				?><tr height="35px"><?
					?><td class="left"><?php echo _REGISTER_EMAIL; ?></td><?
					?><td class="right"><input type="text" name="email" id="insite_register_email" size="40" value="" class="input_ajax ex_order_input input_light" maxlength="100" /></td><?
				?></tr><?
				?><tr height="35px"><?
					?><td class="left"><?php echo _REGISTER_PASS; ?>&nbsp;</td><?
					?><td class="right"><input type="password" name="password" id="insite_register_password" class="input_ajax ex_order_input input_light" size="40" value="" /></td><?
				?></tr><?
				?><tr height="35px"><?
					?><td class="left">Подтверждение&nbsp;пароля:&nbsp;</td><?
					?><td class="right"><input type="password" name="password2" id="insite_register_password2" class="input_ajax ex_order_input input_light" size="40" value="" /></td><?
				?></tr><?
				?><tr><?
					?><td class="left">Код&nbsp;безопасности:&nbsp;*&nbsp;</td><?
					?><td class="right" style="padding-left:2px;"><table cellpadding="0" cellspacing="0" border="0"><tr><td valign="middle" style="vertical-align:middle;"><? $captcha->codeid_input(); $captcha->show_captcha() ?></td><?
						?><td valign="middle" style="vertical-align:middle; font-size:22px; font-weight:normal; font-style:normal; font-family:Arial, Helvetica, sans-serif; ">&nbsp;&rarr;&nbsp;</td><?
						?><td valign="middle" style="vertical-align:middle; "><input type='text' name='gbcode'  id="insite_register_gbcode" maxlength='5' style='width:60px;vertical-align:middle;' class='insite_login_register' title='Введите показанный код' /></td><?
						?><td valign="middle" style="vertical-align:middle; ">&nbsp;&nbsp;&nbsp;<a href="javascript:spamfixreload('insite_order_register_code', '<?=$captcha->codeid ?>')" >не&nbsp;вижу</a></td><?
					?></tr></table></td><?
				?></tr><?
				?><tr class="order_button"><?
					?><td colspan="2" align="right" class="order_button" ><a href="javascript: $('#trushForm_register').ajaxSubmit(options_submit_register); void(0); ">Сохранить</a></td><?
				?></tr><?
			?></table><div id="trushForm_register_server_javascript"></div><?
			?></form><?
			}
}

/*
 * ЭТАП 4 - КОНТАКТНАЯ ИНФОРМАЦИЯ ДЛЯ ЗАКАЗА - ФОРМА
 */
function lib_order_contact_form(){
	global $reg, $my;
		$dop->fio = "";
		$dop->username="";
		$dop->email="(не обязательно)";
                $dop->address="";
		if (  ggri('show_order_register_me')==1  &&  ggri('uid')>0  ){  // т.е. если мы предварительно зарегистрировались (быстрая регистрация при оформлении заказа),
                                                                                // то теперь введеные контактные данные берем из учетной записи
			$reg_user = ggo (  ggri('uid')  ,   "#__users"  );
			if (  $reg_user->username!=''  )$dop->fio     = userfio($reg_user); else $dop->fio="";
			if (  $reg_user->email!=''  ) 	$dop->email   = $reg_user->email;   else $dop->email="(не обязательно)";
			if (  $reg_user->tel!=''  ) 	$dop->tel     = $reg_user->tel;     else $dop->tel="";
                        if (  $reg_user->address!=''  ) $dop->address = $reg_user->address; else $dop->address="";
		}
		else if (  $my->id  ){
			if (  $my->username!=''  ) 	$dop->fio 	= userfio($my); else $dop->fio="";
			if (  $my->email!=''  ) 	$dop->email 	= $my->email; 	else $dop->email="(не обязательно)";
			if (  $my->tel!=''  ) 		$dop->tel 	= $my->tel; 	else $dop->tel="";
                        if (  $my->address!=''  )	$dop->address 	= $my->address; else $dop->address="";
		}
		/* ВВОДИМ ДОПОЛНИТЕЛЬНУЮ ИНФОРМАЦИЮ */
		?><script language="javascript">
			var options_submit_order={	target:  '#trushForm_contact_server_javascript',
										beforeSubmit:  function(){	over_fade('#mycart_allcart', '#mycart_allcart', '', 0.5, 'popup'); },
										success: function(){ over_fade_hide(); }
										/*dataType: 'script' */
								};
		</script><?
		$myform = new insiteform();
		$captcha = new captcha();    $captcha->img_id="insite_order_register_code"; 	$captcha->codeid_id="insite_order_register_codeid";		$captcha->init();
		?><form name="trushForm_contact" id="trushForm_contact" method="post" action="shop.html"  onsubmit=" $(this).ajaxSubmit(options_submit_order); return false; "><?
		?><input type="hidden" name="c" value="ex" /><?
		?><input type="hidden" name="task" value="viewtrush" /><?
		?><input type="hidden" name="mycart_task" id="mycart_task" value="submit_order" /><?
		?><input type="hidden" name="show_order_register_me" id="show_order_register_me" value="<?=ggri('show_order_register_me'); ?>" /><?
		?><input type="hidden" name="id" value="<? print $id; ?>" /><?
		?><input type="hidden" name="4ajax" value="1" /><?
		?><input type="hidden" name="floating" value="<?=ggri('floating'); ?>" /><?
		?><input type="hidden" name="show_order_register_me" value="<?=ggri('show_order_register_me'); ?>" /><?
		?><input type="hidden" name="uid" value="<?=ggri('uid'); ?>" /><?

		?><table align="center" width="100%" border="0" cellpadding="0" cellspacing="0" class="ex_order" id="ex_order_contact_table" ><?
			?><tr height="30"><?
				?><td colspan="2" style="padding-bottom:4px; " >&nbsp;<span id="trushForm_contact_server_answer">&nbsp;</span></td><?
			?></tr><?

			?><tr class="ex_order_row_title" ><?
				?><th colspan="2" >Дополнительная информация</th><?
			?></tr><?

			?><tr height="35px"><?
				?><td class="left">Фамилия Имя Отчество&nbsp;</td><?
				?><td class="right"><input <? $myform->make_java_text_effect('u_fio', 'input_light'); ?> name="u_fio" id="u_fio" value="<?=($dop->fio); ?>"  title="<?=($dop->fio); ?>" size="70"  class="input_ajax input_gray ex_order_input" /></td><?
			?></tr><?
			?><tr height="35px"><?
				?><td class="left">Контактный телефон&nbsp;</td><?
				?><td align="left" class="right"><input name="u_tel" value="<?=($dop->tel); ?>" id="u_tel" class="input_ajax input_light ex_order_input" size="70" /></td><?
			?></tr><?
			?><tr height="35px"><?
				?><td class="left">Адрес электронной почты&nbsp;</td><?
				?><td class="right"><input <? $myform->make_java_text_effect('u_mail', 'input_light'); ?> name="u_mail" id="u_mail" size="70" class="input_ajax input_gray ex_order_input" value="<?=($dop->email); ?>" title="<?=($dop->email); ?>" /></td><?
			?></tr><?
			?><tr height="35px"><?
				?><td class="left">Адрес доставки</td><?
                                ?><td class="right"><input name="u_address" id="u_address" value="<?=($dop->address) ?>" size="70" class="input_ajax input_light ex_order_input" /></td><?
			?></tr><?
			?><tr height="35px"><?
				?><td class="left last">Способ оплаты</td><?
				?><td align="left" class="right last"><?
					$paymethods_sql = ggsql ("select * from #__expaymethod; ");
					foreach ($paymethods_sql as $paymethod){
						$paymethods[] = mosHTML::makeOption( $paymethod->id, $paymethod->name );
					}
					print mosHTML::selectList( $paymethods, 'expaymethod', ' size="1" class="input_ajax input_light ex_order_select" id="expaymethod" ', 'value', 'text', 1 );
				?></td><?
			?></tr><?
			?><tr height="35px"><?
				?><td class="left">Комментарий</td><?
				?><td align="left" class="right"><textarea  <? $myform->make_java_text_effect('u_note', 'input_light'); ?> name="u_note" id="u_note" cols="53" rows="5" class="input_ajax input_gray ex_order_input" title="(не обязательно)" >(не обязательно)</textarea></td><?
			?></tr><?
			?><tr class="order_button"><?
				?><td colspan="2" align="right" class="order_button" ><a href="javascript: $('#trushForm_contact').ajaxSubmit(options_submit_order); void(0); ">Отправить заявку</a></td><?
			?></tr><?
			?><tr height="5" ><?
				?><td colspan="2" id="trushForm_contact_server_javascript" ></td><?
			?></tr><?

		?></table><?
		/* END//ВВОДИМ ДОПОЛНИТЕЛЬНУЮ ИНФОРМАЦИЮ */
		?></form><?
}
/*
 * ЭТАП 5 - ВЫВОД БЛАГОДАРСТВЕННОГО ПИСЬМА И ССЫЛКА НА ЗАКАЗ
 */
function thank(){
    global $reg;
    ?><table align="center" width="100%" border="0" cellpadding="0" cellspacing="0" class="ex_order" id="ex_order_contact_table" ><?
        ?><tr height="30"><?
                ?><td colspan="2" style="padding-bottom:4px; " >&nbsp;</td><?
        ?></tr><?
        ?><tr><?
            ?><td><?
                $exgfg = ggo (1, "#__excfg");
                print desafelySqlStr($exgfg->thanku);
	if($_REQUEST['code']) { ?>Вы можете посмотреть статус вашего заказа и историю его обработки онлайн <a href="/cab_orders?order=<?=$_REQUEST['code']; ?>">на странице заказа</a><? }
            ?></td><?
        ?></tr><?
     ?></table><?
}