<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

function ideleteUser($userid){
	if ($userid)  $userid = intval( $userid );
	else return true;

	$query = "DELETE FROM #__users WHERE id = " . (int) $userid;
	return ggsqlq ($query);
}
/**
 * Validation and filtering
 * @return boolean True is satisfactory
 */
function icheckUser(&$row) {
	$iUserError = false;
		global $iuniquemail, $database;

		// Validate user information
		if (trim( $row->name ) == '') return addslashes( _REGWARN_NAME );
		if (trim( $row->username ) == '') return addslashes( _REGWARN_UNAME );

		// check that username is not greater than 25 characters
		$username = $row->username;
		if ( strlen($username) > 25 ) {
			$row->username = substr( $username, 0, 25 ); 
		}

		// check that password is not greater than 50 characters
		$password = $row->password;
		if ( strlen($password) > 50 ) {
			$row->password = substr( $password, 0, 50 ); 
		}

		if (eregi( "[\<|\>|\"|\'|\%|\;|\(|\)|\&|\+|\-]", $row->username) || strlen( $row->username ) < 3) {
				return sprintf( addslashes( _VALID_AZ09 ), addslashes( _PROMPT_UNAME ), 2 );
		}

		if ((trim($row->email == "")) || (preg_match("/[\w\.\-]+@\w+[\w\.\-]*?\.\w{1,4}/", $row->email )==false))
				return addslashes( _REGWARN_MAIL );

		// check for existing username
		$query = "SELECT id"
		. "\n FROM #__users "
		. "\n WHERE username = " . $database->Quote( $row->username )
		. "\n AND id != " . (int)$row->id
		;
		$xid = intval( ggsqlr ($query) );
		if ($xid && $xid != intval( $row->id )) {
				return addslashes( _REGWARN_INUSE );
		}

		if ($iuniquemail) {
				// check for existing email
				$query = "SELECT id"
				. "\n FROM #__users "
				. "\n WHERE email = " . $database->Quote( $row->email )
				. "\n AND id != " . (int)$row->id
				;
				$xid = intval( ggsqlr($query) );
				if ($xid && $xid != intval( $row->id )) {
						return addslashes( _REGWARN_EMAIL_INUSE );
				}
		}

		return true;
}

function istoreUser( &$row, $isNew ) {
		$section_value = 'users';
		if(   !($isNew)  ) {
			$query = "UPDATE #__users SET name='".getUserStateFromRequest(  'name', "no_name" )."', 
			username='".getUserStateFromRequest(  'username', "no_name" )."', 
			usersurname='".getUserStateFromRequest(  'usersurname', "no_surname" )."', 
			userparentname='".getUserStateFromRequest(  'userparentname', "no_parentname" )."', 
			email='".getUserStateFromRequest(  'email', "no_email" )."', 
			gid='".intval(getUserStateFromRequest(  'gid', 18 ))."', 
			usertype='".$row->usertype."', 
			block='".intval(getUserStateFromRequest(  'block', 1 ))."', 
			sendEmail='".intval(getUserStateFromRequest(  'sendEmail', 0 ))."', 
			password='".$row->password."', 
			params='".$row->params."', 
			note_icq='".$row->note_icq."', 
			note_sms_tel1='".$row->note_sms_tel1."', 
			note_sms_tel2='".$row->note_sms_tel2."', 
			note_sms_oper='".$row->note_sms_oper."', 
			note_icq_enable='".$row->note_icq_enable."', 
			note_sms_enable='".$row->note_sms_enable."', 
			
			uinfo='".$row->uinfo."' 
			WHERE id=".intval(getUserStateFromRequest(  'id', 0 ))."
			";
			return ggsqlq ($query);
		} else {
			$query = "INSERT INTO `#__users`	(`name`,												`username`,													`usersurname`,													`userparentname`,														`email`,												`password`,				`usertype`,				`block`,										`sendEmail`,										`gid`,											`registerDate`,				`lastvisitDate`,		`activation`,							`params`,				`note_icq`,				`note_sms_tel1`,			`note_sms_tel2`,			`note_sms_oper`,			`note_icq_enable`,				`note_sms_enable`) 
					  VALUES 				  	('".getUserStateFromRequest(  'name', "no_name" )."',	'".getUserStateFromRequest(  'username', "no_name" )."', 	'".getUserStateFromRequest(  'usersurname', "no_surname" )."',	'".getUserStateFromRequest(  'userparentname', "no_parentname" )."',	'".getUserStateFromRequest(  'email', "no_email" )."',	'".$row->password."',	'".$row->usertype."',	".intval(getUserStateFromRequest(  'block', 1 )).",	".intval(getUserStateFromRequest(  'sendEmail', 0 )).",	".intval(getUserStateFromRequest(  'gid', 18 )).",	'".$row->registerDate."',	'0000-00-00 00:00:00',	'',				'".$row->params."',		'".$row->note_icq."',	'".$row->note_sms_tel1."',	'".$row->note_sms_tel2."',	'".$row->note_sms_oper."',	'".$row->note_icq_enable."',	'".$row->note_sms_enable."'); ";
			$result =  ggsqlq ($query);
			$new_user = ggsql (  "select id from #__users order by id desc limit 0,1"  );
			$row->id=$new_user[0]->id;
			return $result;
		}
}

function ilogoutUser( $cid ){
global $database;
	$query = "DELETE FROM #__session WHERE  userid=$cid ";
	$database->setQuery( $query );
	$database->query();
}

?>