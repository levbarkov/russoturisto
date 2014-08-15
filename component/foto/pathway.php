<?
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $reg;
// id - идентификатор категории
$fotocatid = isset($_REQUEST['id'])?$_REQUEST['id']:0;
if (  $fotocatid>0  ) $thisfotocat = ggo($fotocatid, "#__exfoto");
else{
	$thisfotocat->id = 0;
	$thisfotocat->name = $reg['exfoto_name'];
}

// выводим путь навигации
$icatway = get_pathway_array($thisfotocat, "#__exfoto", "parent", "/foto", $reg['exfoto_name'], 0);
i24pwprint (  $icatway  );
