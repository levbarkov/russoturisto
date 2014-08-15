<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my;
ilogoutUser( $my->id );
mosRedirect( 'index.php', "" );

?>