<?php
global $reg;
defined( '_VALID_INSITE' ) or die( 'Direct Access to this location is not allowed.' );

ilog::vlog($_SERVER);
do_nopage_stat();
$nopage = ggo (1, "#__nopage"); 
print desafelySqlStr($nopage->nopage);

/****************************ОТДЕЛ СТАТИСТИКИ****************************/
function do_nopage_stat(){
	global $reg;
	if (  ifipbaned()  ) return;
	
	$sitelog = new sitelog();
	$sitelog->f[0] = $reg['c'];
	if (  $sitelog->isnewlog()  ) $sitelog->desc = $reg['nopage_name'];
	$sitelog->savelog();
	
	// сохраняем дополнительную статистику для несуществующих страниц
	$url = safelySqlStr($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
	$url_reffer = safelySqlStr($_SERVER['HTTP_REFERER']);
	$sql_stat = "SELECT * FROM #__stat_nopage WHERE url = '".$url."' AND url_reffer = '".$url_reffer."'; ";
	$pio = ggsql (  $sql_stat  ); $pio = $pio[0];
	
	$i24r = new mosDBTable( "#__stat_nopage", "id", $reg['db'] );
	$i24r->ip = $_SERVER['REMOTE_ADDR']; $i24r->ctime = time();
	if (  isset($pio->id)  )  	$i24r->id = $pio->id;			else $i24r->id = 0;
	if (  isset($pio->cnt)  )  	$i24r->cnt = $pio->cnt+1;		else $i24r->cnt = 1;
	if (  !isset($pio->id)  )  	{ $i24r->url = $url; $i24r->url_reffer = $url_reffer; }
	if (!$i24r->check()) { echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n"; } else $i24r->store();
	//ggdd();
	return;
}