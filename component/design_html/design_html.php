<?
	$designhtml = ggo('designhtml', "#__content", 'sefname');
	print desafelySqlStr( $designhtml->fulltext );
	
	global $reg;
	$verstka = new verstka();
	$total = 2000; 		// всего объектов
	$limitstart = 70; 	// номер объекта с которого начинаем отображение в реальном компоненте
	$limit = 10; 		// количество объектов на странице
	$verstka->pageNavigation ($total, $limitstart, $limit);	
?>