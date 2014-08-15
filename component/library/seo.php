<?
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $reg;
if (  $sefname1!='library'  )	return;

$tsefname = $sefname;
preg_match("/^.*[\/](.*)\.html$/",$sefname, $matches);
if (  $matches[1]!=''  ){	// необходимо вывести содержимое статьи
	if (  $matches[1]!='' ){
		$_REQUEST['c']='library';	$_REQUEST['file']=$matches[1];
		rewrite_option();			$seoresult=true;	return;
	}
}
$_REQUEST['c']='library'; $seoresult=true; rewrite_option();
?>