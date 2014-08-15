<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

class mosSection extends mosDBTable {
        /** @var int Primary key */
        var $id                                        = null;
        /** @var string The menu title for the Section (a short name)*/
        var $title                                = null;
        /** @var string The full name for the Section*/
        var $name                                = null;
        /** @var string */
        var $image                                = null;
        /** @var string */
        var $scope                                = null;
        /** @var int */
        var $image_position                = null;
        /** @var string */
        var $description                = null;
        /** @var boolean */
        var $published                        = null;
        /** @var boolean */
        var $checked_out                = null;
        /** @var time */
        var $checked_out_time        = null;
        /** @var int */
        var $ordering                        = null;
        /** @var int */
        var $access                                = null;
        /** @var string */
        var $params                                = null;

        /**
        * @param database A database connector object
        */
        function mosSection( &$db ) {
                $this->mosDBTable( '#__sections', 'id', $db );
        }
        // overloaded check function
        function check() {
                // check for valid name
                if (trim( $this->title ) == '') {
                        $this->_error = "Ваш раздел должен содержать заголовок.";
                        return false;
                }
                if (trim( $this->name ) == '') {
                        $this->_error = "Ваш раздел должен иметь название.";
                        return false;
                }
                // check for existing name
                $query = "SELECT id"
                . "\n FROM #__sections "
		. "\n WHERE name = " . $this->_db->Quote( $this->name )
		. "\n AND scope = " . $this->_db->Quote( $this->scope )
                ;
                $this->_db->setQuery( $query );

                $xid = intval( $this->_db->loadResult() );
                if ($xid && $xid != intval( $this->id )) {
                        $this->_error = "Уже имеется раздел с таким названием. Пожалуйста, измените название раздела.";
                        return false;
                }
                return true;
        }
}
?>