<?php
/* 
 * используется для генерирования правильных seo-ссылок
 */

/**
 * Description of filter
 */
class seorecalc {
	var $good_table;
	var $cat_table;
	var $good_parent_field;
	var $cat_parent_field;
	var $cat_goods_field="goods";
	
	function recalc_req($sefurl, $catid, $realgoods) {
	global $database;
		$catgoods = ggsqlr ( "select count(id) from $this->good_table where $this->good_parent_field=$catid " );
		if (  $realgoods!=$catgoods  and  $catid>0  ){
			$i24r = new mosDBTable( $this->cat_table, "id", $database );	
			$i24r->id = $catid; 
			$cat_goods_field = $this->cat_goods_field;
			$i24r->$cat_goods_field = $catgoods; 
			if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
		}
		if (  $catgoods>0  and  $catid>0  ){
			$goods = ggsql ( "select * from $this->good_table where $this->good_parent_field=$catid " ); // ggtr ($goods);
			foreach ($goods as $good){
				if (  $good->sefnamefullcat!=$sefurl  ){	
					$i24r = new mosDBTable( $this->good_table, "id", $database );
					$i24r->id = $good->id; 	$i24r->sefnamefullcat = $sefurl;
					if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
				}
			}
		}	
		$cats = ggsql ( " select * from $this->cat_table where $this->cat_parent_field=$catid " ); //  ggtr($cats);
		if (  count($cats)>0  )
			foreach ($cats as $cat){	// ggtr ($cat,10);
				// обновляем sefurlfull для категории
				$i24r = new mosDBTable( $this->cat_table, "id", $database );
				$i24r->id = $cat->id;
				$i24r->sefnamefull = $sefurl;  $cat->sefnamefull = $sefurl; // ggtr01 ($i24r->sefnamefull);
				if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();
				// обновляем sefurlcat для объектов
				$cat_goods_field = $this->cat_goods_field;
				$this->recalc_req ($sefurl."/".$cat->sefname, $cat->id, $cat->$cat_goods_field);
			}
	}


}
?>
