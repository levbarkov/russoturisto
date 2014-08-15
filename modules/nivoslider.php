<? nivoslider::java_init(); ?>
<script type="text/javascript">
        $(document).ready(function() {
                <? nivoslider::ready_init(); ?>
        });
</script>
<div id="nivo_slider"><?

	$component_foto = new component_foto( 0 );
	$component_foto->init( 'exfoto' );
	$component_foto->parent = 20;
	$rows = $component_foto->get_fotos();
	
	foreach ($rows as $r){
		?><img src="/images/foto/<?=$r->org; ?>" width="640" height="480"  /><?
	}
?></div><br />
