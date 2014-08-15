<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ ограничен' );

class HTML_banners {

	function showBanners( &$rows, &$pageNav, $option ) {
		global $my;

		//mosCommonHTML::loadOverlib();
		?>
		<form action="index2.php" method="get" name="adminForm">
		<table class="adminheading">
		<tr>
			<th>
			Управление баннерами
			</th>
		</tr>
		</table>

		<table class="adminlist" border="0" >
		<tr>
			<th width="20">
			id
			</th>
			<th width="20">
			#
			</th>
			<th width="20">
			<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />
			</th>
			<th align="left" nowrap="nowrap" width="">
			Название баннера
			</th>
			<th width="10%" nowrap="nowrap" align="left">
			На сайте
			</th>
			<!--<th width="11%" nowrap="nowrap">
			Показов сделано
			</th>
			<th width="11%" nowrap="nowrap">
			Показов осталось
			</th>-->
			<th width="20%" align="left">
			Просмотров
			</th>
			<th width="70%" align="left">
			Нажатий
			</th>
		<th  align="left">
			Сбросить счетчик
			</th>
			<!--<th width="8%" nowrap="nowrap">
			% нажатий
			</th>-->
		</tr>
		<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row = &$rows[$i];
			$row->id 	= $row->bid;
			$link 		= 'index2.php?ca=banners&task=editA&hidemainmenu=1&id='. $row->id;

			$impleft 	= $row->imptotal - $row->impmade;
			if( $impleft < 0 ) {
				$impleft 	= "unlimited";
			}

			if ( $row->impmade != 0 ) {
				$percentClicks = substr(100 * $row->clicks/$row->impmade, 0, 5);
			} else {
				$percentClicks = 0;
			}

			$task 	= $row->showBanner ? 'unpublish' : 'publish';
			$img 	= $row->showBanner ? 'publish_g.png' : 'publish_x.png';
			$alt 	= $row->showBanner ? 'Опубликовано' : 'Не опубликовано';

			$checked 	= mosCommonHTML::CheckedOutProcessing( $row, $i );
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td align="center">
				<?php echo ($row->bid); ?>
				</td>
				<td align="center">
				<?php echo $pageNav->rowNumber( $i ); ?>
				</td>
				<td align="center">
				<?php echo $checked; ?>
				</td>
				<td align="left">
				<?php
				if ( $row->checked_out && ( $row->checked_out != $my->id ) ) {
					echo $row->name;
				} else {
					?>
					<a href="<?php echo $link; ?>" title="Изменить баннер">
					<?php echo $row->name; ?>
					</a>
					<?php
				}
				?>
				</td>
				<td align="center">
				<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')">
				<img src="images/<?php echo $img;?>" width="12" height="12" border="0" alt="<?php echo $alt; ?>" />
				</a>
				</td>
			<!--	<td align="center">
				<?php echo $row->impmade;?>
				</td>
				<td align="center">
				<?php echo $impleft;?>
				</td>-->
				<td align="center">
				<?php echo $row->impmade; ?>
				</td>
				<td align="center">
				<?php if($row->clicks) {echo $row->clicks;} else {echo "0";}?>
				</td>
				<td><form name="breset"><input type="submit" value="Сброс"/><input type="hidden" name="ca" value="banners"/>
					<input type="hidden" name="task" value="breset"><input type="hidden" name="id" value="<?php echo ($row->bid); ?>"/></form></td>
				<!--<td align="center">
				<?php echo $percentClicks;?>
				</td>-->
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</table>
		<?php echo $pageNav->getListFooter(); ?>

		<input type="hidden" name="ca" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		
		</form>
		<?php
	}

	function bannerForm( &$_row, &$lists, $_option ) {
		?>
		<script language="javascript" type="text/javascript">
		<!--
		function changeDisplayImage() { var str =document.adminForm.imageurl.value
					document.getElementById("parsHere").innerHTML="";
						
			if (document.adminForm.imageurl.value !='') {
			var ft = str.substr(str.length-3);
				if(ft=="swf"){
var swfOb="<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000'codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0'><param name='movie' value='../images/stories/"+document.adminForm.imageurl.value+"' /><param name='quality' value='high' /><embed src='../images/stories/"+document.adminForm.imageurl.value+"' quality='high' pluginspage='http://www.macromedia.com/go/getflashplayer' type='application/x-shockwave-flash'</embed></object>";
					
						
				} else {
 swfOb="<img src='../images/stories/"+document.adminForm.imageurl.value+"' name='imagelib' />"
				}
document.getElementById("parsHere").innerHTML = swfOb;
			} else {
				document.adminForm.imagelib.src='images/blank.png';
			}
		}
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			// do field validation
			if (form.name.value == "") {
				alert( "Введите название баннера." );
			} else if (getSelectedValue('adminForm','cid') < 1) {
				alert( "Выберите клиента." );
			} else if (form.custombannercode.value == "") {
				alert( "Заполните позицию." );form.custombannercode.focus();
			} else if (!getSelectedValue('adminForm','imageurl')) {
				alert( "Выберите изображение баннера." );
			} else if (form.clickurl.value == "") {
				alert( "Заполните URL для баннера." );
			} else {
				submitform( pressbutton );
			}
		}
		//-->
		</script>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th>
			Баннер:
			<small>
			<?php echo $_row->cid ? 'Изменение' : 'Новый';?>
			</small>
			</th>
		</tr>
		</table>

		<table class="adminform">
		<tr>
			<th colspan="2">
			Подробности
			</th>
		</tr>
		<tr>
			<td width="20%">
			Название баннера:
			</td>
			<td width="80%">
			<input class="inputbox" type="text" name="name" value="<?php echo $_row->name;?>" />
			</td>
		</tr>
		<tr>
			<td style="display:none;">
			Имя клиента:
			</td>
			<td align="left" style="display:none;">
			<select class="inputbox" size="1" name="cid">
<option value="0">-Выберите клиента-</option>
<option value="1">Динамический баннер с акциями</option>
<option value="2" selected>Динамический баннер с промоуслугами</option>
</select>
			</td>
		</tr>
		<tr>
			<td class="hid" id="hid" style="display:none;">
			Сколько раз показать:
			</td>
			<?php
			$unlimited = '';
			if ($_row->imptotal == 0) {
				$unlimited = 'checked="checked"';
				$_row->imptotal = '';
			}
			?>
			<td style="display:none;">
			<input class="inputbox" type="text" name="imptotal" size="12" maxlength="11" value="<?php echo $_row->imptotal;?>" />
			&nbsp;&nbsp;&nbsp;&nbsp;
			Всегда показывать <input type="checkbox" name="unlimited" <?php echo $unlimited;?> />
			</td>
		</tr>
		<tr>
			<td>
			Показывать баннер:
			</td>
			<td>
			<?php echo $lists['showBanner']; ?>
			</td>
		</tr>
		<tr>
			<td>
			URL клика:
			</td>
			<td>
			<input class="inputbox" type="text" name="clickurl" size="100" maxlength="200" value="<?php echo $_row->clickurl;?>" />
			</td>
		</tr>

		<tr>
			<td valign="top">
			Позиция:
			</td>
			<td>
			<input type="text" class="inputbox" id="poss" name="custombannercode" value="<?php echo $_row->custombannercode;?>" />
			</td>
		</tr>
		<tr >
			<td valign="top">
			Переключатель изображения баннера:
			</td>
			<td align="left">
			<?php echo $lists['imageurl']; ?>
			</td>
		</tr>
		<tr>
			<td valign="top">
			Изображение баннера:
			</td>
			<td valign="top" id="parsHere">
		
			<?php 
			if (eregi("swf", $_row->imageurl)) {
				?>
			<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" >
          			<param name="movie" value="../images/stories/<?echo $_row->imageurl;?>" />
          			<param name="quality" value="high" />
          			<embed src="../images/stories/<?echo $_row->imageurl;?>" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" ></embed>
			</object>
				<?php
			} elseif (eregi("gif|jpg|png", $_row->imageurl)) {
				?>
				<img src="../images/stories/<?php echo $_row->imageurl; ?>"  />
				<?php
			} else {
				?>
				<img src="images/blank.png" name="imagelib" />
				<?php
			}
			?>
			</td>
		</tr>
		<tr><td>Ширина* </td>
		<td>
		<input type="text" name="width" value="<? echo $_row->width; ?>"/> 
		</td>
		</tr>
		<tr><td>Высота* </td>
		<td>
		<input type="text" name="height" value="<? echo $_row->height; ?>"/> <br><br>* указывать для flash-баннеров!
		</td>
		</tr>
		<tr>
			<td colspan="3">
			</td>
		</tr>
		</table>

		<input type="hidden" name="ca" value="<?php echo $_option; ?>" />
		<input type="hidden" name="bid" value="<?php echo $_row->bid; ?>" />
		<input type="hidden" name="clicks" value="<?php echo $_row->clicks; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="impmade" value="<?php echo $_row->impmade; ?>" />
		</form>

<form name="bform" action="index2.php" method="post" enctype="multipart/form-data">
<table><tr>
<td><input type="file" name="uploadBanner" size="85" /></td>
			<td><input type="submit" value="Добавить Баннер" /></td>
			<td width="100%">
			<input type="hidden" name="ca" value="banners" />
			<input type="hidden" name="id" value="<?php echo $_row->bid; ?>" />
			<input type="hidden" name="task" value="upload" />
			<input type="hidden" name="hidemainmenu" value="1" />
			
			</td>
</tr>
</table>
</form>

		<?php
	}
}

/**
* Banner clients
* @package Joomla RE
*/
class HTML_bannerClient {

	function showClients( &$rows, &$pageNav, $option ) {
		global $my;

		mosCommonHTML::loadOverlib();
		?>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th>
			Управление клиентами баннеров
			</th>
		</tr>
		</table>

		<table class="adminlist">
		<tr>
			<th width="20">
			#
			</th>
			<th width="20">
			<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />
			</th>
			<th align="left" nowrap="nowrap">
			Имя клиента
			</th>
			<th align="left" nowrap="nowrap">
			Контакт
			</th>
			<th align="center" nowrap="nowrap">
			Количество активных баннеров
			</th>
			<th align="center" nowrap="nowrap">
			ID клиента
			</th>
		</tr>
		<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row = &$rows[$i];
			$row->id 	= $row->cid;
			$link 		= 'index2.php?ca=banners&task=editclientA&hidemainmenu=1&id='. $row->id;

			$checked 	= mosCommonHTML::CheckedOutProcessing( $row, $i );
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td width="20" align="center">
				<?php echo $pageNav->rowNumber( $i ); ?>
				</td>
				<td width="20">
				<?php echo $checked; ?>
				</td>
				<td width="35%">
				<?php
				if ( $row->checked_out && ( $row->checked_out != $my->id ) ) {
					echo $row->name;
				} else {
					?>
					<a href="<?php echo $link; ?>" title="Изменить клиента баннера">
					<?php echo $row->name; ?>
					</a>
					<?php
				}
				?>
				</td>
				<td width="35%">
				<?php echo $row->contact;?>
				</td>
				<td width="15%" align="center">
				<?php echo $row->bid;?>
				</td>
				<td width="15%" align="center">
				<?php echo $row->cid; ?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</table>
		<?php echo $pageNav->getListFooter(); ?>
		
		<input type="hidden" name="ca" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="listclients" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>
		<?php
	}

	function bannerClientForm( &$row, $option ) {
		?>
		<script language="javascript" type="text/javascript">
		<!--
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancelclient') {
				submitform( pressbutton );
				return;
			}
			// do field validation
			if (form.name.value == "") {
				alert( "Заполните имя клиента." );
			} else if (form.contact.value == "") {
				alert( "Заполните контактное имя." );
			} else if (form.email.value == "") {
				alert( "Заполните контактный E-mail." );
			} else {
				submitform( pressbutton );
			}
		}
		//-->
		</script>
		<table class="adminheading">
		<tr>
			<th>
			Клиент баннера:
			<small>
			<?php echo $row->cid ? 'Изменение' : 'Новый';?>
			</small>
			</th>
		</tr>
		</table>

		<form action="index2.php" method="post" name="adminForm">
		<table class="adminform">
		<tr>
			<th colspan="2">
			Подробности
			</th>
		</tr>
		<tr>
			<td width="10%">
			Имя клиента:
			</td>
			<td>
			<input class="inputbox" type="text" name="name" size="30" maxlength="60" valign="top" value="<?php echo $row->name; ?>" />
			</td>
		</tr>
		<tr>
			<td width="10%">
			Контактное имя:
			</td>
			<td>
			<input class="inputbox" type="text" name="contact" size="30" maxlength="60" value="<?php echo $row->contact; ?>" />
			</td>
		</tr>
		<tr>
			<td width="10%">
			Контактный E-mail:
			</td>
			<td>
			<input class="inputbox" type="text" name="email" size="30" maxlength="60" value="<?php echo $row->email; ?>" />
			</td>
		</tr>
		<tr>
			<td valign="top">
			Дополнительная информация:
			</td>
			<td>
			<textarea class="inputbox" name="extrainfo" cols="60" rows="10"><?php echo str_replace('&','&amp;',$row->extrainfo);?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="3">
			</td>
		</tr>
		</table>

		<input type="hidden" name="ca" value="<?php echo $option;?>" />
		<input type="hidden" name="cid" value="<?php echo $row->cid; ?>" />
		<input type="hidden" name="task" value="" />
		</form>
		<?php
	}
}
?>