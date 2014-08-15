<?php
/**
 * Класс работы с числами
 */
class lib1c {

    var $zip = 'yes';
    var $file_limit = '33554432';

    /** Статус заказов для выгрузки в 1С */
    var $orderstatus4export = 1;

    /** 1 — сохраняем логи выполненных операций */
    var $debug = 0;

    /** 1 — удалять полученный архив */
    var $del_zip = 0;
    
    /** директория в которую записываются картинки и файл обмена */
    var $importPathFiles = '/component/1c/files';

    function  __construct() {
        $this->importPathFiles = site_path . $this->importPathFiles;
    }
    /**
     * АВТОРИЗАЦИЯ И ПРОВЕРКА СОЕДИНЕНИЯ
     */
    function checkauth(){
        global $mainframe;
        $result =  'success'.chr(13).chr(10).'PHPSESSID'.chr(13).chr(10).$mainframe->_session->session_id;
        $this->log1c(  $result  );
        print $result;
    }

    function getSiteConf(){
        print 'zip='.$this->zip.chr(13).chr(10).'file_limit='.$this->file_limit;
    }

    function log1c($obj){
        if (  $this->debug==1  )  ilog::vlog(  $obj  );
    }


    # Загрузка файла из 1С методом POST
    # Все файлы попадают в директорию $this->importPathFiles
    # Загрузка файла POST'ом
    function loadfile() {

            global $log;
            #global $session;

            #$image_data = "";
            //Считываем файл в строку

            $filename_to_save = $this->importPathFiles . '/' . $_REQUEST ['filename'];

            $this->log1c( 'loadfile & create dir name ' . $filename_to_save );

            $image_data = file_get_contents ( "php://input" );

            if (isset ( $image_data )) {
                    //if (file_exists($filename_to_save)) {unlink($filename_to_save);}


                    $png_file = fopen ( $filename_to_save, "ab" ) or die ( "File not opened" );
                    if ($png_file) {
                            set_file_buffer ( $png_file, 20 );
                            fwrite ( $png_file, $image_data );
                            fclose ( $png_file );

                            $this->log1c ( 'Получен файл  ' . $filename_to_save );
                            $this->log1c ('loadfile_success');
                            return "success";
                    }
            }
            $this->log1c ( 'Ошибка получения файла ' );
            $this->log1c ('loadfile_error POST');
            return "error POST";
    }



    # Распаковка архивов
    function unzip($file, $folder = '') {

            global $log;
            $this->log1c ( 'unzip file 3 ' . $folder . $file  );
            $zip = zip_open ( $folder . $file );
            $files = 0;
            #$folders = 0;

            if ($zip) {
                    while ( $zip_entry = zip_read ( $zip ) ) {

                            $name = $folder . zip_entry_name ( $zip_entry );

                            $path_parts = pathinfo ( $name );
                            # Создем отсутствующие директории
                            $this->log1c ( 'loadfile create dir name ' . $path_parts ['dirname'] );
                            if (! is_dir ( $path_parts ['dirname'] )) {
                                    mkdir ( $path_parts ['dirname'], 0755, true );
                            }

                            if (zip_entry_open ( $zip, $zip_entry, "r" )) {
                                    $buf = zip_entry_read ( $zip_entry, zip_entry_filesize ( $zip_entry ) );

                                    $file = fopen ( $name, "wb" );
                                    if ($file) {
                                            fwrite ( $file, $buf );
                                            fclose ( $file );
                                            $files ++;
                                    } else {
                                            $this->log1c ( 'error unzipopen file ' . $name );
                                    }
                                    zip_entry_close ( $zip_entry );
                            }
                    }
                    zip_close ( $zip );
            } else {
                    $this->log1c ( 'error unzip file ' . $name );
            }

    }

    function parse(){

        $xml = simplexml_load_file ( $this->importPathFiles . "/" . 'import.xml' );
        $this->log1c ( '{ парсим категории ' );
            $this->category = $this->groups_create($xml->Классификатор,	$this->category ,  0); // ПАРСИМ КАТАЛОГИ
        $this->log1c ( 'парсим категории }' );
        $this->log1c ( '{ парсим товары' );
            $this->products_create($xml->Каталог,	$products); // ПАРСИМ ТОВАРЫ
        $this->log1c ( 'парсим товары }' );
        $this->log1c ( '{ парсим цены' );
            $xml = simplexml_load_file ( $this->importPathFiles . "/" . 'offers.xml' );
            $this->price_tovar_create( $xml->ПакетПредложений ); // ПАРСИМ ЦЕНЫ
        $this->log1c ( 'парсим цены }' );
    }

    # Парсинг типов цен на комплектации
    function price_tovar_create($xml) {
            global $reg;
            if (!isset($xml->Предложения)) return $price;
            
            # Перебираем товары
            foreach ($xml->Предложения->Предложение as $price_data){
                    $this->log1c ( '/* Предложение' );
                    $this->log1c ( $price_data );

                    $connect_exgood = $this->xmlin_wrapper(  substr((string)$price_data->Ид,0,36)  ); //ИД ТОВАРА, пример d6463ad4-e133-11d8-937c-000d884f5d5e
                    $connect_expack = $this->xmlin_wrapper(  (string)$price_data->Ид  );              //ОБЩИЙ ИД ТОВАРА И КОМПЛЕКТАЦИИ = ИД_ТОВАРА#ИД_КОМПЛЕКТАЦИИ, пример ea215058-e133-11d8-937c-000d884f5d5e#79e24bfb-e136-11d8-937c-000d884f5d5e

                    $expack = new expack();
                    $expack->vars->id         = $this->expack [$connect_expack] ['expack_id'];  // 0 добавить новую, если >0 - то изменить существующую
                    $expack->dontUpdate_expack=1;

                    $expack->sklad   = array( 1=>(int)$price_data->Количество, 2=>(int)$price_data->Количество );     // задаем остатки
                    $expack->price   = array( );              // задаем стоимость
                    $expack->cy      = array( 1=>1,  2=>1 );  // задаем валюту — всегда рубли, так как патриоты мы

                    # Перебираем цены на товар
                    foreach ($price_data->Цены->Цена as $price_tovar_data){
                        $price_type = (string)$price_tovar_data->ИдТипаЦены;
                        if (  isset(  $this->price1ctype[$price_type]  )  ){
                            $expack->price[$this->price1ctype[$price_type]] = $this->xmlin_wrapper(  (string)$price_tovar_data->ЦенаЗаЕдиницу  );
                        }
                    }
                    $this->log1c ( $expack );
                    $expack->saveme(0);
                    $this->log1c ( 'Предложение */' );

            }
    }

    # Парсинг списка товаров и характеристик
    function products_create($xml, $products) {
        global $reg;

        $p->saveNames = 0;  // для увеличения скорости принудительно отключаем сохранение names
        $p->saveTag = 0;    // и Тегов
        $p->SmartOrder = 0;     // оrder у товаров - всегда прописывается равным 2478
                                // используется как режим для отладки, потом можно быстро удалять товары по order=2478
        $p->updateGooodsCount = 0;  // не обновлять счетчик товаров в категориях (#__excat.goods)
        $p->excat = 25;             // категория для новых ( неопределенных ) товаров
        $p->currentExcat = 25;      // текущая категория для товаров

        // КЭШ, глобальный
        $this->exgood = array();
        $this->expack = array();


	if (  !isset($xml->Товары)  ) return $products;
	
	$i=0;
	foreach ($xml->Товары->Товар as $product_data){  $i++;

            $connect_exgood = $this->xmlin_wrapper(  substr((string)$product_data->Ид,0,36)  ); //ИД ТОВАРА, пример d6463ad4-e133-11d8-937c-000d884f5d5e
            $connect_expack = $this->xmlin_wrapper(  (string)$product_data->Ид  );              //ОБЩИЙ ИД ТОВАРА И КОМПЛЕКТАЦИИ = ИД_ТОВАРА#ИД_КОМПЛЕКТАЦИИ, пример ea215058-e133-11d8-937c-000d884f5d5e#79e24bfb-e136-11d8-937c-000d884f5d5e

            if (  $connect_exgood  ){
                $exgood_parent = 0;
                foreach ($product_data->Группы as $groups_data){
                        $id = $this->xmlin_wrapper(  (string)$groups_data->Ид  );
                        $exgood_parent = $this->category [$id] ['category_id'];
                }

                // смотрим товар в базе сайта
                $check_exgood = ggsql (  "SELECT id FROM #__exgood WHERE connect='".$connect_exgood."' ; "  );
                if (  !$check_exgood[0]->id  ){//создаем товар
                    $this->log1c ( '+ новый товар ' );

                    $exgood = new exgood();
                    $exgood->vars->id      = 0;
                    $exgood->vars->parent  = $exgood_parent;    // категория
                    $exgood->vars->name    = $this->xmlin_wrapper(  (string)$product_data->Наименование  );
                    $exgood->vars->sdesc   = '';
                    $exgood->vars->fdesc   = '';
                    $exgood->vars->publish = 1;
                    $exgood->vars->connect = $connect_exgood;    // поле для связи с 1С, поэтому обязательно для заполнения
                    $exgood->vars->spec    = 0;
                    $exgood->vars->sefnamefullcat = $this->category [$id] ['sefnamefull'].'/'.$this->category [$id] ['sefname'];
                    $exgood->vars->expack_select_type = 1;
                    $exgood->vars->_tag_field   = '';
                    $exgood->vars->_names_field = '';
                    $exgood->saveme( $p );  // оптимизировать там строчку 430
                    // КЭШ
                    $this->exgood[$connect_exgood]['exgood_id'] = $exgood->id;

                    $this->log1c ( $this->exgood[$connect_exgood] );
                }
                else  $this->exgood[$connect_exgood]['exgood_id'] = $check_exgood[0]->id; // КЭШ
            }
            // ищем комплектацию в БД сайта
            if (  $connect_expack  ){
                $check_expack = ggsql (  "SELECT id FROM #__expack WHERE connect='".$connect_expack."' ; "  );

                if (  !$check_expack[0]->id  ){//создаем комплектацию
                    $this->log1c ( '+ новая комплектация ' );  $this->log1c ( $product_data );

                    // единица измерения
                    $exunit = new exunit();
                    $exunit_id = $exunit->getUnitIDbyConnect($this->xmlin_wrapper(  (string)$product_data->БазоваяЕдиница['Код']  ), $this->xmlin_wrapper(  (string)$product_data->БазоваяЕдиница  ),  $this->exgood[$connect_exgood]['exgood_id']);

                    // формируем красивое имя комплектации, на основании характеристик товара
                    $expack_name = array();
                    if(  count(  $product_data->ХарактеристикиТовара->ХарактеристикаТовара  )>0  ){
                        foreach ($product_data->ХарактеристикиТовара->ХарактеристикаТовара as $tovar_property){
                            $tovar_property_name = $this->xmlin_wrapper(  (string) $tovar_property->Наименование  );
                            $tovar_property_val  = $this->xmlin_wrapper(  (string) $tovar_property->Значение  );
                            if (  $tovar_property_name==''  ) continue;
                            $expack_name[] = $tovar_property_name.': '.$tovar_property_val;
                        }
                        if (  count($expack_name)>0  ) $expack_name_str =  implode(', ', $expack_name);
                        else $expack_name_str = $this->xmlin_wrapper(  (string)$product_data->БазоваяЕдиница  );
                    }
                    else $expack_name_str = $this->xmlin_wrapper(  (string)$product_data->БазоваяЕдиница  );
/*if (  $connect_expack=='0430245e-8214-11df-b6c1-e6c6c4e4d223#04302460-8214-11df-b6c1-e6c6c4e4d223'){
    ggr(  $product_data  );
    ggtr ($expack_name);
    ggd();
}*/
                    // создаем комплектацию
                    $expack = new expack();
                    $expack->vars->id         = 0;  // 0 добавить новую, если >0 - то изменить существующую
                    $expack->vars->sku        = $this->xmlin_wrapper(  (string)$product_data->Артикул  );
                    $expack->vars->connect    = $connect_expack; // поле для связи с 1С и поэтому ОБЯЗАТЕЛЬНОЕ для заполнения !!!
                    $expack->vars->name       = $expack_name_str;
                    $expack->vars->parent     = $this->exgood[$connect_exgood]['exgood_id'];  // id в #__exgood ( сам товар )
                    $expack->vars->expack_set = 0;//225; // используем группы характеристик с id=225 (#___expack_set), в данном примере это - "Сотовый телефоны"
                                                  // если характеристики товара не используется, то =0
                    $expack->vars->unit       = $exunit_id;   // id в #__exgood_unit ( единицы измерения )

                    // значения свойств комплектации, только если выбранна группа характеристик (  $expack->vars->expack_set>0  )
                    // для указанной группы характеристик этого примера (Сотовый телефоны) имеются 2 свойства: Память и Цвет
                    // если группа характеристик не используется - закомментировать
                    /**
                     *
                    $expack_set_val = array();
                    $expack_set_val[1]->attrib=16;      //свойство "Память" ( #__expack_attrib )
                    $expack_set_val[1]->attrib_val=43;  //значение "4 Gb"   ( #__expack_attrib_val )
                    $expack_set_val[2]->attrib=15;      //свойство "Цвет"
                    $expack_set_val[2]->attrib_val=39;  //значение "Белый"
                    $expack->expack_set_val = &$expack_set_val;
                     *
                     */
                    $expack->saveme();
                    // КЭШ
                    $this->expack[$connect_expack]['expack_id'] = $expack->id;

                    $this->log1c ( $expack );
                }
                else $this->expack[$connect_expack]['expack_id'] = $check_expack[0]->id; // КЭШ
            }

	}
    }


    # Обход дерева групп полученных из 1С
    function groups_create($xml, $category, $owner) {
        global $reg;


	if (!isset($xml->Группы))

	{
		return $category;
	}

	foreach ($xml->Группы->Группа as $category_data){
                //ggtr ($category_data);
                $excatName = $this->xmlin_wrapper(  $category_data->Наименование  );
		$excatConnect	= $this->xmlin_wrapper(  (string)$category_data->Ид  );   // поле для связи с 1С, выполняет роль артикула категории

		$category [$excatConnect] ['name'] = $name;
		$category [$excatConnect] ['owner'] = $owner;
                
                // поиск группы в базе данных
                $check_excat = ggsql (  "SELECT id  FROM #__excat WHERE connect='".$excatConnect."' ; "  );
                if (  !$check_excat[0]->id  ){//создаем категорию
                        if (  $owner>0  ) $excat_owner = ggo($owner, '#__excat');
                        else{
                            $excat_owner->sefnamefull = '';
                            $excat_owner->sefname = $reg['ex_seoname'];
                        }
                        $i24r = new mosDBTable( "#__excat", "id", $reg['db'] );
                        $i24r->id = 0;
                        $i24r->parent = $owner;
                        $i24r->name = $excatName;
                        $i24r->sdesc = '';
                        $i24r->fdesc = '';
                        $i24r->publish = 1;
                        $i24r->connect = $excatConnect; // поле для связи с 1С
                        $i24r->sefname = sefname( $i24r->name );
                        $i24r->sefnamefull = $excat_owner->sefnamefull.'/'.$excat_owner->sefname;
                        $iexmaxorder = ggsql ("SELECT * FROM #__excat WHERE parent=".$owner." ORDER BY #__excat.order DESC LIMIT 0,1 "); // ggtr ($iexmaxorder);
                        $i24r->order = $iexmaxorder[0]->order+1;
                        if (!$i24r->check()) { echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n"; } else $i24r->store();
                } else $i24r->id = $check_excat[0]->id;
                
                $category [$excatConnect] ['sefname']     = $i24r->sefname;
                $category [$excatConnect] ['sefnamefull'] = $i24r->sefnamefull;
                $category [$excatConnect] ['category_id'] = ( int ) $i24r->id;
		$category = $this->groups_create ( $category_data, $category, $category [$excatConnect] ['category_id'] );

	}
	return $category;
    }


    function export_orders(){
        global $reg;
        
		$timechange = time ();

		$no_spaces = '<?xml version="1.0" encoding="UTF-8"?>
							<КоммерческаяИнформация ВерсияСхемы="2.04" ДатаФормирования="' . date ( 'Y-m-d', $timechange ) . 'T' . date ( 'H:m:s', $timechange ) . '"></КоммерческаяИнформация>';
		$xml = new SimpleXMLElement ( $no_spaces );

                
                // 1 заказ, они будут в цикле foreach
                
			$doc = $xml->addChild ( "Документ" );
                        $val = 'руб';
			$doc->addChild ( "Ид", '976416d5464c7d5068c1f25afdb28332' );
			$doc->addChild ( "Номер", 46 );
			$doc->addChild ( "Дата", date ( 'Y-m-d', 1288264766 ) );
			$doc->addChild ( "ХозОперация", "Заказ товара" );
			$doc->addChild ( "Роль", "Продавец" );
			$doc->addChild ( "Валюта", $val );
			$doc->addChild ( "Курс", 1 );
			$doc->addChild ( "Сумма", 700 );
			$doc->addChild ( "Время", date ( 'H:m:s', 1288264766 ) );

                        $k1 = $doc->addChild ( 'Контрагенты' );
                        $k1_1 = $k1->addChild ( 'Контрагент' );
                        $k1_2 = $k1_1->addChild ( "Наименование", " Физ лицо" );
                        $k1_2 = $k1_1->addChild ( "Роль", "Покупатель" );
                        $k1_2 = $k1_1->addChild ( "ПолноеНаименование", "Физ лицо" );
                        $k1_2 = $k1_1->addChild ( "Имя", "лицо" );
                        $k1_2 = $k1_1->addChild ( "Фамилия", "Физ" );

                        // в цикл
				$t1 = $doc->addChild ( 'Товары' );
				$t1_1 = $t1->addChild ( 'Товар' );
				$t1_2 = $t1_1->addChild ( "Ид", 'ea215076-e133-11d8-937c-000d884f5d5e' );
				$t1_2 = $t1_1->addChild ( "Наименование", 'Чайник MOULINEX L 1,3 СУПЕР ТОВАРРРР' );
				$t1_2 = $t1_1->addChild ( "ЦенаЗаЕдиницу", 700 );
				$t1_2 = $t1_1->addChild ( "Количество", 1 );
				$t1_2 = $t1_1->addChild ( "Сумма", 700  );
				$t1_2 = $t1_1->addChild ( "ЗначенияРеквизитов" );
				$t1_3 = $t1_2->addChild ( "ЗначениеРеквизита" );
				$t1_4 = $t1_3->addChild ( "Наименование", "ВидНоменклатуры" );
				$t1_4 = $t1_3->addChild ( "Значение", "Товар" );

				$t1_2 = $t1_1->addChild ( "ЗначенияРеквизитов" );
				$t1_3 = $t1_2->addChild ( "ЗначениеРеквизита" );
				$t1_4 = $t1_3->addChild ( "Наименование", "ТипНоменклатуры" );
				$t1_4 = $t1_3->addChild ( "Значение", "Товар" );




                        //print $xml->asXML ();
			header ( "Content-type: text/xml; charset=utf-8" );
			print iconv ( "utf-8", "windows-1251", $xml->asXML () );
                        //ilog::vlog(  $xml->asXML ()  );

    }


    function xmlin_wrapper( $txt ){
        return safelySqlStr(  trim($txt)  );
    }


}
?>
