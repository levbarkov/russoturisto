<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
if (!defined( 'ICSEX_LIB' )) require_once( 'component/ex/ex_lib.php' );
global $reg;
if (  !isset($_REQUEST['4ajax_module'])  ){	?>
<style>
	a.imstrush_link:link, a.imstrush_link:visited{
		text-decoration:none;
		font-size:11px;
		color:#444444;
		font-weight:normal;
	}
	a.imstrush_link:hover{
		text-decoration:underline;
	}
</style>
<div id="shopCart" class="shop-cart">
<?
}
	$mycart = new mycart();
	$mycart->load();
	/*
	 * КЛАССИЧЕСКИЙ ВЫЗОВ КОРЗИНЫ В НОВОЙ СТРАНИЦЕ
	 */
//	$exurl  = '/'.$reg['ex_seoname'];	// ggtr (  ggrr('task')  );
//	if (  ggrr('c')=='ex'  ){
//		if (  ggrr('task')=='excat'  and  ggri('id')>0  )   {  $exurl = $reg['mainobj']->sefnamefull."/".$reg['mainobj']->sefname; }
//		else if (  ggrr('task')=='view'  )   {  $exurl = $reg['mainobj']->sefnamefullcat; }
//	}
//	$cart_url = $exurl."/shop.html";
	/*
	 * ВЫЗОВ КОРЗИНЫ В POPUP ОКНЕ
	 */
	$cart_url = "javascript: ins_ajax_open('/".$reg['ex_seoname']."/shop.html?4ajax=1&floating=1', 0, 0); void(0); ";

	
	if (  $mycart->mycart_index==0  ){ ?><div style="padding-left:15px;"><a href="<?=$cart_url ?>">в корзине <br />нет товаров</a></div><?
		if (  !isset($_REQUEST['4ajax_module'])  ){	?></div><? }
		return; 
	}
	?><table width="210" border="0" cellpadding="0" cellspacing="0" align="center" class="mytrush"><?
		$mycart->recalcPrice();
		?><tr class="mytrush_title"><?
			?><th colspan="4" ><a href="<?=$cart_url ?>">В корзине <?=$mycart->goodsInShop ?> <?=num::morph($mycart->goodsInShop, 'товар','товара','товаров') ?></a></th><?
		?></tr><?
		foreach ($mycart->mycart as $index => $mytovar){
		$cgood_pack = ggo ($mytovar['id'], "#__expack");
		$cgood = ggo ($cgood_pack->parent, "#__exgood");
		?><tr class="mytrush_row"><?
			?><td width="50%" class="left" ><a class="imstrush_link" href="<?=$cgood->sefnamefullcat.'/'.$cgood->sefname.'.html'; ?>" ><?=stripslashes($cgood->name); ?><br /><?=orderItem::getRealUsePackname('('.stripslashes($cgood_pack->name).')',  $cgood->expack_select_type  ); ?><?=$mycart->get_options_str ($mytovar) ?></a></td><?
			?><td width="30%" class="cell" ><nobr><?=$mytovar['price_qty'].'&nbsp;'.rub0(); ?></nobr></td><?
			?><td width="10%" class="cell" ><nobr> x <a class="mytrash_count" title="Изменить количество" href="#" onclick="recountItem('<?=$mytovar['cartid'] ?>', this); return false; " ><?=$mytovar['qty'] ?></a></nobr></td><?
			?><td width="10%" class="right" ><a title="Удалить товар" href="#" onclick="deleteItem('<?=$mytovar['cartid'] ?>', this); return false; "><img height="16" border="0" width="16" alt="Удалить" src="/component/ex/delme.gif" class="delme"></a></td><?
		?></tr><?
		}
		// ВЫВОДИМ ИТОГ
		?><tr class="mytrush_total"><?
			?><td>Общая сумма:</td><?
			?><td colspan="3"><nobr><?=$mycart->priceTotal.'&nbsp;'.rub2(); ?></nobr></td><?
		?></tr><?
	?></table><?
	if (  !isset($_REQUEST['4ajax_module'])  ){	?></div><? }
?>