<?  
do_frontpage_stat();
?>
<link href="/includes/css/start.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="/includes/js/jquery.easing.js"></script>
<script language="javascript" type="text/javascript" src="/includes/js/start.js"></script>


<div class="inner_start">
	<div class="p_start">

		<div class="st-slider">
			<div id="lofslidecontent45" class="lof-slidecontent  lof-snleft">
				<div class="preload"><div></div></div>
				<? $rows = ggsql(" select b.name, b.link, b.desc, b.org from `#__exfoto` as a, `#__foto` as b where a.id = b.parent and a.sefname='start' and b.publish = '1' order by b.ordering limit 15; "); ?>
				<div class="lof-main-outer">
					<ul class="lof-main-wapper">
						<? 
						$first = ' class="current"';
						foreach( $rows as $row )
						{
							$desc = $row->desc ? " <div class='slider-description'>". desafelySqlStr($row->desc) ."</div>" : '';
							if($row->link)	{ echo "<li{$first}><a href='{$row->link}'><img src='/images/foto/{$row->org}' />{$desc}</a></li>"; }
							else 			{ echo "<li{$first}><img src='/images/foto/{$row->org}' />{$desc}</li>"; }
							$first = '';
						} ?>
					</ul>  	
				</div>
				<div class="lof-navigator-outer">
					<ul class="lof-navigator">
						<? foreach( $rows as $row ){ echo "<li><div>{$row->name}</div></li>"; } ?>
					</ul>
				</div>
			</div>
			<div class="module_tours1">
				<?/**/?>
				<div id="TVSearchForm"></div><script src="http://tourvisor.ru/module/newform/searchform.min.js"></script>
				<script type="text/javascript"> TV.initModule({ moduleid: 258}); </script>
				<?/**/?>
			</div>			
		</div>

		<div class="tours_sale">
			<h3>Распродажа туров</h3>
			<div class="module_tours2">
				<?/**/?>
				<script type="text/javascript" src="http://tourvisor.ru/module/ts_sale_module.js" charset="utf-8"></script>
				<div id="ts_sale_result" align=center></div>
				<script type="text/javascript">TS_Sale_Module({ columns: "5", rows: "2", stars: "0", rating: "0", city: "12", searchlink: "http://travelclubrusso.ru", countries: "" });</script>
				<?/**/?>
			</div>
			<a class="all_view unl" href="/all_sale">Посмотреть все предложения распродажи</a>
		</div>

		<div class="ours_adgood">
			<h3>Наши предложения</h3>
			<div class="items">
				<?
					$proc = '<b class="proc"></b>';
					$rows = ggsql(" select * from #__menu where `published`='1' AND `menutype` = 'slide_menu' AND `parent` = '0' order by `ordering` ASC; ");
					if($rows) foreach($rows as $row)
					{
						echo "<a class='cr{$row->params}'  href='{$row->link}'><img src='/images/adgood/{$row->params}.png'  width='263' height='263' alt='img' />{$proc} <div><span>{$row->name}</span></div></a>";
						$proc = '';
					}
				?>
			</div>
		</div>

		<div class="news_block row4 unl">
			<? $rows = ggsql(" select * from #__content where `sefnamefullcat`='/news' AND `state`='1' order by `ordering` ASC limit 2; "); ?>
			<div class="span4"><a href="/news"><h3>Новости</h3></a>
								<p><?=mindate($rows[0]->created); ?> / Новости</p> <a class="title" href="<?=$rows[0]->sefnamefullcat.'/'.$rows[0]->sefname.'.html'; ?>"><?=$rows[0]->title ?></a> <?=$rows[0]->introtext ?></div>
			<div class="span4">	<p><?=mindate($rows[1]->created); ?> / Новости</p> <a class="title" href="<?=$rows[1]->sefnamefullcat.'/'.$rows[1]->sefname.'.html'; ?>"><?=$rows[1]->title ?></a> <?=$rows[1]->introtext ?></div>
			<div class="vline"></div>
			<? $rows = ggsql(" select * from #__content where `sefnamefullcat`='/stock' AND `state`='1' order by `ordering` ASC limit 2; "); ?>
			<div class="span4"><a href="/stock"><h3>Акции</h3></a>
								<p><?=mindate($rows[0]->created); ?> / Акции</p> <a class="title" href="<?=$rows[0]->sefnamefullcat.'/'.$rows[0]->sefname.'.html'; ?>"><?=$rows[0]->title ?></a> <?=$rows[0]->introtext ?></div>
			<div class="span4">	<p><?=mindate($rows[1]->created); ?> / Акции</p> <a class="title" href="<?=$rows[1]->sefnamefullcat.'/'.$rows[1]->sefname.'.html'; ?>"><?=$rows[1]->title ?></a> <?=$rows[1]->introtext ?></div>
			<div class="clear"></div>
		</div>
	</div>
</div>
<?
 
 
 
 
 
/****************************ОТДЕЛ СТАТИСТИКИ****************************/
function do_frontpage_stat(){
	global $reg;
	if (  ifipbaned()  ) return;
	
	$sitelog = new sitelog();
	$sitelog->f[0] = $reg['c'];
	if (  $sitelog->isnewlog()  ) $sitelog->desc = 'Главная страница';
	$sitelog->savelog();
}