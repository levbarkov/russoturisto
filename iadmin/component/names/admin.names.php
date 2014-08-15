<?php
// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my, $task, $id, $reg;
$cid = josGetArrayInts( 'cid' );
switch ($task) {
        case 'apply':		
	case 'save':		savenames( $task );
						break;
	case 'editA':		editnames( $id, $option );
						break;
	case 'new':			editnames( 0, $option );
						break;
	case 'remove':		removenames( 0, $option );
						break;
	case 'saveorder':	saveOrdernames( $cid );
						break;
	default:			shownames( $option );
						break;
	case 'cfg':			cfg();
						break;
	case 'savecfg':		load_adminclass('config');	 $conf = new config($reg['db']);   $conf->save_config();	$adminlog = new adminlog(); $adminlog->logme('cfg', $reg['names_name'], "", "" );
						mosRedirect( 'index2.php?ca='.$reg['ca'].'&task=cfg', "Настройки сохранены" );
						break;
	case 'removecfg':	$adminlog = new adminlog(); $adminlog->logme('delcfg', $reg['names_name'], "", "" );
						load_adminclass('config'); $conf = new config($reg['db']); $conf->remove($_REQUEST['conf_values'], $_REQUEST['id']); 
						mosRedirect( 'index2.php?ca='.$reg['ca'].'&task=cfg', "Настройки удалены" );
						break;

}

function cfg(){
	global $reg;
	?><form <? ctrlEnterCtrlAS (' '.$reg['submit_apply_event'], ' '.$reg['submit_save_event']) ?> name="adminForm" action="index2.php" method="post"><input type="hidden"  name="iuse" id="iuse" value="0" />
	<? load_adminclass('config');	$conf = new config($reg['db']);
	$conf->show_config('names', "Настройки / ".$reg['names_name']); ?>
	<input type="hidden" name="task" value="savecfg"  />
	<input type="hidden" name="ca" value="<?=$reg['ca'] ?>" />
	<input type="submit" style="display:none;" /></form><?
}
function saveOrdernames( &$cid ) {
	global $database;
	for ($exi = 0; $exi<count($_REQUEST['order']); $exi++){
		$i24r = new mosDBTable( "#__names", "id", $database );
		$i24r->id = $_REQUEST['namesid'][$exi];
		$i24r->order = $_REQUEST['order'][$exi];
		if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	}
	$msg 	= 'Новый порядок сохранен'; $namesid	= intval( getUserStateFromRequest(  'id', 0 ) );
	mosRedirect( 'index2.php?ca=names&task=view&limit='.$_REQUEST['limit'].'&limitstart='.$_REQUEST['limitstart'], $msg );
} // saveOrder

function shownames( $option ) {
	global $database, $my, $iConfig_list_limit, $reg;
	$filter_type	= getUserStateFromRequest( 'filter_type', 0 );
	$filter_logged	= intval( getUserStateFromRequest(  'filter_logged', 0 ) );
	$limit 			= intval( getUserStateFromRequest( 'limit', $iConfig_list_limit ) );
	$limitstart 	= intval( getUserStateFromRequest( 'limitstart', 0 ) );
	
	$prop_rows = ggsql (  "select * from #__names_prop"  );
	$prop = libarray::convert_ggsql_object_to_array ($prop_rows);
	
	$where = array();
	if (  icsmart('icsmart_names_prop')  ) $where[]= " a.propid='".icsmart('icsmart_names_prop')."' ";
	if (  icsmart('icsmart_names_search')  ) $where[]= " a.name like '%".icsmart('icsmart_names_search')."%' ";
	$where_sql = ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" );
	
	$query = "SELECT COUNT(a.id) FROM #__names AS a $where_sql "; $database->setQuery( $query ); $total = $database->loadResult();
	require_once( site_path . '/iadmin/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );
	
	$allprops = ggsql (  "select id,name from #__names_prop "  ); $vcats[] = mosHTML::makeOption( "", "- Выберите категорию -");
	foreach ($allprops as $allprops1) $vcats[] = mosHTML::makeOption( $allprops1->id, $allprops1->name );
	
		?><form action="index2.php" method="post" name="adminForm">
		<table class="adminheading" ><tr><td width="100%"><?
				$iway[0]->name=$reg['names_name'];
				$iway[0]->url="index2.php?ca=names_prop&task=view";
				$iway[1]->name="Список";
				$iway[1]->url="";
	
				i24pwprint_admin ($iway);
			?></td><?
			?><td align="right" >Поиск:&nbsp;</td><?
			?><td align="right" ><input type="text" name="icsmart_names_search" value="<?php echo htmlspecialchars( icsmart('icsmart_names_search') );?>" class="inputtop" onchange="document.adminForm.submit();" /></td><?
			?><td ><?php print mosHTML::selectList( $vcats, 'icsmart_names_prop', 'class="inputtop" onchange="document.adminForm.submit();" size="1" id="icsmart_names_prop"  ', 'value', 'text', icsmart('icsmart_names_prop') ); ?></td><?
			?><td ><input type="submit" value="Искать" class="gosearch" /></td><?
		?></tr></table><?
		// инициализация класса необходимого для перемящаемой таблицы
		$table_drug  = new ajax_table_drug ;
		$table_drug->id="ajax_table_drug_td";
		$table_drug->table="#__names";
		$table_drug->order="ordering";
		?><table class="adminlist" <?=$table_drug->table(); ?> >
		<tr <?=$table_drug->row(); ?> ><?
			?><th width="2%" class="title" class="dragHandle">#</th><?
			?><th width="3%" class="title" class="dragHandle"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo ($total); ?>);" /></th><?
			?><th class="title" class="dragHandle">Название</th><?
			?><th class="title">Состояние</th><?
			?><th class="title">Свойство</th><?
			?><th align="center" width="5%">Сортировка</th><?
			?><th width="3%" ><a href="javascript: saveorder( <?php echo count( $rows )-1; ?> )" onMouseOver="return Tip('Сохранить заданный порядок отображения');">Сохранить&nbsp;порядок</a></th><?
		?></tr><?
		$k = 0;  
		$rows = ggsql("SELECT * FROM #__names AS a $where_sql ORDER BY a.ordering ASC ", $limitstart, $limit  );

			for ($i=0, $n=count( $rows ); $i < $n; $i++) {
				$exsi = $i;
				$row 	=& $rows[$i];			
				$task 	= $row->publish==0 ? 'unblock' : 'block';			
				$alt 	= $row->publish==0 ? '<span style="color:#ff0000;">Блокирован</span>' : 'Разрешен';
				$alt2 	= $row->publish==0 ? 'Снять блокировку' : 'Блокировать';
				$link 	= 'index2.php?ca='.$reg['ca'].'&amp;task=editA&amp;id='. $row->id. '&amp;hidemainmenu=1&amp;search='. $_REQUEST['search'].'&amp;filter_type='. $_REQUEST['filter_type'].'&amp;filter_logged='. $_REQUEST['filter_logged'];
				?><tr <?=$table_drug->row($row->id, $row->ordering); ?> class="<?php echo "row$k"; ?>"><?
					?><td><?php echo $exsi+1; ?></td><?
					?><td><?php echo mosHTML::idBox( $exsi, $row->id ); ?></td><?
					?><td align="left"><a href="<?php echo $link; ?>"><? print $row->name; ?></a></td><?
					?><td align="center"><a title="<? print $alt2; ?>" onclick="return listItemTask('cb<? print $exsi ?>','<? print $task; ?>')" href="javascript: void(0);"><?php echo $alt;?></a></td><?
					?><td align="left"><? print $prop[$row->propid]; ?></td><?
					?><td align="center" class="dragHandle drugme" >&nbsp;</td><?
					?><td align="center"><input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center" /><input type="hidden" name="namesid[]" value="<?php echo $row->id; ?>" /></td><?
				?></tr><?
				$k = 1 - $k; 
			}

		//shownames_rec( $k, 0, 0, $limit, $limitstart, $pageNav, $exsi, $table_drug )
		?></table><?
		$table_drug->debug_div();
		echo $pageNav->getListFooter(); ?>
		<input type="hidden" name="ca" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>
		<?php
}

function editnames( $uid='0', $option='users' ) {
	global $database, $my, $acl, $mainframe, $reg;
	
	if (  $uid>0  ) $row = ggo ($uid, "#__names");
	else {
		$row->id = 0;
		$row->propid = icsmarti('icsmart_names_prop');
		$row->name = "";
		$row->ldesc = "";
		$row->sdesc = "";
		$row->fdesc = "";
		$row->publish = 1;
	}
	
	$vcats[] = mosHTML::makeOption( "", "- Выберите свойство -");
	$ivcats = ggsql (  "select * from #__names_prop"  );
	foreach ($ivcats as $ivcat){
		$vcats[] = mosHTML::makeOption( $ivcat->id, $ivcat->name);
	}

	$component_foto = new component_foto ( 0 );
	$component_foto->init($reg['ca']);
	$component_foto->parent = $row->id;

        $component_file = new component_file ( 0 );
        $component_file->init( $reg['ca'] );
        $component_file->parent = $row->id;

	
?><form <? ctrlEnterCtrlAS (' '.$reg['submit_apply_event'], ' '.$reg['submit_save_event']) ?> name="adminForm" action="index2.php" method="post" enctype="multipart/form-data">
<table class="adminheading"><tr><td class="edit"><?
	$iway[0]->name=$reg['names_name'];
	$iway[0]->url="";
	$iway[1]->name=$row->id ? 'Редактирование' : 'Новый';
	$iway[1]->url="";

	i24pwprint_admin ($iway, 0);
?></td></tr></table>
<table border="0" cellpadding="4" cellspacing="0" width="100%" align="center">
	<tr class="workspace">
		<td>Свойство: </td>
		<td><? print mosHTML::selectList( $vcats, 'propid', 'class="inputbox" size="1" ', 'value', 'text', $row->propid ); ?></td>
	</tr>
	<tr class="workspace">
		<td>Название: </td>
		<td><input name="name" size="120" mosreq="1" moslabel="Название" value="<? print ($row->name); ?>" /></td>
	</tr>
    <tr class="workspace">
		<td>Внутреннее название: </td>
		<td><input name="innername" size="120" mosreq="1" moslabel="Внутреннее название" value="<? print ($row->innername); ?>" /></td>
	</tr>

	<tr class="workspace">
		<td>Очень краткое описание: </td>
		<td><? editorArea( 'editor1',  ($row->ldesc) , 'ldesc', '100%;', '150', '75', '5' ) ; ?></td>
	</tr>
	<tr class="workspace">
		<td>Краткое описание: </td>
		<td><? editorArea( 'editor1',  ($row->sdesc) , 'sdesc', '100%;', '250', '75', '20' ) ; ?></td>
	</tr>
	<tr class="workspace">
		<td>Подробное описание: </td>
		<td><? editorArea( 'editor1',  ($row->fdesc) , 'fdesc', '100%;', '350', '75', '30' ) ; ?></td>
	</tr>
	<tr class="workspace">
		<td>Опубликованно: </td>
		<td><select name="publish">
			<option <? if (  $row->publish==1  ) print 'selected="selected"'; ?> value="1">да</option>
			<option <? if (  $row->publish==0  ) print 'selected="selected"'; ?> value="0">нет</option>
		</select></td>
	</tr>
        <? itable_hr(2) ?>
	<tr class="workspace"><?
		?><td valign="top" style="vertical-align:top">Основное изображение:</td><?
		?><td><? print $row->small; ?></td><?
	?></tr><?
	?><tr class="workspace"><?
		?><td></td><?
		?><td><input type="file" class="inputbox"  name="newfoto" id="newfoto" value="" onchange="document.getElementById('view_imagelist').src = '/includes/images/after_save.jpg'" /></td><?
	?></tr><?
	?><tr class="workspace"><?
                        ?><td></td><?
			?><td><table border="0" cellpadding="0" cellspacing="0"><tr><? component_foto::delmainfoto_checkbox(); ?></tr></table></td><?
	?></tr><?
	?><tr class="workspace"><?
			?><td>Основное изображение:</td><?
			?><td ><? $component_foto->parent_obj=&$row; $component_foto->previewMainFoto(); ?></td><?
	?></tr><?
        itable_hr(2)
	?><tr class="workspace">
		<td>Прикрепленные файлы: <br /><br /><?
		if (  $row->id>0  ){
			$component_file->make_edit_link();
		} else {
			?><span class="gray">Прикрепление<br />файлов возможно только<br />после сохранения.</span><?
		} ?>
		</td>
                <td ><?
			$component_file->iadmin_show_files();
		?></td>
	</tr><?
	?><input type="hidden" name="iuse" id="iuse" value="0" /><?
	?><input type="hidden" name="input_id" id="input_id" value="imagelist" /><?
	?><input type="hidden" name="isrc_id" id="isrc_id" value="view_imagelist" />
</table>

<?
	/*
	 * ВОД ИНДИВИУАЛЬНЫХ НАСТРОЕК ДЛЯ ОБЪЕКТА
	 * например индивидуальные параметры для фотографий
	 */
	load_adminclass('config');	$conf = new config($reg['db']);
	$conf->prefix_id = '#__names'."_ID".$row->id."__";
	$conf->returnme('index2.php?ca='.$reg['ca'].'&task=editA&hidemainmenu=1&id='.$row->id );
	$conf->show_config($conf->prefix_id, "addition_ajax");	//Дополнительные настройки
?>

<input type="hidden" name="id" value="<? print $row->id; ?>" />
<input type="hidden" name="task" value="save"  />
<input type="hidden" name="ca" value="names" /></form>
<?
}

function savenames( $task ) {
	global $database, $my, $reg;

	$i24r = new mosDBTable( "#__names", "id", $database );
	$i24r->id = ggri('id');
	$i24r->propid = ggri('propid');
	$i24r->name = $_REQUEST['name'];	//safelySqlStr
	$i24r->innername = $_REQUEST['innername'];
	if (  $i24r->innername==''  ) $i24r->innername = $i24r->name;
        $i24r->ldesc = $_REQUEST['ldesc'];
        $i24r->sdesc = $_REQUEST['sdesc'];
        $i24r->fdesc = $_REQUEST['fdesc'];
	$i24r->publish = ggri('publish');
	if (  $i24r->id==0  ){
		$iexmaxorder = ggsql ("SELECT * FROM #__names ORDER BY #__names.ordering DESC LIMIT 0,1 ");
		$i24r->ordering = $iexmaxorder[0]->ordering+1;
	}
	if (!$i24r->check()) {		echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";	} else $i24r->store();

	
	$adminlog = new adminlog();	
	if (  ggri('id')==0  )	$adminlog->logme('new', $reg['names_name'], $i24r->name, $i24r->id ); else $adminlog->logme('save', $reg['names_name'], $i24r->name, $i24r->id );

	/*
	 * СОХРАНЯЕМ ИНДИВИДУАЛЬНЫЙ КОНФИГ
	 */	
	load_adminclass('config');	 
	$conf = new config($reg['db']);
	$conf->prefix_id = '#__names'."_ID".$i24r->id."__";
	$conf->save_config();

	// УДАЛЯЕМ ОСНОВНОЕ ФОТО, Если пользователь поставил галочку - Удалить изображение
        $component_foto = new component_foto( 0 );
        $component_foto->init( 'names_main' );
        $component_foto->parent = $i24r->id;
	$component_foto->delmainfoto_ifUserSetChackBox();

	if (  $_FILES["newfoto"]['tmp_name']  ){	// ВЫБРАНО НОВОЕ ФОТО - РЕДИРЕКТ НА ФОТОГАЛЕРЕЮ
		switch ( $task ) {
			case 'apply':	$ret_url = 'index2.php?ca=names&task=editA&hidemainmenu=1&id='.$i24r->id;  
					$ret_msg = 'Объект сохранен: '. $i24r->name; break;
			case 'save':
			default:    $ret_url = 'index2.php?ca=names';
                                    $ret_msg = 'Объект сохранен: '. $i24r->name; break;
		}
                $component_foto->publish = 'dont_save_publish';  // так как у объекта names - publish не актуален
                $component_foto->delmainfoto();
		$component_foto->external_foto($ret_url, $ret_msg); return;
	}	


	switch ( $task ) {
		case 'apply':
			$msg = 'Сохранено: '. $i24r->name;
                        mosRedirect( 'index2.php?ca='.$reg['ca'].'&task=editA&id='.$i24r->id.'&hidemainmenu=1&search=&filter_type=&filter_logged=', $msg );
			break;
		case 'save':
		default:
			$msg = 'Сохранено: '. $row->name;
			mosRedirect( 'index2.php?ca='.$reg['ca'], $msg );
			break;
	}
}

function removenames( $task ) {
	global $database, $my, $reg;
	foreach ($_REQUEST['cid'] as $dfgd){
		// удаляем фото
		$component_foto = new component_foto ( 0 );
		$component_foto->init($reg['ca']);
		$component_foto->parent = $dfgd;
		$component_foto->load_parent();
		$component_foto->del_fotos();

		$adminlog_obg = ggo($dfgd, "#__names");	$adminlog = new adminlog(); $adminlog->logme('del', $reg['names_name'], $adminlog_obg->name, $adminlog_obg->id );
		ggsqlq (  "DELETE FROM #__names WHERE id=".safelySqlStr($dfgd)  );

                // удаление индивидуальных настроек
                load_adminclass('config');
                $conf = new config($reg['db']);
                $conf->prefix_id = '#__names'."_ID".$dfgd."__";
                $conf->remove_addition_config();
	}
	$msg = 'Объект(ы) удалены: ';
	mosRedirect( 'index2.php?ca=names', $msg );
}
?>