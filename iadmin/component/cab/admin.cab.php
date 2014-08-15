<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my, $task, $id, $reg;
require_once( site_path.'/component/ad/ad_lib.php' );
$cid = josGetArrayInts( 'cid' );
switch ($task) {
	case 'save':
	case 'savecfg':		load_adminclass('config');	 $conf = new config($reg['db']);   $conf->save_config();	$adminlog = new adminlog(); $adminlog->logme('cfg', $reg['cab_name'], "", "" );
						mosRedirect( 'index2.php?ca='.$reg['ca'].'&task=cfg', "Настройки сохранены" );
						break;
	case 'removecfg':	$adminlog = new adminlog(); $adminlog->logme('delcfg', $reg['cab_name'], "", "" );
						load_adminclass('config'); $conf = new config($reg['db']); $conf->remove($_REQUEST['conf_values'], $_REQUEST['id']); 
						mosRedirect( 'index2.php?ca='.$reg['ca'].'&task=cfg', "Настройки удалены" );
						break;
	case 'cfg':			
	default:			cfgcabcfg( $option );
						break;
}
function cfgcabcfg(){
global $option, $reg;
$exgfg = ggo (1, "#__excfg");
?>
<form name="adminForm" action="index2.php" method="post"><input type="hidden"  name="iuse" id="iuse" value="0" />
<table class="adminheading"><tr><td width="100%"><?
	$iway[0]->name=$reg['cab_name'];
	$iway[0]->url="index2.php?ca=cab";
	$iway[1]->name="настройка";
	$iway[1]->url="";

	i24pwprint_admin ($iway);
	?></td></tr></table>
			
<? load_adminclass('config');	$conf = new config($reg['db']);
$conf->show_config('usercab', "") ?>
<input type="hidden" name="task" value="savecfg"  />
<input type="hidden" name="ca" value="<?=$reg['ca'] ?>" />
<input type="submit" style="display:none;" /></form><?
}


?>