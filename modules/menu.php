<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $reg;

/*
 * ВЫВОД МЕНЮ САЙТА. ПОДДЕРЖИВАЕТ 2 УРОВНЯ.
 * ВЫВОД СДЕЛАН ТАКИМ ОБРАЗОМ ЧТОБЫ В ИСХОДНОМ КОДЕ СОХРАНЯЛАСЬ ТАБУЛЯЦИЯ
 * РЕКОМЕНДУЕТСЯ ИСПОЛЬЗОВАТЬ КАК ШАБЛОН ДЛЯ НАПИСАНИЯ МОДУЛЯ МЕНЮ САЙТА
 */

$menu_name = 'Основное меню сайта';

$menus = menu::get_menu_items($menu_name, 0, 2);
?>
<ul id="tpl-menu">
	<? for ($i=0; $i<count($menus); $i++){
		if (  $menus[$i]->type=='url'  ){ // ОСНОВНОЕ МЕНЮ ?>
			<li><a href="<?=desafelysqlstr($menus[$i]->link) ;?>"><?=desafelysqlstr($menus[$i]->name) ;?></a></li>
		<?  } else { // ПОДМЕНЮ ВТОРОГО УРОВНЯ ?>
			<li><a class="showsub" href="javascript:void(0);"><?=desafelysqlstr($menus[$i]->name) ;?></a><?
			if (  count($menus[$i]->submenu)>0  ){ ?>

						<ul class="submenu">
				<? for ($j=0; $j<count($menus[$i]->submenu); $j++){ ?>
			<li><a href="<?=desafelysqlstr($menus[$i]->submenu[$j]->link) ;?>"><?=desafelysqlstr($menus[$i]->submenu[$j]->name) ;?></a></li>
				<?  } ?>
		</ul>
			<? }  ?>
		</li>
		<? }
	} ?>
	</ul>
<?
// отображаем кнопку редактирования меню
editme( 'menu', array('menutype'=>$menu_name, 'note'=>'редактировать меню'), 'small' );
editme( 'body', array('img'=>'quicklink', 'menutype'=>$menu_name, 'note'=>'Добавить в меню эту страницу'), 'small' );

return;
  /* ПРИМЕР ВЫВОДИМОГО HTML-КОДА. В исходном html-коде также сохраняется табуляция
        	<ul>
            	<li><a href="">Компания</a></li>
            	<li><a class="showsub" href="javascript:void(0);">Новости и Акции</a>
                        <ul class="submenu">
                            <li><a href="">Диагностика двигателя </a></li>
                            <li><a href="">Диагностика и ремонт ТНВД</a></li>

                        </ul>
                </li>
            	<li><a class="showsub" href="javascript:void(0);">Виды работ</a>
                        <ul class="submenu">
                            <li><a href="">Диагностика двигателя </a></li>
                            <li><a href="">Диагностика и ремонт ТНВД</a></li>
                            <li><a href="">Замена ремня ГРМ</a></li>
                            <li><a href="">Капитальный ремонт двигателя любого объема</a></li>
                            <li><a href="">Снятие / установка турбины</a></li>
                        </ul>
                </li>
            	<li><a href="">Магазин</a></li>
            	<li><a href="">Контакты</a></li>
            </ul>
*/ ?>