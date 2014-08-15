<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my, $task, $id;
require_once( site_path.'/component/ex/ex_lib.php' );

$cid = josGetArrayInts( 'cid' );
//ggtr ($_REQUEST);
//ggtr ($task); die();
switch ($task) {
	case 'apply':		
	case 'save':		saveexcat( $task );
						break;
	case 'blockthem':	changeExcatBlock( $cid, 0, $option );
						break;
	case 'allowthem':	changeExcatBlock( $cid, 1, $option );
						break;
	case 'block':		changeExcatBlock( $cid, 0, $option );
						break;
	case 'unblock':		changeExcatBlock( $cid, 1, $option );
						break;
	case 'editA':		editexcat( $id, $option );
						break;
	case 'new':             editexcat( 0, $option );
						break;
	case 'remove':		removeexcat( 0, $option );
						break;
	case 'saveorder':	saveOrderexcat( $cid );
						break;
	case 'orderup':		orderupexcat( $cid );
						break;
	case 'orderdown':	orderdownexcat( $cid );
						break;
	default:			showexcat( $option );
						break;
}
function orderupexcat( $cid ) {
	global $database;
	$excatfoto_this = ggo($_REQUEST['cid'][0], '#__excat');
	//ggtr ($excatfoto_this );
	$excatfoto_up = ggsql(" SELECT * FROM #__excat WHERE #__excat.order< ".$excatfoto_this->order." AND #__excat.parent=".$excatfoto_this->parent." ORDER BY #__excat.order DESC LIMIT 0,1 ;");  $excatfoto_up = $excatfoto_up[0];
//	ggtr ($database); die();
	$i24r = new mosDBTable( "#__excat", "id", $database );
	$i24r->id = $_REQUEST['cid'][0];
	$i24r->order = $excatfoto_up->order;
//	ggtr ($i24r);
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();

	$i24r = new mosDBTable( "#__excat", "id", $database );
	$i24r->id = $excatfoto_up->id;
	$i24r->order = $excatfoto_this->order;
//	ggtr ($i24r); die();
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	$msg = "Порядок изменен"; 
	mosRedirect( 'index2.php?ca=excat&task=view&limit='.$_REQUEST['limit'].'&limitstart='.$_REQUEST['limitstart'], $msg );
}
function orderdownexcat( $cid ) {
	global $database;
	$excatfoto_this = ggo($_REQUEST['cid'][0], '#__excat');
	//ggtr ($excatfoto_this );
	$excatfoto_up = ggsql(" SELECT * FROM #__excat WHERE #__excat.order> ".$excatfoto_this->order." AND #__excat.parent=".$excatfoto_this->parent." ORDER BY #__excat.order ASC LIMIT 0,1 ;");  $excatfoto_up = $excatfoto_up[0];
//	ggtr ($database); die();
	$i24r = new mosDBTable( "#__excat", "id", $database );
	$i24r->id = $_REQUEST['cid'][0];
	$i24r->order = $excatfoto_up->order;
//	ggtr ($i24r);
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();

	$i24r = new mosDBTable( "#__excat", "id", $database );
	$i24r->id = $excatfoto_up->id;
	$i24r->order = $excatfoto_this->order;
//	ggtr ($i24r); die();
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	$msg = "Порядок изменен"; 
	mosRedirect( 'index2.php?ca=excat&task=view&limit='.$_REQUEST['limit'].'&limitstart='.$_REQUEST['limitstart'], $msg );
}

function saveOrderexcat( &$cid ) {
	global $database;
//	ggtr ($_REQUEST); die();
	for ($exi = 0; $exi<count($_REQUEST['order']); $exi++){
		$i24r = new mosDBTable( "#__excat", "id", $database );
		$i24r->id = safelySqlInt(  $_REQUEST['excatid'][$exi]  );
		$i24r->order = $_REQUEST['order'][$exi];
		if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	}
	$msg 	= 'Новый порядок сохранен'; $excatid	= intval( getUserStateFromRequest(  'id', 0 ) );
	mosRedirect( 'index2.php?ca=excat&task=view&limit='.$_REQUEST['limit'].'&limitstart='.$_REQUEST['limitstart'], $msg );
} // saveOrder


function showexcat_rec( $k, $excatid, $excatlev, $limit, $limitstart, &$pageNav, &$exsi, &$all_expack_set ) {
		global $reg;
		$rows = ggsql("SELECT * FROM #__excat AS a WHERE a.parent=".$excatid." ORDER BY a.order ASC ; ");
		$component_foto = new component_foto ( 0 );
		$component_foto->init($reg['ca']);

		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
                        iflush::flush(10);   // данные отправляем браузеру, для мгновенного отображения
			$row 	=& $rows[$i];			
			$task 	= $row->publish==0 ? 'unblock' : 'block';
			$alt 	= $row->publish==0 ? '<span style="color:#ff0000;">Блокирован</span>' : 'Разрешен';
			$alt2 	= $row->publish==0 ? 'Снять блокировку' : 'Блокировать';
			$link 	= 'index2.php?ca=excat&amp;task=editA&amp;id='. $row->id. '&amp;hidemainmenu=1&amp;search='. $_REQUEST['search'].'&amp;filter_type='. $_REQUEST['filter_type'].'&amp;filter_logged='. $_REQUEST['filter_logged'];
			$component_foto->parent = $row->id;

			if (  ($exsi>=($limitstart))  &&  ($exsi<=($limitstart+$limit))  ){
				?><tr class="<?php echo "row$k"; ?>"><?
					?><td><?php echo $exsi+1; ?></td><?
					?><td><?php echo mosHTML::idBox( $exsi, $row->id ); ?></td><?
					?><td align="left"><?
					for ($j=0; $j<$excatlev; $j++) print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					?><a href="<?php echo $link; ?>"><? print (($row->name)); ?></a><?
					?></td><?
                                        ?><td><div id="pack_set_name<?=$row->id ?>"><a href="javascript: ins_ajax_open('?ca=expack&task=select_pack_set&excat=<?=$row->id ?>&4ajax=1', 570, 570); void(0);" title="Выбрать группу характеристик"><?
                                            if (  isset($all_expack_set[$row->expack_set])  ) print $all_expack_set[$row->expack_set]; else print 'не указанна';
                                        ?></a></div></td><?
					?><td><a href="index2.php?ca=exgood&task=view&amp;icsmart_exgood_parent=<? print $row->id; ?>">объекты (<? print ggsqlr ("SELECT count(id) FROM #__exgood WHERE parent=".$row->id.""); ?>)</a></td><?
					?><td><a target="_blank" href="<?=$component_foto->get_link(); ?>">фото (<? print $component_foto->howmany_fotos(); ?>)</a></td><?
					?><td align="right"><?php echo $pageNav->orderUpIcon( $exsi, ($row->parent == @$rows[$i-1]->parent) ); ?></td><?
					?><td align="left"><?php echo $pageNav->orderDownIcon( $exsi, $n, ($row->parent == @$rows[$i+1]->parent) ); ?></td><?
					?><td align="center"><input type="text" name="order[]" size="5" value="<?php echo $row->order; ?>" class="text_area" style="text-align: center" /><input type="hidden" name="excatid[]" value="<?php echo $row->id; ?>" /></td><?
					?><td align="center"><a title="<? print $alt2; ?>" onclick="return listItemTask('cb<? print $exsi ?>','<? print $task; ?>')" href="javascript: void(0);"><?php echo $alt;?></a></td><?
				?></tr><?
				$k = 1 - $k; 
			}
			$exsi++;
			$row_subs = ggsqlr("SELECT count(id) FROM #__excat AS a WHERE a.parent=".$row->id." ; ");
			if (  $row_subs>0  ){
				showexcat_rec( $k, $row->id, ($excatlev+1), $limit, $limitstart, $pageNav, $exsi, $all_expack_set );
			}
		}
}
function showexcat( $option ) {
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
	
	$query = "SELECT COUNT(a.id) FROM #__excat AS a  "; $database->setQuery( $query ); $total = $database->loadResult();
	require_once( site_path . '/iadmin/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );
		?><form action="index2.php" method="post" name="adminForm">
		<table class="adminheading"><tr><td width="100%"><?
			$iway[0] = new stdClass();
			$iway[0]->name = $reg['ex_name'];
			$iway[0]->url  = "";
			$iway[1] = new stdClass();
			$iway[1]->name = "Список категорий";
			$iway[1]->url  = "";

			i24pwprint_admin ($iway);
			?></td></tr></table>
		<table class="adminlist">
		<tr><?
			?><th width="2%" class="title">#</th><?
			?><th width="3%" class="title"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo ($total); ?>);" /></th><?
			?><th class="title">Категория</th><?
			?><th class="title">Группа хр-к</th><?
                        ?><th class="title">Объекты</th><?
			?><th class="title">Фото</th><?
			?><th colspan="2" align="center" width="5%">Сортировка</th><?
			?><th width="3%" ><a href="javascript: saveorder( <?php echo count( $rows )-1; ?> )" onMouseOver="return Tip('Сохранить заданный порядок отображения');">Сохранить&nbsp;порядок</a></th><?
			?><th class="title" style="text-align:center">Доступ</th><?
		?></tr><?
		$k = 0;  $exsi = 0;
		showexcat_rec( $k, 0, 0, $limit, $limitstart, $pageNav, $exsi, $all_expack_set )
		?></table>
		<?php echo $pageNav->getListFooter(); ?>
		<input type="hidden" name="ca" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
                <!-- НУЖЕН ДЛЯ СОВМЕСТИМОСТИ С expack ПРИ ВЫБОРЕ ГРУППЫ ХАРАКТЕРИСТИК  -->
                <input type="hidden" value="" id="pack_set_val" name="pack_set_val">
		</form>
		<?php
}


function changeExcatBlock( $cid=null, $block=1, $option ) {
	global $database;
	$action = $block ? 'блокировки' : 'разблокировки';
	if (count( $cid ) < 1) {
		echo "<script type=\"text/javascript\"> alert('Выберите объект для $action'); window.history.go(-1);</script>\n";
		exit;
	}
	mosArrayToInts( $cid );
	$cids = 'id=' . implode( ' OR id=', $cid );

	$query = "UPDATE #__excat"
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


function editexcat( $uid='0', $option='users' ) {
	global $database, $my, $acl, $mainframe, $reg;

	if (  $uid>0  ) $row = ggo ($uid, "#__excat");
	else {
		$row->id = 0;
		$row->parent = 0;   if (  ggri('parent')>0  ) $row->parent = ggri('parent');
		$row->name = "";
		$row->sdesc = "";
		$row->fdesc = "";
		$row->publish = 1;
		$row->order = 1;
	}
	$vcats[] = mosHTML::makeOption( '0', '- Главная -' );
	do_excatlist(0, $vcats, 0, $row->id);
	
	$component_foto = new component_foto ( 0 );
	$component_foto->init($reg['ca']);
	$component_foto->parent = $row->id;

        $component_file = new component_file ( 0 );
        $component_file->init( $reg['ca'] );
        $component_file->parent = $row->id;

	//names
	$names = new names($row->id, $reg['ca'], $reg);
?>
<table class="adminheading"><tr><td width="100%"><?php
	$iway[0] = new stdClass();
	$iway[1] = new stdClass();
	$iway[0]->name=$reg['ex_name'];
	$iway[0]->url="index2.php?ca=excat&task=view";
	if (  $row->id  ){
		$iway[1]->name="Категория ".stripslashes($row->name).' [редактирование]';
		$iway[1]->url="";
	} else {
		$iway[1]->name="Новая категория";
		$iway[1]->url="";
	}
	i24pwprint_admin ($iway);
?></td></tr></table>
<form <? ctrlEnterCtrlAS (' '.$reg['submit_apply_event'], ' '.$reg['submit_save_event']) ?> name="adminForm" action="index2.php" method="post"><input type="hidden"  name="iuse" id="iuse" value="0" />
<table border="0" cellpadding="4" cellspacing="0" width="100%" align="center">
	<tr class="workspace">
		<td>Расположение категории: </td>
		<td>
                    <table border="0" cellpadding="0" cellspacing="0" width="700" align="left">
                            <tr class="workspace">
                                    <td><? print mosHTML::selectList( $vcats, 'parent', 'class="inputbox" size="1" id="excat" mosreq="1" moslabel="Группа" ', 'value', 'text', $row->parent ); ?></td>
                                    <td class="gray">Поле для синхронизации категории с внешней базой (1С)</td>
                                    <td><input class="gray" name="connect" size="254" style="width:100px;" value="<? print ($row->connect); ?>" /></td>
                            </tr>
                    </table>
		</td>
	</tr>
	<tr class="workspace">
		<td>Название: </td>
		<td><input name="name" size="120" mosreq="1" moslabel="Название" value="<? print ($row->name); ?>" /></td>
	</tr>
	<tr class="workspace">
		<td class="gray">Адрес:&nbsp;</td>
		<td>
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
			<tr>
				<td nowrap="nowrap"  width="16%" class="gray" style="white-space:nowrap;"><?=site_url.$row->sefnamefull."/"; ?></td>
				<td align="left" width="84%"><input class="gray" name="sefname" size="57" mosreq="1" moslabel="Название" value="<? print ( $row->sefname ); ?>" /></td>
			</tr>
			</table>
		</td>
	</tr>
        <tr class="workspace">
		<td><?=$reg['names_name']?>: </td>
		<td colspan="2" nowrap="nowrap" style="white-space:nowrap;"><a href="javascript: ins_ajax_open('?ca=names_ajax&task=shownames&4ajax=1', 570, 570); void(0);" title="Показать все значения"><img border="0" src="/iadmin/images/properties01.png"  align="absmiddle"/></a><?
			 print $names->field($row->id, 150, "all_names", "_names_field", "ex_tegs_names_style");
		?></td>
	</tr>
	<? if (  $reg['iseoex']==1  ){ ?>
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
			<td>Содержимое &lt;title&gt;&lt;/title&gt;<br /> для вложенных товаров,<br />//**// &rarr; название товара:</td>
			<td><input name="seo_goodtitle" size="160"  value="<? print ($row->seo_goodtitle); ?>" /></td>
		</tr>
		<tr class="workspace seoblock">
			<td>Содержимое &lt;meta Description&gt;<br /> для вложенных товаров,<br />//**// &rarr; название товара:</td>
			<td><input name="seo_goodmetadesc" size="160"  value="<? print ($row->seo_goodmetadesc); ?>" /></td>
		</tr>
		<tr class="workspace seoblock">
			<td>Содержимое &lt;meta Keywords&gt;<br /> для вложенных товаров,<br />//**// &rarr; название товара:</td>
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
        <? itable_hr(2) ?>
	<tr class="workspace">
		<td>Прикрепленные фото: <br /><br /><? $component_foto->make_galery_link() ?></td>
		<td><?
			$exfotos = $component_foto->get_fotos();
			if(count($exfotos)){
				foreach ($exfotos as $exfoto){
					?><a title="нажмите чтобы увеличить" onclick="return hs.expand(this)" class="highslide" href="<? print site_url; ?>/images/ex/cat/<? print $exfoto->org; ?>" ><img src="/images/ex/cat/<? print $exfoto->small; ?>"  border="2" style="border-color:#888888" align="absmiddle"  vspace="1" /></a> <?
				}
			}
		?></td>
	</tr>
        <? itable_hr(2) ?>
	<tr class="workspace">
		<td>Группа характеристик для товаров в этой категории:</td>
                <td colspan="2"><?
                    if (  $row->expack_set  )	$expack_set = ggo ($row->expack_set, "#__expack_set");
                    else {  $expack_set->id=0;  $expack_set->name="не указанна";    }
                    
                    ?>Выбранная группа:&nbsp;<span id="pack_set_name<?=$row->id ?>"><?=$expack_set->name ?></span><input name="pack_set_val" id="pack_set_val" type="hidden" value="<?=$expack_set->id ?>" /><?
                    ?><br><br><a href="javascript: ins_ajax_open('?ca=expack&task=select_pack_set&excat=<?=$row->id ?>&4ajax=1', 570, 570); void(0);">Выбрать группу&nbsp;характеристик</a><?

                ?></td>
	</tr>
        <? itable_hr(2) ?>


</table>

<?
	/*
	 * ВОД ИНДИВИУАЛЬНЫХ НАСТРОЕК ДЛЯ ОБЪЕКТА
	 * например индивидуальные параметры для фотографий
	 */
	load_adminclass('config');	$conf = new config($reg['db']);
	$conf->prefix_id = '#__excat'."_ID".$row->id."__";
	$conf->returnme('index2.php?ca='.$reg['ca'].'&task=editA&hidemainmenu=1&id='.$row->id );
	$conf->show_config($conf->prefix_id, "addition_ajax");	//Дополнительные настройки
?>

<input type="hidden" name="id" value="<? print $row->id; ?>" />
<input type="hidden" name="task" value="save"  />
<input type="hidden" name="ca" value="excat" />
<script language="javascript">
function doform(){
	document.adminForm.submit();
	return 1;
}
</script><?
}

function saveexcat( $task ) {
	global $database, $my, $reg;
	$excat = ggo (  ggri('id'), "#__excat"  );  $newsefnamefull = false;
	if (  ggri('id')==0  ) { $newsefname = true; $excat->goods = 0; }
	else if (  $excat->sefname!=ggrr('sefname')  ) $newsefname = true; 
	else $newsefname = false;
	
	if (  $excat->parent!=$_REQUEST['parent']  or  ggri('id')==0  ){ $newsefnamefull = true;	// новая категория или поменяли родителя - необходимо обновить sefnamefull
		if (  $_REQUEST['parent']==0  ) $excat->sefnamefull = '/'.$reg['ex_seoname'];
		else { $papa = ggo (  $_REQUEST['parent'], "#__excat"  ); $excat->sefnamefull = $papa->sefnamefull.'/'.$papa->sefname; }
	}

	$i24r = new mosDBTable( "#__excat", "id", $database );
	$i24r->id = $_REQUEST['id'];
	$i24r->parent = $_REQUEST['parent'];
        $i24r->name = $_REQUEST['name'];
	$i24r->sdesc = $_REQUEST['sdesc'];
	$i24r->fdesc = $_REQUEST['fdesc'];
	$i24r->publish = $_REQUEST['publish'];
        $i24r->connect = ggrr('connect');
        $i24r->expack_set = $_REQUEST['pack_set_val'];
	if (  $reg['iseoex']==1  ){
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
	} else $i24r->sefname = $excat->sefname;
	
	if (  $newsefnamefull  ){ $i24r->sefnamefull = $excat->sefnamefull; }
	
	if (  $i24r->id==0  ){
		$iexmaxorder = ggsql ("SELECT * FROM #__excat WHERE parent=".$_REQUEST['parent']." ORDER BY #__excat.order DESC LIMIT 0,1 "); // ggtr ($iexmaxorder);
		$i24r->order = $iexmaxorder[0]->order+1;
	}
	if (!$i24r->check()) { echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n"; } else $i24r->store();
	if (  $newsefname  or  $newsefnamefull  ){ // необходимо обновить информацию sefnamefull для детей

		$recalc = new seorecalc();
		$recalc->good_table	= "#__exgood";
		$recalc->cat_table = "#__excat";
		$recalc->good_parent_field = "parent";
		$recalc->cat_parent_field = "parent";
		$recalc->recalc_req(  $excat->sefnamefull.'/'.ggrr('sefname'), $i24r->id, $excat->goods  );
		//exrecalc_req(  $excat->sefnamefull.'/'.ggrr('sefname'), $i24r->id, $excat->goods  );
	}

	$adminlog = new adminlog();	
	if (  ggri('id')==0  )	$adminlog->logme('new_cat', $reg['ex_name'], $i24r->name, $i24r->id ); else $adminlog->logme('save_cat', $reg['ex_name'], $i24r->name, $i24r->id );

        // сохраняем NAMES
        $names = new names($i24r->id, $reg['ca'], $reg);
        $names->apply_names(ggrr('_names_field'));

	/*
	 * СОХРАНЯЕМ ИНДИВИДУАЛЬНЫЙ КОНФИГ
	 */	
	load_adminclass('config');	 
	$conf = new config($reg['db']);
	$conf->prefix_id = '#__excat'."_ID".$i24r->id."__";
	$conf->save_config();

	switch ( $task ) {
		case 'apply':
			$msg = 'Категория сохранена: '. $i24r->name; mosRedirect( 'index2.php?ca=excat&task=editA&hidemainmenu=1&id='.$i24r->id, $msg );
		case 'save':
		default:
			$msg = 'Категория сохранена: '. $i24r->name; mosRedirect( 'index2.php?ca=excat', $msg );
			break;
	}
}

function removeexcat( $task ) {
	global $database, $my, $reg;
        $excat = new excat();
	foreach ($_REQUEST['cid'] as $dfgd){
            $excat->id = $dfgd;
            $excat->delme( 1, 0 );
	}
	$msg = 'Категория(и) удалены: ';
	mosRedirect( 'index2.php?ca=excat', $msg );
}


?>