<?
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

switch ($_REQUEST['s'])
{
	case 'subs': 		subs();			break;
	case 'tour': 		tour();			break;
	case 'visa': 		visa();			break;
	case 'call': 		call();			break;
	case 'tour_r': 		tour_r();		break;
	case 'visa_r': 		visa_r();		break;
	case 'call_r': 		call_r();		break;

	default: 			none();			break;
}




function none()
{
	echo "Ошибка! ping.";
}



function subs()
{
	if(!$_REQUEST[email]){ msg_error('Введите e-mail!'); } 
	
	$body = <<<HTML
					<h1>Письмо на подписку</h1>
					<p>e-mail: {$_REQUEST[email]}</p>
HTML;
	ping_send_mail(4, $body);
}



function tour()
{
	?>
	<div class="iframe">
		<form action="" class="form" id="jq_form">
			<h1>Заявка на расчет тура</h1>
			<p>«Заполните заявку с Вашими пожеланиями, и мы оперативно свяжемся с Вами предложив вам самые выгодные предложения и интересные варианты отдыха!».</p>
			<label for="for1">Имя<sup>*</sup></label>
			<input id="for1" class="input" type="text" name="name" required />
			<label for="for2">Телефон<sup>*</sup></label>
			<input id="for2" class="input" type="tel" name="tel" required />
			<label for="for3">e-mail</label>
			<input id="for3" class="input" type="email" name="email" />
			<label for="for4">Удобное время для звонка</label>
			<div class="inp_small">c <input id="for4" class="input" type="time" name="from" /> до <input class="input" type="time" name="to" /></div>
			<label for="for5">Детали поездки, пожелания</label>
			<textarea id="for5" class="input" name="text"></textarea>
			<h5 class="jq_data"><sup>*</sup>Поля отмеченный звездочкой обязательны к заполнению</h5>
			<input type="hidden" name="c" value="ping" />
			<input type="hidden" name="s" value="tour_r" />
			<input type="submit" class="btn2 fr" value="Отправить">
			<div class="clear"></div>
		</form>
	</div>
	<?
}
function tour_r()
{
	if(!$_REQUEST[name] || !$_REQUEST[tel]){ msg_error('Заполните обязательные поля, отмеченные звездочкой!'); }

	$body = <<<HTML
					<h1>Заявка на расчет тура</h1>
					<p>Имя: {$_REQUEST[name]}</p>
					<p>Телефон: {$_REQUEST[tel]}</p>
					<p>e-mail: {$_REQUEST[email]}</p>
					<p>Удобное время для звонка: с {$_REQUEST[from]} до {$_REQUEST[to]}</p>
					<p>Детали поездки, пожелания: {$_REQUEST[text]}</p>
HTML;

	ping_send_mail(1, $body);
}



function visa()
{
	?>
	<div class="iframe">
		<form action="" class="form" id="jq_form">
			<h1>Заявка на визу</h1>
			<label for="for1">Имя<sup>*</sup></label>
			<input id="for1" class="input" type="text" name="name" required />
			<label for="for2">Телефон<sup>*</sup></label>
			<input id="for2" class="input" type="tel" name="tel" required />
			<label for="for3">e-mail</label>
			<input id="for3" class="input" type="email" name="email" />	
			<label for="for6">Страна</label>
			<input id="for6" class="input" type="text" name="country" />
			<label for="for4">Сроки поездки</label>
			<div class="inp_small f2">c <input id="for4" class="input" type="date" name="from" /> до <input class="input" type="date" name="to" /></div>
			<h5 class="jq_data"><sup>*</sup>Поля отмеченный звездочкой обязательны к заполнению</h5>
			<input type="hidden" name="c" value="ping" />
			<input type="hidden" name="s" value="visa_r" />
			<input type="submit" class="btn2 fr" value="Отправить">
			<div class="clear"></div>
		</form>
	</div>
	<?
}
function visa_r()
{
	if(!$_REQUEST[name] || !$_REQUEST[tel]){ msg_error('Заполните обязательные поля, отмеченные звездочкой!'); }
	
	$body = <<<HTML
					<h1>Заявка на визу</h1>
					<p>Имя: {$_REQUEST[name]}</p>
					<p>Телефон: {$_REQUEST[tel]}</p>
					<p>e-mail: {$_REQUEST[email]}</p>
					<p>Страна: {$_REQUEST[country]}</p>
					<p>Сроки поездки: с {$_REQUEST[from]} до {$_REQUEST[to]}</p>
HTML;

	ping_send_mail(2, $body);
}



function call()
{
	?>
	<div class="iframe big">
		<form action="" class="form" id="jq_form">
			<h1>Заявка на обратный звонок</h1>
			<div class="bk bk1">
				<label for="for1">Имя<sup>*</sup></label>
				<input id="for1" class="input" type="text" name="name" required />
				<label for="for2">Телефон<sup>*</sup></label>
				<input id="for2" class="input" type="tel" name="tel" required />
			</div>
			<div class="bk bk2">
				<label for="for7">Отчество</label>
				<input id="for7" class="input" type="text" name="sirname" />
				<label for="for4">Удобное время для звонка</label>
				<div class="inp_small">c <input id="for4" class="input" type="time" name="from" /> до <input class="input" type="time" name="to" /></div>
			</div>
			<div class="clear"></div>
			<h5 class="jq_data"><sup>*</sup>Поля отмеченный звездочкой обязательны к заполнению</h5>
			<input type="hidden" name="c" value="ping" />
			<input type="hidden" name="s" value="call_r" />
			<input type="submit" class="btn2 fr" value="Отправить">
			<div class="clear"></div>
		</form>
	</div>
	<?
}
function call_r()
{
	if(!$_REQUEST[tel]){ msg_error('Введите номер телефона!'); }
	
	$body = <<<HTML
					<h1>Заявка на обратнрый звонок</h1>
					<p>Имя: {$_REQUEST[name]}</p>
					<p>Телефон: {$_REQUEST[tel]}</p>
					<p>Отчество: {$_REQUEST[sirname]}</p>
					<p>Удобное время для звонка: с {$_REQUEST[from]} до {$_REQUEST[to]}</p>
HTML;

	ping_send_mail(3, $body);
}




function ping_send_mail($id_mail, $body)
{
	global $reg;
	$mymail = new mymail();
	
	$rows = ggsql(" select name, val from #__config where component='backlink'; ");
	foreach($rows as $row){ $conf[$row->name] = $row->val; }
	#xmp($conf);

	$mail_to      = "{$id_mail}_mail_to"; 
	$mail_subject = "{$id_mail}_mail_subject"; 
	$mail_result  = "{$id_mail}_mail_result"; 
	$metrika1  	  = "{$id_mail}_metrika"; 
	$metrika  	  = $conf[$metrika1]; 

	
	$mail_to_arr = explode(',', $conf[$mail_to]);
	
	foreach($mail_to_arr as $mail_to)
	{
		$mymail->add_address(  trim($mail_to)  );
	}
		$mymail->set_subject ( $conf[$mail_subject] );
		$mymail->set_body	 ( $body );
		$mymail->send();
	
	echo "<b class='true'>{$conf[$mail_result]}</b>";
	
	if($metrika){ echo "<script>yaCounter23291488.reachGoal('{$metrika}');</script>"; }
}

function msg_error($msg='Ошибка.')
{
	echo "<b class='error'>{$msg}</b>";
	exit();
}









?>