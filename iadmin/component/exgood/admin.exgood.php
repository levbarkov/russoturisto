<?php

/*
 * класс exgood - содержит все метода работы с товаром
 *
 * get_main_foto ( $type, $noimage_return=0 ) - возвращает ссылку на основное фото или ссылку на noimage, $type = small/mid/org/full
 *
 */
 

/*
 * класс excat - содержит все метода работы с товаром
 */

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my, $task, $id;
require_once( site_path.'/component/ex/ex_lib.php' );
$cid = josGetArrayInts( 'cid' );
//ggtr ($_REQUEST);
//ggtr ($task); die();
switch ($task) {
	case 'apply':		
	case 'save':		saveexgood( $task );
						break;
	case 'blockthem':	changeExgoodBlock( $cid, 0, $option );
						break;
	case 'allowthem':	changeExgoodBlock( $cid, 1, $option );
						break;
	case 'block':		changeExgoodBlock( $cid, 0, $option );
						break;
	case 'unblock':		changeExgoodBlock( $cid, 1, $option );
						break;
	case 'spec':		changeExgoodSpec( $cid, 0, $option );
						break;
	case 'unspec':		changeExgoodSpec( $cid, 1, $option );
						break;

	case 'editA':		editexgood( $id, $option );
						break;
	case 'new':			editexgood( 0, $option );
						break;
	case 'remove':		removeexgood( 0, $option );
						break;
	case 'move':		moveexgood( $cid );
						break;
	case 'movesave':	moveexgoodsave( $cid );
						break;
	case 'copy':		copyexgood( $cid );
						break;
	case 'copysave':	copyexgoodsave( $cid );
						break;

	case 'saveorder':	saveOrderexgood( $cid );
						break;
	case 'orderup':		orderupexgood( $cid );
						break;
	case 'orderdown':	orderdownexgood( $cid );
						break;
	default:			showexgood( $option );
						break;
}

function orderupexgood( $cid ) {
	global $database;
	$exgoodfoto_this = ggo($_REQUEST['cid'][0], '#__exgood');
	ggtr ($exgoodfoto_this );		ggtr ($_REQUEST, 10); 
	$exgoodfoto_up = ggsql(" SELECT * FROM #__exgood WHERE #__exgood.order< ".$exgoodfoto_this->order." AND #__exgood.parent=".$exgoodfoto_this->parent." ORDER BY #__exgood.order DESC LIMIT 0,1 ;");  $exgoodfoto_up = $exgoodfoto_up[0];
//	ggtr ($database); die();
	$i24r = new mosDBTable( "#__exgood", "id", $database );
	$i24r->id = $_REQUEST['cid'][0];
	$i24r->order = $exgoodfoto_up->order;
//	ggtr ($i24r);
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();

	$i24r = new mosDBTable( "#__exgood", "id", $database );
	$i24r->id = $exgoodfoto_up->id;
	$i24r->order = $exgoodfoto_this->order;
//	ggtr ($i24r); die();
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	$msg = "Порядок изменен"; 
	mosRedirect( 'index2.php?ca=exgood&task=view&limit='.$_REQUEST['limit'].'&limitstart='.$_REQUEST['limitstart'], $msg );
}
function orderdownexgood( $cid ) {
	global $database;
	$exgoodfoto_this = ggo($_REQUEST['cid'][0], '#__exgood');
	ggtr ($exgoodfoto_this );		ggtr ($_REQUEST, 10); 
	$exgoodfoto_up = ggsql(" SELECT * FROM #__exgood WHERE #__exgood.order> ".$exgoodfoto_this->order." AND #__exgood.parent=".$exgoodfoto_this->parent." ORDER BY #__exgood.order ASC LIMIT 0,1 ;");  $exgoodfoto_up = $exgoodfoto_up[0];
//	ggtr ($database); die();
	$i24r = new mosDBTable( "#__exgood", "id", $database );
	$i24r->id = $_REQUEST['cid'][0];
	$i24r->order = $exgoodfoto_up->order;
//	ggtr ($i24r);
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();

	$i24r = new mosDBTable( "#__exgood", "id", $database );
	$i24r->id = $exgoodfoto_up->id;
	$i24r->order = $exgoodfoto_this->order;
//	ggtr ($i24r); die();
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	$msg = "Порядок изменен"; 
	mosRedirect( 'index2.php?ca=exgood&task=view&limit='.$_REQUEST['limit'].'&limitstart='.$_REQUEST['limitstart'], $msg );
}

function saveOrderexgood( &$cid ) {
	global $database;
//	ggtr ($_REQUEST); die();
	for ($exi = 0; $exi<count($_REQUEST['order']); $exi++){
		$i24r = new mosDBTable( "#__exgood", "id", $database );
		$i24r->id = $_REQUEST['exgoodid'][$exi];
		$i24r->order = $_REQUEST['order'][$exi];
		if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	}
	$msg 	= 'Новый порядок сохранен'; $exgoodid	= intval( getUserStateFromRequest(  'id', 0 ) );
	mosRedirect( 'index2.php?ca=exgood&task=view&limit='.$_REQUEST['limit'].'&limitstart='.$_REQUEST['limitstart'], $msg );
} // saveOrder


function showexgood( $option ) {
	global $database, $my, $iConfig_list_limit, $reg;
        iflush::flush(0);   // данные отправляем браузеру, для мгновенного отображения
        
	$filter_type	= getUserStateFromRequest( 'filter_type', 0 );
	$filter_logged	= intval( getUserStateFromRequest(  'filter_logged', 0 ) );
	$limit 			= intval( getUserStateFromRequest( 'limit', $iConfig_list_limit ) );
	$limitstart 	= intval( getUserStateFromRequest( 'limitstart', 0 ) );

        /*
         * ПОЛУЧАЕМ СПИСОК ВСЕХ ГРУПП ХАРАКТЕРИСТИК ТОВАРОВ
         */
        $all_expack_set_rows = ggsql (" select * from #__expack_set  ");
        $all_expack_set = libarray::convert_ggsql_object_to_array( $all_expack_set_rows );

	$where = array();
	if (  icsmart('icsmart_exgood_parent')  ) $where[]= " a.parent='".icsmart('icsmart_exgood_parent')."' ";
	if (  icsmart('icsmart_exgood_search')  ) $where[]= " a.name like '%".icsmart('icsmart_exgood_search')."%' ";
	
	$query = "SELECT COUNT(a.id) FROM #__exgood AS a ".( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" );  $database->setQuery( $query ); $total = $database->loadResult();
	$query_rows = "SELECT a.* FROM #__exgood AS a ".( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" ) . "ORDER BY a.order ASC LIMIT $limitstart, $limit ";
	$rows = ggsql( $query_rows );
	require_once( site_path . '/iadmin/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );
	
	$component_comment = new comments("exgood", $reg['db'], $reg);
	$component_comment->init();
		?><form action="index2.php" method="get" name="searchForm">
		<table class="adminheading"><tr><?
			?><td width="100%" nowrap="nowrap"><?
			$patheay_row_id = icsmarti('icsmart_exgood_parent');
			if (  $patheay_row_id>0  ) $patheay_row_data = ggo($patheay_row_id, "#__excat");
			else $patheay_row_data->name = '';

			$iway[0]->name=$reg['ex_name'];
			$iway[0]->url="index2.php?ca=excat";
			$iway[1]->name=stripslashes($patheay_row_data->name);
			$iway[1]->url="";
			$iway[2]->name="список товаров";
			$iway[2]->url="";

			i24pwprint_admin ($iway);

			?></td><?
			?><td align="right" >Поиск:&nbsp;</td><?
			?><td align="right" ><input type="text" name="icsmart_exgood_search" value="<?php echo htmlspecialchars( icsmart('icsmart_exgood_search') );?>" class="inputtop" onchange="document.searchForm.submit();" /></td><?
			?><td ><?php $vcats[] = mosHTML::makeOption( "", "- Выберите категорию -"); do_excatlist(0, $vcats, 0); print mosHTML::selectList( $vcats, 'icsmart_exgood_parent', 'class="inputtop" onchange="document.searchForm.submit();" size="1" id="parent" mosreq="1" moslabel="Группа" ', 'value', 'text', icsmart('icsmart_exgood_parent') ); ?></td><?
			?><td ><input type="submit" value="Искать" class="gosearch" /></td><?
		?></tr></table><input type="hidden" name="ca" value="<?php echo $option;?>" /><?
		?><input type="hidden" name="task" value="" /><?
		?></form><form action="index2.php" method="post" name="adminForm"><?
		if (  $patheay_row_id>0  ){
				$table_drug  = new ajax_table_drug ;
				$table_drug->id="ajax_table_drug_td";
				$table_drug->table="#__exgood";
				$table_drug->order="order";
		}
		?><table class="adminlist" <? if (  $patheay_row_id>0  ) print $table_drug->table(); ?> ><?
		?><tr  <? if (  $patheay_row_id>0  ) print $table_drug->row(); ?>  >
			<th width="2%" class="title">#</th>
			<th width="3%" class="title"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count($rows); ?>);" /></th>
			<th class="title"></th>
			<th class="title">Название</th>
			<th class="title">Категория</th><?
                        ?><th class="title">Группа&nbsp;хр-к</th><?
			?><th <? ($patheay_row_id>0 ? print '' : print 'colspan="2"') ?> align="center" width="5%">Сортировка</th><?
			?><th width="3%" ><a href="javascript: saveorder( <?php echo count( $rows )-1; ?> )" onMouseOver="return Tip('Сохранить заданный порядок отображения');">Сохранить&nbsp;порядок</a></th><?
			?><th class="title">Фото</th><?
			?><th class="title">Отзывы</th><?
			?><th class="title" style="text-align:center">Спец. товар</th><?
			?><th class="title" style="text-align:center">Доступ</th><?
		?></tr><?
		$k = 0;  $exsi = 0;
		$component_foto = new component_foto ( 0 );
		$component_foto->init($reg['ca']);
                iflush::flush(0);   // данные отправляем браузеру, для мгновенного отображения
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row 	=& $rows[$i];			
			$task 	= $row->publish==0 ? 'unblock' : 'block';			
			$alt 	= $row->publish==0 ? '<span style="color:#ff0000;">Блокирован</span>' : 'Разрешен';
			$alt2 	= $row->publish==0 ? 'Снять блокировку' : 'Блокировать';
			$link 	= 'index2.php?ca=exgood&amp;task=editA&amp;id='. $row->id. '&amp;hidemainmenu=1&amp;search='. $_REQUEST['search'].'&amp;filter_type='. $_REQUEST['filter_type'].'&amp;filter_logged='. $_REQUEST['filter_logged'];
			
			$spec_task 	= $row->spec==0 ? 'unspec' : 'spec';
			$spec_alt 	= $row->spec==1 ? '<span style="color:#ff0000;">Спец. товар</span>' : 'Обычный';
			$spec_alt2 	= $row->spec==1 ? 'Снять cпец. усл' : 'Сделать cпец. товаром';
			
			$component_foto->parent = $row->id;
//			ggtr ($exsi,4);  ggtr ($limitstart,1);  ggtr ($limit,1);

			?><tr <? if (  $patheay_row_id>0  ) print $table_drug->row($row->id, $row->order); ?>  class="<?php echo "row$k"; ?>"><?
				?><td><?php echo ($exsi+1); ?></td><?
				?><td><?php echo mosHTML::idBox( $exsi, $row->id ); ?></td><?
				?><td align="left"><? if (  $row->small  ) { ?><a onclick="return hs.expand(this)"  href="/images/ex/good/<?=$row->small ?>"><img src="/includes/images/cam.gif" width="22" height="15" border="0" /></a><? } ?></td><?
				?><td align="left"><?
				for ($j=0; $j<$exgoodlev; $j++) print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				?><a href="<?php echo $link; ?>"><? echo desafelySqlStr($row->name); ?></a><?
				?></td><?
				?><td><?php $iexcurcat = ggo($row->parent, "#__excat");
					$iexsubcat_prefix = "";  $iexcurcat_name = $iexcurcat->name; $iexcurcat_id = $iexcurcat->id;
					$iexcatlev = 0;  $iexcatslevs = array();
					while ($iexcurcat->parent!=0){
						$iexcurcat = ggo($iexcurcat->parent, "#__excat");
						$iexcatslevs[] = $iexcurcat->name;
					}
					for ($exc = count($iexcatslevs)-1; $exc>=0; $exc--){
						for ($j=0; $j<$exc; $j++) $iexsubcat_prefix_white .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
						$iexsubcat_prefix = $iexsubcat_prefix.$iexcatslevs[$exc]."<br />".$iexsubcat_prefix_white;
					}
					if (  $iexsubcat_prefix  ){
						$iexsubcat_prefix = $iexsubcat_prefix."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$iexcurcat_name;
						?><a href="index2.php?ca=excat&task=editA&id=<? print $iexcurcat_id; ?>&hidemainmenu=1" onMouseOver="return Tip('<? print $iexsubcat_prefix; ?>');"><? print ($iexcurcat_name);?></a><?
					} else {
						?><a href="index2.php?ca=excat&task=editA&id=<? print $iexcurcat_id; ?>&hidemainmenu=1"><? print ($iexcurcat_name); ?></a><?
					}
				?></td><?
                                ?><td><div id="pack_set_name<?=$row->id ?>"><a href="javascript: ins_ajax_open('?ca=expack&task=select_pack_set&good=<?=$row->id ?>&4ajax=1', 570, 570); void(0);" title="Выбрать группу характеристик"><?
                                    if (  isset($all_expack_set[$row->expack_set])  ) print desafelySqlStr($all_expack_set[$row->expack_set]); else print 'не указанна';
                                ?></a></div></td><?
				if (  $patheay_row_id>0  ){
					?><td align="center" class="dragHandle drugme" >&nbsp;</td><?
				} else {
					?><td align="right"><?php echo $pageNav->orderUpIcon( $exsi, ($row->parent == @$rows[$i-1]->parent) ); ?></td><?
					?><td align="left"><?php echo $pageNav->orderDownIcon( $exsi, $n, ($row->parent == @$rows[$i+1]->parent) ); ?></td><?
				}
				?><td align="center"><input type="text" name="order[]" size="5" value="<?php echo $row->order; ?>" class="text_area" style="text-align: center" /><input type="hidden" name="exgoodid[]" value="<?php echo $row->id; ?>" /></td><?
				?><td><a target="_blank" href="<?=$component_foto->get_link(); ?>">смотреть (<? print $component_foto->howmany_fotos(); ?>)</a></td><?
				?><td><a href="<? $component_comment->parent = $row->id; print $component_comment->get_link(); ?>">смотреть (<? print $component_comment->howmany_comments(); ?>)</a></td><?
				?><td align="center"><a title="<? print $spec_alt2; ?>" onclick="return listItemTask('cb<? print $exsi ?>','<? print $spec_task; ?>')" href="javascript: void(0);"><?php echo $spec_alt;?></a></td><?
				?><td align="center"><a title="<? print $alt2; ?>" onclick="return listItemTask('cb<? print $exsi ?>','<? print $task; ?>')" href="javascript: void(0);"><?php echo $alt;?></a></td><?
			?></tr><?
			$k = 1 - $k; 
			$exsi++;
                        iflush::flush(20); // данные отправляем браузеру, для мгновенного отображения
		}
		?></table><?php 
		if (  $patheay_row_id>0  ) $table_drug->debug_div();
		echo $pageNav->getListFooter(); ?>
		<input type="hidden" name="ca" value="<?php echo $option;?>" />
		<input type="hidden" name="parent" value="<?php echo $_REQUEST['parent'];?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" /></form>
		<?php
}


function changeExgoodBlock( $cid=null, $block=1, $option ) {
	global $database;
	$action = $block ? 'блокировки' : 'разблокировки';
	if (count( $cid ) < 1) {
		echo "<script type=\"text/javascript\"> alert('Выберите объект для $action'); window.history.go(-1);</script>\n";
		exit;
	}
	mosArrayToInts( $cid );
	$cids = 'id=' . implode( ' OR id=', $cid );
	$query = "UPDATE #__exgood"
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
function changeExgoodSpec( $cid=null, $block=1, $option ) {
	global $database;
	$action = $block ? 'спец. товара(ов)' : 'снятия спец. товара(ов)';
	if (count( $cid ) < 1) {
		echo "<script type=\"text/javascript\"> alert('Выберите объект(ы) для $action'); window.history.go(-1);</script>\n";
		exit;
	}
	mosArrayToInts( $cid );
	$cids = 'id=' . implode( ' OR id=', $cid );
	$query = "UPDATE #__exgood"
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

function editexgood( $uid='0', $option='users' ) {
	global $database, $my, $acl, $mainframe, $reg;

	if (  $uid>0  ) $row = ggo ($uid, "#__exgood");
	else {
		$row->id = 0;
		$row->name = "";
		$row->sku = "";
		$row->parent = icsmarti('icsmart_exgood_parent');
		$row->sdesc = "";
		$row->fdesc = "";
		$row->publish = 1;
		$row->order = 1;
	}
	$vcats[] = mosHTML::makeOption( 0, "- Выберите категорию -");
	do_excatlist(0, $vcats, 0);
	$mygood = new exgood();
	
	$component_foto = new component_foto ( 0 );
	$component_foto->init($reg['ca']);
	$component_foto->parent = $row->id;

        $component_file = new component_file ( 0 );
        $component_file->init( $reg['ca'] );
        $component_file->parent = $row->id;
	
	//names
	$names = new names($row->id, $reg['ca'], $reg);


//	ggtr ($vcats, 50);

?><form <? ctrlEnterCtrlAS (' '.$reg['submit_apply_event'], ' '.$reg['submit_save_event']) ?> name="adminForm" action="index2.php" method="post" enctype="multipart/form-data" ><input type="hidden"  name="iuse" id="iuse" value="0" />
<table class="adminheading"><tr><td class="edit"><?
			$iway[0]->name=$reg['ex_name'];
			$iway[0]->url="";
			$iway[1]->name= $row->id ? 'Редактирование' : 'Новый товар';
			$iway[1]->url="";

			i24pwprint_admin ($iway,0);
?></td></tr></table>
<table border="0" cellpadding="4" cellspacing="0" width="100%" align="center">
	<tr class="workspace">
		<td>Категории: </td>
		<td>
                    <table border="0" cellpadding="0" cellspacing="0" width="700" align="left">
                            <tr class="workspace">
                                    <td><? print mosHTML::selectList( $vcats, 'parent', 'class="inputbox" size="1" id="exgood" mosreq="1" moslabel="Группа" ', 'value', 'text', $row->parent ); ?></td>
                                    <td class="gray">Поле для синхронизации товара с внешней базой (1С)</td>
                                    <td ><input class="gray" name="connect" size="254" style="width:100px;" value="<? print ($row->connect); ?>" /></td>
                            </tr>
                    </table>
		</td><? 
		$all_cy_ggsql = ggsql (  "select * from #__exprice_cy "  );
		foreach ($all_cy_ggsql as $ivcat_cy)	$vcats_cy[] = mosHTML::makeOption( $ivcat_cy->id, $ivcat_cy->name); ?>

		<td rowspan="4" style="vertical-align:top; " valign="top">
			<? if (   $row->id==0  ){ ?>
				<table border="0" cellpadding="4" cellspacing="0" width="95%" align="right">
					<tr class="workspace">
						<td>Артикул: </td><td><input name="sku" size="254" style="width:140px;" value="<?= $row->sku ?>" /></td>
					</tr>
					<tr class="workspace">
						<td>Единица&nbsp;изм.: </td><td><input name="unit" size="254" style="width:140px;"  value="шт." /></td>
					</tr>
					<? $all_prices = ggsql (  "select * from #__exprice "  ); 
					foreach ($all_prices as $price) {?>
						<tr class="workspace">
							<td nowrap="nowrap"><?=$price->name ?>: </td><td><input name="price[<?=$price->id ?>]" size="36" style="width:70px;" value="<? print ($row->sku); ?>" /><? print mosHTML::selectList( $vcats_cy, 'cy['.$price->id.']', 'class="inputbox" style="border:0px none; "  size="1" ', 'value', 'text' ); ?></td>
						</tr>
					<? } ?>

				</table>
			<? } ?>
		</td>
	</tr>
	<tr class="workspace">
		<td>Название: </td>
		<td><input name="name" size="104" mosreq="1" moslabel="Название" value="<?= $row->name ?>" /></td>
	</tr>
	<tr class="workspace">
		<td class="gray">Адрес:&nbsp;</td>
		<td>
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
			<tr>
				<td nowrap="nowrap" class="gray"  width="10%" style="white-space:nowrap;"><?=site_url.$row->sefnamefullcat ?>/</td>
				<td align="left" width="90%"><input class="gray" name="sefname" size="44" mosreq="1" moslabel="Название" value="<? print ( $row->sefname ); ?>" /></td>
			</tr>
			</table>
		</td>
	</tr>				

	<tr class="workspace">
		<td></td>
		<td class="gray">Опубликовано: <select name="publish" class="noborder gray">
			<option <? if (  $row->publish==1  ) print 'selected="selected"'; ?> value="1">да</option>
			<option <? if (  $row->publish==0  ) print 'selected="selected"'; ?> value="0">нет</option>
		</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Спец. товар: <select name="spec"  class="noborder gray">
			<option <? if (  $row->spec==1  ) print 'selected="selected"'; ?> value="1">да</option>
			<option <? if (  $row->spec==0  ) print 'selected="selected"'; ?> value="0">нет</option>
		</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Метод выбора комплектации: <? 
			print mosHTML::selectList( $mygood->get_expack_select_type(), 'expack_select_type', 'class="inputbox noborder gray" size="1" ', 'value', 'text', $row->expack_select_type );
			?></td>
	</tr>
	
	<? if($reg["exgoodAllowTags"] == 1){ ?> 
		  <tr class="workspace">
			<td>Тэги: </td>
			<td colspan="2" nowrap="nowrap" style="white-space:nowrap;"><a href="javascript: ins_ajax_open('?ca=tags_ajax&task=showtags&4ajax=1', 570, 570); void(0);"  title="Показать все тэги"><img border="0" src="/iadmin/images/properties01.png"  align="absmiddle"/></a><?
				try {	$tag = new tags("exgood", $database, $reg);  print $tag->field($uid, 150, "exgood_tags", "ex_tegs_names_style");	}
			  	catch (Exception $e){	print $e->getMessage();   }
			?></td>
		</tr><?
	} ?>
        <tr class="workspace">
		<td><?=$reg['names_name']?>: </td>
		<td colspan="2" nowrap="nowrap" style="white-space:nowrap;">
		<a href="javascript: ins_ajax_open('?ca=names_ajax&task=shownames&4ajax=1', 570, 570); void(0);" title="Показать все значения"><img border="0" src="/iadmin/images/properties01.png"  align="absmiddle"/></a>
		<?
			 print $names->field($uid, 150, "all_names", "_names_field", "ex_tegs_names_style");
		?></td>
	</tr>
        <? /* редактирование поля - бренды <tr class="workspace">
		<td>Бренд: </td>
		<td colspan="2" nowrap="nowrap" style="white-space:nowrap;"><a href="javascript: ins_ajax_open('?ca=names_ajax&task=showbrands&4ajax=1', 570, 570); void(0);" title="Показать все значения"><img border="0" src="/iadmin/images/properties01.png"  align="absmiddle"/></a><?
			 //print $names->field($uid, 150, "all_brand", "_brand_field", "ex_tegs_names_style");
                if (  $row->brand  )  $mybrand = ggo ($row->brand, "#__names"); else $mybrand->innername='';
		?><input type="text" class="ex_tegs_names_style" value="<?=$mybrand->innername ?>" size="150" id="all_brand" name="_brand_field" ></td>
	</tr> */ ?>
	<tr class="workspace">
		<td>Общая информация: </td>
		<td colspan="2"><? editorArea( 'editor1',  ($row->sdesc) , 'sdesc', '100%;', '350', '75', '20' ) ; ?></td>
	</tr>
	
	<tr class="workspace">
		<td>Информация по визе: </td>
		<td colspan="2"><? editorArea( 'editor1',  ($row->fdesc) , 'fdesc', '100%;', '550', '75', '40' ) ; ?></td>
	</tr>
	<tr class="workspace"><?
		?><td rowspan="2" valign="top" style="vertical-align:top">Основное изображение:<br /><br /><a href="javascript: ins_ajax_open('?ca=limit&task=foto_limit&4ajax=1', 0, 0); void(0);">Смотреть ограничения на загрузку фото</a></td><?
		?><td><? print $row->images; ?></td><?
		?><td></td><?
	?></tr><?
	?><tr class="workspace"><?
		?><td><input type="file" class="inputbox" style="width:100%" name="newfoto" id="newfoto" value="" onchange="document.getElementById('view_imagelist').src = '/includes/images/after_save.jpg'" /><br>
                или введите <input <? insiteform::make_java_text_effect('furl', 'input_light'); ?> type="text" name="furl" id="furl" class="inputbox input_gray" style="width:700px;" value="URL фото (не обязательное)" title="URL фото (не обязательное)" /></td><?
		?><td></td><?
	?></tr><?
	?><tr class="workspace"><?
			?><td></td><?
			?><td colspan="2"><table border="0" cellpadding="0" cellspacing="0"><tr><td><input name="i24_dosmallfoto" type="checkbox" checked="checked" /></td><td style="padding-right:34px; ">&nbsp;Уменьшить изображение</td><? component_foto::delmainfoto_checkbox(); ?></tr></table></td><?
	?></tr><?
	?><tr class="workspace"><?
			?><td>Основное изображение:</td><?
			?><td colspan="2"><? $component_foto->parent_obj=&$row; $component_foto->previewMainFoto(); ?></td><?
	?></tr><?
	?><input type="hidden"  name="iuse" id="iuse" value="0" /><?
	?><input type="hidden" name="input_id" id="input_id" value="imagelist" /><?
	?><input type="hidden"  name="isrc_id" id="isrc_id" value="view_imagelist" /><?
	?><input type="hidden" name="id" value="<? print $row->id; ?>" /><?
	?><input type="hidden" name="task" value="apply"  /><?
	?><input type="hidden" name="ca" value="exgood" /><?
	?></form><?
        itable_hr(3);
	?><tr class="workspace">
		<td>Прикрепленные фото: <br /><br /><? $component_foto->make_galery_link() ?></td>
		<td colspan="2"><?
			$exfotos = $component_foto->get_fotos();
                        if (  count($exfotos)  )
			foreach ($exfotos as $exfoto){
				?><a title="нажмите чтобы увеличить" onclick="return hs.expand(this)" class="highslide" href="<? print site_url; ?>/images/ex/good/<? print $exfoto->org; ?>" ><img src="/images/ex/good/<? print $exfoto->small; ?>"  border="2" style="border-color:#888888" align="absmiddle"  vspace="1" /></a> <?
			}
		?></td>
	</tr>
        <? itable_hr(3) ?>
	<tr class="workspace">
		<td>Прикрепленные файлы: <br /><br /><?
		if (  $row->id>0  ){
			$component_file->make_edit_link();
		} else {
			?><span class="gray">Прикрепление<br />файлов возможно только<br />после сохранения.</span><?
		} ?>
		</td>
                <td colspan="2"><?
			$component_file->iadmin_show_files();
		?></td>
	</tr>
        <? itable_hr(3) ?>
	<tr class="workspace">
		<td>Группа<br />характеристик<br />товара:</td>
                <td colspan="2"><?
                    if  (  $row->id==0  ){  // новый товар
                        //смотрим какая группа характеристик у категории товара
                        if (  $row->parent!=0  ){  $excat = ggo($row->parent, '#__excat'); $expack_set = ggo ($excat->expack_set, "#__expack_set");  }
                        else                    {  $expack_set->id=0;  $expack_set->name="не указанна";    }
                    } else {     // товар уже создан
                        if (  $row->expack_set  )	$expack_set = ggo ($row->expack_set, "#__expack_set");
                        else {  $expack_set->id=0;  $expack_set->name="не указанна";    }
                    }

                    ?>Выбранная группа:&nbsp;<span id="pack_set_name<?=$row->id ?>"><?=$expack_set->name ?></span><input name="pack_set_val" id="pack_set_val" type="hidden" value="<?=$expack_set->id ?>" /><?
                    ?><br><br><a href="javascript: ins_ajax_open('?ca=expack&task=select_pack_set&good=<?=$row->id ?>&4ajax=1', 570, 570); void(0);">Выбрать группу&nbsp;характеристик</a><?

                ?></td>
	</tr>
        <? itable_hr(3) ?>
</table>

<? if (   $row->id>0  ){ ?>

	<script language="javascript">
	function toggle_unit(){
		if (  $("#unit").attr ('i24state')=='hide'  ){
			$("#unit").show();	$("#unit").attr ('i24state','display');
			ins_ajax_load_target ("ca=exunit&task=show_units&good=<?=$row->id ?>&4ajax=1", "#all_units");
		} else {
			$("#unit").hide();	$("#unit").attr ('i24state','hide');
		}	
	}
	</script>
	<table class="adminheading"><tr><td class="edit"><br /><a class="ajax_link" href="javascript: toggle_unit(); void(0); ">Единицы измерения</a></td></tr></table>
	<table border="0" cellpadding="4" cellspacing="0" width="100%" align="center"  i24state="hide" id="unit" style="display:none; ">
		<tr class="workspace">
			<td>
				<table width="100%" >
					<tr>
						<td colspan="2"><div id="all_units" ><img src="/iadmin/images/loading.gif" width="32" height="32" /></div></td>
					</tr>	
				</table>
			</td>
		</tr>
	</table>
	
	<script language="javascript">
	function toggle_boekomplekt(){
		if (  $("#boe_komplekt").attr ('i24state')=='hide'  ){
			$("#boe_komplekt").show();	$("#boe_komplekt").attr ('i24state','display');
			ins_ajax_load_target ("ca=expack&task=show_packs&good=<?=$row->id ?>&4ajax=1", "#all_packs");
		} else {
			$("#boe_komplekt").hide();	$("#boe_komplekt").attr ('i24state','hide');
		}	
	}
	</script>
	<table class="adminheading"><tr><td class="edit"><br /><a class="ajax_link" href="javascript: toggle_boekomplekt(); void(0); ">Комплектации товара, стоимость и остатки</a></td></tr></table>
	<?
		if (  $row->expack_set  )	$expack_set = ggo ($row->expack_set, "#__expack_set");
		else {
			$expack_set->id=0;
			$expack_set->name="не указанна";
		}
	?>
	<table border="0" cellpadding="4" cellspacing="0" width="100%" align="center"  i24state="hide" id="boe_komplekt" style="display:none; ">
		<tr class="workspace">
			<td>
				<table width="100%" >
					<tr>
						<td colspan="2"><div id="all_packs" ><img src="/iadmin/images/loading.gif" width="32" height="32" /></div></td>
					</tr>
	
					<tr>
						<td colspan="2"><img src="/iadmin/images/ins.png" width="16" height="16" align="absmiddle" />&nbsp;<a href="javascript: ins_ajax_open('?ca=expack&task=newpack&attrib_set='+$('#pack_set_val').val()+'&good=<?=$row->id ?>&4ajax=1', 570, 570); void(0);">Добавить комплектацию</a></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	
	
	<script language="javascript">
	function toggle_recomemnded(){
		if (  $("#recomemnded").attr ('i24state')=='hide'  ){
			$("#recomemnded").show();	$("#recomemnded").attr ('i24state','display');
			ins_ajax_load ("ca=exrecommended&task=show_recommended&good=<?=$row->id ?>&4ajax=1");
		} else {
			$("#recomemnded").hide();	$("#recomemnded").attr ('i24state','hide');
		}	
	}
	</script>
	<table class="adminheading"><tr><td class="edit"><br /><a class="ajax_link" href="javascript: toggle_recomemnded(); void(0); ">Рекомендуемые товары</a></td></tr></table>
	
	<table border="0" cellpadding="4" cellspacing="0" width="100%" align="center"  i24state="hide" id="recomemnded" style="display:none; ">
		<tr class="workspace">
			<td>
				<table width="100%" >
					<tr>
						<td><div id="all_recommended" ><img src="/iadmin/images/loading.gif" width="32" height="32" /></div></td>
					</tr>
	
					<tr>
						<td><a href="javascript: ins_ajax_open('?ca=exrecommended&task=newrecommended&parent=<?=$row->id ?>&good=<?=$row->id ?>&4ajax=1', 570, 570); void(0);">Добавить рекомендуемые товары</a></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	
	<?
		/*
		 * ВОД ИНДИВИУАЛЬНЫХ НАСТРОЕК ДЛЯ ОБЪЕКТА
		 * например индивидуальные параметры для фотографий
		 */
                 load_adminclass('config');	$conf = new config($reg['db']);
                 $conf->prefix_id = '#__exgood'."_ID".$row->id."__";
                 $conf->returnme('index2.php?ca='.$reg['ca'].'&task=editA&hidemainmenu=1&id='.$row->id );
                 $conf->show_config($conf->prefix_id, "addition_ajax");	//Дополнительные настройки
	?>

<? } ?>

<script language="javascript">
function doform(){
	if (  document.adminForm.parent.value==0  ) { alert ("Выберите категорию"); return;}
	document.adminForm.submit();
	return 1;
}
</script>
<?
}

function saveexgood( $task ) {
	global  $reg;
        // сохраняем товар
        $exgood = new exgood();
        $exgood->vars->id      = ggri('id');
        $exgood->vars->parent  = ggri('parent'); // категория товара
        $exgood->vars->name    = $_REQUEST['name'];
        $exgood->vars->sdesc   = $_REQUEST['sdesc'];
        $exgood->vars->fdesc   = $_REQUEST['fdesc'];
        $exgood->vars->publish = $_REQUEST['publish'];
        $exgood->vars->spec    = $_REQUEST['spec'];
        $exgood->vars->expack_select_type = $_REQUEST['expack_select_type'];
        $exgood->vars->expack_set = $_REQUEST['pack_set_val'];
        $exgood->vars->sefname = $_REQUEST['sefname'];
        $exgood->vars->connect = ggrr('connect');
        $exgood->vars->_tag_field   = $_REQUEST['_tag_field'];
        $exgood->vars->_names_field = $_REQUEST['_names_field'];
        $exgood->saveme();
        excat_update_goods ( $exgood->vars->parent );

        if (  $exgood->vars->id==0  ){
            // сохраняем единицу измерения
            $exunit = new exunit();
            $exunit->vars->id=0;      // 0 добавить новую, если >0 - то изменить существующую
            $exunit->vars->parent=$exgood->id;  // id товара (  таблица excgood )
            $exunit->vars->name=$_REQUEST['unit'];
            $exunit->saveme();

            // создаем комплектацию
            $expack = new expack();
            $expack->vars->id         = 0;  // 0 добавить новую, если >0 - то изменить существующую
            $expack->vars->sku        = $_REQUEST['sku'];
            $expack->vars->name       = $_REQUEST['unit'];
            $expack->vars->parent     = $exgood->id;  // id в #__exgood ( сам товар )
            $expack->vars->expack_set = $_REQUEST['pack_set_val']; // характеристики товара не используется
            $expack->vars->unit       = $exunit->id;   // id в #__exgood_unit ( единицы измерения )

            // указание стоимости и остатков для комплектации
            $expack->sklad   = array(); $all_sklads = ggsql (  "select * from #__exsklad "  );
            foreach ($all_sklads as $sklad ) $expack->sklad[$sklad->id] = 0;

            $expack->price   = $_REQUEST['price'];
            $expack->cy      = $_REQUEST['cy'];

            $expack->saveme();
        }
	
	$adminlog = new adminlog();
	if (  ggri('id')==0  )	$adminlog->logme('new', $reg['ex_name'], $exgood->vars->name, $exgood->id ); else $adminlog->logme('save', $reg['ex_name'], $exgood->vars->name, $exgood->id );
	
	/*
	 * СОХРАНЯЕМ ИНДИВИДУАЛЬНЫЙ КОНФИГ
	 */	
	load_adminclass('config');	 
	$conf = new config($reg['db']);
	$conf->prefix_id = '#__exgood'."_ID".$exgood->id."__";
	$conf->save_config();
	
	// УДАЛЯЕМ ОСНОВНОЕ ФОТО, Если пользователь поставил галочку - Удалить изображение
	$component_foto = new component_foto( 0 );
	$component_foto->init( 'exgood_main' );
	$component_foto->parent = $exgood->id;
	$component_foto->delmainfoto_ifUserSetChackBox();
	
	if (  $_FILES["newfoto"]['tmp_name']  ||  $_REQUEST['furl']!='URL фото (не обязательное)'  ){	// ВЫБРАНО НОВОЕ ФОТО - РЕДИРЕКТ НА ФОТОГАЛЕРЕЮ
		switch ( $task ) {
			case 'apply':	$ret_url = 'index2.php?ca=exgood&task=editA&hidemainmenu=1&id='.$exgood->id;
							$ret_msg = 'Объект сохранен: '. $exgood->vars->name; break;
			case 'save':
			default:		$ret_url = 'index2.php?ca=exgood';	  
							$ret_msg = 'Объект сохранен: '. $exgood->vars->name; break;
		}
                $component_foto->publish = 'dont_save_publish';  // так как у объекта exgood - publish не актуален
		$component_foto->delmainfoto();
		$component_foto->external_foto($ret_url, $ret_msg); return;
	}	
	// ggr ($_REQUEST); ggrd(0); return;
	switch ( $task ) {
		case 'apply':
			$msg = 'Объект сохранен: '. $exgood->vars->name;  mosRedirect( 'index2.php?ca=exgood&task=editA&hidemainmenu=1&id='.$exgood->id, $msg ); break;
		case 'save':
		default:
			$msg = 'Объект сохранен: '. $exgood->vars->name;   mosRedirect( 'index2.php?ca=exgood', $msg ); break;
	}
}

function removeexgood( $task ) {
	global $database, $my, $reg;
        $exgood = new exgood();

	foreach ($_REQUEST['cid'] as $dfgd){
	              ilog::vlog('{ удаление товара id='.$dfgd);
                $exgood->id = $dfgd;
                $exgood->delme( 1 );
                ilog::vlog('удаление товара }');
	}
	$msg = 'Товар(ы) удалены: ';
	mosRedirect( 'index2.php?ca=exgood', $msg );
}


function moveexgood( $cid ) {
	global $database;
?><form name="adminForm" action="index2.php" method="post">
		<br/>
		<table class="adminheading"><tr><th class="edit">Перемещение объектов</th></tr></table>
		<br/>
		<table class="adminform"><tr>
			<td width="40%" valign="top" align="left">
			<strong>Переместить в категорию:</strong><br/><? 
			$vcats[] = mosHTML::makeOption( 0, "- Выберите категорию -");  do_excatlist(0, $vcats, 0);
			print mosHTML::selectList( $vcats, 'parent', 'class="inputbox" size="1" id="exgood" mosreq="1" moslabel="Группа" ', 'value', 'text', $row->parent ); ?>
			<br/><br/>
			</td>
			<td valign="top" align="left">
			<strong>Будут перемещены товары:</strong>
			<br/>
			<ol>
				<? foreach ($cid as $ci){ safelySqlInt($ci); $exgood = ggo($ci, '#__exgood'); ?>
					<li><? print $exgood->name; ?></li><input type="hidden" value="<? print $ci; ?>" name="cid[]"/>
				<? } ?>
			</ol></td>
		</tr>
		</table>
		<br/><br/>

<input type="hidden" name="id" value="<? print $row->id; ?>" />
<input type="hidden" name="task" value="moveexgoodsave"  />
<input type="hidden" name="ca" value="exgood" />
<input type="hidden" name="hidemainmenu" value="1" />
</form>
<?
}
function copyexgood( $cid ) {
	global $database;
?><form name="adminForm" action="index2.php" method="post">
		<br/>
		<table class="adminheading"><tr><th class="edit">Копирование объектов</th></tr></table>
		<br/>
		<table class="adminform"><tr>
			<td width="40%" valign="top" align="left">
			<strong>Копировать в категорию:</strong><br/><? 
			$vcats[] = mosHTML::makeOption( 0, "- Выберите категорию -");  do_excatlist(0, $vcats, 0);
			print mosHTML::selectList( $vcats, 'parent', 'class="inputbox" size="1" id="exgood" mosreq="1" moslabel="Группа" ', 'value', 'text', $row->parent ); ?>
			<br/><br/>
			<strong>Префикс (для названия копии):</strong><br/>
			<input type="text" name="copyprefix" value="_копия" />
			</td>
			<td valign="top" align="left">
			<strong>Будут скопированы товары:</strong>
			<br/>
			<ol>
				<? foreach ($cid as $ci){ safelySqlInt($ci); $exgood = ggo($ci, '#__exgood'); ?>
					<li><? print $exgood->name; ?></li><input type="hidden" value="<? print $ci; ?>" name="cid[]"/>
				<? } ?>
			</ol></td>
		</tr>
		</table>
		<br/><br/>

<input type="hidden" name="id" value="<? print $row->id; ?>" />
<input type="hidden" name="task" value="copyexgoodsave"  />
<input type="hidden" name="ca" value="exgood" />
<input type="hidden" name="hidemainmenu" value="1" />
</form>
<?
}
function moveexgoodsave( $cid ) {
	global $database, $my;
	$exgoodparent = $_REQUEST['parent'];
	safelySqlInt($exgoodparent);
	foreach ($cid as $ci){ safelySqlInt($ci);
		$i24r = new mosDBTable( "#__exgood", "id", $database );
		$i24r->id = $ci;
		$i24r->parent = $exgoodparent;
		if (!$i24r->check()) {
			echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";
		} else $i24r->store();
	}
	excat_update_goods ( $exgoodparent );
	$msg = 'Товар(ы) перемещены: ';
	mosRedirect( 'index2.php?ca=exgood', $msg );
}
function copyexgoodsave( $cid ) {
	global $database, $my, $reg;
	$exgoodparent = $_REQUEST['parent'];
	safelySqlInt($exgoodparent);
	$copyprefix = $_REQUEST['copyprefix'];
	foreach ($cid as $ci){ $ci = safelySqlInt($ci); $exgood = ggo($ci, '#__exgood');
	   $old_cat = $exgood->parent;
            $i24r = new mosDBTable( "#__exgood", "id", $database );
            foreach ( $exgood as $k => $v ){
                $i24r->$k = $v;
            }
            $i24r->id = 0;
            $i24r->name = $exgood->name.$copyprefix;
            $i24r->parent = $exgoodparent;
            $i24r->sefname = sefname( $i24r->name );

            $query = "select id from #__exgood where parent = {$exgoodparent} and sefname = '{$i24r->sefname}'";
            if (count(ggsql($query)) > 0) {
                $query = "select max(id) + 1 as id from #__exgood";
                $max_ids = ggsql($query);
                $i24r->sefname .= '_'.$max_ids[0]->id;
            }

            $parent = ggo ($exgoodparent, "#__excat");
            $i24r->sefnamefullcat = $parent->sefnamefull.'/'.$parent->sefname;
            if (!$i24r->check()) {
                    echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";
            } else $i24r->store();
            // находим новый ID
            $iexgoodnewID = $i24r->id;

            // теперь необходимо сделать копию основного фото
            $component_foto = new component_foto ( 0 );
            $component_foto->init($reg['ca'].'_main');
            $component_foto->parent = $ci;
            $component_foto->load_parent();
            $component_foto->copy_main_foto( $iexgoodnewID );

            // теперь необходимо организовать копию фото товаров
            $component_foto2 = new component_foto ( 0 );
            $component_foto2->init($reg['ca']);
            $component_foto2->parent = $ci;
            $component_foto2->copy_fotos( $iexgoodnewID );

            // копирование стоимости и остатков
            $expacks = ggsql("select * from #__expack where parent={$ci}");
            foreach ($expacks as $pack) {
                $pack_obj = new mosDBTable( "#__expack", "id", $database );
                foreach ($pack as $k => $v) {
                    $pack_obj->$k = $v;
                }
                $pack_obj->id = 0;
                $pack_obj->expack_set = 0;
                $pack_obj->parent = $iexgoodnewID;
                if (!$pack_obj->check())
                    echo "<script> alert('".$pack_obj->getError()."'); window.history.go(-1); </script>\n";
                else $pack_obj->store();
                $pack_id = $pack_obj->id;

                $exprice_goods = ggsql("select * from #__exprice_good where expack={$pack->id}");
                foreach ($exprice_goods as $price_good) {
                    $price_good_obj = new mosDBTable( "#__exprice_good", "id", $database );
                    foreach ($price_good as $k => $v) {
                        $price_good_obj->$k = $v;
                    }
                    $price_good_obj->expack = $pack_id;
                    if (!$price_good_obj->check())
                        echo "<script> alert('".$price_good_obj->getError()."'); window.history.go(-1); </script>\n";
                    else $price_good_obj->store();
                }

                $exsklad_goods = ggsql("select * from #__exsklad_good where expack={$pack->id}");
                foreach ($exsklad_goods as $sklad_good) {
                    $sklad_good_obj = new mosDBTable( "#__exsklad_good", "id", $database );
                    foreach ($sklad_good as $k => $v) {
                        $sklad_good_obj->$k = $v;
                    }
                    $sklad_good_obj->expack = $pack_id;
                    if (!$sklad_good_obj->check())
                        echo "<script> alert('".$sklad_good_obj->getError()."'); window.history.go(-1); </script>\n";
                    else $sklad_good_obj->store();
                }

                $expack_sets = ggsql("select * from #__expack_set where id={$pack->expack_set}");
                $pack_set = $expack_sets[0];
                $pack_set_obj = new mosDBTable( "#__expack_set", "id", $database );
                foreach ($pack_set as $k => $v) {
                    $pack_set_obj->$k = $v;
                }
                $pack_set_obj->id = 0;
                if (!$pack_set_obj->check())
                    echo "<script> alert('".$pack_set_obj->getError()."'); window.history.go(-1); </script>\n";
                else $pack_set_obj->store();
                $pack_set_id = $pack_set_obj->id;

                $pack_obj->expack_set = $pack_set_id;
                if (!$pack_obj->check())
                    echo "<script> alert('".$pack_obj->getError()."'); window.history.go(-1); </script>\n";
                else $pack_obj->store();

                $expack_set_vals = ggsql("select * from #__expack_set_val where pack_id={$pack->id}");
                foreach ($expack_set_vals as $set_val) {
                    $set_val_obj = new mosDBTable( "#__expack_set_val", "id", $database );
                    foreach ($set_val as $k => $v) {
                        $set_val_obj->$k = $v;
                    }
                    $set_val_obj->pack_id = $pack_id;
                    if (!$set_val_obj->check())
                        echo "<script> alert('".$set_val_obj->getError()."'); window.history.go(-1); </script>\n";
                    else $set_val_obj->store();
                }
            }

  	excat_update_goods ( $old_cat );
	}
	excat_update_goods ( $exgoodparent );
	// return;
	$msg = 'Товар(ы) скопированы: ';
	mosRedirect( 'index2.php?ca=exgood', $msg );
}

?>