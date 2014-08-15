<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

/**
* @package Joomla RE
* @subpackage Polls
*/
class HTML_poll {

	function showPolls( &$rows, &$pageNav, $option ) {
		global $my;

//		mosCommonHTML::loadOverlib();
		?>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<td><?
			$iway[0]->name="Опросы";
			$iway[0]->url="index2.php?ca=poll&task=view";
			$iway[1]->name="Список опросов";
			$iway[1]->url="";

			i24pwprint_admin ($iway);
			?></td>
		</tr>
		</table>

		<table class="adminlist">
		<tr>
			<th width="5">
			#
			</th>
			<th width="20">
			<input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $rows ); ?>);" />
			</th>
			<th align="left">
			Заголовок опроса
			</th>
			<th width="10%" align="center">
			На сайте
			</th>
			<th width="10%" align="center">
			Параметры
			</th>
			<th width="10%" align="center">
			Задержка
			</th>
		</tr>
		<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row = &$rows[$i];
			$link 	= 'index2.php?ca=poll&task=editA&hidemainmenu=1&id='. $row->id;

			$task 	= $row->published ? 'unpublish' : 'publish';
			$img 	= $row->published ? 'publish_g.png' : 'publish_x.png';
			$alt 	= $row->published ? 'Опубликован' : 'Неопубликован';

			$checked 	= mosCommonHTML::CheckedOutProcessing( $row, $i );
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
				<?php echo $pageNav->rowNumber( $i ); ?>
				</td>
				<td>
				<?php echo $checked; ?>
				</td>
				<td align="left">
				<a href="<?php echo $link; ?>" title="Изменить опрос">
				<?php echo $row->title; ?>
				</a>
				</td>
				<td align="center">
				<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')">
				<img src="images/<?php echo $img;?>" width="12" height="12" border="0" alt="<?php echo $alt; ?>" />
				</a>
				</td>
				<td align="center">
				<?php echo $row->numoptions; ?>
				</td>
				<td align="center">
				<?php echo $row->lag; ?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</table>
		<?php echo $pageNav->getListFooter(); global $option; ?>

		<input type="hidden" name="ca" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0">
		</form>
		<?php
	}


	function editPoll( &$row, &$options, &$lists ) {
		?>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			// do field validation
			if (form.title.value == "") {
				alert( "Опрос должен иметь название" );
			} else if( isNaN( parseInt( form.lag.value ) ) ) {
				alert( "Задержка между ответами не должна быть нулевой" );
			//} else if (form.menu.options.value == ""){
			//	alert( "Опрос должен иметь страницы." );
			//} else if (form.adminForm.textfieldcheck.value == 0){
			//	alert( "Опрос должен иметь ответы." );
			} else {
				submitform( pressbutton );
			}
		}
		</script>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<td><?
			$iway[0]->name="Опросы";
			$iway[0]->url="";
			$iway[1]->name=$row->id ? 'Изменение' : 'Новый';
			$iway[1]->url="";

			i24pwprint_admin ($iway, 0);
			?></td>
		</tr>
		</table>

		<table class="adminform">
		<tr>
			<th colspan="4">
			Подробности
			</th>
		</tr>
		<tr>
			<td width="10%">
			Заголовок:
			</td>
			<td>
			<input class="inputbox" type="text" name="title" size="60" value="<?php echo $row->title; ?>" />
			</td>
			<td width="20px">&nbsp;

			</td>
			<td width="100%" rowspan="20" valign="top">
			Показывается при пунктах меню:
			<br />
			<?php echo $lists['select']; ?>
			</td>
		</tr>
		<tr>
			<td>
			Задержка между ответами:
			</td>
			<td>
			<input class="inputbox" type="text" name="lag" size="10" value="<?php echo $row->lag; ?>" /> (секунд между голосами)
			</td>
		</tr>
		<tr>
			<td valign="top">
			Опубликован (на сайте):
			</td>
			<td>
			<?php echo $lists['published']; ?>
			</td>
		</tr>
		<tr>
			<td colspan="3">
			<br /><br />
			Варианты ответов:
			</td>
		</tr>
		<?php
		for ($i=0, $n=count( $options ); $i < $n; $i++ ) {
			?>
			<tr>
				<td>
				<?php echo ($i+1); ?>
				</td>
				<td>
				<input class="inputbox" type="text" name="polloption[<?php echo $options[$i]->id; ?>]" value="<?php echo htmlspecialchars( stripslashes($options[$i]->text) ); ?>" size="60" />
				</td>
			</tr>
			<?php
		}
		for (; $i < 12; $i++) {
			?>
			<tr>
				<td>
				<?php echo ($i+1); ?>
				</td>
				<td>
				<input class="inputbox" type="text" name="polloption[]" value="" size="60"/>
				</td>
			</tr>
			<?php
		}
		?>
		</table>

		<input type="hidden" name="task" value="">
		<input type="hidden" name="ca" value="poll" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="textfieldcheck" value="<?php echo $n; ?>" />
		</form>
		<?php
	}

}
?>