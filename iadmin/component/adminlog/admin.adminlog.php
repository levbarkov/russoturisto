<?php
// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my, $task, $id, $reg;
$cid = josGetArrayInts( 'cid' );
switch ($task) {
	default:			showeasylist( $option );
						break;
}


function showeasylist( $option ) {
	global $database, $my, $iConfig_list_limit, $reg;
	$filter_type	= getUserStateFromRequest( 'filter_type', 0 );
	$filter_logged	= intval( getUserStateFromRequest(  'filter_logged', 0 ) );
	$limit 			= intval( getUserStateFromRequest( 'limit', 3000 ) );
	$limitstart 	= intval( getUserStateFromRequest( 'limitstart', 0 ) );
	
	?><form action="index2.php" method="post" name="adminForm">
	<table class="adminheading" ><tr><td width="100%"><?
		$iway[0]->name="Лог операций";
		$iway[0]->url="";

		i24pwprint_admin ($iway);
	?></td></tr></table><?

	$ixml = array();
	$adminlog = new adminlog();	$donext = $adminlog->get_log($ixml, $limitstart, $limit);
	?><table border="0" width="100%">
	<tr>
		<td width="100%" valign="top" style="vertical-align:top">
			<table class="adminlist">
                <tbody>
					<tr>
                        <th class="title">Действие</th>
						<th class="title">ID объекта</th>
						<th class="title">Время</th>
						<th class="title">Логин пользователя</th>
						<th class="title">ID пользователя</th>
                        <th class="title">ip пользователя</th>
	                </tr>
				<?			
				foreach ( $ixml as $row ){	
				$rowi = 1 - $rowi;
				?><tr class="row<? print $rowi; ?>">
						<td align="left"><? print ($row->act); ?></td>
						<td><? print $row->id; ?></td>
						<td><? print $row->mod; ?></td>
						<td><? print $row->u; ?></td>
						<td><? print $row->uid; ?></td>
						<td><? print $row->ip; ?></td>
                 </tr>			 
				 <? } ?>
				<tr height="35px">
                        <td colspan="6" align="center" valign="middle" style="text-align:center; vertical-align:middle; "><?
							if (  $limitstart>0  ){	?><a href="index2.php?ca=<? print $option; ?>&limitstart=<?=($limitstart-$limit) ?>" class="imagelist_class">&larr; Предыдущая страница</a> <?	}
							if (  $donext  ){		?><a href="index2.php?ca=<? print $option; ?>&limitstart=<?=($limitstart+$limit) ?>" class="imagelist_class">Следующая страница &rarr;</a><?	}
						?></td>
                </tr>
           </tbody></table>
		</td>
	</tr>
</table>
		<input type="hidden" name="ca" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>
		<?php
}


?>