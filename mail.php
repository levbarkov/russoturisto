<?php
header("Content-Type: text/html; charset=windows-1251");
//Если форма отправлена
if(isset($_POST['submit'])) {

	//Проверка правильности ввода EMAIL
	if(trim($_POST['email']) == '')  {
		$hasError = true;
	} else {
		$email = trim($_POST['email']);
	}

	//Проверка наличия ТЕКСТА сообщения
	if(trim($_POST['message']) == '') {
		$hasError = true;
	} else {
		if(function_exists('stripslashes')) {
			$comments = stripslashes(trim($_POST['message']));
		} else {
			$comments = trim($_POST['message']);
		}
	}
	
	//Если ошибок нет, отправить email
	if(!isset($hasError)) {
		$emailTo = 'russoturisto1@mail.ru'; //Сюда введите Ваш email
		$subject = trim($_POST['subject']);
		$message = '<html> 
    <head>
		<meta http-equiv="Content-Type" content="text/html; charset=windows-1251"> 
        <title>Туристическое агенство Руссо Туристо</title> 		
	</head>
	<body style="background-color:#91d3f3;margin: 0;padding: 0;"> 

<table cellpadding="0" cellspacing="0" border="0" width="100%" bgcolor="91d3f3" style="background-image:url(\'http://travelclubrusso.ru/mail_images/background.jpg\');background-repeat:no-repeat;background-position: center bottom;margin: 0;padding: 0;">
	<tbody> <tr>
		<td>
		<table align="center" cellpadding="0" cellspacing="0" border="0" width="871">
			<tbody> 
				<tr>
					<td height="20">&nbsp;</td>
				</tr>
				<tr>
					<td>
						<table cellpadding="0" cellspacing="0" border="0" width="85" height="126" align="center" style="background-image: url(\'http://travelclubrusso.ru/mail_images/header1.png\');">
									<tbody>
										<tr>
											<td>&nbsp;</td>
										</tr>
								</tbody>
							</table>
					</td>
					<td>
						<table cellpadding="0" cellspacing="0" border="0" width="45" height="126" align="center" style="background-image: url(\'http://travelclubrusso.ru/mail_images/header2.png\');">
									<tbody>
										<tr>
											<td>&nbsp;</td>
										</tr>
								</tbody>
							</table>
					</td>
					<td>
						<table cellpadding="0" cellspacing="0" border="0" width="610" height="126" align="center" style="background-image: url(\'http://travelclubrusso.ru/mail_images/header3.png\');">
							<tbody>
								<tr>
									<td width="13"></td>
									<td width="389"><img src="http://travelclubrusso.ru/mail_images/logo.png" /></td>
								</tr>
							</tbody>
						</table>
					</td>
					<td>
						<table cellpadding="0" cellspacing="0" border="0" width="46" height="126" align="center" style="background-image: url(\'http://travelclubrusso.ru/mail_images/header4.png\');">
									<tbody>
										<tr>
											<td>&nbsp;</td>
										</tr>
								</tbody>
							</table>
					</td>
					<td>
						<table cellpadding="0" cellspacing="0" border="0" width="85" height="126" align="center" style="background-image: url(\'http://travelclubrusso.ru/mail_images/header5.png\');">
									<tbody>
										<tr>
											<td>&nbsp;</td>
										</tr>
								</tbody>
							</table>
					</td>
				</tr>
				<tr>
					<td width="85">&nbsp;</td>
					<td bgcolor="f9f3e3">&nbsp;</td>
					<td>
						<table cellpadding="10" cellspacing="0" border="0" width="610" align="center" bgcolor="f9f3e3" style="min-height: 408px;">
						<tbody><tr><td valign="top"> '.$comments.'
						</td></tr></tbody>
						</table>
					</td>
					<td bgcolor="f9f3e3" style="background-image:url(\'http://travelclubrusso.ru/mail_images/footer6.png\');background-repeat:no-repeat;background-position: center bottom;">&nbsp;</td>
					<td width="85" style="background-image:url(\'http://travelclubrusso.ru/mail_images/footer7.png\');background-repeat:no-repeat;background-position: center bottom;">&nbsp;</td>
				</tr>
				<tr>
					<td>
					<table cellpadding="0" cellspacing="0" border="0" width="85" height="248" valign="bottom" align="center" style="background-image:url(\'http://travelclubrusso.ru/mail_images/footer1.png\');">
								<tbody>
									<tr>
										<td>&nbsp;</td>
									</tr>
							</tbody>
					</table>
					</td>
					<td>
					<table cellpadding="0" cellspacing="0" border="0" width="45" height="248" valign="bottom" align="center" style="background-image:url(\'http://travelclubrusso.ru/mail_images/footer2.png\');">
								<tbody>
									<tr>
										<td>&nbsp;</td>
									</tr>
							</tbody>
					</table>
					</td>
					<td>
					<table cellpadding="0" cellspacing="0" border="0" width="610" height="248" align="center" style="background-image:url(\'http://travelclubrusso.ru/mail_images/footer3.png\');">
					<tbody>
					<tr><td height="10"></td></tr>
					<tr>
						<td width="403"><p><b>Мы находимся по адресу:</b><br/>
			ООО «Клуб Путешественников «Руссо Туристо-ТК»<br/>
			г. Красноярск ул. Шахтеров, 18а<br/>
			(Мерседес клуб, 2 этаж, вход с торца)<br/></p></td>
						<td><p><b>Режим работы:</b><br/>
			пн. - пт. с 10.00 до 19.00<br/>
			сб. с 11.00 до 15.00<br/>
			выходной: воскресенье<br/></p></td>
					</tr>
					<tr><td height="125"></td></tr>
					</tbody>
					</table>
				</td>
					<td>
					<table cellpadding="0" cellspacing="0" border="0" width="46" height="248" valign="bottom" align="center" style="background-image:url(\'http://travelclubrusso.ru/mail_images/footer4.png\');">
								<tbody>
									<tr>
										<td>&nbsp;</td>
									</tr>
							</tbody>
					</table>
					</td>
					<td>
					<table cellpadding="0" cellspacing="0" border="0" width="85" height="248" valign="bottom" align="center" style="background-image:url(\'http://travelclubrusso.ru/mail_images/footer5.png\');">
								<tbody>
									<tr>
										<td>&nbsp;</td>
									</tr>
							</tbody>
					</table>
					</td>
				</tr>
			</tbody>
		</table>
		</td> 
		 </tr> </tbody>
</table>
</body>
</html>';

		$headers  = "Content-type: text/html; charset=windows-1251 \r\n";
		$headers .= "From: Туристическое агенство Руссо Туристо <".$emailTo.">\r\n";
		$headers .= "Reply-To: ". $email . "\r\n"; 

		mail($email, $subject, $message, $headers);
		$emailSent = true;
	}
}
?>

<?php if(isset($emailSent) && $emailSent == true) { ?>
<!DOCTYPE html>
<html> 
    <head>
		<meta http-equiv="Content-Type" content="text/html; charset=windows-1251"> 
        <title>Отправка письма</title> 		
	</head>
	<body style="background-color:#91d3f3;margin: 0;padding: 0;"> 

<table cellpadding="0" cellspacing="0" border="0" width="100%" bgcolor="91d3f3" style="background-image:url('/mail_images/background.jpg');background-repeat:no-repeat;background-position: center bottom;margin: 0;padding: 0;">
	<tbody> <tr>
		<td>
		<table align="center" cellpadding="0" cellspacing="0" border="0" width="871">
			<tbody> 
				<tr>
					<td height="20">&nbsp;</td>
				</tr>
				<tr>
					<td>
						<table cellpadding="0" cellspacing="0" border="0" width="85" height="126" align="center" style="background-image: url('/mail_images/header1.png');">
									<tbody>
										<tr>
											<td>&nbsp;</td>
										</tr>
								</tbody>
							</table>
					</td>
					<td>
						<table cellpadding="0" cellspacing="0" border="0" width="45" height="126" align="center" style="background-image: url('/mail_images/header2.png');">
									<tbody>
										<tr>
											<td>&nbsp;</td>
										</tr>
								</tbody>
							</table>
					</td>
					<td>
						<table cellpadding="0" cellspacing="0" border="0" width="610" height="126" align="center" style="background-image: url('/mail_images/header3.png');">
							<tbody>
								<tr>
									<td width="13"></td>
									<td width="389"><img src="/mail_images/logo.png" /></td>
								</tr>
							</tbody>
						</table>
					</td>
					<td>
						<table cellpadding="0" cellspacing="0" border="0" width="46" height="126" align="center" style="background-image: url('/mail_images/header4.png');">
									<tbody>
										<tr>
											<td>&nbsp;</td>
										</tr>
								</tbody>
							</table>
					</td>
					<td>
						<table cellpadding="0" cellspacing="0" border="0" width="85" height="126" align="center" style="background-image: url('/mail_images/header5.png');">
									<tbody>
										<tr>
											<td>&nbsp;</td>
										</tr>
								</tbody>
							</table>
					</td>
				</tr>
				<tr>
					<td width="85">&nbsp;</td>
					<td bgcolor="f9f3e3">&nbsp;</td>
					<td>
						<table cellpadding="10" cellspacing="0" border="0" width="610" align="center" bgcolor="f9f3e3" style="min-height: 408px;">
						<tbody><tr><td valign="top">
		<p><strong>Email успешно отправлен!</strong></p><br/>
			<?php echo $comments; ?>
		</td></tr></tbody>
						</table>
					</td>
					<td bgcolor="f9f3e3" style="background-image:url('/mail_images/footer6.png');background-repeat:no-repeat;background-position: center bottom;">&nbsp;</td>
					<td width="85" style="background-image:url('/mail_images/footer7.png');background-repeat:no-repeat;background-position: center bottom;">&nbsp;</td>
				</tr>
				<tr>
					<td>
					<table cellpadding="0" cellspacing="0" border="0" width="85" height="248" valign="bottom" align="center" style="background-image:url('/mail_images/footer1.png');">
								<tbody>
									<tr>
										<td>&nbsp;</td>
									</tr>
							</tbody>
					</table>
					</td>
					<td>
					<table cellpadding="0" cellspacing="0" border="0" width="45" height="248" valign="bottom" align="center" style="background-image:url('/mail_images/footer2.png');">
								<tbody>
									<tr>
										<td>&nbsp;</td>
									</tr>
							</tbody>
					</table>
					</td>
					<td>
					<table cellpadding="0" cellspacing="0" border="0" width="610" height="248" align="center" style="background-image:url('/mail_images/footer3.png');">
					<tbody>
					<tr><td height="10"></td></tr>
					<tr>
						<td width="403"><p><b>Мы находимся по адресу:</b><br/>
			ООО «Клуб Путешественников «Руссо Туристо-ТК»<br/>
			г. Красноярск ул. Шахтеров, 18а<br/>
			(Мерседес клуб, 2 этаж, вход с торца)<br/></p></td>
						<td><p><b>Режим работы:</b><br/>
			пн. - пт. с 10.00 до 19.00<br/>
			сб. с 11.00 до 15.00<br/>
			выходной: воскресенье<br/></p></td>
					</tr>
					<tr><td height="125"></td></tr>
					</tbody>
					</table>
				</td>
					<td>
					<table cellpadding="0" cellspacing="0" border="0" width="46" height="248" valign="bottom" align="center" style="background-image:url('/mail_images/footer4.png');">
								<tbody>
									<tr>
										<td>&nbsp;</td>
									</tr>
							</tbody>
					</table>
					</td>
					<td>
					<table cellpadding="0" cellspacing="0" border="0" width="85" height="248" valign="bottom" align="center" style="background-image:url('/mail_images/footer5.png');">
								<tbody>
									<tr>
										<td>&nbsp;</td>
									</tr>
							</tbody>
					</table>
					</td>
				</tr>
			</tbody>
		</table>
		</td> 
		 </tr> </tbody>
</table>
</body>
</html>

	<?php } else { ?>
	
<!DOCTYPE html>
<html>

<head>
	<title>Отправка письма</title>
	<meta http-equiv="content-type" content="text/html; charset=windows-1251" />
	<meta http-equiv="Content-Style-Type" content="text/css" />

<style type="text/css">
body {
	font-family:Arial, Tahoma, sans-serif;	margin: 0 auto;	width: 694px;
}
#contact-wrapper {
	border:1px solid #e2e2e2;
	background:#f1f1f1;
	padding:30px;
}
#contact-wrapper div {
	clear:both;
	margin:1em 0;
}
#contact-wrapper label {
	display:block;
	float:none;
	font-size:16px;
	width:auto;
}
form .contactform {
	border-color:#B7B7B7 #E8E8E8 #E8E8E8 #B7B7B7;
	border-style:solid;
	border-width:1px;
	padding:5px;
	font-size:16px;
	color:#333;
}.button{	color: #fff;	background-color: #428bca;	border-color: #357ebd;	display: inline-block;	margin-bottom: 0;	font-weight: 400;	text-align: center;	white-space: nowrap;	vertical-align: middle;	cursor: pointer;	-webkit-user-select: none;	-moz-user-select: none;	-ms-user-select: none;	user-select: none;	background-image: none;	border: 1px solid transparent;	padding: 10px 16px;	font-size: 18px;	line-height: 1.33;	border-radius: 6px;}.button:hover, .button:focus, .button:active{	background-color: #3071a9;	border-color: #285e8e;}
</style>
<script src="//cdn.ckeditor.com/4.4.2/standard/ckeditor.js"></script>
</head>

<body>
	<div id="contact-wrapper">

	<?php if(isset($hasError)) { //Если найдены ошибки ?>
		<p class="error">Проверьте, пожалуйста, правильность заполения всех полей.</p>
	<?php } ?>

	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >

		<div>
			<label for="email"><strong>Email:</strong></label>
			<input type="text" size="50" name="email" id="email" value="" class="contactform"/>
		</div>

		<div>
			<label for="subject"><strong>Тема:</strong></label>
			<input type="text" size="50" name="subject" id="subject" value="" class="contactform"/>
		</div>

		<div>
			<label for="message"><strong>Сообщение:</strong></label>
			<textarea name="message" id="message"></textarea>
			<script type="text/javascript">                CKEDITOR.replace( 'message' );            </script>
		</div>
	    <input type="submit" value="Отправить письмо" name="submit" class="button"/>
	</form>
	</div>
</body>
</html>
<?php }?>