<?
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
if (  defined('LOAD_XMLSITEPAM_AD')  ) return;
	  define( 'LOAD_XMLSITEPAM_AD', 1 );

global $database, $reg;
if (  $reg['xmlsitemap_show_loaded_components']==1  )  ixmlmap_show_loaded_component(__FILE__);

if (  $reg['xmlsitemap_ad']==1  ){
	$srows = ggsql ( " select * from #__adcat " );
	foreach ($srows as $srows){
		$contents .= xmlmapsite_url(site_url.$srows->sefnamefull.'/'.$srows->sefname, strftime( '%Y-%m-%d' ), 'monthly', '0.5' );
		$adcatgoods = ggsqlr ( "select count(id) from #__adgood where parent=$srows->id " );
		if (  $adcatgoods>0  ){
			$adgoods = ggsql ( "select * from #__adgood where parent=$srows->id " ); //ggtr ($adgoods);
			foreach ($adgoods as $adgood){
				$contents .= xmlmapsite_url(site_url.$adgood->sefnamefullcat.'/'.$adgood->sefname.'.html', strftime( '%Y-%m-%d' ), 'monthly', '0.6' );
			}
		}
	}
}
?>