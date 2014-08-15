<?php
defined( '_VALID_INSITE' ) or die( 'Direct Access to this location is not allowed.' );

class mosEasybook extends mosDBTable {
  var $gbid=null;
  var $gbip=null;
  var $gbname=null;
  var $gbmail=null;
  var $gbloca=null;
  var $gbpage=null;
  var $gbvote=null;
  var $gbtext=null;
  var $gbdate=null;
  var $gbcomment=null;
  var $gbedit=null;
  var $gbeditdate=null;
  var $published=null;
  var $gbicq=null;
  var $gbaim=null;
  var $gbmsn=null;
  var $gbyah=null;
  var $gbskype=null;
  function mosEasybook( &$db ) {
    $this->mosDBTable( '#__easybook', 'id', $db );
  }
}

?>