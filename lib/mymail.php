<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

/*
 *
 * КЛАСС ДЛЯ ОТПРАВКИ ПОЧТОВЫХ СООБЩЕНИЙ
 *
 */
class mymail{
	var $id;
	var $mail;

	function __construct(){
		global $reg;
		$this->mail             = new PHPMailer();

		if (  $reg['mail_sender']=='smtp'  )    $this->mail->IsSMTP();  // telling the class to use SMTP server
                else                                    $this->mail->IsMail();  // telling the class to use mail php function
		
		$this->mail->Encoding = '8bit';
		$this->mail->CharSet  = 'UTF-8';
//		$this->mail->SetLanguage('ru');
		if (  $reg['mail_sender']=='smtp'  ) {
			if (  $reg['send_from_debug_smtp']==1  )   //отправляем через ПРОВЕРЕННЫЙ ЯЩИК GMAIL
			{
			    $this->mail->SMTPDebug  = $reg['mail_debug'];		// enables SMTP debug information (for testing)
											// 1 = errors and messages
											// 2 = messages only
			    $this->mail->SMTPAuth   = true;           			// enable SMTP authentication
			    $this->mail->SMTPSecure = 'ssl';
			    $this->mail->Host       = 'smtp.gmail.com';		// sets the SMTP server
			    $this->mail->Port       = '465';                    // set the SMTP port for the GMAIL server
			    $this->mail->Username   = '2955591@gmail.com';	// SMTP account username  ( ivanov@firma.ru )
			    $this->mail->Password   = 'megagmailvfcnth';	// SMTP account password
			    $this->mail->SetFrom($reg['mail_username'], desafelySqlStr($reg['mail_from_name']));
			    $this->mail->AltBody    = "Для просмотра сообщения включите поддержку HTML!";
			    //$this->mail->MsgHTML($body);
			} 
			else // используем настройки почтового сервера клиента
			{
			    $this->mail->SMTPDebug  = $reg['mail_debug'];		// enables SMTP debug information (for testing)
											// 1 = errors and messages
											// 2 = messages only
			    $this->mail->SMTPAuth   = true;           			// enable SMTP authentication
			    if (  $reg['mail_smtp_secure']!=''  ) $this->mail->SMTPSecure = $reg['mail_smtp_secure'];
			    $this->mail->Host       = $reg['mail_host'];		// sets the SMTP server
			    $this->mail->Port       = $reg['mail_port'];		// set the SMTP port for the GMAIL server
			    $this->mail->Username   = $reg['mail_username'];	// SMTP account username  ( ivanov@firma.ru )
			    $this->mail->Password   = $reg['mail_password'];	// SMTP account password
			    $this->mail->SetFrom($reg['mail_username'], desafelySqlStr($reg['mail_from_name']));
			    $this->mail->AltBody    = "Для просмотра сообщения включите поддержку HTML!";
			    //$this->mail->MsgHTML($body);
			}
		} else {
			$this->mail->SetFrom($reg['mail_username'], desafelySqlStr($reg['mail_from_name']));
			#if($_SERVER['REMOTE_ADDR']=='188.0.30.39') { ggd($reg['mail_from_name']); }
		}
	}


	function attach_file($path, $name = '', $encoding = 'base64', $type = 'application/octet-stream') {
		$this->mail->AddAttachment($path, $name, $encoding, $type);
	}
	
	function set_subject($txt){
		$this->mail->Subject    = $txt;
	}
	
	function set_body ( $body ){
		$this->mail->MsgHTML($body);		
	}
	function set_alt_body ( $body ){
		$this->mail->AltBody = $body;
	}

	function is_html($bool){
		$this->mail->IsHTML($bool);
	}
	
	function add_address($address){
		if (  JosIsValidEmail($address)  )	$this->mail->AddAddress($address, "");
	}
	
	function clear_addresses(){
		$this->mail->ClearAddresses();
	}
	
	function send(){
		if(!$this->mail->Send()) {
		  // echo "Mailer Error: " . $mail->ErrorInfo;
		} else {
		  // echo "Message sent!";
		}

	}
}
?>