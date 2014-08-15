<?php

// no direct access
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $mainframe;

//require_once( $mainframe->getPath( 'front_html', 'com_content') );

global $database;

$modules=ggsql("SELECT * FROM #__modules WHERE `module`='excatshow'");

foreach ($modules as $module){
	$params= new mosParameters($module->params);
	$param=$params->_params; 
	$cat_id=$param->cat_id;
	$limit=$param->limit;
}
// ggtr ($cat_id);
//$specs=ggsql("SELECT * from #__excat WHERE `publish` AND `parent`='$cat_id'");
//ggtr ($database, 55);
//	foreach($specs as $spec)
//	{   //ggtr($spec->id);
		$spec_objs=ggsql("SELECT * from #__exgood WHERE `publish` AND `parent`=$cat_id limit 0, $limit");
		if (sizeof($spec_objs)>0) {	
		
			foreach($spec_objs as $spec_obj)
			{
				echo"<div style='border:1px solid #ffffff;'><div style='font-weight:bold; padding:3px'><a href=\"index.php?c=excat&task=oview&id=$spec_obj->id\" style='color:#6f7b91' >$spec_obj->name</a> </div>";
				$query="SELECT * FROM #__exgood_foto WHERE `exgood_id`=$spec_obj->id LIMIT 1";
				$fotos=ggsql($query);  
 				if (sizeof($fotos)>0) { 
					foreach ($fotos as $foto){
						echo "<a title=\"нажмите чтобы увеличить\" onclick=\"return hs.expand(this)\" class=\"highslide\" href=\"/images/ex/good/$foto->org\" ><img src='/images/ex/good/$foto->small' border=0>	</a>&nbsp;";
					}
				}
				$excat = ggo ($cat_id, "#__excat");
				echo"
				<div style='padding:3px'><b>Категория:</b> ".($excat->name)." <br>
				<div style='background-color:#ffffff; color:#673d02; padding:3px'>Цена: $spec_obj->price руб. </div></div>";
			}	
		
		}
		
		
//	}





?>