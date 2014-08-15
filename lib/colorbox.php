<?php

/**
 * sitelog
 */
class colorbox {
	var $theme = "theme4";
	function java_init(){
		css("/includes/colorbox/".$this->theme."/colorbox.css");
		js("/includes/colorbox/jquery.colorbox.js");
	}
}
?>
