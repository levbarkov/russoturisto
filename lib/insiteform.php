<?php
/* 
 * используется для генерирования капчи
 */

class insiteform {
	var $formname="adminForm";
	var $showRequest = "showRequest";
	var $showResponse = "showResponse";
	var $reset_jquery;	//	команда jquery, необходмая, чтобы вернуть поле в исходное состояние
	var $light_jquery; 	//	команда jquery, необходмая, чтобы подсветить поле с ошибкой
	var $target = "output1";
	var $formoutput = "formoutput";
	var $forminput = "forminput";
	var $serveranswer = "serveranswer";	// id DIV'а ответа с результатом обработки формы
	var $email;
	var $errorids = array();
	var $sret = "";	// общий ответ выполнения формы
	var $captcha_gbcode_id = "gbcode";	// id поля, в котором содержится код капчи
	var $debugme = 0;	//	стандартное поле, если 1 - выводим отладочную информацию
	var $txt_fail_sret_start = "<strong>В вашей форме обнаружены следующие ошибки:</strong><br />";	//	начало сообщения об ошибках
	var $main_function_post = "";
	
	function redirect_me($url, $msg){
            // $url = '/'.$reg['feedback_seoname'].'/?';
            ?>window.location = '<?=$url ?>imsg=<?=$msg ?>';<?
	}
	function debug_div(){
		global $reg;
		if (  $reg['insiteform_debug']==1  or  $this->debugme==1  ){
			?><div id="<?=$this->forminput ?>" style="position: relative; border: 1px solid #ccc; width: 500px; margin: 20px 10px 20px 20px; padding: 10px;">ПОСЫЛКА СЕРВЕРУ</div><?
			?><div id="<?=$this->formoutput ?>" style="position: relative; border: 1px solid #ccc; width: 500px; margin: 20px 10px 20px 20px; padding: 10px;">ОТВЕТ ОТ СЕРВЕРА</div><?
		}
	}
	function checkmail($email=""){
		if (  $email==''  ) $email = $this->email;
		if (!preg_match("/^(?:[a-z0-9]+(?:[-_]?[a-z0-9]+)?@[a-z0-9]+(?:\.?[a-z0-9]+)?\.[a-z]{2,5})$/i",trim($email))){ return FALSE; }
		return true;
	}
	function check_user_mail($mail_field){
		if (  !$this->checkmail (ggrr($mail_field))  ){
			$this->sret .= "Почта не является правильным e-mail адресом<br />";
			$this->adderror($mail_field);
		}
	}
	function check_captcha(){
		if (   $this->finderror($this->captcha_gbcode_id)  ) return; // скорее всего код еще не ввели, поэтому на него уже есть ошибка
		$captcha = new captcha();
		if(  !$captcha->check_me()  ){
			$this->sret .= "Введен неверный код безопасности<br />";
			$this->adderror($this->captcha_gbcode_id);
		}
	}
	function adderror( $errorid ){
		if (   !$this->finderror($errorid)  )	$this->errorids[] = $errorid;
	}
	function finderror($errorid){ //find errorid in array of errors
		foreach (  $this->errorids as $link  ){
			if (  $link==$errorid  ) { return true; break; }
		}
		return false;
	}
	function check_for_empty_required_fields(){	// проверка обязательные поля, если пустое - то добавляется ошибка
		$reqidarray = explode(";", ggrr('reqid') );	$reqnamearray = explode(";", ggrr('reqname') );
		if (  count($reqidarray)>0  )
		foreach ( $reqidarray as $index=>$reqid1 ){
			if (  $reqid1==''  ) continue; 
			if (  ggrr($reqid1)==''  ){
				$this->sret .= $reqnamearray[$index]."<br />";
				$this->adderror( $reqid1 );
				/*$("#<=$reqid1 >").attr("ireqname"); */
			}
		}
	}
	function lrec($i){
                if (  $this->light_jquery==''  ) return;
		?>$('#<?=$this->errorids[$i] ?>').<? print $this->light_jquery;
			if ($i<count($this->errorids)-1){
				?>, function(){   <?
					$this->lrec( ($i+1) );
				?>   }<?
			}
		?>);<?
	}


	function tell_user_result($txt_ok="Спасибо! Ваша информация успешно отправлена.<br />"){
		if (  $this->sret==""  ) $this->sret = $txt_ok;
		else $this->sret = $this->txt_fail_sret_start.$this->sret."<br />";

		?>$("#<?=$this->serveranswer ?>").hide();
                // выводим текст об ошибке или спасибо
		$("#<?=$this->serveranswer ?>").html('<?=$this->sret ?>');
		$("#<?=$this->serveranswer ?>").toggle(1500, function(){ <?
                        // подсветка неправильно заполненных полей,
                        // необходим плагин jquery_color
			if (  count($this->errorids)>0  ){
				$this->lrec(0);
			}
		?>
		});
		<?
	}

        /**
         * снятие выделения со всех полей формы
         * когда используем  эффект подсветки полей
         */
	function reset_form(){
		$reqidarray = explode(";", ggrr('reqid') );
		if (  count($reqidarray)>0  )
		foreach ( $reqidarray as $reqid1 ){
			if (  $reqid1==''  ) continue;
			?>$('#<?=$reqid1 ?>').<?=$this->reset_jquery ?>; <?
		}

	}
	function if_filled_correct(){
		if (  count($this->errorids)>0  ) return false;
		else return true;
	}

	function java_make_ready(){  
		?>	$(document).ready(function() { 
				var options = { 
					target:        	'#<?=$this->target ?>',   // target element(s) to be updated with server response 
					dataType:		'script',
					beforeSubmit:  	<?=$this->showRequest ?>,  // pre-submit callback 
					success:       	<?=$this->showResponse ?>  // post-submit callback 
				}; 
				// bind form using 'ajaxForm' 
				$('#<?=$this->formname ?>').ajaxForm(options); 
			});
		<?
	}
	function java_make_reqid_and_reqname(){ ?>
		var cur_form = document.<?=$this->formname ?>;
		var me = cur_form.elements;
		var reqid = ""; reqname = "";
		for (var i=0; i < me.length; i++){
			if (  me[i].getAttribute('ireq')==1  ){
				reqid 	= reqid + me[i].getAttribute('id') + ";";   reqname = reqname + me[i].getAttribute('ireqname') + ";";
			}
		}
	<?
	}
	function java_make_showRequest(){ /* pre-submit callback  */ 
		?>function showRequest(formData, jqForm, options) {
			var reqid = ""; var reqname = ""; 
			// необходимо собрать поля, которые имеют статус ОБЯЗАТЕЛЬНЫЕ ДЛЯ ЗАПОЛНЕНИЯ (ireq="1")
			<? $this->java_make_reqid_and_reqname(); ?>
			//	alert (reqid);	alert (reqname);

			formData[formData.length] = { name: 'reqid', value: reqid } ;
			formData[formData.length] = { name: 'reqname', value: reqname } ;
			var queryString = $.param(formData); 
			$("div#<?=$this->forminput ?>").html('About to submit: ' + queryString);
			return true; 
		} 
		<?
	}
	function java_make_showResponse(){ /* post-submit callback  */ 
		?>function showResponse(responseText, statusText, xhr, $form)  { 
			$("div#<?=$this->formoutput ?>").html('<strong>status</strong>: ' + statusText + '<br /><br /><strong>responseText</strong>: <br />' + responseText + 
				'\n\n');
		} 
		<?
	}
	
	function java_make_code(){ /* generate all java code  */ ?>
		<script language="javascript">
			<? $this->java_make_ready(); ?>
			<? // function showRequest(formData, jqForm, options) { alert(1); }; ?>

			<? $this->java_make_showRequest(); ?>
			<? $this->java_make_showResponse(); ?>
		</script>
		<?
	}
	
	function java_make_stars(){ /* javacode makes * for required fields */ 
		?><script language="javascript">
			var cur_form = document.<?=$this->formname ?>;
			var me = cur_form.elements;
			var i24tr = me[2].parentNode.parentNode;
			for (var i=0; i < me.length; i++){
				if (  me[i].getAttribute('ireq')==1  ){
					var i24tr = me[i].parentNode.parentNode;
					if (   i24tr.childNodes[0].nodeType==1  ) 	i24tr.childNodes[0].innerHTML += '&nbsp;<span class="req_field">*</span>';
					else 										i24tr.childNodes[1].innerHTML += '&nbsp;<span class="req_field">*</span>';
				}
			}
		</script><?
	}

        /**
         * ЭФФЕКТ ДЛЯ ПОЛЕЙ ВВОДА
         *  - ЗАМЕНА ЦВЕТА ШРИФТА КОГДА НАЧИНАЕМ ПИСАТЬ
         *  - УДАЛЕНИЯ СТАНДАРТНОГО ТЕКСТА В ПОЛЕ
         *
         * @param <type> $input_id - ID элемента
         * @param <type> $input_class_new - класс, который присваивается, когда пишем
         */
	function make_java_text_effect($input_id, $input_class_new){ /* ЭФФЕКТ ЗАМЕНЫ ЦВЕТА ПРИ ВВОДЕ ТЕКСТА */ 
		 ?> onblur="if(this.value=='') { this.value=this.title; $('#<?=$input_id ?>').removeClass('<?=$input_class_new ?>'); }" onfocus="if(this.value==this.title) { this.value=''; $('#<?=$input_id ?>').addClass('<?=$input_class_new ?>'); } " <?
	}
	
	
	
}
?>
