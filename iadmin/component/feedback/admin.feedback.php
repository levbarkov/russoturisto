<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my, $task, $id, $reg;
//$task 			= strval( mosGetParam( $_REQUEST, 'task', '' ) );
$cid = josGetArrayInts( 'cid' );
switch ($task) {
	case 'remove':		removefeedback( 0, $option );
						break;
	case 'cfg':			cfg();
						break;
	case 'savecfg':		load_adminclass('config');	 $conf = new config($reg['db']);   $conf->save_config();	$adminlog = new adminlog(); $adminlog->logme('cfg', $reg['feedback_name'], "", "" );
						mosRedirect( 'index2.php?ca='.$reg['ca'].'&task=cfg', "Настройки сохранены" );
						break;
	case 'removecfg':	$adminlog = new adminlog(); $adminlog->logme('delcfg', $reg['feedback_name'], "", "" );
						load_adminclass('config'); $conf = new config($reg['db']); $conf->remove($_REQUEST['conf_values'], $_REQUEST['id']); 
						mosRedirect( 'index2.php?ca='.$reg['ca'].'&task=cfg', "Настройки удалены" );
						break;

	case 'edit':		editfeedback( 0, $option );
						break;
	case 'save':		savefotofeedback( 0, $option );
						break;
	case 'cancel':		mosRedirect( 'index2.php?ca=feedback&task=view', "" );
						break;
	case 'block':		changeExgoodBlock( $cid, 0, $option );
						break;
	case 'unblock':		changeExgoodBlock( $cid, 1, $option );
						break;
	default:			showfeedback( $option );
						break;
}
function cfg(){
	global $reg;
	?><form name="adminForm" action="index2.php" method="post"><input type="hidden"  name="iuse" id="iuse" value="0" />
	<? load_adminclass('config');	$conf = new config($reg['db']);
	$conf->show_config('feedback', "Настройки"); ?>
	<input type="hidden" name="task" value="savecfg"  />
	<input type="hidden" name="ca" value="<?=$reg['ca'] ?>" />
	<input type="submit" style="display:none;" /></form><?
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
	$query = "UPDATE #__feedback"
	. "\n SET publish = " . (int) $block
	. "\n WHERE ( $cids )"
	;
	//ggtr ( $query ); die();
	$database->setQuery( $query );
	if (!$database->query()) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}
	$limit 				= intval( getUserStateFromRequest( 'limit', $iConfig_list_limit ) );
	$limitstart 		= intval( getUserStateFromRequest( 'limitstart', 0 ) );
	mosRedirect( 'index2.php?ca='. $option.'&task=view&limit='.$_REQUEST['limit'].'&limitstart='.$_REQUEST['limitstart'] );
}

function showfeedback( $option ) {
	global $database, $my, $iConfig_list_limit;
	$limit 			= intval( getUserStateFromRequest( 'limit', $iConfig_list_limit ) ); 	$limit 		= safelySqlInt ($limit);
	$limitstart 	= intval( getUserStateFromRequest( 'limitstart', 0 ) );					$limitstart = safelySqlInt ($limitstart);
	
	$where = array();
	$smart_string = icsmart('icsmart_feedback_search');
	if (  $smart_string  ) {
		$where[] = "( 
			( LOWER( #__feedback.name ) LIKE '%" . $smart_string . "%' ) OR
			( LOWER( #__feedback.txt ) LIKE '%" . $smart_string . "%' )
		)";
	}

	
	$query = "SELECT COUNT(#__feedback.id) FROM #__feedback ". ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" );
	$database->setQuery( $query ); $total = $database->loadResult();
	$query2 = str_replace( "COUNT(#__feedback.id)", "*", $query )." ORDER BY #__feedback.ctime DESC ";
	$rows = ggsql($query2, $limitstart, $limit);
	require_once( site_path . '/iadmin/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );
		?><table class="adminheading"><tr><td width="100%">Управление отзывами</td></tr></table>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminlist">
		<tr>
			<th width="2%" class="title">#</th>
			<th width="3%" class="title"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count($rows); ?>);" /></th>
			<th class="title">ФИО</th>
			<th class="title">Отзыв</th>
			<th class="title" align="center" style="text-align:center">Опубликован</th>
		</tr>
		<?php
		$k = 0;
		$exsi = 0;
//		$rows = ggsql("SELECT * FROM #__feedback AS a WHERE a.excat_id=".$excatid." ORDER BY a.order ASC LIMIT $limitstart, $limit ; ");  ggtr ($database);

		
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row 	=& $rows[$i];			
			$task 	= $row->publish==0 ? 'unblock' : 'block';
			$alt 	= $row->publish==0 ? '<span style="color:#ff0000;">Блокирован</span>' : 'Разрешен';
			$alt2 	= $row->publish==0 ? 'Снять блокировку' : 'Блокировать';
			$link 	= 'index2.php?ca=feedback&amp;task=editA&amp;id='. $row->id. '&amp;hidemainmenu=1&amp;search='. $_REQUEST['search'].'&amp;filter_type='. $_REQUEST['filter_type'].'&amp;filter_logged='. $_REQUEST['filter_logged'];
			?><tr class="<?php echo "row$k"; ?>"><?
				?><td><?php echo $i+1+$pageNav->limitstart;?></td><?
				?><td><?php echo mosHTML::idBox( $i, $row->id ); ?></td><?
				?><td align="left"><a href="index2.php?ca=feedback&task=edit&hidemainmenu=1&id=<? print $row->id; ?>"><? echo ($row->name); ?></a></td><?
				?><td align="left"><? echo (  str::get_substr($row->txt,140)  ); ?></td><?
				?><td align="center"><a title="<? print $alt2; ?>" onclick="return listItemTask('cb<? print $exsi ?>','<? print $task; ?>')" href="javascript: void(0);"><?php echo $alt;?></a></td><?
			?></tr><?
			$k = 1 - $k;
			$exsi++;
		}
		?></table>
		<?php echo $pageNav->getListFooter(); ?>

		<input type="hidden" name="ca" value="<?php echo $option;?>" />
		<input type="hidden" name="id" value="<?php echo $excatid;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />	
		</form>
		<?php




}

function savefotofeedback( $task ) {
	global $database, $my;
	$ithisfoto = ggo ($_REQUEST['id'], "#__feedback");

	$i24r = new mosDBTable( "#__feedback", "id", $database );
	$feedid	= intval( getUserStateFromRequest(  'id', 0 ) );  $feedid = safelySqlInt ($feedid);
	
	$i24r->id = $feedid;
	$i24r->name = $_REQUEST['name'];
	$i24r->txt = $_REQUEST['txt'];

	if (!$i24r->check()) {
		echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";
	} else $i24r->store();
//	ggtr ($database, 20); die();
	$msg = 'Отзыв сохранен: ';
	mosRedirect( 'index2.php?ca=feedback&task=view', $msg );
}
function removefeedback( $task ) {
	global $database, $reg;
	foreach ($_REQUEST['cid'] as $dfgd){
		$dfgd = safelySqlInt($dfgd);
		$excatfoto = ggo ($dfgd, "#__feedback");  //ggtr ($excatfoto);
		$adminlog_obg = ggo($dfgd, "#__feedback");	$adminlog = new adminlog(); $adminlog->logme('del', $reg['feedback_name'], $adminlog_obg->name, $adminlog_obg->id );
		ggsqlq ("DELETE FROM #__feedback WHERE id=".$dfgd);	
	}
	$msg = 'Отзыв(ы) удалены:';
	mosRedirect( 'index2.php?ca=feedback&task=view', $msg );
}
function editfeedback( $task ) {
	global $database;
	
	$ithisfeed = ggo ($_REQUEST['id'], "#__feedback");
	?><form name="adminForm" enctype="multipart/form-data" action="index2.php" method="post">
	<table border="0" cellpadding="4" cellspacing="0" width="100%" align="center">
		<tr class="workspace">
			<td>Имя, Фамилия, Отчество: </td>
			<td><input name="name" value="<? print ($ithisfeed->name); ?>" type="text" size="119" /></td>
		</tr>
		
		<tr class="workspace">
			<td>Отзыв: </td>
			<td><textarea cols="85" rows="8" name="txt"><? print ($ithisfeed->txt); ?></textarea></td>
		</tr>

	</table>
	<input type="hidden" name="id" value="<? print $ithisfeed->id; ?>" />
	<input type="hidden" name="task" value="save"  />
	<input type="hidden" name="ca" value="feedback" /><?
}

?>