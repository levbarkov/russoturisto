<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
require_once(site_path."/lib/saver.php");
global $reg, $my, $task;
?><link title="green" href="/includes/js/calendar/calendar-mos.css" media="all" type="text/css" rel="stylesheet">
<script src="/includes/js/calendar/calendar_mini.js" type="text/javascript"></script>
<script src="/includes/js/calendar/lang/calendar-en.js" type="text/javascript"></script><?
$cid = josGetArrayInts( 'cid' );

$limit 		= intval( mosGetParam( $_REQUEST, 'limit', $reg['iConfig_list_limit'] ) );
$limitstart 	= intval( mosGetParam( $_REQUEST, 'limitstart', 0 ) );

switch ($task) {
    case "addnewstatus": { addnewstatus(); } break;
    case "apply" :
    case "save"  : { savenewstatus(); } break;
    default: { search_orders( ggrr('str'), ggrr('start'), ggrr('finish'), ggrr('fio'), $limitstart, $limit ); }  break;
}

/** Поиск заказа */
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
    if (  !$reg['my']->id  ) return 'нет менеджера';

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
    if(count($where) > 0) { $where = " AND ".join(" AND ", $where); }
    else $where = "";

    $query = "SELECT COUNT(o.id) FROM  #__orders as o
                   INNER JOIN #__ordermanagers as om ON ( om.order_id=o.id  AND  om.manager_id=".$reg['my']->id."  )
                   $where ";

    $total = ggsqlr (  $query  ); //ggtr ($total);

    $query = str_replace("COUNT(o.id)", "o.id", $query)."  ORDER BY o.create_time DESC  ";
    $db->setQuery($query, $limitstart, $limit );
    $objs = $db->loadResultArray();
    // ggtr ($reg['db']->_sql);

    print "<div id='searchform'>";
	
  	$showOrders = array();
	$showOrders[] = mosHTML::makeOption( "-78", "Все");
	$more_options = ggsql ( "SELECT * FROM #__orderstatuslist ORDER BY ordering " );
	foreach ($more_options as $showOrder){
		$showOrders[] = mosHTML::makeOption(  $showOrder->id, desafelySqlStr($showOrder->name)  );
	}
 ?>
	<form action="index2.php" method="POST" name="searchForm">
		<table border='1' class='adminlist'>
		  <tr>
			<td nowrap="nowrap" style="white-space:nowrap; ">Отображать:&nbsp;<? print mosHTML::selectList( $showOrders, 'showorders', 'class="inputbox" onchange="document.searchForm.submit();" size="1" ', 'value', 'text', ggri('showorders') ); ?></td>
			<td nowrap="nowrap" style="white-space:nowrap; ">Номер заказа: <input type="text" name="str" style="width:59px" value="<?=$_REQUEST['str']; ?>" /></td>
			<td nowrap="nowrap" style="white-space:nowrap; ">Дата: <input onclick="return showCalendar('start', 'y-mm-dd');" type="text" id="start" value="<?=$start; ?>"  name="start" class="date1 dp-applied" style="width:59px; " size="15">
			 - <input type="text" onclick="return showCalendar('finish', 'y-mm-dd');" value="<?=$finish; ?>" id = "finish" name="finish" class="date1 dp-applied" size="15" style="width:59px; "></td>
			<td nowrap="nowrap" style="white-space:nowrap; ">ФИО <input type="text" size="30" name="fio" value="<?=$_REQUEST['fio']; ?>"/><input type="submit" value="Искать" /></td>
		  </tr>
		</table>
		<input type="hidden" name="ca" value="shopmanager" />
		<input type="hidden" name="task" value="search" />		
	 </form>
<?
    print "&nbsp;</div>";
    print '<form action="index2.php" name="adminForm" method="GET">';
    print "<table border='1' class='adminlist'>";
    print "<tr><th style='width:50px'>Заказ №</th><th width='25%'>Покупатель</th><th width='10%'>Дата заказа</th><th width='10%'>Товары</th><th width='10%'>Цена</th><th>Статус</th><th width='20%'>Примечание</th><th width='5%'>Вопросы</th></tr>";

    if(count($objs) > 0)
    {
      foreach($objs as $o)
      {
	  if(trim($o) != ""){
	      $shopOrder = new shopOrder($reg);
	      $shopOrder->load($o);            
	      orderAdminRec($shopOrder);
	  }
      }
    }   
    table_footer($total, $limitstart, $limit);
}

function table_footer($number, $limitstart, $limit){
  global $_REQUEST;
    print "</table>";

  include_once(site_path."/iadmin/includes/pageNavigation.php");

  $pageNav = new mosPageNav($number, $limitstart, $limit);
  print $pageNav->getListFooter();

?>
		<input type="hidden" name="ca" value="shopmanager" />
		<input type="hidden" name="task" value="<?=$_REQUEST['task'];?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
	</form>
<?
}

/** Добавление нового статуса */
function addnewstatus()
{
  global $reg, $_REQUEST;
  $db = $reg['db'];
  $id = intval($_REQUEST['orderid']);
  $so = new shopOrder($reg);
  $so->load($id);
  $sel = false;
  $status = intval($so->statusFull->status_id);
  if($status != 0)
  {
      $db->setQuery("SELECT `ordering` FROM #__orderstatuslist WHERE id = ".$status);
      $db->query();
      $status = $db->loadResult();      
  }
  $status = intval($status);  
  ?>
	<script>
      function submForm()
      {
    	  /*var note = document.forms['adminForm'].note.value;
	      var stat = document.forms['adminForm'].newstatus.value;	      */
		  $('#stat_<?=$id; ?>').html('<img src="/iadmin/images/loading16.gif" width="16" height="16" align="absmiddle" /> Сохранение...');
	      var note = $("#note").val();
	      var stat = $("#newstatus").val();
	      var str = 'ca=shopmanager&4ajax=1&task=save&id=<?=$id; ?>&newstatus='+stat+'&note=' + note;
	      ins_ajax_load_target (str,  '#stat_<?=$id; ?>'); 
	      $.fn.colorbox.close();
      }
      </script>

      <form name="adminForm" action="index2.php" method="post" enctype="multipart/form-data" >
      <input type="hidden" name="id" value="<?=$id; ?>" />
      <input type="hidden" name="task" value="save"  />
      <input type="hidden" name="ca" value="<?=$_REQUEST['ca']; ?>" />
  <?


  print "<b>Новый статус: </b>&nbsp;&nbsp; ";  
  $db->setQuery("SELECT * from #__orderstatuslist order by `ordering` ASC, id DESC");
  $db->query();
  $stat = $db->loadObjectList();	
  if(is_array($stat))
  {
      print "<select name='newstatus' id = 'newstatus' style='width:200px'>\n";
      foreach ($stat as $s)
      {
	  $selected = "";
	  if(!$sel)
	  {
	    if($s->ordering > $status) { $selected = " SELECTED "; $sel = true; }
	  }

	  printf("<option value='%d' %s>%s</option>", $s->id, $selected, $s->name);
      }
      print "</select> <br /><br />";
  
  print "<div style='text-align:center'><textarea cols=70 id = 'note' rows=20 name='note'></textarea></div>";
  }
  ?>
<div style='text-align:right'><br /><a href='#' onclick="submForm();">Сохранить</a></div>
</form> <?
//ggr($stat);
}

function savenewstatus()
{
  global $reg, $_REQUEST;   
  $id = intval($_REQUEST['id']);
  
  $so = new shopOrder($reg);
  $so->load($id);
  if($so->id == ''|| $so->id == 0) return false;  
  
    $ok = $so->setStatus($_REQUEST['newstatus'], $_REQUEST['note']);  
    $status = $so->statusList[--$so->status];
    if($status == "") $status = "Нет статуса";
    if(  $ok  &&  $so->clientEmail!=''  &&  JosIsValidEmail($so->clientEmail)  ){ 
		$sf = file_get_contents(site_path."/component/ex/status_template.html");
		$sf = str_replace("{orderID}", $so->id, $sf);
		$sf = str_replace("{orderStatus}", $status, $sf);	
		$sf = str_replace("{NOTE}", $_REQUEST['note'], $sf);
		$sf = str_replace("{orderLink}", site_url."/cab_orders?order=".$so->code, $sf);
  
		$mymail = new mymail();
		$mymail->add_address (  desafelySqlStr( $so->clientEmail )  );
		$mymail->set_subject (  desafelySqlStr( short_surl()." заказ № ".$so->id  ) );
		$mymail->set_body	 (  desafelySqlStr( $sf )  );
		$mymail->send ();
		
    }
    ?> <a href="javascript: ins_ajax_open('?ca=shopajax&task=showstatushistory&orderid=<?=$so->id; ?>&4ajax=1', 580, 470); void(0);" class="dotted"><img src="/iadmin/images/properties.png" align="top" border="0" /> <?=$status;?> </a> <?  
}

function orderAdminRec($shopOrder)
{
	global $reg;
    $status = $shopOrder->statusList[--$shopOrder->status];
    if($status == "") $status = "Нет статуса";
	
	// определяем количество заданный вопросов
//	$comments = new comments('exgood', $reg['db'], $reg);	$questions = $comments->get(30, 0, 99999); ggtr5 ($questions);
	$comments = new comments('order', $reg['db'], $reg);
	$questions = $comments->get($shopOrder->id, 0, 99999);
	$question_cnt= count ($questions);
	if (  !$questions[0]->id  ) $question_cnt=0;

	// определяем количество не просмотренных вопросов
	$question_no_answered=0;
	if (  $questions[0]->id  ) {
		foreach ( $questions as $question ) 
			if (  !isset($question->children)  ) $question_no_answered++;
	} else $question_no_answered=0;
    ?>
    <tr>
      <td><?=$shopOrder->id; ?></td>
      <td><?=$shopOrder->clientFIO; ?> <br /><?=$shopOrder->getUserName($shopOrder->uid); ?></td>
      <td nowrap="nowrap" style="white-space:nowrap;"><?=date("d/m/Y   H:i:s", $shopOrder->create_time) ?><br /><a target="_blank" href="<?=site_url."/cab_orders?order=".$shopOrder->code; ?>" title="открыть страницу заказа">(на&nbsp;страницу&nbsp;заказа)</a></td>
      <td><a href="javascript: ins_ajax_open('?ca=shopajax&task=showorderitems&orderid=<?=$shopOrder->id;?>&4ajax=1', 730, 470); void(0);" class='dotted'><?=count($shopOrder->items); ?> шт.<br />(подробно)</a></td>
      <td><?=$shopOrder->recalcPrice(); ?> <small>руб. </small></td><?
	  $linkit ="javascript: ins_ajax_open('?ca=shopajax&task=showstatushistory&orderid=".$shopOrder->id."&4ajax=1', 580, 470); void(0); ";
      ?><td><div id="stat_<?=$shopOrder->id; ?>"><a href="<?=$linkit ?>" class='dotted nohover'><img src='/iadmin/images/properties01.png' align="absmiddle" border='0' /> </a><a href="<?=$linkit ?>" class='dotted'><?=$status;?> </a> </div>	<?
	  $linkit ="javascript: ins_ajax_open('?ca=shopmanager&task=addnewstatus&orderid=".$shopOrder->id."&4ajax=1', 580, 470); void(0); ";
	  ?><br /><a href="<?=$linkit ?>" class='dotted nohover'><img src='/iadmin/images/ins.png' border='0' align='absmiddle'> </a><a href="<?=$linkit ?>" class='dotted'>новый</a></td>
      <td><?=$shopOrder->note; ?></td>
	  <td><a href="index2.php?ca=comment&task=view&icsmart_comment_parent=<?=$shopOrder->id; ?>&icsmart_comment_parent2=<?=$shopOrder->id; ?>&icsmart_comment_type=order" target="_blank"><? if (  $question_no_answered  ){ ?><span style="font-size:16px; color:#FF0000; font-weight:bold;"><? } ?><?=$question_no_answered; ?>/<? if (  $question_no_answered  ) print '</span>'; ?><?=$question_cnt; ?></a></td>
	</tr>         
    <?
}


?>