<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

require_once( site_path .'/iadmin/includes/menubar.html.php' );

if ($path = igetPath( 'toolbar' )) {
	include_once( $path );
}

?>