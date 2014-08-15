<?
require_once("../../wrapper.php");

ilog::vlog('$_REQUEST=');
ilog::vlog($_REQUEST);

$my1c = new lib1c();
$my1c->debug=0;

/*
 * 1. ПРОВЕРКА СОЕДИНЕНИЯ
 * ?type=catalog&mode=checkauth
 */
if (  ggrr('type')=='catalog'  and  ggrr('mode')=='checkauth'  ){
    $my1c->checkauth();
}

/*
 * 2. ЗАПРОС ПАРАМЕТРОВ САЙТА ИЗ 1С
 * ?type=catalog&mode=init
 */
else if (  ggrr('type')=='catalog'  and  ggrr('mode')=='init'  ){
    $my1c->getSiteConf();
}

/*
 * 3. ПЕРЕДАЧА ФАЙЛОВ
 */
else if (isset ( $_REQUEST ['type'] ) && $_REQUEST ['type'] == 'catalog' && isset ( $_REQUEST ['mode'] ) && $_REQUEST ['mode'] == 'file' && isset ( $_REQUEST ['filename'] )) {
        $result = $my1c->loadfile () . "\n" . $_REQUEST ['filename'];
        $my1c->log1c(  $result  );
        print $result;
}


// 4. 5. проверка импорта файлов
//?type=catalog&mode=import&filename=import.xml
/*
 * 4. import.xml - значит загрузили товары и категории
 * 5. offers.xml - содержит цены, это последний файл и по его получению нужно запускать парсер XML
 */
if (isset ( $_REQUEST ['type'] ) && $_REQUEST ['type'] == 'catalog' && isset ( $_REQUEST ['mode'] ) && $_REQUEST ['mode'] == 'import') {
	$cnt = 0;

	//switch ($_REQUEST['filename']) {
	switch ($_REQUEST ['filename']) {
		case "import.xml" :

			# Все файлы обмена загружены перебираем их и разархивировываем
			$file_txt = scandir ( $my1c->importPathFiles ); # получаем массив с именами файлов, из дирректории
			foreach ( $file_txt as $filename_to_save ) # перебираем получишийся массив
			{
				$my1c->log1c ( 'unzip файл ' . $filename_to_save );
				if (substr ( $filename_to_save, - 3 ) == 'zip') {
					# распаковываем архив
					$my1c->unzip ( $filename_to_save, $my1c->importPathFiles . "/" );
					# удаляем полученный архив
					if (  $my1c->del_zip==1  ) unlink ( $my1c->importPathFiles . "/" . $filename_to_save );
				}
			}
                        $my1c->log1c('import.xml_success');
			print "success\n";
			break;

		case "offers.xml" :
			if (file_exists ( $my1c->importPathFiles . "/" . 'offers.xml' )) {
                                // для отладки - удалить все товары и категории с прошлой выгрузки
                                // $my1c->log1c( '{ clean' ); exgood::clean_by_order(2478); $my1c->log1c( 'clean }' ); ggd();

                                /*
                                 * ПАРСИМ 1С ДАННЫЕ
                                 */
				$my1c->log1c( 'Анализ xml ' . $my1c->importPathFiles . '/import.xml' );
                                $my1c->price1ctype['38640fa5-e0a5-11d8-937b-000d884f5d5e']=1;   // розничная цена
                                $my1c->price1ctype['38640fad-e0a5-11d8-937b-000d884f5d5e']=2;   // оптовая цена
                                $my1c->price1ctype['ee24b616-e6a4-11d8-8d32-505054503030']=1;   // оптовая цена
                                
                                $my1c->parse ();
                                
				
				/*$clear	=	(bool)$xml->Каталог->attributes()->СодержитТолькоИзменения;
				$log->addEntry ( array ('comment' => 'Очистка базы товары ' . $clear ) );
				ClearBase ( $clear );
				$manufacturer = LoadmanufacturerName();*/

                                $my1c->log1c("offers.xml_success\noffer.xml");
				print "success\noffer.xml";
				break;
			}
	}
	// Возврат результата импорта файла
}

/*
 * ОБМЕН ЗАКАЗАМИ с 1С
 * ПОКА НЕ ДОДЕЛАН
 *
 * ВСЕ ДАННЫЕ ПОЛУЧАЕТ ОТ 1С, НЕТ ИХ ПАРСЕРА
 * ДАННЫЕ 1С ОТПРАВЛЯЕ, НО ПОКА ТОЛЬКО ТЕСТОВЫЙ ЗАКАЗ!!!
 */

/*
 * АВТОРИЗАЦИЯ
 * 1. ?type=sale&mode=checkauth
 */
if (  ggrr('type')=='sale'  and  ggrr('mode')=='checkauth'  ){
    $my1c->checkauth();
}
/*
 * 2. ЗАПРОС ПАРАМЕТРОВ САЙТА
 * ?type=sale&mode=init
 */
else if (  ggrr('type')=='sale'  and  ggrr('mode')=='init'  ){
    $my1c->zip = "no";
    $my1c->getSiteConf();
}

/*
 * 3. Выгрузка заказов с статусом orderstatus4export
 * ?type=sale&mode=init
 */
else if (isset ( $_REQUEST ['type'] ) && $_REQUEST ['type'] == 'sale' && isset ( $_REQUEST ['mode'] ) && $_REQUEST ['mode'] == 'query') {
    $my1c->export_orders();
}


if (isset ( $_REQUEST ['type'] ) && $_REQUEST ['type'] == 'sale' && isset ( $_REQUEST ['mode'] ) && $_REQUEST ['mode'] == 'file' && isset ( $_REQUEST ['filename'] )) {
        $result = $my1c->loadfile () . "\n" . $_REQUEST ['filename'];
        $my1c->log1c(  $result  );
        print $result;
}

//?type=sale&mode=success
if (isset ( $_REQUEST ['type'] ) && $_REQUEST ['type'] == 'sale' && isset ( $_REQUEST ['mode'] ) && $_REQUEST ['mode'] == 'success') {
	print 'success\n';

}


return;
?>

<?

/*
 * ВЫГРУЗКА ПО СТАНДАРТУ COMMERCE ML 2
 *
 * http://localhost:6448/bitrix/admin/1c_exchange.php
 *
 */


defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $database, $my, $acl, $mainframe, $reg;
mb_internal_encoding("UTF-8");

$cfg->filename = site_path.'/component/csvin/_test.csv';
$cfg->delimiter = ';';

// отображаем содержимое CSV файла
//csvin_show_csv($cfg);

/*
 * НА ВСЯКИЙ... КОММЕНТИРУЕМ, ЧТОБЫ СЛУЧАЙНО НЕ ЗАПУСТИЛОСЬ
 */
//return;

/*
 * АВТОУДАЛЕНИЕ ВСЕХ ТОВАРОВ И ПОДКАТЕГОРИЙ ИЗ КАТЕГОРИИ
 */
//excat::clean_folder(25);
//return;



?>
