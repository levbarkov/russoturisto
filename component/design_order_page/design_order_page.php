<?
global $reg;
$good_packs = array (56, 601);
	?><table align="left" width="90%" border="0" cellpadding="0" cellspacing="0" class="ex_trush"><?
		?><tr class="ex_trush_row_title"><?
			?><th width="20%" align="center" class="trush_left">Количество</th><?
			?><th align="center" class="trush_row" ></th><?
			?><th align="left" class="trush_row" style="text-align:left" >Наименование</th><?
			?><th width="14%" align="center" class="trush_row">Цена</th><?
			?><th width="14%" align="center" class="trush_row trush_right">Сумма</th><?

		?></tr><?
	foreach ($good_packs as $good_pack){
		$cgood_pack = ggo ($good_pack, "#__expack");
		$cgood = ggo ($cgood_pack->parent, "#__exgood");		//if ( $cgood->id==31 ) $cgood->images="";
		?><tr class="ex_trush_row"><?
			?><td class="trush_left" align="center"><?
				?>1 шт.<br />(ед. берутся из expack "unit")<?
			?></td><?
			?><td align="center" class="trush_row_img"><?
				if (  $cgood->small!=''  )	shadow_effect('<a href="/images/ex/good/'.stripslashes($cgood->org).'" class="highslide fancy good1" title="'.stripslashes($cgood->name.' '.$cgood_pack->name).'"><img src="/images/ex/good/'.stripslashes($cgood->small).'" hspace="0" border="0" ></a>');
				else { ?><img alt="" style="border: 0px none;" border="0" src="<?=$reg['exgoodnoimage'] ?>"/><? }
			?></td><?
			?><td align="left" valign="top" class="trush_row_desc"><?
				?><a href="<?=$cgood->sefnamefullcat.'/'.$cgood->sefname ?>.html" target="_blank" class="incatgoodlink"><? print stripslashes($cgood->name); ?></a><br><?
				?><br /><?=stripslashes($cgood_pack->name); ?><br /><?
				?>Доставка — <?=$row->srok+14; ?> дней<br><?
				?>Артикул: <strong><?=num::fillzerro($cgood_pack->sku,8); ?></strong><br><?
			?></td><?
			?><td align="center" class="trush_row">255&nbsp;<? print rub1(); ?></td><?
			?><td align="center" class="trush_row trush_right"><? print 255; print '&nbsp;'.rub1(); ?></td><?
		?></tr><?
	}
		?><tr class="ex_trush_row_total"><?
			?><td align="left" class="trush_row trush_left">&nbsp;</td><?
			?><td align="left" class="trush_row">&nbsp;</td><?
			?><td align="left" class="trush_row">&nbsp;</td><?
			?><td align="right" class="trush_row"><nobr>Общая сумма:</nobr></td><?
			?><td align="center" class="trush_row trush_right"><nobr><? print (255+255).' '.rub1(); ?></nobr></td><?

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
			?><td class="right">Сотников Георгий Андреевич</td><?
		?></tr><?
		?><tr height="35px"><?
			?><td class="left">Контактный телефон</td><?
			?><td align="left" class="right">+7 904 799988</td><?
		?></tr><?
		?><tr height="35px"><?
			?><td class="left">Адрес электронной почты</td><?
			?><td class="right">mazxamail@mail.ru</td><?
		?></tr><?
		?><tr height="35px"><?
			?><td class="left">Адрес доставки</td><?
			?><td class="right">г. Красноярск, 9 мая 40а - 72</td><?
		?></tr><?
		?><tr height="35px"><?
			?><td class="left last">Способ оплаты</td><?
			?><td align="left" class="right last">Оплата безналичным расчетом</td><?
		?></tr><?
		?><tr height="35px"><?
			?><td class="left last"></td><?
			?><td align="left" class="right last">В зависимости от способа оплаты<br />например: Рапечатать квитанцию для сбербанка</td><?
		?></tr><?

		?><tr height="35px"><?
			?><td class="left">Комментарий</td><?
			?><td align="left" class="right">Все круто!!!</td><?
		?></tr><?
		?><tr height="35px"><?
			?><td class="left">История работы с заказом</td><?
			?><td align="left" class="right">
				<table width="90%">
					<tr>
						<td><strong>Время</strong></td>
						<td><strong>Статус</strong></td>
						<td></td>
					</tr>
					<tr>
						<td>10.06.2010 19:15:29</td>
						<td>Отправлен</td>
						<td></td>
					</tr>
					<tr>
						<td>07.06.2010 19:44:20</td>
						<td>Оплачен</td>
						<td><a href="javascript: ins_ajax_open('/kontakty?4ajax=1'); void(0); " class="order_note">Примечание</a></td>
					</tr>
					<tr>
						<td>07.06.2010 14:28:39</td>
						<td>В обработке</td>
						<td><a href="javascript: ins_ajax_open('/kontakty?4ajax=1'); void(0); " class="order_note">Примечание</a></td>
					</tr>
					<tr>
						<td>06.06.2010 18:45:00</td>
						<td>Новый</td>
						<td></td>
					</tr>

				</table>
			</td><?
		?></tr><?

		?><tr class="order_button"><?
			?><td colspan="2" align="right" class="order_button" ><a href="/cab">Вернуться в личный кабинет</a></td><?
		?></tr><?
	?></table><div style="clear:both;"></div><?	
	$comments = new comments('order', $reg['db'], $reg);
	$comments->comments_here(1, 'say_question');

?>