<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ ограничен' );

class content_archive_category_menu_html {

	function editCategory( &$menu, &$lists, &$params, $option ) {
		global $mosConfig_live_site;
		?>
		<div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			var form = document.adminForm;
			<?php
			if ( !$menu->id ) {
				?>
				if ( getSelectedValue( 'adminForm', 'componentid' ) < 1 ) {
					alert( 'Вы должны выбрать категорию' );
					return;
				}
				sectcat = getSelectedText( 'adminForm', 'componentid' );
				sectcats = sectcat.split('/');
				section = getSelectedOption( 'adminForm', 'componentid' );
			
				form.link.value = "index.php?c=ex&task=excat&id=" + form.componentid.value;
				if ( form.name.value == '' ) {
					form.name.value = sectcats[1];
				}
				submitform( pressbutton );
				<?php
			} else {
				?>
				if ( form.name.value == '' ) {
					alert( 'Этот пункт меню должен иметь название' );
				} else {
					submitform( pressbutton );
				}
				<?php
			}
			?>
		}
		</script>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th>
			<?php echo $menu->id ? 'Изменение -' : 'Добавление -';?> Пункт меню :: Блог - Содержимое категории в архиве
			</th>
		</tr>
		</table>

		<table width="100%">
		<tr valign="top">
			<td width="60%">
				<table class="adminform">
				<tr>
					<th colspan="3">
					Детали
					</th>
				</tr>
				<tr>
					<td width="10%" align="right" valign="top">Название:</td>
					<td width="200px">
					<input type="text" name="name" size="30" maxlength="100" class="inputbox" value="<?php echo htmlspecialchars( $menu->name, ENT_QUOTES ); ?>"/>
					</td>
					<td>
					<?php
					if ( !$menu->id ) {
						echo mosToolTip( 'Если поле будет оставлено пустым, то автоматически будет использовано название категории' );
					}
					?>
					</td>
				</tr>
				<tr>
					<td valign="top" align="right">Категория:</td>
					<td>
					<?php /*echo $lists['componentid'];*/ $icats=ggsql("SELECT * FROM #__excat"); //ggtr($icats); 

						echo '<select class="inputbox" size="10" name="componentid">'; 
						foreach ($icats as $icat)
						{
						 echo "<option value='$icat->id'>$icat->name</option>";   
						}
						echo'</select>';
					?>
					</td>
				</tr>
				<tr>
					<td align="right">URL:</td>
					<td>
                    <?php //echo ampReplace($lists['link']);
							echo '<input value="" name="link" type="text">';  ?>
					</td>
				</tr>
				<tr>
					<td align="right">Родительский пункт меню:</td>
					<td>
					<?php echo $lists['parent']; ?>
					</td>
				</tr>
				<tr>
					<td valign="top" align="right">Порядок расположения:</td>
					<td>
					<?php echo $lists['ordering']; ?>
					</td>
				</tr>
				<tr>
					<td valign="top" align="right">Уровень доступа:</td>
					<td>
					<?php echo $lists['access']; ?>
					</td>
				</tr>
				<tr>
					<td valign="top" align="right">Опубликовано (на сайте):</td>
					<td>
					<?php echo $lists['published']; ?>
					</td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				</table>
			</td>
			<td width="40%">
				<table class="adminform">
				<tr>
					<th>
					Параметры
					</th>
				</tr>
				<tr>
					<td>
					<?php echo $params->render();?>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		</table>

		<input type="hidden" name="ca" value="<?php echo $option;?>" />
		<input type="hidden" name="id" value="<?php echo $menu->id; ?>" />
		<input type="hidden" name="menutype" value="<?php echo $menu->menutype; ?>" />
		<input type="hidden" name="type" value="ex_category" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>
		
		<?php
	}
}
?>