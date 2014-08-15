<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
$task 			= mosGetParam( $_REQUEST, 'task', "view" );				safelySqlStr ($task);
switch ( $task ) {
	default:
		show_backs_pathway(  );
		break;
}
function show_backs_pathway(  ) {
	global $reg;
	$iway[0]->name=$reg['feedback_name'];
	$iway[0]->url="";
	i24pwprint ($iway);
}