<?php
define( "_VALID_INSITE", 1 );
setlocale(LC_ALL, 'ru_RU.UTF-8');

require_once( 'iconfig.php' );
require_once( 'i24.php' );
require_once( 'idb.php' );

$database = new database( $DBhostname, $DBuserName, $DBpassword, $DBname, $DBPrefix );

ggd(fuck);
        $i24r = new mosDBTable( "#__orders", "id", $database );
        $i24r->id = 'd';
        $i24r->code = $code;
        //$i24r->note = '';
        //$i24r->clientAddress = '';
        if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();

        //print " ";
?>