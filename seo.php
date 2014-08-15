<?
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $sefname1, $reg;
$sefname = ggrr('sefname');
// ХАК ОТ Димы учитывающий случай с VGORODE24-ASSIST
if($sefname == '' && $_GET['sefname'] != '' && $_REQUEST['c'] == '' && $_GET['c'] = "seo") $sefname = ggsss($_GET['sefname']);
//$sefname = "/about/sdfsd/sdf/sd/fs/df/s/d=sdfdsgfdfgd.hmytyt?345345=dfgd&dsfds=34";

//необходимо определить первый раздел
preg_match("#^/?([^/]+)#",$sefname, $matches);
//	ggtr3 ($matches);
$sefname1 = $matches[1];
$reg['sefname1'] = $sefname1;
//поиск в базе и определение компонента

	//сканирование директорий
	$dir = site_path."/component"; $seoresult = false;
	foreach (glob("$dir/*") as $path) {
		if(  is_dir($path)  ){
			if (  file_exists($path."/seo.php")  ){
				require_once( $path."/seo.php" );
				if (  $seoresult==true  ) break;
			}
		}
	}
	
	if($seoresult==false) {
		if($sefname1) header('HTTP/1.0 404 Not Found');
	}

?>