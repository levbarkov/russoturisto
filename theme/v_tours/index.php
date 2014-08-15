<?
$swf = <<<HTML
<p class="gloss">
<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="70" height="70" align="middle">
<param name="movie" value="/includes/css/images/gloss.swf">
<param name="wmode" value="transparent">
<param name="quality" value="high">
<embed src="/includes/css/images/gloss.swf" quality="high" width="25" height="25" name="gloss" align="middle" wmode="transparent" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer">
</object>
</p>
HTML;

// $rows1 = ggsql(" select b.name, b.sefname, b.sefnamefullcat  from #__excat as a, #__exgood as b where a.sefname='asia'		and b.parent = a.id and b.publish = '1' order by b.order ; ");
// $rows2 = ggsql(" select b.name, b.sefname, b.sefnamefullcat  from #__excat as a, #__exgood as b where a.sefname='europa' 	and b.parent = a.id and b.publish = '1' order by b.order ; ");
// $rows3 = ggsql(" select b.name, b.sefname, b.sefnamefullcat  from #__excat as a, #__exgood as b where a.sefname='exotic' 	and b.parent = a.id and b.publish = '1' order by b.order ; ");
// $rows4 = ggsql(" select b.name, b.sefname, b.sefnamefullcat  from #__excat as a, #__exgood as b where a.sefname='usa' 		and b.parent = a.id and b.publish = '1' order by b.order ; ");
// $rows5 = ggsql(" select b.name, b.sefname, b.sefnamefullcat  from #__excat as a, #__exgood as b where a.sefname='africa' 	and b.parent = a.id and b.publish = '1' order by b.order ; ");

$rows1 = ggsql("SELECT title, sefname, sefnamefullcat FROM #__content WHERE catid=14 AND state=1 order by title DESC;");
$rows2 = ggsql("SELECT title, sefname, sefnamefullcat FROM #__content WHERE catid=18 AND state=1 order by title DESC;");
$rows3 = ggsql("SELECT title, sefname, sefnamefullcat FROM #__content WHERE catid=16 AND state=1 order by title DESC;");
$rows4 = ggsql("SELECT title, sefname, sefnamefullcat FROM #__content WHERE catid=29 AND state=1 order by title DESC;");
$rows5 = ggsql("SELECT title, sefname, sefnamefullcat FROM #__content WHERE catid=28 AND state=1 order by title DESC;");

if($rows1)foreach($rows1 as $row){ $link1 .= "<a href='{$row->sefnamefullcat}/{$row->sefname}.html'>{$row->title}</a>\n"; }
if($rows2)foreach($rows2 as $row){ $link2 .= "<a href='{$row->sefnamefullcat}/{$row->sefname}.html'>{$row->title}</a>\n"; }
if($rows3)foreach($rows3 as $row){ $link3 .= "<a href='{$row->sefnamefullcat}/{$row->sefname}.html'>{$row->title}</a>\n"; }
if($rows4)foreach($rows4 as $row){ $link4 .= "<a href='{$row->sefnamefullcat}/{$row->sefname}.html'>{$row->title}</a>\n"; }
if($rows5)foreach($rows5 as $row){ $link5 .= "<a href='{$row->sefnamefullcat}/{$row->sefname}.html'>{$row->title}</a>\n"; }


verstka::insite_header();
?>
<body>
<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-MTLF5F"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-MTLF5F');</script>
<!-- End Google Tag Manager -->
<div class="b-header">
	<div class="h-top">
		<div class="world inv">
			<div class="i1"><img src="/includes/css/images/s_green.png" width="70" height="55" /> Азия <?=$swf 				?><span class="fadeInLeft animated"><?=$link1 	?></span></div>
			<div class="i2"><img src="/includes/css/images/s_grey.png" width="70" height="55" /> Европа <?=$swf				?><span class="fadeInUp animated"><?=$link2 	?></span></div>
			<div class="i3"><img src="/includes/css/images/s_blue.png" width="70" height="55"/> Экзотические страны <?=$swf ?><span class="fadeInUp animated"><?=$link3 	?></span></div>
			<div class="i4"><img src="/includes/css/images/s_red.png" width="70" height="55" /> Америка <?=$swf				?><span class="fadeInUp animated"><?=$link4 	?></span></div>
			<div class="i5"><img src="/includes/css/images/s_fire.png" width="70" height="55" /> Африка <?=$swf				?><span class="fadeInRight animated"><?=$link5 	?></span></div>
		</div>
		<div class="wrap">
			<a class="logo" href="/" title="На главную">
				Руссо туристо - туристическое агенство
				<span class="logo_swf">
					<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="70" height="70">
					<param name="movie" value="/includes/css/images/gloss.swf">
					<param name="wmode" value="transparent">
					<param name="quality" value="high">
					<embed src="/includes/css/images/gloss.swf" quality="high" width="55" height="55" name="gloss" align="middle" wmode="transparent" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer">
					</object>
				</span>
			</a>
			<div class="address"><? im('p1'); ?></div>
			<div class="menu_button">
				<a href="/sertificat"><span class="icon1 cert"></span> Подарочный сертификат</a>
				<a href="/ping?s=visa" class="colorbox2"><span class="icon1 iworld"></span> Заявка <br>на визу</a>
				<a href="/ping?s=tour" class="colorbox2"><span class="icon1 calc"></span> Заявка на расчет тура</a>
			</div>
		</div>
	</div>
	<a name="cont"></a>
	<div class="menu">
		<div class="wrap">
		<ul><? 	$rows = ggsql(" select * from #__menu where `published`='1' AND `menutype` = 'mainmenu' AND `parent` = '0' order by `ordering` ASC; ");
				if($rows) foreach($rows as $row){ echo "<li><a ".(("/{$sefname1}"==$row->link)?'class=current ':'')."href='{$row->link}'>{$row->name}</a></li>"; } ?>
		</ul>
		</div>
	</div>
</div>

<div class="b-content">



<div class="padd_right">
	<? ib(); ?>
</div>
	<div class="clear"></div>
</div>

<div class="b-footer">
	<div class="wrap">
		<? im('p2'); ?>
		<? im('p3'); ?>
		<div class="ft-feedback">
			<? im('p4'); ?>
			<div class="span33_0 forms">
				<form action="" class="form" id="jq_form2">
					<label for="input_pod">Подписка</label>
					<input id="input_pod" class="input_pod" type="text" name="email" placeholder="введите ваш e-mail" />
					<input type="hidden" name="c" value="ping" />
					<input type="hidden" name="s" value="subs" />
					<?/*/?><input type="submit" class="button_sub" value="Подписаться" /><?/**/?>
					<input type="submit" class="btn2 button_sub" value="Подписаться" />
					<div class="jq_data"></div>
				</form>
				<p class="social"><? im('p5'); ?></p>
			</div>
			<div class="clear"></div>
		</div>
	</div>
</div>
<div class="b-footer2">
	<div class="wrap">
		<a class="fr unl" href="http://krasinsite.ru" target="_blank" title="Создание сайтов Красноярск">Создание сайтов Красноярск - КрасИнсайт</a>
		<p class="fl"><? im('p6'); ?></p>
		<p class="tc">
			<img src="/includes/css/images/vs.png" width="58" height="37" alt="VS" /> <img src="/includes/css/images/mc.png" width="58" height="36" alt="MC" />
			<!-- AddThis Button BEGIN -->
			<div class="addthis_toolbox addthis_default_style addthis_32x32_style" style="width: 250px; margin: 0 auto; position: relative; left: -60px;">
			<a class="addthis_button_vk"></a>
			<a class="addthis_button_facebook"></a>
			<a class="addthis_button_twitter"></a>
			<a class="addthis_button_odnoklassniki_ru"></a>
			<a class="addthis_button_compact"></a><a class="addthis_counter addthis_bubble_style"></a>
			</div>
			<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-52d4e7f57e49e99e"></script>
			<!-- AddThis Button END -->
		</p>
	</div>
</div>
</body>
</html>
<?





























?>