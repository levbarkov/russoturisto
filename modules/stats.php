<?php
// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ ограничен' );

global $mosConfig_caching, $mosConfig_enable_stats;
global $mosConfig_gzip, $reg;

$serverinfo = 1;
$siteinfo 	= 1;

$content = '';
?><span  style='color:#FFFFFF'><?
if ($serverinfo) {
	echo "<strong>ОС:</strong>&nbsp;"  . substr(php_uname(),0,7) . "<br />\n";
	echo "<strong>"._TIME_STAT.":&nbsp;</strong>&nbsp;" .date("H:i",time()+$reg['iServerTimeOffset']) . "<br />\n";
}

if ($siteinfo) {
	$query="SELECT COUNT( id ) AS count_users"
	. "\n FROM #__users"
	;
	global $database;
	$database->setQuery($query);
	echo "<strong>"._MEMBERS_STAT.":</strong>&nbsp;" .$database->loadResult() . "<br />\n";

	$query="SELECT COUNT( id ) AS count_items"
	. "\n FROM #__content"
	;
	$database->setQuery($query);
	echo "<strong>"._NEWS_STAT.":</strong>&nbsp;".$database->loadResult() . "<br />\n";

}


$session_id = stripslashes( mosGetParam( $_SESSION, 'session_id', '' ) );

// Get no. of users online not including current session
$query = "SELECT COUNT( session_id )"
. "\n FROM #__session"
. "\n WHERE session_id != " . $database->Quote( $session_id )
;
$database->setQuery($query);
$online_num = intval( $database->loadResult() );
echo "<strong>На&nbsp;сайте:&nbsp;</strong> ".$online_num . "<br />\n";
?></span>