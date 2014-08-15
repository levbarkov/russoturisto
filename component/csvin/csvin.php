<?
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
return;

/*
 * КОНВЕРТИМ EXCEL -> CSV
 * класс "mega_excel" в стандартный пакет не включен, спросить у Георгия
 */
require_once site_path.'/mega_excel/PHPExcel/IOFactory.php';
$objPHPExcel = PHPExcel_IOFactory::load(site_path."/price/price_avtodiesel.xls");
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV')
    ->setDelimiter(';')
    ->setEnclosure('')
    ->setLineEnding("\r\n")
    ->setSheetIndex(0)
    ->save(  $cfg->filename  );


/*
 * АВТОУДАЛЕНИЕ ВСЕХ ТОВАРОВ И ПОДКАТЕГОРИЙ ИЗ КАТЕГОРИИ
 */
//excat::clean_folder(25);
//return;

/*
 * САМ ИМПОРТ
 */
    $p->saveNames = 0;  // для увеличения скорости принудительно отключаем сохранение names 
    $p->saveTag = 0;    // и Тегов
    $p->updateGooodsCount = 0;  // не обновлять счетчик товаров в категориях (#__excat.goods)
    $p->excat = 25;             // категория для новых ( неопределенных ) товаров
    $p->currentExcat = 25;      // текущая категория для товаров

    $f = fopen($cfg->filename, "r") or die("Ошибка!");
    for ($i=0; $datastr=fgets($f,4096); $i++) {
        /*
         * ОБЯЗАТЕЛЬНО ВСЕ ДАННЫЕ ПРОПУСКАТЬ ЧЕРЕЗ ФУНКЦИЮ safelySqlStr
         */
        $datacsv = explode($cfg->delimiter,$datastr);
        $datacsv[1] = csvin_wrapper( $datacsv[1] );
        $datacsv[2] = csvin_wrapper( $datacsv[2] );
        $datacsv[3] = csvin_wrapper( $datacsv[3] );
        $datacsv[4] = csvin_wrapper( $datacsv[4] );
        //ggtr ( $datacsv );
        /*
         * ИНОГДА НАЗВАНИЕ НАЧИНАЕТСЯ И ОКАНЧИВАЕТСЯ КАВЫЧКАМИ
         * ПРАВИМ ЭТОТ НЕДОСТАТОК ПРАЙСОВ EXCEL
         * кавычка это &#039; или &quot; так как после экранирования
         */
        $cname=&$datacsv[2];
        $first_chr = mb_substr($cname, 0, 6);
        if(  $first_chr=='&#039;'  or  $first_chr=='&quot;'  ){
            $cname = trim(  mb_substr($cname, 6)  );
            $last_chr = mb_substr($cname, (mb_strlen($cname)-6), 6);
            if(  $last_chr=='&#039;'  or  $last_chr=='&quot;'  )
                $cname = trim(  mb_substr($cname, 0, (mb_strlen($cname)-6))  );
        }
        

        /*
         * ОТФИЛЬТРОВЫВАЕМ НЕ ТОВАРЫ
         */
        if (  $i<=10 ) continue;




        /*
         * ЕСЛИ НАШЛИ КАТЕГОРИЮ - ТО СОЗДАЕМ ЕЕ
         * И ДЕЛАЕМ ТЕКУЩЕЙ
         */
        if (  trim($datacsv[1])==''  and  trim($datacsv[4])==''  ) {

            // наверно категория, смотрим, новая или уже есть
            $excatConnect = $datacsv[2];
            if (  $excatConnect  ){
                $check_excat = ggsql (  "SELECT id  FROM #__excat WHERE connect='".$excatConnect."' ; "  );

                if (  !$check_excat[0]->id  ){//создаем категорию
                        $excat25 = ggo($p->excat, '#__excat');
                        $i24r = new mosDBTable( "#__excat", "id", $database );
                        $i24r->id = 0;
                        $i24r->parent = $p->excat;
                        $i24r->name = $datacsv[2];
                        $i24r->sdesc = '';
                        $i24r->fdesc = '';
                        $i24r->publish = 1;
                        $i24r->connect = $excatConnect; // поле для связи с 1С
                        $i24r->sefname = sefname( $i24r->name );
                        $i24r->sefnamefull = $excat25->sefnamefull.'/'.$excat25->sefname;
                        $iexmaxorder = ggsql ("SELECT * FROM #__excat WHERE parent=".$p->excat." ORDER BY #__excat.order DESC LIMIT 0,1 "); // ggtr ($iexmaxorder);
                        $i24r->order = $iexmaxorder[0]->order+1;
                        if (!$i24r->check()) { echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n"; } else $i24r->store();
                }
                else $i24r->id = $check_excat[0]->id; // категория уже есть, просто записчваем id

                $p->currentExcat = $i24r->id;
                if (  !$p->currentExcat  )  $p->currentExcat = $p->excat;
            }
            continue;
        }
        if (  trim($datacsv[4])=='Ед.'  ) continue;
        //ggtr ($datacsv);

        /*
         * ОБНОВЛЯЕМ ИЛИ СОЗДАЕМ ТОВАР
         */
        $expackConnect = $datacsv[1];
        if (  $expackConnect  ){

            $check_expack = ggsql (  "SELECT id FROM #__expack WHERE connect='".$expackConnect."' ; "  );

            if (  !$check_expack[0]->id  ){//создаем товар
                // создаем товар
                $exgood = new exgood();
                $exgood->vars->id      = 0;
                $exgood->vars->parent  = $p->currentExcat;    // категория
                $exgood->vars->name    = $datacsv[2];
                $exgood->vars->sdesc   = '';
                $exgood->vars->fdesc   = '';
                $exgood->vars->publish = 1;
                $exgood->vars->connect = $expackConnect;    // поле для связи с 1С, поэтому обяхательно для заполнения
                $exgood->vars->spec    = 0;
                $exgood->vars->expack_select_type = 1;
                $exgood->vars->_tag_field   = '';
                $exgood->vars->_names_field = '';
                $exgood->saveme( $p );

                // создаем единицу измерения
                $exunit = new exunit();
                $exunit->vars->id=0;      // 0 добавить новую, если >0 - то изменить существующую
                $exunit->vars->parent=$exgood->id;  // id товара (  таблица excgood )
                $exunit->vars->name=$datacsv[4];
                $exunit->saveme();

                // создаем комплектацию
                $expack = new expack();
                $expack->vars->id         = 0;  // 0 добавить новую, если >0 - то изменить существующую
                $expack->vars->sku        = $datacsv[1];
                $expack->vars->connect    = $datacsv[1]; // поле для связи с 1С и поэтому ОБЯЗАТЕЛЬНОЕ для заполнения !!!
                $expack->vars->name       = $datacsv[4];
                $expack->vars->parent     = $exgood->id;  // id в #__exgood ( сам товар )
                $expack->vars->expack_set = 0;//225; // используем группы характеристик с id=225 (#___expack_set), в данном примере это - "Сотовый телефоны"
                                              // если характеристики товара не используется, то =0
                $expack->vars->unit       = $exunit->id;   // id в #__exgood_unit ( единицы измерения )

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

                // указание стоимости и остатков для комплектации
                $expack->sklad   = array( 1=>1,                   2=>1 );   // задаем остатки
                $expack->price   = array( 1=>intval($datacsv[3]), 2=>intval($datacsv[3]) );  // задаем стоимость
                $expack->cy      = array( 1=>1,                   2=>1 );   // задаем валюту

                $expack->saveme();
            } else {    // обновляем информацию об остатках и стоимости
                $expack = new expack();
                $expack->vars->id         = $check_expack[0]->id;  // 0 добавить новую, если >0 - то изменить существующую
                $expack->dontUpdate_expack=1;

                $expack->sklad   = array( 1=>1,                   2=>1 );   // задаем остатки
                $expack->price   = array( 1=>intval($datacsv[3]), 2=>intval($datacsv[3]) );  // задаем стоимость
                $expack->cy      = array( 1=>1,                   2=>1 );   // задаем валюту

                $expack->saveme(0);
            }

        }
        //if (  $i>200  ) break;
        //return;

    }
    fclose($f);
    return;


/**
 * ВЫВОДИМ СОДЕРЖИМОЕ CSV - ФАЙЛА
 * @param <type> $cfg
 */
function csvin_show_csv( $cfg ){
	//	определение кол-ва столбцов
        $f = fopen($cfg->filename, "r") or die("Ошибка!"); $i100 = 0; $max_cols = 0;
	for ($i=0; $datastr=fgets($f,4096); $i++) {
          $data = explode($cfg->delimiter,$datastr);
	  $num = count($data);
	  if ($num > $max_cols) $max_cols = $num;
	  $i100++;
	  if (  $i100>400  ) break;
	}
	fclose($f);



	?><table border="1"><?
	?><tr><?
	?><td style="font-size:9px; vertical-align:top;" valign="top">номер<br />(кол-во полей)</td><?
	for ($i=0; $i<$max_cols; $i++) {
		?><td valign="top" style="vertical-align:top;"><? print "<strong>поле&nbsp;$i</strong>"; ?><br /></td><?
	}
	?></tr><?

	$f = fopen($cfg->filename, "r") or die("Ошибка!");
	for ($i=0; $datastr=fgets($f,4096); $i++) {
          $data = explode($cfg->delimiter,$datastr);
	  $num = count($data);
	  ?><tr><td><? echo "<h3>$i($num)</h3>"; ?></td><?
            for ($c=0; $c<$num; $c++){
	  	?><td><? print @iconv("windows-1251", "UTF-8", $data[$c] ); ?></td><?
            }
	  ?></tr><?
	}
	?></table><?
	fclose($f);
}

function csvin_wrapper( &$txt ){
    return safelySqlStr(trim(@iconv("windows-1251", "UTF-8", $txt )));
}
?>