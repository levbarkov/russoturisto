<?php
// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

global $reg;

if ($path = igetPath( 'toolbar' )) {
        $reg['toolbar_footer']=1;
	include( $path );
}

?>