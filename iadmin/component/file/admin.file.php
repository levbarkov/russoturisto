<?php
// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my, $task, $id, $reg;
//$task 			= strval( mosGetParam( $_REQUEST, 'task', '' ) );
//ggtr ($_REQUEST); ggtr ($task);  //die();
$cid = josGetArrayInts( 'cid' );
switch ($task) {
	case 'cfg':			cfg();
						break;
	case 'savecfg':		load_adminclass('config');	 $conf = new config($reg['db']);   $conf->save_config();	$adminlog = new adminlog(); $adminlog->logme('cfg', $reg['file_name'], "", "" );
						mosRedirect( 'index2.php?ca='.$reg['ca'].'&task=cfg', "Настройки сохранены" );
						break;
	case 'removecfg':	$adminlog = new adminlog(); $adminlog->logme('delcfg', $reg['file_name'], "", "" );
						load_adminclass('config'); $conf = new config($reg['db']); $conf->remove($_REQUEST['conf_values'], $_REQUEST['id']); 
						mosRedirect( 'index2.php?ca='.$reg['ca'].'&task=cfg', "Настройки удалены" );
						break;
	case 'remove':		removeexgood_file( 0, $option );
						break;
	case 'edit':		editexgood_file( 0, $option );
						break;
        case 'newfile':
	case 'apply':
	case 'save':		save_file( $option );
						break;
	case 'saveorder':	saveOrderexgood_file( $cid );
						break;
	case 'orderup':		orderupexgood_file( $cid );
						break;
	case 'orderdown':	orderdownexgood_file( $cid );
						break;

        case 'filecat_edit':    filecat_edit() ; break;
        case 'filecat_cancel_edit':
        case 'filecat_apply':
        case 'filecat_save':    filecat_save() ; break;


	case 'cancel':		$type = ggrr('type');
                                switch (  $type  ){
                                    case 'exgood':
                                    case 'excat':
                                    case 'icat':    $task = 'editA'; break;
                                    default:        $task = 'edit'; break;
                                }

                                mosRedirect( site_url.'/iadmin/index2.php?ca='.$type.'&task='.$task.'&hidemainmenu=1&id='.ggri('parent'), "" );
						break;
	default:		showexgood_file( $option );
						break;
}

function cfg(){
	global $reg;
	?><form <? ctrlEnterCtrlAS (' '.$reg['submit_apply_event'], ' '.$reg['submit_save_event']) ?> name="adminForm" action="index2.php" method="post"><input type="hidden"  name="iuse" id="iuse" value="0" />


		<table class="adminheading"><tr><td width="100%"><?
			$iway[0]->name=$reg['file_name'];
			$iway[0]->url="";
			$iway[1]->name="настройка";
			$iway[1]->url="";

			i24pwprint_admin ($iway);
			?></td></tr></table>
			<!--<table border="0" cellpadding="4" cellspacing="0" width="100%" align="center">
				<tr class="workspace">
					<td></td>
					<td>- Если указано не верное количество изображений в категориях - также необходимо выполнить функцию <a class="bright" href="javascript: submitbutton('fotorecalc'); ">пересчитать</a>.</td>
				</tr>
			</table>-->

	<? load_adminclass('config');	$conf = new config($reg['db']);
	$conf->show_config('file', ""); ?>
	<input type="hidden" name="task" value="savecfg"  />
	<input type="hidden" name="ca" value="<?=$reg['ca'] ?>" />
	<input type="submit" style="display:none;" /></form><?
}

function orderupexgood_file( $cid ) {
	global $database;
	$exgoodfile_this = ggo($_REQUEST['cid'][0], '#__exgood_file');
	$exgoodfile_up = ggsql(" SELECT * FROM #__exgood_file WHERE #__exgood_file.order< ".$exgoodfile_this->order." ORDER BY #__exgood_file.order DESC LIMIT 0,1 ;");  $exgoodfile_up = $exgoodfile_up[0];
//	ggtr ($database); die();
	$i24r = new mosDBTable( "#__exgood_file", "id", $database );
	$i24r->id = $_REQUEST['cid'][0];
	$i24r->order = $exgoodfile_up->order;
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();

	$i24r = new mosDBTable( "#__exgood_file", "id", $database );
	$i24r->id = $exgoodfile_up->id;
	$i24r->order = $exgoodfile_this->order;
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	$msg = "Порядок изменен"; $exgoodid	= intval( getUserStateFromRequest(  'id', 0 ) );
	mosRedirect( 'index2.php?ca=exgood_file&task=view&id='.$exgoodid.'&limit='.$_REQUEST['limit'].'&limitstart='.$_REQUEST['limitstart'], $msg );
}
function orderdownexgood_file( $cid ) {
	global $database;
	$exgoodfile_this = ggo($_REQUEST['cid'][0], '#__exgood_file');
	$exgoodfile_up = ggsql(" SELECT * FROM #__exgood_file WHERE #__exgood_file.order> ".$exgoodfile_this->order." ORDER BY #__exgood_file.order ASC LIMIT 0,1 ;");  $exgoodfile_up = $exgoodfile_up[0];
//	ggtr ($database); die();
	$i24r = new mosDBTable( "#__exgood_file", "id", $database );
	$i24r->id = $_REQUEST['cid'][0];
	$i24r->order = $exgoodfile_up->order;
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();

	$i24r = new mosDBTable( "#__exgood_file", "id", $database );
	$i24r->id = $exgoodfile_up->id;
	$i24r->order = $exgoodfile_this->order;
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	$msg = "Порядок изменен"; $exgoodid	= intval( getUserStateFromRequest(  'id', 0 ) );
	mosRedirect( 'index2.php?ca=exgood_file&task=view&id='.$exgoodid.'&limit='.$_REQUEST['limit'].'&limitstart='.$_REQUEST['limitstart'], $msg );
}

function saveOrderexgood_file( &$cid ) {
	global $database;
	for ($exi = 0; $exi<count($_REQUEST['order']); $exi++){
		$i24r = new mosDBTable( "#__file", "id", $database );
		$i24r->id = $_REQUEST['exfileid'][$exi];
		$i24r->ordering = $_REQUEST['order'][$exi];
		if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	}
	$msg 	= 'Новый порядок сохранен';
	mosRedirect( 'index2.php?ca=file&task=view&parent='.ggri('parent').'&filecat='.ggri('filecat').'&type='.ggrr('type'), $msg );
} // saveOrder

function showexgood_file( $option ) {
	global $database, $my, $iConfig_list_limit, $reg;
	$exgoodid	= intval( getUserStateFromRequest(  'id', 0 ) );
	$component_file = new component_file();	
        $component_file->default_init();
        $component_file->init( ggrr('type') );
	$component_file->load_files();
	$myform = new insiteform();
        $names = new names(0, 'filename'.$component_file->type, $reg);

        $file_cats_array = ggsql("SELECT * FROM #__file_cat AS a WHERE a.parent=".ggri('parent')." AND a.type='".ggrr('type')."' ORDER BY a.order ; ");  //ggtr ($database);
        $file_cats =array(  mosHTML::makeOption( 0, 'Без привязки к подкатегории')  );
        foreach ($file_cats_array as $ivcat)    $file_cats[] = mosHTML::makeOption( $ivcat->id, $ivcat->name);

                ?><table cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td><?

                            ?><form action="index2.php" method="post" enctype="multipart/form-data" name="newfileForm">
                            <table class="adminheading" align="left" width="300" style="width:300px;">
                            <tr><td>Добавление нового файла</td></tr>
                            <tr class="workspace">
                                    <td align="right" style=" text-align: right; "><input type="file" name="newfile" size="85" /></td>
                                    <td rowspan="5"><input type="submit" style=" width: 134px; height: 150px; " value="Прикрепить" /></td>
                            </tr>
                            <tr class="workspace"><td><input <? $myform->make_java_text_effect('name', 'input_light'); ?> type="text" name="name" id="name" class="input_gray" style="width:340px;" value="Название файла (не обязательное)" title="Название файла (не обязательное)" /></td></tr>
                            <tr class="workspace"><td><textarea <? $myform->make_java_text_effect('desc', 'input_light'); ?> name="desc" id="desc" cols="70" class="input_gray" style="width:340px;" title="Описание файла (не обязательное)" rows="4">Описание файла (не обязательное)</textarea></td></tr>
                            <tr  class="workspace"><td><?php print mosHTML::selectList( $file_cats, 'filecat', 'class="gray_border gray w340" size="1" ', 'value', 'text', ggri('filecat') ); ?></td></tr>
                            <tr class="workspace">
                                <td nowrap="nowrap" style="white-space:nowrap;"><?
                                    print $names->field($uid, 150, "all_names", "_names_field", "input_gray w354");
                                ?><a href="javascript: ins_ajax_open('?ca=names_ajax&task=shownames&4ajax=1', 570, 570); void(0);" title="Показать все значения"><img border="0" src="/iadmin/images/properties01.png"  align="absmiddle"/></a></td>
                            </tr>
                            <tr><td><a href="javascript: ins_ajax_open('?ca=limit&task=foto_limit&4ajax=1', 0, 0); void(0);">Смотреть ограничения на загрузку файлов</a></td></tr>
                            <tr><td>&nbsp;</td></tr>

                            </table>
                            <input type="hidden" name="ca" value="<?php echo $reg['ca']; ?>" />
                            <input type="hidden" name="type" value="<?php echo $component_file->type; ?>" />
                            <input type="hidden" name="parent" value="<?php echo $component_file->parent; ?>" />
                            <input type="hidden" name="task" value="newfile" />
                            </form>
                        </td>
                        <td>&nbsp;&nbsp;&nbsp;</td>
                        <td valign="top" style="vertical-align: top; ">
                            <div id="show_all_list">&nbsp;</div>

                            <script language="javascript">
                                    ins_ajax_load_target ("ca=file_ajax&task=showfilecats&type=<?=ggrr('type') ?>&parent=<?=ggrr('parent') ?>&4ajax=1", "#show_all_list");
                            </script>

                        </td>

                    </tr>
                </table>
                <br /><?
		//$rows = ggsql("SELECT * FROM #__exgood_file AS a WHERE a.exgood_id=".$exgoodid." ORDER BY a.order ASC LIMIT $limitstart, $limit ; ");  //ggtr ($database);
                ?><form action="index2.php" method="get" name="catfileForm"><?
                ?><input type="hidden" name="ca" value="<?=ggrr('ca') ?>"><?
                ?><input type="hidden" name="type" value="<?=ggrr('type') ?>"><?
                ?><input type="hidden" name="parent" value="<?=ggrr('parent') ?>"><?
		?><table class="adminheading"><tr><td width="100%"><?
			$component_file->icatway = i24pathadd(  $component_file->icatway, $reg['file_name'], ""  );
			i24pwprint_admin ($component_file->icatway, 0);
                    ?></td>
                    <td align="right" >Выводить&nbsp;фото&nbsp;из&nbsp;подкатегории:&nbsp;</td>
                    <td align="right" ><?php print mosHTML::selectList( $file_cats, 'filecat', 'class="inputtop" onchange="document.catfileForm.submit();" size="1" ', 'value', 'text', ggri('filecat') ); ?></td>
                </tr></table>
                </form>
		<form action="index2.php" method="post" name="adminForm"><?
		// инициализация класса необходимого для перемящаемой таблицы
		$table_drug  = new ajax_table_drug ;
		$table_drug->id="ajax_table_drug_td";
		$table_drug->table="#__file";
		$table_drug->order="ordering";
		?><table class="adminlist" <?=$table_drug->table(); ?>  >
		<tr <?=$table_drug->row(); ?> >
			<th width="2%" class="title">#</th>
			<th width="3%" class="title"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?=$component_file->file_total; ?>);" /></th>
			<th class="title"></th>
                        <th class="title">Тип&nbsp;файла</th><?
			?><th align="center" width="5%">Сортировка</th><?
			?><th width="3%" ><a href="javascript: saveorder( <?php echo count( $rows )-1; ?> )" onMouseOver="return Tip('Сохранить заданный порядок отображения');">Сохранить&nbsp;порядок</a></th><?
			?><th class="title"></th>
		</tr>
		<?php
		$k = 0;
                
		for ($i=0; $i < $component_file->file_total; $i++) {
			$row 	= &$component_file->files[$i];
			$task 	= $row->publish==0 ? 'unblock' : 'block';
			$alt 	= $row->publish==0 ? '<span style="color:#ff0000;">Блокирован</span>' : 'Разрешен';
			$alt2 	= $row->publish==0 ? 'Снять блокировку' : 'Блокировать';
			$link 	= 'index2.php?ca=exgood_file&amp;task=editA&amp;id='. $row->id. '&amp;hidemainmenu=1&amp;search='. $_REQUEST['search'].'&amp;filter_type='. $_REQUEST['filter_type'].'&amp;filter_logged='. $_REQUEST['filter_logged'];
			?><tr <?=$table_drug->row($row->id, $row->ordering); ?> class="<?php echo "row$k"; ?>"><?
				?><td><?php echo $i+1+$pageNav->limitstart;?></td><?
				?><td><?php echo mosHTML::idBox( $i, $row->id ); ?></td><?
				?><td align="left"><? if (  $row->name  ){ ?><strong><? echo $row->name; ?></strong><br /><? }
					if (  $row->desc  ){  echo $row->desc; ?><br /><br /><? }
					?><strong>Файл:</strong> <? echo $row->filename; ?></td><?
				?><td><a title="нажмите чтобы скачать"  href="<?=$component_file->url_prefix ?><? print $row->filename; ?>" ><img src="/ibots/editors/tinymce/e24code/AjexFileManager/skin/dark/ext/<? print $row->fileext; ?>.png"  border="0"  /></a></td><?
				?><td align="center" class="dragHandle drugme" >&nbsp;</td><?
				?><td align="center"><input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center" /><input type="hidden" name="exfileid[]" value="<?php echo $row->id; ?>" /></td><?
				?><td><a href="index2.php?ca=file&type=<?=$component_file->type ?>&parent=<?=$component_file->parent ?>&filecat=<?=$component_file->filecat?>&task=remove&cid[]=<? print $row->id; ?>">Удалить</a><br /><br /><a href="index2.php?ca=file&type=<?=$component_file->type ?>&parent=<?=$component_file->parent ?>&filecat=<?=$component_file->filecat?>&task=edit&cid[]=<? print $row->id; ?>&fnum=<?php echo $i+1+$pageNav->limitstart; ?>">Изменить</a></td><?
			?></tr><?
			$k = 1 - $k;
		}
		?></table><?
		//require_once( site_path . '/iadmin/includes/pageNavigation.php' );
		//$pageNav = new mosPageNav( $total, $limitstart, $limit  );
		//echo $pageNav->getListFooter(); ?>
		<input type="hidden" name="ca" value="<?php echo $option; ?>" />
		<input type="hidden" name="type" value="<?=$component_file->type ?>" />
		<input type="hidden" name="parent" value="<?=$component_file->parent ?>" />
		<input type="hidden" name="filecat" value="<?=$component_file->filecat ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="1" />
		</form><?
}

function save_file ( $task ){
	global $reg;

	$component_file = new component_file();
        $component_file->default_init();
	$component_file->init( ggrr('type') );

        /*
         * СОХРАНЯЕМ ФАЙЛ
         */
        $p->id = ggri ('id');
        $p->filedest = $_FILES['newfile']['tmp_name'];
        $p->filename = $_FILES['newfile']['name'];
        $p->name = ggrr('name');
        $p->desc = ggrr('desc');
        $p->filecat = ggri('filecat');
        $p->type = ggrr ('type');
        $p->parent = ggri ('parent');
        $p->filecat = ggri ('filecat');
        $p->publish = ggri ('publish');
        if (  $p->name=='Название файла (не обязательное)'  ) $p->name='';
        if (  $p->desc=='Описание файла (не обязательное)'  ) $p->desc='';

        // удаление старого файла
        if (  $p->id>0  and  $_FILES['newfile']['tmp_name']  ){
            $file_obj = ggo( $p->id, "#__file" );
            delfile ( $component_file->dir.$file_obj->filename );
        }

        $component_file->save_new_file( $p );

        if (  $reg['task']=='apply'  or  $reg['task']=='save'  )    $fileAction = 'save_file';
        else $fileAction = 'new_file';
        $adminlog = new adminlog(); $adminlog->logme($fileAction, $component_file->parent_component_name, $component_file->parent_obj->name, $component_file->parent_obj->id );

	/*
	 * СОХРАНЯЕМ ИНДИВИДУАЛЬНЫЙ КОНФИГ
	 */
        if (  $p->id>0  ){
            load_adminclass('config');
            $conf = new config($reg['db']);
            $conf->prefix_id = "#__file_ID".$p->type.$p->id."__";
            $conf->save_config();
        }
        
        switch ( $reg['task'] ) {
            case 'newfile':
                    $msg = 'Новый файл сохранен: ';		mosRedirect( 'index2.php?ca=file&type='.$component_file->type.'&parent='.$component_file->parent.'&filecat='.$component_file->filecat, $msg );
                    break;
            case 'apply':
                    $msg = 'Файл №'.ggri('fnum').' сохранен';	mosRedirect( 'index2.php?ca=file&type='.$p->type.'&parent='.$p->parent.'&filecat='.$p->filecat.'&cid[]='.$p->id.'&task=edit&fnum='.ggri('fnum'), $msg );
                    break;
            case 'save':
                    $msg = 'Файл №'.ggri('fnum').' сохранен';	mosRedirect( 'index2.php?ca=file&type='.$p->type.'&parent='.$p->parent.'&filecat='.$p->filecat, $msg );
                    break;
        }
}

function removeexgood_file( $task ) {
	global $database, $reg;
	$component_file = new component_file();
        $component_file->default_init();
	$component_file->init( ggrr('type') );

	foreach ($_REQUEST['cid'] as $dfgd){
		$exgoodfile = ggo ($dfgd, "#__file");  	delfile (  $component_file->dir.$exgoodfile->filename  );

		$adminlog = new adminlog(); $adminlog->logme('del_file', $component_file->parent_component_name, $component_file->parent_obj->name, $component_file->parent_obj->id );
		ggsqlq ("DELETE FROM #__file WHERE id=".$dfgd);

                $names = new names($dfgd, 'filename'.$exgoodfile->type, $reg);
                $names->delete();
	}
	$msg = 'Файл(и) удалены:';
	mosRedirect( 'index2.php?ca=file&type='.$component_file->type.'&parent='.$component_file->parent.'&filecat='.$component_file->filecat, $msg );
}
function editexgood_file( $task ) {
	global $database, $reg;

	$component_file = new component_file();
        $component_file->default_init();
	$component_file->init( ggrr('type') );

	$names = new names($_REQUEST['cid'][0], 'filename'.$component_file->type, $reg);
	$ithisfile = ggo ($_REQUEST['cid'][0], "#__file");

       	$component_foto = new component_foto ( 0 );
	$component_foto->init($reg['ca']);
	$component_foto->parent = $ithisfile->id;

        $file_publish = array(  mosHTML::makeOption( 1, 'Да'),  mosHTML::makeOption( 0, 'Нет')   );

        $foto_cats_array = ggsql("SELECT * FROM #__file_cat AS a WHERE a.parent=".ggri('parent')." AND a.type='".ggrr('type')."' ORDER BY a.order ; ");  //ggtr ($database);
        $foto_cats =array(  mosHTML::makeOption( 0, 'Без привязки к подкатегории')  );
        foreach ($foto_cats_array as $ivcat)    $foto_cats[] = mosHTML::makeOption( $ivcat->id, $ivcat->name);

	?><form <? ctrlEnterCtrlAS (' '.$reg['submit_apply_event'], ' '.$reg['submit_save_event']) ?> name="adminForm" enctype="multipart/form-data" action="index2.php" method="post">
	<table class="adminheading"><tr><td class="edit"><?
			$component_file->icatway = i24pathadd(  $component_file->icatway, $reg['file_name'], ""  );
			$component_file->icatway = i24pathadd(  $component_file->icatway, "Редактирование файла ".ggri('fnum'), ""  );
			i24pwprint_admin ($component_file->icatway, 0);	
	?></td></tr></table>
	<table border="0" cellpadding="4" cellspacing="0" width="100%" align="center">
		<tr class="workspace">
			<td>Название: </td>
			<td><input name="name" style="width:570px;" value="<? print $ithisfile->name; ?>"  /></td>
		</tr>
		<tr class="workspace">
                        <td><?=$reg['names_name']?>: </td>
                        <td colspan="2" nowrap="nowrap" style="white-space:nowrap;"><a href="javascript: ins_ajax_open('?ca=names_ajax&task=shownames&4ajax=1', 570, 570); void(0);" title="Показать все значения"><img border="0" src="/iadmin/images/properties01.png"  align="absmiddle"/></a><?
                                 print $names->field($uid, 150, "all_names", "_names_field", "w554");
                        ?></td>
                </tr>
		<tr class="workspace">
			<td>Описание: </td>
			<td><textarea cols="85" rows="8" style="width:570px;" name="desc"><? print $ithisfile->desc; ?></textarea></td>
		</tr>
                <tr  class="workspace">
                        <td>Подкатегория: </td>
                        <td><?php print mosHTML::selectList( $foto_cats, 'filecat', 'size="1" style="width: 574px; " ', 'value', 'text', $ithisfile->filecat ); ?></td>
                </tr>
                <tr class="workspace">
			<td>Публиковать на сайте</td>
			<td><?php print mosHTML::selectList( $file_publish, 'publish', ' size="1" ', 'value', 'text', $ithisfile->publish ); ?></td>
		</tr>
                <? itable_hr(2) ?>
                <tr class="workspace">
                        <td>Прикрепленные фото: <br /><br /><? $component_foto->make_galery_link() ?></td>
                        <td><?
                                $exfotos = $component_foto->get_fotos();
                                foreach ($exfotos as $exfoto){
                                        ?><a title="нажмите чтобы увеличить" onclick="return hs.expand(this)" class="highslide" href="<? print site_url; ?>/images/files/fotos/<? print $exfoto->org; ?>" ><img src="/images/files/fotos/<? print $exfoto->small; ?>"  border="2" style="border-color:#888888" align="absmiddle"  vspace="1" /></a> <?
                                }
                        ?></td>
                </tr>

		<tr class="workspace">
			<td>Файл:</td>
			<td><a title="нажмите чтобы скачать" href="<? print $component_file->url_prefix; ?><? print $ithisfile->filename; ?>" ><img src="/ibots/editors/tinymce/e24code/AjexFileManager/skin/dark/ext/<? print $ithisfile->fileext; ?>.png"  border="0"  align="absmiddle"  vspace="1" /></a></td>
		</tr>
		<tr class="workspace">
			<td>Закачать новый</td>
			<td><input type="file" size="85" name="newfile"/></td>
		</tr>

	</table>
<?
	/*
	 * ВОД ИНДИВИУАЛЬНЫХ НАСТРОЕК ДЛЯ ОБЪЕКТА
	 * например индивидуальные параметры для фотографий
	 */
	load_adminclass('config');	$conf = new config($reg['db']);
	$conf->prefix_id = "#__file_ID".$component_file->type.$ithisfile->id."__";
	$conf->returnme('index2.php?ca='.$reg['ca'].'&type='.$component_file->type.'&parent='.ggri('parent').'&filecat='.ggri('filecat').'&task='.ggrr('task').'&cid[]='.$ithisfile->id.'&fnum='.ggri('fnum') );
	$conf->show_config($conf->prefix_id, "addition_ajax");	//Дополнительные настройки
?>
	<input type="hidden" name="id" value="<? print $_REQUEST['cid'][0]; ?>" />
	<input type="hidden" name="parent" value="<? print $_REQUEST['parent']; ?>" />
	<input type="hidden" name="type" value="<? print $_REQUEST['type']; ?>" />
	<input type="hidden" name="task" value="apply"  />
	<input type="hidden" name="fnum" value="<?=ggri('fnum') ?>"  />
	<input type="hidden" name="ca" value="file" /></form><?
}

/**
 * Добавление новой категории/редактирование существующей
 * @global <type> $reg
 */
function filecat_edit(){
	global $reg;
        if (  ggri('id')  ) $foto_cat = ggo ( ggri('id'), "#__file_cat" );
        else{
            $max_order = ggsql (  "select * from #__file_cat WHERE type='".ggrr('type')."'  AND  parent='".ggrr('parent')."' ORDER BY #__file_cat.order DESC  LIMIT 0,1  " );
            $foto_cat->name=""; $foto_cat->type=ggrr('type'); $foto_cat->type=ggrr('type'); $foto_cat->parent=ggrr('parent'); $foto_cat->order=$max_order[0]->order+1; $foto_cat->publish=1;
        }

        $foto_cat_publish = array(  mosHTML::makeOption( 1, 'Да'),  mosHTML::makeOption( 0, 'Нет')   );

		?><script language="javascript" type="text/javascript">
		<!--
		var folderimages = new Array;

		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			if (form.name.value == ""){
				alert( "Этот объект должен иметь заголовок" );
			} else {
				<?php // getEditorContents( 'editor1', 'introtext' ) ; ?>
				<?php // getEditorContents( 'editor2', 'fulltext' ) ; ?>
				submitform( pressbutton );
			}
		}
		//-->
		</script><?
	?><form <? ctrlEnterCtrlAS (" submitbutton('filecat_apply');", " submitbutton('filecat_save');") ?> name="adminForm" method="post" id="adminForm" >
        <table class="adminheading"><tr><td width="100%"><?
	$iway[0]->name="Редактирование подкатегории";
	$iway[0]->url="";
	i24pwprint_admin ($iway);
        ?></td></tr></table>

        <table class="adminheading" cellspacing="0" cellpadding="4" >
		<tr class="workspace"><td colspan="2">&nbsp;</td></tr>
                <tr class="workspace"><td colspan="2">Информация о предназначении подкатегорий</td></tr>
                <? itable_hr(2) ?>
		<tr class="workspace">
			<td>Название: </td>
			<td><input type="text" value="<?=$foto_cat->name ?>" style="width:270px; " name="name" id="name"  /></td>
		</tr>
		<tr class="workspace">
			<td>Описание: </td>
                        <td><textarea type="text" style="width:270px; " name="desc" rows="7" ><?=desafelySqlStr($foto_cat->desc); ?></textarea></td>
		</tr>
                <tr class="workspace">
			<td>Порядок: </td>
			<td><input type="text" value="<?=$foto_cat->order ?>" style="width:270px; " id="expack_connect" name="order" /></td>
		</tr>
		<tr class="workspace">
			<td>Публиковать на сайте</td>
			<td><?php print mosHTML::selectList( $foto_cat_publish, 'publish', ' size="1" ', 'value', 'text', $foto_cat->publish ); ?></td>
		</tr>
                <tr class="workspace"><td colspan="2">&nbsp;</td></tr>

                <tr style="display: none;">
			<td></td><td><input type="submit" value="Сохранить" class="button" /></td>
		</tr>
	</table>


<?
	/*
	 * ВОД ИНДИВИУАЛЬНЫХ НАСТРОЕК ДЛЯ ОБЪЕКТА
	 * например индивидуальные параметры для фотографий
	 */
	load_adminclass('config');	$conf = new config($reg['db']);
	$conf->prefix_id = '#__file_cat'."_ID".$foto_cat->id."__";
	$conf->returnme('index2.php?ca='.$reg['ca'].'&task='.ggrr('filecat_edit').'&id='.ggri('id').'&type='.ggrr('type').'&parent='.ggrr('parent').'&filecat='.ggri('filecat') );
	$conf->show_config($conf->prefix_id, "addition_ajax");	//Дополнительные настройки
?>


	<input type="hidden" name="ca" value="<?php echo $reg['ca']; ?>" />
	<input type='hidden' name='task' value='fotocat_save' />
	<input type='hidden' name='parent' value='<?=$foto_cat->parent ?>' />
	<input type='hidden' name='type' value='<?=$foto_cat->type ?>' />
        <input type='hidden' name='filecat' value='<?=ggri('filecat') ?>' />
	</form>
	<?
}

/**
 * СОХРАНЯЕМ ФОТО ПОДКАТЕГОРИЮ
 * @global  $reg
 */
function filecat_save (  ){
    global $reg;

    if (  ggrr('task')=='filecat_cancel_edit'  ){
        $msg = 'Изменения подкатегории '. ggrr('name').' отменены'; mosRedirect( 'index2.php?ca='.ggrr('ca').'&type='.ggrr('type').'&parent='.ggrr('parent').'&filecat='.ggrr('filecat'), $msg );
    }
    /*
     * Сохраняем подкатегорию для фото
     */
    $i24r = new mosDBTable( "#__file_cat", "id", $reg['db'] );
    $i24r->id = ggri('id');
    $i24r->order = ggri('order');
    $i24r->name = ggrr('name');
    $i24r->desc = ggrr('desc');
    $i24r->type = ggrr('type');
    $i24r->parent = ggrr('parent');
    $i24r->publish = ggrr('publish');
    if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();

    if (  $i24r->_db->_errorNum!=0  ) ggdd(); // выполнено не без ошибок

    $adminlog = new adminlog();
    if (  ggri('id')==0  )	$adminlog->logme('new_file_subcat', 'тип '.$i24r->type, $i24r->name, $i24r->id ); else $adminlog->logme('save_file_subcat', 'тип '.$i24r->type, $i24r->name, $i24r->id );

    /*
     * СОХРАНЯЕМ ИНДИВИДУАЛЬНЫЙ КОНФИГ
     */
    load_adminclass('config');
    $conf = new config($reg['db']);
    $conf->prefix_id = '#__foto_cat'."_ID".$i24r->id."__";
    $conf->save_config();
    

    switch ( ggrr('task') ) {
            case 'filecat_apply':
                    $msg = 'Подкатегория сохранена: '. $i24r->name; mosRedirect( 'index2.php?ca='.ggrr('ca').'&task=filecat_edit&type='.ggrr('type').'&parent='.ggrr('parent').'&filecat='.ggrr('filecat').'&id='.$i24r->id, $msg );
            case 'filecat_save':
            default:
                    $msg = 'Подкатегория сохранена: '. $i24r->name; mosRedirect( 'index2.php?ca='.ggrr('ca').                  '&type='.ggrr('type').'&parent='.ggrr('parent').'&filecat='.ggrr('filecat'), $msg );
                    break;
    }



}

?>