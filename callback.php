<?
$message .= 'Имя: '.$_POST['name'].'<br>';
$message .= 'Телефон: '.$_POST['telephone'].'<br>';

$from = "site@travelclubrusso.ru";
$eol = "\n";

$headers = "From: ".$from.$eol;
$headers .= "MIME-Version: 1.0".$eol;
$headers .= "Content-Type: text/html; charset=\"utf-8\"".$eol;

// Отправляем
if(mail('russoturisto1@mail.ru', 'Сайт: запрос на обратный звонок от имени '.$_POST['name'].' и телефон '.$_POST['telephone'], $message, $headers))
	echo 'OK';
//mail('morozov@bureauit.ru', 'bureauit.ru/ladding Новое письмо с сайта БюроИТ', $message, $headers);
?>
