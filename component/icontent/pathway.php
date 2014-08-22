<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $reg, $my;

$id			= intval( mosGetParam( $_REQUEST, 'id', 0 ) );

$task 	= mosGetParam( $_REQUEST, 'task', 0 );
$id 	= intval( mosGetParam( $_REQUEST, 'id', 0 ) );

switch ( $task ) {
	case 'view':
		ishowItem_pathway( $id, $gid, $pop, $option );
		break;
	case 'icat':
		showicat_pathway( $id, $gid, $pop, $Itemid, $limit, $limitstart );
		break;
}
function ishowItem_pathway( $id, $gid, $pop, $option ){
	global $reg;
	$row = ggo ($id, "#__content");
	$icars = ggo ($row->catid, "#__icat");
	
	$i=1;
	if( substr($reg['sefname1'],0,6)=='tours_' ){ $i=2; }
	if( $icars->name == 'подкатегория' ){ $icars->name = ''; }

	// выводим путь навигации
	$icatway = get_pathway_array($icars, "#__icat", "parent", "", "" , $i);
	i24pwprint (  $icatway  );
}

function showicat_pathway( $id=0, $gid, $pop, $now=NULL, $limit, $limitstart ) {
	global $mainframe, $Itemid, $reg;
	$icars = ggo ($id, "#__icat");

	// выводим путь навигации
	$icatway = get_pathway_array($icars, "#__icat", "parent", "", "" , 0);
	i24pwprint (  $icatway  );
}