<?php
/* 
 * используется для генерирования капчи
 */

/**
 * Description of filter
 */
class captcha {
	var $codeid;
	var $img_id = "code";
	var $codeid_id = "codeid_id";	// ID скрытого поля, содержащего значение которое используется при проверки
	var $newcode_img = "/lib/captcha/images/reload.gif";
	var $captcha_img = "/lib/captcha/img.php";
	
	function generate_codeid (){
		mt_srand((double)microtime()*1000000);
		$this->codeid = mt_rand(100000,999999);
	}
	
	function init (){
		$this->generate_codeid();
	}
	
	function show_captcha($img_w=60, $img_h=25){
		?><img class="captcha_img" src="<?=$this->captcha_img.'?CodeID='.$this->codeid; ?>" border='0' title='Введите этот код в поле слева' alt='Code' id='<?=$this->img_id ?>' width='<?=$img_w ?>' height='<?=$img_h ?>'/><?
	}

	function newimg_button(){
		?><a class="captcha_new" href="javascript:spamfixreload('<?=$this->img_id ?>', '<?=$this->codeid ?>')"><img src='<?=$this->newcode_img ?>' title='Новый код' alt='Новый код' border='0'></a><?
	}
	
	function check_me (){
		global $reg;
		$usercodeid = ggri( 'CodeID' );	//  код для праверки, одинаковый у нас и в базе
		$usergbcode = ggri( 'gbcode' ); //  код введеный пользователем
		$query = "SELECT * FROM #__captcha_code WHERE CodeID='$usercodeid'"; 
		$reg['db']->setQuery( $query ); $row = NULL;
		$reg['db']->loadObject( $row );	// ggdd ();
		//ggd ($row);
		if(  (isset($row->CodeMD5) and ($row->CodeMD5 != "") and ($row->CodeMD5 == md5($usergbcode)))  ) return true; // введен правильный код безопасности
		else return false;
	}
	
	function codeid_input(){ // генерируем input с codeid: <input type='hidden' name='CodeID' value='$captcha->codeid' /> - вставлять только в форме
		?><input type='hidden' name='CodeID' id="<?=$this->codeid_id ?>" value='<?=$this->codeid ?>' /><?
	}
}
?>
