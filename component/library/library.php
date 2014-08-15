<?php
defined( '_VALID_INSITE' ) or die( 'Direct Access to this location is not allowed.' );
if (  isset($_REQUEST['file'])  ){
	require_once(  site_path.'/component/library/'.$_REQUEST['file'].'.php'  );
}
 