<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

$id 			= intval( mosGetParam( $_REQUEST, 'id', 0 ) );			safelySqlInt ($id);
$print_version 	= intval( mosGetParam( $_REQUEST, 'pop', 0 ) );			safelySqlInt ($print_version);
$limit 			= intval( mosGetParam( $_REQUEST, 'limit', 10 ) );		safelySqlInt ($limit);
$limitstart 	= get_insite_limit_start ( $limit );
$task 			= mosGetParam( $_REQUEST, 'task', "adcat" );			safelySqlStr ($task);
if (  $task==''  ) $task = "adcat";

switch ( $task ) {

	case 'view':
		ishowItem_pathway( $id, $gid, $print_version, $option );
		break;
	case 'new':
		editad_pathway( 0 );
		break;
	case 'adcat':
		showadcat_pathway( $id, $gid, $print_version, $Itemid, $limit, $limitstart, $task );
		break;
	case 'excomp':
		showexcomp_pathway( $id );
		break;


}
function editad_pathway( $uid ){
	global $reg;

	if (  ggri('id')>0  ) $icars = ggo (ggri('id'), "#__adgood");
	else {
		$icars->id = 0;
		$icars->name = "";
	}
	// ОПРЕДЕЛЯЕМ ID ПАПЫ

	$iway[0]->name=$reg['ad_name'];
	$iway[0]->url="/".$reg['ad_name']."/$icars->sefnamefull/$icars->sefname";
	$iway[1]->name= $icars->id ? 'Редактирование' : 'Новое объявление';
	$iway[1]->url="";
	i24pwprint ($iway);
}
function ishowItem_pathway( $id, $gid, $print_version, $option ){
global $database, $reg;
	$row = ggo ($id, "#__adgood");
	if (  $row->parent>0  ) $icars = ggo ($row->parent, "#__adcat");
	else{	$icars->id = 0;
			$icars->name = $reg['ad_name']; 
	}
	$icatway = get_pathway_array($icars, "#__adcat", "parent", "/".$reg['ad_seoname'], $reg['ad_name'], 1);
	i24pwprint (  $icatway  );
}

function showadcat_pathway( $id=0, $gid, $print_version, $now=NULL, $limit, $limitstart, $task ) {
	global $database, $mainframe, $Itemid, $reg;
	
	// ОПРЕДЕЛЯЕМ ID ПАПЫ
	$papa = ggsql ("select * from #__adcat WHERE id='".ggri('id')."'  ");
	if (  count($papa)>0  ) {
		$id = $papa[0]->id;
		$icars = $papa[0];
	} else {
		$id=0;
		$icars->id = 0;
		$icars->name = $reg['ad_name'];
	}
	$icatway = get_pathway_array($icars, "#__adcat", "parent", "/".$reg['ad_seoname'], $reg['ad_name'], 0);
	i24pwprint (  $icatway  );
}
function showexcomp_pathway($id){
	global $reg;
	
	if (  $id>0  ) $icars = ggo ($id, "#__adcat");
	else{
		$icars->id = 0;
		$icars->name = $reg['ad_name'];
	}
	$icatway = get_pathway_array($icars, "#__adcat", "parent", "/".$reg['ad_seoname'], $reg['ad_name'], 1);
	$icatway = i24pathadd(  $icatway, "Список сравнения", ""  );	
	i24pwprint (  $icatway  );
}