<?php

/**
 * mylist - ИЗБРАННОЕ
 * список хранится в базе, таким образом мы обеспечиваем его сохранность вне зависимости от сессии
 * подробнее - http://effect.krasinsite.ru/ideas/ami/ami.htm
 */
 
 /**
 * ВНУТРЯНЯЯ СТРУКТУРА ТАБЛИЦЫ ИЗБРАННОГО
 * $mylist->mylist = sql_obj_result(
               array(
			   		   'id'      => 2, - id объекта
					   'listid'	 => 'b99ccdf16028f015540f341130b6d8ec',	- уникальное неповторяющее число для идентификации объекта в списке
                       'userid'     => 1,
                       'options' => array('Size' => 'L', 'Color' => 'Red'),
					   'comp' => 'ex' - компонент, объект которого мы поместили в список
                    ),
               array(
			   		   'id'      => 2,
					   'listid'	 => 'b9fdg9ccgdfgdd015540f341130b6d8ec',
                       'userid'     => 1,
                       'options' => array('Size' => 'M', 'Color' => 'Black'),
					   'comp' => 'ex'
                    ),
               array(
			   		   'id'      => 78,
					   'listid'	 => 'b99ccdf16028f015540f34fg130bsd84f',
                       'userid'     => 1,
					   'comp' => 'ex'
                    )
            );
 */
class mylist {
	var $mylist_session_name = 'mylist';
	
	function __construct(){ ; }

	function java_init(){	//	формирование java кода для эффектов

		// sortable effect
		js("/includes/mylist/jquery-ui.js");
		
		// mylist section
		js("/includes/mylist/mylist.js");
		
	}
	function maketask($task){	//	ggd ($task);
		switch ( $task ) {
			case 'put':				$this->put();
									break;
			case 'del1':			$this->del1(  ggrr('mylist_listid')  );
									break;
			case 'delall':			$this->delall(  ggrr('mylist_comp')  );
									break;
			case 'update_order':	$this->update_order(  ggrr('mylist_comp')  );
									break;

		}
	}
	/** удалить все объекты из списка, если $comp="" - то для всех компонентов */
	function delall($comp=""){
		global $my;
		if (  !$my->id  ) return;
		if (  !isset($_REQUEST['mylist_comp'])  ) return;
		if (  $comp!=''  ) $comp_sql = " and comp='$comp' "; 
		else $comp_sql = ""; 
		
		ggsqlq (  " DELETE FROM #__mylist where userid='$my->id' $comp_sql "  );
	}
	function update_order($comp=""){
		global $my;
		if (  !$my->id  ) return;
		
		$array	= $_POST['arrayorder'];
		$count = 1;
		foreach ($array as $idval) {
			$query = "UPDATE #__mylist SET ordering = " . $count . " WHERE id = " . $idval;
			ggsqlq($query) or die('Error, insert query failed');
			$count ++;	
		}
	}

	/** удалить один объект из списка del1($listid) */
	function del1($listid){ // http://insite.dev/?	mylist_task=del1&
							//						mylist_listid=145&
		global $my;
		if (  !$my->id  ) return;
		if (  $listid==''  ) return;
		ggsqlq (  " DELETE FROM #__mylist where listid='$listid' "  );
	}
	function put(){	// http://insite.dev/?	mylist_task=put&
					//						mylist_id=145&
					//						mylist_comp=145&
		global $my;
		if (  !$my->id  ) return;
		$good->parent = ggri( 'mylist_parent' );
		$good->comp = ggrr( 'mylist_comp' );
		$good->listid = md5(   "mylist".ggrr( 'mylist_id' ).rand(0,99999)   );	// генерируем уникальное неповторяющее число
		$good->userid = $my->id;
		
		$this->put_good($good);
	}
	/** получить всесь список пользователя $userid для компонента $comp, если $comp="" - то для всех компонентов  */
	function get_list($userid, $comp){
		return ggsql (  "select * from #__mylist where userid='$userid' and comp='$comp' order by ordering "  );
	}
	function get_list_count($userid, $comp){
		return ggsqlr (  "select count(id) from #__mylist where userid='$userid' and comp='$comp' "  );
	}


	function get_max_ordering($parent, $userid, $comp){	/* поиск максимального значения ordering (позиция при отображении) в списке */
		$found_goods = ggsql (  "select ordering from #__mylist where userid='$userid' and comp='$comp' order by ordering DESC LIMIT 0,1 "  );
		if (  count($found_goods)>0  ) return $found_goods[0]->ordering;
		else return 0;
	}

	function find($good){	/* поиск элемета $good в списке */
		$found_goods = ggsql (  "select * from #__mylist where userid='$good->userid' and parent='$good->parent' and comp='$good->comp' LIMIT 0,1"  );
		if (  count($found_goods)>0  ) return $found_goods[0];
		else return false;
	}
	function put_good($good){
		global $reg;
		$find_good_listid = $this->find($good);
		if (  $find_good_listid==false  ){// ложим товар в избраннное
			$i24r = new mosDBTable( "#__mylist", "id", $reg['db'] );
			$i24r->id = 0;
			
			$i24r->parent = $good->parent;
			$i24r->comp = $good->comp;
			$i24r->listid = $good->listid;
			$i24r->userid = $good->userid;
			$i24r->ordering = $this->get_max_ordering($good->parent, $good->userid, $good->comp)+1;

			if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();

			// sql код
		}
	}
}
?>
