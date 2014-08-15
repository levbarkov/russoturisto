<?php

/**
 *
 * adminlog
 * <log><act>http://insite.dev/catalogue/auto</act><u>Admin</u><mod>2010-05-01</mod></log>
 *
 */
class iflush {

    public static function init(){
        global $reg;
        $flush = new stdClass();
        $flush->cnt = 0;
        $reg['flush']=$flush;
    }

    /**
     * ОТОБРАЗИТЬ ТЕКЩЕЕ СОСТОЯНИЕ В БРАУЗЕРЕ ДЛЯ УСКОРЕНИЯ ОТКРЫТИЯ СТРАНИЦЫ
     * @global <type> $reg
     * @param <type> $icnt
     */
    public static function flush ($icnt){
        global $reg;

        if ($icnt==0) { flush (); return; }
        
        $reg['flush']->cnt++;
        if (  $reg['flush']->cnt>=$icnt  ){
            flush ();
            $reg['flush']->cnt=0;
        }

    }

}
?>
