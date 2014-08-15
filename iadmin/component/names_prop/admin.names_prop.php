<?php
// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my, $task, $id, $reg;
$cid = josGetArrayInts( 'cid' );
switch ($task) {
        case 'apply':
	case 'save':		savenames_prop( $task );
						break;
	case 'editA':		editnames_prop( $id, $option );
						break;
	case 'new':			editnames_prop( 0, $option );
						break;
	case 'remove':		removenames_prop( 0, $option );
						break;
	case 'saveorder':	saveOrdernames_prop( $cid );
						break;
	default:			shownames_prop( $option );
						break;
}

function saveOrdernames_prop( &$cid ) {
	global $database;
	for ($exi = 0; $exi<count($_REQUEST['order']); $exi++){
		$i24r = new mosDBTable( "#__names_prop", "id", $database );
		$i24r->id = $_REQUEST['names_propid'][$exi];
		$i24r->ordering = $_REQUEST['order'][$exi];
		if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	}
	$msg 	= 'Новый порядок сохранен'; $names_propid	= intval( getUserStateFromRequest(  'id', 0 ) );
	mosRedirect( 'index2.php?ca=names_prop&task=view&limit='.$_REQUEST['limit'].'&limitstart='.$_REQUEST['limitstart'], $msg );
} // saveOrder

function shownames_prop( $option ) {
	global $database, $my, $iConfig_list_limit, $reg;
	$filter_type	= getUserStateFromRequest( 'filter_type', 0 );
	$filter_logged	= intval( getUserStateFromRequest(  'filter_logged', 0 ) );
	$limit 			= intval( getUserStateFromRequest( 'limit', $iConfig_list_limit ) );
	$limitstart 	= intval( getUserStateFromRequest( 'limitstart', 0 ) );
	
	$names = new names();
	
	$query = "SELECT COUNT(a.id) FROM #__names_prop AS a  "; $database->setQuery( $query ); $total = $database->loadResult();
	require_once( site_path . '/iadmin/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );
		?><form action="index2.php" method="post" name="adminForm">
		<table class="adminheading" ><tr><td width="100%"><?
			$iway[0]->name=$reg['names_name'];
			$iway[0]->url="";
			$iway[1]->name="Рубрики";
			$iway[1]->url="";

			i24pwprint_admin ($iway);
		?></td></tr></table><?
		// инициализация класса необходимого для перемящаемой таблицы
		$table_drug  = new ajax_table_drug ;
		$table_drug->id="ajax_table_drug_td";
		$table_drug->table="#__names_prop";
		$table_drug->order="ordering";
		?><table class="adminlist" <?=$table_drug->table(); ?> >
		<tr <?=$table_drug->row(); ?> ><?
			?><th width="2%" class="title" class="dragHandle">#</th><?
			?><th width="3%" class="title" class="dragHandle"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo ($total); ?>);" /></th><?
			?><th class="title" class="dragHandle">Название</th><?
			?><th class="title" class="dragHandle">Свойства</th><?
			?><th align="center" width="5%">Сортировка</th><?
			?><th width="3%" ><a href="javascript: saveorder( <?php echo count( $rows )-1; ?> )" onMouseOver="return Tip('Сохранить заданный порядок отображения');">Сохранить&nbsp;порядок</a></th><?
		?></tr><?
		$k = 0;  
		$rows = ggsql("SELECT * FROM #__names_prop AS a ORDER BY a.ordering ASC; ");
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
					?><td align="left"><a href="?ca=names&task=view&icsmart_names_prop=<?=$row->id ?>">Свойства (<?=$names->howmany_names($row->id) ?>)</a></td><?
					?><td align="center" class="dragHandle drugme" >&nbsp;</td><?
					?><td align="center"><input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center" /><input type="hidden" name="names_propid[]" value="<?php echo $row->id; ?>" /></td><?
				?></tr><?
				$k = 1 - $k; 
			}

		//shownames_prop_rec( $k, 0, 0, $limit, $limitstart, $pageNav, $exsi, $table_drug )
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

function editnames_prop( $uid='0', $option='users' ) {
	global $database, $my, $acl, $mainframe, $reg;
	
	if (  $uid>0  ) $row = ggo ($uid, "#__names_prop");
	else {
		$row->id = 0;
		$row->name = "";
	}
	$component_foto = new component_foto ( 0 );
	$component_foto->init($reg['ca']);
	$component_foto->parent = $row->id;
        
        $component_file = new component_file ( 0 );
        $component_file->init( $reg['ca'] );
        $component_file->parent = $row->id;

	
?><form <? ctrlEnterCtrlAS (' '.$reg['submit_apply_event'], ' '.$reg['submit_save_event']) ?> name="adminForm" action="index2.php" method="post" enctype="multipart/form-data">
<table class="adminheading"><tr><td class="edit"><?
	$iway[0]->name=$reg['names_prop_name'];
	$iway[0]->url="";
	$iway[1]->name=$row->id ? 'Редактирование' : 'Новый';
	$iway[1]->url="";

	i24pwprint_admin ($iway, 0);
?></td></tr></table>
<table border="0" cellpadding="4" cellspacing="0" width="100%" align="center">
	<tr class="workspace">
		<td>Название: </td>
		<td><input name="name" size="120" mosreq="1" moslabel="Название" value="<? print ($row->name); ?>" /></td>
	</tr>
        <? itable_hr(2); ?>
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
	$conf->prefix_id = '#__names_prop'."_ID".$row->id."__";
	$conf->returnme('index2.php?ca='.$reg['ca'].'&task=editA&hidemainmenu=1&id='.$row->id );
	$conf->show_config($conf->prefix_id, "addition_ajax");	//Дополнительные настройки
?>

<input type="hidden" name="id" value="<? print $row->id; ?>" />
<input type="hidden" name="task" value="save"  />
<input type="hidden" name="ca" value="names_prop" />
<?
}

function savenames_prop( $task ) {
	global $database, $my, $reg;

	$i24r = new mosDBTable( "#__names_prop", "id", $database );
	$i24r->id = ggri('id');
	$i24r->name = $_REQUEST['name'];	//safelySqlStr
	if (  $i24r->id==0  ){
		$iexmaxorder = ggsql ("SELECT * FROM #__names_prop ORDER BY #__names_prop.ordering DESC LIMIT 0,1 ");
		$i24r->ordering = $iexmaxorder[0]->ordering+1;
	}
	if (!$i24r->check()) {		echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";	} else $i24r->store();
	
	$adminlog = new adminlog();	
	if (  ggri('id')==0  )	$adminlog->logme('new_cat', $reg['names_name'], $i24r->name, $i24r->id ); else $adminlog->logme('save_cat', $reg['names_name'], $i24r->name, $i24r->id );

	/*
	 * СОХРАНЯЕМ ИНДИВИДУАЛЬНЫЙ КОНФИГ
	 */	
	load_adminclass('config');	 
	$conf = new config($reg['db']);
	$conf->prefix_id = '#__names_prop'."_ID".$i24r->id."__";
	$conf->save_config();

	// УДАЛЯЕМ ОСНОВНОЕ ФОТО, Если пользователь поставил галочку - Удалить изображение
        $component_foto = new component_foto( 0 );
        $component_foto->init( 'names_prop_main' );
        $component_foto->parent = $i24r->id;
	$component_foto->delmainfoto_ifUserSetChackBox();

	if (  $_FILES["newfoto"]['tmp_name']  ){	// ВЫБРАНО НОВОЕ ФОТО - РЕДИРЕКТ НА ФОТОГАЛЕРЕЮ
		switch ( $task ) {
			case 'apply':	$ret_url = 'index2.php?ca=names_prop&task=editA&hidemainmenu=1&id='.$i24r->id;  
							$ret_msg = 'Объект сохранен: '. $i24r->name; break;
			case 'save':
			default:		$ret_url = 'index2.php?ca=names_prop';
							$ret_msg = 'Объект сохранен: '. $i24r->name; break;
		}
                $component_foto->publish = 'dont_save_publish';  // так как у объекта names - publish не актуален
                $component_foto->delmainfoto();
		$component_foto->external_foto($ret_url, $ret_msg); return;
	}	


	switch ( $task ) {
                case 'apply':
                        $msg = 'Сохранено: '. $i24r->name;
			mosRedirect( 'index2.php?ca='.$reg['ca'].'&task=editA&hidemainmenu=1&id='.$i24r->id, $msg );
			break;
		case 'save':
		default:
			$msg = 'Сохранено: '. $i24r->name;
			mosRedirect( 'index2.php?ca='.$reg['ca'], $msg );
			break;
	}
}

function removenames_prop( $task ) {
	global $database, $my, $reg;
	foreach ($_REQUEST['cid'] as $dfgd){
		// удаляем фото
		$component_foto = new component_foto ( 0 );
		$component_foto->init($reg['ca']);
		$component_foto->parent = $dfgd;
		$component_foto->load_parent();
		$component_foto->del_fotos();

		$adminlog_obg = ggo($dfgd, "#__names_prop");	$adminlog = new adminlog(); $adminlog->logme('del_cat', $reg['names_name'], $adminlog_obg->name, $adminlog_obg->id );
		ggsqlq (  "DELETE FROM #__names_prop WHERE id=".safelySqlStr($dfgd)  );

                // удаление индивидуальных настроек
                load_adminclass('config');
                $conf = new config($reg['db']);
                $conf->prefix_id = '#__names_prop'."_ID".$dfgd."__";
                $conf->remove_addition_config();
	}
	$msg = 'Объект(ы) удалены: ';
	mosRedirect( 'index2.php?ca=names_prop', $msg );
}
?>