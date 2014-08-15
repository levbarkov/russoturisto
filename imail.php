<?php
/*######################################################################*/
/*######################################################################*/
/*#########################  MAIL SECTION   ############################*/
/*######################################################################*/
/*######################################################################*/
require_once( site_path. '/includes/phpmailer/class.phpmailer.php' );
/**
* Function to create a mail object for futher use (uses phpMailer)
* @param string From e-mail address
* @param string From name
* @param string E-mail subject
* @param string Message body
* @return object Mail object
*/
function mosCreateMail( $from='', $fromname='', $subject, $body ) {
        global $mosConfig_absolute_path, $mosConfig_sendmail;
        global $mosConfig_smtpauth, $mosConfig_smtpuser;
        global $mosConfig_smtppass, $mosConfig_smtphost;
        global $mosConfig_mailfrom, $mosConfig_fromname, $mosConfig_mailer, $MAILmailer, $MailcharSet, $sitename;

        $mail = new mosPHPMailer();

        $mail->PluginDir = $mosConfig_absolute_path .'/includes/phpmailer/';
        $mail->SetLanguage( 'en', $mosConfig_absolute_path . '/includes/phpmailer/language/' );
        $mail->CharSet         = $MailcharSet;
        $mail->IsMail();
        $mail->From         = $from ? $from : $mosConfig_mailfrom;
        $mail->FromName = $fromname ? $fromname : $mosConfig_fromname;
        $mail->Mailer         = $MAILmailer;

        // Add smtp values if needed
        if ( $mosConfig_mailer == 'smtp' ) {
                $mail->SMTPAuth = $mosConfig_smtpauth;
                $mail->Username = $mosConfig_smtpuser;
                $mail->Password = $mosConfig_smtppass;
                $mail->Host         = $mosConfig_smtphost;
        } else

        // Set sendmail path
        if ( $mosConfig_mailer == 'sendmail' ) {
                if (isset($mosConfig_sendmail))
                        $mail->Sendmail = $mosConfig_sendmail;
        } // if
        $mail->Subject      = $subject;
        $mail->Body         = $body;

        return $mail;
}

/**
* Mail function (uses phpMailer)
* @param string From e-mail address
* @param string From name
* @param string/array Recipient e-mail address(es)
* @param string E-mail subject
* @param string Message body
* @param boolean false = plain text, true = HTML
* @param string/array CC e-mail address(es)
* @param string/array BCC e-mail address(es)
* @param string/array Attachment file name(s)
* @param string/array ReplyTo e-mail address(es)
* @param string/array ReplyTo name(s)
* @return boolean
*/
function mosMail( $from, $fromname, $recipient, $subject, $body, $mode=0, $cc=NULL, $bcc=NULL, $attachment=NULL, $replyto=NULL, $replytoname=NULL ) {
	global $mosConfig_mailfrom, $mosConfig_fromname, $mosConfig_debug;

	// Allow empty $from and $fromname settings (backwards compatibility)
	if ($from == '') {
		$from = $mosConfig_mailfrom;
	}
	if ($fromname == '') {
		$fromname = $mosConfig_fromname;
	}

	// Filter from, fromname and subject
	if (!JosIsValidEmail( $from ) || !JosIsValidName( $fromname ) || !JosIsValidName( $subject )) {
		return false;
	}

        $mail = mosCreateMail( $from, $fromname, $subject, $body );

        // activate HTML formatted emails
        if ( $mode ) {
        	$mail->IsHTML(true);
        }
        if( is_array($recipient) ) {
			foreach ($recipient as $to) {
			if (!JosIsValidEmail( $to )) {
				return false;
			}
			$mail->AddAddress($to);
		}
        } else {
			if (!JosIsValidEmail( $recipient )) {
				return false;
			}
			$mail->AddAddress($recipient);
        }
    if (isset($cc)) {
		if (is_array( $cc )) {
			foreach ($cc as $to) {
				if (!JosIsValidEmail( $to )) {
					return false;
				}
				$mail->AddCC($to);
			}
		} else {
			if (!JosIsValidEmail( $cc )) {
				return false;
			}
                        $mail->AddCC($cc);
        }
	}
    if (isset($bcc)) {
		if (is_array( $bcc )) {
			foreach ($bcc as $to) {
				if (!JosIsValidEmail( $to )) { return false; }
				$mail->AddBCC( $to );
			}
		} else {
			if (!JosIsValidEmail( $bcc )) { return false; }
			$mail->AddBCC($bcc);
        }
	}
    if ($attachment) {
		if (is_array( $attachment )) {
			foreach ($attachment as $fname) {
				$mail->AddAttachment( $fname );
			}
		} else {
            $mail->AddAttachment($attachment);
		}
	}
	//Important for being able to use mosMail without spoofing...
	if ($replyto) {
		if (is_array( $replyto )) {
			reset( $replytoname );
			foreach ($replyto as $to) {
				$toname = ((list( $key, $value ) = each( $replytoname )) ? $value : '');
				if (!JosIsValidEmail( $to ) || !JosIsValidName( $toname )) {
					return false;
				}
				$mail->AddReplyTo( $to, $toname );
			}
        } else {
			if (!JosIsValidEmail( $replyto ) || !JosIsValidName( $replytoname )) {
				return false;
			}
			$mail->AddReplyTo($replyto, $replytoname);
		}
    }
	$mailssend = $mail->Send();

	if( $mail->error_count > 0 ) {
			//$mosDebug->message( "The mail message $fromname <$from> about $subject to $recipient <b>failed</b><br /><pre>$body</pre>", false );
			//$mosDebug->message( "Mailer Error: " . $mail->ErrorInfo . "" );
	}
	return $mailssend;
} // mosMail

/**
 * Checks if a given string is a valid email address
 *
 * @param	string	$email	String to check for a valid email address
 * @return	boolean
 */
function JosIsValidEmail( $email ) {
    $address = trim($email);
    if (!PHPMailer::ValidateAddress($address)) {
    	return false;
    }
	return true;
}

/**
 * Checks if a given string is a valid (from-)name or subject for an email
 *
 * @since		1.0.11
 * @deprecated	1.5
 * @param		string		$string		String to check for validity
 * @return		boolean
 */
function JosIsValidName( $string ) {
	/*
	 * The following regular expression blocks all strings containing any low control characters:
	 * 0x00-0x1F, 0x7F
	 * These should be control characters in almost all used charsets.
	 * The high control chars in ISO-8859-n (0x80-0x9F) are unused (e.g. http://en.wikipedia.org/wiki/ISO_8859-1)
	 * Since they are valid UTF-8 bytes (e.g. used as the second byte of a two byte char),
	 * they must not be filtered.
	 */
	$invalid = preg_match( '/[\x00-\x1F\x7F]/', $string );
	if ($invalid) {
		return false;
	} else {
		return true;
	}
}
/*  MAIL SECTION  */
?>