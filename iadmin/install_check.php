<?
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
class install_check{
	var $writeable_array = array();
	var $skip_array = array();
	function checkAllErrors(){
		;
	}
	function print_error($err_mess){
		$err_mess = explode("//",$err_mess);
		?><tr><td><?=$err_mess[0] ?><span class="gray_error">//<?=$err_mess[1] ?></span></td></tr><? 
	}
	function print_error_hidden($err_mess, $err_id){
		$err_mess = explode("//",$err_mess);
		?><tr style="display:none" id="<?=$err_id ?>"><td><?=$err_mess[0] ?><span class="gray_error">//<?=$err_mess[1] ?></span></td></tr><? 
	}

	function check_error($reg_field, $reg_value, $err_mess){
		global $reg;
		$err_mess = str_replace("_mes_", '«'.$reg_field.'»', $err_mess );
		if  (  $reg[$reg_field]	== $reg_value  ) $this->print_error($err_mess);
	}
	function check_dir($dir, $access){
		if (  $access=='readonly'  ){
			if (   is_writable($dir)   )	$this->print_error('&mdash; Папка доступна для записи // «'.$dir.'» (установите права 544 (только чтение)');
		} else if (  $access=='writable'  ){
			if (   !is_writable($dir)   )	$this->print_error('&mdash; Папка не доступна для записи // «'.$dir.'» (установите права 744 (чтение и запись)');
		}
	}
	function check_file($dir, $access){
		if (  $access=='readonly'  ){
			if (   is_writable($dir)   )	$this->print_error('&mdash; Файл&nbsp;&nbsp; доступен для записи // «'.$dir.'»  (установите права 544 (только чтение)');
		} else if (  $access=='writable'  ){
			if (   !is_writable($dir)   )	$this->print_error('&mdash; Файл&nbsp;&nbsp; не доступен для записи // «'.$dir.'» (установите права 744 (чтение и запись)');
		}
	}
	
	function check_readonly($path) {
	
		$handle = opendir($path);
		while ( false !== ($file = readdir($handle)) ) {
			if (   ($file !== "..")  &&  ($file !== ".")   &&  !in_array($path."/".$file, $this->writeable_array)   &&  !in_array($path."/".$file, $this->skip_array)  ) {
				  //@chmod($path . "/" . $file, $perm);
				  // print $path . "/" . $file;  print "<br />";
				 if (  !is_file($path."/".$file)  ){
						$this->check_dir($path."/".$file, 'readonly');
						$this->check_readonly($path . "/" . $file);
				 } 
				 else  $this->check_file($path."/".$file, 'readonly');
			}
		}
		closedir($handle);
		
	}

	function check_writeable($path) {
	
		if (  is_file($path)  ){
			$this->check_file($path, 'writable');
			return;
		}
		
		$handle = opendir($path);
		while ( false !== ($file = readdir($handle)) ) {
			if (   ($file !== "..")  &&  ($file !== ".")   ) {
				 if (  !is_file($path."/".$file)  ){
						$this->check_dir($path."/".$file, 'writable');
						$this->check_writeable($path . "/" . $file);
				 } 
				 else  $this->check_file($path."/".$file, 'writable');
			}
		}
		closedir($handle);
		
	}

}
?>