<? innerfade::java_init(); ?>
<script type="text/javascript">
        $(document).ready(function() {
                <? innerfade::ready_init(); ?>
        });
</script>
<ul id="baner-slideshow" style="padding-left:0px; text-align:left; "><?

	$component_foto = new component_foto( 0 );
	$component_foto->init( 'exfoto' );
	$component_foto->parent = 20;
	$rows = $component_foto->get_fotos();
	
	foreach ($rows as $r){
		print "<li><img src=\"/images/foto/".$r->org."\" alt=\"\" /></li>\n";
	}
	?>
</ul>
