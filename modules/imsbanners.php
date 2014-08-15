<?php
defined( '_VALID_INSITE' ) or die( 'Доступ ограничен' );
$clientids = $params->get( 'banner_cids', '' );
$banner = null; $bpos=$p2;
$where = '';
if( $clientids != '' ) {
	$clientidsArray = explode( ',', $clientids );
	mosArrayToInts( $clientidsArray );
	$where = "\n AND ( cid=" . implode( " OR cid=", $clientidsArray ) . " )";
}
$query = "SELECT *"
. "\n FROM #__banner"
. "\n WHERE showBanner = 1 AND custombannercode=".$bpos
; 
global $database;
$database->setQuery( $query );
$banners = $database->loadObjectList(); 
//ggtr($banners);
$numrows = count( $banners );
$bannum = 0; 
//ggtr($banners);
if ($numrows > 1) {
    $numrows--;
	mt_srand( (double) microtime()*1000000 );
	$bannum = mt_rand( 0, $numrows );
}
if($numrows){ 
	$banner = $banners[0];
    	$query = "UPDATE #__banner"
    	. "\n SET impmade = impmade + 1"
	. "\n WHERE bid = " . (int) $banner->bid
	;
	$database->setQuery( $query );
	if(!$database->query()) {
		echo $database->stderr( true );
		return;
	}
	$banner->impmade++;
	
	if ($numrows > 0) {
		// Проверка, было ли последнее нажатие одиночным и выводится баннер 
		if ($banner->imptotal == $banner->impmade) {
			$query = "INSERT INTO #__bannerfinish ( cid, type, name, impressions, clicks, imageurl, datestart, dateend )"
			. "\n VALUES ( " . (int) $banner->cid . ", " . $database->Quote( $banner->type ) . ", "
			. $database->Quote( $banner->name ) . ", " . (int) $banner->impmade . ", " . (int) $banner->clicks
			. ", " . $database->Quote( $banner->imageurl ) . ", " . $database->Quote( $banner->date ) . ", 'now()' )"
			;
			$database->setQuery($query);
			if(!$database->query()) {
				die($database->stderr(true));
			}
			$query = "DELETE FROM #__banner"
			. "\n WHERE bid = " . (int) $banner->bid
			;
			$database->setQuery($query);
			if(!$database->query()) {
				die($database->stderr(true));
			}
		}
		/*if (trim( $banner->custombannercode )) {
			echo $banner->custombannercode;
		} else*/ 
        if($bpos!=1){
        echo "<br /><center>";
        }
        if (eregi( "(\.bmp|\.gif|\.jpg|\.jpeg|\.png)$", $banner->imageurl )) {
			$imageurl 	= $mosConfig_live_site .'/images/stories/'. $banner->imageurl;
	
			$link	= $banner->clickurl;//	= sefRelToAbs('index.php?c=banners&amp;task=click&amp;bid='. $banner->bid );
			if( !defined('_BANNER_ALT') ) DEFINE('_BANNER_ALT','Реклама');
			echo "<a href=\"index.php?c=banners&task=flash&bid=$banner->bid\" target=\"_blank\">
            <img src='".$imageurl."' border=\"1\" alt=\"Реклама\" style='border:1px solid; border-color:#000000' />
            </a>";
		} else if (eregi("\.swf$", $banner->imageurl)) {
			$imageurl 	= "$mosConfig_live_site/images/stories/".$banner->imageurl;
			$redirect='index.php?c=banners&amp;task=flash&amp;www='.$banner->clickurl.'&amp;bid='.$banner->bid;
			echo "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0\" width=\"$banner->width\" height=\"$banner->height\" >
					<param name=\"movie\" value=\"$imageurl\" ><param name=\"loop\" value=\"true\">
					<PARAM NAME=FlashVars VALUE='redirect_url=index.php?c=banners%26task=flash%26bid=$banner->bid'>

					<embed src=\"$imageurl\"  width=\"$banner->width\" height=\"$banner->height\" loop=\"true\" FlashVars='redirect_url=index.php?c=banners%26task=flash%26bid=$banner->bid' pluginspage=\"http://www.macromedia.com/go/get/flashplayer\" type=\"application/x-shockwave-flash\"></embed></object>";
		}
        if($bpos!=1){
            echo "</center>
            <br />";
        }       
	
	}
} else {
	echo "&nbsp;";
}
?>