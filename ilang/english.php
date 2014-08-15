<?php
// no direct access
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

// Site page note found
define( '_404', 'We\'re sorry but the page you requested could not be found.' );
define( '_404_RTS', 'Return to site' );

define( '_SYSERR1', 'The database adapter is not available' );
define( '_SYSERR2', 'Could not connect to the database server' );
define( '_SYSERR3', 'Could not connect to the database' );

// common
DEFINE('_LANGUAGE','en');
DEFINE('_NOT_FIND_STAT_CONTENT','cannot find static content');
DEFINE('_ENTER_PASSWORD','Please enter password');
DEFINE('_ISO','iso-8859-1');
$now = date( 'Y-m-d H:i', time() );
DEFINE( '_CURRENT_SERVER_TIME', $now );
DEFINE( '_CURRENT_SERVER_TIME_FORMAT', '%Y-%m-%d %H:%M:%S' );
DEFINE( '_CMN_YES', 'Yes' );
DEFINE( '_CMN_NO', 'No' );
DEFINE('_REGWARN_NAME','Please enter your name.');
DEFINE('_REGWARN_UNAME','Please enter a User name.');
DEFINE('_VALID_AZ09',"Please enter a valid %s.  No spaces, more than %d characters and contain 0-9,a-z,A-Z");
DEFINE('_PROMPT_UNAME','Username:');
DEFINE('_REGWARN_MAIL','Please enter a valid e-mail address.');
DEFINE('_REGWARN_INUSE','This username/password already in use. Please try another.');
DEFINE('_REGWARN_EMAIL_INUSE', 'This e-mail is already registered. If you forgot the password click on "Lost your Password" and a new password will be sent to you.');
DEFINE('_SEL_AUTHOR','- Select Author -');
DEFINE('_SEL_SECTION','- Select rubric -');
DEFINE('_SEL_CATEGORY','- Select subrubric -');
DEFINE('_CMN_NEW_ITEM_FIRST','New Items default to the first place. Ordering can be changed after this Item is saved.');
DEFINE('_CMN_CENTER','Center');
DEFINE('_CMN_LEFT','Left');
DEFINE('_CMN_RIGHT','Right');
DEFINE('_CMN_NEW_ITEM_LAST','New Items default to the last place. Ordering can be changed after this Item is saved.');
DEFINE('_MAINMENU_DEL','* You cannot `delete` this Menu as it is required for the proper operation of Joomla! *');
DEFINE('_MAINMENU_HOME','* The 1st Published item in this Menu [mainmenu] is the default `Home page` for the site *');
DEFINE('_DATE_FORMAT_LC',"%A, %d %B %Y"); //Uses PHP's strftime Command Format
DEFINE('_STATIC_CONTENT','Static Content');
DEFINE('_SEARCH_ARCHIVED','Archived');
DEFINE('_CONCLUSION','Total $totalRows results found.');
DEFINE('_PN_RESULTS','Results');
DEFINE('_PN_OF','of');
DEFINE('_PN_PREVIOUS','Prev');
DEFINE('_PN_START','Start');
DEFINE('_PN_NEXT','Next');
DEFINE('_PN_END','End');
DEFINE('_TIME_STAT','Time');
DEFINE('_MEMBERS_STAT','Members');
DEFINE('_NEWS_STAT','News');
DEFINE('_USERNAME','Username');
DEFINE('_PASSWORD','Password');
DEFINE('_REMEMBER_ME','Remember me');
DEFINE('_LOST_PASSWORD','Lost Password?');
DEFINE('_BUTTON_LOGIN','Login');
DEFINE('_BUTTON_LOGOUT','Logout');
DEFINE('_HI','Hi, ');
DEFINE('_LOGIN_INCORRECT','Incorrect username or password. Please try again.');
DEFINE('_SEL_POSITION','- Select Position -');
DEFINE('_SEL_TYPE','- Select Type -');
DEFINE('_NO_PARAMS','');
DEFINE('_NOT_AUTH','You are not authorised to view this resource.');
DEFINE('_PROMPT_PASSWORD','Lost your Password?');
DEFINE('_NEW_PASS_DESC','Please enter your Username and e-mail address then click on the Send Password button.<br />'
.'You will receive a new password shortly.  Use this new password to access the site.<br /><br />');
DEFINE('_BUTTON_SEND_PASS','Send Password');	
DEFINE('_NEWPASS_MSG','The User account $checkusername has this e-mail associated with it.\n'
.'A web user from $mosConfig_live_site has just requested that a new password be sent.\n\n'
.' Your New Password is: $newpass\n\nIf you didn\'t ask for this, don\'t worry.'
.' You are seeing this message, not them. If this was an error just login with your'
.' new password and then change your password to what you would like it to be.');
DEFINE('_NEWPASS_SENT','New User Password created and sent!');
DEFINE('_ERROR_PASS','Sorry, no corresponding User was found');
// СЕКЦИЯ АДМИНИСТРАТОРА

?>
