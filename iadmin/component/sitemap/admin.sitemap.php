<?php
// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $my, $task, $id, $reg;
switch ($task) {
	case 'makemap':	makemap( $option );
					break;
	case 'viewmap':	viewmap( $option );
					break;
	case 'savecfg':	load_adminclass('config');	 $conf = new config($reg['db']);   $conf->save_config();	$adminlog = new adminlog(); $adminlog->logme('cfg', $reg['sitemap_name'], "", "" );
					mosRedirect( 'index2.php?ca='.$reg['ca'].'&task=viewmap', "Настройки сохранены" );
					break;
	case 'removecfg':	$adminlog = new adminlog(); $adminlog->logme('delcfg', $reg['sitemap_name'], "", "" );
						load_adminclass('config'); $conf = new config($reg['db']); $conf->remove($_REQUEST['conf_values'], $_REQUEST['id']); 
						mosRedirect( 'index2.php?ca='.$reg['ca'].'&task=viewmap', "Настройки удалены" );
						break;

}
function xmlmapsite_url ($iloc, $ilastmod, $ichangefreq, $ipriority){
	$contentrow = '
   <url>
      <loc>'.$iloc.'</loc>
      <lastmod>'.$ilastmod.'</lastmod>
      <changefreq>'.$ichangefreq.'</changefreq>
      <priority>'.$ipriority.'</priority>
   </url>';
   return $contentrow;
}
function ixmlmap_show_loaded_component( $ixmlmapcomponent ){
	ggtr01 ( $ixmlmapcomponent, 140 );
}
function makemap(){
	$filename = site_path."/sitemap.xml";
	$_f = fopen($filename,"w") or die("cant open");
	
//  ФОРМИРУЕМ XML ФАЙЛ
	$contents = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
	//сканирование директорий
		// директории вне очерди
		require_once( site_path."/component/ex/xmlsitemap.php" );
	$dir = site_path."/component"; $seoresult = false;
	foreach (glob("$dir/*") as $path) {
		if(  is_dir($path)  ){
			if (  file_exists($path."/xmlsitemap.php")  ){	//ggtr01($path."/xmlsitemap.php");
				require_once( $path."/xmlsitemap.php" );
			}
		}
	}

	$contents .= '
</urlset>';
	ggtr5( $contents, 140 );
	//$contents =  win12512utf8($contents);  for windows-1251 insite version
	fwrite($_f,$contents);
	fclose($_f);
        ?><h1 style="color: white;">XML-карта сгенерирована.</h1>
        <table cellpadding="0" cellspacing="0" border="0" id="toolbar" class="toolbar_footer"><tbody>
                <tr valign="middle" align="center"><td><a class="toolbar" href="/iadmin/index2.php?ca=sitemap&task=viewmap">Вернуться</a></td></tr>
        </tbody></table><?
}
function viewmap(){
global  $option, $reg;
?>
		<table class="adminheading"><tr><td width="100%"><?
			$iway[0]->name=$reg['sitemap_name'];
			$iway[0]->url="index2.php?ca=sitemap&task=viewmap";
			$iway[1]->name="Просмотр карты сайта для поисковых систем";
			$iway[1]->url="";
			
			i24pwprint_admin ($iway);
			?></td></tr></table>
<table border="0" cellpadding="4" cellspacing="0" width="100%" align="center">
	<tr class="workspace">
		<td valign="top" style="vertical-align:top;"><strong>Что это ?</strong></td>
		<td>С помощью файла Sitemap веб-мастеры могут сообщать поисковым системам о веб-страницах, которые доступны для сканирования. Файл Sitemap представляет собой XML-файл, в котором перечислены URL-адреса веб-сайта в сочетании с метаданными, связанными с каждым URL-адресом (дата его последнего изменения; частота изменений; его приоритетность на уровне сайта), чтобы поисковые системы могли более грамотно сканировать этот сайт.
<br /><br />Сканеры обычно находят страницы по ссылкам, указанным на сканируемом сайте и на других сайтах. Эта информация, дополненная данными из файлов Sitemap, позволяет сканерам, поддерживающим протокол Sitemap, найти все URL в файле Sitemap и собрать информацию об этих URL с помощью связанных метаданных. Использование протокола Sitemap не является гарантией того, что веб-страницы будут проиндексированы поисковыми системами, это всего лишь дополнительная подсказка для сканеров, которые смогут выполнить более тщательное сканирование Вашего сайта.</td>
	</tr><? itable_hr(2); ?>
	<tr class="workspace">
		<td  valign="top" style="vertical-align:top;"><strong>Внимание: </strong></td>
		<td>Поисковые системы регулярно проверяет все файлы <strong>Sitemap</strong> на наличие изменений, Поэтому, если Вы внесли изменения на своем сайте &ndash; повторно создавайте файл <strong>Sitemap</strong>.</td>
	</tr><? itable_hr(2); ?>
	<tr class="workspace">
		<td  valign="top" style="vertical-align:top;"><strong>Sitemap&nbsp;сайта:</strong></td>
		<td><?
			$filename = site_path."/sitemap.xml";
			print "Имя файла: ".$filename;
			$_f = fopen($filename,"r") or die("cant open");
			print "<br /><br />Размер файла: ".filesize($filename);
			$contents = fread($_f, filesize($filename));
			ggtr5( $contents, 140 );
			fclose($_f);			
		?></td>
	</tr>
</table>
<form name="adminForm" action="index2.php" method="post"><input type="hidden"  name="iuse" id="iuse" value="0" />
<? load_adminclass('config');	$conf = new config($reg['db']);
$conf->show_config('sitemap', "<br />Настройки формирования xml-файла sitemap и отображения карты-сайта по адресу <a target='_blank' href='".$reg['surl']."/".$reg['sitemap_seoname']."'>".$reg['surl']."/".$reg['sitemap_seoname']."</a>" );  ?>
<input type="hidden" name="task" value="savecfg"  />
<input type="hidden" name="ca" value="<?=$reg['ca'] ?>" />
<input type="submit" style="display:none;" /></form><?
}
?>