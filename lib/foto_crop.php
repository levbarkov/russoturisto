<?php
/* 
 * используется для функция обрезания картинок
 */

/**
 * Description of filter
 */
class foto_crop {
	function get_sizes ($use_fix, $config_width, $config_height, $img_fullname){
		$this->fix = $use_fix;
		$this->config_width = $config_width;
		$this->config_height = $config_height;
		$imginfo = getimagesize($img_fullname);
		$this->image_width = abs($imginfo[0]);
		$this->image_height = abs($imginfo[1]);
		if (  $this->fix==1  ){
			if (  $this->config_height==0  )		{ $zoom   = $this->config_width / $this->image_width; }
			else if (  $this->config_width==0  )	{ $zoom   = $this->config_height / $this->image_height; }
			else{
				$zoom_h = $this->config_height / $this->image_height;
				$zoom_w = $this->config_width / $this->image_width;
				$this->zoom   = max($zoom_h, $zoom_w);
			}
			$this->new_image_width  = $this->config_width;
			$this->new_image_height = $this->config_height;
			
			$this->image_width_crop_selected = round($this->new_image_width / $this->zoom);
			$this->image_height_crop_selected = round($this->new_image_height/ $this->zoom);
			$this->new_image_left_offset = round(  ($this->image_width  - $this->image_width_crop_selected)/2  );
			$this->new_image_top_offset =  round(  ($this->image_height - $this->image_height_crop_selected)/2  );
		} else if (  $this->fix==0  ){
			if (  $this->config_height==0  )		{ $this->zoom   = $this->config_width / $this->image_width; }
			else if (  $this->config_width==0  )	{ $this->zoom   = $this->config_height / $this->image_height; }
			else{
				$zoom_h = $this->config_height / $this->image_height;
				$zoom_w = $this->config_width / $this->image_width;
				$this->zoom   = min($zoom_h, $zoom_w);
			}
			$this->new_image_height = $this->zoom<1 ? round($this->image_height*$this->zoom) : $this->image_height;
			$this->new_image_width  = $this->zoom<1 ? round($this->image_width*$this->zoom) : $this->image_width;
			$this->new_image_left_offset = 0;
			$this->new_image_top_offset = 0;
			$this->image_width_crop_selected = $this->image_width;
			$this->image_height_crop_selected = $this->image_height;
		}
	}
	
	function make_crop_table(){
		global $reg;		
/*		$height='fix';
		if (  $this->config_height=='auto'  ){
			$height='auto';
			$this->config_height = $this->config_width;
			$this->new_image_height = $this->new_image_width;
		} */
		//ggd ($this);
		?><table border="0" cellpadding="4" cellspacing="0" width="100%" align="center">
			<tr class="workspace"><td><?=$this->title ?><span style="float:right; ">Масштаб: <?=round($this->main_zoom*100) ?>%&nbsp;</span></td><td></td></tr>
			<tr class="workspace"><?
				?><td valign="top" style="vertical-align:top"><img id="<?=$this->photoid ?>" src="<?=$this->file_crop_url ?>" width="<?=round($this->main_zoom*$this->image_width) ?>px" /></td><?
				?><td width="100%" valign="top" style="vertical-align:top; padding-left:30px;">
				   <DIV id="<?=$this->previewid ?>" style="width: <?=$this->config_width ?>px; height: <?=$this->config_height ?>px; border:1px solid #464646; overflow:hidden; background:url(/includes/images/transparent_bg.gif) ">
					<IMG src="<?=$this->file_crop_url ?>" style="width: <?=$this->config_width ?>px; height: <?=$this->config_height ?>px;  ">
				   </DIV>
					  <table><tr><td <? if (  $reg['iadmin']==0  ) print 'style=" display:none; "' ?> >
						  <TABLE class="foto_crop_table" >
							<TR><TD colspan="2" style="font-size: 110%; font-weight: bold; text-align: left; padding-left: 0.1em;">Координаты</TD></TR>
							<TR><TD width="30%" style="width: 30%;">X<SUB>1</SUB>:</TD><TD width="70%" style="width: 70%;"><INPUT type="text" name="<?=$this->previewid ?>_x1" id="<?=$this->previewid ?>_x1" value="<?=$this->new_image_left_offset ?>"></TD></TR>
							<TR><TD>Y<SUB>1</SUB>:</TD><TD><INPUT type="text" name="<?=$this->previewid ?>_y1" id="<?=$this->previewid ?>_y1" value="<?=$this->new_image_top_offset ?>"></TD></TR>
							<TR><TD>X<SUB>2</SUB>:</TD><TD><INPUT type="text" name="<?=$this->previewid ?>_x2" id="<?=$this->previewid ?>_x2" value="<?=($this->new_image_left_offset+$this->image_width_crop_selected) ?>"></TD></TR>
							<TR><TD>Y<SUB>2</SUB>:</TD><TD><INPUT type="text" name="<?=$this->previewid ?>_y2" id="<?=$this->previewid ?>_y2" value="<?=($this->new_image_top_offset+$this->image_height_crop_selected) ?>"></TD></TR>
							
							<TR><TD colspan="2" style="font-size: 110%; font-weight: bold; text-align: left; padding-left: 0.1em;">Размеры</TD></TR>
							<TR><TD style="width: 20%;">Ширина:</TD><TD><INPUT type="text" value="-" name="<?=$this->previewid ?>_w" id="<?=$this->previewid ?>_w"></TD></TR>
							<TR><TD>Высота:</TD><TD><INPUT type="text" value="-" name="<?=$this->previewid ?>_h" id="<?=$this->previewid ?>_h" ></TD></TR>
							<TR><TD>ZOOM:</TD><TD><INPUT type="text" name="<?=$this->previewid ?>_zoom" id="<?=$this->previewid ?>_zoom"></TD></TR>
							
							<TR><TD colspan="2" style="font-size: 110%; font-weight: bold; text-align: left; padding-left: 0.1em;">Заданные в настройках параметры</TD></TR>
							<TR><TD style="width: 20%;">Ширина:</TD><TD><INPUT type="text" value="<?=$this->config_width ?>" name="<?=$this->previewid ?>_w_max" id="<?=$this->previewid ?>_w_max"></TD></TR>
							<TR><TD>Высота:</TD><TD><INPUT type="text" name="<?=$this->previewid ?>_h_max" id="<?=$this->previewid ?>_h_max" value="<?=$this->config_height ?>"></TD></TR>
							<TR><TD>Quality:</TD><TD><INPUT type="text" value="<?=$this->quality ?>"></TD></TR>
							<TR><TD>Тип фото:</TD><TD><select name="<?=$this->previewid ?>_type">
															<option <? if (  $this->type=='self'  ) 	print 'selected="selected"' ?> value="self">Исходный</option>
															<option <? if (  $this->type=='png'  ) 	print 'selected="selected"' ?> value="png">png</option>
															<option <? if (  $this->type=='jpg'  ) 	print 'selected="selected"' ?> value="jpg">jpg</option>
															<option <? if (  $this->type=='gif'  ) 	print 'selected="selected"' ?> value="gif">gif</option>
															<option <? if (  $this->type=='bmp'  ) 	print 'selected="selected"' ?> value="bmp">bmp</option>
														</select>
							</TD></TR>
							
							<TR><TD colspan="2" style="font-size: 110%; font-weight: bold; text-align: left; padding-left: 0.1em;">Применить эффекты:&nbsp;<span style="font-size:14px">&rarr;</span>&nbsp;<a class="highslide   " 
onclick="return hs.htmlExpand(this, { 
	outlineType: 'rounded-white',
	wrapperClassName: 'draggable-header', 
	objectType: 'ajax',
	width: '650',
	height: '650',
	align : 'center'
} )" href="/iadmin/component/foto/foto_effect_help.html">описание кодов</a></TD></TR>
							<TR><TD colspan="2"><textarea name="<?=$this->previewid ?>_effect" cols="40" rows="4" id="all_effects" ><?=$this->effect ?></textarea></TD></TR>
							
						  </TABLE><INPUT type="hidden" name="<?=$this->previewid ?>_w_full" id="<?=$this->previewid ?>_w_full" value="<?=$this->image_width ?>"><?
						  ?><INPUT type="hidden" name="<?=$this->previewid ?>_h_full" id="<?=$this->previewid ?>_h_full" value="<?=$this->image_height ?>"><?
						  ?><INPUT type="hidden" name="<?=$this->previewid ?>_main_zoom" id="<?=$this->previewid ?>_h_full" value="<?=$this->main_zoom ?>">
						 </td>
						 <td valign="top" style="padding-top:25px; vertical-align:top;"><? 
						 if (  $reg['iadmin']  ){ ?><input style="width:170px; height:292px;" type="submit" value="Сохранить фото" /><? } 
						 else if(  $this->previewid=='preview_small'  ){ ?><input style="width:170px; height:292px;" type="submit" value="Сохранить фото" /><? } ?></td>
					 </tr></table>
				</td>
			</tr><?
		?></table><?
	}
	function make_java_code(){
		$this->main_zoom = 1;
		while(1){
			if (  $this->main_zoom*$this->image_width>500  and  $this->main_zoom>0.11  ) $this->main_zoom = $this->main_zoom-0.1;
			else break;			
		}
		$imgAreaSelect->x1 = $this->new_image_left_offset*$this->main_zoom;
		$imgAreaSelect->y1 = $this->new_image_top_offset*$this->main_zoom;
		$imgAreaSelect->x2 = ($this->new_image_left_offset+$this->image_width_crop_selected)*$this->main_zoom;
		$imgAreaSelect->y2 = ($this->new_image_top_offset+$this->image_height_crop_selected)*$this->main_zoom-1;/*sdneo -1 ---- */
		if (  floor($imgAreaSelect->x2)>floor(($this->image_width -1)*$this->main_zoom)  ) $imgAreaSelect->x2 = ($this->image_width -1)*$this->main_zoom;
		if (  floor($imgAreaSelect->y2)>floor(($this->image_height-1)*$this->main_zoom)  ) $imgAreaSelect->y2 = ($this->image_height-1)*$this->main_zoom;
		
		if(  $this->select=='full'  ){ //   Нужно принудительно выделить все пространство
			$imgAreaSelect->x1 = 0;
			$imgAreaSelect->y1 = 0;
			$imgAreaSelect->x2 = ($this->image_width -1)*$this->main_zoom;
			$imgAreaSelect->y2 = ($this->image_height-1)*$this->main_zoom;
		}

		//ggd ($imgAreaSelect);

		?><SCRIPT type="text/javascript">
			function <?=$this->previewid ?>(img, selection) {
				if (!selection.width || !selection.height)	return;
				var scaleX = <?=$this->config_width ?> / selection.width;
				var scaleY = <?=$this->config_height ?> / selection.height;
				if (  scaleX>scaleY ) scale = scaleY;	else scale = scaleX;
				
				var iwidth =  Math.round(  (scale * <?=$this->image_width  ?>)*<?=$this->main_zoom ?>  );
				var iheight = Math.round(  (scale * <?=$this->image_height ?>)*<?=$this->main_zoom ?>  );
				
				$('#<?=$this->previewid ?> img').css({
					width: iwidth,
					height: iheight,
					marginLeft: -Math.round(  (scale * selection.x1)  ),
					marginTop:  -Math.round(  (scale * selection.y1)  )
				});		
				$('#<?=$this->previewid ?>_x1').val(Math.round(   selection.x1/<?=$this->main_zoom ?>)   );
				$('#<?=$this->previewid ?>_y1').val(Math.round(   selection.y1/<?=$this->main_zoom ?>)   );
				$('#<?=$this->previewid ?>_x2').val(Math.round(selection.x2/<?=$this->main_zoom ?>)   );
				$('#<?=$this->previewid ?>_y2').val(Math.round(selection.y2/<?=$this->main_zoom ?>)   );
				$('#<?=$this->previewid ?>_w').val(Math.round(selection.width/<?=$this->main_zoom ?>)   );
				$('#<?=$this->previewid ?>_h').val(Math.round(selection.height/<?=$this->main_zoom ?>)   );    
				$('#<?=$this->previewid ?>_zoom').val(scale*<?=$this->main_zoom ?>);
			}
			$(function () {
				$('#<?=$this->photoid ?>').imgAreaSelect({ 	x1: <?=floor(  $imgAreaSelect->x1  ) ?>, 
															y1: <?=floor(  $imgAreaSelect->y1  ) ?>, 
															x2: <?=floor(  $imgAreaSelect->x2  ) ?>, 
															y2: <?=floor(  $imgAreaSelect->y2  ) ?>, 
															/* aspectRatio: '1:<?=$this->new_image_height/$this->new_image_width ?>',  */
															/* keys: { arrows: 1, ctrl: 5, shift: 'resize' }, */
															handles: true,
															fadeSpeed: 500, 
															onSelectChange: <?=$this->previewid ?>, 
															onInit: <?=$this->previewid ?> 
														});
			});
		</SCRIPT><?
	}
	
	function make_foto($img_fullname, $prefix, $dir){
		error_reporting(E_ALL);
		if (!defined( 'CLASS_UPLOAD' )) { include(site_path.'/includes/class.upload/class.upload.php');	define( 'CLASS_UPLOAD', 1 ); }		
		ini_set("max_execution_time",0);
		$this->handle = new Upload($img_fullname);
		//w_max  = ширина с конфиге
		//w 	 = ширина выделенной области
		//w_full = ширина исходного изображения
		
		$new_w = $this->config_w; $new_h = $this->config_h;
		$real_r_offset = (   $_REQUEST[$prefix.'_w_full'] - ($_REQUEST[$prefix.'_x1'/*'_w_full'*/] + (1/$_REQUEST[$prefix.'_zoom'])*$this->config_w /*- $_REQUEST[$prefix.'_x2']*/)   );
		$real_t_offset = (   $_REQUEST[$prefix.'_h_full'] - ($_REQUEST[$prefix.'_y1'/*'_h_full'*/] + (1/$_REQUEST[$prefix.'_zoom'])*$this->config_h /*- $_REQUEST[$prefix.'_y2']*/)   );
		$real_x2 = $_REQUEST[$prefix.'_x1'] + $this->config_w*(1/$_REQUEST[$prefix.'_zoom']) ;
		$real_y2 = $_REQUEST[$prefix.'_y1'] + $this->config_h*(1/$_REQUEST[$prefix.'_zoom']) ;
		
		//ggtr01 ($real_x2);  ggtr01 ($real_y2);
		if (  $real_x2>$_REQUEST[$prefix.'_w_full']  ) { $new_w = $new_w - $_REQUEST[$prefix.'_zoom']*($real_x2-$_REQUEST[$prefix.'_w_full']);	$real_r_offset = 0; }
		if (  $real_y2>$_REQUEST[$prefix.'_h_full']  ) { $new_h = $new_h - $_REQUEST[$prefix.'_zoom']*($real_y2-$_REQUEST[$prefix.'_h_full']);	$real_t_offset = 0; }
		
		// добавить что это для фото у оторых размер меньше заданного , а не когда мы выделили очень немного
		if (  $this->zoom_ifsmall==0  and  $_REQUEST[$prefix.'_zoom']>1 ){			
			$new_w = $new_w/$_REQUEST[$prefix.'_zoom'];
			$new_h = $new_h/$_REQUEST[$prefix.'_zoom'];
//			$new_w = $_REQUEST[$prefix.'_w_full']-$real_r_offset;
//			$new_h = $_REQUEST[$prefix.'_h_full']-$real_t_offset; 
		} 
		// ggr ($_REQUEST); ggtr01($new_w);ggtr01($new_h); ggd ($this);
		
		if ($this->handle->uploaded) {    // then we check if the file has been "uploaded" properly in our case, it means if the file is present on the local file system
			$this->handle->image_resize          = true;
			$this->handle->image_precrop         = 	$_REQUEST[$prefix.'_y1'].' '.
													$real_r_offset.' '.
													$real_t_offset.' '.
													$_REQUEST[$prefix.'_x1'];
			$this->handle->image_x               = round($new_w);
			$this->handle->image_y               = round($new_h);
			$this->handle->jpeg_quality = $this->config->jpeg_quality;
			if (  $this->config->type!='self'  )	$this->handle->image_convert = $this->config->type; 
			
			// ggr ($this);ggd ();
			$this->handle->Process($dir);
		}
	}
	function make_foto_full($img_fullname, $dir){
		error_reporting(E_ALL);
		if (!defined( 'CLASS_UPLOAD' )) { include(site_path.'/includes/class.upload/class.upload.php');	define( 'CLASS_UPLOAD', 1 ); }		
		ini_set("max_execution_time",0);
	
		$this->handle = new Upload($img_fullname);
		/* if (  $this->zoom>1  ){
			$this->handle->Process($dir);	// save full image with no changes
		}
		else */
		if ($this->handle->uploaded) {    // then we check if the file has been "uploaded" properly in our case, it means if the file is present on the local file system
			$this->handle->image_resize          = true;
			if (  $this->config->image_x>0  ) 	$this->handle->image_x = $this->config->image_x;
			if (  $this->config->image_y>0  ) 	$this->handle->image_y = $this->config->image_y;
			if (  $this->config->image_x==0  ) 	$this->handle->image_ratio_x = true;
			if (  $this->config->image_y==0  ) 	$this->handle->image_ratio_y = true;
			$this->handle->jpeg_quality = $this->config->jpeg_quality;
			$this->handle->image_convert = 'jpg';
			$this->handle->Process($dir);
		}
	}
	function copy_foto($img_fullname, $dir){
		//error_reporting(E_ALL);
		if (!defined( 'CLASS_UPLOAD' )) { include(site_path.'/includes/class.upload/class.upload.php');	define( 'CLASS_UPLOAD', 1 ); }		
		ini_set("max_execution_time",0);
	
		$this->handle = new Upload($dir.$img_fullname);
		if ($this->handle->uploaded) {    // then we check if the file has been "uploaded" properly in our case, it means if the file is present on the local file system
			$this->handle->Process($dir);
		}
	}
	function round_png($dir){

		$filename = $this->handle->file_src_pathname;
		$radius = $this->handle->image_radius;
		/**
		 * Чем выше rate, тем лучше качество сглаживания и больше время обработки и
		 * потребление памяти.
		 *  
		 * Оптимальный rate подбирается в зависимости от радиуса.
		 */ 
		$rate = $this->handle->image_rate;
		
		$img = imagecreatefromstring(file_get_contents($filename));
		imagealphablending($img, false);
		imagesavealpha($img, true);
		
		$width = imagesx($img);
		$height = imagesy($img);
		
		$rs_radius = $radius * $rate;
		$rs_size = $rs_radius * 2;
		
		$corner = imagecreatetruecolor($rs_size, $rs_size);
		imagealphablending($corner, false);
		
		$trans = imagecolorallocatealpha($corner, 255, 255, 255, 127);
		imagefill($corner, 0, 0, $trans);
		
		$positions = array(
			array(0, 0, 0, 0),
			array($rs_radius, 0, $width - $radius, 0),
			array($rs_radius, $rs_radius, $width - $radius, $height - $radius),
			array(0, $rs_radius, 0, $height - $radius),
		);
		
		foreach ($positions as $pos) {
			imagecopyresampled($corner, $img, $pos[0], $pos[1], $pos[2], $pos[3], $rs_radius, $rs_radius, $radius, $radius);
		}
		
		$lx = $ly = 0;
		$i = -$rs_radius;
		$y2 = -$i;
		$r_2 = $rs_radius * $rs_radius;
		
		for (; $i <= $y2; $i++) {
		
			$y = $i;
			$x = sqrt($r_2 - $y * $y);
		
			$y += $rs_radius;
			$x += $rs_radius;
		
			imageline($corner, $x, $y, $rs_size, $y, $trans);
			imageline($corner, 0, $y, $rs_size - $x, $y, $trans);
		
			$lx = $x;
			$ly = $y;
		}
		
		foreach ($positions as $i => $pos) {
			imagecopyresampled($img, $corner, $pos[2], $pos[3], $pos[0], $pos[1], $radius, $radius, $rs_radius, $rs_radius);
		}		
		imagepng($img, $dir.$this->handle->file_src_name);

	}
	function make_effect($img_fullname, $dir, $effects){
		$effects = explode(",",$effects);
		
		error_reporting(E_ALL);
		if (!defined( 'CLASS_UPLOAD' )) { include(site_path.'/includes/class.upload/class.upload.php');	define( 'CLASS_UPLOAD', 1 ); }		
		ini_set("max_execution_time",0);
		
		$this->handle = new Upload($dir.$img_fullname);
		if ($this->handle->uploaded) {    // then we check if the file has been "uploaded" properly in our case, it means if the file is present on the local file system
			
			if (  count($effects)>0  )
			foreach ($effects as $effect){
				$this->handle->file_overwrite = true;
				$effect = explode("#", trim($effect) );
				
				if ($effect[0]=='gray'){
					$this->handle->image_greyscale = true;
					$this->handle->Process($dir);
				}
				else if ($effect[0]=='opacity'){
					// $effect[1] - background color, hex
					// $effect[2] - opacity, percent 0..100
					$this->handle->image_overlay_color  = '#'.$effect[1];
					$this->handle->image_overlay_percent = 100-$effect[2];
					$this->handle->Process($dir);
				}
				else if ($effect[0]=='tint'){
					// $effect[1] - tint color, hex
					$this->handle->image_tint_color = '#'.$effect[1];
					$this->handle->Process($dir);
				}
				else if ($effect[0]=='brightness'){
					// $effect[1] - brightness, value between -127 and 127
					$this->handle->image_brightness = $effect[1];
					$this->handle->Process($dir);
				}
				else if ($effect[0]=='reflection'){
					// $effect[1] - height, Format is either in pixels or percentage, such as 40, '40', '40px' or '40%' 
					// $effect[2] - space in pixels between the source image and the reflection, can be negative
					// $effect[3] - reflection background color, in hex
					// $effect[4] - opacity level at which the reflection starts, integer between 0 and 100
					$this->handle->image_reflection_height  = $effect[1];
					$this->handle->image_reflection_space   = $effect[2];
					$this->handle->image_default_color  = '#'.$effect[3];
					$this->handle->image_reflection_opacity = $effect[4];
					$this->handle->Process($dir);
				}
				else if ($effect[0]=='watermark'){
					// $effect[1] - СМЕЩЕНИЕ_Х, в px от левого края. может быть отрицательным
					// $effect[2] - СМЕЩЕНИЕ_Y, в px от верхнего края. может быть отрицательным
					// $effect[3] - ВЫРАВНИВАНИЕ - указание расположения, комбинация из 'TBLR' (top, bottom, left, right)
					// $effect[4] - WATERMARK_FILE
					if (  $effect[1]!=''  )	$this->handle->image_watermark_x  = $effect[1];
					if (  $effect[2]!=''  )	$this->handle->image_watermark_y  = $effect[2];
					if (  $effect[3]!=''  )	$this->handle->image_watermark_position  = $effect[3];
											$this->handle->image_watermark = site_path.$effect[4];
					$this->handle->Process($dir);
				}
				else if ($effect[0]=='round_png'){
					// $effect[1] - РАДИУС, в px
					// $effect[2] - КАЧЕСТВО
					if (  $effect[1]!=''  )	$this->handle->image_radius = $effect[1];
					if (  $effect[2]!=''  )	$this->handle->image_rate  	= $effect[2];
					$this->round_png($dir);
				}

			}
		}
	}

}
?>
