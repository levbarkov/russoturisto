<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

/**
* @package Joomla RE
* @subpackage Templates
*/
class HTML_templates {
	/**
	* @param array An array of data objects
	* @param object A page navigation object
	* @param string The option
	*/
	function showTemplates( &$rows, &$pageNav, $option, $client ) {
		global $my, $mosConfig_live_site;

		if ( isset( $row->authorUrl) && $row->authorUrl != '' ) {
			$row->authorUrl = str_replace( 'http://', '', $row->authorUrl );
		}

		//mosCommonHTML::loadOverlib();
		?>
		<script language="Javascript">
		<!--
		function showInfo(name, dir) {
			var pattern = /\b \b/ig;
			name = name.replace(pattern,'_');
			name = name.toLowerCase();
			if (document.adminForm.doPreview.checked) {
				var src = '<?php echo $mosConfig_live_site . ($client == 'admin' ? '/administrator' : '');?>/templates/'+dir+'/template_thumbnail.png';
				var html=name;
				html = '<br /><img border="1" src="'+src+'" name="imagelib" alt="Предпросмотр недоступен" width="206" height="145" />';
				return overlib(html, CAPTION, name)
			} else {
				return false;
			}
		}
		-->
		</script>

		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<td  width="100%">Темы сайта</td>
			<td align="right" style="display:none"><input type="checkbox" name="doPreview" checked="checked"/></td>
		</tr>
		</table>
		<table class="adminlist">
		<tr>
			<th width="1%">#</th>
			<th width="1%">&nbsp;</th>
			<th width="68%" class="title">Название</th>
			<th width="15%">По умолчанию</th>
			<th width="15%">Назначен</th>
		</tr>
		<?php
		$k = 0;
		for ( $i=0, $n = count( $rows ); $i < $n; $i++ ) {
			$row = &$rows[$i];
			?>
			<tr class="<?php echo 'row'. $k; ?>">
				<td><?php echo $pageNav->rowNumber( $i ); ?></td>
				<td><?php
				if ( $row->checked_out && $row->checked_out != $my->id ) {
					?>&nbsp;<?php
				} else {
					?><input type="radio" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->directory; ?>" onClick="isChecked(this.checked);" /><?php
				}
				?></td>
				<td align="left"><?php echo $row->directory;?></td>
					<td align="center">
					<?php
					if ( $row->published == 1 ) {
						?><img src="images/tick.png" alt="По умолчанию"><?php
					} else {
						?>&nbsp;<?php
					}
					?></td>
					<td align="center"><?
					if ( $row->assigned == 1 ) {
						?><img src="images/tick.png" alt="Назначен" /><?php
					} else {
						?>&nbsp;<?php
					}
					?></td>
			</tr><?php
		}
		?>
		</table>
		<?php echo $pageNav->getListFooter(); ?>

		<input type="hidden" name="ca" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		<input type="hidden" name="client" value="<?php echo $client;?>" />
		</form>
		<?php
	}


	/**
	* @param string Template name
	* @param string Source code
	* @param string The option
	*/
	function editTemplateSource( $template, &$content, $option, $client ) {
		global $mosConfig_absolute_path;
		$template_path =
			$mosConfig_absolute_path . ($client == 'admin' ? '/administrator':'') .
			'/templates/' . $template . '/index.php';
		?>
		<form action="index2.php" method="post" name="adminForm">
		<table cellpadding="1" cellspacing="1" border="0" width="100%">
		<tr>
			<td width="290"><table class="adminheading"><tr><th class="templates">HTML-редактор шаблона</th></tr></table></td>
			<td width="220">
				<span class="componentheading">index.php is :
				<b><?php echo is_writable($template_path) ? '<font color="green"> Доступен для записи</font>' : '<font color="red"> Недоступен для записи</font>' ?></b>
				</span>
			</td>
<?php
			if (mosIsChmodable($template_path)) {
				if (is_writable($template_path)) {
?>
			<td>
				<input type="checkbox" id="disable_write" name="disable_write" value="1"/>
				<label for="disable_write">Сделать недоступным для записи после сохранения</label>
			</td>
<?php
				} else {
?>
			<td>
				<input type="checkbox" id="enable_write" name="enable_write" value="1"/>
				<label for="enable_write">При сохранении игнорировать защиту от записи</label>
			</td>
<?php
				} // if
			} // if
?>
		</tr>
		</table>
		<table class="adminform">
			<tr><th><?php echo $template_path; ?></th></tr>
			<tr><td><textarea style="width:100%;height:500px" cols="110" rows="25" name="filecontent" class="inputbox"><?php echo $content; ?></textarea></td></tr>
		</table>
		<input type="hidden" name="template" value="<?php echo $template; ?>" />
		<input type="hidden" name="ca" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="client" value="<?php echo $client;?>" />
		</form>
		<?php
	}


	/**
	* @param string Template name
	* @param string Source code
	* @param string The option
	*/
	function editCSSSource( $template, &$content, $option, $client ) {
		global $mosConfig_absolute_path;
		$css_path =
			$mosConfig_absolute_path . ($client == 'admin' ? '/administrator' : '')
			. '/templates/' . $template . '/css/template_css.css';
		?>
		<form action="index2.php" method="post" name="adminForm">
		<table cellpadding="1" cellspacing="1" border="0" width="100%">
		<tr>
			<td width="280"><table class="adminheading"><tr><th class="templates">Редактор CSS шаблона</th></tr></table></td>
			<td width="260">
				<span class="componentheading">template_css.css is :
				<b><?php echo is_writable($css_path) ? '<font color="green"> Доступен для записи</font>' : '<font color="red"> Недоступен для записи</font>' ?></b>
				</span>
			</td>
<?php
			if (mosIsChmodable($css_path)) {
				if (is_writable($css_path)) {
?>
			<td>
				<input type="checkbox" id="disable_write" name="disable_write" value="1"/>
				<label for="disable_write">Сделать недоступным для записи после сохранения</label>
			</td>
<?php
				} else {
?>
			<td>
				<input type="checkbox" id="enable_write" name="enable_write" value="1"/>
				<label for="enable_write">При сохранении игнорировать защиту от записи</label>
			</td>
<?php
				} // if
			} // if
?>
		</tr>
		</table>
		<table class="adminform">
			<tr><th><?php echo $css_path; ?></th></tr>
			<tr><td><textarea style="width:100%;height:500px" cols="110" rows="25" name="filecontent" class="inputbox"><?php echo $content; ?></textarea></td></tr>
		</table>
		<input type="hidden" name="template" value="<?php echo $template; ?>" />
		<input type="hidden" name="ca" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="client" value="<?php echo $client;?>" />
		</form>
		<?php
	}


	/**
	* @param string Template name
	* @param string Menu list
	* @param string The option
	*/
	function assignTemplate( $template, &$menulist, $option ) {
		?>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminform">
		<tr>
			<th class="left" colspan="2">
			Назначение шаблона <?php echo $template; ?> для пунктов меню
			</th>
		</tr>
		<tr>
			<td valign="top" align="left">
			Страница(ы):
			</td>
			<td width="90%">
			<?php echo $menulist; ?>
			</td>
		</tr>
		</table>
		<input type="hidden" name="template" value="<?php echo $template; ?>" />
		<input type="hidden" name="ca" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		</form>
		<?php
	}


	/**
	* @param array
	* @param string The option
	*/
	function editPositions( &$positions, $option ) {
		$rows = 25;
		$cols = 2;
		$n = $rows * $cols;
		?>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th class="templates">
			Позиции модулей
			</th>
		</tr>
		</table>

		<table class="adminlist">
		<tr>
		<?php
		for ( $c = 0; $c < $cols; $c++ ) {
			?>
			<th width="25">
			#
			</th>
			<th align="left">
			Позиция
			</th>
			<th align="left">
			Описание
			</th>
			<?php
		}
		?>
		</tr>
		<?php
		$i = 1;
		for ( $r = 0; $r < $rows; $r++ ) {
			?>
			<tr>
			<?php
			for ( $c = 0; $c < $cols; $c++ ) {
				?>
				<td>(<?php echo $i; ?>)</td>
				<td>
				<input type="text" name="position[<?php echo $i; ?>]" value="<?php echo @$positions[$i-1]->position; ?>" size="10" maxlength="10" />
				</td>
				<td>
				<input type="text" name="description[<?php echo $i; ?>]" value="<?php echo htmlspecialchars( @$positions[$i-1]->description ); ?>" size="50" maxlength="255" />
				</td>
				<?php
				$i++;
			}
			?>
			</tr>
			<?php
		}
		?>
		</table>
		<input type="hidden" name="ca" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		</form>
		<?php
	}
}
?>