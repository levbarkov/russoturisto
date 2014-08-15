<?php

// нет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

/**
* Insite Admin_misc class
* 
*/
class HTML_admin_misc {


	function get_php_setting($val, $colour=0, $yn=1) {
		$r =  (ini_get($val) == '1' ? 1 : 0);
		
		if ($colour) {
			if ($yn) {
				$r = $r ? '<span style="color: green;">ON</span>' : '<span style="color: red;">OFF</span>';
			} else {
				$r = $r ? '<span style="color: red;">ON</span>' : '<span style="color: green;">OFF</span>';			
			}
			
			return $r; 
		} else {
		return $r ? 'ON' : 'OFF';
	}
	}

	function get_server_software() {
		if (isset($_SERVER['SERVER_SOFTWARE'])) {
			return $_SERVER['SERVER_SOFTWARE'];
		} else if (($sf = phpversion() <= '4.2.1' ? getenv('SERVER_SOFTWARE') : $_SERVER['SERVER_SOFTWARE'])) {
			return $sf;
		} else {
			return 'n/a';
		}
	}
	/**
	* Preview site
	*/
	function preview( $tp=0 ) {
		$tp = intval( $tp );
		?>
		<style type="text/css">
		.previewFrame {
			border: none;
			width: 95%;
			height: 600px;
			padding: 0px 5px 0px 10px;
		}
		</style>
		<table class="adminform">
		<tr>
			<th width="50%" class="title">
			Предпросмотр сайта
			</th>
			<th width="50%" style="text-align:right">
			<a href="<?php echo site_url . '/index.php?tp=' . $tp;?>" target="_blank">
			Открыть в новом окне
			</a>
			</th>
		</tr>
		<tr>
			<td width="100%" valign="top" colspan="2">
			<iframe name="previewFrame" src="<?php echo site_url . '/index.php?tp=' . $tp;?>" class="previewFrame" /></iframe>
			</td>
		</tr>
		</table>
		<?php
	}

	/*
	* Displays contents of Changelog.php file
	*/
	function changelog() {
		?>
		<pre>
			<?php
			readfile( $GLOBALS['mosConfig_absolute_path'].'/CHANGELOG.php' );
			?>
		</pre>
		<?php
	}
}
?>