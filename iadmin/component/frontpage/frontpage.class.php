<?php
defined( '_VALID_INSITE' ) or die( 'Restricted access' );

class mosFrontPage extends mosDBTable {
	/** @var int Primary key */
	var $content_id	= null;
	/** @var int */
	var $ordering	= null;

	function mosFrontPage( &$db ) {
		$this->mosDBTable( '#__content_frontpage', 'content_id', $db );
	}
}
?>