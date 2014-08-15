<?php 
function i24get_file_extension($file_name){
	$i = strlen($file_name);
	while (  ($file_name[$i]!='.')  &&  ($i>0)  ) $i--;
	return substr ($file_name, ($i+1));
}


$site_path = substr(__FILE__,  0,  strlen(__FILE__)-9);
$filename = site_path.$_REQUEST['f'];
//print $filename;
     //this function outputs an image when run. 
     Header("Content-type: image/gif"); 

/*     // get contents of a file into a string 
     $handle = fopen ($filename, "r"); 
     $contents = fread ($handle, filesize ($filename)); 
     echo $contents; 
     fclose ($handle); 
	 
	 
	 */





$src_file = $filename; //$thumb_name,
							$max_width_t = 153;
							$max_height_t = 0;
							$tag="";

$mosConfig_absolute_path = site_path;
	$types = array( 
        IMAGETYPE_JPEG => 'jpeg', 
        IMAGETYPE_GIF => 'gif', 
        IMAGETYPE_PNG => 'png' 
    ); 
    ini_set('memory_limit', '20M');
	
	$src_file = urldecode($src_file);
	$file_ext = i24get_file_extension($src_file);
		if (  strpos($file_ext, 'jpg') === false  ){
			if (  strpos($file_ext, 'jpeg') === false  ){
				if (  strpos($file_ext, 'gif') === false  ){
					if (  strpos($file_ext, 'png') === false  ){ return; }
					else{ $type = "png"; }
				} else{ $type = "gif"; }
			} else{ $type = "jpeg"; }
		} else{ $type = "jpeg"; }
	
	$max_thumb_h = $max_height_t;
	$max_thumb_w = $max_width_t;
	
	$read = 'imagecreatefrom' . $type; 
	$write = 'image' . $type; 
	
	$src_img = $read($src_file);
	
	// height/width
	$imginfo = getimagesize($src_file);
	$src_w = $imginfo[0];
	$src_h = $imginfo[1];
	
	if (  $max_height_t==0  )		{ $zoom   = $max_thumb_w / $src_w; }
	else if (  $max_width_t==0  )	{ $zoom   = $max_thumb_h / $src_h; }
	else{
		$zoom_h = $max_thumb_h / $src_h;
		$zoom_w = $max_thumb_w / $src_w;
		$zoom   = min($zoom_h, $zoom_w);
	}
	$dst_thumb_h  = $zoom<1 ? round($src_h*$zoom) : $src_h;
	$dst_thumb_w  = $zoom<1 ? round($src_w*$zoom) : $src_w;

	$dst_t_img = imagecreatetruecolor($dst_thumb_w,$dst_thumb_h);
	$white = imagecolorallocate($dst_t_img,255,255,255);
	imagefill($dst_t_img,0,0,$white);
	imagecopyresampled($dst_t_img,$src_img, 0,0,0,0, $dst_thumb_w,$dst_thumb_h,$src_w,$src_h);
	$textcolor = imagecolorallocate($dst_t_img, 255, 255, 255);
	if (isset($tag))
		imagestring($dst_t_img, 2, 2, 2, "$tag", $textcolor);
	$desc_img = $write($dst_t_img,"$thumb_name", 95);
/*}*/

?>