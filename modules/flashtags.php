<?php
defined( '_VALID_INSITE' ) or die( 'Доступ ограничен' );
global $reg; 
$imodule = ggo("flashtags", "#__modules", "module");	$params  = new mosParameters($imodule->params);
/*****
mode	String: 			tags|cats|both	Tells the movie to expect and display tags, categories or both.
distr	String: 			true|false	If set to true, the tags are distributed evenly over the sphere’s surface.
tcolor	Hex color value: 	0xff0000 for red.	The default tag color
tcolor2	Hex color value:	Second tag color. If supplied, tags will get a color from a gradient between both colors based on their popularity.
hicolor	Hex color value:	Tag mouseover/hover color
tspeed	Number: 			percentage	Determines the speed of the sphere’s rotation. The default is 100, higher numbers increase the speed.
tagcloud					XML string (urlencoding optional)	The tag cloud, XML format described above.
xmlpath						Path to load the XML from
*****/
$tag_file = site_path.$reg['tags_file'];
?><div id="flashcontent" style="">
<script type="text/javascript">
	var rnumber = Math.floor(Math.random()*9999999);
var tagcloud_cl_temp = encodeURIComponent("<tags><a href='http://allday.ru/index.php?do=tags&tag=' style='font-size:8pt;'></a><a href='http://allday.ru/index.php?do=tags&tag=2009' style='font-size:8pt;'>2009</a><a href='http://allday.ru/index.php?do=tags&tag=2010' style='font-size:8pt;'>2010</a><a href='http://allday.ru/index.php?do=tags&tag=3D' style='font-size:8pt;'>3D</a><a href='http://allday.ru/index.php?do=tags&tag=DVDRip' style='font-size:8pt;'>DVDRip</a><a href='http://allday.ru/index.php?do=tags&tag=House' style='font-size:8pt;'>House</a><a href='http://allday.ru/index.php?do=tags&tag=jpg' style='font-size:8pt;'>jpg</a><a href='http://allday.ru/index.php?do=tags&tag=mp3' style='font-size:11.5pt;'>mp3</a><a href='http://allday.ru/index.php?do=tags&tag=music' style='font-size:8pt;'>music</a><a href='http://allday.ru/index.php?do=tags&tag=photo' style='font-size:8pt;'>photo</a><a href='http://allday.ru/index.php?do=tags&tag=photoshop' style='font-size:11.5pt;'>photoshop</a><a href='http://allday.ru/index.php?do=tags&tag=pictures' style='font-size:8pt;'>pictures</a><a href='http://allday.ru/index.php?do=tags&tag=png' style='font-size:8pt;'>png</a><a href='http://allday.ru/index.php?do=tags&tag=psd' style='font-size:15pt;'>psd</a><a href='http://allday.ru/index.php?do=tags&tag=wallpapers' style='font-size:11.5pt;'>wallpapers</a><a href='http://allday.ru/index.php?do=tags&tag=Windows' style='font-size:8pt;'>Windows</a><a href='http://allday.ru/index.php?do=tags&tag=%E0%F0%F2' style='font-size:8pt;'>арт</a><a href='http://allday.ru/index.php?do=tags&tag=%E1%E5%F1%EF%EB%E0%F2%ED%EE' style='font-size:8pt;'>бесплатно</a><a href='http://allday.ru/index.php?do=tags&tag=%E2%E5%EA%F2%EE%F0' style='font-size:8pt;'>вектор</a><a href='http://allday.ru/index.php?do=tags&tag=%E2%E8%E4%E5%EE' style='font-size:8pt;'>видео</a><a href='http://allday.ru/index.php?do=tags&tag=%E3%F0%E0%F4%E8%EA%E0' style='font-size:8pt;'>графика</a><a href='http://allday.ru/index.php?do=tags&tag=%E4%E5%E2%F3%F8%EA%E8' style='font-size:8pt;'>девушки</a><a href='http://allday.ru/index.php?do=tags&tag=%E4%E8%E7%E0%E9%ED' style='font-size:11.5pt;'>дизайн</a><a href='http://allday.ru/index.php?do=tags&tag=%E4%F0%E0%EC%E0' style='font-size:8pt;'>драма</a><a href='http://allday.ru/index.php?do=tags&tag=%EA%E0%F0%F2%E8%ED%EA%E8' style='font-size:8pt;'>картинки</a><a href='http://allday.ru/index.php?do=tags&tag=%EA%EB%E8%EF%E0%F0%F2' style='font-size:11.5pt;'>клипарт</a><a href='http://allday.ru/index.php?do=tags&tag=%EC%F3%E7%FB%EA%E0' style='font-size:8pt;'>музыка</a><a href='http://allday.ru/index.php?do=tags&tag=%EE%E1%EE%E8' style='font-size:15pt;'>обои</a><a href='http://allday.ru/index.php?do=tags&tag=%EF%F0%E8%F0%EE%E4%E0' style='font-size:8pt;'>природа</a><a href='http://allday.ru/index.php?do=tags&tag=%EF%F0%EE%E3%F0%E0%EC%EC%E0' style='font-size:8pt;'>программа</a><a href='http://allday.ru/index.php?do=tags&tag=%F0%E0%EC%EA%E0' style='font-size:11.5pt;'>рамка</a><a href='http://allday.ru/index.php?do=tags&tag=%F0%E0%EC%EA%E8' style='font-size:8pt;'>рамки</a><a href='http://allday.ru/index.php?do=tags&tag=%F1%EA%E0%F7%E0%F2%FC' style='font-size:22pt;'>скачать</a><a href='http://allday.ru/index.php?do=tags&tag=%F1%EE%F4%F2' style='font-size:8pt;'>софт</a><a href='http://allday.ru/index.php?do=tags&tag=%F4%E8%EB%FC%EC' style='font-size:8pt;'>фильм</a><a href='http://allday.ru/index.php?do=tags&tag=%F4%EE%F2%EE' style='font-size:18.5pt;'>фото</a><a href='http://allday.ru/index.php?do=tags&tag=%F4%EE%F2%EE%F8%EE%EF' style='font-size:11.5pt;'>фотошоп</a><a href='http://allday.ru/index.php?do=tags&tag=%F6%E2%E5%F2%FB' style='font-size:8pt;'>цветы</a><a href='http://allday.ru/index.php?do=tags&tag=%F8%E0%E1%EB%EE%ED' style='font-size:11.5pt;'>шаблон</a><a href='http://allday.ru/index.php?do=tags&tag=%F8%E0%E1%EB%EE%ED%FB' style='font-size:8pt;'>шаблоны</a></tags>");
	var so = new SWFObject("/modules/flashtags/tagcloud.swf?r="+rnumber, "tagcloud", "<?=$params->def('width', '530') ?>", "<?=$params->def('height', '375') ?>", "7", "#<?=$params->def('bgcolor', '336699') ?>");
	so.addParam("wmode", "transparent");
	so.addVariable("mode", "<?=$params->def('width', 'tags') ?>");
	so.addVariable("distr", "<?=$params->def('distr', 'true') ?>");
	so.addVariable("tcolor", "<?=$params->def('tcolor', '0xf8941d') ?>");
	so.addVariable("hicolor", "<?=$params->def('hicolor', '0x000000') ?>");
	so.addVariable("tspeed", "<?=$params->def('tspeed', '100') ?>");
	so.addVariable("tagcloud", encodeURIComponent("<tags><?  
		if (  file_exists($tag_file)  ) {
			$xml = simplexml_load_file(  $tag_file  );
			$tags = $xml->tag;
			foreach($tags as $tag){	//$tag['ex']
				/*  $tag = 
						[@attributes] => Array
							(
								[exgood] => 1
							)
					
						[id] => 51
						[name] => джип
						[size] => 1
						[bright] => 0.8
						[cnt] => 191
						[m_time] => 1275144674
				*/
				if (  $tag->size<1  ) $tag->size=1;
				?><a href='<?=site_url ?>/search?isearch=<?=stripslashes($tag->name) ?>&from_cp1251=1' style='<?=round($tag->size*24) ?>'><?=stripslashes($tag->name) ?></a><?
			}
		} else { ?><a href='<?=site_url ?>' style='24'>NO XML FILE ТЕГОВ</a><? }
	 ?></tags>") );
	so.write("flashcontent");
</script>
</div>