<?php

/**
 *
 * Class ilog
 *
 * Вывод и запись служебной информации в текстовый файл
 *
 */
class ilog {
	function get_time(){
		global $reg;
		return strftime( '%Y-%m-%d %H:%M:%S', time() );
	}
	
	function get_ip(){
		return $_SERVER['REMOTE_ADDR'];
	}

        /**
         * ВЫВОД ОТЛАДОЧНОЙ ИНФОРМАЦИИ, ЕСЛИ УСТАНОВЛЕН ФЛАГ show_debug_info (конфигурация сайта)
         *
         * @param <obj> $var
         * @param <string> $txt
         */
        function ggr(&$var, $txt=''){
               global $reg;

               if (  $reg['show_debug_info']  ){
                    if (  $txt!=''  ) print '<br>'.$txt.'=';
                    $var_type = gettype($var);
                    if (  $var_type=='array'  or  $var_type=='object'   ){
                        ggr ( $var );
                    } else print $var;

               }

        }

        /**
         * СОХРАНИТЬ В ФАЙЛЕ СОДЕРЖИМОЕ ОБЪЕКТА
         *
         * @param <obj> $GGobj
         */
	public static function vlog($obj){
            global $reg;
                if (  !$reg['oper_log']  ) return;
		$file = site_path.'/'.$reg['file_log'];
		/*if (  !file_exists($file)  )  	die('Удален файл статистики операций ("'.$fname.'"). Продолжение не возможно');
		if (  !is_file($file)  )  	die('Удален файл статистики операций ("'.$fname.'"). Продолжение не возможно');
		if (  !is_writable($file)  )  	die('Нет прав на запись у файла статистики операций ("'.$fname.'"). Продолжение не возможно');*/
		 
		$fh = fopen($file, "a");
		//fwrite(  $fh, ilog::get_time()."    ".ilog::get_ip().chr(13).chr(10)  );
                $main_res = print_r ($obj, true);
                //fwrite(  $fh, $main_res.chr(13).chr(10)  );
                fwrite(  $fh, "<pre style='text-align=left'>".$main_res."</pre>".chr(13).chr(10)  );
		fclose($fh);
	}

        /**
         * Вывести содержимое объекта в качестве комментария
         *
         * @param <obj> $GGobj
         */
	public static function commentlog($obj){
            global $reg;
            if (  isset($_REQUEST['who'])  ){
                print '<!-- ';
                print_r ($obj);
                print ' -->';
            }
	}
}
?>
