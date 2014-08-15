<?php
// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

/**
* Writes the edit form for new and existing content item
*
* A new record is defined when <var>$row</var> is passed with the <var>id</var>
* property set to 0.
*/
class url_menu_html {

	function edit( $menu, $lists, $params, $option ) {
		global $mosConfig_live_site, $reg;
		?>
		<div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}

			// do field validation
			if (trim(form.name.value) == ""){
				alert( "Ссылка должна иметь имя" );
			} else if (trim(form.link.value) == ""){
				alert( "Вы должны ввести url." );
			} else {
				submitform( pressbutton );
			}
		}
		</script>

		<form <? ctrlEnterCtrlAS (' '.$reg['submit_apply_event'], ' '.$reg['submit_save_event']) ?> action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<td>
			<?php 
				$iway[0]->name='Меню';
				$iway[0]->url="";
				$iway[1]->name='Ссылка - URL';
				$iway[1]->url="";
				if (  $menu->id  ){
					$iway[2]->name="Изменение";
					$iway[2]->url="";
				} else {
					$iway[2]->name="Добавление";
					$iway[2]->url="";
				}
				i24pwprint_admin ($iway);
			?>
			</td>
		</tr>
		</table>

		<table width="100%">
		<tr valign="top">
			<td width="70%">
				<table class="adminform">
				<tr>
					<th colspan="2">Детали</th>
				</tr>
				<tr>
					<td width="30%" align="right">Название:</td>
					<td width="70%">
					<input class="inputbox" type="text" name="name" size="50" maxlength="150" value="<?php echo htmlspecialchars( $menu->name, ENT_QUOTES ); ?>" />
					</td>
				</tr>
				<tr>
					<td width="30%" align="right">Ссылка:</td>
					<td width="70%">
					<input class="inputbox" type="text" name="link" size="50" maxlength="250" value="<?php echo $menu->link; ?>" />
					</td>
				</tr>
				<tr>
					<td valign="top" align="right">При нажатии открыть в ...</td>
					<td><?php echo $lists['target']; ?></td>
				</tr>
				<tr>
					<td align="right">Меню сайта:</td>
					<td><?php echo $lists['menutypes']; ?></td>
				</tr>

				<tr>
					<td align="right">Родительский пункт меню:</td>
					<td id="parent_s_menu"><?php echo $lists['parent']; ?></td>
				</tr>
				<tr>
					<td valign="top" align="right">Порядок расположения:</td>
					<td><?php echo $lists['ordering']; ?></td>
				</tr>
				<tr>
					<td valign="top" align="right">Уровень доступа:</td>
					<td><?php echo $lists['access']; ?></td>
				</tr>
				<tr>
					<td valign="top" align="right">Опубликовано (на сайте):</td>
					<td><?php echo $lists['published']; ?></td>
				</tr>
				</table>
			</td>
			<td width="30%">
				<table class="adminform">
				<tr><th>Параметры</th></tr>
				<tr>
                                    <td><?php echo $params->render();?></td>
				</tr>
				</table>
			</td>
		</tr>
		</table>

		<input type="hidden" name="ca" value="<?php echo $option;?>" />
		<input type="hidden" name="id" value="<?php echo $menu->id; ?>" />
		<input type="hidden" name="type" value="<?php echo $menu->type; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>
		
		<?php
	}
}
?>