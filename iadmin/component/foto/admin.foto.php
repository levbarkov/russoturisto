<?php
// ggd ($_REQUEST);
// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my, $task, $id;
//$task 			= strval( mosGetParam( $_REQUEST, 'task', '' ) );
// ggtr ($_REQUEST); ggtr ($task);  die();
$cid = josGetArrayInts( 'cid' );




switch ($task) {
	case 'cancel_edit':	$contentid	= intval( getUserStateFromRequest(  'id', 0 ) );
						$_REQUEST['id'] = $contentid;
						$task = $_REQUEST['task'] = 'view';
						showexgood_foto( $option );
						break;
	case 'newfoto': 	fotoexcat_foto_crop( 0, $option );
						break;
	case 'remove':		removeexgood_foto( 0, $option );
						break;
	case 'edit':		editexgood_foto( 0, $option );
						break;
	case 'apply':
	case 'save':		fotoexcat_foto_crop( $cid, $option );
						break;
	case 'newfoto_store':
	case 'apply_store':
	case 'save_store':	savefotoexgood_foto( $task, $option );
						break;
	case 'saveorder':	saveOrderexgood_foto( $cid );
						break;
	case 'orderup':		orderupexgood_foto( $cid );
						break;
	case 'orderdown':	orderdownexgood_foto( $cid );
						break;

        case 'fotocat_edit':    fotocat_edit() ; break;
        case 'fotocat_cancel_edit':    
        case 'fotocat_apply':    
        case 'fotocat_save':    fotocat_save() ; break;
        case 'save_cfg':        save_cfg() ; break;

	case 'cancel':		$type = ggrr('type');
                                $component_foto = new component_foto();
                                $component_foto->default_init();
                                $component_foto->init( ggrr('type') );
                                //$component_foto->load_fotos( );
                                switch (  $type  ){
                                    case 'file': mosRedirect( site_url.'/iadmin/index2.php?ca=file&type='.$component_foto->parent_obj->type.'&parent='.$component_foto->parent_obj->parent.'&filecat=0&task=edit&cid[]='.$component_foto->parent_obj->id.'&fnum=', "" ); return;
                                    case 'exgood':
                                    case 'excat':
                                    case 'icat':    $task = 'editA'; break;
                                    default:        $task = 'edit'; break;
                                }
                                mosRedirect( site_url.'/iadmin/index2.php?ca='.$type.'&task='.$task.'&hidemainmenu=1&id='.ggri('parent'), "" );
						break;
	default:		showexgood_foto( $option );
                                                break;
}
function orderupexgood_foto( $cid ) {
	global $database;
	$exgoodfoto_this = ggo($_REQUEST['cid'][0], '#__exgood_foto');
	$exgoodfoto_up = ggsql(" SELECT * FROM #__exgood_foto WHERE #__exgood_foto.order< ".$exgoodfoto_this->order." ORDER BY #__exgood_foto.order DESC LIMIT 0,1 ;");  $exgoodfoto_up = $exgoodfoto_up[0];
//	ggtr ($database); die();
	$i24r = new mosDBTable( "#__exgood_foto", "id", $database );
	$i24r->id = $_REQUEST['cid'][0];
	$i24r->order = $exgoodfoto_up->order;
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();

	$i24r = new mosDBTable( "#__exgood_foto", "id", $database );
	$i24r->id = $exgoodfoto_up->id;
	$i24r->order = $exgoodfoto_this->order;
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	$msg = "Порядок изменен"; $exgoodid	= intval( getUserStateFromRequest(  'id', 0 ) );
	mosRedirect( 'index2.php?ca=exgood_foto&task=view&id='.$exgoodid.'&limit='.$_REQUEST['limit'].'&limitstart='.$_REQUEST['limitstart'], $msg );
}
function orderdownexgood_foto( $cid ) {
	global $database;
	$exgoodfoto_this = ggo($_REQUEST['cid'][0], '#__exgood_foto');
	$exgoodfoto_up = ggsql(" SELECT * FROM #__exgood_foto WHERE #__exgood_foto.order> ".$exgoodfoto_this->order." ORDER BY #__exgood_foto.order ASC LIMIT 0,1 ;");  $exgoodfoto_up = $exgoodfoto_up[0];
//	ggtr ($database); die();
	$i24r = new mosDBTable( "#__exgood_foto", "id", $database );
	$i24r->id = $_REQUEST['cid'][0];
	$i24r->order = $exgoodfoto_up->order;
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();

	$i24r = new mosDBTable( "#__exgood_foto", "id", $database );
	$i24r->id = $exgoodfoto_up->id;
	$i24r->order = $exgoodfoto_this->order;
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	$msg = "Порядок изменен"; $exgoodid	= intval( getUserStateFromRequest(  'id', 0 ) );
	mosRedirect( 'index2.php?ca=exgood_foto&task=view&id='.$exgoodid.'&limit='.$_REQUEST['limit'].'&limitstart='.$_REQUEST['limitstart'], $msg );
}

function saveOrderexgood_foto( &$cid ) {
	global $database;
	// ggd ($_REQUEST); //die();
	for ($exi = 0; $exi<count($_REQUEST['order']); $exi++){
		$i24r = new mosDBTable( "#__foto", "id", $database );
		$i24r->id = $_REQUEST['exfotoid'][$exi];
		$i24r->ordering = $_REQUEST['order'][$exi];
		if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	}
	$msg 	= 'Новый порядок сохранен';
	mosRedirect( 'index2.php?ca=foto&type='.ggrr('type').'&parent='.ggri('parent').'&fotocat='.ggri('fotocat'), $msg );
} // saveOrder

function showexgood_foto( $option ) {
	global $database, $my, $iConfig_list_limit, $reg;
	$exgoodid	= intval( getUserStateFromRequest(  'id', 0 ) );
	$limit 			= intval( getUserStateFromRequest( 'limit', $iConfig_list_limit ) );
	$limitstart 	= intval( getUserStateFromRequest( 'limitstart', 0 ) );
	$component_foto = new component_foto();	
        $component_foto->default_init();
        $component_foto->init( ggrr('type') );
	$component_foto->load_fotos( );
        $names = new names(0, 'fotoname'.$component_foto->type, $reg);
	$myform = new insiteform();
        $foto_cats_array = ggsql("SELECT * FROM #__foto_cat AS a WHERE a.parent=".ggri('parent')." AND a.type='".ggrr('type')."' ORDER BY a.order ; ");  //ggtr ($database);
        $foto_cats =array(  mosHTML::makeOption( 0, 'Без привязки к подкатегории')  );
        foreach ($foto_cats_array as $ivcat)    $foto_cats[] = mosHTML::makeOption( $ivcat->id, $ivcat->name);
        
        ?><table cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td><?
                    ?><form action="index2.php" method="post" enctype="multipart/form-data" name="newfotoForm">
                    <table class="adminheading" align="left" width="300" style="width:300px;">
                    <tr><td>Добавление новой фотографии</td></tr>
                    <tr  class="workspace">
                            <td align="right" style=" text-align: right; "><input type="file" name="newfoto" size="85" /></td>
                            <td rowspan="6"><input type="submit" style=" width: 134px; height: 170px; " value="Добавить фото" /></td>
                    </tr>
                    <tr  class="workspace"><td><input <? $myform->make_java_text_effect('furl', 'input_light'); ?> type="text" name="furl" id="furl" class="input_gray" style="width:340px;" value="URL фото (не обязательное)" title="URL фото (не обязательное)" /></td></tr>
                    <tr  class="workspace"><td><input <? $myform->make_java_text_effect('name', 'input_light'); ?> type="text" name="name" id="name" class="input_gray" style="width:340px;" value="Название фото (не обязательное)" title="Название фото (не обязательное)" /></td></tr>
                    <tr class="workspace"><td><textarea <? $myform->make_java_text_effect('desc', 'input_light'); ?> name="desc" id="desc" cols="70" class="input_gray" style="width:340px;" title="Описание фото (не обязательное)" rows="4">Описание фото (не обязательное)</textarea></td></tr>
                    <tr  class="workspace"><td><?php print mosHTML::selectList( $foto_cats, 'fotocat', 'class="gray_border gray w340" size="1" ', 'value', 'text', ggri('fotocat') ); ?></td></tr>
                    <tr class="workspace">
                        <td nowrap="nowrap" style="white-space:nowrap;"><?
                            print $names->field($uid, 150, "all_names", "_names_field", "input_gray w354");
                        ?><a href="javascript: ins_ajax_open('?ca=names_ajax&task=shownames&4ajax=1', 570, 570); void(0);" title="Показать все значения"><img border="0" src="/iadmin/images/properties01.png"  align="absmiddle"/></a></td>
                    </tr>
                    <tr><td><a href="javascript: ins_ajax_open('?ca=limit&task=foto_limit&4ajax=1', 0, 0); void(0);">Смотреть ограничения на загрузку фото</a></td></tr>
                    <tr><td>&nbsp;</td></tr>

                    </table>
                    <input type="hidden" name="ca" value="<?php echo $reg['ca']; ?>" />
                    <input type="hidden" name="type" value="<?php echo $component_foto->type; ?>" />
                    <input type="hidden" name="parent" value="<?php echo $component_foto->parent; ?>" />
                    <input type="hidden" name="task" value="newfoto" />
                    </form>
                </td>
                <td>&nbsp;&nbsp;&nbsp;</td>
                <td valign="top" style="vertical-align: top; ">
                    <div id="show_all_list">&nbsp;</div>

                    <script language="javascript">
                            ins_ajax_load_target ("ca=foto_ajax&task=showfotocats&type=<?=ggrr('type') ?>&parent=<?=ggrr('parent') ?>&4ajax=1", "#show_all_list");
                    </script>

                </td>
                    
            </tr>
        </table>
                <br /><?
		//$rows = ggsql("SELECT * FROM #__exgood_foto AS a WHERE a.exgood_id=".$exgoodid." ORDER BY a.order ASC LIMIT $limitstart, $limit ; ");  //ggtr ($database);
                ?><form action="index2.php" method="get" name="catfotoForm"><?
                ?><input type="hidden" name="ca" value="<?=ggrr('ca') ?>"><?
                ?><input type="hidden" name="type" value="<?=ggrr('type') ?>"><?
                ?><input type="hidden" name="parent" value="<?=ggrr('parent') ?>"><?
		?><table class="adminheading"><tr><td width="100%"><?
			$component_foto->icatway = i24pathadd(  $component_foto->icatway, "Фотогалерея", ""  );
			i24pwprint_admin ($component_foto->icatway, 0);
                    ?></td>
                    <td align="right" >Выводить&nbsp;фото&nbsp;из&nbsp;подкатегории:&nbsp;</td>
                    <td align="right" ><?php print mosHTML::selectList( $foto_cats, 'fotocat', 'class="inputtop" onchange="document.catfotoForm.submit();" size="1" ', 'value', 'text', ggri('fotocat') ); ?></td>
                </tr></table>
                </form>
		<form <? ctrlEnterCtrlAS (" submitbutton('save_cfg');", " submitbutton('save_cfg');") ?> action="index2.php" method="post" name="adminForm"><?
		// инициализация класса необходимого для перемящаемой таблицы
		$table_drug  = new ajax_table_drug ;
		$table_drug->id="ajax_table_drug_td";
		$table_drug->table="#__foto";
		$table_drug->order="ordering";
		?><table class="adminlist" <?=$table_drug->table(); ?>  >
		<tr <?=$table_drug->row(); ?> >
			<th width="2%" class="title">#</th>
			<th width="3%" class="title"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?=$component_foto->foto_total; ?>);" /></th>
			<th class="title"></th>
			<th class="title">Фото</th><?
			?><th align="center" width="5%">Сортировка</th><?
			?><th width="3%" ><a href="javascript: saveorder( <?php echo count( $rows )-1; ?> )" onMouseOver="return Tip('Сохранить заданный порядок отображения');">Сохранить&nbsp;порядок</a></th><?
			?><th class="title"></th>
		</tr>
		<?php
		$k = 0;
		for ($i=0; $i < $component_foto->foto_total; $i++) {
			$row 	= &$component_foto->fotos[$i];
			$task 	= $row->publish==0 ? 'unblock' : 'block';
			$alt 	= $row->publish==0 ? '<span style="color:#ff0000;">Блокирован</span>' : 'Разрешен';
			$alt2 	= $row->publish==0 ? 'Снять блокировку' : 'Блокировать';
			$link 	= 'index2.php?ca=exgood_foto&amp;task=editA&amp;id='. $row->id. '&amp;hidemainmenu=1&amp;search='. $_REQUEST['search'].'&amp;filter_type='. $_REQUEST['filter_type'].'&amp;filter_logged='. $_REQUEST['filter_logged'];
			?><tr <?=$table_drug->row($row->id, $row->ordering); ?> class="<?php echo "row$k"; ?>"><?
				?><td><?php echo $i+1+$pageNav->limitstart;?></td><?
				?><td><?php echo mosHTML::idBox( $i, $row->id ); ?></td><?
				?><td align="left"><? if (  $row->name  ){ ?><strong><? echo $row->name; ?></strong><br /><? }
					if (  $row->desc  ){  echo $row->desc; ?><br /><br /><? }
					?><strong>Файл:</strong><br />оригинальное : <? echo $row->org; ?><br /><?
					?>уменьшенное: <? echo $row->small; ?></td><?
				?><td><a title="нажмите чтобы увеличить" onclick="return hs.expand(this)" class="highslide" href="<?=$component_foto->url_prefix ?><? print $row->org; ?>" ><img src="<?=$component_foto->url_prefix ?><? print $row->small; ?>"  border="5" style="border-color:#cccccc" /></a></td><?
				?><td align="center" class="dragHandle drugme" >&nbsp;</td><?
				?><td align="center"><input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center" /><input type="hidden" name="exfotoid[]" value="<?php echo $row->id; ?>" /></td><?
				?><td><a href="index2.php?ca=foto&type=<?=$component_foto->type ?>&parent=<?=$component_foto->parent ?>&fotocat=<?=$component_foto->fotocat?>&task=remove&cid[]=<? print $row->id; ?>">Удалить</a><br /><br /><a href="index2.php?ca=foto&type=<?=$component_foto->type ?>&parent=<?=$component_foto->parent ?>&fotocat=<?=$component_foto->fotocat?>&task=edit&cid[]=<? print $row->id; ?>&fnum=<?php echo $i+1+$pageNav->limitstart; ?>">Изменить</a></td><?
			?></tr><?
			$k = 1 - $k;
		}
		?></table><?
                /*
                 * ВОД ИНДИВИУАЛЬНЫХ НАСТРОЕК ДЛЯ ОБЪЕКТА
                 * например индивидуальные параметры для фотографий
                 */
                load_adminclass('config');	$conf = new config($reg['db']);
                $conf->prefix_id = '#__foto_cat_'.ggrr('type').'_'.ggrr('parent').'_ID0__';
                $conf->returnme('index2.php?ca='.$reg['ca'].'&task='.ggrr('fotocat_edit').'&id='.ggri('id').'&type='.ggrr('type').'&parent='.ggrr('parent').'&fotocat='.ggri('fotocat') );
                $conf->show_config($conf->prefix_id, "addition_ajax");	//Дополнительные настройки

                ?>
		<input type="hidden" name="ca" value="<?php echo $option; ?>" />
		<input type="hidden" name="type" value="<?=$component_foto->type ?>" />
		<input type="hidden" name="parent" value="<?=$component_foto->parent ?>" />
		<input type="hidden" name="fotocat" value="<?=$component_foto->fotocat ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="1" />
		</form><?
}



/**
 * Добавление новой категории/редактирование существующей
 * @global <type> $reg
 */
function fotocat_edit(){
	global $reg;
        if (  ggri('id')  ) $foto_cat = ggo ( ggri('id'), "#__foto_cat" );
        else{
            $max_order = ggsql (  "select * from #__foto_cat WHERE type='".ggrr('type')."'  AND  parent='".ggrr('parent')."' ORDER BY #__foto_cat.order DESC  LIMIT 0,1  " );
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
	?><form <? ctrlEnterCtrlAS (" submitbutton('fotocat_apply');", " submitbutton('fotocat_save');") ?> name="adminForm" method="post" id="adminForm" >
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
	$conf->prefix_id = '#__foto_cat_'.ggrr('type').'_'.ggrr('parent').'_ID'.$foto_cat->id."__";
	$conf->returnme('index2.php?ca='.$reg['ca'].'&task='.ggrr('fotocat_edit').'&id='.ggri('id').'&type='.ggrr('type').'&parent='.ggrr('parent').'&fotocat='.ggri('fotocat') );
	$conf->show_config($conf->prefix_id, "addition_ajax");	//Дополнительные настройки
?>


	<input type="hidden" name="ca" value="<?php echo $reg['ca']; ?>" />
	<input type='hidden' name='task' value='fotocat_save' />
	<input type='hidden' name='parent' value='<?=$foto_cat->parent ?>' />
	<input type='hidden' name='type' value='<?=$foto_cat->type ?>' />
        <input type='hidden' name='fotocat' value='<?=ggri('fotocat') ?>' />
	</form>
	<?
}

/**
 * Сохраняем дополнительные параметры для родительской подкатегории (ID=0)
 */
function save_cfg(){
    /*
     * СОХРАНЯЕМ ИНДИВИДУАЛЬНЫЙ КОНФИГ
     */
    load_adminclass('config');
    $conf = new config($reg['db']);
    $conf->prefix_id = '#__foto_cat_'.ggrr('type').'_'.ggrr('parent').'_ID0__';
    $conf->save_config();
    
    $msg = 'Дополнительные настройки сохранены: ';
    mosRedirect( 'index2.php?ca='.ggrr('ca').'&type='.ggrr('type').'&parent='.ggrr('parent').'&fotocat='.ggrr('fotocat'), $msg );
}

/**
 * СОХРАНЯЕМ ФОТО ПОДКАТЕГОРИЮ
 * @global  $reg
 */
function fotocat_save (  ){
    global $reg;

    if (  ggrr('task')=='fotocat_cancel_edit'  ){
        $msg = 'Изменения подкатегории '. ggrr('name').' отменены'; mosRedirect( 'index2.php?ca='.ggrr('ca').'&type='.ggrr('type').'&parent='.ggrr('parent').'&fotocat='.ggrr('fotocat'), $msg );
    }
    /*
     * Сохраняем подкатегорию для фото
     */
    $i24r = new mosDBTable( "#__foto_cat", "id", $reg['db'] );
    $i24r->id = ggri('id');
    $i24r->order = ggri('order');
    $i24r->name = ggrr('name');
    $i24r->link = ggrr('link');
    $i24r->desc = ggrr('desc');
    $i24r->type = ggrr('type');
    $i24r->parent = ggrr('parent');
    $i24r->publish = ggrr('publish');
    if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();

    if (  $i24r->_db->_errorNum!=0  ) ggdd(); // выполнено не без ошибок

    $adminlog = new adminlog();
    if (  ggri('id')==0  )	$adminlog->logme('new_foto_subcat', 'тип '.$i24r->type, $i24r->name, $i24r->id ); else $adminlog->logme('save_foto_subcat', 'тип '.$i24r->type, $i24r->name, $i24r->id );

    /*
     * СОХРАНЯЕМ ИНДИВИДУАЛЬНЫЙ КОНФИГ
     */
    load_adminclass('config');
    $conf = new config($reg['db']);
    $conf->prefix_id = '#__foto_cat_'.$i24r->type.'_'.$i24r->parent.'_ID'.$i24r->id."__";
    $conf->save_config();

    switch ( ggrr('task') ) {
            case 'fotocat_apply':
                    $msg = 'Подкатегория сохранена: '. $i24r->name; mosRedirect( 'index2.php?ca='.ggrr('ca').'&task=fotocat_edit&type='.ggrr('type').'&parent='.ggrr('parent').'&fotocat='.ggrr('fotocat').'&id='.$i24r->id, $msg );
            case 'fotocat_save':
            default:
                    $msg = 'Подкатегория сохранена: '. $i24r->name; mosRedirect( 'index2.php?ca='.ggrr('ca').                  '&type='.ggrr('type').'&parent='.ggrr('parent').'&fotocat='.ggrr('fotocat'), $msg );
                    break;
    }

    

}

function fotoexcat_foto_crop ( $cid, $task ){
	global $reg;

        $component_foto = new component_foto();
        $component_foto->default_init();
	$component_foto->init( ggrr('type') );
        
        if (  $_REQUEST['furl']=='URL фото (не обязательное)'  ) $_REQUEST['furl'] = '';
        if (  $_REQUEST['furl']!=''  ){ // КОПИ ФАЙЛ ПО URL АДРЕСУ И ЭМУЛИРУЕМ ОБЫЧНУЮ ЗАГРУЗКУ ДЛЯ СОВМЕСТИМОСТИ КОДА
                $_FILES['newfoto']['name'] = component_file::makeUniqName($_REQUEST['furl']);
                $_FILES['newfoto']['tmp_name'] = $component_foto->dir.'tmp/'.$_FILES['newfoto']['name'];
                // копируем файл по ссылке
                $ch = curl_init(    desafelySqlStr($_REQUEST['furl'])    );
                $fp = fopen($_FILES['newfoto']['tmp_name'], 'wb');
                curl_setopt($ch, CURLOPT_FILE, $fp);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_exec($ch);
                curl_close($ch);
                fclose($fp);
        }

        $component_foto->loadImageSizes();


	if (  $_FILES["newfoto"]['tmp_name']  ||  $_REQUEST['furl']!=''  ){
		if (  $component_foto->small_use  ){
			$cropper[0] = new foto_crop();
			$cropper[0]->get_sizes (1, $component_foto->small_w, $component_foto->small_h, $_FILES['newfoto']['tmp_name']);
			$cropper[0]->photoid = "photo_small";
			$cropper[0]->previewid = "preview_small";
			$cropper[0]->type =    $component_foto->small_type;
			$cropper[0]->quality = $component_foto->small_quality;
			$cropper[0]->effect =  $component_foto->small_effect;
			$cropper[0]->select =  $component_foto->small_select;
			$cropper[0]->title = "Превью (маленькое изображение:)";
			$cropper[0]->file_crop_url = $component_foto->url_prefix.trans2eng($_FILES['newfoto']['name']);
		}
		if (  $component_foto->mid_use  ){
			$cropper[1] = new foto_crop();	
			$cropper[1]->get_sizes (1, 	$component_foto->mid_w, 	$component_foto->mid_h, $_FILES['newfoto']['tmp_name']);
			$cropper[1]->photoid = "photo_mid";
			$cropper[1]->previewid = "preview_mid";
			$cropper[1]->type =    $component_foto->mid_type;
			$cropper[1]->quality = $component_foto->mid_quality;
			$cropper[1]->effect =  $component_foto->mid_effect;
			$cropper[1]->select =  $component_foto->mid_select;
			$cropper[1]->title = "Среднее изображение:";
			$cropper[1]->file_crop_url = $component_foto->url_prefix.trans2eng($_FILES['newfoto']['name']);
		}
		if (  $component_foto->org_use  ){
			$cropper[2] = new foto_crop();	
			$cropper[2]->get_sizes (1, 	$component_foto->org_w, 	$component_foto->org_h, $_FILES['newfoto']['tmp_name']);
			$cropper[2]->photoid = "photo_org";
			$cropper[2]->previewid = "preview_org";
			$cropper[2]->type =    $component_foto->org_type;
			$cropper[2]->quality = $component_foto->org_quality;
			$cropper[2]->effect =  $component_foto->org_effect;
			$cropper[2]->select =  $component_foto->org_select;
			$cropper[2]->title = "Основное изображение:";
			$cropper[2]->file_crop_url = $component_foto->url_prefix.trans2eng($_FILES['newfoto']['name']);
		}
	}
	/*
	 * СОХРАНЯЕМ ИНДИВИДУАЛЬНЫЙ КОНФИГ
	 */
        if (  $component_foto->id>0  ){
            load_adminclass('config');
            $conf = new config($reg['db']);
            $conf->prefix_id = '#__foto'.$component_foto->type."_ID".$component_foto->id."__";
            $conf->save_config();
        }

	$component_foto->cropper = $cropper;
	$component_foto->foto_foto_crop($cid, $task);
}


function savefotoexgood_foto( $task ) {
	global $database, $my, $reg;
	
	$component_foto = new component_foto();
    $component_foto->default_init();
	$component_foto->init( ggrr('type') );
	$component_foto->savefoto_foto($task);
}

function removeexgood_foto( $task ) {
	global $database, $reg;
	$component_foto = new component_foto();
        $component_foto->default_init();
	$component_foto->init( ggrr('type') );
	// ggr($_REQUEST); ggd ($component_foto);

	foreach ($_REQUEST['cid'] as $dfgd){
		$exgoodfoto = ggo ($dfgd, "#__foto");  	delfile (  $component_foto->dir.$exgoodfoto->small  );
												delfile (  $component_foto->dir.$exgoodfoto->mid  );
												delfile (  $component_foto->dir.$exgoodfoto->org  );
												delfile (  $component_foto->dir.$exgoodfoto->full  );
		$adminlog = new adminlog();		$adminlog->logme('del_foto_cat', $component_foto->parent_component_name, $component_foto->parent_obj->name, $component_foto->parent_obj->id );
		ggsqlq ("DELETE FROM #__foto WHERE id=".$dfgd);
	}
	$msg = 'Фотография(и) удалены:'; $exgoodid = intval( getUserStateFromRequest(  'id', 0 ) );
	mosRedirect( 'index2.php?ca=foto&type='.$component_foto->type.'&parent='.$component_foto->parent.'&fotocat='.$component_foto->fotocat, $msg );
}
function editexgood_foto( $task ) {
	global $database, $reg;
	$component_foto = new component_foto();
        $component_foto->default_init();
	$component_foto->init( ggrr('type') );
	$names = new names($_REQUEST['cid'][0], 'fotoname'.$component_foto->type, $reg);
	$ithisfoto = ggo ($_REQUEST['cid'][0], "#__foto");

        $foto_publish = array(  mosHTML::makeOption( 1, 'Да'),  mosHTML::makeOption( 2, 'Нет')   );

        $foto_cats_array = ggsql("SELECT * FROM #__foto_cat AS a WHERE a.parent=".ggri('parent')." AND a.type='".ggrr('type')."' ORDER BY a.order ; ");  //ggtr ($database);
        $foto_cats =array(  mosHTML::makeOption( 0, 'Без привязки к подкатегории')  );
        foreach ($foto_cats_array as $ivcat)    $foto_cats[] = mosHTML::makeOption( $ivcat->id, $ivcat->name);

	?><form <? ctrlEnterCtrlAS (' '.$reg['submit_apply_event'], ' '.$reg['submit_save_event']) ?> name="adminForm" enctype="multipart/form-data" action="index2.php" method="post">
	<table class="adminheading"><tr><td class="edit"><?
			$component_foto->icatway = i24pathadd(  $component_foto->icatway, "Фотогалерея", ""  );
			$component_foto->icatway = i24pathadd(  $component_foto->icatway, "Редактирование Фото ".ggri('fnum'), ""  );
			i24pwprint_admin ($component_foto->icatway, 0);	
	?></td></tr></table>
	<table border="0" cellpadding="4" cellspacing="0" width="100%" align="center">
		<tr class="workspace">
			<td>Название: </td>
			<td><input name="name" style="width:570px;" value="<? print $ithisfoto->name; ?>"  /></td>
		</tr>
		<tr class="workspace">
			<td>Ссылка: </td>
			<td><input name="link" style="width:570px;" value="<?  print $ithisfoto->link; ?>"  /></td>
		</tr>

		<tr class="workspace">
			<td>Описание: </td>
			<td><textarea cols="85" rows="8" style="width:570px;" name="desc"><? print $ithisfoto->desc; ?></textarea></td>
		</tr>
                <tr  class="workspace">
                        <td>Подкатегория: </td>
                        <td><?php print mosHTML::selectList( $foto_cats, 'fotocat', 'size="1" style="width: 574px; " ', 'value', 'text', ggri('fotocat') ); ?></td>
                </tr>
                <tr class="workspace">
			<td>Публиковать на сайте</td>
			<td><?php print mosHTML::selectList( $foto_publish, 'publish', ' size="1" ', 'value', 'text', $ithisfoto->publish ); ?></td>
		</tr>
		<tr class="workspace">
			<td>Фото:</td>
			<td><a title="нажмите чтобы увеличить" onclick="return hs.expand(this)" class="highslide" href="<? print $component_foto->url_prefix; ?><? print $ithisfoto->org; ?>" ><img src="<? print $component_foto->url_prefix; ?><? print $ithisfoto->small; ?>"  border="2" style="border-color:#888888" align="absmiddle"  vspace="1" /></a></td>
		</tr>
		<tr class="workspace">
			<td>Закачать новое фото</td>
			<td><input type="file" size="85" name="newfoto"/><br>
                        или введите <input <? insiteform::make_java_text_effect('furl', 'input_light'); ?> type="text" name="furl" id="furl" class="input_gray" style="width:340px;" value="URL фото (не обязательное)" title="URL фото (не обязательное)" /></td>
		</tr>
	</table>
<?
	/*
	 * ВОД ИНДИВИУАЛЬНЫХ НАСТРОЕК ДЛЯ ОБЪЕКТА
	 * например индивидуальные параметры для фотографий
	 */
	load_adminclass('config');	$conf = new config($reg['db']);
	$conf->prefix_id = '#__foto'.$component_foto->type."_ID".$ithisfoto->id."__";
	$conf->returnme('index2.php?ca='.$reg['ca'].'&type='.$component_foto->type.'&parent='.ggri('parent').'&fotocat='.ggri('fotocat').'&task='.ggrr('task').'&cid[]='.$ithisfoto->id.'&fnum='.ggri('fnum') );
	$conf->show_config($conf->prefix_id, "addition_ajax");	//Дополнительные настройки
?>
	<input type="hidden" name="id" value="<? print $_REQUEST['cid'][0]; ?>" />
	<input type="hidden" name="parent" value="<? print $_REQUEST['parent']; ?>" />
	<input type="hidden" name="type" value="<? print $_REQUEST['type']; ?>" />
	<input type="hidden" name="task" value="apply"  />
	<input type="hidden" name="fnum" value="<?=ggri('fnum') ?>"  />
	<input type="hidden" name="ca" value="foto" /></form><?
}

?>