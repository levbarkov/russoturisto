<?php
// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ ограничен' );

?><table border="0" cellpadding="0" cellspacing="0" align="left"><?
	?><tr height="27px"><?
		?><td width="158px" valign="middle" background="/theme/insite/images/s_box.gif"  style="padding-left:6px; background-repeat:no-repeat;"><?
			?><form name="ssdd" action="index.php" method="get"><?
			?><input type="text" style="border: 0px none ;" onfocus="if(this.value=='Поиск...') this.value='';" onblur="if(this.value=='') this.value='Поиск...';" value="Поиск..." size="20" id="isearch" class="iinput" alt="Поиск" maxlength="50" name="isearch" style="background:none; color:#d6e7f8"/><?
		?></td><?
		?><td width="30px" valign="middle"><a href="#" onClick="document.ssdd.submit( ); "><?
			?><img alt="Искать" border="0" src="<?php echo site_url; ?>/theme/insite/images/s_but.gif" width="30px" height="27px" align="absmiddle"></a><?
			?><input type="hidden" name="pi" value="10" /><?
			?><input type="hidden" name="c" value="search" /><?
			?></form><?
		?></td><?
	?></tr><?
?></table><?