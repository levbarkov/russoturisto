<?php

// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $reg;

$myform = new insiteform();
?>
<!-- ФОРМА ОТПРАВКИ SMS СООБЩЕНИЙ ЧЕРЕЗ ПОЧТУ -->
<style>
    .docasms{
        font-size: 16px;
        background:url(/theme/start/img/white1x1.png) repeat;
        padding: 10px;
    }

    .home-additional-news-item  td input{
        padding: 7px 7px 7px 10px;
        font-size: 24px;
        
        width: 140px;
    }
    .home-additional-news-item  td select{
        padding: 7px 7px 7px 7px;
        font-size: 24px;
        height: 30px;
        vertical-align: middle;
        background:url(/theme/start/img/white1x1.png) repeat;

    }
    .home-additional-news-item  td option{
        padding: 7px 7px 7px 7px;
        font-size: 24px;
        height: 20px;
        line-height: 20px;
        vertical-align: middle;
        background:url(/theme/start/img/white1x1.png) repeat;
        /* background-color: #ffffff; */
    }
</style>

<div class="home-additional-news-item">
    <div style="background:url(/theme/start/img/sms.png); background-position:center left; background-repeat:no-repeat; width:128px; height:128px; padding-left:60px; padding-top:40px; vertical-align:top;">
            <form <? ctrlEnter (submit) ?> name="mail2sms_sender" action="index2.php">
                    <table width="167" border="0" cellspacing="0" cellpadding="0" align="left" >
                        <tr><td valign="bottom" style="vertical-align:bottom; "><?
                                    ?><table cellpadding="0" cellspacing="0" height="30"  >
                                            <tr height="30">
                                                    <td width="47"  valign="bottom" style="vertical-align:bottom; "><input <? $myform->make_java_text_effect('sms_tel1', 'input_light'); ?> class="input_ajax input_width input_gray" type="text" maxlength="3" value="904" title="904" name="sms_tel1" id="sms_tel1" style="width:47px; background:url(/theme/start/img/white1x1.png);" /></td>
                                                    <td width="120" valign="bottom" style="vertical-align:bottom; "><input <? $myform->make_java_text_effect('sms_tel2', 'input_light'); ?> class="input_ajax input_width input_gray" type="text" maxlength="8" value="210-9659" title="210-9659" name="sms_tel2" id="sms_tel2" style="width:120px; background:url(/theme/start/img/white1x1.png);" /></td>
                                            </tr>
                                    </table><?
                              ?></td></tr>
                            <tr><td align="right" style="text-align:right"><select class="input_ajax input_width input_gray" style="padding:0; margin:0; width:100%;  float:right;  background:url(/theme/start/img/white1x1.png);" name="operator">
                                            <option value="etk">ЕТК</option>
                                            <option value="megafon_sibir">Megafon-Сибирь</option>
                                            <option value="beeline">Beeline</option>
                                            <option value="mts">MTC</option>
                                    </select></td></tr>
                            <tr><td  align="right" style="text-align:right"><a href="javascript: document.mail2sms_sender.submit(); void(0);">отправить</a><br /><?=ctrlEnterHint () ?></td></tr>
                    </table>
                <input name="ca" value="mail2sms" type="hidden">
            </form>
    </div>
</div><?


if (  ggrr('sms_tel2')!=''  &&  urldecode(ggrr('sms_tel2'))!='210-9659'  ){
	$mail2sms = new mail2sms();
	$mail2sms->tel = urldecode(ggrr('sms_tel1')).urldecode(ggrr('sms_tel2'));
	$mail2sms->tel = preg_replace("/[- ]/", "", $mail2sms->tel);
	$mail2sms->oper = ggrr('operator');
	$mail2sms->text = '123 тест test.';
	
	$mail2sms->sendSms();
	print 'Письмо отправлено на '.$mail2sms->mail_addr;

?>

<div class="docasms"><?=$mail2sms->helpMe() ?></div>
<? } 
?>