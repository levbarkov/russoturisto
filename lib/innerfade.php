<?php

/**
 * sitelog
 */
class innerfade {
	var $div_id = "nivo_slider";
	public static function java_init(){
            js("/includes/js/jquery.innerfade.js"); /* innerfade image slide effect */
	}
	public static function ready_init(){ ?>
            $('ul#baner-slideshow').innerfade({
                    speed: 2000,
                    timeout: 5000,
                    type: 'sequence',
                    containerheight: '480px'
            });
	<? }
}
?>
