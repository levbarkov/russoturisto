<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my, $task, $id, $reg;
$cid = josGetArrayInts( 'cid' );
switch ($task) {
	case 'cancel':		$msg = 'Изменения не были сохранены: ';
						mosRedirect( 'index2.php?ca='.$reg['ca'].'&task=cfg', $msg );
						break;
	case 'save':		saveexcfg( $task );
						break;
	case 'removecfg':	$adminlog = new adminlog(); $adminlog->logme('delcfg', $reg['nopage_name'], "", "" );
						load_adminclass('config'); $conf = new config($reg['db']); $conf->remove($_REQUEST['conf_values'], $_REQUEST['id']); 
						mosRedirect( 'index2.php?ca='.$reg['ca'].'&task=cfg', "Настройки удалены" );
						break;
	case 'cfg':			
	default:			cfgexcfg( $option );
						break;
}

function cfgexcfg(){
global $reg;
$exgfg = ggo (1, "#__nopage");  // ggtr5 ($exgfg);
?><form <? ctrlEnterCtrlAS (' '.$reg['submit_save_event'], ' '.$reg['submit_save_event']) ?> name="adminForm" action="index2.php" method="post"><input type="hidden"  name="iuse" id="iuse" value="0" />
<table class="adminheading"><tr><td width="100%"><?
	$iway[0]->name=$reg['nopage_name'];
	$iway[0]->url="";
	i24pwprint_admin ($iway);
?></td></tr></table>
<table border="0" cellpadding="4" cellspacing="0" width="100%" align="center">
	<tr class="workspace">
		<td>Пояснительный текст: </td>
		<td ><? editorArea( 'editor1',  ($exgfg->nopage) , 'nopage', '100%;', '550', '75', '55' ) ; ?></td>
	</tr>
</table>
<? 
load_adminclass('config');	$conf = new config($reg['db']);
$conf->show_config('nopage', "<br />Дополнительные настройки") ?>
<input type="hidden" name="task" value="save"  />
<input type="hidden" name="ca" value="<?=$reg['ca'] ?>" /><?
}
function saveexcfg( $task ) {
	global $database, $my, $reg;
	$i24r = new mosDBTable( "#__nopage", "id", $database );
	$i24r->id = 1;
	$i24r->nopage = ggrr('nopage');
	
	if (!$i24r->check()) { echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n"; } else $i24r->store();
	load_adminclass('config');	 $conf = new config($reg['db']);   $conf->save_config(); 
	
	$adminlog = new adminlog(); $adminlog->logme('cfg', $reg['nopage_name'], "", "" );
	
	switch ( $task ) {
		case 'save':
		default:
			$msg = 'Изменения сохранены: ';  mosRedirect( 'index2.php?ca='.$reg['ca'].'&task=cfg', $msg ); break;
	}
}


?>