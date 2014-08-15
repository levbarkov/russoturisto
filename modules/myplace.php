<?php
defined( '_VALID_INSITE' ) or die( 'Доступ ограничен' );	global $reg, $my; 
$imodule = ggo("myplace", "#__modules", "module");	$params  = new mosParameters($imodule->params);
/*****
$params->def('name', 'default_value') ?>
mode	String: 			tags|cats|both	Tells the movie to expect and display tags, categories or both.
*****/
if ($my->id){
?><table border="0" width="100%" align="left">
	<tr>
		<td><?
			$component_foto = new component_foto( 0 );
			$component_foto->init( 'user_main' );
			print $component_foto->createPreviewFotoLink ( 'small', 'org', $my, $reg['uinoimage'], '', ' border="0" ', '/cab'  );
		?></td>
	</tr>
</table><?
}
else{
?><?
}
?>