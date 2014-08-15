<?	global $reg;
	defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
	$imodule = ggo("news", "#__modules", "module");	$params  = new mosParameters($imodule->params);
	//получение ID категории и количества отображаемых новостей
	$catid = $params->def('catid', '2');
	$show_news = $params->def('show_news', '2');
	$preview_length = $params->def('preview_length', '50');
	$gnews = ggsql ("select * from #__content where catid=$catid and `state` order by id desc limit 0,$show_news "  );
	?><h3><a href="/main/">Новости</a></h3><?
	foreach ($gnews as $gnew){ 
	?><div class="home-additional-news-item">
		<h4><a href="<?=$gnew->sefnamefullcat ?>/<?=$gnew->sefname ?>.html"><?=desafelysqlstr($gnew->title) ?></a></h4>
		<p><? print strip_tags($gnew->introtext)==''?str_replace("&nbsp;", " ", str::get_substr_clean($gnew->fulltext, $preview_length)):str_replace("&nbsp;", " ", str::get_substr_clean($gnew->introtext, $preview_length)); ?></p>
	</div><? 
	}
// отображаем кнопку редактирования новостей
editme( 'icat_list', array('id'=>$catid, 'note'=>'редактировать новости<br>'), 'small' );
editme( 'module', array('id'=>23, 'note'=>'параметры отображения'), 'small' );
?>