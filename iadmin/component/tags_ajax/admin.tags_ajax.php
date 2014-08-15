<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my, $reg, $id;
$cid = josGetArrayInts( 'cid' );

if (  $reg['task']==''  ) return;
$function_name = $reg['task'];
$function_name();

function showtags(){
	global $reg;
	?><div id="tags_select_list"><?
		$all_sets = ggsql (  "select * from #__tags order by name"  );
		?><table cellpadding="1" cellspacing="0"><?
			foreach ( $all_sets as $name1){
				?><tr class="rowajax"><td width="16"></td><?
				?><td><a href="<?
					if (  1  ){ ?>javascript: $('#exgood_tags').val( $('#exgood_tags').val()+', <?=$name1->name ?>' ); void(0);<? }
					else { ?><? } ?>" ><?=just_del_quotes($name1->name) ?></a></td>
					<td><?
					?></td>
				</tr><?
			}
		?></table><?
	?></div><?
}

?>
