<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

/*
 *
 * учет статистики сайта
 *
 */ 
 
class site_statistics{ 
	var $i24http_ref;
	var $i24http_refa;
        /** кодировка для сохранения результата: 0 - utf,                                 // 1-windows-1251 */
        var $resultEncoding=0;
	
	function __construct ($id=0){
                //$_SERVER['HTTP_REFERER'] = "http://www.google.com/search?sourceid=navclient&ie=UTF-8&rls=ITVA,ITVA:2006-43,ITVA:en&q=%E1%E5%F1%EF%EB%E0%F2%ED%EE%E5";
                //$_SERVER['HTTP_REFERER'] = "http://www.google.ru/search?q=%D0%BB%D0%B5%D1%87%D0%B5%D0%BD%D0%B8%D0%B5+%D0%B0%D0%BB%D0%BA%D0%BE%D0%B3%D0%BE%D0%BB%D0%B8%D0%B7%D0%BC%D0%B0+%D0%BD%D0%BE%D0%B2%D0%BE%D1%81%D0%B8%D0%B1%D0%B8%D1%80%D1%81%D0%BA&hl=ru&newwindow=1&rlz=1G1GGLQ_RURU392&ei=MI21TK_JO4zqOfbS_OoJ&start=30&sa=N";
		$this->i24http_ref  = isset($_SERVER['HTTP_REFERER']) ? safelySqlStr($_SERVER['HTTP_REFERER']) : '';
		$this->i24http_refa = url_parse ($this->i24http_ref);// ggd ($i24http_refa);
	}

        function get_ip_addr_url($ip){
            return 'http://whatismyipaddress.com/ip/'.$ip;
        }

        function result_converting($txt){
            if (  $this->resultEncoding==0  ) return $txt;
            else return converting::utf82win1251($txt);
            //else return iconv('cp1251', 'utf8', $txt);
            // если iconv не работает используйте всегдаработающуюсамописную функцию utf82win1251,
        }
	
	function SearchEngineStatisticSave(){
		global $database, $reg;

		$i24http_refp = array();
		/*utf82win1251*/
                     if (  preg_match("/yandex\.[a-zA-Z\.]+\/(?:yandsearch|search|msearch|sitesearch)/",                $this->i24http_ref)  ){	$i24http_refp['sbot'] = "yandex"; 		$i24http_refp['text'] = $this->result_converting($this->i24http_refa['text']); 	}
		else if (  preg_match("/images\.yandex\./",                                                             $this->i24http_ref)  ){ $i24http_refp['sbot'] = "yandex(картинки)";     $i24http_refp['text'] = $this->result_converting($this->i24http_refa['text']); 	}
		
		else if (  preg_match("/(?:google|gogle)\.[a-zA-Z\.]+\/(?:search|xhtml)/",                              $this->i24http_ref)  ){ $i24http_refp['sbot'] = "google"; 	$i24http_refp['text'] = $this->result_converting($this->i24http_refa['q']);	}
		
		else if (  preg_match("/rambler\.[a-zA-Z\.]+\/(?:search|srch)/",                                        $this->i24http_ref)  ){ $i24http_refp['sbot'] = "rambler"; 		$i24http_refp['text'] = $this->result_converting($this->i24http_refa['query']); }
		else if (  !(strpos($this->i24http_ref, "search.rambler.ru/cgi-bin/rambler_search") === false)  ){                              $i24http_refp['sbot'] = "rambler";              $i24http_refp['text'] = $this->result_converting($this->i24http_refa['words']); }
		
		else if (  preg_match("/yahoo\.[a-zA-Z\.]+\/search/",                                                   $this->i24http_ref)  ){ $i24http_refp['sbot'] = "yahoo.ru"; 		$i24http_refp['text'] = $this->result_converting($this->i24http_refa['p']); 	}
		/*utf82win1251*/
		
		else if (  !(strpos($this->i24http_ref, "sm.aport.ru/") === false)  ){                                                          $i24http_refp['sbot'] = "aport"; 		$i24http_refp['text'] = ($this->i24http_refa['r']); 	}
		else if (  preg_match("/go\.mail\.[a-zA-Z\.]+\/search/",                                                $this->i24http_ref)  ){	$i24http_refp['sbot'] = "mail"; 		$i24http_refp['text'] = ($this->i24http_refa['q']); 	}
		else if (  !(strpos($this->i24http_ref, "nigma.ru") === false)  ){                                                              $i24http_refp['sbot'] = "nigma"; 		$i24http_refp['text'] = ($this->i24http_refa['s']); 	}
		else if (  preg_match("/search\.qip\.[a-zA-Z\.]+\/search/",                                             $this->i24http_ref)  ){	$i24http_refp['sbot'] = "qip"; 			$i24http_refp['text'] = ($this->i24http_refa['query']); }

                if (isset($i24http_refp['text']) && !preg_match('//u', $i24http_refp['text'])) { // не UTF-8 => конвертируем в UTF-8
                    //по умолчанию считаем, что новая кодировка - win1251
                    if (  $this->resultEncoding==0  )   // т.е. мы храним данные в utf8 и нам надо конверитировать
                        $i24http_refp['text'] = ' '.@iconv("windows-1251", "UTF-8", $i24http_refp['text'] );
                }
		//ggd($i24http_refp);
		if (  isset($i24http_refp['sbot'])  ){
			$query = "SELECT cnt FROM #__stat_sbot WHERE LOWER( text ) = ".$database->Quote($i24http_refp['text'])." AND sbot = ".$database->Quote($i24http_refp['sbot'])." ;";
			$database->setQuery( $query ); $hits = intval( $database->loadResult() );
			if ( $hits ) {
				$query = "UPDATE #__stat_sbot SET cnt = ( cnt + 1 ), url='".$this->i24http_ref."', ctime='".( time() )."', ip='".$_SERVER['REMOTE_ADDR']."' "
				. "\n WHERE LOWER( text ) = ".$database->Quote($i24http_refp['text'])." AND sbot = ".$database->Quote($i24http_refp['sbot'])." ;";
				$database->setQuery( $query ); $database->query();
			} else {
				$query = "INSERT INTO #__stat_sbot VALUES ( " . $database->Quote( $i24http_refp['sbot'] ) . ", " . $database->Quote( $i24http_refp['text'] ) . ", '".$this->i24http_ref."', ".( time() ).", 1, '".$_SERVER['REMOTE_ADDR']."' )";
				$database->setQuery( $query ); $database->query();
			}
		} else if (!empty($_SERVER['HTTP_REFERER'])) { // учет статистики переходов с сайтов
		//	$i24http_ref = "http://cms.krasinsite.ru/index.php?c=showscont&task=view&id=";
			preg_match("/^[\w]+:\/\/([-\w\.]+)/",$this->i24http_ref, $matches); $isbot_sitename = $matches[1];
			if (  substr($isbot_sitename,0,3)=='www' ) $isbot_sitename = substr($isbot_sitename,4,strlen($isbot_sitename) );
			$query = "SELECT cnt FROM #__stat_sbot_site WHERE LOWER( site ) = '".strtolower($isbot_sitename)."' ;";
			$database->setQuery( $query ); $hits = intval( $database->loadResult() );
			if ( $hits ) {
				$query = "UPDATE #__stat_sbot_site SET cnt = ( cnt + 1 ), url='".$this->i24http_ref."', ctime='".( time() )."', ip='".$_SERVER['REMOTE_ADDR']."' "
				. "\n WHERE LOWER( site ) = '".strtolower($isbot_sitename)."' ;";
				$database->setQuery( $query ); $database->query();
			} else {
				$query = "INSERT INTO #__stat_sbot_site VALUES ( '".strtolower($isbot_sitename)."', '".$this->i24http_ref."', ".( time() ).", 1, '".$_SERVER['REMOTE_ADDR']."' )";
				$database->setQuery( $query ); $database->query();
			}
		}
	}
	
	
	
	// учет статистики по дням
	function DayStatisticSave(){
		global $database, $reg;
	
		if (  !ifipbaned()  ){
			$sbotcdate = getdate( time() );  $ssbotcdate = $sbotcdate['year'].".".num::fillzerro($sbotcdate['mon'],2).".".num::fillzerro($sbotcdate['mday'],2);
			$stat_sbot_days = ggo ($ssbotcdate, "#__stat_sbot_days", "cdate");
			$i24r = new mosDBTable( "#__stat_sbot_days", "id", $database );
			if (  isset($stat_sbot_days->id)  )  	$i24r->id = $stat_sbot_days->id;			else $i24r->id = 0;
			if (  isset($stat_sbot_days->cnt)  )  	$i24r->cnt = $stat_sbot_days->cnt+1;		else $i24r->cnt = 1;
			$i24r->cdate = $ssbotcdate;
			$i24r->last = time();
			if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
		}
	}

        /**
         * Записывает в файл $report_file IP и время посещения сайта
         *
         * @param <string> $report_file
         * @param <array> $ip_ban
         */
        function ggstat($report_file, $ip_ban){
                foreach ($ip_ban as $iprow){
                        if (  strcmp($_SERVER['REMOTE_ADDR'], $iprow)==0  ) return;
                }
                $counter_file = site_path."/".$report_file;
                clearstatcache();
                $fh = fopen($counter_file, 'a+');
                $e24today = getdate();
                $counter_file = $e24today['year'].".".num::fillzerro($e24today['mon'],2).".".num::fillzerro($e24today['mday'],2)." ".num::fillzerro($e24today['hours'],2).":".num::fillzerro($e24today['minutes'],2).":".num::fillzerro($e24today['seconds'],2)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a target='_blank' href='http://wservice.info/?target=".$_SERVER['REMOTE_ADDR']."&queryType=lookup'>".$_SERVER['REMOTE_ADDR']."</a>\n<br />";
                fwrite($fh, $counter_file);
                fclose($fh);
        }
	
}

?>