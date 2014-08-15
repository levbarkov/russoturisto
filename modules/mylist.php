<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
if (!defined( 'ICSEX_LIB' )) require_once( 'component/ex/ex_lib.php' );
global $reg, $my;
//ggtr ($reg['task']);;
$mylist_link = "/".$reg['ex_seoname']."/mylist.html";

if (  !isset($_REQUEST['4ajax_module'])  ){	?><div id="mylist_div_wrapper" class="mylist_div"><? }
	$mylist = new mylist();
	if (  !$my->id  ){ ?><a id="mylist" href="<?=$mylist_link ?>">в избранном <span id="mylist-amount">нет товаров</span></a><?
		if (  !isset($_REQUEST['4ajax_module'])  ){	?></div><? }
		return; 
	}
	
	$mylist_count = $mylist->get_list_count($my->id, 'ex');
	?><a id="mylist" href="<?=$mylist_link ?>">в избранном <span id="mylist-amount"><?=$mylist_count; ?> товара</span></a><?
	if (  !isset($_REQUEST['4ajax_module'])  ){	?></div><? }
?>