<?php
global $reg;
defined( '_VALID_INSITE' ) or die( 'Direct Access to this location is not allowed.' );
$mail2sms = new mail2sms();

if (  ggrr('sms_tel2')!=''  &&  urldecode(ggrr('sms_tel2'))!='210-9659'  ){
	$mail2sms->tel = urldecode(ggrr('sms_tel1')).urldecode(ggrr('sms_tel2'));
	$mail2sms->tel = preg_replace("/[- ]/", "", $mail2sms->tel);
	$mail2sms->oper = ggrr('operator');
	$mail2sms->text = urldecode(ggrr('sms_text'));
	
	$mail2sms->sendSms();
	print 'Письмо отправлено на '.$mail2sms->mail_addr;
}
?>



<br />
<br />
<br />
<?=$mail2sms->helpMe(); ?>

