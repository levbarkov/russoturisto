<?
global $reg;
$id = intval($_REQUEST['id']);

$db  = &$reg['db'];
$db->setQuery("SELECT * FROM #__users WHERE id = ".$id);
$db->loadObject($manager);

if($manager->gid > 17){
?><div style="width:350px; height:150px; padding-left:10px; padding-top:10px; text-align:left;">
	<table cellspacing="0" cellpadding="0" border="0" align="left" width="350" height="150" class="insite_ajax_form_table" style="padding:0px; margin:0px;" >
		<? if(  strlen($manager->small)>0  ) { ?><tr height="20"><td colspan="2" align="left" style="text-align: left; "><a href="/images/cab/logo/<?=$manager->org; ?>" class="fancy no-underline" ><img src="/images/cab/logo/<?=$manager->small; ?>" border="0" /></a></td></tr><? } ?>
		<tr height="20"><th colspan="2" style="padding-top:14px;"><?=desafelySqlStr( $manager->usersurname.' '.$manager->name.' '.$manager->userparentname ); ?></th></tr>
		<? if (strlen($manager->note_sms_tel1)>0) { ?> <tr><td align="left" style="width:70px; padding-top:4px;">Тел. </td><td align="left">+7 <?=$manager->note_sms_tel1." ".$manager->note_sms_tel2; ?> </td></tr><? } ?>
	    <? if (strlen($manager->email)>0) { ?><tr><td align="left" nowrap="nowrap" style="padding-right:15px; padding-top:4px; white-space:nowrap;">Эл.&nbsp;почта</td><td align="left"><?=$manager->email; ?></td.></tr><? } ?>
		<tr><td style="font-size: 8px;">&nbsp;</td></tr>
	</table>
</div>
	<script language="javascript">
	$(".fancy").fancybox();
	</script><? 
}
?>