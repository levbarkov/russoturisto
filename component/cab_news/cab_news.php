<?php
// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my, $task, $id, $option, $isgal;
$cid = josGetArrayInts( 'cid' );
$id	= intval( getUserStateFromRequest(  'id', 0 ) );
$isgal = ggri('gal');
switch ($task) {
	case 'remove':		removecab_news( 0, $option );
						break;
	case 'edit':		editcab_news( $id, $option );
						break;
	case 'new':		editcab_news( 0, $option );
						break;
	case 'apply':
	case 'save':		savecab_news( $id, $option );
						break;
	case 'saveorder':	saveOrdercab_news( $cid );
						break;
	case 'orderup':		orderupcab_news( $cid );
						break;
	case 'orderdown':	orderdowncab_news( $cid );
						break;
	case 'cancel':		mosRedirect( 'index.php?c=cab_news&task=view', "" );
						break;
	default:		showcab_news( $option );
						break;
}
function orderupcab_news( $cid ) {
	global $database, $isgal;
	  
	$excatfoto_this = ggo($_REQUEST['cid'][0], '#__cab_news');
	$excatfoto_up = ggsql(" SELECT * FROM #__cab_news WHERE #__cab_news.order< ".$excatfoto_this->order." ORDER BY #__cab_news.order DESC LIMIT 0,1 ;");  $excatfoto_up = $excatfoto_up[0];
//	ggtr ($database); die();
	$i24r = new mosDBTable( "#__cab_news", "id", $database );
	$i24r->id = $_REQUEST['cid'][0];
	$i24r->order = $excatfoto_up->order;
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();

	$i24r = new mosDBTable( "#__cab_news", "id", $database );
	$i24r->id = $excatfoto_up->id;
	$i24r->order = $excatfoto_this->order;
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	$msg = "Порядок изменен"; $excatid	= intval( getUserStateFromRequest(  'id', 0 ) );
	mosRedirect( 'index.php?c=cab_news&task=view&gal='.$isgal.'&limit='.$_REQUEST['limit'].'&limitstart='.$_REQUEST['limitstart'], $msg );
}
function orderdowncab_news( $cid ) {
	global $database, $isgal;
	  
	$excatfoto_this = ggo($_REQUEST['cid'][0], '#__cab_news');
	$excatfoto_up = ggsql(" SELECT * FROM #__cab_news WHERE #__cab_news.order> ".$excatfoto_this->order." ORDER BY #__cab_news.order ASC LIMIT 0,1 ;");  $excatfoto_up = $excatfoto_up[0];
//	ggtr ($database); die();
	$i24r = new mosDBTable( "#__cab_news", "id", $database );
	$i24r->id = $_REQUEST['cid'][0];
	$i24r->order = $excatfoto_up->order;
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();

	$i24r = new mosDBTable( "#__cab_news", "id", $database );
	$i24r->id = $excatfoto_up->id;
	$i24r->order = $excatfoto_this->order;
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	$msg = "Порядок изменен"; $excatid	= intval( getUserStateFromRequest(  'id', 0 ) );
	mosRedirect( 'index.php?c=cab_news&task=view&gal='.$isgal.'&limit='.$_REQUEST['limit'].'&limitstart='.$_REQUEST['limitstart'], $msg );
}
function saveOrdercab_news( &$cid ) {
	global $database, $isgal;
	  
	// ggtr ($_REQUEST); //die();
	for ($exi = 0; $exi<count($_REQUEST['order']); $exi++){
		$i24r = new mosDBTable( "#__cab_news", "id", $database );
		$i24r->id = $_REQUEST['exfotoid'][$exi];
		$i24r->order = $_REQUEST['order'][$exi];
		if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	}
	$msg 	= 'Новый порядок сохранен'; $excatid	= intval( getUserStateFromRequest(  'id', 0 ) );
	mosRedirect( 'index.php?c=cab_news&task=view&gal='.$isgal.'&limit='.$_REQUEST['limit'].'&limitstart='.$_REQUEST['limitstart'], $msg );
} // saveOrder
function showcab_news( $option ) {
	global $database, $my, $iConfig_list_limit, $iConfig_list_limit, $isgal;
	
	$excatid	= intval( getUserStateFromRequest(  'id', 0 ) );
	$limit 			= intval( getUserStateFromRequest( 'limit', $iConfig_list_limit ) );
	$limitstart 	= intval( getUserStateFromRequest( 'limitstart', 0 ) );
	
	$gal_prefix = $isgal ? " and a.type=1 " : " and a.type=0 ";
	$query = "SELECT COUNT(a.id) FROM #__cab_news AS a WHERE a.uid=".$my->id.$gal_prefix; $database->setQuery( $query ); $total = $database->loadResult();
	global $mosConfig_absolute_path;
	require_once( $mosConfig_absolute_path . '/includes/pageNavigation.php' );	
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );

	$rows = ggsql("SELECT * FROM #__cab_news AS a WHERE a.uid=".$my->id.$gal_prefix." ORDER BY a.order ASC LIMIT $limitstart, $limit ; ");  //ggtr ($database);
	?><script language="javascript" type="text/javascript">
	<!--
	function client_submitbutton(pressbutton) {
		var form = document.adminForm;
		form.task.value=pressbutton; form.submit();
	}
	//-->
	</script>
	<form action="index.php" method="get" name="adminForm" >
	<table cellspacing="0" cellpadding="0" border="0"  width="100%" align="center" >
		<tr>
			<td width="50%" class="cab_action"><? print (ggri('gal')==1) ? "Разделы фотогалереи" : "Мои новости";?></td>
			<td align="right" style="text-align:right"><?
				?><table cellspacing="0" cellpadding="0" border="0"  align="right" id="toolbar">
					<tr>
						<td nowrap="nowrap" align="right"><a class="toolbar" href="javascript: client_submitbutton('remove'); ">Удалить</a></td><td>&nbsp;</td>
						<td nowrap="nowrap" align="right"><a class="toolbar" href="javascript: client_submitbutton('new'); ">Новый</a></td>
					</tr>
				</table><?
			?></td>
		</tr>
		<tr height="1px">
			<td colspan="2" bgcolor="#cecfce"></td>
		</tr>
	</table>

	<table class="adminlist" id="gallary" border="0" cellpadding="0" cellspacing="0" >
	<thead>
	<tr>
		<td width="4%" height="25" align="center">№</td>
		<td width="3%" align="center"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count($rows); ?>, 'cb', 'adminForm');" /></td>
		<td align="center">Названеи</td>
		<td align="center">Фото</td><?
		?><td colspan="2" align="center" width="5%">Сортировка</td><?
		?><td id="green" width="18%" align="center"><a href="javascript: saveorder( <?php echo count( $rows )-1; ?> )" onMouseOver="return Tip('Сохранить заданный порядок отображения');">Сохранить&nbsp;порядок</a></td><?
		?><td ></td>
	</tr>
	</thead>
	<?php
	$k = 0;
	for ($i=0, $n=count( $rows ); $i < $n; $i++) {
		$row 	=& $rows[$i];
		$task 	= $row->publish==0 ? 'unblock' : 'block';
		$alt 	= $row->publish==0 ? '<span style="color:#ff0000;">Блокирован</span>' : 'Разрешен';
		$alt2 	= $row->publish==0 ? 'Снять блокировку' : 'Блокировать';
		$link 	= 'index.php?c=cab_news&amp;task=editA&amp;id='. $row->id. '&amp;hidemainmenu=1&amp;search='. $_REQUEST['search'].'&amp;filter_type='. $_REQUEST['filter_type'].'&amp;filter_logged='. $_REQUEST['filter_logged'];
		?><tr class="<? if($i%2==1){echo "brown";}?>"><?
			?><td align="center"><?php echo $i+1+$pageNav->limitstart;?></td><?
			?><td align="center"><?php echo mosHTML::idBox( $i, $row->id ); ?></td><?
			?><td align="center" nowrap="nowrap"><a href="index.php?c=cab_news&amp;task=edit&amp;id=<? print $row->id; ?>&gal=<?=$isgal?>"><?=stripslashes($row->title)?></a></td><?
			?><td align="center" nowrap="nowrap"><a href="index.php?c=cab_foto&amp;task=view&amp;parent=<? print $row->id; ?>">смотреть (<? print ggsqlr ("SELECT count(id) FROM #__cab_foto WHERE new_id=".$row->id.""); ?>)</a></td><?
			?><td align="right"><?php echo $pageNav->orderUpIcon( $i, ($row->excat_id == @$rows[$i-1]->excat_id), 'orderup', 'Передвинуть выше', 'adminForm' ); ?></td><?
			?><td align="right"><?php echo $pageNav->orderDownIcon( $i, $n, ($row->excat_id == @$rows[$i+1]->excat_id), 'orderdown', 'Передвинуть ниже', 'adminForm' ); ?></td><?
			?><td align="center"><input type="text" name="order[]" size="5" value="<?php echo $row->order; ?>" class="text_area" style="text-align: center" /><input type="hidden" name="exfotoid[]" value="<?php echo $row->id; ?>" /></td><?
			?><td><a href="index.php?c=cab_news&task=edit&id=<?=$row->id?>&gal=<?=$isgal?>">Изменить</a></td><?
		?></tr><?
		$k = 1 - $k;
	}
	?></table>
	<?php echo $pageNav->getListFooter(); ?>
	<input type="hidden" name="c" value="<?php echo $option;?>" />
	<input type="hidden" name="id" value="<?php echo $excatid;?>" />
	<input type="hidden" name="task" value="view" />
	<input type="hidden" name="gal" value="<?=$isgal?>" />
	<input type="hidden" name="boxchecked" value="0" />
	</form>
	<?php 
}

function savecab_news( $id, $option ) {
	global $database, $my, $task, $isgal;
	  
	$i24r = new mosDBTable( "#__cab_news", "id", $database );
	$i24r->id = ggri('id');
	if (  $i24r->id==0  ) $i24r->uid = $my->id;
    $i24r->title = ggrr('title');
    $i24r->introtext = ggrr('introtext');
	$i24r->fulltext = ggrr('fulltext');
	$i24r->type = ggri('type');
	if (  $i24r->id==0  ){
		$iexmaxorder = ggsql ("SELECT * FROM #__cab_news WHERE uid=".$my->id." ORDER BY #__cab_news.order DESC LIMIT 0,1 ");
		$i24r->order = $iexmaxorder[0]->order+1;
	}
//	ggd ($i24r);
	if (!$i24r->check()) {
		echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";
	} else $i24r->store();
//	ggtr ($database, 20); die();
	switch ( $task ) {
		case 'apply':
			$msg = 'Сохранено: '. $i24r->name; mosRedirect( 'index.php?c=cab_news&task=edit&id='.$i24r->id.'&gal='.$isgal, $msg ); break;
		case 'save':
		default:
			$msg = 'Сохранено: '. $i24r->name; mosRedirect( 'index.php?c=cab_news&task=view&id='.$i24r->id.'&gal='.$isgal, $msg ); break;
	}
}
function removecab_news( $task ) {
	global $database, $isgal;
	  
	foreach ($_REQUEST['cid'] as $dfgd){
		$idata = ggo($dfgd, "#__cab_news");
		ggsqlq ("DELETE FROM #__cab_news WHERE id=".$dfgd);	
	}
	$msg = 'Удалено: '.$idata->title."<br />"; mosRedirect( 'index.php?c=cab_news&task=view&gal='.$isgal, $msg );
}
function editcab_news( $id, $option ) {
	global $database, $my, $isgal;
	if (  $id>0  ) $row = ggo($id, "#__cab_news");
	else {
		$row->id = 0;
		$row->uid = $my->id;
		$row->title = "";
		$row->introtext = "";
		$row->fulltext = "";
		$row->type = 0;
		$row->order = 1;
	}
	?><script language="javascript" type="text/javascript">
	<!--
	function client_submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') { form.task.value=pressbutton; form.submit(); }
		if (form.title.value == ""){ alert( "Этот объект должен иметь заголовок" ); }
		else { form.task.value=pressbutton; form.submit(); }
	}
	//-->
	</script>
	<form name="adminForm" action="index.php" method="post">
	<table cellspacing="0" cellpadding="0" border="0"  width="100%" align="center" >
		<tr>
			<td width="50%" class="cab_action">Редактирование</td>
			<td align="right" style="text-align:right"><?
				?><table cellspacing="0" cellpadding="0" border="0"  align="right" id="toolbar">
					<tr>
						<td nowrap="nowrap" align="right"><a class="toolbar" href="javascript: client_submitbutton('save'); ">Сохранить</a></td><td>&nbsp;</td>
						<td nowrap="nowrap" align="right"><a class="toolbar" href="javascript: client_submitbutton('apply'); ">Применить</a></td><td>&nbsp;</td>
						<td nowrap="nowrap" align="right"><a class="toolbar" href="javascript: client_submitbutton('cancel'); ">Отменить</a></td>
					</tr>
				</table><?
			?></td>
		</tr>
		<tr height="1px">
			<td colspan="2" bgcolor="#cecfce"></td>
		</tr>
	</table>
	<table border="0" cellpadding="4" cellspacing="0" width="100%" align="center">
		<tr >
			<td>Название:</td>
			<td><input name="title" value="<? print $row->title; ?>" class="inputbox" size="100" /></td>
		</tr>
		<tr >
			<td>Превью:</td>
			<td><? editorArea( 'editor1',  ($row->introtext), 'introtext', '100%;', '350', '75', '20' ) ; ?></td>
		</tr>
		<tr >
			<td>Описание:</td>
			<td><? editorArea( 'editor1',  ($row->fulltext), 'fulltext', '100%;', '400', '75', '30' ); ?></td>
		</tr>
	</table>
	<input type="hidden" name="id" value="<? print $row->id; ?>" />
	<input type="hidden" name="type" value="<?=$row->type?>" />
	<input type="hidden" name="task" value="save"  />
	<input type="hidden" name="c" value="<?=$option?>" />
	<input type="hidden" name="gal" value="<?=$isgal?>" />
	</form>
<? 
}
?>