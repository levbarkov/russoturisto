<?	global $reg;
	defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
	//получение ID категории и количества отображаемых новостей
	$propid = $params->propid;
	$names = ggsql ("select * from #__names where propid=$propid and `publish` order by id desc  "  ); 
	$icats_per_row = 4;
	$icats_index = 0;
	?><table id="home-additional-logos-table" border="0" width="100%" align="center" >
	<tr><?
	foreach ($names as $name){
		if (  ($icats_index>0)  &&  ($icats_index % $icats_per_row==0)  ) { ?></tr><tr><? }
		?><td nowrap="nowrap" width="<? print round(100/$icats_per_row); ?>%" valign="top" align="left" style="text-align:left; vertical-align:top; "><?
				?><div class="home-additional-wrapper-left" onmouseover="BrandColor(<?=$name->id ?>,'over')" onmouseout="BrandColor(<?=$name->id ?>,'out')"><?
					?><a href="#"  ><?
						?><img src="/images/names/<?=$name->small ?>" id="cnt-brand-logo-norm-<?=$name->id ?>" class="js-hidden" /><?
						?><img src="/images/names/<?=$name->mid ?>" id="cnt-brand-logo-shad-<?=$name->id ?>" /><?
					?></a><?
				?></div><?
			?></td><?
		$icats_index++;
	}
	?></tr></table>