<?php
// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my, $task, $id;
$cid = josGetArrayInts( 'cid' );
//ggtr ($_REQUEST);
//ggtr ($task); die();
switch ($task) {
	case 'apply':
	case 'save':		saveicat( $task );
						break;
	case 'blockthem':	changeicatBlock( $cid, 0, $option );
						break;
	case 'allowthem':	changeicatBlock( $cid, 1, $option );
						break;
	case 'block':		changeicatBlock( $cid, 0, $option );
						break;
	case 'unblock':		changeicatBlock( $cid, 1, $option );
						break;
	case 'editA':		editicat( $id, $option );
						break;
	case 'new':			editicat( 0, $option );
						break;
	case 'remove':		removeicat( 0, $option );
						break;
	case 'saveorder':	saveOrdericat( $cid );
						break;
	case 'orderup':		orderupicat( $cid );
						break;
	case 'orderdown':	orderdownicat( $cid );
						break;
	default:			showicat( $option );
						break;
}
function orderupicat( $cid ) {
	global $database;
	$icatfoto_this = ggo($_REQUEST['cid'][0], '#__icat');
	$icatfoto_up = ggsql(" SELECT * FROM #__icat WHERE #__icat.order< ".$icatfoto_this->order." AND #__icat.parent=".$icatfoto_this->parent." ORDER BY #__icat.order DESC LIMIT 0,1 ;");  $icatfoto_up = $icatfoto_up[0];
//	ggtr ($database); die();
	$i24r = new mosDBTable( "#__icat", "id", $database );
	$i24r->id = $_REQUEST['cid'][0];
	$i24r->order = $icatfoto_up->order;
//	ggtr ($i24r);
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();

	$i24r = new mosDBTable( "#__icat", "id", $database );
	$i24r->id = $icatfoto_up->id;
	$i24r->order = $icatfoto_this->order;
//	ggtr ($i24r); die();
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	$msg = "Порядок изменен"; 
	mosRedirect( 'index2.php?ca=icat&task=view&limit='.$_REQUEST['limit'].'&limitstart='.$_REQUEST['limitstart'], $msg );
}
function orderdownicat( $cid ) {
	global $database;
	$icatfoto_this = ggo($_REQUEST['cid'][0], '#__icat');
	$icatfoto_up = ggsql(" SELECT * FROM #__icat WHERE #__icat.order> ".$icatfoto_this->order." AND #__icat.parent=".$icatfoto_this->parent." ORDER BY #__icat.order ASC LIMIT 0,1 ;");  $icatfoto_up = $icatfoto_up[0];
//	ggtr ($database); die();
	$i24r = new mosDBTable( "#__icat", "id", $database );
	$i24r->id = $_REQUEST['cid'][0];
	$i24r->order = $icatfoto_up->order;
//	ggtr ($i24r);
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();

	$i24r = new mosDBTable( "#__icat", "id", $database );
	$i24r->id = $icatfoto_up->id;
	$i24r->order = $icatfoto_this->order;
//	ggtr ($i24r); die();
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	$msg = "Порядок изменен"; 
	mosRedirect( 'index2.php?ca=icat&task=view&limit='.$_REQUEST['limit'].'&limitstart='.$_REQUEST['limitstart'], $msg );
}

function saveOrdericat( &$cid ) {
	global $database;
//	ggtr ($_REQUEST); die();
	for ($exi = 0; $exi<count($_REQUEST['order']); $exi++){
		$i24r = new mosDBTable( "#__icat", "id", $database );
		$i24r->id = $_REQUEST['icatid'][$exi];
		$i24r->order = $_REQUEST['order'][$exi];
		if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	}
	$msg 	= 'Новый порядок сохранен'; $icatid	= intval( getUserStateFromRequest(  'id', 0 ) );
	mosRedirect( 'index2.php?ca=icat&task=view&limit='.$_REQUEST['limit'].'&limitstart='.$_REQUEST['limitstart'], $msg );
} // saveOrder


function showicat_rec( $k, $icatid, $icatlev, $limit, $limitstart, &$pageNav, &$exsi ) {
		global $reg;
		$rows = ggsql("SELECT * FROM #__icat AS a WHERE a.parent=".$icatid." ORDER BY a.order ASC ; ");
		$component_foto = new component_foto ( 0 );
		$component_foto->init($reg['ca']);

		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row 	=& $rows[$i];			
			$task 	= $row->publish==0 ? 'unblock' : 'block';
			$alt 	= $row->publish==0 ? '<span style="color:#ff0000;">Блокирован</span>' : 'Разрешен';
			$alt2 	= $row->publish==0 ? 'Снять блокировку' : 'Блокировать';
			$link 	= 'index2.php?ca=icat&amp;task=editA&amp;id='. $row->id. '&amp;hidemainmenu=1&amp;search='. $_REQUEST['search'].'&amp;filter_type='. $_REQUEST['filter_type'].'&amp;filter_logged='. $_REQUEST['filter_logged'];
			$component_foto->parent = $row->id;
			
			if (  ($exsi>=($limitstart))  &&  ($exsi<=($limitstart+$limit))  ){
				?><tr class="<?php echo "row$k"; ?>"><?
					?><td><?php echo $exsi+1; ?></td><?
					?><td><?php echo mosHTML::idBox( $exsi, $row->id ); ?></td><?
					?><td align="left"><?
					for ($j=0; $j<$icatlev; $j++) print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					?><a href="<?php echo $link; ?>"><? print htmlspecialchars($row->name); ?></a><?
					?></td><?
					?><td><a href="index2.php?ca=content&task=view&amp;icsmart_content_catid=<? print $row->id; ?>">объекты (<? print ggsqlr ("SELECT count(id) FROM #__content WHERE catid=".$row->id." AND state<>-1"); ?>)</a></td><?
					?><td><a target="_blank" href="<?=$component_foto->get_link(); ?>">фото (<? print $component_foto->howmany_fotos(); ?>)</a></td><?
					?><td align="right"><?php echo $pageNav->orderUpIcon( $exsi, ($row->parent == @$rows[$i-1]->parent) ); ?></td><?
					?><td align="left"><?php echo $pageNav->orderDownIcon( $exsi, $n, ($row->parent == @$rows[$i+1]->parent) ); ?></td><?
					?><td align="center" style="text-align:center"><input type="text" name="order[]" size="5" value="<?php echo $row->order; ?>" class="text_area" style="text-align: center" /><input type="hidden" name="icatid[]" value="<?php echo $row->id; ?>" /></td><?
					?><td align="center" style="text-align:center"><a title="<? print $alt2; ?>" onclick="return listItemTask('cb<? print $exsi ?>','<? print $task; ?>')" href="javascript: void(0);"><?php echo $alt;?></a></td><?
					?><td align="center" style="text-align:center"><?php echo $row->id;?></td><?
				?></tr><?
				$k = 1 - $k; 
			}
			$exsi++;
			$row_subs = ggsqlr("SELECT count(id) FROM #__icat AS a WHERE a.parent=".$row->id." ; ");
			if (  $row_subs>0  ){
				showicat_rec( $k, $row->id, ($icatlev+1), $limit, $limitstart, $pageNav, $exsi );
			}
		}
}
function showicat( $option ) {
	global $database, $my, $iConfig_list_limit, $reg;
	$filter_type	= getUserStateFromRequest( 'filter_type', 0 );
	$filter_logged	= intval( getUserStateFromRequest(  'filter_logged', 0 ) );
	$limit 			= intval( getUserStateFromRequest( 'limit', $iConfig_list_limit ) );
	$limitstart 	= intval( getUserStateFromRequest( 'limitstart', 0 ) );
	
	$query = "SELECT COUNT(a.id) FROM #__icat AS a  "; $database->setQuery( $query ); $total = $database->loadResult();
	require_once( site_path . '/iadmin/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );
		?><form action="index2.php" method="post" name="adminForm">
		<table class="adminheading"><tr><td width="100%"><?
			$iway[0]->name=$reg['content_name'];
			$iway[0]->url="index2.php?ca=content";
			$iway[1]->name="список рубрик";
			$iway[1]->url="";
		
			i24pwprint_admin ($iway);		
		?></td></tr></table>
		<table class="adminlist">
		<tr><?
			?><th width="2%" class="title">#</th><?
			?><th width="3%" class="title"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo ($total); ?>);" /></th><?
			?><th class="title">Рубрика</th><?
			?><th class="title">Объекты</th><?
			?><th class="title">Фото</th><?
			?><th colspan="2" align="center" width="5%">Сортировка</th><?
			?><th width="3%" ><a href="javascript: saveorder( <?php echo count( $rows )-1; ?> )" onMouseOver="return Tip('Сохранить заданный порядок отображения');">Сохранить&nbsp;порядок</a></th><?
			?><th class="title" style="text-align:center">Доступ</th><?
			?><th class="title" style="text-align:center">ID</th><?
		?></tr><?
		$k = 0;  $exsi = 0;
		showicat_rec( $k, 0, 0, $limit, $limitstart, $pageNav, $exsi )
		?></table>
		<?php echo $pageNav->getListFooter(); ?>
		<input type="hidden" name="ca" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>
		<?php




}


function changeicatBlock( $cid=null, $block=1, $option ) {
	global $database;
	$action = $block ? 'блокировки' : 'разблокировки';
	if (count( $cid ) < 1) {
		echo "<script type=\"text/javascript\"> alert('Выберите объект для $action'); window.history.go(-1);</script>\n";
		exit;
	}
	mosArrayToInts( $cid );
	$cids = 'id=' . implode( ' OR id=', $cid );

	$query = "UPDATE #__icat"
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


function editicat( $uid='0', $option='users' ) {
	global $database, $my, $acl, $mainframe, $reg;

	if (  $uid>0  ) $row = ggo ($uid, "#__icat");
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
	do_icatlist(0, $vcats, 0, $row->id);
	
	$component_foto = new component_foto ( 0 );
	$component_foto->init($reg['ca']);
	$component_foto->parent = $row->id;

        $component_file = new component_file ( 0 );
        $component_file->init( $reg['ca'] );
        $component_file->parent = $row->id;

        //names
	$names = new names($row->id, $reg['ca'], $reg);

		?><script language="javascript" type="text/javascript">
		<!--
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			if (form.name.value == ""){
				alert( "Введите название рубрики" );
			} else {
				<?php getEditorContents( 'editor1', 'sdesc' ) ; ?>
				<?php getEditorContents( 'editor2', 'fdesc' ) ; ?>
				submitform( pressbutton );
			}
		}
		//-->
		</script>
<form <? ctrlEnterCtrlAS (' '.$reg['submit_apply_event'], ' '.$reg['submit_save_event']) ?> name="adminForm" action="index2.php" method="post"><input type="hidden"  name="iuse" id="iuse" value="0" />
<table class="adminheading"><tr><td width="100%"><?
	$iway[0]->name=$reg['content_name'];
	$iway[0]->url="index2.php?ca=icat&task=view";
	if (  $row->id  ){
		$iway[1]->name="Рубрика ".stripslashes($row->name).' [редактирование]';
		$iway[1]->url="";
	} else {
		$iway[1]->name="Новая рубрика";
		$iway[1]->url="";
	}
	i24pwprint_admin ($iway);
?></td></tr></table>

<table border="0" cellpadding="4" cellspacing="0" width="100%" align="center">
	<tr class="workspace">
		<td>Расположение рубрики: </td>
		<td align="left">
			<? print mosHTML::selectList( $vcats, 'parent', 'class="inputbox" size="1" id="icat" mosreq="1" moslabel="Группа" ', 'value', 'text', $row->parent ); ?>
		</td>
	</tr>
	<tr class="workspace">
		<td>Название: </td>
		<td align="left"><input name="name" size="120" mosreq="1" moslabel="Название" value="<? print ( $row->name ); ?>" /></td>
	</tr>
	<tr class="workspace">
		<td>Адрес:&nbsp;<span style="float:right;"><?=site_url ?>/</span></td>
		<td align="left"><input name="sefname" size="120" mosreq="1" moslabel="Название" value="<? print ( $row->sefname ); ?>" /></td>
	</tr>
        <tr class="workspace">
		<td><?=$reg['names_name']?>: </td>
		<td colspan="2" nowrap="nowrap" style="white-space:nowrap;"><a href="javascript: ins_ajax_open('?ca=names_ajax&task=shownames&4ajax=1', 570, 570); void(0);" title="Показать все значения"><img border="0" src="/iadmin/images/properties01.png"  align="absmiddle"/></a><?
			 print $names->field($row->id, 150, "all_names", "_names_field", "ex_tegs_names_style");
		?></td>
	</tr>
	
	<? if (  $reg['iseocontent']==1  ){ ?>
		<? itable_hr(2); ?>
		<tr class="workspace">
			<td></td>
			<td align="left"><a href="javascript: seoblock('.seoblock', '#seoblock_a'); void(0); " id="seoblock_a" visible="0">Показать данные для програмной оптимизации сайта</a></td>
		</tr>
		<tr class="workspace seoblock">
			<td>Содержимое тега<br />&lt;title&gt;&lt;/title&gt; рубрики: </td>
			<td><input name="seo_title" size="160"  value="<? print ($row->seo_title); ?>" /></td>
		</tr>
		<tr class="workspace seoblock">
			<td>Содержимое тега<br />&lt;meta Description&gt; рубрики: </td>
			<td><input name="seo_metadesc" size="160"  value="<? print ($row->seo_metadesc); ?>" /></td>
		</tr>
		<tr class="workspace seoblock">
			<td>Содержимое тега<br />&lt;meta Keywords&gt; рубрики: </td>
			<td><input name="seo_metakey" size="160"  value="<? print ($row->seo_metakey); ?>" /></td>
		</tr>
		
		<tr class="workspace seoblock">
			<td>Содержимое &lt;title&gt;&lt;/title&gt;<br /> для вложенных новостей(статей),<br />//**// &rarr; название новости(статьи):</td>
			<td><input name="seo_goodtitle" size="160"  value="<? print ($row->seo_goodtitle); ?>" /></td>
		</tr>
		<tr class="workspace seoblock">
			<td>Содержимое &lt;meta Description&gt;<br /> для вложенных новостей(статей),<br />//**// &rarr; название новости(статьи):</td>
			<td><input name="seo_goodmetadesc" size="160"  value="<? print ($row->seo_goodmetadesc); ?>" /></td>
		</tr>
		<tr class="workspace seoblock">
			<td>Содержимое &lt;meta Keywords&gt;<br /> для вложенных новостей(статей),<br />//**// &rarr; название новости(статьи):</td>
			<td><input name="seo_goodmetakey" size="160"  value="<? print ($row->seo_goodmetakey); ?>" /></td>
		</tr>
		<? itable_hr(2); ?>
	<? } ?>
	<tr class="workspace">
		<td>Краткое описание: </td>
		<td><? editorArea( 'editor1', ( $row->sdesc ) , 'sdesc', '100%;', '350', '75', '20' ) ; ?></td>
	</tr>
	<tr class="workspace">
		<td>Подробное описание: </td>
		<td><? editorArea( 'editor1', ( $row->fdesc ) , 'fdesc', '100%;', '550', '75', '40' ) ; ?></td>
	</tr>
	<tr class="workspace">
		<td></td>
                <td><table border="0" cellpadding="0" cellspacing="0"><tr><td><input name="dontautolist" type="checkbox" <? if (  $row->dontautolist==1  ) print 'checked="checked"'; ?> id="dontautolist" /></td><td><label for="dontautolist">Не формировать список вложеннных рубрик</label></td></tr></table></td>
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
		<td>Прикрепленные фото: <br /><br /><?
		if (  $row->id>0  ){
			$component_foto->make_galery_link();
		} else { 
			?><span class="gray">Добавление дополнительных<br />фото возможно только<br />после сохранения.</span><?
		} ?>
		</td>
		<td><?
			$exfotos = $component_foto->get_fotos();
			if(count($exfotos)){
				foreach ($exfotos as $exfoto){
					?><a title="нажмите чтобы увеличить" onclick="return hs.expand(this)" class="highslide" href="<? print site_url; ?>/images/icat/icat/<? print $exfoto->org; ?>" ><img src="/images/icat/icat/<? print $exfoto->small; ?>"  border="2" style="border-color:#888888" align="absmiddle"  vspace="1" /></a> <?
				}
			}
		?></td>
	</tr>
        <? itable_hr(2) ?>
	<tr class="workspace">
		<td>Прикрепленные файлы: <br /><br /><?
		if (  $row->id>0  ){
			$component_file->make_edit_link();
		} else {
			?><span class="gray">Прикрепление<br />файлов возможно только<br />после сохранения.</span><?
		} ?>
		</td>
		<td><?
			$component_file->iadmin_show_files();
		?></td>
	</tr>
</table>

<?
	/*
	 * ВОД ИНДИВИУАЛЬНЫХ НАСТРОЕК ДЛЯ ОБЪЕКТА
	 * например индивидуальные параметры для фотографий
	 */
	load_adminclass('config');	$conf = new config($reg['db']);
	$conf->prefix_id = '#__icat'."_ID".$row->id."__";
	$conf->returnme('index2.php?ca='.$reg['ca'].'&task=editA&hidemainmenu=1&id='.$row->id );
	$conf->show_config($conf->prefix_id, "addition_ajax");	//Дополнительные настройки
?>

<input type="hidden" name="id" value="<? print $row->id; ?>" />
<input type="hidden" name="task" value="save"  />
<input type="hidden" name="ca" value="icat" />
<script language="javascript">
function doform(){
	document.adminForm.submit();
	return 1;
}
</script><?
}

function saveicat( $task ) {
	global $database, $my, $reg;	
	$excat = ggo (  ggri('id'), "#__icat"  );  $newsefnamefull = false;
	if (  ggri('id')==0  ) { $newsefname = true; $excat->goods = 0; }	// новая категория - поэтому нужно сохранять sefname
	else if (  $excat->sefname!=ggrr('sefname')  ) $newsefname = true;  // изменен sefname - сохпаняем sefname
	else $newsefname = false;
	
	if (  $excat->parent!=$_REQUEST['parent']  or  ggri('id')==0  ){ $newsefnamefull = true;	// новая категория или поменяли родителя - необходимо обновить sefnamefull
		if (  $_REQUEST['parent']==0  ) $excat->sefnamefull = '';
		else { $papa = ggo (  $_REQUEST['parent'], "#__icat"  ); $excat->sefnamefull = $papa->sefnamefull.'/'.$papa->sefname; }
	}

	$i24r = new mosDBTable( "#__icat", "id", $database );
	$i24r->id = safelySqlInt($_REQUEST['id']);
	$i24r->parent = safelySqlInt($_REQUEST['parent']);
        $i24r->name =  safelySqlStr( $_REQUEST['name'] );
	$i24r->sdesc = safelySqlStr( $_REQUEST['sdesc'] );
	$i24r->fdesc = safelySqlStr( $_REQUEST['fdesc'] );
	$i24r->publish = safelySqlInt($_REQUEST['publish']);	
	$i24r->dontautolist = isset($_REQUEST['dontautolist'])?1:0;
	if (  $reg['iseocontent']==1  ){
		$i24r->seo_title = ggrr('seo_title');
		$i24r->seo_goodtitle = ggrr('seo_goodtitle');
		$i24r->seo_metadesc = ggrr('seo_metadesc');
		$i24r->seo_metakey = ggrr('seo_metakey');
		$i24r->seo_goodmetadesc = ggrr('seo_goodmetadesc');
		$i24r->seo_goodmetakey = ggrr('seo_goodmetakey');
	}
	if (  $newsefname  ){
		if (  ggrr('sefname')!=''  ) $i24r->sefname = sefname( ggrr('sefname') );
		else $i24r->sefname = sefname( $i24r->name );
	} else $i24r->sefname = $excat->sefname;
	
	if (  $newsefnamefull  ){ $i24r->sefnamefull = $excat->sefnamefull; }
	
	if (  $i24r->id==0  ){
		$iexmaxorder = ggsql ("SELECT * FROM #__icat WHERE parent=".$_REQUEST['parent']." ORDER BY #__icat.order DESC LIMIT 0,1 "); // ggtr ($iexmaxorder);
		$i24r->order = $iexmaxorder[0]->order+1;
	}
	if (!$i24r->check()) { echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n"; } else $i24r->store();
	
	if (  $newsefname  or  $newsefnamefull  ){ // необходимо обновить информацию sefnamefull для детей
		$recalc = new seorecalc();
		$recalc->good_table	= "#__content";
		$recalc->cat_table = "#__icat";
		$recalc->good_parent_field = "catid";
		$recalc->cat_parent_field = "parent";
		$recalc->recalc_req(  $excat->sefnamefull.'/'.ggrr('sefname'), $i24r->id, $excat->goods  );
	}

        // сохраняем NAMES
        $names = new names($i24r->id, $reg['ca'], $reg);
        $names->apply_names( ggrr('_names_field') );

	/*
	 * СОХРАНЯЕМ ИНДИВИДУАЛЬНЫЙ КОНФИГ
	 */	
	load_adminclass('config');	 
	$conf = new config($reg['db']);
	$conf->prefix_id = '#__icat'."_ID".$i24r->id."__";
	$conf->save_config();


	$adminlog = new adminlog();	
	if (  ggri('id')==0  )	$adminlog->logme('new_cat', $reg['content_name'], $i24r->name, $i24r->id ); else $adminlog->logme('save_cat', $reg['content_name'], $i24r->name, $i24r->id );

	switch ( $task ) {
		case 'apply':
			$msg = 'Рубрика сохранена: '. $i24r->name;
			mosRedirect( 'index2.php?ca=icat&task=editA&hidemainmenu=1&id='.$i24r->id, $msg );
			break;
		case 'save':
		default:
			$msg = 'Рубрика сохранена: '. $i24r->name;
			mosRedirect( 'index2.php?ca=icat', $msg );
			break;
	}
}

function removeicat( $task ) {
	global $database, $my, $reg;
	foreach ($_REQUEST['cid'] as $dfgd){
		// проверяем, есть ли вложенные объекты
		$exgoodsincat = ggsqlr( "SELECT count(id) FROM #__content WHERE catid=".$dfgd );
		if ($exgoodsincat>0){
			?><script language="javascript">  alert("Категория: '<? $icattodel = ggo($dfgd, "#__icat"); print $icattodel->name; ?>' содержит объекты, удаление невозможно");  </script><?
			continue;
		}
		// удаляем фото
		$component_foto = new component_foto ( 0 );
		$component_foto->init($reg['ca']);
		$component_foto->parent = $dfgd;
		$component_foto->load_parent();
		$component_foto->del_fotos();

		$adminlog_obg = $component_foto->parent_obj;	$adminlog = new adminlog(); $adminlog->logme('del_cat', $reg['content_name'], $adminlog_obg->name, $adminlog_obg->id );
		ggsqlq ("DELETE FROM #__icat WHERE id=".$dfgd);

                $names = new names($dfgd, $reg['ca'], $reg);
                $names->delete();

                // удаление индивидуальных настроек
                load_adminclass('config');
                $conf = new config($reg['db']);
                $conf->prefix_id = '#__icat'."_ID".$dfgd."__";
                $conf->remove_addition_config();
	}
	$msg = 'Рубрика(и) удалены: ';
	mosRedirect( 'index2.php?ca=icat', $msg );
}


?>