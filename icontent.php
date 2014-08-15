<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

class mosContent extends mosDBTable {
        /** @var int Primary key */
        var $id                                        = null;
        /** @var string */
        var $title                                = null;
        /** @var string */
        var $title_alias                = null;
        /** @var string */
        var $seo_title                = null;
        /** @var string */
        var $sefname                = null;
        /** @var string */
        var $sefnamefullcat                = null;
        /** @var string */
        var $introtext                        = null;
        /** @var string */
        var $fulltext                        = null;
        /** @var int */
        var $state                                = null;
        /** @var int DEPRECATED */
        var $mask                                = null;
        /** @var int */
        var $catid                                = null;
        /** @var datetime */
        var $created                        = null;
        /** @var int User id*/
        var $created_by                        = null;
        /** @var string An alias for the author*/
        var $created_by_alias        = null;
        /** @var datetime */
        var $modified                        = null;
        /** @var int User id*/
        var $modified_by                = null;
        /** @var boolean */
        var $checked_out                = null;
        /** @var time */
        var $checked_out_time        = null;
        /** @var datetime */
        var $frontpage_up                = null;
        /** @var datetime */
        var $frontpage_down                = null;
        /** @var datetime */
        var $publish_up                        = null;
        /** @var datetime */
        var $publish_down                = null;
        /** @var string */
        var $small                                = null;
        /** @var string */
        var $mid                                = null;
        /** @var string */
        var $org                                = null;
        /** @var string */
        var $full                                = null;
        /** @var string */
        var $urls                                = null;
        /** @var string */
        var $attribs                        = null;
        /** @var int */
        var $version                        = null;
        /** @var int */
        var $parentid                        = null;
        /** @var int */
        var $ordering                        = null;
        /** @var string */
        var $metakey                        = null;
        /** @var string */
        var $metadesc                        = null;
        /** @var int */
        var $access                                = null;
        /** @var int */
        var $hits                                = null;

        /**
        * @param database A database connector object
        */
        function mosContent( &$db ) {
                $this->mosDBTable( '#__content', 'id', $db );
        }

        /**
         * Validation and filtering
         */
        function check() {
                // filter malicious code
                //$ignoreList = array( 'title', 'introtext', 'fulltext' );
                //$this->filter( $ignoreList );

                /*
                TODO: This filter is too rigorous,
                need to implement more configurable solution
                // specific filters
                $iFilter = new InputFilter( null, null, 1, 1 );
                $this->introtext = trim( $iFilter->process( $this->introtext ) );
                $this->fulltext =  trim( $iFilter->process( $this->fulltext ) );
                */

                if (trim( str_replace( '&nbsp;', '', $this->fulltext ) ) == '') {
                        $this->fulltext = '';
                }

                return true;
        }

        /**
        * Converts record to XML
        * @param boolean Map foreign keys to text values
        */
        function toXML( $mapKeysToText=false ) {
                global $database;

                if ($mapKeysToText) {
                        $query = "SELECT name"
                        . "\n FROM #__sections"
			. "\n WHERE id = " . (int) $this->sectionid
                        ;
                        $database->setQuery( $query );
                        $this->sectionid = $database->loadResult();

                        $query = "SELECT name"
                        . "\n FROM #__categories"
			. "\n WHERE id = " . (int) $this->catid
                        ;
                        $database->setQuery( $query );
                        $this->catid = $database->loadResult();

                        $query = "SELECT name"
                        . "\n FROM #__users"
			. "\n WHERE id = " . (int) $this->created_by
                        ;
                        $database->setQuery( $query );
                        $this->created_by = $database->loadResult();
                }

                return parent::toXML( $mapKeysToText );
        }
}
?>