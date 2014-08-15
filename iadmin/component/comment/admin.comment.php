<?php
// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my, $task, $id;
$cid = josGetArrayInts( 'cid' );
switch ($task) {
	case 'save':		savecontent_comment( $task );
						break;
	case 'editA':		editcontent_comment( $id, $option );
						break;
	case 'new':			editcontent_comment( 0, $option );
						break;
	case 'remove':		removecontent_comment( 0, $option );
						break;
	case 'saveorder':	saveOrdercontent_comment( $cid );
						break;
	case 'orderup':		orderupcontent_comment( $cid );
						break;
	case 'orderdown':	orderdowncontent_comment( $cid );
						break;
	case 'cancel':		$msg = ''; mosRedirect( 'index2.php?ca=comment', $msg );
						break;
	case 'block':		changeCommentBlock( $cid, 0, $option );
						break;
	case 'unblock':		changeCommentBlock( $cid, 1, $option );
						break;
	default:			showcontent_comment( $option );
						break;
}
function changeCommentBlock( $cid=null, $block=1, $option ) {
	global $database, $reg;
	$action = $block ? 'блокировки' : 'разблокировки';
	if (count( $cid ) < 1) {		echo "<script type=\"text/javascript\"> alert('Выберите объект для $action'); window.history.go(-1);</script>\n";		exit;	}
	
	mosArrayToInts( $cid );
	$cids = 'id=' . implode( ' OR id=', $cid );
	$query = "UPDATE #__comments SET publish = " . (int) $block. " WHERE ( $cids )" ;
	$database->setQuery( $query ); // ggd ($query);
	if (!$database->query()) {		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";		exit();	}
	
	mosRedirect( 'index2.php?ca='. $reg['ca'] );
}
function showcontent_comment_rec( $k, $content_commentid, $content_commentlev, $limit, $limitstart, &$pageNav, &$exsi, &$query_rows ) {
	global $database, $reg;
	$rows = ggsql(  $query_rows  );

		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row 	=& $rows[$i];
			$task 	= $row->publish==0 ? 'unblock' : 'block';
			$alt 	= $row->publish==0 ? '<span style="color:#ff0000;">Блокирован</span>' : 'Разрешен';
			$alt2 	= $row->publish==0 ? 'Снять блокировку' : 'Блокировать';
			$link 	= 'index2.php?ca=comment&amp;task=editA&amp;id='. $row->id. '&amp;hidemainmenu=1&amp;search='. $_REQUEST['search'].'&amp;filter_type='. $_REQUEST['filter_type'].'&amp;filter_logged='. $_REQUEST['filter_logged'];
			if (  ($exsi>=($limitstart))  &&  ($exsi<=($limitstart+$limit))  ){
				?><tr class="<?php echo "row$k"; ?>"><?
					?><td><?php echo $exsi+1; ?></td><?
					?><td><?php echo mosHTML::idBox( $exsi, $row->id ); ?></td><?
					?><td align="left">
						<? if (  $row->userid  ){ ?>
							<a href="index2.php?ca=users&task=editA&id=<?=$row->userid ?>&hidemainmenu=1" title="перейти к пользователю"><? print ($row->name); ?></a><br />
						<? } else { ?>
							<span class="light_text"><? print ($row->name); ?></span><br />
						<? } ?><span class="light_text"><? print ($row->mail); ?></span><?
					?></td><?
					?><td><?
						$str_offset = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $content_commentlev);
						print $str_offset; ?><a href="<?php echo $link; ?>" title="редактировать комментарий"><? print ($row->text); ?></a><br /><?
						?><span class="light_text">&rarr;&nbsp;</span><a href="<?=$row->url."#com".$row->id; ?>" class="light_text" target="_blank">перейти к комментарию</a><br /><?
						?><span class="light_text">&rarr;&nbsp;</span><a href="javascript: ins_ajax_open('?ca=write_comment_ajax&4ajax=1&parent=<?=$row->id ?>&type=comment&say=say_answer', 400, 470); void(0);" class="light_text" >ответить</a></td><?
					?><td><a title="<? print $alt2; ?>" onclick="return listItemTask('cb<? print $exsi ?>','<? print $task; ?>')" href="javascript: void(0);"><?php echo $alt;?></a><br /><?
					$itime = getdate($row->time + $reg['iServerTimeOffset']);  print $itime['year'].".".num::fillzerro($itime['mon'],2).".".num::fillzerro($itime['mday'],2)."&nbsp;&nbsp;".$itime['hours'].":".$itime['minutes']; ?><br /><?
					?><span class="light_text"><? print $row->ip; ?></span></td><?
					?><td><? 
						$comments = new comments($row->type, $reg['db'], $reg);
						$comments->init(); if (  icsmart('icsmart_comment_type')=='order'  ) $comments->parent_component_name = "Вопрос/ответ";
						$comments->parent=$row->parent;	$comments->load_parent();
						?><span style="white-space:nowrap"><? print ($comments->parent_component_name); ?></span><?
						?><br /><span class="light_text"><? print ($comments->parent_obj->name); ?></span><?
					?></td><?
				?></tr><?
				$k = 1 - $k; 
				$row_sub_comments = ggsqlr("SELECT count(id) FROM #__comments AS a WHERE a.parent=".$row->id." AND a.type='comment' ; ");
				if (  $row_sub_comments>0  ){
					$sql_query = "SELECT * FROM #__comments AS a WHERE a.parent=".$row->id." AND a.type='comment' ; ";
					showcontent_comment_rec( $k, $row->id, ($content_commentlev+1), $limit, $limitstart, $pageNav, $exsi,  $sql_query);
				}

			}
			$exsi++;
		}
}
function showcontent_comment( $option ) {
	global $database, $my, $iConfig_list_limit, $id, $reg;
	$filter_type	= getUserStateFromRequest( 'filter_type', 0 );
	$filter_logged	= intval( getUserStateFromRequest(  'filter_logged', 0 ) );
	$limit 			= intval( getUserStateFromRequest( 'limit', $iConfig_list_limit ) );
	$limitstart 	= intval( getUserStateFromRequest( 'limitstart', 0 ) );
	
	$component_comment = new comments(icsmart('icsmart_comment_type'), $reg['db'], $reg);
	$component_comment->init();
	
	$vcats_comment[] = mosHTML::makeOption( 0, "- Отобразить все -");
	if (  icsmart('icsmart_comment_type')!=""  )  {
		$component_comment->load_parent=1;
		$component_comment->parent=icsmarti('icsmart_comment_parent2');
		$component_comment->init();
		$vcats_comment[] = mosHTML::makeOption( $component_comment->parent_obj->id, $component_comment->parent_obj->name  );
	}
	if (  icsmarti('icsmart_comment_parent')>0  ){
		$where[]= " a.parent='".icsmarti('icsmart_comment_parent')."' ";
		if (  icsmart('icsmart_comment_type')!=""  )  $where[]= " a.type='".icsmarti('icsmart_comment_type')."' ";		
	} else {
		// $where[]= " a.type<>'comment' ";		
	}
	
	$query = "SELECT COUNT(a.id) FROM #__comments AS a  ".( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" ); $database->setQuery( $query ); $total = $database->loadResult();
	$query_rows = str_replace("COUNT(a.id)", "*", $query);
	$query_rows .= "ORDER BY a.id DESC LIMIT $limitstart, $limit ";
	
	require_once( site_path . '/iadmin/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );
		?><form action="index2.php" method="get" name="searchForm">
		<table class="adminheading comments"><tr><?
			?><td width="100%" nowrap="nowrap"><?
				if (  icsmarti('icsmart_comment_parent')==0  ){
					$component_comment->icatway = array();
				}
				$component_comment->icatway = i24pathadd(  $component_comment->icatway, "Управление комментариями", ""  );
				i24pwprint_admin ($component_comment->icatway, 0);
			?></td><?
			?><td align="right" >Родитель:&nbsp;</td><?
			?><td ><? print mosHTML::selectList( $vcats_comment, 'icsmart_comment_parent', 'class="inputbox" onchange="document.searchForm.submit();" size="1" id="icsmart_comment_parent" mosreq="1" moslabel="Группа" ', 'value', 'text', icsmarti('icsmart_comment_parent') ); ?></td><?
		?></tr></table><input type="hidden" name="ca" value="<?php echo $option;?>" /><?
		?><input type="hidden" name="task" value="" /><?
		?></form><form action="index2.php" method="post" name="adminForm">
		<table class="adminlist">
		<tr><?
			?><th width="2%" class="title">#</th><?
			?><th width="3%" class="title"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo ($total); ?>);" /></th><?
			?><th class="title">Имя<br /><span class="light_text">e-mail</span></th><?
			?><th class="title">Текст</th><?
			?><th class="title">Добавлено<br /><span class="light_text">IP</span></th><?
			?><th class="title">Объект</th><?
		?></tr><?
		$k = 0;  $exsi = $limitstart;
		showcontent_comment_rec( $k, 0, 0, $limit, $limitstart, $pageNav, $exsi, $query_rows )
		?></table>
		<?php echo $pageNav->getListFooter(); ?>
		<input type="hidden" name="ca" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>
		<?php
}

function editcontent_comment( $uid='0', $option='users' ) {
	global $database, $my, $acl, $mainframe, $reg;

//	$thiscomment=$row->parent;	$comments->load_parent();	
//	$comments = new comments($row->type, $reg['db'], $reg);


	if (  $uid>0  ) $row = ggo ($uid, "#__comments");
	else {
		$row->id = 0;
		if (  icsmarti('icsmart_content_comment_ire')  ) {  $row->cid = icsmarti('icsmart_content_comment_ire');  }
		$row->name = "";
		$row->mail = "";
		$row->mail = "";
		$row->text = "";
		$row->time = 0;
		$row->ip 	 = "";
	}
	$comments = new comments($row->type, $reg['db'], $reg);
	$comments->init();
	$comments->parent=$row->parent;	$comments->load_parent();
?><form name="adminForm" action="index2.php" method="post"><input type="hidden"  name="iuse" id="iuse" value="0" />
<table class="adminheading"><tr><td class="edit"><?
			$iway[0]->name='Комментарий - отзыв';
			$iway[0]->url="";
			$iway[1]->name= $row->id ? 'Редактирование' : 'Новый товар';
			$iway[1]->url="";

			i24pwprint_admin ($iway,0);
?></td></tr></table>
<table border="0" cellpadding="4" cellspacing="0" width="100%" align="center">
	<tr class="workspace">
		<td>Родитель: </td>
		<td><? print $comments->parent_component_name; ?>: <? print $comments->parent_obj->name; ?></td>
	</tr>
	<tr class="workspace">
		<td>Имя: </td>
		<td><? print $row->name; ?></td>
	</tr>
	<tr class="workspace">
		<td>Mail: </td>
		<td><? print $row->mail; ?></td>
	</tr>
	<tr class="workspace">
		<td>Текст: </td>
		<td><textarea cols="80" rows="8" name="text" ><? print $row->text; ?></textarea></td>
	</tr>
	<tr class="workspace">
		<td>Опубликованно: </td>
		<td><select name="publish">
			<option <? if (  $row->publish==1  ) print 'selected="selected"'; ?> value="1">да</option>
			<option <? if (  $row->publish==0  ) print 'selected="selected"'; ?> value="0">нет</option>
		</select></td>
	</tr>
	
	<tr class="workspace">
		<td>Время добавления: Г.М.Д Ч:М:С</td>
		<td><? $itime = getdate($row->time + $reg['iServerTimeOffset']); print $itime['year'].".".num::fillzerro($itime['mon'],2).".".num::fillzerro($itime['mday'],2)."&nbsp;".$itime['hours'].":".$itime['minutes'].":".$itime['seconds']; ?></td>
	</tr>
	<tr class="workspace">
		<td>IP: </td>
		<td><? print $row->ip; ?></td>
	</tr>

</table>
<input type="hidden" name="id" value="<? print $row->id; ?>" />
<input type="hidden" name="cid" value="<? print $row->cid; ?>" />
<input type="hidden" name="task" value="save"  />
<input type="hidden" name="ca" value="<?=$reg['ca'] ?>" />
<?
}

function savecontent_comment( $task ) {
	global $database, $my, $reg;
//	$date2time_arr = split("\.",safelySqlStr($_REQUEST['avtime']));
//	$date2time_arr2 = split(" ",$date2time_arr[2]);
//	$date2time_arr2[1] = split(":",$date2time_arr2[1]);
//	$timeresult = mktime ($date2time_arr2[1][0],$date2time_arr2[1][1],$date2time_arr2[1][2],$date2time_arr[1], $date2time_arr2[0], $date2time_arr[0]);

//	$i24r = new mosDBTable( "#__coo", "id", $database );
//	$i24r->id = safelySqlInt($_REQUEST['id']);
//	$i24r->cid = safelySqlInt($_REQUEST['cid']);
//	$i24r->avname = safelySqlStr($_REQUEST['avname']);
//    $i24r->avmail = safelySqlStr($_REQUEST['avmail']);
//	$i24r->avtext = safelySqlStr($_REQUEST['avtext']);
//	$i24r->avtime = safelySqlInt($timeresult);
//	$i24r->avip = safelySqlStr($_REQUEST['avip']);
/*	if (!$i24r->check()) {		echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";	} else $i24r->store(); */
	$row = ggo (ggri('id'), "#__comments");
	$comments = new comments($row->type, $reg['db'], $reg);
	$comments->init();
	$params = array(
					"id" =>   ggri('id'),
					"text" => $_POST['text'],
					"publish" => $_POST['publish']
	);
	$new_commid = $comments->edit($params);

	switch ( $task ) {
		case 'save':
		default:
			$msg = 'Комментарий сохранен';  mosRedirect( 'index2.php?ca=comment', $msg );  break;
	}
}

function removecontent_comment( $task ) {
	global $database, $my;
	foreach ($_REQUEST['cid'] as $dfgd){
		ggsqlq (  "DELETE FROM #__comments WHERE id=".safelySqlStr($dfgd)  );
	}
	$msg = 'Комментарий(и) удалены: ';
	mosRedirect( 'index2.php?ca=comment', $msg );
}
?>