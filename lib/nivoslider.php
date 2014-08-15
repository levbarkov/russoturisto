<?php

/**
 * sitelog
 */
class nivoslider {
	var $div_id = "nivo_slider";
	function java_init(){ ?>
		<style>
			#nivo_slider {
				position:relative;
				background:url(/includes/images/loading.gif) no-repeat 50% 50%; 
			}
			#nivo_slider img {
				position:absolute;
				top:0px;
				left:0px;
				display:none;
			}
		</style>
		<? 
			css("/includes/nivoslider/nivo-slider.css");			// nivo image slide effect
			js("/includes/nivoslider/jquery.nivo.slider.pack.js"); 	// nivo image slide effect
	}
	function ready_init(){ ?>
				$('#nivo_slider').nivoSlider({
					effect:'random',
					slices: 15,
					animSpeed: 1400,
					pauseTime: 7000,
					directionNav:true,
					directionNavHide:true,
					controlNav:true,
					manualAdvance:false
				});
	<? }
}
?>
