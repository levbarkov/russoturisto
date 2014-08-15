<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my, $reg, $id;
require_once( site_path.'/component/ex/ex_lib.php' );
$cid = josGetArrayInts( 'cid' );

if (  $reg['task']==''  ) return;
$function_name = $reg['task'];
$function_name();

function foto_limit(){
	global $reg;

	?><table cellpadding="4" cellspacing="0" width="500"><?

	?><tr>
		<td colspan="3">На загрузку фото на хостинге установленны следующие ограничения</td>
	</tr><?
	?><tr class="rowajax">
		<td>Максимальный размер для загрузки на сервер</td>
		<td><strong><? echo ini_get ( 'upload_max_filesize' ); ?></strong></td>
		<td class="input_gray">upload_max_filesize</td>
	</tr><?
	?><tr class="rowajax">
		<td>Максимальный размер для передачи файлов</td>
		<td><strong><? echo ini_get ( 'post_max_size' ); ?></strong></td>
		<td class="input_gray">post_max_size</td>
	</tr><?
	?><tr>
		<td colspan="3">&nbsp;</td>
	</tr><?
	?><tr>
		<td colspan="3">Фотографии с большим расширением, например с фотоаппарата могут не загружаться на сайт, так как для их обработки нехватит производительности сервера.</td>
	</tr><?
	?><tr>
		<td colspan="3">Для решения данной проблемы необходимо уменьшить их размер, рекомендуемый размер - не более 800 px по ширине или высоте</td>
	</tr><?
	?><tr>
		<td colspan="3">Список сайтов для обработки фото онлайн: <a href="http://www.pixlr.com/editor/?loc=ru" target="_blank">foto-editor</a> &nbsp; <a href="http://mypictureresize.com/" target="_blank">mypictureresize</a> &nbsp; 
		</td>
	</tr><?
	
	
	?></table><?
}
?>