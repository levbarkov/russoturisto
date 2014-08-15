<?php  
    
	
    /*
    Copyright 2008, 2009, 2010 Patrik Hultgren
    
    YOUR PROJECT MUST ALSO BE OPEN SOURCE IN ORDER TO USE PHP IMAGE EDITOR.
    OR ELSE YOU NEED TO BUY THE COMMERCIAL VERSION AT:
    http://www.shareit.com/product.html?productid=300296445&backlink=http%3A%2F%2Fwww.phpimageeditor.se%2F
    
    This file is part of PHP Image Editor.

    PHP Image Editor is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    PHP Image Editor is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with PHP Image Editor.  If not, see <http://www.gnu.org/licenses/>.
    */

    
    
	header("Cache-Control: no-store"); 
	header('content-type: text/html; charset: utf-8');
	include 'includes/constants.php';
	include 'config.php';
	include 'includes/functions.php';
	include 'classes/phpimageeditor.php';
	global $objPHPImageEditor;
	$objPHPImageEditor = new PHPImageEditor();
?>
<?php if (!$objPHPImageEditor->isAjaxPost) { ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>PHP Image Editor</title>
	    <script type="text/javascript" src="javascript/jquery-1.3.2.min.js"></script>
	    <script type="text/javascript" src="javascript/jquery.jcrop.js"></script>
        <script type="text/javascript" src="javascript/jquery.numeric.js"></script>
	    <script type="text/javascript" src="javascript/ui.core.js"></script>
	    <script type="text/javascript" src="javascript/ui.slider.js"></script>
	    <script type="text/javascript" src="javascript/ui.resizable.js"></script>
	    <script type="text/javascript" src="javascript/effects.core.js"></script>
        <script type="text/javascript" src="javascript/phpimageeditor.js"></script>
	    
	    <link rel="stylesheet" type="text/css" href="css/style.css"/>
	    <link rel="stylesheet" type="text/css" href="css/ui.resizable.css"/>
	    <link rel="stylesheet" type="text/css" href="css/ui.slider.css"/>
	    <link rel="stylesheet" type="text/css" href="css/jquery.jcrop.css"/>
	    
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		
        <script type="text/javascript">
	        var ImageMaxWidth = <?php PIE_Echo(IMAGE_MAX_WIDTH); ?>;
	        var ImageMaxHeight = <?php PIE_Echo(IMAGE_MAX_HEIGHT); ?>;
	        var ImageWidth = <?php PIE_Echo($objPHPImageEditor->GetWidthFinal()); ?>;
	        var ImageHeight = <?php PIE_Echo($objPHPImageEditor->GetHeightFinal()); ?>;
	        var TextIsRequired = "<?php PIE_Echo($objPHPImageEditor->texts["IS REQUIRED"]); ?>";
	        var TextMustBeNumeric = "<?php PIE_Echo($objPHPImageEditor->texts["MUST BE NUMERIC"]); ?>";
	        var TextWidth = "<?php PIE_Echo($objPHPImageEditor->texts["WIDTH"]); ?>";
	        var TextHeight = "<?php PIE_Echo($objPHPImageEditor->texts["HEIGHT"]); ?>";
	        var TextNotNegative = "<?php PIE_Echo($objPHPImageEditor->texts["NOT NEGATIVE"]); ?>";
	        var TextNotInRange = "<?php PIE_Echo($objPHPImageEditor->texts["NOT IN RANGE"]); ?>";
	        var TextCantBeLargerThen = "<?php PIE_Echo($objPHPImageEditor->texts["CANT BE LARGER THEN"]); ?>";
	        var TextAnUnexpectedError = "<?php PIE_Echo($objPHPImageEditor->texts["AN UNEXPECTED ERROR"]); ?>";
	        var Brightness = <?php PIE_Echo($objPHPImageEditor->inputBrightness); ?>;
	        var Contrast = <?php PIE_Echo($objPHPImageEditor->inputContrast); ?>;
	        var BrightnessMax = <?php PIE_Echo($objPHPImageEditor->brightnessMax); ?>;
	        var ContrastMax = <?php PIE_Echo($objPHPImageEditor->contrastMax); ?>;
            var FormAction = "<?php PIE_Echo($objPHPImageEditor->GetFormAction()); ?>";
            var FormId = "<?php PIE_Echo($objPHPImageEditor->formName); ?>";
            var ActionUpdate = "<?php PIE_Echo($objPHPImageEditor->actionUpdate); ?>";
            var ActionUndo = "<?php PIE_Echo($objPHPImageEditor->actionUndo); ?>";
            var ActionSaveAndClose = "<?php PIE_Echo($objPHPImageEditor->actionSaveAndClose); ?>";
            var ActionRotateLeft = "<?php PIE_Echo($objPHPImageEditor->actionRotateLeft); ?>";
            var ActionRotateRight = "<?php PIE_Echo($objPHPImageEditor->actionRotateRight); ?>";
            var ActionSaveAndClose = "<?php PIE_Echo($objPHPImageEditor->actionSaveAndClose); ?>";
            var MenuResize = "<?php PIE_Echo(MENU_RESIZE); ?>";
            var MenuRotate = "<?php PIE_Echo(MENU_ROTATE); ?>";
            var MenuCrop = "<?php PIE_Echo(MENU_CROP); ?>";
            var MenuEffects = "<?php PIE_Echo(MENU_EFFECTS); ?>";
            var AjaxPostTimeoutMs = <?php PIE_Echo(AJAX_POST_TIMEOUT_MS); ?>; 
			
        </script>
	</head>
	<body><div id="ggTitle" style="display:block; width:100%; height:10px; font-size:10px;">&nbsp;</div>
		<div id="phpImageEditor">
<?php } ?>

			<form id="<?php PIE_Echo($objPHPImageEditor->formName); ?>" name="<?php PIE_Echo($objPHPImageEditor->formName); ?>" method="post" action="<?php PIE_Echo($objPHPImageEditor->GetFormAction()); ?>">
				<?php if (!$objPHPImageEditor->ErrorHasOccurred()) { ?>
					 
					<div class="tabs">
					
						<div id="menu">
							<div class="<?php PIE_Echo($objPHPImageEditor->inputPanel == MENU_RESIZE ? 'selected' : 'not-selected'); ?>" id="menuitem_<?php PIE_Echo(MENU_RESIZE); ?>">
								<?php PIE_Echo($objPHPImageEditor->texts["RESIZE IMAGE"]); ?>
							</div>
							<div class="<?php PIE_Echo($objPHPImageEditor->inputPanel == MENU_ROTATE ? 'selected' : 'not-selected'); ?>" id="menuitem_<?php PIE_Echo(MENU_ROTATE); ?>">
								<?php PIE_Echo($objPHPImageEditor->texts["ROTATE IMAGE"]); ?>
							</div>
							<div class="<?php PIE_Echo($objPHPImageEditor->inputPanel == MENU_CROP ? 'selected' : 'not-selected'); ?>" id="menuitem_<?php PIE_Echo(MENU_CROP); ?>">
								<?php PIE_Echo($objPHPImageEditor->texts["CROP IMAGE"]); ?>
							</div>
							<?php if ($objPHPImageEditor->IsPHP5OrHigher()) { ?>
								<div class="<?php PIE_Echo($objPHPImageEditor->inputPanel == MENU_EFFECTS ? 'selected' : 'not-selected'); ?>" id="menuitem_<?php PIE_Echo(MENU_EFFECTS); ?>">
									<?php PIE_Echo($objPHPImageEditor->texts["EFFECTS"]); ?>
								</div>
							<?php } ?>
							<div class="<?php PIE_Echo($objPHPImageEditor->inputPanel == MENU_SAVE_AND_MORE ? 'selected' : 'not-selected'); ?>" id="menuitem_<?php PIE_Echo(MENU_SAVE_AND_MORE); ?>">
								<?="Сохранить / Отмена / Выход" ?>
							</div>

						</div>
							
						<div id="actionContainer">
			
							<div id="panel_<?php PIE_Echo(MENU_RESIZE); ?>" class="panel">
								<table cellpadding="0" cellspacing="0" border="0">
									<tr>
										<td>	
											<div class="field widthAndHeight">
												<div class="col-1">
													<label for="width"><?php PIE_Echo($objPHPImageEditor->texts["WIDTH"]); ?></label>
													<input class="input-number" type="text" name="width" id="width" value="<?php PIE_Echo($objPHPImageEditor->GetWidthFinal()); ?>"/>
													<input type="hidden" name="widthoriginal" id="widthoriginal" value="<?php PIE_Echo($objPHPImageEditor->GetWidth()); ?>"/>
												</div>
												<div class="col-2">
													<label for="height"><?php PIE_Echo($objPHPImageEditor->texts["HEIGHT"]); ?></label>
													<input class="input-number" type="text" name="height" id="height" value="<?php PIE_Echo($objPHPImageEditor->GetHeightFinal()); ?>"/>
													<input type="hidden" name="heightoriginal" id="heightoriginal" value="<?php PIE_Echo($objPHPImageEditor->GetHeight()); ?>"/>
												</div>
											</div>
											<div class="field">
												<input class="checkbox" type="checkbox" name="<?php PIE_Echo($objPHPImageEditor->fieldNameKeepProportions); ?>" id="<?php PIE_Echo($objPHPImageEditor->fieldNameKeepProportions); ?>" <?php PIE_Echo($objPHPImageEditor->inputKeepProportions ? 'checked="checked"' : ''); ?>/>
												<input type="hidden" name="keepproportionsval" id="keepproportionsval" value="<?php PIE_Echo($objPHPImageEditor->inputKeepProportions ? '1' : '0'); ?>"/>
												<label for="<?php PIE_Echo($objPHPImageEditor->fieldNameKeepProportions); ?>" class="checkbox"><?php PIE_Echo($objPHPImageEditor->texts["KEEP PROPORTIONS"]); ?></label>
											</div>
										</td>
										<td>
											<div class="help" id="resizehelp">
												<div class="help-header" id="resizehelpheader"><?php PIE_Echo($objPHPImageEditor->texts["INSTRUCTIONS"]); ?></div>
												<div class="help-content" id="resizehelpcontent"><?php PIE_Echo($objPHPImageEditor->texts["RESIZE HELP"]); ?></div>
											</div>
										</td>
									</tr>
								</table>
							</div>
		
							<div id="panel_<?php PIE_Echo(MENU_ROTATE); ?>" class="panel">
								<div class="field">
									<input id="btnRotateLeft" type="button" value="<?php PIE_Echo($objPHPImageEditor->texts["LEFT 90 DEGREES"]); ?>"/>
									<input id="btnRotateRight" type="button" value="<?php PIE_Echo($objPHPImageEditor->texts["RIGHT 90 DEGREES"]); ?>"/>
									<input type="hidden" name="rotate" id="rotate" value="-1"/>
								</div>
							</div>
		
							<div id="panel_<?php PIE_Echo(MENU_CROP); ?>" class="panel">
								<table cellpadding="0" cellspacing="0" border="0">
									<tr>
										<td>
											<div class="field">
												<div class="col-1">
													Ширина: <span id="cropwidth">0</span>
												</div>
												<div class="col-2">
													Высота: <span id="cropheight">0</span>
												</div><br /><br />
												<div style="display:block; white-space:nowrap">
													<input id="cropkeepproportions" class="checkbox" type="checkbox" name="cropkeepproportions" <?php PIE_Echo($objPHPImageEditor->inputCropKeepProportions ? 'checked="checked"' : ''); ?>/>
													<label class="checkbox" for="cropkeepproportions">Сохранить пропорции</label>
													<input id="cropkeepproportionsval" type="hidden" name="cropkeepproportionsval" value="<?php PIE_Echo($objPHPImageEditor->inputCropKeepProportions ? '1' : '0'); ?>"/>									
													<input id="cropkeepproportionsratio" type="hidden" name="cropkeepproportionsratio" value="<?php PIE_Echo($objPHPImageEditor->inputCropKeepProportionsRatio); ?>"/>
												</div>
											</div>

										</td>
										<td>
											<div class="field">
												<input class="input-number" type="hidden" name="croptop" id="croptop" value="0"/>
												<input class="input-number" type="hidden" name="cropleft" id="cropleft" value="0"/>
												<input class="input-number" type="hidden" name="cropright" id="cropright" value="0"/>
												<input class="input-number" type="hidden" name="cropbottom" id="cropbottom" value="0"/>
												<div class="help" id="crophelp">
													<div class="help-header" id="crophelpheader"><?php PIE_Echo($objPHPImageEditor->texts["INSTRUCTIONS"]); ?></div>
													<div class="help-content" id="crophelpcontent"><?php PIE_Echo($objPHPImageEditor->texts["CROP HELP"]); ?></div>
												</div>
											</div>
										</td>
									</tr>
								</table>
							</div>
							<div id="panel_<?php PIE_Echo(MENU_EFFECTS); ?>" class="panel" style="display: <?php PIE_Echo($objPHPImageEditor->IsPHP5OrHigher() ? 'block' : 'none'); ?>;">
								<table style="padding-left:20px;">
									<tr height="30"><input type="hidden" name="brightness" id="brightness" value="<?php PIE_Echo($objPHPImageEditor->inputBrightness); ?>"/>
										<td valign="bottom" style="vertical-align:bottom;"><label for="brightness"><?php PIE_Echo($objPHPImageEditor->texts["BRIGHTNESS"]); ?></label></td>
										<td valign="middle" style="vertical-align:middle;"><div id="brightness_slider_track"></div></td>
									</tr>
									<tr height="30"><input type="hidden" name="contrast" id="contrast" value="<?php PIE_Echo($objPHPImageEditor->inputContrast); ?>"/>
										<td valign="bottom" style="vertical-align:bottom;"><label for="contrast"><?php PIE_Echo($objPHPImageEditor->texts["CONTRAST"]); ?></label></td>
										<td valign="middle" style="vertical-align:middle;"><div id="contrast_slider_track"></div></td>
									</tr>
									<tr height="30">
										<td valign="middle" style="vertical-align:middle;" colspan="2">
											<input class="checkbox" type="checkbox" name="<?php PIE_Echo($objPHPImageEditor->actionGrayscale); ?>" id="<?php PIE_Echo($objPHPImageEditor->actionGrayscale); ?>" <?php PIE_Echo($objPHPImageEditor->inputGrayscale ? 'checked="checked"' : ''); ?>/>
											<label for="<?php PIE_Echo($objPHPImageEditor->actionGrayscale); ?>" class="checkbox"><?php PIE_Echo($objPHPImageEditor->texts["GRAYSCALE"]); ?></label>
											<input type="hidden" name="grayscaleval" id="grayscaleval" value="<?php PIE_Echo($objPHPImageEditor->inputGrayscale ? '1' : '0'); ?>"/>
										</td>
									</tr>

								</table>
							</div>
							




							<div id="panel_<?php PIE_Echo(MENU_SAVE_AND_MORE); ?>" class="panel_save" style=" <? if (  $_POST['gg_save_pan']!=1  ) print 'display:none;' ?> "   <? if (  $_POST['gg_save_pan']!=1  ) print 'show_save="0"'; else  print 'show_save="1"'; ?> >
								<table width="100%" >
									<tr>
										<td style="padding-left:14px; padding-top:0px;">
											<table width="100%" cellspacing="0">
												<tr>
													<td><strong style="font-size:14px">Сохранение изображения</strong></td>
													<td style="padding-left:14px; padding-top:5px; padding-bottom:15px; text-align:right" align="right"><a href="javascript: close_open_save_gg(); void(0);" ><img border="0"  alt="" src="/ibots/editors/tinymce/e24code/phpimageeditor/images/tab_close.png" width="24" height="24"></a></td>
												</tr>

												<tr>
													<td valign="middle" style="vertical-align:middle;" nowrap="nowrap">Качество % (0..100): </td><td><input size="3" class="input-number_s" name="gg_quality" id="gg_quality" type="text" value="<? if(  $_POST['gg_quality']!=''  ) print $_POST['gg_quality']; else print '90';   ?>" style="width:24px;" /></td>
												</tr>
												<tr>
													<td valign="middle" style="vertical-align:middle;">Сохранить изображение в: </td><td><select class="input-select_s" name="gg_save_to" id="gg_save_to">
														<option value="org" <? if (  $_POST['gg_save_to']=='org'  ) print 'selected="selected"'; ?> >Исходном формате</option>
														<option value="jpg" <? if (  $_POST['gg_save_to']=='jpg'  ) print 'selected="selected"'; ?>>JPG</option>
														<option value="gif" <? if (  $_POST['gg_save_to']=='gif'  ) print 'selected="selected"'; ?>>GIF</option>
														<option value="png" <? if (  $_POST['gg_save_to']=='png'  ) print 'selected="selected"'; ?>>PNG</option>
													</select></td>
												</tr>
												<tr>
													<td  valign="middle" style="vertical-align:middle;">Новый файл с префиксом: </td><td><input size="3" class="input-number_s" name="gg_prefix" id="gg_prefix" type="text" value="<?=$_POST['gg_save_prefix'] ?>" style="width:54px;" /></td>
												</tr>
												<tr>
													<td  valign="middle" style="vertical-align:middle;" colspan="2">
														<input type="button" <?php PIE_Echo($objPHPImageEditor->actions == "" ? 'disabled="disabled"' : ''); ?> id="btnsave" name="btnsave" value="Сохранить и вернуться в галерею"/>
													</td>
												</tr>
												<tr>
													<td colspan="2">&nbsp;</td>
												</tr>

												<tr>
													<td colspan="2"><strong style="font-size:14px;">Другие действия</strong></td>
												</tr>

											</table>
										</td>
									</tr>
									<tr>
										<td style="padding-left:14px; padding-top:7px; padding-bottom:10px;">
												<input type="button" id="btnupdate" name="btnupdate" value="<?php PIE_Echo($objPHPImageEditor->texts["UPDATE"]); ?>"/>
												<input type="button" <?php PIE_Echo($objPHPImageEditor->actions == "" ? 'disabled="disabled"' : ''); ?> id="btnundo" name="btnundo" value="<?php PIE_Echo($objPHPImageEditor->texts["UNDO"]); ?>"/>
												<input onclick='javascript:window.open("/ibots/editors/tinymce/e24code/AjexFileManager/index.html?type=image&connector=php&returnTo=tinymce&lang=ru&skin=dark&contextmenu=true", "_self")' type="button" id="btncancel" name="btncancel" value="Вернуться в галерею" />
										</td>
									</tr>
								</table>

							</div>
							
							<div style="width:100%; height:3px;">
								<div id="loading" style="display: none;"><div id="loading_bar" style="width: 0px;"></div></div>
							</div>
							
							
														
						</div>







						
		
					</div>
					<input type="hidden" name="actiontype" id="actiontype" value="<?php PIE_Echo($objPHPImageEditor->actionUpdate); ?>"/>
					<input type="hidden" name="panel" id="panel" value="<?php PIE_Echo($objPHPImageEditor->inputPanel); ?>"/>
					<input type="hidden" name="language" id="language" value="<?php PIE_Echo($objPHPImageEditor->inputLanguage); ?>"/>
					<input type="hidden" name="actions" id="actions" style="width: 1000px;" value="<?php $objPHPImageEditor->GetActions(); ?>"/>
					<input type="hidden" name="widthlast" id="widthlast" value="<?php PIE_Echo($objPHPImageEditor->GetWidthFinal()); ?>"/>
					<input type="hidden" name="heightlast" id="heightlast" value="<?php PIE_Echo($objPHPImageEditor->GetHeightFinal()); ?>"/>
					<input type="hidden" name="widthlastbeforeresize" id="widthlastbeforeresize" value="<?php PIE_Echo($objPHPImageEditor->GetWidthKeepProportions()); ?>"/>
					<input type="hidden" name="heightlastbeforeresize" id="heightlastbeforeresize" value="<?php PIE_Echo($objPHPImageEditor->GetHeightKeepProportions()); ?>"/>
					<input type="hidden" name="userid" id="userid" value="<?php PIE_Echo($objPHPImageEditor->userId); ?>"/>
					<input type="hidden" name="contrastlast" id="contrastlast" value="<?php PIE_Echo($objPHPImageEditor->inputContrast); ?>"/>
					<input type="hidden" name="brightnesslast" id="brightnesslast" value="<?php PIE_Echo($objPHPImageEditor->inputBrightness); ?>"/>
					<input type="hidden" name="isajaxpost" id="isajaxpost" value="false"/>
				<?php } ?>
			</form>
			<?php $objPHPImageEditor->GetErrorMessages(); ?>
			<div id="divJsErrors" class="error" style="display: none;">
				<ul id="ulJsErrors" style="display: none;"><li></li></ul>
			</div>
			<div><img src="images/empty.gif" alt=""/></div>
			<?php if (!$objPHPImageEditor->ErrorHasOccurred()) { ?>
				<div id="editimage">
					<img id="image" style="position: absolute; left: 0px; top: 0px; width: <?php PIE_Echo($objPHPImageEditor->GetWidthFinal()); ?>px; height: <?php PIE_Echo($objPHPImageEditor->GetHeightFinal()); ?>px;" alt="" src="<?php PIE_Echo($objPHPImageEditor->srcWorkWith); ?>?timestamp=<?php PIE_Echo(time()); ?>"/>
					<div id="imageResizerKeepProportions" style="diplay: <?php PIE_Echo(($objPHPImageEditor->inputKeepProportions && $objPHPImageEditor->inputPanel == MENU_RESIZE) ? 'block' : 'none'); ?>; width: <?php PIE_Echo($objPHPImageEditor->GetWidthFinal()); ?>px; height: <?php PIE_Echo($objPHPImageEditor->GetHeightFinal()); ?>px;"></div>
					<div id="imageResizerNoProportions" style="diplay: <?php PIE_Echo((!$objPHPImageEditor->inputKeepProportions && $objPHPImageEditor->inputPanel == MENU_RESIZE) ? 'block' : 'none'); ?>; width: <?php PIE_Echo($objPHPImageEditor->GetWidthFinal()); ?>px; height: <?php PIE_Echo($objPHPImageEditor->GetHeightFinal()); ?>px;"></div>
				</div>	
			<?php } ?>

<?php if (!$objPHPImageEditor->isAjaxPost) { ?>
		</div>
	</body>
	</html>
<?php } ?>

<?php $objPHPImageEditor->CleanUp(); ?>