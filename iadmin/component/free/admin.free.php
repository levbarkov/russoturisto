<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my, $database;

if (  $my->gid<23  ) {
	mosRedirect( 'index2.php?', _NOT_AUTH );
}
$nullDate = $database->getNullDate();
?>
<table class="adminheading">
	<tr>
		<td width="100%" >
			Глобальная разблокировка
		</td>
	</tr>
</table>

<?php
$tables = $database->getTableList();
$k = 0;
foreach ($tables as $tn) {
	// make sure we get the right tables based on prefix
	if (!preg_match( "/^".$mosConfig_dbprefix."/i", $tn )) {
		continue;
	}
	$fields = $database->getTableFields( array( $tn ) );

	$foundCO = false;
	$foundCOT = false;
	$foundE = false;

	$foundCO	= isset( $fields[$tn]['checked_out'] );
	$foundCOT	= isset( $fields[$tn]['checked_out_time'] );
	$foundE		= isset( $fields[$tn]['editor'] );

	if ($foundCO && $foundCOT) {
		if ($foundE) {
			$query = "SELECT checked_out, editor"
			. "\n FROM $tn"
			. "\n WHERE checked_out > 0"
			;
		} else {
			$query = "SELECT checked_out"
			. "\n FROM $tn"
			. "\n WHERE checked_out > 0"
			;
		}
		$database->setQuery( $query );
		$res = $database->query();
		$num = $database->getNumRows( $res );
		$k += $num;

		if ($foundE) {
			$query = "UPDATE $tn"
			. "\n SET checked_out = 0, checked_out_time = " . $database->Quote( $nullDate ) . ", editor = NULL"
			. "\n WHERE checked_out > 0"
			;
		} else {
			$query = "UPDATE $tn"
			. "\n SET checked_out = 0, checked_out_time = " . $database->Quote( $nullDate )
			. "\n WHERE checked_out > 0"
			;
		}
		$database->setQuery( $query );
		$res = $database->query();
	}
}
?>
			<strong>Разблокировано объектов: <? print $k; ?></strong>
