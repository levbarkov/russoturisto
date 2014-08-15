<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $reg;
$id 			= intval( mosGetParam( $_REQUEST, 'id', 0 ) );			safelySqlInt ($id);
$print_version 	= intval( mosGetParam( $_REQUEST, 'pop', 0 ) );			safelySqlInt ($print_version);
$limit 			= intval( mosGetParam( $_REQUEST, 'limit', 10 ) );		safelySqlInt ($limit);
$limitstart 	= get_insite_limit_start ( $limit );
$task 			= mosGetParam( $_REQUEST, 'task', "excat" );			safelySqlStr ($task);

switch ( $task ) {

	case 'view':
		ishowItem_pathway( $id, $gid, $print_version, $option );
		break;
	case 'thank':
		thank_pathway( $id );
		break;
	case 'excat':
		showexcat_pathway( $id, $gid, $print_version, $reg['pi'], $limit, $limitstart, $task );
		break;
	case 'excomp':
		showexcomp_pathway( $id );
		break;
	case 'viewtrush':
		showextrush_pathway( $id );
		break;


}
function ishowItem_pathway( $id, $gid, $print_version, $option ){
	global $reg;
	if (  $reg['mainobj']->parent>0  ) $icars = $reg['mainparent'];
	else{
		$icars->id = 0;
		$icars->name = $reg['ex_name'];
	}
	$icatway = get_pathway_array($icars, "#__excat", "parent", "/".$reg['ex_seoname'], $reg['ex_name'], 1);
	i24pwprint (  $icatway  );
}

function showexcat_pathway( $id=0, $gid, $print_version, $now=NULL, $limit, $limitstart, $task ) {
	global $mainframe, $reg;
	if (  $id>0  ) $icars = $reg['mainobj'];
	else{
		$icars->id = 0;
		$icars->name = $reg['ex_name'];
	}
	$icatway = get_pathway_array($icars, "#__excat", "parent", "/".$reg['ex_seoname'], $reg['ex_name'], 0);
	i24pwprint (  $icatway  ); $sefurl = $icatway[count($icatway)-1]->url.'/'; 
}

function showexcomp_pathway($id){
global $reg;

	if (  $id>0  ) $icars = $reg['mainobj'];
	else{
		$icars->id = 0;
		$icars->name = $reg['ex_name'];
	}
	$icatway = get_pathway_array($icars, "#__excat", "parent", "/".$reg['ex_seoname'], $reg['ex_name'], 1);
	$icatway = i24pathadd(  $icatway, "Избранное", ""  );
	i24pwprint (  $icatway  );
}
function thank_pathway($id){
	global $reg;
	if (  $id>0  ) $icars = $reg['mainobj'];
	else{
		$icars->id = 0;
		$icars->name = $reg['ex_name'];
	}
	$icatway = get_pathway_array($icars, "#__excat", "parent", "/".$reg['ex_seoname'], $reg['ex_name'], 1);
	$icatway = i24pathadd(  $icatway, "Заказ отправлен", ""  );
	i24pwprint (  $icatway  );
}
function showextrush_pathway($id){ // КОРЗИНА 456
	global $reg;	
	if (  $_REQUEST['mycart_task']=='submit_order'  ) return;
	
	if (  $id>0  ) $icars = $reg['mainobj'];
	else{
		$icars->id = 0;
		$icars->name = $reg['ex_name'];
	}	
	$icatway = get_pathway_array($icars, "#__excat", "parent", "/".$reg['ex_seoname'], $reg['ex_name'], 1);
	if (  $_REQUEST['mycart_task']=='order'  ) $icatway = i24pathadd(  $icatway, "Оформление заказа", ""  );
	else $icatway = i24pathadd(  $icatway, "Корзина заказов", ""  );
	i24pwprint (  $icatway  );
}