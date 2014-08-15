<?php

/**
 *
 * adminlog
 * <log><act>http://insite.dev/catalogue/auto</act><u>Admin</u><mod>2010-05-01</mod></log>
 *
 */
class adminlog {
	var $fname = "/iadmin/images/adminlog.xml";
	var $log_tmp = "<log><act></act><id></id><u></u><uid></uid><mod></mod><ip></ip></log>";
	
	function get_data_auc($act, $comp_name, &$obj_name, &$obj_id){
		$ret = "";
		switch ($act){
			case 'man':				$ret = $obj_name;
									break;			
			case 'del':				$ret = $comp_name.' — удаление объекта «'.$obj_name.'»';
									break;
			case 'del_cat':			$ret = $comp_name.' — удаление категории «'.$obj_name.'»';
									break;
			case 'del_themecfg':	$config_id = $_REQUEST['id'][0];  $config_id = safelySqlInt($config_id);  $config_row = ggo ($config_id, "#__theme_config");		 $obj_name = "$config_row->theme | type=$config_row->type | val=$config_row->val | ext_file=$config_row->ext_file"; $obj_id = $config_id;
									$ret = $comp_name.' — удаление шаблона «'.$obj_name.'»';
									break;
			case 'delcfg':			$config_id = $_REQUEST['id'][0];  $config_id = safelySqlInt($config_id);  $config_row = ggo ($config_id, "#__config");		 $obj_name = $config_row->name; $obj_id = $config_id;
									$ret = $comp_name.' — удаление настройки «'.$obj_name.'»';
									break;
			case 'cfg':				$ret = $comp_name.' — изменение (добавление) настроек';
									break;
			case 'save':			$ret = $comp_name.' — изменение объекта «'.$obj_name.'»';
									break;
			case 'new':				$ret = $comp_name.' — создание объекта «'.$obj_name.'»';
									break;
			case 'save_cat':		$ret = $comp_name.' — изменение категории «'.$obj_name.'»';
									break;
			case 'new_cat':			$ret = $comp_name.' — создание категории «'.$obj_name.'»';
									break;
			case 'del_foto_cat':	$ret = $comp_name.' — удаление фото категории «'.$obj_name.'»';
									break;
                        case 'del_foto_subcat':	$ret = $comp_name.' — удаление подкатегории фото «'.$obj_name.'»';
									break;
                        case 'del_file_subcat':	$ret = $comp_name.' — удаление подкатегории файлов «'.$obj_name.'»';
									break;
			case 'del_foto':		$ret = $comp_name.' — удаление фото объекта «'.$obj_name.'»';
									break;
			case 'save_foto':		$ret = $comp_name.' — изменение фото объекта «'.$obj_name.'»';
									break;
			case 'new_foto':		$ret = $comp_name.' — создание фото объекта «'.$obj_name.'»';
									break;
			case 'save_foto_cat':	$ret = $comp_name.' — изменение фото категории «'.$obj_name.'»';
									break;
			case 'new_foto_cat':	$ret = $comp_name.' — создание фото категории «'.$obj_name.'»';
									break;
			case 'save_foto_subcat':	$ret = $comp_name.' — изменение подкатегории фото «'.$obj_name.'»';
									break;
			case 'new_foto_subcat':	$ret = $comp_name.' — создание подкатегории фото «'.$obj_name.'»';
									break;
                        case 'save_file_subcat':	$ret = $comp_name.' — изменение подкатегории файлов «'.$obj_name.'»';
									break;
			case 'new_file_subcat':	$ret = $comp_name.' — создание подкатегории файлов «'.$obj_name.'»';
									break;
			case 'del_file':		$ret = $comp_name.' — удаление файла объекта «'.$obj_name.'»';
									break;
			case 'save_file':		$ret = $comp_name.' — изменение файла объекта «'.$obj_name.'»';
									break;
			case 'new_file':		$ret = $comp_name.' — создание файла объекта «'.$obj_name.'»';
									break;
		}
		return safelyXml($ret);
	}
	function get_data_user(){
		global $my;
		return safelyXml($my->username);
	}
	function get_data_userid(){
		global $my;
		return $my->id;
	}
	function get_data_time(){
		global $reg;
		return strftime( '%Y-%m-%d %H:%M:%S', time() );
	}
	function get_data_ip(){
		return $_SERVER['REMOTE_ADDR'];
	}

	function get_data($act, $comp_name, $obj_name, $obj_id){
		$tmp = $this->log_tmp;
		$tmp = str_replace("<act></act>",	"<act>".$this->get_data_auc($act, $comp_name, $obj_name, $obj_id)."</act>", $tmp);
		$tmp = str_replace("<id></id>",		"<id>". $obj_id."</id>", $tmp);
		$tmp = str_replace("<u></u>", 		"<u>".  $this->get_data_user()."</u>", $tmp);
		$tmp = str_replace("<uid></uid>", 	"<uid>".$this->get_data_userid()."</uid>", $tmp);
		$tmp = str_replace("<mod></mod>", 	"<mod>".$this->get_data_time()."</mod>", $tmp);
		$tmp = str_replace("<ip></ip>", 	"<ip>".$this->get_data_ip()."</ip>", $tmp);
		
		return $tmp;
	}

	function logme($act, $comp_name, $obj_name, $obj_id){
		$file = site_path.$this->fname;
		if (  !file_exists($file)  )  	die('Удален файл статистики операций ("'.$fname.'"). Продолжение не возможно');
		if (  !is_file($file)  )  		die('Удален файл статистики операций ("'.$fname.'"). Продолжение не возможно');
		if (  !is_writable($file)  )  	die('Нет прав на запись у файла статистики операций ("'.$fname.'"). Продолжение не возможно');
		 
		$fh = fopen($file, "a");
		fwrite(  $fh, ( $this->get_data($act, $comp_name, $obj_name, $obj_id) ).chr(13).chr(10)  );
		fclose($fh);
	}
	function get_log(&$xml, $limitstart, $limit){
		$file = site_path.$this->fname;
		$fh = fopen($file, "r");
		$i=0; $donext = true;
		while (1){
			$line1 = fgets($fh, 1000);	
			if (  $line1!=''  )	{			if (   $i>=$limitstart  ) $contents .= $line1;				}
			else { $donext = false; break; }
			$i++;
			if (  $i>=($limitstart+$limit)  ) break;
		}
		fclose($fh);
		$xml_string = '<?xml version="1.0" encoding="utf-8"?><logs>'.$contents.'</logs>';
		$xml = simplexml_load_string($xml_string) or die("Файл логов пуст");
		return $donext;
	}
}
