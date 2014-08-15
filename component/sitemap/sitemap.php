<?php
global $reg;
defined( '_VALID_INSITE' ) or die( 'Direct Access to this location is not allowed.' );
require_once( 'sitemap_lib.php' );
$links = new slinks;
do_sitemap_stat();

// ВЫВОД ПРЕДУПРЕЖДЕНИЯ
        if (  $reg['sitemap_debug']==1  ){
            ?><p style=" clear: both; ">
                Внимание: Включен режим отладки карты сайта, sitemap_debug=1 в Сайт//Компоненты/Карта сайта.
                <br>В данном режиме выводится большое количество отладочной информации.
            </p><?
        }
// УСТАНАВЛИВАЕМ ЧТО ОСНОВНЫЕ КОМПОНЕНТЫ ЕЩЕ НЕ ВЫВЕДЕНЫ ( ДЛЯ ИСКЛЮЧНИЯ ПОВТОРЕНИЙ )
	$load_ex = 0;
	$load_ad = 0;
	$load_news = 0;

// СОБИРАЕМ ВСЕ МЕНЮ САЙТА
	$menus = ggsql (  " select distinct(menutype) from #__menu "  );
	?><ul><?
	foreach ($menus as $menu){
		$iurls = ggsql (  " select * from #__menu where menutype='".$menu->menutype."' and parent=0 order by ordering  "  ); //ggtr2 ($iurls);
		foreach ( $iurls as $iurl){
			$links->add_link( $iurl->link );
			?><li><?
				if (  $iurl->link==''  ) { ?><span class="sitemap"><?=stripslashes($iurl->name); ?></span><? }
				else { ?><a class="sitemap" href="<?=$iurl->link; ?>"><?=stripslashes($iurl->name); ?></a><? }
				$iurl_children_cnt = ggsqlr (  " select count(id) from #__menu where menutype='".$menu->menutype."' and parent=$iurl->id "  ); //ggtr ($iurl_children_cnt);

                                // если есть вложенные подменю - запускаем рекурсию
				if (  $iurl_children_cnt>0  ){ sitemap_rec($menu->menutype, $iurl->id, &$links); }

                                //  проверка - ссылка на рубрику каталога или нет ?
				else if (  $load_ex==0  and  check_url_for_c ($iurl->link,$reg['ex_seoname'])  ){ $load_ex = 1; ex_rec(0); }

                                //  проверка - ссылка на рубрику доски объявлений или нет ?
				else if (  $load_ad==0  and  check_url_for_c ($iurl->link,$reg['ad_seoname'])  ){ $load_ad = 1; ad_rec(0); }

                                // проверка - это РУБРИКА НОВОСТЕЙ/СТАТЕЙ
				else if (  is_icat($iurl->link)  ){	
					load_lib ('icontent');
					$url 	= str_replace (site_url, "", $iurl->link);		$url 	= str_replace ("/", "", $url);
					$icat = ggsql ( "select id, goods from #__icat where sefname = '".$url."' " );	print $reg->degub('sitemap_debug', ' | ссылка опознана как рубрика  id: '.$icat[0]->id.' | '.$icat[0]->goods); 
					icat_rec($icat[0]->id);
				}
			?></li><?
		}
	}
	?></ul><?
	//ggtr5 ($links);
	?><ul>
		<li><span class="sitemap">СТАТИЧНОЕ СОДЕРЖИМОЕ</span>
			<ul><?
				$statcontents = ggsql ( "select * from #__content where catid=0" );
				foreach (  $statcontents as $statcontent  ){
					if (  !$links->find($statcontent->sefname)  ) { ?><li><a target="_blank" class="sitemap" href="/<?=$statcontent->sefname ?>"><?=stripslashes($statcontent->title); ?></a></li><? }
				}
			
			?></ul>
		</li>
	</ul><?

function icat_rec($iparent){
	global $database, $reg;
	// ПРОВЕРЯЕМ ВЛОЖЕННЫЕ НОВОСТИ / СТАТЬИ
	$iurl_good_children_cnt = ggsqlr (  " select count(id) from #__content where catid=$iparent  "  ); //ggtr ($iurl_good_children_cnt);
	if (  $iurl_good_children_cnt>0  ){ ?><ul><?
		$iurl_good_childrens = ggsql (  " select * from #__content where catid=$iparent order by ordering "  ); //ggtr ($iurl_children_cnt);
		$icat = new icat (); $icat->load($iparent);
		foreach ($iurl_good_childrens as $iurl_good_children){ 
			?><li><a class="sitemap" target="_blank" href="<?=$icat->sefnamefull().'/'.$icat->row->sefname.'/'.$iurl_good_children->sefname; ?>.html"><?=stripslashes($iurl_good_children->title); ?></a></li><?
		}
	?></ul><? }
	
	if(  ggsqlr (  " select count(id) from #__icat where publish=1 and parent=$iparent order by #__icat.order "  )>0  )
		if (  !isset($icat)  ) {  $icat = new icat (); $icat->load($iparent); }
		$iurls = ggsql (  " select * from #__icat where publish=1 and parent=$iparent order by #__icat.order "  ); // ggtr2 ($iurls);
		?><ul><?
		foreach ( $iurls as $iurl){
			?><li><?
				?><a class="sitemap" target="_blank" href="<?=$icat->sefnamefull().'/'.$icat->row->sefname.'/'.$iurl->sefname; ?>"><?=$reg->degub('sitemap_debug', 'рубрика: '); ?><?=stripslashes($iurl->name); ?><?=$reg->degub('sitemap_debug', ' | '.$iurl->goods) ?></a><?
				$iurl_children_cnt = ggsqlr (  " select count(id) from #__icat where publish=1 and parent=$iurl->id  "  ); //ggtr ($iurl_children_cnt);
				$iurl_good_children_cnt = ggsqlr (  " select count(id) from #__content where catid=$iurl->id  "  ); //ggtr ($iurl_good_children_cnt);			
				if (  $iurl_children_cnt>0  or  $iurl_good_children_cnt>0  ){ icat_rec($iurl->id); }
			?></li><?
		} ?></ul><?
}
function ad_rec($iparent){
	global $reg;
	$iurls = ggsql (  " select * from #__adcat where publish=1 and parent=$iparent order by #__adcat.order "  ); //ggtr2 ($iurls);
	?><ul><?
	foreach ( $iurls as $iurl){
		?><li><?
			?><a class="sitemap" target="_blank" href="<?=$iurl->sefnamefull.'/'.$iurl->sefname; ?>"><?=$reg->degub('sitemap_debug', 'категория: '); ?><?=stripslashes($iurl->name); ?><?=$reg->degub('sitemap_debug', ' | '.$iurl->goods) ?></a><?
			$iurl_children_cnt = ggsqlr (  " select count(id) from #__adcat where publish=1 and parent=$iurl->id  "  ); //ggtr ($iurl_children_cnt);
			$iurl_good_children_cnt = ggsqlr (  " select count(id) from #__adgood where publish=1 and parent=$iurl->id  "  ); //ggtr ($iurl_children_cnt);
			if (  $iurl_children_cnt>0  ){ ad_rec($iurl->id); }
			if (  $iurl_good_children_cnt>0  ){ ?><ul><?
				$iurl_good_childrens = ggsql (  " select * from #__adgood where publish=1 and parent=$iurl->id order by #__adgood.order "  ); //ggtr ($iurl_children_cnt);
				foreach ($iurl_good_childrens as $iurl_good_children){ ?><li><a class="sitemap" target="_blank" href="<?=$iurl_good_children->sefnamefullcat.'/'.$iurl_good_children->sefname; ?>.html"><?=stripslashes($iurl_good_children->name); ?></a></li><? }
			?></ul><? }
		?></li><?
	} ?></ul><?
}

function ex_rec($iparent){
	global $reg;
	$iurls = ggsql (  " select * from #__excat where publish=1 and parent=$iparent order by #__excat.order "  ); //ggtr2 ($iurls);
	?><ul><?
	foreach ( $iurls as $iurl){
		?><li><?
			?><a class="sitemap" target="_blank" href="<?=$iurl->sefnamefull.'/'.$iurl->sefname; ?>"><?=$reg->degub('sitemap_debug', 'категория: '); ?><?=stripslashes($iurl->name); ?><?=$reg->degub('sitemap_debug', ' | '.$iurl->goods) ?></a><?
			$iurl_children_cnt = ggsqlr (  " select count(id) from #__excat where publish=1 and parent=$iurl->id  "  ); //ggtr ($iurl_children_cnt);
			$iurl_good_children_cnt = ggsqlr (  " select count(id) from #__exgood where publish=1 and parent=$iurl->id  "  ); //ggtr ($iurl_children_cnt);
			if (  $iurl_children_cnt>0  ){ ex_rec($iurl->id); }
			if (  $iurl_good_children_cnt>0  ){ ?><ul><?
				$iurl_good_childrens = ggsql (  " select * from #__exgood where publish=1 and parent=$iurl->id order by #__exgood.order "  ); //ggtr ($iurl_children_cnt);
				foreach ($iurl_good_childrens as $iurl_good_children){ ?><li><a class="sitemap" target="_blank" href="<?=$iurl_good_children->sefnamefullcat.'/'.$iurl_good_children->sefname; ?>.html"><?=stripslashes($iurl_good_children->name); ?></a></li><? }
			?></ul><? }
		?></li><?
	} ?></ul><?
}
function sitemap_rec($imenutype, $iparent, &$links){
	global $reg;
	$iurls = ggsql (  " select * from #__menu where menutype='".$imenutype."' and parent=$iparent order by ordering "  ); //ggtr2 ($iurls);
	?><ul><?
	foreach ( $iurls as $iurl){
		$links->add_link( $iurl->link );
		?><li><?
			?><a class="sitemap" href="<?=$iurl->link; ?>"><?=stripslashes($iurl->name); ?></a><?
			$iurl_children_cnt = ggsqlr (  " select count(id) from #__menu where menutype='".$imenutype."' and parent=$iurl->id  "  ); //ggtr ($iurl_children_cnt);
			if (  $iurl_children_cnt>0  ){ sitemap_rec($imenutype, $iurl->id, $links); }
			else if (  $load_ex==0  and  check_url_for_c ($iurl->link,$reg['ex_seoname'])  ){ $load_ex = 1; ex_rec(0); }
			else if (  $load_ad==0  and  check_url_for_c ($iurl->link,$reg['ad_seoname'])  ){ $load_ad = 1; ad_rec(0); }
			else if (  is_icat($iurl->link)  ){
				load_lib ('icontent');
				$url 	= str_replace (site_url, "", $iurl->link);		$url 	= str_replace ("/", "", $url);
				$icat = ggsql ( "select id, goods from #__icat where sefname = '".$url."' " );	print $reg->degub('sitemap_debug', ' | ссылка опознана как рубрика  id: '.$icat[0]->id.' | '.$icat[0]->goods); 
				icat_rec($icat[0]->id);
			}

		?></li><?
	}
	?></ul><?
}
function check_url_for_c($url, $icomp){
	if 		(  $url==$icomp  ) return true;
	else if (  $url==$icomp.'/'  ) return true;
	else if (  $url=='/'.$icomp  ) return true;
	else if (  $url=='/'.$icomp.'/'  ) return true;
	else if (  $url==site_url.'/'.$icomp  ) return true;
	else if (  $url==site_url.'/'.$icomp.'/'  ) return true;
	return false;
}
/* ***************************ОТДЕЛ СТАТИСТИКИ*************************** */
function do_sitemap_stat(){
	global $reg;
	if (  ifipbaned()  ) return;
	
	$sitelog = new sitelog();
	$sitelog->f[0] = $reg['c'];
	if (  $sitelog->isnewlog()  ) $sitelog->desc = $reg['sitemap_name'];
	$sitelog->savelog();
}