<div id="home-catalog-block">
						<a href="#" onclick="return toggleCatalogState()" class="home-catalog-toggler-upper">
							<span class="home-catalog-toggler-view-open">Посмотреть каталог</span>
							<span class="home-catalog-toggler-view-close">Скрыть каталог</span>
						</a>
						<div id="home-catalog-pixline"></div>
						<div id="home-catalog-wrapper">
							<? $fotocats = ggsql ("select * from #__exgood where parent=17 order by #__exgood.order limit 0, 8  ");
							$icats_per_row = 4;
							$icats_index = 0; ?>
							<table id="home-catalog">
								<tr><?
								foreach ($fotocats as $fotocat){ 
									if (  ($icats_index>0)  &&  ($icats_index % $icats_per_row==0)  ) { ?></tr><tr><? } ?>
									<td width="<? print round(100/$icats_per_row); ?>%">
										<div class="home-catalog-item">
											<div class="home-catalog-image"><?
												shadow_effect('<a href="'.$fotocat->sefnamefullcat.'/'.$fotocat->sefname.'.html" ><img src="/images/ex/good/'.$fotocat->small.'" /></a>');
											?></div>
											<a href="<?=$fotocat->sefnamefullcat.'/'.$fotocat->sefname ?>.html" class="home-catalog-link"><?=stripslashes($fotocat->name) ?></a>
											<div class="home-catalog-description"><? 
												print trim(  str::get_substr_clean($fotocat->fdesc, 100)  ); 
											?></div>
										</div>
									</td><?
									$icats_index++;
								} ?>
								</tr>
							</table>
						</div>
						<a href="#" onclick="return toggleCatalogState()" class="home-catalog-toggler-lower"><span class="home-catalog-toggler-view-close">Скрыть каталог</span></a>
					</div>