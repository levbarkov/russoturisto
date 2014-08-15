<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
require_once(site_path."/lib/saver.php");
global $reg, $reg, $my, $task; 

?><link title="green" href="/includes/js/calendar/calendar-mos.css" media="all" type="text/css" rel="stylesheet">
<script src="/includes/js/calendar/calendar_mini.js" type="text/javascript"></script>
<script src="/includes/js/calendar/lang/calendar-en.js" type="text/javascript"></script><?

$cid = josGetArrayInts( 'cid' );
$limit      = intval( mosGetParam( $_REQUEST, 'limit', $reg['iConfig_list_limit'] ) );
$limitstart = intval( mosGetParam( $_REQUEST, 'limitstart', 0 ) );	

switch ($task) {
	case "remove":
		remove_orders();
		break;
	default:
		search_orders($_REQUEST['str'], $_REQUEST['start'], $_REQUEST['finish'], $_REQUEST['fio'], $limitstart, $limit);
}

/**
 * ПОИСК ЗАКАЗА
 *
 * @global <type> $reg
 * @param <type> $str
 * @param <type> $start
 * @param <type> $finish
 * @param <type> $fio
 */
function search_orders($str, $start, $finish, $fio, $limitstart, $limit)
{
    global $reg;    
    $db = $reg['db'];
    $where = Array();
    
    /*
     * УЧЕТ Даты:
     */
    if (  $start==''  )  $start  = date("Y-m-d");
    if (  $finish==''  ) $finish = date("Y-m-d");
    if (  !($start==date("Y-m-d")  and  $start==$finish)  ){
		$t1 =  explode("-", $start);
		$t2 = explode("-", $finish);
		$t1 = mktime(0, 0, 0,  $t1[1], $t1[2], $t1[0]);
		$t2 = mktime(23,59,59, $t2[1], $t2[2], $t2[0]);
		$where[] = " o.create_time BETWEEN ".$t1." AND ".$t2." ";
    }
    
    /*
     * УЧЕТ НОМЕРА ЗАКАЗА
     * можно вводить 24-34
     */
    $arr = Array();
    $str = str_replace(" ", '', $str);

    if(preg_match("/,/", $str))
    {
        $tmp = explode(",", $str);
        foreach($tmp as $s){
                if(preg_match("/-/", $s)){
                          $tmp2 = explode("-", $s);
                          $a1 = intval($tmp2[0]);
                          $a2 = intval($tmp2[1]);
                          if($a1 < $a2){  for($i = $a1; $i<=$a2; $i++) $arr[] = $i; }
                }
                else $arr[] = intval($s);
        }
    }
    elseif(preg_match("/-/", $str)){
          $tmp2 = explode("-", $str);
          $a1 = intval($tmp2[0]);
          $a2 = intval($tmp2[1]);
          if($a1 < $a2){    for($i = $a1; $i<=$a2; $i++) $arr[] = $i;    }
    }
    else $arr[] = intval($str);

    if(  count($arr) > 0 && ( $arr[0] != '0')  ){
          $string = join(",", $arr);
          $where[] = " o.id IN ( ".$string.") ";
    }

    /*
     * УЧЕТ МЕНЕДЖЕРОВ
     */
    $manager_join = "";
    if (  ggri('showorders')>0  and  ggri('showmanagers')==-100  ) $_REQUEST['showmanagers'] = -78;  // при выборе статуса менежеров переводим с БЕЗ МЕНЕДЖЕРОВ на ВСЕ
    if (  ggri('showmanagers')>0  ) $manager_join = " INNER JOIN #__ordermanagers as om ON ( om.order_id=o.id  AND  om.manager_id=".ggri('showmanagers')." ) ";
    if (  ggri('showmanagers')==-100  ){    // отобразить БЕЗ МЕНЕДЖЕРОВ
        $manager_join = " LEFT JOIN #__ordermanagers as om ON ( om.order_id=o.id ) ";
        $where[] = " om.manager_id IS null ";
    }

    /*
     * ПОИСК ПО ФАМИЛИИ ПОКУПАТЕЛЯ
     */
    $fio = trim($fio);
    if(strlen($fio) > 0) { $where[] = " o.clientFIO LIKE \"%".$fio."%\" "; }

    /*
     * УЧЕТ СТАТУСА ЗАКАЗА
     */
    if (  ggri('showorders')>0  )$where[] = " o.status_id=".ggri('showorders')." ";


    /*
     * ФОРМИРУЕМ WHERE ЗАПРОС
     */
    if(count($where) > 0) { $where = " WHERE ".join(" AND ", $where); }
    else $where = "";

    $query = "SELECT COUNT(o.id) FROM  #__orders as o
                   $manager_join
                   $where ";

    $total = ggsqlr (  $query  ); //ggtr ($total);

    $query = str_replace("COUNT(o.id)", "o.id", $query)."  ORDER BY o.create_time DESC  ";
    $db->setQuery($query, $limitstart, $limit );
    $objs = $db->loadResultArray();
    // ggtr ($reg['db']->_sql);
    
    table_header();
    if(  count($objs) > 0  ){
      foreach($objs as $o){
	  if(trim($o) != ""){
	      $shopOrder = new shopOrder($reg);
	      $shopOrder->load($o);
	      orderAdminRec($shopOrder);
	  }
      }
    }
    table_footer($total, $limitstart, $limit);
}

function orderAdminRec($shopOrder)
{
    global $reg;
    $man = "";
    if(is_array($shopOrder->managers)){
        foreach($shopOrder->managers as $manager) { 
            $man .= "<a href=\"javascript: ins_ajax_load_target('ca=shopajax&task=remmanager&orderid=".$shopOrder->id."&manager_id=".$manager."&4ajax=1', '#shop_".$shopOrder->id."'); void(0);\" ><img align='top' src='/iadmin/images/del.png' border='0' /></a>&nbsp;&nbsp;<a href='/iadmin/index2.php?ca=users&task=editA&id=".$manager."&hidemainmenu=1' target='_blank' alt='Перейти к редактированию пользователя'>". $shopOrder->getUserName($manager). "&nbsp;&nbsp;(".$shopOrder->getManagerOrders($manager).")</a> <br />";
        }
    }
	?><tr>
		<td><?=$shopOrder->id ?></td>
		<td><?=$shopOrder->clientFIO; ?> <br /><? if (  $shopOrder->uid  ){ ?><a target="_blank" href="/iadmin/index2.php?ca=users&task=editA&id=<?=$shopOrder->uid ?>&hidemainmenu=1" title="открыть свойства пользователя"><? } ?><?=$shopOrder->getUserName($shopOrder->uid) ?><? if (  $shopOrder->uid  ) print '</a>' ?></td>
		<td nowrap="nowrap" style="white-space:nowrap;"><?=date("d/m/Y   H:i:s", $shopOrder->create_time) ?><br /><a target="_blank" href="<?=site_url."/cab_orders?order=".$shopOrder->code; ?>" title="открыть страницу заказа">(на&nbsp;страницу&nbsp;заказа)</a></td>
		<td><a href="javascript: ins_ajax_open('?ca=shopajax&task=showorderitems&orderid=<?=$shopOrder->id ?>&4ajax=1', 730, 470); void(0);" class='dotted'><?=count($shopOrder->items) ?> шт.<br />(подробно)</a></td>
		<td><?=$shopOrder->recalcPrice() ?> <small>руб.</small></td>
		<td><div id ='shop_<?=$shopOrder->id ?>'> <?=$man ?> </div><br /><a href="javascript: ins_ajax_open('?ca=shopajax&task=newmanager&orderid=<?=$shopOrder->id ?>&4ajax=1', 380, 570); void(0);" class="nohover"><img src="/iadmin/images/ins.png" align="top" border="0" />&nbsp;&nbsp;</a><a href="javascript: ins_ajax_open('?ca=shopajax&task=newmanager&orderid=<?=$shopOrder->id ?>&4ajax=1', 380, 570); void(0);">Добавить</a></td>
		<td><input type="checkbox" id="ident<?=$shopOrder->id ?>" name="cid[]" value="<?=$shopOrder->id ?>" onclick="isChecked(this.checked);"/></td>
	</tr><? 
}

function remove_orders(){
	global $reg, $_REQUEST;
	$cid = $_REQUEST['cid'];        
	$c = 0;
    
	if(!is_array($cid)){
		echo "Не выбраны заказы";
		return;
	}
        
	foreach($cid as $key => $value){
		$so = new shopOrder($reg);
		$so->load($value);
		$so->delete();
		$c++;
	}
	
	$msg = "Удалено заказов: " . $c;
	mosRedirect('index2.php?ca=shop', $msg);
}

/* Шапка */
function table_header(){
    global $_REQUEST, $reg;
    if(!isset($_REQUEST['start'])) $_REQUEST['start'] = date("Y-m-d");
    if(!isset($_REQUEST['finish'])) $_REQUEST['finish'] = date("Y-m-d");

    print "<div id='searchform'>";
  
  	$showOrders = array();
	$showOrders[] = mosHTML::makeOption( "-78", "Все");
	$more_options = ggsql ( "SELECT * FROM #__orderstatuslist ORDER BY ordering " );
	foreach ($more_options as $showOrder){
		$showOrders[] = mosHTML::makeOption(  $showOrder->id, desafelySqlStr($showOrder->name)  );
	}
        $showManadger[] = mosHTML::makeOption( "-100", "Без менеджеров");
	$showManadger[] = mosHTML::makeOption( "-78", "Все");
	$more_options = ggsql ( "SELECT id, name, userparentname, gid FROM #__users WHERE gid>=23 " );
	foreach ($more_options as $showOrder){
		$showManadger[] = mosHTML::makeOption(  $showOrder->id, desafelySqlStr($showOrder->name.' '.$showOrder->userparentname)  );
	}
    ?>
	<form action="index2.php" name="searchForm" method="GET">
	<table border='1' class='adminlist'>
	  <tr>
	  	<td nowrap="nowrap" style="white-space:nowrap; ">Отображать:&nbsp;<? print mosHTML::selectList( $showOrders, 'showorders', 'class="inputbox" onchange="document.searchForm.submit();" size="1" ', 'value', 'text', ggri('showorders') ); ?></td>
		<td nowrap="nowrap" style="white-space:nowrap; ">Менеджер:&nbsp;<? print mosHTML::selectList( $showManadger, 'showmanagers', 'class="inputbox" onchange="document.searchForm.submit();" size="1" ', 'value', 'text', ggri('showmanagers') ); ?></td>
		<td nowrap="nowrap" style="white-space:nowrap; ">Номер заказа: <input type="text" name="str" style="width:59px" value="<?=$_REQUEST['str']; ?>" /></td>
	  	<td nowrap="nowrap" style="white-space:nowrap; ">Дата: <input onclick="return showCalendar('start', 'y-mm-dd');" type="text" id="start" value="<?=$_REQUEST['start']; ?>"  name="start" class="date1 dp-applied" style="width:59px; " size="15">
		 - <input type="text" onclick="return showCalendar('finish', 'y-mm-dd');" value="<?=$_REQUEST['finish']; ?>" id = "finish" name="finish" class="date1 dp-applied" size="15" style="width:59px; "></td>
		<td nowrap="nowrap" style="white-space:nowrap; ">ФИО <input type="text" size="30" name="fio" value="<?=$_REQUEST['fio']; ?>"/><input type="submit" value="Искать" /></td>
	  </tr>
	</table>
		<input type="hidden" name="ca" value="shop" />
		<input type="hidden" name="task" value="search" />		
	 </form>
    <?
    print "&nbsp;</div>";
    print '<form action="index2.php" name="adminForm">';
    print "<table border='1' class='adminlist'>";
    print "<tr><th style='width:50px'>Заказ №</th><th width='25%'>Покупатель</th><th width='10%'>Дата заказа</th><th width='10%'>Товары</th><th width='10%'>Цена</th><th>Менеджеры</th><th style='width:25px'>Удалить</th></tr>";
}

function table_footer($number, $limitstart, $limit)
{
  global $_REQUEST;
    print "</table>";

  include_once(site_path."/iadmin/includes/pageNavigation.php");

  $pageNav = new mosPageNav($number, $limitstart, $limit);
  print $pageNav->getListFooter();

?>
		<input type="hidden" name="ca" value="shop" />
		<input type="hidden" name="task" value="<?=$_REQUEST['task']; ?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
	</form>
<?    
}
?>