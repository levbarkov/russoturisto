<?php
// ФАЙЛ СОДЕРЖИТ ОПИСАНИЯ ДОПОЛНИТЕЛЬНЫХ ФУНКЦИЯ, НЕ ВХОДЯЩИХ В ДАННЫЙ INSITE
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

#echo getRealIpAddr();
if( getRealIpAddr() != '18803039' ){ $_REQUEST['debug'] = true; };



# подготовка числа с плавающей точкой для сохранения в БД
function float_prepare($var) {
    $var = sprintf('%.2f', floatval($var));
    $var = str_replace(',', '.', $var);
    return $var;
}

function mb_ucfirst($string) {  
	$string = mb_ereg_replace("^[\ ]+", "", $string);  
	$string = mb_strtoupper(mb_substr($string, 0, 1, "UTF-8"), "UTF-8") . mb_substr($string, 1, mb_strlen($string), "UTF-8" );  
	return $string;  
}

# Для обхода всех вложнных категорий группы атрибута
function getAllExpackSet($id){
    if(empty($id))
        return false;
    
    $ids = array($id);
    $subset = ggsql("select `id` from #__expack_set where `parent` = {$id}");
    if(count($subset)){
        foreach($subset as $set){
            $sub_ids = getAllExpackSet($set->id);
            $ids = array_merge($ids, $sub_ids);
        }
    }		
    return $ids;		
}

function word_limiter($str, $limit = 100, $end_char = '&#8230;'){
	if (trim($str) == '')
		return $str;

	preg_match('~^\s*+(?:\S++\s*+){1,'. intval($limit) . '}~usi', $str, $matches);

	if (mb_strlen($str, 'utf-8') == mb_strlen($matches[0], 'utf-8'))
		$end_char = '';
		
	return rtrim($matches[0]) . $end_char;
}






// sdneo

function getRealIpAddr()
{
  if (!empty($_SERVER['HTTP_CLIENT_IP']))
  {
    $ip=$_SERVER['HTTP_CLIENT_IP'];
  }
  elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
  {
    $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
  }
  else
  {
    $ip=$_SERVER['REMOTE_ADDR'];
  }
  
  $ip_arr1 = explode(',',$ip);
  $ip_arr2 = explode('.',$ip_arr1[0]);
  $ip = $ip_arr2[0].$ip_arr2[1].$ip_arr2[2].$ip_arr2[3];

  return $ip;
}


if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
{
	$_REQUEST['4ajax'] = 1;
}

function xmp($array)
{
	if($_REQUEST['debug'])return;// o_o
		
	echo "<xmp>"; print_r( $array ); echo "</xmp>";
}


function pgtr ($val)
{
	if($_REQUEST['debug'])return;// o_o
	
	ggtr($val);
}

function gdetu($function)
{
	if($_REQUEST['debug'])return;// o_o
		
	$reflex = new ReflectionFunction($function);
	echo "<xmp>";
	print_r( $reflex->getFileName() );
	echo " ";
	print_r( $reflex->getStartLine() );
	echo "</xmp>";
}


function cutString($string, $maxlen) {
    $len = (mb_strlen($string) > $maxlen)
        ? mb_strripos(mb_substr($string, 0, $maxlen), ' ')
        : $maxlen
    ;
    $cutStr = mb_substr($string, 0, $len);
    return $cutStr;
}


function mindate($date, $mask=1) // 2011-06-20 16:06:02
{
	$date	= explode(' ', $date);
	$date	= explode('-', $date[0]);	

	$dayzero	= array('01','02','03','04','05','06','07','08','09' );
	$daynorm	= array('1','2','3','4','5','6','7','8','9' );
	# $month_arr1	= array( '01'=>'янв','02'=>'фев','03'=>'мар','04'=>'апр','05'=>'мая','06'=>'июн','07'=>'июл','08'=>'авг','09'=>'сен','10'=>'окт','11'=>'ноя','12'=>'дек' );
	# $month_arr2	= array( '01'=>'ЯНВАРЯ','02'=>'ФЕВРАЛЯ','03'=>'МАРТА','04'=>'АПРЕЛЯ','05'=>'МАЯ','06'=>'ИЮНЯ','07'=>'ИЮЛЯ','08'=>'АВГУСТА','09'=>'СЕНТЯБРЯ','10'=>'ОКТЯБРЯ','11'=>'НОЯБРЯ','12'=>'ДЕКАБРЯ' );
	# $month_arr3	= array( '01'=>'Января','02'=>'Февраля','03'=>'Марта','04'=>'Апреля','05'=>'Мая','06'=>'Июня','07'=>'Июля','08'=>'Августа','09'=>'Сентября','10'=>'Октября','11'=>'Ноября','12'=>'Декабря' );
	# $month_arr4	= array( '01'=>'января','02'=>'февраля','03'=>'марта','04'=>'апреля','05'=>'мая','06'=>'июня','07'=>'июля','08'=>'августа','09'=>'сентября','10'=>'октября','11'=>'ноября','12'=>'декабря' );

	if( $mask == 1 )
	{
		$day = str_replace($dayzero, $daynorm, $date[2]);
		$view_date = "{$day}.{$date[1]}.{$date[0]}";
	}

	return $view_date;
}






































