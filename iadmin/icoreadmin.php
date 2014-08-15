<?
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
function iMainBody_Admin() {
global  $option;
	// загрузка главного модуля для админки
	$icom_file = site_path."/iadmin/component/$option/admin.$option.php";
	require_once( $icom_file );	
}
?>