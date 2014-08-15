<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my, $task, $id, $reg;
$cid = josGetArrayInts( 'cid' );
//ggtr ($_REQUEST);
//ggtr ($task); die();
switch ($task) {
	case 'apply':
	case 'save':		saveexfoto( $task );
						break;
	case 'blockthem':	changeExcatBlock( $cid, 0, $option );
						break;
	case 'allowthem':	changeExcatBlock( $cid, 1, $option );
						break;
	case 'block':		changeExcatBlock( $cid, 0, $option );
						break;
	case 'unblock':		changeExcatBlock( $cid, 1, $option );
						break;
	case 'editA':		editexfoto( $id, $option );
						break;
	case 'new':			editexfoto( 0, $option );
						break;
	case 'remove':		removeexfoto( 0, $option );
						break;
	case 'saveorder':	saveOrderexfoto( $cid );
						break;
	case 'orderup':		orderupexfoto( $cid );
						break;
	case 'orderdown':	orderdownexfoto( $cid );
						break;
	case 'cfg':			cfg();
						break;
	case 'savecfg':		load_adminclass('config');	 $conf = new config($reg['db']);   $conf->save_config();	$adminlog = new adminlog(); $adminlog->logme('cfg', $reg['exfoto_name'], "", "" );
						mosRedirect( 'index2.php?ca='.$reg['ca'].'&task=cfg', "Настройки сохранены" );
						break;
	case 'removecfg':	$adminlog = new adminlog(); $adminlog->logme('delcfg', $reg['exfoto_name'], "", "" );
						load_adminclass('config'); $conf = new config($reg['db']); $conf->remove($_REQUEST['conf_values'], $_REQUEST['id']); 
						mosRedirect( 'index2.php?ca='.$reg['ca'].'&task=cfg', "Настройки удалены" );
						break;
	case 'fotorecalc':	fotorecalc();
						break;
	default:			showexfoto( $option );
						break;
						
}

function fotorecalc() { 
	global $reg, $option;
	// необходимо пройтись по всем директориям рукурсией
	$recalc = new seorecalc();
	$recalc->good_table	= "#__exfoto_foto";
	$recalc->good_parent_field = "exfoto_id";
	$recalc->cat_table = "#__exfoto";
	$recalc->cat_parent_field = "parent";
	$sefurl = "/".$reg['exfoto_seoname']; $recalc->recalc_req($sefurl, 0, 0);

	//$sefurl = "/".$reg['ad_seoname']; adrecalc_req($sefurl, 0, 0);
	$msg = 'Пересчет SEO-путей и количества фотографий в фотогалереи завершен'; mosRedirect( 'index2.php?ca='.$reg['ca'].'&task=cfg', $msg );
	return ;
}

function cfg(){
	global $reg;
	?><form name="adminForm" action="index2.php" method="post"><input type="hidden"  name="iuse" id="iuse" value="0" />


		<table class="adminheading"><tr><td width="100%"><?
			$iway[0]->name=$reg['ad_name'];
			$iway[0]->url="index2.php?ca=adcat";
			$iway[1]->name="настройка";
			$iway[1]->url="";

			i24pwprint_admin ($iway);
			?></td></tr></table>
			<table border="0" cellpadding="4" cellspacing="0" width="100%" align="center">
				<tr class="workspace">
					<td><strong>Внимание: </strong></td>
					<td>- Если некоторые фотографии не открываются, значит необходимо <a class="bright" href="javascript: submitbutton('fotorecalc'); ">пересчитать</a> их SEO-пути.</td>
				</tr>
				<tr class="workspace">
					<td></td>
					<td>- Если указано не верное количество изображений в категориях - также необходимо выполнить функцию <a class="bright" href="javascript: submitbutton('fotorecalc'); ">пересчитать</a>.</td>
				</tr>
			
			</table>

	<? load_adminclass('config');	$conf = new config($reg['db']);
	$conf->show_config('foto', "<br />Дополнительные настройки"); ?>
	<input type="hidden" name="task" value="savecfg"  />
	<input type="hidden" name="ca" value="<?=$reg['ca'] ?>" />
	<input type="submit" style="display:none;" /></form><?
}

function orderupexfoto( $cid ) {
	global $database;
	$exfotofoto_this = ggo($_REQUEST['cid'][0], '#__exfoto');
	ggtr ($exfotofoto_this );		ggtr ($_REQUEST, 10); 
	$exfotofoto_up = ggsql(" SELECT * FROM #__exfoto WHERE #__exfoto.order< ".$exfotofoto_this->order." AND #__exfoto.parent=".$exfotofoto_this->parent." ORDER BY #__exfoto.order DESC LIMIT 0,1 ;");  $exfotofoto_up = $exfotofoto_up[0];
//	ggtr ($database); die();
	$i24r = new mosDBTable( "#__exfoto", "id", $database );
	$i24r->id = $_REQUEST['cid'][0];
	$i24r->order = $exfotofoto_up->order;
//	ggtr ($i24r);
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();

	$i24r = new mosDBTable( "#__exfoto", "id", $database );
	$i24r->id = $exfotofoto_up->id;
	$i24r->order = $exfotofoto_this->order;
//	ggtr ($i24r); die();
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	$msg = "Порядок изменен"; 
	mosRedirect( 'index2.php?ca=exfoto&task=view&limit='.$_REQUEST['limit'].'&limitstart='.$_REQUEST['limitstart'], $msg );
}
function orderdownexfoto( $cid ) {
	global $database;
	$exfotofoto_this = ggo($_REQUEST['cid'][0], '#__exfoto');
	ggtr ($exfotofoto_this );		ggtr ($_REQUEST, 10); 
	$exfotofoto_up = ggsql(" SELECT * FROM #__exfoto WHERE #__exfoto.order> ".$exfotofoto_this->order." AND #__exfoto.parent=".$exfotofoto_this->parent." ORDER BY #__exfoto.order ASC LIMIT 0,1 ;");  $exfotofoto_up = $exfotofoto_up[0];
//	ggtr ($database); die();
	$i24r = new mosDBTable( "#__exfoto", "id", $database );
	$i24r->id = $_REQUEST['cid'][0];
	$i24r->order = $exfotofoto_up->order;
//	ggtr ($i24r);
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();

	$i24r = new mosDBTable( "#__exfoto", "id", $database );
	$i24r->id = $exfotofoto_up->id;
	$i24r->order = $exfotofoto_this->order;
//	ggtr ($i24r); die();
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	$msg = "Порядок изменен"; 
	mosRedirect( 'index2.php?ca=exfoto&task=view&limit='.$_REQUEST['limit'].'&limitstart='.$_REQUEST['limitstart'], $msg );
}

function saveOrderexfoto( &$cid ) {
	global $database;
//	ggtr ($_REQUEST); die();
	for ($exi = 0; $exi<count($_REQUEST['order']); $exi++){
		$i24r = new mosDBTable( "#__exfoto", "id", $database );
		$i24r->id = $_REQUEST['exfotoid'][$exi];
		$i24r->order = $_REQUEST['order'][$exi];
		if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	}
	$msg 	= 'Новый порядок сохранен'; $exfotoid	= intval( getUserStateFromRequest(  'id', 0 ) );
	mosRedirect( 'index2.php?ca=exfoto&task=view&limit='.$_REQUEST['limit'].'&limitstart='.$_REQUEST['limitstart'], $msg );
} // saveOrder


function showexfoto_rec( $k, $exfotoid, $exfotolev, $limit, $limitstart, &$pageNav, &$exsi ) {
	global $reg;
	$rows = ggsql("SELECT * FROM #__exfoto AS a WHERE a.parent=".$exfotoid." ORDER BY a.order ASC ; ");

	$component_foto = new component_foto ( 0 );
	$component_foto->init($reg['ca']);

		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row 	=& $rows[$i];			
			$task 	= $row->publish==0 ? 'unblock' : 'block';
			$alt 	= $row->publish==0 ? '<span style="color:#ff0000;">Блокирован</span>' : 'Разрешен';
			$alt2 	= $row->publish==0 ? 'Снять блокировку' : 'Блокировать';
			$link 	= 'index2.php?ca=exfoto&amp;task=editA&amp;id='. $row->id. '&amp;hidemainmenu=1&amp;search='. $_REQUEST['search'].'&amp;filter_type='. $_REQUEST['filter_type'].'&amp;filter_logged='. $_REQUEST['filter_logged'];
			$component_foto->parent = $row->id;
			if (  ($exsi>=($limitstart))  &&  ($exsi<=($limitstart+$limit))  ){
				?><tr class="<?php echo "row$k"; ?>"><?
					?><td><?php echo $exsi+1; ?></td><?
					?><td><?php echo mosHTML::idBox( $exsi, $row->id ); ?></td><?
					?><td align="left" style="text-align:left"><?
					for ($j=0; $j<$exfotolev; $j++) print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					?><a href="<?php echo $link; ?>"><? print htmlspecialchars($row->name); ?></a><?
					?></td><?
					?><td><a target="_blank" href="<?=$component_foto->get_link(); ?>">фото (<? print $component_foto->howmany_fotos(); ?>)</a></td><?
					?><td align="right"><?php echo $pageNav->orderUpIcon( $exsi, ($row->parent == @$rows[$i-1]->parent) ); ?></td><?
					?><td align="left"><?php echo $pageNav->orderDownIcon( $exsi, $n, ($row->parent == @$rows[$i+1]->parent) ); ?></td><?
					?><td align="center"><input type="text" name="order[]" size="5" value="<?php echo $row->order; ?>" class="text_area" style="text-align: center" /><input type="hidden" name="exfotoid[]" value="<?php echo $row->id; ?>" /></td><?
					?><td align="center"><a title="<? print $alt2; ?>" onclick="return listItemTask('cb<? print $exsi ?>','<? print $task; ?>')" href="javascript: void(0);"><?php echo $alt;?></a></td><?
				?></tr><?
				$k = 1 - $k; 
			}
			$exsi++;
			$row_subs = ggsqlr("SELECT count(id) FROM #__exfoto AS a WHERE a.parent=".$row->id." ; ");
			if (  $row_subs>0  ){
				showexfoto_rec( $k, $row->id, ($exfotolev+1), $limit, $limitstart, $pageNav, $exsi );
			}
		}
}
function showexfoto( $option ) {
	global $database, $my, $iConfig_list_limit, $reg;
	$filter_type	= getUserStateFromRequest( 'filter_type', 0 );
	$filter_logged	= intval( getUserStateFromRequest(  'filter_logged', 0 ) );
	$limit 			= intval( getUserStateFromRequest( 'limit', $iConfig_list_limit ) );
	$limitstart 	= intval( getUserStateFromRequest( 'limitstart', 0 ) );
	
	$query = "SELECT COUNT(a.id) FROM #__exfoto AS a  "; $database->setQuery( $query ); $total = $database->loadResult();
	require_once( site_path . '/iadmin/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );
		?><form action="index2.php" method="post" name="adminForm">
		<table class="adminheading"><tr><td width="100%"><?
			$iway[0]->name=$reg['exfoto_name'];
			$iway[0]->url="index2.php?ca=exfoto&task=view";
			$iway[1]->name="Список категорий";
			$iway[1]->url="";

			i24pwprint_admin ($iway);

		?></td></tr></table>
		<table class="adminlist">
		<tr><?
			?><th width="2%" class="title">#</th><?
			?><th width="3%" class="title"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo ($total); ?>);" /></th><?
			?><th class="title">Категория</th><?
			?><th class="title">Фото</th><?
			?><th colspan="2" align="center" width="5%">Сортировка</th><?
			?><th width="3%" ><a href="javascript: saveorder( <?php echo count( $rows )-1; ?> )" onMouseOver="return Tip('Сохранить заданный порядок отображения');">Сохранить&nbsp;порядок</a></th><?
			?><th class="title" style="text-align:center">Доступ</th><?
		?></tr><?
		$k = 0;  $exsi = 0;
		showexfoto_rec( $k, 0, 0, $limit, $limitstart, $pageNav, $exsi )
		?></table>
		<?php echo $pageNav->getListFooter(); ?>
		<input type="hidden" name="ca" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
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

	$query = "UPDATE #__exfoto"
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


function editexfoto( $uid='0', $option='users' ) {
	global $database, $my, $acl, $mainframe, $reg;


	if (  $uid>0  ) $row = ggo ($uid, "#__exfoto");
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
	do_exfotolist(0, $vcats, 0, $row->id);
	
	$component_foto = new component_foto ( 0 );
	$component_foto->init($reg['ca']);
	$component_foto->parent = $row->id;

?><form <? ctrlEnterCtrlAS (' '.$reg['submit_apply_event'], ' '.$reg['submit_save_event']) ?> name="adminForm" action="index2.php" method="post"><input type="hidden"  name="iuse" id="iuse" value="0" />
<table class="adminheading"><tr><td width="100%"><?
	$iway[0]->name=$reg['exfoto_name'];
	$iway[0]->url="index2.php?ca=exfoto&task=view";
	if (  $row->id  ){
		$iway[1]->name="Категория ".stripslashes($row->name).' [редактирование]';
		$iway[1]->url="";
	} else {
		$iway[1]->name="Новая категория";
		$iway[1]->url="";
	}
	i24pwprint_admin ($iway);
?></td></tr></table>

<table border="0" cellpadding="4" cellspacing="0" width="100%" align="center">
	<tr class="workspace">
		<td>Расположение категории: </td>
		<td>
			<? print mosHTML::selectList( $vcats, 'parent', 'class="inputbox" size="1" id="exfoto" mosreq="1" moslabel="Группа" ', 'value', 'text', $row->parent ); ?>
		</td>
	</tr>
	<tr class="workspace">
		<td>Название: </td>
		<td><input name="name" size="120" mosreq="1" moslabel="Название" value="<? print ( $row->name ); ?>" /></td>
	</tr>
	<tr class="workspace">
			<td align="left">Адрес&nbsp;страницы:</td>
			<td><?=site_url.$row->sefnamefull ?>/<input class="inputbox" type="text" name="sefname" size="100" maxlength="100" value="<?php echo $row->sefname; ?>" /></td>
	</tr>
	<? if (  $reg['iseofoto']==1  ){ ?>
		<? itable_hr(2); ?>
		<tr class="workspace">
			<td></td>
			<td align="left"><a href="javascript: seoblock('.seoblock', '#seoblock_a'); void(0); " id="seoblock_a" visible="0">Показать данные для програмной оптимизации сайта</a></td>
		</tr>
		<tr class="workspace seoblock">
			<td>Содержимое тега<br />&lt;title&gt;&lt;/title&gt;&nbsp;категории:</td>
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
		<td>Опубликованно: </td>
		<td><select name="publish">
			<option <? if (  $row->publish==1  ) print 'selected="selected"'; ?> value="1">да</option>
			<option <? if (  $row->publish==0  ) print 'selected="selected"'; ?> value="0">нет</option>
		</select></td>
	</tr>
	<tr class="workspace">
		<td>Прикрепленные фото: <br /><br /><? $component_foto->make_galery_link() ?></td>
		<td><?php
			$exfotos = $component_foto->get_fotos();
			if(count($exfotos)){
				foreach ($exfotos as $exfoto){
					?><a title="нажмите чтобы увеличить" onclick="return hs.expand(this)" class="highslide" href="<? print site_url; ?>/images/foto/<? print $exfoto->org; ?>" ><img src="/images/foto/<? print $exfoto->small; ?>"  border="2" style="border-color:#888888" align="absmiddle"  vspace="1" /></a> <?
				}
			}
		?></td>
	</tr>
</table>

<?
	/*
	 * ВОД ИНДИВИУАЛЬНЫХ НАСТРОЕК ДЛЯ ОБЪЕКТА
	 * например индивидуальные параметры для фотографий
	 */
	load_adminclass('config');	$conf = new config($reg['db']);
	$conf->prefix_id = '#__exfoto'."_ID".$row->id."__";
	$conf->returnme('index2.php?ca='.$reg['ca'].'&task=editA&hidemainmenu=1&id='.$row->id );
	$conf->show_config($conf->prefix_id, "addition_ajax");	//Дополнительные настройки
?>

<input type="hidden" name="id" value="<? print $row->id; ?>" />
<input type="hidden" name="task" value="save"  />
<input type="hidden" name="ca" value="exfoto" />
<script language="javascript">
function doform(){
	document.adminForm.submit();
	return 1;
}
</script><?
}



function saveexfoto( $task ){
	global $database, $my, $reg;
	$excat = ggo (  ggri('id'), "#__exfoto"  );  $newsefnamefull = false;
	if (  ggri('id')==0  ) { $newsefname = true; $excat->goods = 0; }
	else if (  $excat->sefname!=ggrr('sefname')  ) $newsefname = true; 
	else $newsefname = false;
	
	if (  $excat->parent!=$_REQUEST['parent']  or  ggri('id')==0  ){ $newsefnamefull = true;	// новая категория или поменяли родителя - необходимо обновить sefnamefull
		if (  $_REQUEST['parent']==0  ) $excat->sefnamefull = '/'.$reg['exfoto_seoname'];
		else { $papa = ggo (  $_REQUEST['parent'], "#__exfoto"  ); $excat->sefnamefull = $papa->sefnamefull.'/'.$papa->sefname; }
	}

	$i24r = new mosDBTable( "#__exfoto", "id", $database );
	$i24r->id = safelySqlInt ( $_REQUEST['id'] );
	$i24r->parent = safelySqlInt( $_REQUEST['parent'] );
    $i24r->name  = safelySqlStr( $_REQUEST['name'] );
	$i24r->sdesc = safelySqlStr( $_REQUEST['sdesc'] );
	$i24r->fdesc = safelySqlStr( $_REQUEST['fdesc'] );
	if (  ggrr('sefname')!=''  ) $i24r->sefname = sefname( ggrr('sefname') );
	else $i24r->sefname = sefname( $i24r->name );
	$i24r->publish = safelySqlInt( $_REQUEST['publish'] );
	if (  $reg['iseofoto']==1  ){
		$i24r->seo_title = ggrr('seo_title');
		$i24r->seo_metadesc = ggrr('seo_metadesc');
		$i24r->seo_metakey = ggrr('seo_metakey');
	}
	
	if (  $newsefname  ){
		if (  ggrr('sefname')!=''  ) $i24r->sefname = sefname( ggrr('sefname') );
		else $i24r->sefname = sefname( $i24r->name );
	} else $i24r->sefname = $excat->sefname;
	
	if (  $newsefnamefull  ){ $i24r->sefnamefull = $excat->sefnamefull; }
	
	if (  $i24r->id==0  ){
		$iexmaxorder = ggsql ("SELECT * FROM #__exfoto WHERE parent=".$_REQUEST['parent']." ORDER BY #__exfoto.order DESC LIMIT 0,1 "); // ggtr ($iexmaxorder);
		$i24r->order = $iexmaxorder[0]->order+1;
	}
//	ggtr ($i24r); die();
	if (!$i24r->check()) { echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n"; } else $i24r->store();
//	ggtr ($database, 20); die();
	if (  $newsefname  or  $newsefnamefull  ){ // необходимо обновить информацию sefnamefull для детей

		$recalc = new seorecalc();
		$recalc->good_table	= "#__exfoto_foto";
		$recalc->good_parent_field = "exfoto_id";
		$recalc->cat_table = "#__exfoto";
		$recalc->cat_parent_field = "parent";
		$recalc->recalc_req(  $excat->sefnamefull.'/'.ggrr('sefname'), $i24r->id, $excat->goods  );
		//exrecalc_req(  $excat->sefnamefull.'/'.ggrr('sefname'), $i24r->id, $excat->goods  );
	}

	$adminlog = new adminlog();	
	if (  ggri('id')==0  )	$adminlog->logme('new_cat', $reg['exfoto_name'], $i24r->name, $i24r->id ); else $adminlog->logme('save_cat', $reg['exfoto_name'], $i24r->name, $i24r->id );

	/*
	 * СОХРАНЯЕМ ИНДИВИДУАЛЬНЫЙ КОНФИГ
	 */	
	load_adminclass('config');	 
	$conf = new config($reg['db']);
	$conf->prefix_id = '#__exfoto'."_ID".$i24r->id."__";
	$conf->save_config();

	switch ( $task ) {
		case 'apply':
			$msg = 'Категория сохранена: '. $i24r->name;  mosRedirect( 'index2.php?ca=exfoto&task=editA&hidemainmenu=1&id='.$i24r->id, $msg ); break;
		case 'save':
		default:
			$msg = 'Категория сохранена: '. $i24r->name;  mosRedirect( 'index2.php?ca=exfoto', $msg ); break;
	}
}

function removeexfoto( $task ) {
	global $database, $my, $reg;
	foreach ($_REQUEST['cid'] as $dfgd){
		// проверяем, есть ли вложенные объекты
		$exgoodsincat = ggsqlr( "SELECT count(id) FROM #__exfoto WHERE parent=".$dfgd );
		if ($exgoodsincat>0){
			?><script language="javascript">  alert("Категория: '<? $exfototodel = ggo($dfgd, "#__exfoto"); print $exfototodel->name; ?>' содержит объекты, удаление невозможно");  </script><?
			continue;
		}
		// удаляем фото
		$exfotofotos = ggsql( "SELECT * FROM #__exfoto_foto WHERE exfoto_id=".$dfgd );
		foreach ($exfotofotos as $exfotofoto){
			$ifilesmall = site_path."/images/foto/".$exfotofoto->small; 	delfile ($ifilesmall);
			$ifileorg = site_path."/images/foto/".$exfotofoto->org; 		delfile ($ifileorg);
			ggsqlq ("DELETE FROM #__exfoto_foto WHERE id=".$exfotofoto->id); 
		}
		$adminlog_obg = ggo($dfgd, "#__exfoto");	$adminlog = new adminlog(); $adminlog->logme('del_cat', $reg['exfoto_name'], $adminlog_obg->name, $adminlog_obg->id );
		ggsqlq ("DELETE FROM #__exfoto WHERE id=".$dfgd);
	}
	$msg = 'Категория(и) удалены: ';
	mosRedirect( 'index2.php?ca=exfoto', $msg );
}


?>