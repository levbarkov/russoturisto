<?php
global $reg;
defined( '_VALID_INSITE' ) or die( 'Direct Access to this location is not allowed.' );


if (  isset($_REQUEST['order'])  ) {
	require_once(site_path."/lib/saver.php");
	$code = $_REQUEST['order'];
	$so = new shopOrder($reg);
	$so->loadByCode($code);
	
	$icatway[0]->name="Личный кабинет";
	$icatway[0]->url="/cab";
	$icatway[1]->name="Мои заказы";
	$icatway[1]->url="/cab_orders";
	if($so->id == '') 	$icatway = i24pathadd(  $icatway, "Заказа не найден", ""  );
	else				$icatway = i24pathadd(  $icatway, "Страница заказа #".$so->id, ""  );
	
	i24pwprint ($icatway);
	return;
}


global $database, $my, $acl, $mainframe, $reg;
$iway[0]->name="Личный кабинет";
$iway[0]->url="/cab";
$iway[1]->name="Мои заказы";
$iway[1]->url="";

i24pwprint ($iway);

?>