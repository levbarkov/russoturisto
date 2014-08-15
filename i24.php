<?php
/*
 *
 * ОСНОВНЫЕ ФУНКЦИИ СИСТЕМЫ
 *
 */

/*
 * функции работы с числами - lib/num.php
 * функции работы со строками - lib/str.php
 * основные названия - lib/ru.php
 *
 */

defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

/** АВТОМАТИЧЕСКОЕ Подключение классов */
function __autoload($class_name) {
    $names = array($class_name, strtolower($class_name));

    foreach ($names as $name) {
        $file = site_path . DIRSEP . 'lib' . DIRSEP . $name . '.php';
        if (file_exists($file) !== false)
            return require_once $file;

        $file = site_path . DIRSEP . 'component' . DIRSEP . $name . DIRSEP . $name . '.class.php';
        if (file_exists($file) !== false)
            return require_once $file;

        $file = site_path . DIRSEP . 'iadmin' . DIRSEP . 'component' . DIRSEP . $name . DIRSEP . $name . '.class.php';
        if (file_exists($file) !== false)
            return require_once $file;
    }

    return false;
}
/*######################################################################*/
/*######################################################################*/
/*##################  раздел защиты базы данных   ######################*/
/*######################################################################*/
/*######################################################################*/

        function safelySqlInt(&$i){ settype($i, 'integer'); return $i; }
        function safelySqlFloat(&$i){ settype($i, 'float'); return $i; }
        function safelySqlStr($i){ // ggtr01 ($i); // settype($i, 'string');
        /*	$i = str_ireplace("UNION", 	"<span>UNION</span>", $i);
                $i = str_ireplace("UPDATE", 	"<span>UPDATE</span>", $i);
                $i = str_ireplace("INSERT", 	"<span>INSERT</span>", $i);
                $i = str_ireplace("TRUNCATE", 	"<span>TRUNCATE</span>", $i);
                $i = str_ireplace("DROP", 		"<span>DROP</span>", $i);
                $i = str_ireplace("DELETE", 	"<span>DELETE</span>", $i);*/
                //$i = str_ireplace(";", 	"&#059;", $i);// &
                $i = str_ireplace("'", 	"&#039;", $i);//'
                $i = str_ireplace('"', 	"&quot;", $i);//" or &#034;
                $i = str_replace('\\', 	"&#092;", $i);
                return $i;
        }
        function desafelySqlStr($i){ // ggtr01 ($i); // settype($i, 'string');
        /*	$i = str_ireplace("<span>UNION</span>", 	"UNION", $i);
                $i = str_ireplace("<span>UPDATE</span>", 	"UPDATE", $i);
                $i = str_ireplace("<span>INSERT</span>", 	"INSERT", $i);
                $i = str_ireplace("<span>TRUNCATE</span>", 	"TRUNCATE", $i);
                $i = str_ireplace("<span>DROP</span>", 		"DROP", $i);
                $i = str_ireplace("<span>DELETE</span>", 	"DELETE", $i);*/
        //	$i = str_replace('&#059;', 	";", $i);//'
                $i = str_replace('&#039;', 	"'", $i);//'
                $i = str_replace('&quot;', 	'"', $i);//" or &#034;
                $i = str_replace("&#092;",      '\\',$i);
                return $i;
        }
        function seesafelySqlStr($i){
                return ( htmlspecialchars ($i, ENT_QUOTES) );
        }

        function ggri($request_name, $i=0){
            return !isset($_REQUEST[$request_name]) ? $i : safelySqlInt($_REQUEST[$request_name]);
        }
        
        function gggi($request_name){    	return safelySqlInt (  $_GET[$request_name]      );			}
        function ggrr($request_name){    	return ( $_REQUEST[$request_name] 	);					}
        function gggr($request_name){ 		return ( $_GET[$request_name] 		);					}
        function ggpr($request_name){    	return ( $_REQUEST[$request_name] 	);					}
        function ggpi($request_name){   	return safelySqlInt (  $_REQUEST[$request_name]  );			}
        function ggrr_strong($request_name){return safelySqlStr (  urldecode($_REQUEST[$request_name])  ); 	}
        function gggr_strong($request_name){return safelySqlStr (  urldecode($_GET[$request_name])  );    		}

        function safelyXml ($text){
                $text = str_replace("&", 	"&amp;", $text);
                $text = str_replace('<', 	"&lt;", $text);
                $text = str_replace('>', 	"&gt;", $text);
                return $text;
        }

/*######################################################################*/
/*######################################################################*/
/*#####################  функции базы данных   #########################*/
/*######################################################################*/
/*######################################################################*/

        /**
         * Выполняем SQL запрос ( подходит для запросов insert, update delete )
         *
         * @param <string> $sql
         * @param <int> $printerr
         * @return <bool>
         */
        function ggsqlq($sql, $printerr=1){
                global $database;
                $database->setQuery( $sql );
                if(!$database->query()){
                        if (  $printerr  ) {
                                print "_________ERRE_4: ошибка базы данных____";
                                print($database->stderr( true ) );
                        }
                        return false;
                }
                return true;
        }

        /**
         * Выполнить sql-запрос и результат поместить в массив объектов
         *
         * @param <string> $sql
         * @param <int> $limitstart
         * @param <int> $limit
         * @return <obj>
         */
        function ggsql($sql, $limitstart=0, $limit=0 ){
                global $database;
                $database->setQuery( $sql, $limitstart, $limit );
                return $database->loadObjectList( );
        }

        /**
         * Выполнить SQL-запрос и возвратить число-результат
         *
         * @param <string> $sql
         * @return <type>
         */
        function ggsqlr($sql){
                global $database;
                $database->setQuery( $sql );
                return $database->loadResult();
        }

        /**
         * Быстрая загрузка объекта (формирование SELECT * FROM $table WHERE $field='$val' )
         *
         * @param <string> $val
         * @param <string> $table
         * @param <string> $field
         * @param <int> $showsql
         * @return <type>
         */
        function ggo($val, $table, $field="id", $showsql=0){
                global $database;
                $sql = "select * from $table where $field = '".$val."' ";
                if (  $showsql  )	ggbr ($sql);
                $database->setQuery( $sql );
                $aucs = $database->loadObjectList( );
                if (  count ($aucs)>0  ) return $aucs[0];
                else return false;
        }

/** выводим время загрузки страницы и время от прошлого вызова функции ggpt (время таймера)
 *
 * @param <text> $txt
 */
function ggpt($txt=''){
    global $reg, $page_time;
    $tend = getmicrotime();
    if ($txt!='') printf ("<br>Время создания страницы %f секунд", ( $tend - $page_time['start'] )  );
    if ($txt!='') printf ("<br><b>Время таймера_$txt=%f секунд</b>", ( $tend - $page_time['timer'] )  );
    $page_time['timer'] = $tend;
}

function aggr($GGobj) {
		print ( "<pre style='text-align=left'>" );
		$main_res = print_r ($GGobj, true);
		$main_res = str_replace("\r", "<br />", $main_res);
		$main_res = str_replace("\n", "<br />", $main_res);
		print( $main_res);
		print(  "</pre>" );
}

function fggtr($fh=0, $GGobj, $rows=8, $cols=80) {
	if ($fh){
		fwrite ($fh, "<p><textarea rows=$rows cols=$cols>" );
		$main_res = print_r ($GGobj, true);
		fwrite($fh, $main_res);
		fwrite($fh,  "</textarea></p>" );
	}
	else ggtr($GGobj);
}
/**
 * вывод содержимого объекта функцией print_r()
 * @param <obj> $data_rows
 */
function ggr($GGobj) {
	print "<pre style='text-align=left'>"; print_r ($GGobj); print "</pre>";
}
/**
 * вывод содержимого объекта в теге <textarea>
 * @param <obj> $data_rows
 * @param <int> $rows
 * @param <int> $cols
 */
function ggt($data_rows,$rows=8,$cols=80){
	print "<p><textarea rows=$rows cols=$cols>";
	print $data_rows;
	print "</textarea></p>";
}
/**
 * вывод содержимого объекта функцией print_r() в теге <textarea>
 * @param <obj> $data_rows
 * @param <int> $rows
 * @param <int> $cols
 */
function ggtr ($data_rows,$rows=8,$cols=80){
	print "<p><textarea rows=$rows cols=$cols>";
	print_r ($data_rows);
	print "</textarea></p>";
}
function ggtr5 ($data_rows,$cols=80){ ggtr($data_rows, 50, $cols); }
function ggtr3 ($data_rows,$cols=80){ ggtr($data_rows, 30, $cols); }
function ggtr2 ($data_rows,$cols=80){ ggtr($data_rows, 20, $cols); }
function ggtr1 ($data_rows,$cols=80){ ggtr($data_rows, 10, $cols); }
function ggtr01 ($data_rows,$cols=80){ ggtr($data_rows, 1, $cols); }

function ggd ($data_rows,$rows=50,$cols=80){
	ggtr (  $data_rows,$rows, $cols  );
	die(  "".rand(0, 10000).""  );
}
function ggdd ($rows=20, $cols=80){ global $database; ggd($database, $rows, $cols); }
function ggrd ($dienow=1){ global $reg; ggr($reg['db']); if (  $dienow==1  ) die(); }

function ggbr ($str=""){
	print "<br />".$str;
}
function fggbr ($str="", $fh=0){
if ($fh){
		fwrite($fh,  "<br />".$str );
	}
	else ggbr($str);
}



function getmicrotime(){
		list($usec, $sec) = explode(" ",microtime());
		return ((float)$usec + (float)$sec);
}




function ixml2array($text) {
 $reg_exp = '/<(\w+)[^>]*>(.*?)<\/\\1>/s';
  preg_match_all($reg_exp, $text, $match);
  foreach ($match[1] as $key=>$val) {
    if ( preg_match($reg_exp, $match[2][$key]) ) {
      $array[$val][] = ixml2array($match[2][$key]);
    } else {
      $array[$val] = $match[2][$key];
    }
  }
  return $array;
}

function del_enter($product_cell){
		$product_cell = str_replace( "\n", "", $product_cell );
		$product_cell = str_replace( "\r", "", $product_cell );
		return $product_cell;
}

/*######################################################################*/
/*######################################################################*/
/*######################  CONVERTER SECTION   ##########################*/
/*######################################################################*/
/*######################################################################*/

function load_xml($table, $field, $where){
global $database;

        $sql = "SELECT $field FROM $table ".$where;
        $query = mysql_query($sql, $database->_resource);
        $dataArray = mysql_fetch_assoc($query);
		$alldata = array();
		$product_cell = str_replace( "\n", "", $dataArray[$field] );
		$product_cell = str_replace( "\r", "", $product_cell );
		$aucs = split(";",$product_cell);
		foreach ($aucs as $auc){
			if (  strcmp($auc,"")==0  ) continue;
			$auc = split ("=",$auc);
			$alldata[$auc[0]] = $auc[1];
		}
		return $alldata;
}


/*######################################################################*/
/*######################################################################*/
/*#######################       TRANSLATE	  ######################*/
/*######################################################################*/
/*######################################################################*/

/**
 * Возвращает строку для адреса, из которой удалены все лишние(запрещенные) символы
 *
 * @param <string> $txt
 * @return <string>
 */

function genRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz-_';
    $m = strlen($characters) - 1;
    $string = '';

    for ($p = 0; $p < $length; $p++)
        $string .= $characters[ mt_rand(0, $m) ];
    return $string;
}

function sefname($txt, $rand=true){
    $sefname = preg_replace('/&#?[\w\d]+;/', '_', trim($txt));
    $sefname = strtolower(trans2eng($sefname));
    $sefname = preg_replace('/[^\d\w\-_\.]/', '_', $sefname);
    $sefname = preg_replace('/_+/', '_', $sefname);
    $sefname = preg_replace('/-+/', '-', $sefname);

    if (strlen($sefname) == 0 )
        $sefname = $rand ? genRandomString() : '';

    return $sefname;
}

/**
 * Преобразует строку из кирилицы в англоязычный вариант (транслитерация)
 * ВАЖНО ДЛЯ РАСКРУТКИ!!! Транслитерация с учетом правил Yandex (т.е. поисковые системы слова в такой транслитерации понимают нормально)
 * изменять НЕЛЬЗЯ!!!
 * @param <string> $e24ss
 * @return <string>
 */
function trans2eng ($e24ss) {
    $e24ss = htmlspecialchars(  urldecode($e24ss)  );
    $conv='';
    $iso = array(
       "Є"=>"YE","І"=>"I","Ѓ"=>"G","і"=>"i","№"=>"#","є"=>"ye","ѓ"=>"g",
       "А"=>"A","Б"=>"B","В"=>"V","Г"=>"G","Д"=>"D",
       "Е"=>"E","Ё"=>"YO","Ж"=>"ZH",
       "З"=>"Z","И"=>"I","Й"=>"Y","К"=>"K","Л"=>"L",
       "М"=>"M","Н"=>"N","О"=>"O","П"=>"P","Р"=>"R",
       "С"=>"S","Т"=>"T","У"=>"U","Ф"=>"F","Х"=>"H",
       "Ц"=>"C","Ч"=>"CH","Ш"=>"SH","Щ"=>"SHH","Ъ"=>"",
       "Ы"=>"I","Ь"=>"","Э"=>"E","Ю"=>"YU","Я"=>"YA",
       "а"=>"a","б"=>"b","в"=>"v","г"=>"g","д"=>"d",
       "е"=>"e","ё"=>"yo","ж"=>"zh",
       "з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
       "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
       "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
       "ц"=>"c","ч"=>"ch","ш"=>"sh","щ"=>"shh","ъ"=>"",
       "ы"=>"i","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya","«"=>"","»"=>"","—"=>"-","/"=>"-"
    );
    return strtr($e24ss, $iso);
}



function do_access(){

	$last_visit = getdate();
	$last_visit = $last_visit['year'].".".$last_visit['mon'].".".$last_visit['mday']." ".$last_visit['hours'].":".$last_visit['minutes'].":".$last_visit['seconds'];

	$do_stat = true;

	if (  strcmp($_SERVER['REMOTE_ADDR'],"77.35.7.229")==0  )	$do_stat = false;
	if (  strcmp($_SERVER['REMOTE_ADDR'],"82.162.164.195")==0  )	$do_stat = false;
	if (  strcmp($_SERVER['REMOTE_ADDR'],"212.16.193.66")==0  )	$do_stat = false;
	if (  strcmp($_SERVER['REMOTE_ADDR'],"82.162.65.66")==0  )	$do_stat = false;
	if (  strcmp($_SERVER['REMOTE_ADDR'],"77.35.7.186")==0  )	$do_stat = false;
	if (  strcmp($_SERVER['REMOTE_ADDR'],"86.102.40.228")==0  )	$do_stat = false;
	if (  strcmp($_SERVER['REMOTE_ADDR'],"85.95.130.66")==0  )	$do_stat = false;

//	if (  strcmp($_SERVER['REMOTE_ADDR'],"80.255.142.157")==0  )	$do_stat = false;

	if (  $do_stat == false  ){

		$ip_data = ggsql("select * from ipstat where addr='".$_SERVER['REMOTE_ADDR']."' ");
		if (  count ($ip_data)==0  ){	//НОВЫЙ АДРЕС НЕОБХОДИМО ЕГО ДОБАВИТЬ
			ggsqlq ("INSERT INTO `ipstat` (`addr`,`count`,`last_visit`, `last_page`) VALUES  ('".$_SERVER['REMOTE_ADDR']."',1,'".$last_visit."', 'banned".$_SERVER['QUERY_STRING']."');");
		}
		else {
			ggsqlq (  "UPDATE `ipstat` SET count = ".($ip_data[0]->count+1).", last_visit='".$last_visit."', last_page='banned".$_SERVER['QUERY_STRING']."' WHERE addr='".$_SERVER['REMOTE_ADDR']."' ;"  );
		}
		sleep (30);
	}


	return $do_stat;
}

function get_file($filename){
	global $jas_sid;

	// открываем файл;
	$data_rows = "";
	$h = fopen($filename,"r");
	while (!feof ($h)) {
		$content = fgets($h);
		$data_rows .= $content;
	}
	fclose($h);
	$data_rows = str_replace("\r", "", $data_rows);
	$data_rows = str_replace("\n", "", $data_rows);
	return $data_rows;
}

/*######################################################################*/
/*######################################################################*/
/*#########################  РАБОТА С ГРАФИКОЙ  ########################*/
/*######################################################################*/
/*######################################################################*/

        function i24get_file_extension($file_name){	$imginfo = getimagesize(  urldecode($file_name)  );
                switch (  $imginfo[2]  ){
                        case 1: return 'gif'; 	break;
                        case 2: return 'jpg'; 	break;
                        case 3: return 'png'; 	break;
                        case 4: return 'swf'; 	break;
                        case 5: return 'psd'; 	break;
                        case 6: return 'bmp'; 	break;
                        case 7: return 'tiff'; 	break;
                        case 9: return 'jpc'; 	break;
                        case 10: return 'jp2'; 	break;
                        case 11: return 'jpx'; 	break;
                        case 12: return 'jb2'; 	break;
                        case 13: return 'swc'; 	break;
                        case 14: return 'iff'; 	break;
                        case 15: return 'wbmp';	break;
                        case 16: return 'xbm'; 	break;
                        default: return 'jpg'; 	break;
                } // preg_match("/\.(.*)$/",$file_name, $matches); return ($matches[1]);
        }
        function i24get_imagecreatefrom_extension($file_name){	$imginfo = getimagesize(  urldecode($file_name)  );
                switch (  $imginfo[2]  ){
                        case 1: return 'gif'; 	break;
                        case 2: return 'jpeg'; 	break;
                        case 3: return 'png'; 	break;
                        case 4: return 'swf'; 	break;
                        case 5: return 'psd'; 	break;
                        case 6: return 'wbmp'; 	break;
                        case 7: return 'tiff'; 	break;
                        case 9: return 'jpc'; 	break;
                        case 10: return 'jp2'; 	break;
                        case 11: return 'jpx'; 	break;
                        case 12: return 'jb2'; 	break;
                        case 13: return 'swc'; 	break;
                        case 14: return 'iff'; 	break;
                        case 15: return 'wbmp';	break;
                        case 16: return 'xbm'; 	break;
                        default: return 'jpg'; 	break;
                } // preg_match("/\.(.*)$/",$file_name, $matches); return ($matches[1]);
        }
        function i24makesmallfoto($src_file, $thumb_name,
                                                                $max_width_t,
                                                                $max_height_t,
                                                                $tag,
                                                                $quality = 95,
                                                                $saveto='jpg'
                                                                )
        {
        //	ggd (site_path);
        //	ggd ($thumb_name);
                global $mosConfig_absolute_path;
                ini_set('memory_limit', '128M');

                $src_file = urldecode($src_file);
                $imginfo = getimagesize($src_file); //ggd ($imginfo);

                $type = i24get_imagecreatefrom_extension ($src_file);  // ggd ($imginfo);
                $read = 'imagecreatefrom' . $type;
                $write = 'image' . $type;
                $src_img = $read($src_file);

                $src_w = $imginfo[0];	$src_h = $imginfo[1];

                if (  $max_height_t==0  )		{ $zoom   = $max_width_t / $src_w; }
                else if (  $max_width_t==0  )	{ $zoom   = $max_height_t / $src_h; }
                else{
                        $zoom_h = $max_height_t / $src_h;
                        $zoom_w = $max_width_t / $src_w;
                        $zoom   = min($zoom_h, $zoom_w);
                }
                $dst_thumb_h  = $zoom<1 ? round($src_h*$zoom) : $src_h;
                $dst_thumb_w  = $zoom<1 ? round($src_w*$zoom) : $src_w;

                $dst_t_img = imagecreatetruecolor($dst_thumb_w,$dst_thumb_h);
                $white = imagecolorallocate($dst_t_img,255,255,255);
                imagefill($dst_t_img,0,0,$white);
                imagecopyresampled($dst_t_img,$src_img, 0,0,0,0, $dst_thumb_w,$dst_thumb_h,$src_w,$src_h);
                $textcolor = imagecolorallocate($dst_t_img, 255, 255, 255);
                if (  $tag!=''  ) imagestring($dst_t_img, 2, 2, 2, "$tag", $textcolor);
        /*	if (  $type=='png'  ) 		$desc_img = imagepng($dst_t_img,"$thumb_name");
                else if (  $type=='wbmp'  )	$desc_img = imagewbmp($dst_t_img,"$thumb_name");
                else 						$desc_img = $write($dst_t_img,"$thumb_name", 95); */
                if (  $saveto=='jpg'  )	$desc_img = @imagejpeg($dst_t_img,"$thumb_name", $quality);  // ggd();
        }
        function i24makesmallfoto_fix($src_file, $thumb_name,
                                                                $max_width_t,
                                                                $max_height_t,
                                                                $tag,
                                                                $quality = 95,
                                                                $saveto='jpg'
                                                                )
        {
                global $mosConfig_absolute_path;
                ini_set('memory_limit', '128M');

                $src_file = urldecode($src_file);
                $imginfo = getimagesize($src_file);

                $type = i24get_imagecreatefrom_extension ($src_file); // ggd ($type);
                $read = 'imagecreatefrom' . $type;
                $write = 'image' . $type;
                $src_img = $read($src_file);

                $src_w = $imginfo[0];	$src_h = $imginfo[1];

                if (  $max_height_t==0  )		{ $zoom   = $max_width_t / $src_w; }
                else if (  $max_width_t==0  )	{ $zoom   = $max_height_t / $src_h; }
                else{
                        $zoom_h = $max_height_t / $src_h;
                        $zoom_w = $max_width_t / $src_w;
                        $zoom   = max($zoom_h, $zoom_w);
                }
                $dst_thumb_h  = $max_height_t;
                $dst_thumb_w  = $max_width_t;

                $srcw = round($dst_thumb_w/$zoom);
                $srch = round($dst_thumb_h/$zoom);
                $offw = round(  ($src_w - $srcw)/2  );
                $offh = round(  ($src_h - $srch)/2  );

                $dst_t_img = imagecreatetruecolor($dst_thumb_w,$dst_thumb_h);
                $white = imagecolorallocate($dst_t_img,255,255,255);
                imagefill($dst_t_img,0,0,$white);
                imagecopyresampled($dst_t_img,$src_img, 0,0,$offw,$offh, $dst_thumb_w,$dst_thumb_h,$srcw,$srch);
                $textcolor = imagecolorallocate($dst_t_img, 255, 255, 255);
        /*	if (  $type=='png'  ) 		$desc_img = imagepng($dst_t_img,"$thumb_name");
                else if (  $type=='wbmp'  )	$desc_img = imagewbmp($dst_t_img,"$thumb_name");
                else 						$desc_img = $write($dst_t_img,"$thumb_name", 95);	*/
                if (  $saveto=='jpg'  )	$desc_img = @imagejpeg($dst_t_img,"$thumb_name", $quality);
        }


function invert_array($icatway){
		$aaa = array();
		for ($iii=count($icatway)-1; $iii>=0; $iii-- ){
			$aaa[] = $icatway[$iii];
		}
		return $aaa;
}
function url_parse($url){
	$url = cut_element($url, "?", "");
	$a = explode('&', $url); $i = 0; $ret = array();
	while ($i < count($a)) {
		$b = explode('=', $a[$i]);
		$ret[$b[0]] = isset($b[1]) ? htmlspecialchars(urldecode($b[1])) : '';
		$i++;
	}
	return $ret;
}
function highslide_init(){
?><script language="JavaScript" type="text/javascript" src="<?php print site_url; ?>/highslide/highslide.packed.js"></script>
<link rel="stylesheet" type="text/css" href="/highslide/highslide.css" />
<script type=text/javascript>
    hs.graphicsDir = '/highslide/graphics/';
    hs.outlineType = 'rounded-white';
    hs.numberOfImagesToPreload = 0;
    hs.showCredits = false;
	hs.lang = {
	   fullExpandTitle : 'Развернуть до полного размера',
	   restoreTitle : 'Кликните для закрытия картинки, нажмите и удерживайте для перемещения',
	   focusTitle : 'Сфокусировать',
	   loadingText :     'Загрузка...',
	   loadingTitle :    'Нажмите для отмены',
	   loadingOpacity : 0.75
	};
</script><?php
}


/*######################################################################*/
/*######################################################################*/
/*############################  СТАТИСТИКА  ############################*/
/*######################################################################*/
/*######################################################################*/

        function do_ipstat(){
                $last_visit = getdate();
                $last_visit = $last_visit['year'].".".$last_visit['mon'].".".$last_visit['mday']." ".$last_visit['hours'].":".$last_visit['minutes'].":".$last_visit['seconds'];
                $ip_data = ggsql("select * from ipstat where addr='".$_SERVER['REMOTE_ADDR']."' ");
                if (  count ($ip_data)==0  ){	//НОВЫЙ АДРЕС НЕОБХОДИМО ЕГО ДОБАВИТЬ
                        ggsqlq ("INSERT INTO `ipstat` (`addr`,`count`,`last_visit`, `last_page`) VALUES  ('".$_SERVER['REMOTE_ADDR']."',1,'".$last_visit."', '".$_SERVER['QUERY_STRING']."');");
                }
                else {
                        ggsqlq (  "UPDATE `ipstat` SET count = ".($ip_data[0]->count+1).", last_visit='".$last_visit."', last_page='".$_SERVER['QUERY_STRING']."' WHERE addr='".$_SERVER['REMOTE_ADDR']."' ;"  );
                }
        }

        function ifipbaned(){
                global $ip_ban;
                foreach ($ip_ban as $iprow){
                        if (  strcmp($_SERVER['REMOTE_ADDR'], $iprow)==0  ) return true;
                }
                return false;
        }

/**
 * Определение браузера, только название, без версии (  Mozilla Firefox, Chrome,  Opera,   MSIE )
 * @param <string> $agent
 * @return <string>
 */
function ibrowser( $agent='xxx' ) {
	 require( site_path .'/includes/agent_browser.php' );
	if (  $agent=='xxx'  ) $agent = $_SERVER['HTTP_USER_AGENT'];

        if (preg_match( "/msie[\/\sa-z]*([\d\.]*)/i", $agent, $m )
        && !preg_match( "/webtv/i", $agent )
        && !preg_match( "/omniweb/i", $agent )
        && !preg_match( "/opera/i", $agent )) {
                // IE
                return "MSIE";
        } else if (preg_match( "/netscape.?\/([\d\.]*)/i", $agent, $m )) {
                // Netscape 6.x, 7.x ...
                return "Netscape";
        } else if (preg_match( "/Chrome[\/\sa-z]*([\d\.]*)/i", $agent, $m )) {
                // Netscape 6.x, 7.x ...
                return "Chrome";

        } else if ( preg_match( "/mozilla[\/\sa-z]*([\d\.]*)/i", $agent, $m )
        && !preg_match( "/gecko/i", $agent )
        && !preg_match( "/compatible/i", $agent )
        && !preg_match( "/opera/i", $agent )
        && !preg_match( "/galeon/i", $agent )
        && !preg_match( "/safari/i", $agent )) {
                // Netscape 3.x, 4.x ...
                return "Netscape";
        } else {
                // Other
                $found = false;
                foreach ($browserSearchOrder as $key) {
                        if (preg_match( "/$key.?\/([\d\.]*)/i", $agent, $m )) {
                                $name = "$browsersAlias[$key]";
                                return $name;
                                break;
                        }
                }
        }
        return 'Unknown';
}

/**
 * Расширенное определение браузера, название и версия
 * @param <string> $agent
 * @return <string>
 */
function ibrowserpro( $agent='xxx' ) {
	 require( site_path .'/includes/agent_browser.php' );
	if (  $agent=='xxx'  ) $agent = $_SERVER['HTTP_USER_AGENT'];

        if (preg_match( "/msie[\/\sa-z]*([\d\.]*)/i", $agent, $m )
        && !preg_match( "/webtv/i", $agent )
        && !preg_match( "/omniweb/i", $agent )
        && !preg_match( "/opera/i", $agent )) {
                // IE
                return "MS Internet Explorer $m[1]";
        } else if (preg_match( "/netscape.?\/([\d\.]*)/i", $agent, $m )) {
                // Netscape 6.x, 7.x ...
                return "Netscape $m[1]";
        } else if (preg_match( "/Chrome[\/\sa-z]*([\d\.]*)/i", $agent, $m )) {
                // Netscape 6.x, 7.x ...
                return "Chrome $m[1]";

        } else if ( preg_match( "/mozilla[\/\sa-z]*([\d\.]*)/i", $agent, $m )
        && !preg_match( "/gecko/i", $agent )
        && !preg_match( "/compatible/i", $agent )
        && !preg_match( "/opera/i", $agent )
        && !preg_match( "/galeon/i", $agent )
        && !preg_match( "/safari/i", $agent )) {
                // Netscape 3.x, 4.x ...
                return "Netscape $m[1]";
        } else {
                // Other
                $found = false;
                foreach ($browserSearchOrder as $key) {
                        if (preg_match( "/$key.?\/([\d\.]*)/i", $agent, $m )) {
                                $name = "$browsersAlias[$key] $m[1]";
                                return $name;
                                break;
                        }
                }
        }
        return 'Unknown';
}
function load_lib ($comp_lib){
	require_once (site_path.'/component/'.$comp_lib.'/'.$comp_lib.'_lib.php');
}

function load_adminclass ($comp_lib){
	require_once (site_path.'/iadmin/component/'.$comp_lib.'/'.$comp_lib.'.class.php');
}
function delfile ($filename){
	if(  is_file($filename)  )	unlink($filename);
}

function get_pathway_array($thisfotocat, $table_name, $parent_field, $start_url, $start_name, $last_is_url){
global $reg;
		$icatway = array(); $iii = 0;
		if (  $last_is_url==1  ){
			if (  $thisfotocat->id==0  ) $icatway[0]->url = $start_url;
			else 						 $icatway[0]->url = $thisfotocat->sefnamefull.'/'.$thisfotocat->sefname;
		}
		$icatway[0]->name = $thisfotocat->name;
		$icatway[0]->parent = $thisfotocat->$parent_field;
		if (  $thisfotocat->id==0  ) return $icatway;

		while ($icatway[$iii]->parent!=0){
			$icur_catfoto = ggo($icatway[$iii]->$parent_field, $table_name);
			$iii++;
			$icatway[$iii]->url = $icur_catfoto->sefnamefull.'/'.$icur_catfoto->sefname;
			$icatway[$iii]->name = $icur_catfoto->name;
			$icatway[$iii]->parent = $icur_catfoto->parent;
		}
		if (  $start_name!=''  ){
			$icatway[$iii+1]->name = $start_name;
			$icatway[$iii+1]->url = $start_url;
		}
		return array_reverse( $icatway );
}
function get_lasturl_for_type($id, $type){
	if (  $type=='excat'  ) return "?ca=excat&task=editA&id=$id&hidemainmenu=1";
	if (  $type=='exgood'  ) return "?ca=exgood&task=editA&id=$id&hidemainmenu=1";
	if (  $type=='content'  ) return "?ca=content&task=edit&id=$id&hidemainmenu=1";
	if (  $type=='typedcontent'  ) return "?ca=typedcontent&task=edit&id=$id&hidemainmenu=1";
	if (  $type=='icat'  ) return "?ca=icat&task=editA&id=$id&hidemainmenu=1";
}
function get_url_for_type($id, $type){
	if (  $type=='excat'  ) return  "?ca=excat&task=editA&id=$id&hidemainmenu=1";
	if (  $type=='exgood'  ) return "?ca=excat&task=editA&id=$id&hidemainmenu=1";
	if (  $type=='content'  ) return "?ca=icat&task=editA&id=$id&hidemainmenu=1";
	if (  $type=='icat'  ) return    "?ca=icat&task=editA&id=$id&hidemainmenu=1";
}
function get_pathway_array_admin($thisfotocat, $table_name, $parent_field, $start_url, $start_name, $last_is_url, $type){
global $reg;
		$icatway = array(); $iii = 0;
		if (  $last_is_url==1  ){
			if (  $thisfotocat->id==0  ) $icatway[0]->url = $start_url;
			else 						 $icatway[0]->url = get_lasturl_for_type($thisfotocat->id, $type);
		}
		if (  $type=='content'  ) $thisfotocat->name = $thisfotocat->title;
		if (  $type=='content'  ) if (  $thisfotocat->catid  ) $thisfotocat->parent = $thisfotocat->catid;
		if (  $type=='typedcontent'  ) $thisfotocat->name = $thisfotocat->title;
		if (  $type=='typedcontent'  ) if (  $thisfotocat->catid  ) $thisfotocat->parent = $thisfotocat->catid;

		$icatway[0]->name = $thisfotocat->name;
		$icatway[0]->parent = $thisfotocat->$parent_field;
		if (  $thisfotocat->id==0  ) return $icatway;

		while ($icatway[$iii]->parent!=0){
			$icur_catfoto = ggo($icatway[$iii]->$parent_field, $table_name);
			$iii++;
			$icatway[$iii]->url = get_url_for_type($icur_catfoto->id, $type);
			$icatway[$iii]->name = $icur_catfoto->name;
			$icatway[$iii]->parent = $icur_catfoto->parent;
		}
		if (  $start_name!=''  ){
			$icatway[$iii+1]->name = $start_name;
			$icatway[$iii+1]->url = $start_url;
		}
		return array_reverse( $icatway );
}

/*
 *
 * ФУНКЦИИ ВЫВОДА ВЫЛЮТ
 *
 * 0...4 - стиль написания, предполагается что для сайта достаточно 5-ти стилей для каждой валюты
 * и для каждого стиля определена своя функция
 * таким образом например для рубля определенно 5 функций rub0() ... rub4()
 * rub0() - самое маленькое обозначение, rub4() - самое большое
 *
 */
		function rub4(){
			if(  ibrowser()=='MSIE'  ){ return '<img src="/includes/images/rub/rubly4.gif" width="12" height="13" align="absmiddle" />'; }
			else { return '<img src="/includes/images/rub/rubly4.gif" width="12" height="13" align="absmiddle" style=" padding:0px; margin:0px; padding-bottom:2px;" />'; }
		}
		function rub3(){
			if(  ibrowser()=='MSIE'  ){ return '<img src="/includes/images/rub/rubly3.gif" width="11" height="12" align="absmiddle" />'; }
			else { return '<img src="/includes/images/rub/rubly3.gif" width="11" height="12" align="absmiddle" style=" padding:0px; margin:0px; padding-bottom:2px;" />'; }
		}
		function rub2(){
			if(  ibrowser()=='MSIE'  ){ return '<img src="/includes/images/rub/rubly2.gif" width="10" height="10" align="absmiddle" />'; }
			else { return '<img src="/includes/images/rub/rubly2.gif" width="10" height="10" align="absmiddle" style=" padding:0px; margin:0px; padding-bottom:1px;" />'; }
		}
		function rub1(){
			if(  ibrowser()=='MSIE'  ){ return '<img src="/includes/images/rub/rubly1.gif" width="10" height="10" align="absmiddle" />'; }
			else { return '<img src="/includes/images/rub/rubly1.gif" width="10" height="10" align="absmiddle" style=" padding:0px; margin:0px; padding-bottom:1px;" />'; }
		}
		function rub0(){
			if(  ibrowser()=='MSIE'  ){ return '<img src="/includes/images/rub/rubly0.gif" width="9" height="8" align="absmiddle" />'; }
			else { return '<img src="/includes/images/rub/rubly0.gif" width="9" height="8" align="absmiddle" style=" padding:0px; margin:0px; padding-bottom:0px;" />'; }
		}

		function eur4(){	return 'EUR';		}
		function eur3(){	return 'EUR';		}
		function eur2(){	return 'EUR';		}
		function eur1(){	return 'EUR';		}
		function eur0(){	return 'EUR';		}

		function usd4(){	return 'USD';		}
		function usd3(){	return 'USD';		}
		function usd2(){	return 'USD';		}
		function usd1(){	return 'USD';		}
		function usd0(){	return 'USD';		}

function get_insite_limit_start( $limit ){
	if(is_numeric($_REQUEST['page'])) return $limit * (ggri('page') -1); // если указана страница
	else return 0;
}
function get_insite_limit( &$request, $limit, $default ){
        if (  !isset($request[$limit])  ) return $default;
        if (  is_string($request[$limit])  and   $request[$limit]==''     ) return $default;
        if (  is_int($request[$limit])     and   $request[$limit]==0      ) return 0;

	return intval( mosGetParam( $request, $limit, $default ) );
}

/*
 * def предназначенна только для работы с РЕЕСТРОМ - $reg
 * def (param1, param2) - если определен param1 - то возвращает param1, иначе param2
 */
function def($i, $defi){
	if (  is_numeric($reg[$type.'_mid_copy'])  ){
		if (  $i==0  ) return $defi;
		else return $i;
	}
	else if (  is_string($reg[$type.'_mid_copy'])  ){
		if (  $i==''  ) return $defi;
		else return $i;
	}
	else{
		if (   $i===false  )	return $defi;
		else return $i;
	}
}
/*
 * def_request предназначенна только для работы с $_REQUEST
 * def_request (name, val) - если определен $_REQUEST['name'] - то возвращает $_REQUEST['name'], иначе val
 */
function def_request($reg_name, $defi){
	if (   isset($_REQUEST[$reg_name])  )	return $_REQUEST[$reg_name];
	else return $defi;
}


/**
 * УДАЛЕНИЕ КАВЫЧЕК, для ajax-скриптов
 * @param <string> $i
 * @return <string>
 */
function just_del_quotes($i){
	$i = str_replace('&quot;', 	"", $i);//"
	$i = str_replace('&#039;', 	"", $i);//'
	return $i;
}


/*
 *
 * ФУНКЦИИ РАБОТЫ С ФОРМАМИ
 *
 */
	/*
	 * АВТОМАТИЧЕСКОЕ СОХРАНЕНИЕ ФОРМЫ ПО Ctrl+Enter
	 *
	 * <form 	<?php ctrlEnter () ?>	 ... >
	 */
	function ctrlEnter ($submit='onsubmit'){ ?> onkeypress=" if((event.ctrlKey) && ((event.keyCode==10)||(event.keyCode==13))) { <?php
	if (  $submit!='onsubmit'  and  $submit!='submit'  ) print $submit;
	else { ?>this.<?=$submit ?>() <?php } ?> }" <?php }

        /**
         *
         * @param <type> $submit_apply
         * @param <type> $submit_save
         */
	function ctrlEnterCtrlAS ($submit_apply='', $submit_save=''){
		?> onkeypress=" if ((event.ctrlKey) &&  (event.charCode==32)) { <?=$submit_apply; ?> } <?
					 ?> if((event.ctrlKey) && ((event.keyCode==10)||(event.keyCode==13))) { <?=$submit_save; ?> } " <?
	}

	// выводит скромную надпись (Ctrl+Enter)
	function ctrlEnterHint ($freecode = ''){
        echo "<span {$freecode} class='CtrlEnterHint'> (Ctrl+Enter)</span>";
    }


/*
 *
 * ФУНКЦИИ РАБОТЫ С АДРЕСОМ
 *
 */
		function addhttp_ifneed($url){
			$result = $url;
			if (  strpos($result, "http://")===false  )
				return "http://".$result;
			return $result;
		}

		/** ВОЗВРАЩАЕТ АДРЕС САЙТА БЕЗ HTTTP://  */
		function short_surl(){
			global $reg;
			return str_replace("http://","", $reg['surl'] );
		}

		/** ПОЛУЧИТЬ ТЕКУЩИЙ ПУТЬ, т.е. нормальный путь без параметров типа ?name1=val1&name2=val2&name3=var3 */
		function get_current_seourl(){
			global $reg, $iseoname;
			$surl_request_url = $_SERVER['REQUEST_URI'];
		//	$surl_request_url = "/catalogue/avt/?sdfsdf=sdfsd";//$_SERVER['REQUEST_URI'];
			if (  strpos($surl_request_url, "?"  )===false  ) {
				return $surl_request_url;
			} else {
				preg_match("/^(.*)\?/",$surl_request_url, $matches);
				ggtr ($matches);
			}
		}
		function getshorturl($site_urlfull){	// убираем http и www
			$sname = str_replace("http://", "", $site_urlfull);
			$sname = str_replace("http://www.", "", $sname);
			return $sname;
		}

/**
 * Возвращает красивое имя пользователя, например Иванов Иван Иванович
 *
 * @param <obj> $user
 * @return <string>
 */
function userfio (&$user){
	$fio=array();
	if (  $user->usersurname!=''  ) 	$fio[] = desafelySqlStr( $user->usersurname );
	if (  $user->name!=''  ) 		$fio[] = desafelySqlStr( $user->name );
	if (  $user->userparentname!=''  ) 	$fio[] = desafelySqlStr( $user->userparentname );
	return implode(" ", $fio);
}
function printVersion ($url, $txt = "Версия для печати"){
	if (  isset($_REQUEST['4print'])  ) return;
	?><a onclick="window.open('<?=$url ?>','','menubar=no,width=900,height=650'); return false;" class="print_link" href="#"><?=$txt ?></a><?php

}

/**
 * true - Открыта версия для печати
 * false - открыта обычная страница
 * @return <type>
 */
function inPrintVersion (){
    if (isset($_REQUEST['4print']))
        return true;
    else
        return false;
}


function codeurl($url){
    $query_deurl = str_replace(  '&', '_iampa_',    $url  );
    $query_deurl = str_replace(  '=', '_iravna_',   $query_deurl  );
    $query_deurl = str_replace(  '?', '_ivoprosa_', $query_deurl  );
    return $query_deurl;
}
function decodeurl($url){
    $query_deurl = str_replace(  '_iampa_',    '&',   $url  );
    $query_deurl = str_replace(  '_iravna_',   '=',   $query_deurl  );
    $query_deurl = str_replace(  '_ivoprosa_', '?',   $query_deurl  );
    return $query_deurl;
}

function codeurl_admin($url){
    $query_url = str_replace(  '&', '_amp_', $url  );
    $query_url = str_replace(  '=', '_ravno_', $query_url  );
    return $query_url;
}
function decodeurl_admin($url){
    $query_url = str_replace(  '_amp_', '&', $url  );
    $query_url = str_replace(  '_ravno_', '=', $query_url  );
    return $query_url;
}
