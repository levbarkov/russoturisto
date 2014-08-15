<?
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $reg;

//ggtr (dirname(__FILE__));
//$safsd = dirname(__FILE__);
//$txt = "<span>delete</span>";
//$txt = "ggsdeletegss";

//preg_match("/(<span>)!(delete)(<\/span>)!/i",$txt, $m);
//ggd($m);
//die();

//$safsd = "dlkjfslvjklcxjkvjxklcjv/dfgdgdf-dfgdfg_sdfsfs";
preg_match("/([\w-]+)$/",dirname(__FILE__), $matches);
//ggd ($matches);

if (  $sefname1!=$matches[1]  )	return;
$_REQUEST['c']=$matches[1]; $seoresult=true; rewrite_option();
?>