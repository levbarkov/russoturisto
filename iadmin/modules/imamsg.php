<?php


// no direct access
defined( '_VALID_INSITE' ) or die( 'Доступ ограничен' );

$mosmsg = isset($_REQUEST['mosmsg']) ? $_REQUEST['mosmsg'] : "";
if (  $mosmsg  ) {	
	// limit mosmsg to 200 characters
	if ( strlen( $mosmsg ) > 200 ) {
		$mosmsg = substr( $mosmsg, 0, 200 );
	}	
	?>
	<div class="message">
		<?php echo $mosmsg; ?>
	</div>
	<?php
}
?>