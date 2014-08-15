<?php

/**
 * Класс для работы с пользователями
 *
 * @author George
 */


class mail2sms {
	var $tel;
	var $oper;
	var $text;
	var $subject;
	function __construct(&$row=""){ 
	}

	function sendSms(){
		global $reg;
		if (  $reg['mail2sms_enable']==0  ) return;
		if (  $this->subject==""  ) $subject = short_surl();
		else $subject = $this->subject;

		switch (  $this->oper  ){
			case 'etk' : 			$mail_addr = '+7'.$this->tel."@sms.etk.ru"; break;
			case 'beeline' :		$mail_addr = '7'.$this->tel."@sms.beemail.ru"; break;
			case 'megafon_sibir' : 	$mail_addr = '+7'.$this->tel."@sms.megafonsib.ru"; break;
			case 'mts' : 			$mail_addr = '7'.$this->tel."@sms.mtslife.ru"; break;
			default    : 			$mail_addr = ""; break;
		}
		if (  $this->oper == "megafon_sibir"  ){
			$this->subject = @iconv("UTF-8", "KOI8-R", $this->subject);
			$this->text = @iconv("UTF-8", "KOI8-R", $this->text);
		}
		$this->mail_addr = $mail_addr;
		if (  $mail_addr!=''  ){
			$mymail = new mymail();
			$mymail->add_address ( $mail_addr );
			$mymail->set_subject ( $subject );
			$mymail->set_body	 ( $this->text );
			$mymail->set_alt_body( $this->text );
			$mymail->is_html(false);
			$mymail->send ();
		}
		
	}

        function helpMe(){
            ?><br />
ЕСЛИ ВЫ НЕ ПОЛУЧИЛИ ПИСЬМО, ТО ВОЗМОЖНО У ВАС НЕПОДКЛЮЧЕНА УСЛУГА, ДЛЯ ЕЕ ВКЛЮЧЕНИЯ НЕОБХОДИМО
<br />
<br />
<i>ЕТК-Красноярск</i><br />
Отправить смс на номер 6040 с текстом &laquo;email&raquo;<br />
<br />
<br />
<i>BeeLine</i><br />
Позвонить по бесплатному номеру 06849909 (включение сервиса приема смс через e-mail)<br />
<br />
<br />
<i>Megafon-Сибирь</i><br />
Отправить смс на номер 508 с анг. символом &laquo;C&raquo;<br />
Внимание! Обязательно наличие символа "+" перед номером абонента.<br />
Например:<br />
+79230000000@sms.megafonsib.ru - правильный e-mail адрес абонента. <br />
79230000000@sms.megafonsib.ru - неправильный e-mail адрес абонента. <br />
Источник: <a href="http://sibir.megafon.ru/services/misc/email2sms/" target="_blank">http://sibir.megafon.ru/services/misc/email2sms/</a><br />
<br />
<br />
<i>МТС</i><br />
При добавлении услуги «SMS to e-mail/e-mail to SMS» вам автоматически предоставляется адрес электронной почты вида:<br />
7913ххххххх@sms.mtslife.ru, где ххххххх — 7 последних цифр номера Вашего мобильного телефона в федеральном формате.<br />
Электронное письмо, отправленное на адрес: 7913ххххххх@sms.mtslife.ru поступит на Ваш мобильный телефон с короткого номера 0883 в виде SMS -сообщения.<br />
Для того, чтобы воспользоваться услугой, необходимо ее подключить через <a href="https://issa.sib.mts.ru/selfcare/" target="_blank">«Интернет-помощник»</a><br />
Далее идем на страницу быстрого добавления новой услуги - <a href="https://issa.sib.mts.ru/selfcare/product-add.aspx">https://issa.sib.mts.ru/selfcare/product-add.aspx</a> и добавляем услугу «SMS to e-mail/e-mail to SMS»
<br />
<br /><?
        }

}
?>
