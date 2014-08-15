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
	case 'save':		saveadgood( $task );
						break;
	case 'blockthem':	changeadgoodBlock( $cid, 0, $option );
						break;
	case 'allowthem':	changeadgoodBlock( $cid, 1, $option );
						break;
	case 'block':		changeadgoodBlock( $cid, 0, $option );
						break;
	case 'unblock':		changeadgoodBlock( $cid, 1, $option );
						break;
	case 'spec':		changeadgoodSpec( $cid, 0, $option );
						break;
	case 'unspec':		changeadgoodSpec( $cid, 1, $option );
						break;

	case 'editA':		$parent_e = ggo (icsmarti('icsmart_adgood_parent'), "#__adcat"); $parent_ee = $parent_e->parent;
						if (  $parent_ee==33  ||  $parent_ee==34  ||  $parent_ee==35  ) editadgood2( $id, $option );
						else editadgood( $id, $option );
						break;
	case 'new':			$parent_e = ggo (icsmarti('icsmart_adgood_parent'), "#__adcat"); $parent_ee = $parent_e->parent;
						if (  $parent_ee==33  ||  $parent_ee==34  ||  $parent_ee==35  ) editadgood2( 0, $option );
						else editadgood( 0, $option );
						break;
	case 'remove':		removeadgood( 0, $option );
						break;
	case 'move':		moveadgood( $cid );
						break;
	case 'movesave':	moveadgoodsave( $cid );
						break;
	case 'copy':		copyadgood( $cid );
						break;
	case 'copysave':	copyadgoodsave( $cid );
						break;

	case 'saveorder':	saveOrderadgood( $cid );
						break;
	case 'orderup':		orderupadgood( $cid );
						break;
	case 'orderdown':		orderdownadgood( $cid );
						break;
	default:			showadgood( $option );
						break;
}

function orderupadgood( $cid ) {
	global $database;
	$adgoodfoto_this = ggo($_REQUEST['cid'][0], '#__adgood');
	ggtr ($adgoodfoto_this );		
	$adgoodfoto_up = ggsql(" SELECT * FROM #__adgood WHERE #__adgood.order< ".$adgoodfoto_this->order." AND #__adgood.parent=".$adgoodfoto_this->parent." ORDER BY #__adgood.order DESC LIMIT 0,1 ;");  $adgoodfoto_up = $adgoodfoto_up[0];
//	ggtr ($database); die();
	$i24r = new mosDBTable( "#__adgood", "id", $database );
	$i24r->id = $_REQUEST['cid'][0];
	$i24r->order = $adgoodfoto_up->order;
//	ggtr ($i24r);
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();

	$i24r = new mosDBTable( "#__adgood", "id", $database );
	$i24r->id = $adgoodfoto_up->id;
	$i24r->order = $adgoodfoto_this->order;
//	ggtr ($i24r); die();
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	$msg = "Порядок изменен"; 
	mosRedirect( 'index2.php?ca=adgood&task=view&limit='.$_REQUEST['limit'].'&limitstart='.$_REQUEST['limitstart'], $msg );
}
function orderdownadgood( $cid ) {
	global $database;
	$adgoodfoto_this = ggo($_REQUEST['cid'][0], '#__adgood');
	ggtr ($adgoodfoto_this );		
	$adgoodfoto_up = ggsql(" SELECT * FROM #__adgood WHERE #__adgood.order> ".$adgoodfoto_this->order." AND #__adgood.parent=".$adgoodfoto_this->parent." ORDER BY #__adgood.order ASC LIMIT 0,1 ;");  $adgoodfoto_up = $adgoodfoto_up[0];
//	ggtr ($database); die();
	$i24r = new mosDBTable( "#__adgood", "id", $database );
	$i24r->id = $_REQUEST['cid'][0];
	$i24r->order = $adgoodfoto_up->order;
//	ggtr ($i24r);
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();

	$i24r = new mosDBTable( "#__adgood", "id", $database );
	$i24r->id = $adgoodfoto_up->id;
	$i24r->order = $adgoodfoto_this->order;
//	ggtr ($i24r); die();
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	$msg = "Порядок изменен"; 
	mosRedirect( 'index2.php?ca=adgood&task=view&limit='.$_REQUEST['limit'].'&limitstart='.$_REQUEST['limitstart'], $msg );
}

function saveOrderadgood( &$cid ) {
	global $database;
//	ggtr ($_REQUEST); die();
	for ($exi = 0; $exi<count($_REQUEST['order']); $exi++){
		$i24r = new mosDBTable( "#__adgood", "id", $database );
		$i24r->id = $_REQUEST['adgoodid'][$exi];
		$i24r->order = $_REQUEST['order'][$exi];
		if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	}
	$msg 	= 'Новый порядок сохранен'; $adgoodid	= intval( getUserStateFromRequest(  'id', 0 ) );
	mosRedirect( 'index2.php?ca=adgood&task=view&limit='.$_REQUEST['limit'].'&limitstart='.$_REQUEST['limitstart'], $msg );
} // saveOrder


function showadgood( $option ) {
	global $database, $my, $iConfig_list_limit, $reg;
	$filter_type	= getUserStateFromRequest( 'filter_type', 0 );
	$filter_logged	= intval( getUserStateFromRequest(  'filter_logged', 0 ) );
	$limit 			= intval( getUserStateFromRequest( 'limit', $iConfig_list_limit ) );
	$limitstart 	= intval( getUserStateFromRequest( 'limitstart', 0 ) );
	
	$where = array();
	if (  icsmart('icsmart_adgood_parent')  ) $where[]= " a.parent='".icsmart('icsmart_adgood_parent')."' ";
	if (  icsmart('icsmart_adgood_search')  ) $where[]= " a.name like '%".icsmart('icsmart_adgood_search')."%' ";
	//ggtr ($where);
	
	$query = "SELECT COUNT(a.id) FROM #__adgood AS a ".( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" );  $database->setQuery( $query ); $total = $database->loadResult();
	$query_rows = str_replace("COUNT(a.id)", "*", $query);
	$query_rows .= "ORDER BY a.order ASC LIMIT $limitstart, $limit ";
	$rows = ggsql( $query_rows );
	require_once( site_path . '/iadmin/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );
		?><form action="index2.php" method="get" name="searchForm">
		<table class="adminheading"><tr><?
			?><td width="100%" nowrap="nowrap"><?
			$patheay_row_id = icsmarti('icsmart_adgood_parent');
			if (  $patheay_row_id>0  ) $patheay_row_data = ggo($patheay_row_id, "#__adcat");
			else $patheay_row_data->name = '';

			$iway[0]->name=$reg['ad_name'];
			$iway[0]->url="index2.php?ca=adcat";
			$iway[1]->name=stripslashes($patheay_row_data->name);
			$iway[1]->url="";
			$iway[2]->name="список товаров";
			$iway[2]->url="";

			i24pwprint_admin ($iway);

			?></td><?
			?><td align="right" >Поиск:&nbsp;</td><?
			?><td align="right" ><input type="text" name="icsmart_adgood_search" value="<?php echo htmlspecialchars( icsmart('icsmart_adgood_search') );?>" class="inputtop" onchange="document.adminForm.submit();" /></td><?
			?><td ><?php $vcats[] = mosHTML::makeOption( "", "- Выберите категорию -"); do_adcatlist(0, $vcats, 0); print mosHTML::selectList( $vcats, 'icsmart_adgood_parent', 'class="inputtop" onchange="document.searchForm.submit();" size="1" id="parent" mosreq="1" moslabel="Группа" ', 'value', 'text', icsmart('icsmart_adgood_parent') ); ?></td><?
			?><td ><input type="submit" value="Искать" class="gosearch" /></td><?
		?></tr></table><input type="hidden" name="ca" value="<?php echo $option;?>" /><?
		?><input type="hidden" name="task" value="" /><?
		?></form><form action="index2.php" method="post" name="adminForm"><?
		// инициализация класса необходимого для перемящаемой таблицы
		$table_drug  = new ajax_table_drug ;
		$table_drug->id="ajax_table_drug_td";
		$table_drug->table="#__adgood";
		$table_drug->order="order";
		?><table class="adminlist"  <?=$table_drug->table(); ?> ><tr  <?=$table_drug->row(); ?>  >
			<th width="2%" class="title">#</th>
			<th width="3%" class="title"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count($rows); ?>);" /></th>
			<th class="title">Название</th>
			<th class="title">Категория</th><?
			?><th align="center" width="5%">Сортировка</th><?
			?><th width="3%" ><a href="javascript: saveorder( <?php echo count( $rows )-1; ?> )" onMouseOver="return Tip('Сохранить заданный порядок отображения');">Сохранить&nbsp;порядок</a></th><?
			?><th class="title" style="text-align:center">Спец. товар</th><?
			?><th class="title" style="text-align:center">Доступ</th><?
		?></tr><?
		$k = 0;  $exsi = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row 	=& $rows[$i];			
			$task 	= $row->publish==0 ? 'unblock' : 'block';			
			$alt 	= $row->publish==0 ? '<span style="color:#ff0000;">Блокирован</span>' : 'Разрешен';
			$alt2 	= $row->publish==0 ? 'Снять блокировку' : 'Блокировать';
			$link 	= 'index2.php?ca=adgood&amp;task=editA&amp;id='. $row->id. '&amp;hidemainmenu=1&amp;search='. $_REQUEST['search'].'&amp;filter_type='. $_REQUEST['filter_type'].'&amp;filter_logged='. $_REQUEST['filter_logged'];
			
			$spec_task 	= $row->spec==0 ? 'unspec' : 'spec';
			$spec_alt 	= $row->spec==1 ? '<span style="color:#ff0000;">Спец. товар</span>' : 'Обычный';
			$spec_alt2 	= $row->spec==1 ? 'Снять cпец. усл' : 'Сделать cпец. товаром';
//			ggtr ($exsi,4);  ggtr ($limitstart,1);  ggtr ($limit,1);

			?><tr <?=$table_drug->row($row->id, $row->order); ?>  class="<?php echo "row$k"; ?>"><?
				?><td><?php echo ($exsi+1); ?></td><?
				?><td><?php echo mosHTML::idBox( $exsi, $row->id ); ?></td><?
				?><td align="left"><?
				for ($j=0; $j<$adgoodlev; $j++) print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				?><a href="<?php echo $link; ?>"><? echo htmlspecialchars(($row->name)); ?></a><?
				?></td><?
				?><td><?php $iexcurcat = ggo($row->parent, "#__adcat");
					$iexsubcat_prefix = "";  $iexcurcat_name = $iexcurcat->name; $iexcurcat_id = $iexcurcat->id;
					$iadcatlev = 0;  $iadcatslevs = array();
					while ($iexcurcat->parent!=0){
						$iexcurcat = ggo($iexcurcat->parent, "#__adcat");
						$iadcatslevs[] = $iexcurcat->name;
					}
					for ($exc = count($iadcatslevs)-1; $exc>=0; $exc--){
						for ($j=0; $j<$exc; $j++) $iexsubcat_prefix_white .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
						$iexsubcat_prefix = $iexsubcat_prefix.$iadcatslevs[$exc]."<br />".$iexsubcat_prefix_white;
					}
					if (  $iexsubcat_prefix  ){
						$iexsubcat_prefix = $iexsubcat_prefix."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$iexcurcat_name;
						?><a href="index2.php?ca=adcat&task=editA&id=<? print $iexcurcat_id; ?>&hidemainmenu=1" onMouseOver="return Tip('<? print $iexsubcat_prefix; ?>');"><? print ($iexcurcat_name);?></a><?
					} else {
						?><a href="index2.php?ca=adcat&task=editA&id=<? print $iexcurcat_id; ?>&hidemainmenu=1"><? print ($iexcurcat_name); ?></a><?
					}
				?></td><?
				?><td align="center" class="dragHandle drugme" >&nbsp;</td><?
				?><td align="center"><input type="text" name="order[]" size="5" value="<?php echo $row->order; ?>" class="text_area" style="text-align: center" /><input type="hidden" name="adgoodid[]" value="<?php echo $row->id; ?>" /></td><?
				?><td align="center"><a title="<? print $spec_alt2; ?>" onclick="return listItemTask('cb<? print $exsi ?>','<? print $spec_task; ?>')" href="javascript: void(0);"><?php echo $spec_alt;?></a></td><?
				?><td align="center"><a title="<? print $alt2; ?>" onclick="return listItemTask('cb<? print $exsi ?>','<? print $task; ?>')" href="javascript: void(0);"><?php echo $alt;?></a></td><?
			?></tr><?
			$k = 1 - $k; 

			$exsi++;
		}
		?></table>
		<?php echo $pageNav->getListFooter(); ?>
		<a onclick="alert(2);">1144dss</a>
		<input type="hidden" name="ca" value="<?php echo $option;?>" />
		<input type="hidden" name="parent" value="<?php echo $_REQUEST['parent'];?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" /></form>
		<?php
}


function changeadgoodBlock( $cid=null, $block=1, $option ) {
	global $database;
	$action = $block ? 'блокировки' : 'разблокировки';
	if (count( $cid ) < 1) {
		echo "<script type=\"text/javascript\"> alert('Выберите объект для $action'); window.history.go(-1);</script>\n";
		exit;
	}
	mosArrayToInts( $cid );
	$cids = 'id=' . implode( ' OR id=', $cid );
	$query = "UPDATE #__adgood"
	. "\n SET publish = " . (int) $block
	. "\n WHERE ( $cids )"
	;
	//ggtr ( $query ); die();
	$database->setQuery( $query );
	if (!$database->query()) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}
	mosRedirect( 'index2.php?ca='. $option.'&parent='.$_REQUEST['parent'] );
}
function changeadgoodSpec( $cid=null, $block=1, $option ) {
	global $database;
	$action = $block ? 'спец. товара(ов)' : 'снятия спец. товара(ов)';
	if (count( $cid ) < 1) {
		echo "<script type=\"text/javascript\"> alert('Выберите объект(ы) для $action'); window.history.go(-1);</script>\n";
		exit;
	}
	mosArrayToInts( $cid );
	$cids = 'id=' . implode( ' OR id=', $cid );
	$query = "UPDATE #__adgood"
	. "\n SET spec = " . (int) $block
	. "\n WHERE ( $cids )"
	;
	//ggtr ( $query ); die();
	$database->setQuery( $query );
	if (!$database->query()) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}
	mosRedirect( 'index2.php?ca='. $option.'&parent='.$_REQUEST['parent'] );
}

function editadgood( $uid='0', $option='users' ) {
	global $database, $my, $acl, $mainframe, $reg;

	if (  $uid>0  ) $row = ggo ($uid, "#__adgood");
	else {
		$row->id = 0;
		$row->name = "";
		$row->sku = "";
		$row->parent = icsmarti('icsmart_adgood_parent');
		$row->sdesc = "";
		$row->publish = 1;
		$row->order = 1;
	}
	$vcats[] = mosHTML::makeOption( 0, "- Выберите категорию -");
	do_adcatlist(0, $vcats, 0);
//	ggtr ($vcats, 50);

?><form name="adminForm" action="index2.php" method="post" enctype="multipart/form-data"><input type="hidden"  name="iuse" id="iuse" value="0" />
<table class="adminheading"><tr><td class="edit"><?
			$iway[0]->name=$reg['ad_name'];
			$iway[0]->url="";
			$iway[1]->name= $row->id ? 'Редактирование' : 'Новый товар';
			$iway[1]->url="";

			i24pwprint_admin ($iway,0);
?></td></tr></table>
<table border="0" cellpadding="4" cellspacing="0" width="100%" align="center">
	<tr class="workspace">
		<td>Категории: </td>
		<td>
			<? print mosHTML::selectList( $vcats, 'parent', 'class="inputbox" size="1" id="adgood" mosreq="1" moslabel="Группа" ', 'value', 'text', $row->parent ); ?>
		</td>
		<? $ins_adgood_price = ggsql (" select * from #__adgood_price ");
		$ins_adgood_price_count = count ($ins_adgood_price); ?>
		<td rowspan="6" valign="top" style="vertical-align:top;">
			<table border="0" cellpadding="4" cellspacing="0" width="95%" align="right">
				<? for ($i=1; $i<=$ins_adgood_price_count; $i++){ ?>
				<tr class="workspace">
					<td><? print $ins_adgood_price[$i-1]->d; ?>: </td>
					<td><input type="text" name="price<? print $i; ?>"  value="<?
						$eval_str = '$cprice = $row->price'.$i.'; ';
						eval ($eval_str);
						print $cprice;
					?>" /></td>
				</tr>
			<? } ?>
			</table>
		</td>
	</tr>
	<tr class="workspace">
		<td>Название: </td>
		<td><input name="name" size="104" mosreq="1" moslabel="Название" value="<? print ($row->name); ?>" /></td>
	</tr>
	<tr class="workspace">
		<td>Адрес:&nbsp;</td>
		<td>
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
			<tr>
				<td nowrap="nowrap"  width="30%" style="white-space:nowrap;"><?=site_url.$row->sefnamefullcat ?>/</td>
				<td align="left" width="70%"><input name="sefname" size="44" mosreq="1" moslabel="Название" value="<? print ( $row->sefname ); ?>" /></td>
			</tr>
			</table>
		</td>
	</tr>				

	<tr class="workspace">
		<td>Опубликованно: </td>
		<td><select name="publish">
			<option <? if (  $row->publish==1  ) print 'selected="selected"'; ?> value="1">да</option>
			<option <? if (  $row->publish==0  ) print 'selected="selected"'; ?> value="0">нет</option>
		</select></td>
	</tr>
	<tr class="workspace">
		<td>Спец. товар: </td>
		<td><select name="spec">
			<option <? if (  $row->spec==1  ) print 'selected="selected"'; ?> value="1">да</option>
			<option <? if (  $row->spec==0  ) print 'selected="selected"'; ?> value="0">нет</option>
		</select></td>
	</tr>
	
	<tr class="workspace">
		<td>Артикул: </td>
		<td><input name="sku" size="36" mosreq="1" moslabel="Название" value="<? print ($row->sku); ?>" /></td>
	</tr>

	<tr class="workspace">
		<td>Краткое описание: </td>
		<td colspan="2"><? editorArea( 'editor1',  ($row->sdesc) , 'sdesc', '100%;', '350', '75', '20' ) ; ?></td>
	</tr>
	
	<? $ins_adgood_f = ggsql (" select * from #__adgood_f ");
	$ins_adgood_f_count = count ($ins_adgood_f); ?>
	<? for ($i=1; $i<=$ins_adgood_f_count; $i++){ ?>
	<tr class="workspace">
		<td><? print $ins_adgood_f[$i-1]->d; ?>: </td>
		<td colspan="2"><input type="text" size="120" name="f<? print $i; ?>"  value="<?
			$eval_str = '$cf = $row->f'.$i.'; ';
			eval ($eval_str);
			print $cf;
		?>" /></td>
	</tr>
	<? } 
	?><tr class="workspace"><?
		?><td rowspan="2" valign="top" style="vertical-align:top">Основное изображение:</td><?
		?><td><? print $row->images; ?></td><?
		?><td></td><?
	?></tr><?
	?><tr class="workspace"><?
		?><td><input type="file" class="inputbox" style="width:100%" name="newfoto" id="newfoto" value="" onchange="document.getElementById('view_imagelist').src = '/includes/images/after_save.jpg'" /></td><?
		?><td></td><?
	?></tr><?
	
	?><tr class="workspace"><?
			?><td></td><?
			?><td colspan="2"><table border="0" cellpadding="0" cellspacing="0"><tr><td><input name="i24_dosmallfoto" type="checkbox" checked="checked" /></td><td>&nbsp;Уменьшить изображение</td></tr></table></td><?
	?></tr><?
	?><tr class="workspace"><?
			?><td></td><?
			?><td colspan="2"><table border="0" cellpadding="0" cellspacing="0"><tr><td><input name="i24_delfoto" type="checkbox"  /></td><td>&nbsp;Удалить изображение</td></tr></table></td><?
	?></tr><?

	?><tr class="workspace"><?
			?><td>Основное изображение:</td><?
			?><td colspan="2"><a class="highslide" onclick="return hs.expand(this)" href="<? print site_url."/images/ad/good/".$row->imagesorg ?>" ><img name="view_imagelist" id="view_imagelist" src="<? print site_url."/images/ad/good/".$row->images ?>" border="0" /></a></td><?
	?></tr><?
	
	for ($i=2; $i<10; $i++){
		?><tr class="workspace"><?
			?><td rowspan="2" valign="top" style="vertical-align:top">Фото <?=$i; ?>:</td><?
			?><td><? eval( 'print $row->images'.$i.';' );  ?></td><?
			?><td></td><?
		?></tr><?
		?><tr class="workspace"><?
			?><td><input type="file" class="inputbox" style="width:100%" name="newfoto<?=$i; ?>" id="newfoto<?=$i; ?>" value="" onchange="document.getElementById('view_imagelist<?=$i; ?>').src = '/includes/images/after_save.jpg'" /></td><?
			?><td></td><?
		?></tr><?
		?><tr class="workspace"><?
				?><td></td><?
				?><td colspan="2"><table border="0" cellpadding="0" cellspacing="0"><tr><td><input name="i24_dosmallfoto<?=$i; ?>" type="checkbox" checked="checked" /></td><td>&nbsp;Уменьшить изображение</td></tr></table></td><?
		?></tr><?
		?><tr class="workspace"><?
				?><td></td><?
				?><td colspan="2"><table border="0" cellpadding="0" cellspacing="0"><tr><td><input name="i24_delfoto<?=$i; ?>" type="checkbox" /></td><td>&nbsp;Удалить изображение</td></tr></table></td><?
		?></tr><?

		?><tr class="workspace"><?
				?><td>Фото <?=$i; ?>:</td><?
				?><td colspan="2"><a class="highslide" onclick="return hs.expand(this)" href="<? eval( 'print site_url."/images/ad/good/".$row->imagesorg'.$i.' ;' ); ?>" >
				<img name="view_imagelist<?=$i; ?>" id="view_imagelist<?=$i; ?>" src="<? eval( 'print site_url."/images/ad/good/".$row->images'.$i.' ;' ); ?>" border="0" /></a></td><?
		?></tr><?
	}
	?><input type="hidden"  name="iuse" id="iuse" value="0" /><?
	?><input type="hidden" name="input_id" id="input_id" value="imagelist" /><?
	?><input type="hidden"  name="isrc_id" id="isrc_id" value="view_imagelist" /><?
?></table>
<input type="hidden" name="id" value="<? print $row->id; ?>" />
<input type="hidden" name="task" value="save"  />
<input type="hidden" name="ca" value="adgood" />

<script language="javascript">
function doform(){
	if (  document.adminForm.parent.value==0  ) { alert ("Выберите категорию"); return;}
	document.adminForm.submit();
	return 1;
}
</script>

<?
}



function editadgood2( $uid='0', $option='users' ) {
	global $database, $my, $acl, $mainframe, $reg;

	if (  $uid>0  ) $row = ggo ($uid, "#__adgood");
	else {
		$row->id = 0;
		$row->name = "";
		$row->sku = "";
		$row->parent = icsmarti('icsmart_adgood_parent');
		$row->sdesc = "";
		$row->publish = 1;
		$row->order = 1;
	}
	$vcats[] = mosHTML::makeOption( 0, "- Выберите категорию -");
	do_adcatlist(0, $vcats, 0);
//	ggtr ($vcats, 50);

?><form name="adminForm" action="index2.php" method="post" enctype="multipart/form-data"><input type="hidden"  name="iuse" id="iuse" value="0" />
<table class="adminheading"><tr><td class="edit"><?
			$iway[0]->name=$reg['ad_name'];
			$iway[0]->url="";
			$iway[1]->name= $row->id ? 'Редактирование' : 'Новый объявление';
			$iway[1]->url="";

			i24pwprint_admin ($iway,0);
?></td></tr></table>
<table border="0" cellpadding="4" cellspacing="0" width="100%" align="center">
	<tr class="workspace">
		<td>Категории: </td>
		<td>
			<? print mosHTML::selectList( $vcats, 'parent', 'class="inputbox" size="1" id="adgood" mosreq="1" moslabel="Группа" ', 'value', 'text', $row->parent ); ?>
		</td>
		<? $ins_adgood_price = ggsql (" select * from #__adgood_price ");
		$ins_adgood_price_count = count ($ins_adgood_price); ?>
		<td rowspan="5">
			<table border="0" cellpadding="4" cellspacing="0" width="95%" align="right">
				<? for ($i=1; $i<=$ins_adgood_price_count; $i++){ ?>
				<tr class="workspace">
					<td><? print $ins_adgood_price[$i-1]->d; ?>: </td>
					<td><input type="text" name="price<? print $i; ?>"  value="<?
						$eval_str = '$cprice = $row->price'.$i.'; ';
						eval ($eval_str);
						print $cprice;
					?>" /></td>
				</tr>
			<? } ?>
			</table>
		</td>
	</tr>
	<tr class="workspace">
		<td>Название: </td>
		<td><input name="name" size="104" mosreq="1" moslabel="Название" value="<? print ($row->name); ?>" /></td>
	</tr>
	<tr class="workspace">
		<td>Опубликованно: </td>
		<td><select name="publish">
			<option <? if (  $row->publish==1  ) print 'selected="selected"'; ?> value="1">да</option>
			<option <? if (  $row->publish==0  ) print 'selected="selected"'; ?> value="0">нет</option>
		</select></td>
	</tr>
	<tr class="workspace">
		<td>Спец. товар: </td>
		<td><select name="spec">
			<option <? if (  $row->spec==1  ) print 'selected="selected"'; ?> value="1">да</option>
			<option <? if (  $row->spec==0  ) print 'selected="selected"'; ?> value="0">нет</option>
		</select></td>
	</tr>

	<tr class="workspace">
		<td>Телефон и адрес клиента: </td>
		<td><input name="sku" size="104" mosreq="1" moslabel="Название" value="<? print ($row->sku); ?>" /></td>
	</tr>
	

	<tr class="workspace">
		<td>Информация об объекте: </td>
		<td colspan="2"><? editorArea( 'editor1',  ($row->sdesc) , 'sdesc', '100%;', '350', '75', '20' ) ; ?></td>
	</tr>
	
	<? $ins_adgood_f = ggsql (" select * from #__adgood_f ");
	$ins_adgood_f[0]->d = 'Цель и назначение';
	$ins_adgood_f[1]->d = 'Правоустанавливающие документы';
	$ins_adgood_f_count = count ($ins_adgood_f); ?>
	<? for ($i=1; $i<=$ins_adgood_f_count; $i++){ ?>
	<tr class="workspace">
		<td><? print $ins_adgood_f[$i-1]->d; ?>: </td>
		<td colspan="2"><input type="text" size="120" name="f<? print $i; ?>"  value="<?
			$eval_str = '$cf = $row->f'.$i.'; ';
			eval ($eval_str);
			print $cf;
		?>" /></td>
	</tr>
	<? } 
	?><tr class="workspace"><?
		?><td rowspan="2" valign="top" style="vertical-align:top">Основное изображение:</td><?
		?><td><? print $row->images; ?></td><?
		?><td></td><?
	?></tr><?
	?><tr class="workspace"><?
		?><td><input type="file" class="inputbox" style="width:100%" name="newfoto" id="newfoto" value="" onchange="document.getElementById('view_imagelist').src = '/includes/images/after_save.jpg'" /></td><?
		?><td></td><?
	?></tr><?
	
	?><tr class="workspace"><?
			?><td></td><?
			?><td colspan="2"><table border="0" cellpadding="0" cellspacing="0"><tr><td><input name="i24_dosmallfoto" type="checkbox" checked="checked" /></td><td>&nbsp;Уменьшить изображение</td></tr></table></td><?
	?></tr><?
	?><tr class="workspace"><?
			?><td></td><?
			?><td colspan="2"><table border="0" cellpadding="0" cellspacing="0"><tr><td><input name="i24_dеlfoto" type="checkbox" /></td><td>&nbsp;Удалить изображение</td></tr></table></td><?
	?></tr><?

	?><tr class="workspace"><?
			?><td>Основное изображение:</td><?
			?><td colspan="2"><a class="highslide" onclick="return hs.expand(this)" href="<? print site_url."/images/ad/good/".$row->imagesorg ?>" ><img name="view_imagelist" id="view_imagelist" src="<? print site_url."/images/ad/good/".$row->images ?>" border="0" /></a></td><?
	?></tr><?
	
	for ($i=2; $i<10; $i++){
		?><tr class="workspace"><?
			?><td rowspan="2" valign="top" style="vertical-align:top">Фото <?=$i; ?>:</td><?
			?><td><? eval( 'print $row->images'.$i.';' );  ?></td><?
			?><td></td><?
		?></tr><?
		?><tr class="workspace"><?
			?><td><input type="file" class="inputbox" style="width:100%" name="newfoto<?=$i; ?>" id="newfoto<?=$i; ?>" value="" onchange="document.getElementById('view_imagelist<?=$i; ?>').src = '/includes/images/after_save.jpg'" /></td><?
			?><td></td><?
		?></tr><?
		?><tr class="workspace"><?
				?><td></td><?
				?><td colspan="2"><table border="0" cellpadding="0" cellspacing="0"><tr><td><input name="i24_dosmallfoto<?=$i; ?>" type="checkbox" checked="checked" /></td><td>&nbsp;Уменьшить изображение</td></tr></table></td><?
		?></tr><?
		?><tr class="workspace"><?
				?><td></td><?
				?><td colspan="2"><table border="0" cellpadding="0" cellspacing="0"><tr><td><input name="i24_delfoto<?=$i; ?>" type="checkbox"  /></td><td>&nbsp;Удалить изображение</td></tr></table></td><?
		?></tr><?

		?><tr class="workspace"><?
				?><td>Фото <?=$i; ?>:</td><?
				?><td colspan="2"><a class="highslide" onclick="return hs.expand(this)" href="<? eval( 'print site_url."/images/ad/good/".$row->imagesorg'.$i.' ;' ); ?>" >
				<img name="view_imagelist<?=$i; ?>" id="view_imagelist<?=$i; ?>" src="<? eval( 'print site_url."/images/ad/good/".$row->images'.$i.' ;' ); ?>" border="0" /></a></td><?
		?></tr><?
	}
	?><input type="hidden"  name="iuse" id="iuse" value="0" /><?
	?><input type="hidden" name="input_id" id="input_id" value="imagelist" /><?
	?><input type="hidden"  name="isrc_id" id="isrc_id" value="view_imagelist" /><?
?></table>
<input type="hidden" name="id" value="<? print $row->id; ?>" />
<input type="hidden" name="task" value="save"  />
<input type="hidden" name="ca" value="adgood" />

<script language="javascript">
function doform(){
	if (  document.adminForm.parent.value==0  ) { alert ("Выберите категорию"); return;}
	document.adminForm.submit();
	return 1;
}
</script>

<?
}

function saveadgood( $task ) {
//	ggtr5 ($_REQUEST);  ggd ($_FILES);
	if (  ggri('id')>0  )  $adgood = ggo (ggri('id'), "#__adgood"); //ggd ($adgood);
	global $database, $my, $reg;
	$i24r = new mosDBTable( "#__adgood", "id", $database );
	$i24r->id = $_REQUEST['id'];
	$i24r->parent = $_REQUEST['parent'];
	$i24r->sku = $_REQUEST['sku'];
    $i24r->name = $_REQUEST['name'];
	$i24r->sdesc = $_REQUEST['sdesc'];
	$i24r->publish = $_REQUEST['publish'];
	$i24r->spec = $_REQUEST['spec'];
	if (  ggrr('sefname')!=''  ) $i24r->sefname = sefname( ggrr('sefname') );
	else $i24r->sefname = sefname( $i24r->name );
	
	if (  ggri('id')==0  or  $adgood->parent!=$_REQUEST['parent']  ){
		if (  $_REQUEST['parent']==0  ) $adgood->sefnamefullcat = '/'.$reg['ad_seoname'];
		else { $papa = ggo (  $_REQUEST['parent'], "#__adcat"  ); $i24r->sefnamefullcat = $papa->sefnamefull.'/'.$papa->sefname; }
	}


	$ins_adgood_price = ggsql (" select * from #__adgood_price ");
	$ins_adgood_price_count = count ($ins_adgood_price);
	for ($i=1; $i<=$ins_adgood_price_count; $i++){			 if (  $_REQUEST["price$i"]==''  ) $_REQUEST["price$i"] = 0;
			$eval_str = ' $i24r->price'.$i.'=$_REQUEST["price'.$i.'"]; '; eval ($eval_str);
	}
	$ins_adgood_f = ggsql (" select * from #__adgood_f ");
	$ins_adgood_f_count = count ($ins_adgood_f);
	for ($i=1; $i<=$ins_adgood_f_count; $i++){
			$eval_str = '$i24r->f'.$i.'=$_REQUEST["f'.$i.'"]; '; eval ($eval_str);
	}

	if (  $i24r->id>0  ) { $exoldgood = ggo (  $i24r->id, "#__adgood"  ); $_REQUEST["imagelistorg"] = $exoldgood->imagesorg; $_REQUEST["imagelist"] = $exoldgood->images; }

	if (  $_FILES["newfoto"]['tmp_name']  ){
		// УДАЛЕНИЕ СТАРЫХ ФОТО
		$ismallexname_old = site_path."/images/ad/good/".$exoldgood->images; 	delfile ($ismallexname_old);
		$isorgexname_old  = site_path."/images/ad/good/".$exoldgood->imagesorg;	delfile ($isorgexname_old);

		$iexfototype = "jpg";
		$iexuni = md5(uniqid("exsalon"));
		$_FILES["newfoto"]['name'] = trans2eng ($_FILES["newfoto"]['name']);
		$_FILES["newfoto"]['name'] = str_replace(" ", "_", $_FILES["newfoto"]['name']);
		$ismallexname = $_FILES["newfoto"]['name']."_small___".$iexuni.".".$iexfototype;
		$isorgexname = $_FILES["newfoto"]['name']."_orign___".$iexuni.".".$iexfototype; 
		i24makesmallfoto( $_FILES["newfoto"]['tmp_name'], site_path."/images/ad/good/".$isorgexname,
									$reg['adgoodmainorgwidth'],
									$reg['adgoodmainorgheight'],
									$reg['adgoodmaintag']);
		$i24r->imagesorg = $isorgexname;
		if (  isset($_POST['i24_dosmallfoto'])  ){	//  необходимо уменьшить основное изображение		
			i24makesmallfoto( $_FILES["newfoto"]['tmp_name'], site_path."/images/ad/good/".$ismallexname,
										$reg['adgoodmainsmallwidth'],
										$reg['adgoodmainsmallheight'],
										$reg['adgoodmaintag']);
			$i24r->images = $ismallexname;
		} else $i24r->images = $isorgexname;
	}	
	if (  isset($_REQUEST['i24_delfoto'])  ){
		$i24r->images = "";    $i24r->imagesorg = "";
	}

	
	
	
	
	for ($i=2; $i<10; $i++){
		if (  $_FILES["newfoto".$i]['tmp_name']  ){
			// УДАЛЕНИЕ СТАРЫХ ФОТО
			eval ( '$ismallexname_old = site_path."/images/ad/good/".$exoldgood->images'.$i.';' ); 			delfile ($ismallexname_old);
			eval ( '$isorgexname_old  = site_path."/images/ad/good/".$exoldgood->imagesorg'.$i.';' );		delfile ($isorgexname_old);
			//ggtr (  $ismallexname_old  ); ggtr (  $isorgexname_old  ); continue;
	
			$iexfototype = "jpg";
			$iexuni = md5(uniqid("exsalon"));
			$_FILES["newfoto".$i]['name'] = trans2eng ($_FILES["newfoto".$i]['name']);
			$_FILES["newfoto".$i]['name'] = str_replace(" ", "_", $_FILES["newfoto".$i]['name']);
			$ismallexname = $_FILES["newfoto".$i]['name']."_small___".$iexuni.".".$iexfototype;
			$isorgexname = $_FILES["newfoto".$i]['name']."_orign___".$iexuni.".".$iexfototype; 
			i24makesmallfoto( $_FILES["newfoto".$i]['tmp_name'], site_path."/images/ad/good/".$isorgexname,
										$reg['adgoodmainorgwidth'],
										$reg['adgoodmainorgheight'],
										$reg['adgoodmaintag']);
			eval ( '$i24r->imagesorg'.$i.' = $isorgexname;' );
			if (  isset($_POST['i24_dosmallfoto'.$i])  ){	//  необходимо уменьшить основное изображение		
				i24makesmallfoto( $_FILES["newfoto".$i]['tmp_name'], site_path."/images/ad/good/".$ismallexname,
											$reg['adgoodmainsmallwidth'],
											$reg['adgoodmainsmallheight'],
											$reg['adgoodmaintag']);
				eval ( '$i24r->images'.$i.' = $ismallexname;' );
			} else eval ( '$i24r->images'.$i.' = $isorgexname;' );
		}
		if (  isset($_REQUEST['i24_delfoto'.$i])  ){
			eval ( '$i24r->images'.$i.' = ""; ' );      eval ( '$i24r->imagesorg'.$i.' = ""; ' );
		}
	}


	if (  $i24r->id==0  ){
		$iexmaxorder = ggsql ("SELECT * FROM #__adgood WHERE parent=".$_REQUEST['parent']." ORDER BY #__adgood.order DESC LIMIT 0,1 "); // ggtr ($iexmaxorder);
		$i24r->order = $iexmaxorder[0]->order+1;
	}


	if (!$i24r->check()) { echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n"; } else $i24r->store();
	if (  ggri('id')==0  or  $adgood->parent!=$_REQUEST['parent']  ){
		if (  $adgood->parent>0  ) adcat_update_goods ( $adgood->parent );
		if (  ggri('parent')>0  ) adcat_update_goods ( ggri('parent') );
	}
	
	$adminlog = new adminlog();	
	if (  ggri('id')==0  )	$adminlog->logme('new', $reg['ad_name'], $i24r->name, $i24r->id ); else $adminlog->logme('save', $reg['ad_name'], $i24r->name, $i24r->id );
	
	switch ( $task ) {
		case 'apply':
			$msg = 'Объект сохранен: '. $i24r->name;  mosRedirect( 'index2.php?ca=adgood&task=editA&hidemainmenu=1&id='.$i24r->id, $msg );  break; 
		case 'save':
		default: 
			$msg = 'Объект сохранен: '. $i24r->name;  mosRedirect( 'index2.php?ca=adgood', $msg ); break;
	}
}

function removeadgood( $task ) {
	global $database, $my, $reg;
	foreach ($_REQUEST['cid'] as $dfgd){
		$delitem = ggo(  $dfgd, "#__adgood"  ); ggtr ($delitem);
													$ifile = site_path."/images/ad/good/".$delitem->images; 		delfile ($ifile);
													$ifile   = site_path."/images/ad/good/".$delitem->imagesorg; 	delfile ($ifile);
													$ifile   = site_path."/images/ad/good/".$delitem->imagesmid; 	delfile ($ifile);
													$ifile   = site_path."/images/ad/good/".$delitem->imagesfull; 	delfile ($ifile);
		for ($i=2; $i<10; $i++){
			eval ( '$ifile = site_path."/images/ad/good/".$delitem->images'.$i.'; ' ); 		delfile ($ifile);
			eval ( '$ifile = site_path."/images/ad/good/".$delitem->imagesorg'.$i.'; ' ); 	delfile ($ifile);
			eval ( '$ifile = site_path."/images/ad/good/".$delitem->imagesfull'.$i.'; ' ); 	delfile ($ifile);
		}
		$adminlog_obg = ggo($dfgd, "#__adgood");	$adminlog = new adminlog(); $adminlog->logme('del', $reg['ad_name'], $adminlog_obg->name, $adminlog_obg->id );
		ggsqlq ("DELETE FROM #__adgood WHERE id=".$dfgd);
		// удаление фото
	}
	$msg = 'Объявление(я) удалены: ';
	mosRedirect( 'index2.php?ca=adgood', $msg );
}


function moveadgood( $cid ) {
	global $database;
?><form name="adminForm" action="index2.php" method="post">
		<br/>
		<table class="adminheading"><tr><th class="edit">Перемещение объектов</th></tr></table>
		<br/>
		<table class="adminform"><tr>
			<td width="40%" valign="top" align="left">
			<strong>Переместить в категорию:</strong><br/><? 
			$vcats[] = mosHTML::makeOption( 0, "- Выберите категорию -");  do_adcatlist(0, $vcats, 0);
			print mosHTML::selectList( $vcats, 'parent', 'class="inputbox" size="1" id="adgood" mosreq="1" moslabel="Группа" ', 'value', 'text', $row->parent ); ?>
			<br/><br/>
			</td>
			<td valign="top" align="left">
			<strong>Будут перемещены товары:</strong>
			<br/>
			<ol>
				<? foreach ($cid as $ci){ safelySqlInt($ci); $adgood = ggo($ci, '#__adgood'); ?>
					<li><? print $adgood->name; ?></li><input type="hidden" value="<? print $ci; ?>" name="cid[]"/>
				<? } ?>
			</ol></td>
		</tr>
		</table>
		<br/><br/>

<input type="hidden" name="id" value="<? print $row->id; ?>" />
<input type="hidden" name="task" value="moveadgoodsave"  />
<input type="hidden" name="ca" value="adgood" />
<input type="hidden" name="hidemainmenu" value="1" />
</form>
<?
}
function copyadgood( $cid ) {
	global $database;
?><form name="adminForm" action="index2.php" method="post">
		<br/>
		<table class="adminheading"><tr><th class="edit">Перемещение объектов</th></tr></table>
		<br/>
		<table class="adminform"><tr>
			<td width="40%" valign="top" align="left">
			<strong>Копировать в категорию:</strong><br/><? 
			$vcats[] = mosHTML::makeOption( 0, "- Выберите категорию -");  do_adcatlist(0, $vcats, 0);
			print mosHTML::selectList( $vcats, 'parent', 'class="inputbox" size="1" id="adgood" mosreq="1" moslabel="Группа" ', 'value', 'text', $row->parent ); ?>
			<br/><br/>
			<strong>Префикс (для названия копии):</strong><br/>
			<input type="text" name="copyprefix" value="_копия" />
			</td>
			<td valign="top" align="left">
			<strong>Будут перемещены товары:</strong>
			<br/>
			<ol>
				<? foreach ($cid as $ci){ safelySqlInt($ci); $adgood = ggo($ci, '#__adgood'); ?>
					<li><? print $adgood->name; ?></li><input type="hidden" value="<? print $ci; ?>" name="cid[]"/>
				<? } ?>
			</ol></td>
		</tr>
		</table>
		<br/><br/>

<input type="hidden" name="id" value="<? print $row->id; ?>" />
<input type="hidden" name="task" value="moveadgoodsave"  />
<input type="hidden" name="ca" value="adgood" />
<input type="hidden" name="hidemainmenu" value="1" />
</form>
<?
}
function moveadgoodsave( $cid ) {
	global $database, $my;
	$adgoodparent = $_REQUEST['parent'];
	safelySqlInt($adgoodparent);
	foreach ($cid as $ci){ safelySqlInt($ci);
		$i24r = new mosDBTable( "#__adgood", "id", $database );
		$i24r->id = $ci;
		$i24r->parent = $adgoodparent;
		if (!$i24r->check()) {
			echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";
		} else $i24r->store();
	}
	$msg = 'Товар(ы) перемещены: ';
	mosRedirect( 'index2.php?ca=adgood', $msg );
}
function copyadgoodsave( $cid ) {
	global $database, $my, $reg;
	$adgoodparent = ggri('parent'); $adcat = ggo($adgoodparent, "#__adcat");
	$copyprefix = ggrr('copyprefix');
	if (  ggrr('copyprefix')==''  )	$copyprefix_sefname = "_copy";
	else 							$copyprefix_sefname = sefname(  ggrr('copyprefix')  );

	foreach ($cid as $ci){ safelySqlInt($ci); $adgood = ggo($ci, '#__adgood');
		$i24r = new mosDBTable( "#__adgood", "id", $database );
		foreach ( $adgood as $adgoodfield => $adgoodfield_value ){
			$istr = '$i24r->'.$adgoodfield.'=$adgood->'.$adgoodfield.'; ';	eval($istr);	//	ggtr ($istr);
		}
		$i24r->id = 0;
		$i24r->name	= $adgood->name.$copyprefix;
		$i24r->sefname 	= $adgood->sefname.$copyprefix_sefname;
		$i24r->parent = $adgoodparent;
		$i24r->sefnamefullcat 	= $adcat->sefnamefull.'/'.$adcat->sefname;
		if (!$i24r->check()) {			echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";		} else $i24r->store();
		// находим новый ID
		$iadgoodnewID = $i24r->id;
		// пересчет количества деток
		adcat_update_goods ( $adgoodparent );
		// копируем картинки::	
		$i24r = new mosDBTable( "#__adgood", "id", $database );
		$i24r->id = $iadgoodnewID;
		$foto_types = array("images", "imagesmid", "imagesorg", "imagesfull");
		for ($i=1; $i<10; $i++){
			if (  $i==1  ) $_image_post = "";	else $_image_post = "$i";
			$field_img = "images$_image_post";
			$path_parts = pathinfo( site_path.'/images/ad/good/'.$adgood->$field_img );	$file_ext = $path_parts['extension'];
			foreach (  $foto_types as $foto_type  ){ 	//	ggr($adgood);	
				$field_img = $foto_type.$_image_post;
				if (  $adgood->$field_img!=''  ){	// картинка есть - копируем
					$file = site_path."/images/ad/good/".$adgood->$field_img;	//	ggtr ($file);
					$newfile = $file.$iadgoodnewID.'.'.$file_ext;								//	ggtr ($newfile);
					if (!copy($file, $newfile)) ggd ( "ОШИБКА! Не удалось скопировать $file..." );
					else {   	$i24r->$field_img = $adgood->$field_img.".".$file_ext;   	}
				}
			}
		}
		if (!$i24r->check()) {			echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";		} else $i24r->store();

	}
	$msg = 'Объявления(е) скопированны в категорию '.stripslashes($adcat->name);
	mosRedirect( 'index2.php?ca=adgood', $msg );
}
?>