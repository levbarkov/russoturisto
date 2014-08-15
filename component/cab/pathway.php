<?
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my, $task, $id;
$task 			= strval( mosGetParam( $_REQUEST, 'task', '' ) );
$cid = josGetArrayInts( 'cid' );
//ggtr ($_REQUEST); ggtr ($task); die();
switch ($task) {
	default:			
	case 'edit':		edituser_pathway( $id, $option );
						break;
}

function edituser_pathway(){
	global $database, $my, $acl, $mainframe, $reg;
	$iway[0]->name="Личный кабинет";
	$iway[0]->url="/cab";
	i24pwprint ($iway);
}