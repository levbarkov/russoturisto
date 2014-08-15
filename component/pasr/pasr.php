<?php
// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $icom, $option;
if (  strcmp($icom, "pasr")==0  ){
	if (  isset ($_REQUEST['task'])  ){
		if (  strcmp($_REQUEST['task'],"igetnewpass")==0  ){
	//		print "1";
			ggtr ($_REQUEST); 
			global $database;
			global $mosConfig_live_site, $mosConfig_sitename;
			global $mosConfig_mailfrom, $mosConfig_fromname;
		
			// simple spoof check security
			josSpoofCheck();
			
			$_live_site = $mosConfig_live_site;
			$_sitename 	= $mosConfig_sitename;	
			$checkusername	= stripslashes( mosGetParam( $_POST, 'checkusername', '' ) );
			$query = "SELECT id"
			. "\n FROM #__users WHERE username = " . $database->Quote( $checkusername );
			$database->setQuery( $query );
			if (!($user_id = $database->loadResult()) || !$checkusername ) {
				mosRedirect( "index.php?c=$option&mosmsg="._ERROR_PASS );
			}
		
			$newpass = mosMakePassword();
			$message = _NEWPASS_MSG;
			eval ("\$message = \"$message\";");
			$subject = _NEWPASS_SUB;
			eval ("\$subject = \"$subject\";");
			
			// получаем mail
			$iuser = ggo ($user_id, "#__users");  $confirmEmail = $iuser->email;
		
			mosMail($mosConfig_mailfrom, $mosConfig_fromname, $confirmEmail, $subject, $message);
		
			$newpass = md5( $newpass );
			$sql = "UPDATE #__users"
			. "\n SET password = " . $database->Quote( $newpass )
			. "\n WHERE id = " . (int) $user_id
			;
			$database->setQuery( $sql );
			if (!$database->query()) {
				die("SQL error" . $database->stderr(true));
			}
		
			mosRedirect( 'index.php?c=pasr&mosmsg='. _NEWPASS_SENT );
		}
	}
}




		// used for spoof hardening
		$validate = josSpoofValue();
		?>
		<form action="index.php" method="post">

		<div class="componentheading"><?php echo _PROMPT_PASSWORD; ?></div>

<?
if (  strcmp($icom, "pasr")==0  ){
	if (  isset ($_REQUEST['mosmsg'])  ){
		?><div class="imes"><? print $_REQUEST['mosmsg']; ?></div><br /><?
	}
}
?>

		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="contentpane">
		<tr>
			<td colspan="3">
				<?php echo _NEW_PASS_DESC; ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo _PROMPT_UNAME; ?>
			</td>
			<td>
				<input type="text" name="checkusername" class="inputbox" size="40" maxlength="25" />
			</td>
			<td>
				<input type="submit" class="button" value="<?php echo _BUTTON_SEND_PASS; ?>" />
			</td>
		</tr>
		</table>
		<input type="hidden" name="c" value="pasr" />
		<input type="hidden" name="task" value="igetnewpass" /> 
		<input type="hidden" name="<?php echo $validate; ?>" value="1" />
		<input type="hidden" name="pi" value="12" />
		</form>
		<?php
?>

