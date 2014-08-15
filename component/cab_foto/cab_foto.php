<?php
// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my, $task, $id, $option, $isgal, $fparent;
$cid = josGetArrayInts( 'cid' );
$id	= intval( getUserStateFromRequest(  'id', 0 ) );
$fparent = ggo(ggri('parent'), "#__cab_news");  $isgal = $fparent->type;
switch ($task) {
	case 'newfoto':		newfotocab_foto( $task );
						break;
	case 'remove':		removecab_foto( 0, $option );
						break;
	case 'edit':		editcab_foto( $id, $option );
						break;
	case 'apply':
	case 'save':		savefotocab_foto( $task );
						break;
	case 'saveorder':	saveOrdercab_foto( $cid );
						break;
	case 'orderup':		orderupcab_foto( $cid );
						break;
	case 'orderdown':	orderdowncab_foto( $cid );
						break;
	case 'cancel':		mosRedirect( 'index.php?c=cab_foto&task=view&parent='.$fparent->id, "" );
						break;
	default:			showcab_foto( $option );
						break;
}
function orderupcab_foto( $cid ) {
	global $database, $isgal, $fparent;
	  
	$excatfoto_this = ggo($_REQUEST['cid'][0], '#__cab_foto');
	$excatfoto_up = ggsql(" SELECT * FROM #__cab_foto WHERE #__cab_foto.order<".$excatfoto_this->order." and #__cab_foto.new_id=".$fparent->id." ORDER BY #__cab_foto.order DESC LIMIT 0,1 ;");  $excatfoto_up = $excatfoto_up[0];
//	ggtr ($database); die();
	$i24r = new mosDBTable( "#__cab_foto", "id", $database );
	$i24r->id = $_REQUEST['cid'][0];
	$i24r->order = $excatfoto_up->order;
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();

	$i24r = new mosDBTable( "#__cab_foto", "id", $database );
	$i24r->id = $excatfoto_up->id;
	$i24r->order = $excatfoto_this->order;
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	$msg = "Порядок изменен"; $excatid	= intval( getUserStateFromRequest(  'id', 0 ) );
	mosRedirect( 'index.php?c=cab_foto&task=view&parent='.$fparent->id.'&limit='.$_REQUEST['limit'].'&limitstart='.$_REQUEST['limitstart'], $msg );
}
function orderdowncab_foto( $cid ) {
	  
	global $database, $isgal, $fparent;
	$excatfoto_this = ggo($_REQUEST['cid'][0], '#__cab_foto');
	$excatfoto_up = ggsql(" SELECT * FROM #__cab_foto WHERE #__cab_foto.order> ".$excatfoto_this->order." and #__cab_foto.new_id=".$fparent->id." ORDER BY #__cab_foto.order ASC LIMIT 0,1 ;");  $excatfoto_up = $excatfoto_up[0];
//	ggtr ($database); die();
	$i24r = new mosDBTable( "#__cab_foto", "id", $database );
	$i24r->id = $_REQUEST['cid'][0];
	$i24r->order = $excatfoto_up->order;
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();

	$i24r = new mosDBTable( "#__cab_foto", "id", $database );
	$i24r->id = $excatfoto_up->id;
	$i24r->order = $excatfoto_this->order;
	if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	$msg = "Порядок изменен"; $excatid	= intval( getUserStateFromRequest(  'id', 0 ) );
	mosRedirect( 'index.php?c=cab_foto&task=view&parent='.$fparent->id.'&limit='.$_REQUEST['limit'].'&limitstart='.$_REQUEST['limitstart'], $msg );
}
function saveOrdercab_foto( &$cid ) {
	global $database, $isgal, $fparent;
	  
	//ggtr ($_REQUEST); //die();
	for ($exi = 0; $exi<count($_REQUEST['order']); $exi++){
		$i24r = new mosDBTable( "#__cab_foto", "id", $database );
		$i24r->id = $_REQUEST['exfotoid'][$exi];
		$i24r->order = $_REQUEST['order'][$exi];
		if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
	}
	$msg 	= 'Новый порядок сохранен'; $excatid	= intval( getUserStateFromRequest(  'id', 0 ) );
	mosRedirect( 'index.php?c=cab_foto&task=view&parent='.$fparent->id.'&limit='.$_REQUEST['limit'].'&limitstart='.$_REQUEST['limitstart'], $msg );
} // saveOrder
function showcab_foto( $option ) {
	global $database, $my, $iConfig_list_limit, $iConfig_list_limit, $isgal, $fparent;
	
	$excatid	= intval( getUserStateFromRequest(  'id', 0 ) );
	$limit 			= intval( getUserStateFromRequest( 'limit', $iConfig_list_limit ) );
	$limitstart 	= intval( getUserStateFromRequest( 'limitstart', 0 ) );

	$query = "SELECT COUNT(a.id) FROM #__cab_foto AS a WHERE a.new_id=".$fparent->id; $database->setQuery( $query ); $total = $database->loadResult();
	global $mosConfig_absolute_path;
	require_once( $mosConfig_absolute_path . '/includes/pageNavigation.php' );	
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );
	?><br /><? 
	$rows = ggsql("SELECT * FROM #__cab_foto AS a WHERE a.new_id=".$fparent->id." ORDER BY a.order ASC LIMIT $limitstart, $limit ; ");  //ggtr ($database);
	?><script language="javascript" type="text/javascript">
	<!--
	function client_submitbutton(pressbutton) {
		var form = document.adminForm;
		form.task.value=pressbutton; form.submit();
	}
	//-->
	</script>
		<form action="index.php" method="post"  id="fotofo" name="newfotoForm" enctype="multipart/form-data">	
		<table class="adminheading" width="100%">
		<tr>
			<th nowrap="nowrap" align="left" valign="top" style="text-align:left; vertical-align:top;">Добавление новой фотографии</th><th>&nbsp;</th><th class="small" align="left" valign="top" style="vertical-align:top; ">(Название файла должно содержать только латинские символы или цифры. Допускаются форматы изображения jpg / gif )</th>
		</tr>
		</table>
		<table class="adminheading" border="0">
		<tr>
			<td><input type="file" name="newfoto" size="85" /></td>
			<td><input type="button" value="Добавить фото" onclick="document.getElementById('fotofo').submit();" /></td>
		</tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr>
			<td colspan="2">Описание фото: <br /><textarea name="desc" cols="70" rows="4"></textarea></td>
		</tr>
		</table>
		<input type="hidden" name="c" value="cab_foto" />
		<input type="hidden" name="parent" value="<?=$fparent->id?>" />
		<input type="hidden" name="task" value="newfoto" />
	</form>
	
	<form action="index.php" method="get" name="adminForm" >
	<table cellspacing="0" cellpadding="0" border="0"  width="100%" align="center" >
		<tr>
			<td width="50%" class="cab_action"><? print ($isgal==1) ? "Управление фото" : "Фото прикрепленные к новости";?></td>
			<td align="right" style="text-align:right"><?
				?><table cellspacing="0" cellpadding="0" border="0"  align="right" id="toolbar">
					<tr>
						<td nowrap="nowrap" align="right"><a class="toolbar" href="javascript: client_submitbutton('remove'); ">Удалить</a></td><td>&nbsp;</td>
						<td nowrap="nowrap" align="right"><a class="toolbar" href="javascript: client_submitbutton('new'); ">Новый</a></td>
					</tr>
				</table><?
			?></td>
		</tr>
		<tr height="1px">
			<td colspan="2" bgcolor="#cecfce"></td>
		</tr>
	</table>

	<table class="adminlist" id="gallary" border="0" cellpadding="0" cellspacing="0" >
	<thead>
	<tr>
			<td width="4%" height="25" align="center">№</td>
			<td width="3%" align="center"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count($rows); ?>);" /></td>
			<td align="center">Описание</td>
			<td align="center">Фото</td><?
			?><td colspan="2" align="center" width="5%">Сортировка</td><?
			?><td id="green" width="18%" align="center"><a href="javascript: saveorder( <?php echo count( $rows )-1; ?> )" onMouseOver="return Tip('Сохранить заданный порядок отображения');">Сохранить&nbsp;порядок</a></td><?
			?><td ></td>
	</tr>
	</thead>
	<?php
	$k = 0;
	for ($i=0, $n=count( $rows ); $i < $n; $i++) {
		$row 	=& $rows[$i];
		$task 	= $row->publish==0 ? 'unblock' : 'block';
		$alt 	= $row->publish==0 ? '<span style="color:#ff0000;">Блокирован</span>' : 'Разрешен';
		$alt2 	= $row->publish==0 ? 'Снять блокировку' : 'Блокировать';
		$link 	= 'index.php?c=cab_foto&amp;task=editA&amp;id='. $row->id. '&amp;hidemainmenu=1&amp;search='. $_REQUEST['search'].'&amp;filter_type='. $_REQUEST['filter_type'].'&amp;filter_logged='. $_REQUEST['filter_logged'];
		?><tr class="<? if($i%2==1){echo "brown";}?>"><?
				?><td align="center"><?php echo $i+1+$pageNav->limitstart;?></td><?
				?><td align="center"><?php echo mosHTML::idBox( $i, $row->id ); ?></td><?
				?><td><? echo htmlspecialchars($row->desc); ?></td><?
				?><td align="center" style="padding-top:5px;padding-bottom:5px"><a title="нажмите чтобы увеличить" onclick="return hs.expand(this)" class="highslide" href="<?=($isgal ? site_url."/images/cab/foto/" : site_url."/images/cab/news/") ?><? print $row->org; ?>" ><img src="<?=($isgal ? site_url."/images/cab/foto/" : site_url."/images/cab/news/") ?><? print $row->small; ?>"  border="5" style="border-color:#cccccc" /></a></td><?
				?><td align="right"><?php echo $pageNav->orderUpIcon( $i, ($row->excat_id == @$rows[$i-1]->excat_id) ); ?></td><?
				?><td align="right"><?php echo $pageNav->orderDownIcon( $i, $n, ($row->excat_id == @$rows[$i+1]->excat_id) ); ?></td><?
				?><td align="center"><input type="text" name="order[]" size="5" value="<?php echo $row->order; ?>" class="text_area" style="text-align: center" /><input type="hidden" name="exfotoid[]" value="<?php echo $row->id; ?>" /></td><?
				?><td><a href="index.php?c=cab_foto&task=edit&id=<?=$row->id?>&parent=<?=$fparent->id?>">Изменить</a></td><?
		?></tr><?
		$k = 1 - $k;
	}
	?></table>
	<?php echo $pageNav->getListFooter(); ?>
	<input type="hidden" name="c" value="<?php echo $option;?>" />
	<input type="hidden" name="id" value="<?php echo $excatid;?>" />
	<input type="hidden" name="parent" value="<?php echo $fparent->id;?>" />
	<input type="hidden" name="task" value="view" />
	<input type="hidden" name="gal" value="<?=$isgal?>" />
	<input type="hidden" name="boxchecked" value="0" />
	</form>
	<?php 
}
function removecab_foto( $task ) {
	global $database, $isgal, $fparent;
	  
	foreach ($_REQUEST['cid'] as $dfgd){
		$contentfoto = ggo ($dfgd, "#__cab_foto");
		delfile( site_path.($isgal ? "/images/cab/foto/" : "/images/cab/news/").$contentfoto->small );
		delfile( site_path.($isgal ? "/images/cab/foto/" : "/images/cab/news/").$contentfoto->org );
		ggsqlq ("DELETE FROM #__cab_foto WHERE id=".$dfgd);
	}
	$msg = 'Фотография(и) удалены:';
	mosRedirect( 'index.php?c=cab_foto&task=view&parent='.$fparent->id, $msg );
}
function editcab_foto( $id, $option ) {
	global $database, $fparent, $isgal;

	$ithisfoto = ggo($id, "#__cab_foto");
	?><script language="javascript" type="text/javascript">
	<!--
	function client_submitbutton(pressbutton) {
		var form = document.dminForm;
		form.task.value=pressbutton; form.submit();
	}
	//-->
	</script>
	<form name="dminForm" enctype="multipart/form-data" action="index.php" method="post">
	<table cellspacing="0" cellpadding="0" border="0"  width="100%" align="center" >
		<tr>
			<td width="50%" class="cab_action"><? print ($isgal==1) ? "Управление фото" : "Фото прикрепленные к новости";?></td>
			<td align="right" style="text-align:right"><?
				?><table cellspacing="0" cellpadding="0" border="0"  align="right" id="toolbar">
					<tr>
						<td nowrap="nowrap" align="right"><a class="toolbar" href="javascript: client_submitbutton('save'); ">Сохранить</a></td><td>&nbsp;</td>
						<td nowrap="nowrap" align="right"><a class="toolbar" href="javascript: client_submitbutton('apply'); ">Применить</a></td><td>&nbsp;</td>
						<td nowrap="nowrap" align="right"><a class="toolbar" href="javascript: client_submitbutton('cancel'); ">Отменить</a></td>
					</tr>
				</table><?
			?></td>
		</tr>
		<tr height="1px">
			<td colspan="2" bgcolor="#cecfce"></td>
		</tr>
	</table>

	<table border="0" cellpadding="4" cellspacing="0" width="95%" align="center">
		<tr >
			<td>Описание: </td>
			<td><textarea cols="85" rows="8" name="desc"><?=stripslashes($ithisfoto->desc); ?></textarea></td>
		</tr>
		<tr >
			<td>Фото:</td>
			<td><a title="нажмите чтобы увеличить" onclick="return hs.expand(this)" class="highslide" href="<?=($isgal ? site_url."/images/cab/foto/" : site_url."/images/cab/news/") ?><? print $ithisfoto->org; ?>" ><img src="<?=($isgal ? site_url."/images/cab/foto/" : site_url."/images/cab/news/") ?><? print $ithisfoto->small; ?>"  border="2" style="border-color:#888888" align="absmiddle"  vspace="1" /></a></td>
		</tr>
		<tr >
			<td>Закачать новое фото</td>
			<td><input type="file" size="85" name="newfoto"/></td>
		</tr>
	</table>
	<input type="hidden" name="id" value="<? print $ithisfoto->id; ?>" />
	<input type="hidden" name="task" value="save"  />
	<input type="hidden" name="parent" value="<?=$fparent->id?>"  />
	<input type="hidden" name="c" value="<?=$option?>" />
	</form>
<? 
}
function newfotocab_foto( $task ) {
	global $database, $my, $isgal, $fparent, $reg;
	  
	
	$iexfototype = "jpg";
	$iexuni = md5(uniqid("exsalon"));
	$_FILES["newfoto"]['name'] = str_replace(" ", "_", $_FILES["newfoto"]['name']);
	$ismallexname = $_FILES["newfoto"]['name']."_small___".$iexuni.".".$iexfototype;
	$isorgexname = $_FILES["newfoto"]['name']."_orign___".$iexuni.".".$iexfototype; 
	//ggtr ($ismallexname,1); ggtr ($isorgexname,1); die();

	$i24makesmallfoto_func = $reg['uicontentcontentsmall_fix']==1 ? 'i24makesmallfoto_fix' : 'i24makesmallfoto';
	$i24makesmallfoto_func( $_FILES["newfoto"]['tmp_name'], site_path.($isgal ? "/images/cab/foto/" : "/images/cab/news/").$ismallexname,
								$reg['uicontentcontentsmallwidth'],	$reg['uicontentcontentsmallheight'],	$reg['uicontentcontenttag_small']);
								
	$i24makesmallfoto_func = $reg['uicontentcontentorigin_fix']==1 ? 'i24makesmallfoto_fix' : 'i24makesmallfoto';
	$i24makesmallfoto_func( $_FILES["newfoto"]['tmp_name'], site_path.($isgal ? "/images/cab/foto/" : "/images/cab/news/").$isorgexname,
								$reg['uicontentcontentoriginwidth'],	$reg['uicontentcontentoriginheight'],	$reg['uicontentcontenttag_origin']);
	
	$i24r = new mosDBTable( "#__cab_foto", "id", $database );
	$i24r->id = 0;
	$i24r->new_id = $fparent->id;
    $i24r->small = $ismallexname;
	$i24r->org = $isorgexname;
	$i24r->desc = ggrr('desc');
	$iexmaxorder = ggsql ("SELECT * FROM #__cab_foto WHERE new_id=".$fparent->id." ORDER BY #__cab_foto.order DESC LIMIT 0,1 "); // ggtr ($iexmaxorder);
	$i24r->order = $iexmaxorder[0]->order+1;
	if (!$i24r->check()) {
		echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";
	} else $i24r->store();
//	ggtr ($database, 20); die();
	$msg = 'Новое фото сохранено: ';
	mosRedirect( 'index.php?c=cab_foto&task=view&parent='.$fparent->id, $msg );
}
function savefotocab_foto( $task ) {
	global $database, $my, $isgal, $fparent, $reg;
	  
	$ithisfoto = ggo ($_REQUEST['id'], "#__cab_foto");
	if (  $_FILES["newfoto"]['tmp_name']  ){
		// УДАЛЕНИЕ СТАРЫХ ФОТО
		$ismallexname_old = site_path.($isgal ? "/images/cab/foto/" : "/images/cab/news/").$ithisfoto->small; 	delfile ($ismallexname_old);
		$isorgexname_old  = site_path.($isgal ? "/images/cab/foto/" : "/images/cab/news/").$ithisfoto->org;		delfile ($isorgexname_old);

		$iexfototype = "jpg";
		$iexuni = md5(uniqid("exsalon"));
		$_FILES["newfoto"]['name'] = str_replace(" ", "_", $_FILES["newfoto"]['name']);
		$ismallexname = $_FILES["newfoto"]['name']."_small___".$iexuni.".".$iexfototype;
		$isorgexname = $_FILES["newfoto"]['name']."_orign___".$iexuni.".".$iexfototype; 
		//ggtr ($ismallexname,1); ggtr ($isorgexname,1); die();

		$i24makesmallfoto_func = $reg['uicontentcontentsmall_fix']==1 ? 'i24makesmallfoto_fix' : 'i24makesmallfoto';
		$i24makesmallfoto_func( $_FILES["newfoto"]['tmp_name'], site_path.($isgal ? "/images/cab/foto/" : "/images/cab/news/").$ismallexname,
									$reg['uicontentcontentsmallwidth'],	$reg['uicontentcontentsmallheight'],	$reg['uicontentcontenttag_small']);
									
		$i24makesmallfoto_func = $reg['uicontentcontentorigin_fix']==1 ? 'i24makesmallfoto_fix' : 'i24makesmallfoto';
		$i24makesmallfoto_func( $_FILES["newfoto"]['tmp_name'], site_path.($isgal ? "/images/cab/foto/" : "/images/cab/news/").$isorgexname,
									$reg['uicontentcontentoriginwidth'],	$reg['uicontentcontentoriginheight'],	$reg['uicontentcontenttag_origin']);

	}
	else {
		$ismallexname = $ithisfoto->small;
		$isorgexname = $ithisfoto->org;
	}
	$i24r = new mosDBTable( "#__cab_foto", "id", $database );
	$contentidid	= intval( getUserStateFromRequest(  'id', 0 ) );
	
	$i24r->id = $contentidid;
    $i24r->small = $ismallexname;
	$i24r->org = $isorgexname;
	$i24r->desc = $_REQUEST['desc'];
//	ggtr ($i24r); 
//ggd ($i24r);
	if (!$i24r->check()) {		echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";	} else $i24r->store();
//	ggtr ($database, 20); die();
	$msg = 'Новое фото сохранено: ';
	switch ( $task ) {
		case 'apply':
			mosRedirect( 'index.php?c=cab_foto&task=edit&id='.$i24r->id.'&parent='.$fparent->id, $msg ); 
			break;
		case 'save':
		default:
			mosRedirect( 'index.php?c=cab_foto&task=view&parent='.$fparent->id, $msg );
			break;
	}

}
?>