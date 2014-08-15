<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my, $task, $id;
require_once( site_path.'/component/ad/ad_lib.php' );
$cid = josGetArrayInts( 'cid' );
//ggtr ($_REQUEST);
//ggtr ($task); die();
switch ($task) {
	case 'apply':		
	case 'save':		saveadcat( $task );
						break;
	case 'blockthem':	changeadcatBlock( $cid, 0, $option );
						break;
	case 'allowthem':	changeadcatBlock( $cid, 1, $option );
						break;
	case 'block':		changeadcatBlock( $cid, 0, $option );
						break;
	case 'unblock':		changeadcatBlock( $cid, 1, $option );
						break;
	case 'editA':		editadcat( $id, $option );
						break;
	case 'new':			editadcat( 0, $option );
						break;
	case 'remove':		removeadcat( 0, $option );
						break;
	case 'saveorder':	saveOrderadcat( $cid );
						break;
	case 'orderup':		orderupadcat( $cid );
						break;
	case 'orderdown':		orderdownadcat( $cid );
						break;
	default:			showadcat( $option );
						break;
}
function orderupadcat( $cid ) {
	global $database;
	$adcatfoto_this = ggo($_REQUEST['cid'][0], '#__adcat');
	ggtr ($adcatfoto_this );		
	$adcatfoto_up = ggsql(" SELECT * FROM #__adcat WHERE #__adcat.order< ".$adcatfoto_this->order." AND #__adcat.parent=".$adcatfoto_this->parent." ORDER BY #__adcat.order DESC LIMIT 0,1 ;");  $adcatfoto_up = $adcatfoto_up[0];
//	ggtr ($database); die();
	$i24r = new mosDBTable( "#__adcat", "id", $database );
	$i24r->id = $_REQUEST['cid'][0];
	$i24r->order = $adcatfoto_up->order;
//	ggtr ($i24r);
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();

	$i24r = new mosDBTable( "#__adcat", "id", $database );
	$i24r->id = $adcatfoto_up->id;
	$i24r->order = $adcatfoto_this->order;
//	ggtr ($i24r); die();
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	$msg = "Порядок изменен"; 
	mosRedirect( 'index2.php?ca=adcat&task=view&limit='.$_REQUEST['limit'].'&limitstart='.$_REQUEST['limitstart'], $msg );
}
function orderdownadcat( $cid ) {
	global $database;
	$adcatfoto_this = ggo($_REQUEST['cid'][0], '#__adcat');
	ggtr ($adcatfoto_this );		
	$adcatfoto_up = ggsql(" SELECT * FROM #__adcat WHERE #__adcat.order> ".$adcatfoto_this->order." AND #__adcat.parent=".$adcatfoto_this->parent." ORDER BY #__adcat.order ASC LIMIT 0,1 ;");  $adcatfoto_up = $adcatfoto_up[0];
//	ggtr ($database); die();
	$i24r = new mosDBTable( "#__adcat", "id", $database );
	$i24r->id = $_REQUEST['cid'][0];
	$i24r->order = $adcatfoto_up->order;
//	ggtr ($i24r);
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();

	$i24r = new mosDBTable( "#__adcat", "id", $database );
	$i24r->id = $adcatfoto_up->id;
	$i24r->order = $adcatfoto_this->order;
//	ggtr ($i24r); die();
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	$msg = "Порядок изменен"; 
	mosRedirect( 'index2.php?ca=adcat&task=view&limit='.$_REQUEST['limit'].'&limitstart='.$_REQUEST['limitstart'], $msg );
}

function saveOrderadcat( &$cid ) {
	global $database;
//	ggtr ($_REQUEST); die();
	for ($exi = 0; $exi<count($_REQUEST['order']); $exi++){
		$i24r = new mosDBTable( "#__adcat", "id", $database );
		$i24r->id = $_REQUEST['adcatid'][$exi];
		$i24r->order = $_REQUEST['order'][$exi];
		if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	}
	$msg 	= 'Новый порядок сохранен'; $adcatid	= intval( getUserStateFromRequest(  'id', 0 ) );
	mosRedirect( 'index2.php?ca=adcat&task=view&limit='.$_REQUEST['limit'].'&limitstart='.$_REQUEST['limitstart'], $msg );
} // saveOrder


function showadcat_rec( $k, $adcatid, $adcatlev, $limit, $limitstart, &$pageNav, &$exsi ) {
	$rows = ggsql("SELECT * FROM #__adcat AS a WHERE a.parent=".$adcatid." ORDER BY a.order ASC ; ");
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row 	=& $rows[$i];			
			$task 	= $row->publish==0 ? 'unblock' : 'block';
			$alt 	= $row->publish==0 ? '<span style="color:#ff0000;">Блокирован</span>' : 'Разрешен';
			$alt2 	= $row->publish==0 ? 'Снять блокировку' : 'Блокировать';
			$link 	= 'index2.php?ca=adcat&amp;task=editA&amp;id='. $row->id. '&amp;hidemainmenu=1&amp;search='. $_REQUEST['search'].'&amp;filter_type='. $_REQUEST['filter_type'].'&amp;filter_logged='. $_REQUEST['filter_logged'];
			if (  ($exsi>=($limitstart))  &&  ($exsi<=($limitstart+$limit))  ){
				?><tr class="<?php echo "row$k"; ?>"><?
					?><td><?php echo $exsi+1; ?></td><?
					?><td><?php echo mosHTML::idBox( $exsi, $row->id ); ?></td><?
					?><td align="left"><?
					for ($j=0; $j<$adcatlev; $j++) print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					?><a href="<?php echo $link; ?>"><? print (($row->name)); ?></a><?
					?></td><?
					?><td><a href="index2.php?ca=adgood&task=view&amp;icsmart_adgood_parent=<? print $row->id; ?>">объявления (<? print ggsqlr ("SELECT count(id) FROM #__adgood WHERE parent=".$row->id.""); ?>)</a></td><?
					?><td align="right"><?php echo $pageNav->orderUpIcon( $exsi, ($row->parent == @$rows[$i-1]->parent) ); ?></td><?
					?><td align="left"><?php echo $pageNav->orderDownIcon( $exsi, $n, ($row->parent == @$rows[$i+1]->parent) ); ?></td><?
					?><td align="center"><input type="text" name="order[]" size="5" value="<?php echo $row->order; ?>" class="text_area" style="text-align: center" /><input type="hidden" name="adcatid[]" value="<?php echo $row->id; ?>" /></td><?
					?><td align="center"><a title="<? print $alt2; ?>" onclick="return listItemTask('cb<? print $exsi ?>','<? print $task; ?>')" href="javascript: void(0);"><?php echo $alt;?></a></td><?
				?></tr><?
				$k = 1 - $k; 
			}
			$exsi++;
			$row_subs = ggsqlr("SELECT count(id) FROM #__adcat AS a WHERE a.parent=".$row->id." ; ");
			if (  $row_subs>0  ){
				showadcat_rec( $k, $row->id, ($adcatlev+1), $limit, $limitstart, $pageNav, $exsi );
			}
		}
}
function showadcat( $option ) {
	global $database, $my, $iConfig_list_limit, $reg;
	$filter_type	= getUserStateFromRequest( 'filter_type', 0 );
	$filter_logged	= intval( getUserStateFromRequest(  'filter_logged', 0 ) );
	$limit 			= intval( getUserStateFromRequest( 'limit', $iConfig_list_limit ) );
	$limitstart 	= intval( getUserStateFromRequest( 'limitstart', 0 ) );
	
	$query = "SELECT COUNT(a.id) FROM #__adcat AS a  "; $database->setQuery( $query ); $total = $database->loadResult();
	require_once( site_path . '/iadmin/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );
		?><form action="index2.php" method="post" name="adminForm">
		<table class="adminheading"><tr><td width="100%"><?
			$iway[0]->name=$reg['ad_name'];
			$iway[0]->url="";
			$iway[1]->name="Список категорий";
			$iway[1]->url="";

			i24pwprint_admin ($iway);
			?></td></tr></table>
		<table class="adminlist">
		<tr><?
			?><th width="2%" class="title">#</th><?
			?><th width="3%" class="title"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo ($total); ?>);" /></th><?
			?><th class="title">Категория</th><?
			?><th class="title">Объявления</th><?
			?><th colspan="2" align="center" width="5%">Сортировка</th><?
			?><th width="3%" ><a href="javascript: saveorder( <?php echo count( $rows )-1; ?> )" onMouseOver="return Tip('Сохранить заданный порядок отображения');">Сохранить&nbsp;порядок</a></th><?
			?><th class="title" style="text-align:center">Доступ</th><?
		?></tr><?
		$k = 0;  $exsi = 0;
		showadcat_rec( $k, 0, 0, $limit, $limitstart, $pageNav, $exsi )
		?></table>
		<?php echo $pageNav->getListFooter(); ?>
		<input type="hidden" name="ca" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>
		<?php
}


function changeadcatBlock( $cid=null, $block=1, $option ) {
	global $database;
	$action = $block ? 'блокировки' : 'разблокировки';
	if (count( $cid ) < 1) {
		echo "<script type=\"text/javascript\"> alert('Выберите объект для $action'); window.history.go(-1);</script>\n";
		exit;
	}
	mosArrayToInts( $cid );
	$cids = 'id=' . implode( ' OR id=', $cid );

	$query = "UPDATE #__adcat"
	. "\n SET publish = " . (int) $block
	. "\n WHERE ( $cids )"
	;
	//ggtr ( $query ); die();
	$database->setQuery( $query );
	if (!$database->query()) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}
	mosRedirect( 'index2.php?ca='. $option );
}


function editadcat( $uid='0', $option='users' ) {
	global $database, $my, $acl, $mainframe, $reg;


	if (  $uid>0  ) $row = ggo ($uid, "#__adcat");
	else {
		$row->id = 0;
		$row->parent = 0;
		$row->name = "";
		$row->sdesc = "";
		$row->fdesc = "";
		$row->publish = 1;
		$row->order = 1;
	}
	$vcats[] = mosHTML::makeOption( '0', '- Главная -' );
	do_adcatlist(0, $vcats, 0, $row->id);
?>
<table class="adminheading"><tr><td width="100%"><?
	$iway[0]->name=$reg['ad_name'];
	$iway[0]->url="index2.php?ca=adcat&task=view";
	if (  $row->id  ){
		$iway[1]->name="Категория ".stripslashes($row->name).' [редактирование]';
		$iway[1]->url="";
	} else {
		$iway[1]->name="Новая категория";
		$iway[1]->url="";
	}
	i24pwprint_admin ($iway);
?></td></tr></table>

<form name="adminForm" action="index2.php" method="post"><input type="hidden"  name="iuse" id="iuse" value="0" />
<table border="0" cellpadding="4" cellspacing="0" width="100%" align="center">
	<tr class="workspace">
		<td>Расположение категории: </td>
		<td>
			<? print mosHTML::selectList( $vcats, 'parent', 'class="inputbox" size="1" id="adcat" mosreq="1" moslabel="Группа" ', 'value', 'text', $row->parent ); ?>
		</td>
	</tr>
	<tr class="workspace">
		<td>Название: </td>
		<td><input name="name" size="120" mosreq="1" moslabel="Название" value="<? print ($row->name); ?>" /></td>
	</tr>
	<tr class="workspace">
		<td>Адрес:&nbsp;</td>
		<td>
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
			<tr>
				<td nowrap="nowrap"  width="16%" style="white-space:nowrap;"><?=site_url.$row->sefnamefull."/"; ?></td>
				<td align="left" width="84%"><input name="sefname" size="57" mosreq="1" moslabel="Название" value="<? print ( $row->sefname ); ?>" /></td>
			</tr>
			</table>
		</td>
	</tr>
	<? if (  $reg['iseoad']==1  ){ ?>
		<? itable_hr(2); ?>
		<tr class="workspace">
			<td></td>
			<td align="left"><a href="javascript: seoblock('.seoblock', '#seoblock_a'); void(0); " id="seoblock_a" visible="0">Показать данные для програмной оптимизации сайта</a></td>
		</tr>
		<tr class="workspace seoblock">
			<td>Содержимое тега<br />&lt;title&gt;&lt;/title&gt; категории: </td>
			<td><input name="seo_title" size="160"  value="<? print ($row->seo_title); ?>" /></td>
		</tr>
		<tr class="workspace seoblock">
			<td>Содержимое тега<br />&lt;meta Description&gt; категории: </td>
			<td><input name="seo_metadesc" size="160"  value="<? print ($row->seo_metadesc); ?>" /></td>
		</tr>
		<tr class="workspace seoblock">
			<td>Содержимое тега<br />&lt;meta Keywords&gt; категории: </td>
			<td><input name="seo_metakey" size="160"  value="<? print ($row->seo_metakey); ?>" /></td>
		</tr>
		
		<tr class="workspace seoblock">
			<td>Содержимое &lt;title&gt;&lt;/title&gt;<br /> для вложенных объявлений,<br />//**// &rarr; название объявления:</td>
			<td><input name="seo_goodtitle" size="160"  value="<? print ($row->seo_goodtitle); ?>" /></td>
		</tr>
		<tr class="workspace seoblock">
			<td>Содержимое &lt;meta Description&gt;<br /> для вложенных объявлений,<br />//**// &rarr; название объявления:</td>
			<td><input name="seo_goodmetadesc" size="160"  value="<? print ($row->seo_goodmetadesc); ?>" /></td>
		</tr>
		<tr class="workspace seoblock">
			<td>Содержимое &lt;meta Keywords&gt;<br /> для вложенных объявлений,<br />//**// &rarr; название объявления:</td>
			<td><input name="seo_goodmetakey" size="160"  value="<? print ($row->seo_goodmetakey); ?>" /></td>
		</tr>
		<? itable_hr(2); ?>
	<? } ?>

	<tr class="workspace">
		<td>Краткое описание: </td>
		<td><? editorArea( 'editor1',  ($row->sdesc) , 'sdesc', '100%;', '350', '75', '20' ) ; ?></td>
	</tr>
	
	<tr class="workspace">
		<td>Подробное описание: </td>
		<td><? editorArea( 'editor1',  ($row->fdesc) , 'fdesc', '100%;', '550', '75', '40' ) ; ?></td>
	</tr>
	
	<tr class="workspace">
		<td>Опубликованно: </td>
		<td><select name="publish">
			<option <? if (  $row->publish==1  ) print 'selected="selected"'; ?> value="1">да</option>
			<option <? if (  $row->publish==0  ) print 'selected="selected"'; ?> value="0">нет</option>
		</select></td>
	</tr>
</table>
<input type="hidden" name="id" value="<? print $row->id; ?>" />
<input type="hidden" name="task" value="save"  />
<input type="hidden" name="ca" value="adcat" />
<script language="javascript">
function doform(){
	document.adminForm.submit();
	return 1;
}
</script><?
}

function saveadcat( $task ) {
	global $database, $my, $reg;
	$adcat = ggo (  ggri('id'), "#__adcat"  ); $newsefnamefull = false;
	if (  ggri('id')==0  ) { $newsefname = true; $adcat->goods = 0; }
	else if (  $adcat->sefname!=ggrr('sefname')  ) $newsefname = true; 
	else $newsefname = false;
	
	if (  $adcat->parent!=$_REQUEST['parent']  or  ggri('id')==0  ){ $newsefnamefull = true;
		if (  $_REQUEST['parent']==0  ) $adcat->sefnamefull = '/'.$reg['ad_seoname'];
		else { $papa = ggo (  $_REQUEST['parent'], "#__adcat"  ); $adcat->sefnamefull = $papa->sefnamefull.'/'.$papa->sefname; }
	}

	$i24r = new mosDBTable( "#__adcat", "id", $database );
	$i24r->id = $_REQUEST['id'];
	$i24r->parent = $_REQUEST['parent'];
    $i24r->name = $_REQUEST['name'];
	$i24r->sdesc = $_REQUEST['sdesc'];
	$i24r->fdesc = $_REQUEST['fdesc'];
	$i24r->publish = $_REQUEST['publish'];
	if (  $reg['iseoad']==1  ){
		$i24r->seo_title = ggrr('seo_title');
		$i24r->seo_goodtitle = ggrr('seo_goodtitle');
		$i24r->seo_metadesc  = ggrr('seo_metadesc');
		$i24r->seo_metakey   = ggrr('seo_metakey');
		$i24r->seo_goodmetadesc = ggrr('seo_goodmetadesc');
		$i24r->seo_goodmetakey  = ggrr('seo_goodmetakey');
	}
	if (  $newsefname  ){
		if (  ggrr('sefname')!=''  ) $i24r->sefname = sefname( ggrr('sefname') );
		else $i24r->sefname = sefname( $i24r->name );
	} else $i24r->sefname = $adcat->sefname;
	
	if (  $newsefnamefull  ){ $i24r->sefnamefull = $adcat->sefnamefull; }
	
	if (  $i24r->id==0  ){
		$iexmaxorder = ggsql ("SELECT * FROM #__adcat WHERE parent=".$_REQUEST['parent']." ORDER BY #__adcat.order DESC LIMIT 0,1 "); // ggtr ($iexmaxorder);
		$i24r->order = $iexmaxorder[0]->order+1;
	}
//	ggtr ($i24r); //die();
	if (!$i24r->check()) { echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n"; } else $i24r->store();
//	ggtr ($database, 20); die();
	if (  $newsefname  or  $newsefnamefull  ){
		adrecalc_req($adcat->sefnamefull.'/'.ggrr('sefname'), $i24r->id, $adcat->goods );
	}
	
	$adminlog = new adminlog();	
	if (  ggri('id')==0  )	$adminlog->logme('new_cat', $reg['ad_name'], $i24r->name, $i24r->id ); else $adminlog->logme('save_cat', $reg['ad_name'], $i24r->name, $i24r->id );

	switch ( $task ) {
		case 'apply':
			$msg = 'Категория сохранена: '. $i24r->name; mosRedirect( 'index2.php?ca=adcat&task=editA&hidemainmenu=1&id='.$i24r->id, $msg );
		case 'save':
		default:
			$msg = 'Категория сохранена: '. $i24r->name; mosRedirect( 'index2.php?ca=adcat', $msg );
			break;
	}
}

function removeadcat( $task ) {
	global $database, $my, $reg;
	foreach ($_REQUEST['cid'] as $dfgd){
		// проверяем, есть ли вложенные объекты
		$adgoodsincat = ggsqlr( "SELECT count(id) FROM #__adgood WHERE parent=".$dfgd );
		if ($adgoodsincat>0){
			?><script language="javascript">  alert("Категория: '<? $adcattodel = ggo($dfgd, "#__adcat"); print $adcattodel->name; ?>' содержит объекты, удаление невозможно");  </script><?
			continue;
		}
		// проверяем, есть ли вложенные категории
		$adgoodsincat = ggsqlr( "SELECT count(id) FROM #__adcat WHERE parent=".$dfgd );
		if ($adgoodsincat>0){
			?><script language="javascript">  alert("Категория: '<? $adcattodel = ggo($dfgd, "#__adcat"); print $adcattodel->name; ?>' содержит вложенные категории, удаление невозможно");  </script><?
			continue;
		}
		$adminlog_obg = ggo($dfgd, "#__adcat");	$adminlog = new adminlog(); $adminlog->logme('del_cat', $reg['ad_name'], $adminlog_obg->name, $adminlog_obg->id );
		ggsqlq ("DELETE FROM #__adcat WHERE id=".$dfgd);
	}
	$msg = 'Категория(и) удалены: ';
	mosRedirect( 'index2.php?ca=adcat', $msg );
}


?>