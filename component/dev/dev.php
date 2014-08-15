<?php
global $reg;
defined( '_VALID_INSITE' ) or die( 'Direct Access to this location is not allowed.' );

$task 	= mosGetParam( $_REQUEST, 'task', 'all' );	//ggtr ($task);

switch ( $task ) {
	case 'time':
		dev_time_form();
		break;
	case 'install':
		dev_install();
		break;	
	case 'strtolower':
		dev_strtolower();
		break;
	case 'chars':
		dev_chars();
		break;

	case 'all':
	default:
		dev_allforms();
		break;
}

//ggtr(  urlencode('aaa?var=nnn&var1=mmm')  );

function dev_allforms(){
	dev_time_form();		?><hr /><?
	dev_strtolower();		?><hr /><?
	?><a href="/dev/chars">Таблица символов</a><?	?><hr /><?

}
function dev_chars(){
	for ($i=1; $i<255; $i++){
		$symbol = '&#'.num::fillzerro($i,3).';';
		print $symbol ?> &nbsp; &rarr; <? print  htmlentities ($symbol); ?><br /><?
	}
}
function dev_strtolower(){
	$_REQUEST['str'] = urldecode($_REQUEST['str']);
	?><form action="/dev/strtolower" name="timeform" method="get"><?
		if (ggrr('str')=='') $_REQUEST['str']="ЙЦУКЕНГШЩЗХЪФЫВАПРОЛДЖЭЯЧСМИТЬБЮйцукенгшщзхъфывапролджэячсмитьбю";
		?>ПРОВЕРКА strtolower()<br />
		<input type="text" name="str" value="<?=stripslashes($_REQUEST['str']); ?>" /><br />
		результат:	<?=strtolower($_REQUEST['str']); ?>
	</form>
	<?
}

function dev_time_form(){
	?><form action="/dev/time" name="timeform" method="get">
		ВРЕМЯ&nbsp;сейчас:&nbsp;<? print time(); ?><br />
		<input type="text" name="time" value="<?=$time; ?>" /><br />
		введено:	<?
			if (ggri('time')>0) ggr(  getdate(ggri('time'))  );
		?>
	</form>
	<?
}



function dev_install(){
	?>значение переменной site_path:<br /><?
		ggtr01( substr(__FILE__,  0,  strlen(__FILE__)-22) );
	
}
?>