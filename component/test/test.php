<?
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $database, $reg, $iseoname, $my;


ggpt();
//ggtr(

/*file_get_contents("http://list.taobao.com/market/fashion.htm?cat=50016772&isprepay=1&random=false&viewIndex=1&yp4p_page=2&commend=all&atype=b&style=grid&s=340&isnew=2&olu=yes#ListView")

);*/



/*$ch = curl_init("http://list.taobao.com/market/fashion.htm?cat=50016772&isprepay=1&random=false&viewIndex=1&yp4p_page=2&commend=all&atype=b&style=grid&s=340&isnew=2&olu=yes#ListView");
$fp = fopen("example_homepage.txt", "w");

curl_setopt($ch, CURLOPT_FILE, $fp);
curl_setopt($ch, CURLOPT_HEADER, 0);

curl_exec($ch);
curl_close($ch);
fclose($fp);
*/


$url_a=array("http://search1.wap.taobao.com/s/search.htm?catmap=50029742&sort=bid&n=20"
		    ,"http://search1.wap.taobao.com/s/search.htm?catmap=50029742&sort=bid&n=20&s=50");
$tuCurl = curl_init(); 
foreach ( $url_a as $url){
	ggpt(start_load);
	$data=NULL;  $i=1;
	$li_cnt = 0;
	






$data = ""; 

curl_setopt($tuCurl, CURLOPT_URL, $url); 
curl_setopt($tuCurl, CURLOPT_HEADER, 0); 
curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1); 
//curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Content-Type: text/xml","SOAPAction: \"/soap/action/query\"", "Content-length: ".strlen($data))); 

$tuData = curl_exec($tuCurl); 
if(!curl_errno($tuCurl)){ 
  $info = curl_getinfo($tuCurl); 
  echo 'Took ' . $info['total_time'] . ' seconds to send a request to ' . $info['url']; 
} else { 
  echo 'Curl error: ' . curl_error($tuCurl); 
} 


echo $tuData; 



	
	/*
	$dataHandle=fopen($url, "r" );
	if($dataHandle)
	{
		while (!feof($dataHandle))
			{
			$data_block = fread($dataHandle, 102400);
			$data.= $data_block;
			print '<br />'.strlen($data_block);
			ggpt($i++);
			//ggtr ($data);
	
					//<li class="list-item">
					//preg_match('#list-item#', $data, $match);
					//ggr ($match);
					//if (  !(mb_strpos($data_block, '<li class="list-item">', 0, 'UTF-8') === false)  ) break;
//					if (  !(mb_strpos($data, 'list-item', 0, 'UTF-8') === false)  ) {
						//ggpt('list-item');
						//ggtr01('list-item');
					//}
	
	
			//<div class="xb-tip">
			if (  !(mb_strpos($data, 'xb-tip', 0, 'UTF-8') === false)  ) {
						ggpt('END_OF_LIST_TIME');
						ggtr01('FIND END OF LIST');
					}
		}
		if($data)
		{
			ggtr01("вывод всего файла");
			ggtr5($data);
		}

	}*/
}
curl_close($tuCurl); 
		fclose($dataHandle);
ggpt(1);

return;





ggtr( pathinfo(  "авто.png"  ) ); ggd();

$mat=array();

?><form action="/test/">
    <input name="qqq" value="<?=ggrr('qqq') ?>">
    <input type="submit">
</form><?
//ggtr (  ggrr (qqq));
ggtr( sefname(ggrr('qqq')) );


return;


ggd(danya_make_svn);
ggpt();

$preg_tmp = "/^http:\/\/[wW.]*yandex.[a-zA-Z]+\//";
$preg_str = "http://rambler.ru/search?sdfsdf=wwwe&dfdf=333&q=%D1%82%D1%80%D0%B0%D1%84%D0%B0%D1%80%D0%B5%D1%82+n95&tld=by&lr=157";
$this1->i24http_ref = $preg_str;

for (  $i=0; $i<100; $i++) {
    //preg_match($preg_tmp, $preg_str);
    //strpos($preg_str, "yandex.ru/yandsearch") ;
    //ggsql ("SELECT id FROM ins_expack WHERE connect='f3c29028-e133-11d8-937c-000d884f5d5e' ;");
    ggsql(  "  select val from ins_config where name = 'sitemap_name'  "  );
}
//ggtr(
//    preg_match("/rambler\.[a-zA-Z\.]+\/(?:search|srch)/", $this1->i24http_ref)
    //);
ggpt("dfjgkhdfgkd");
ggpt("cg22");

/*
        $expack = new expack();
        $expack->vars->id         = 100720;  // 0 добавить новую, если >0 - то изменить существующую
        $expack->vars->sku        = 'ssd003';
        $expack->vars->name       = 'tovar_nnnew003';
        $expack->vars->parent     = 44;  // id в #__exgood ( сам товар )
        $expack->vars->expack_set = 225; // используем группы характеристик с id=225 (#___expack_set)? в данном примере это - Сотовый телефоны
        $expack->vars->unit       = 100085;   // id в #__exgood_unit ( единицы измерения )

        // значения свойств комплектации, только если выбранна группа характеристик (  $expack->vars->expack_set>0  )
        // для указанной группы характеристик этого примера (Сотовый телефоны) имеются 2 свойства: Память и Цвет
        $expack_set_val = array();
        $expack_set_val[1]->attrib=16;      //свойство "Память" ( #__expack_attrib )
        $expack_set_val[1]->attrib_val=43;  //значение "4 Gb"   ( #__expack_attrib_val )
        $expack_set_val[2]->attrib=15;      //свойство "Цвет"
        $expack_set_val[2]->attrib_val=40;  //значение "Белый"
        $expack->expack_set_val = &$expack_set_val;

        // указание стоимости и остатков для комплектации
        $expack->sklad   = array( 1=>rand(1,99),  2=>rand(1,99) );  // задаем остатки
        $expack->price   = array( 1=>rand(1,99),  2=>rand(1,99) );  // задаем стоимость
        $expack->cy      = array( 1=>1,           2=>1 );           // задаем валюту

        $expack->saveme();
        ggr ($expack);
        ggdd();
*/





//require_once(site_path."/lib/saver.php");

// ggtr5(  num::fillzerro('7',2)  );
//preg_match("/^MS Internet Explorer 6/", ibrowserpro())

//print number_format(1234567.44,2,'.', ' ');

//print number_format(1234567,2,'.', ' ');

//print num::flexprice(1234567.14);


//print num::num2str(222,1);

//print num::morph(30, 'товар','товара','товаров');


?>