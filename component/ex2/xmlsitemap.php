<?
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
if (  defined('LOAD_XMLSITEPAM_EX')  ) return;
	  define( 'LOAD_XMLSITEPAM_EX', 1 );
	  
global $database, $reg;
if (  $reg['xmlsitemap_show_loaded_components']==1  )  ixmlmap_show_loaded_component(__FILE__);

if (  $reg['xmlsitemap_ex']==1  ){
	$srows = ggsql ( " select * from #__excat " );
	foreach ($srows as $srows){
		$contents .= xmlmapsite_url(site_url.$srows->sefnamefull.'/'.$srows->sefname, strftime( '%Y-%m-%d' ), 'daily', '0.7' );
		$excatgoods = ggsqlr ( "select count(id) from #__exgood where parent=$srows->id " );
		if (  $excatgoods>0  ){
			$exgoods = ggsql ( "select * from #__exgood where parent=$srows->id " ); //ggtr ($exgoods);
			foreach ($exgoods as $exgood){
				$contents .= xmlmapsite_url(site_url.$exgood->sefnamefullcat.'/'.$exgood->sefname.'.html', strftime( '%Y-%m-%d' ), 'daily', '0.8' );
			}
		}
	}
	
	$contents .= xmlmapsite_url(site_url, strftime( '%Y-%m-%d' ), 'weekly', '0.9' );
	
	$ccat = ggsql ( " select * from #__icat " );
	foreach ($ccat as $cc){
		$contents .= xmlmapsite_url(site_url.$cc->sefnamefull.'/'.$cc->sefname, strftime( '%Y-%m-%d' ), 'weekly', '0.8' );
	}
	
	$srows = ggsql ( " select * from #__content" );
	foreach ($srows as $srows){
		$contents .= xmlmapsite_url(site_url.$srows->sefnamefullcat.'/'.$srows->sefname.'.html', strftime( '%Y-%m-%d' ), 'weekly', '0.8' );
	}
}
?>