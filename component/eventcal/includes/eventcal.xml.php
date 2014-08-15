<?php
/**
 * eventCal
 *
 * XML-Parser - modified version of the Joomla! XML-parser-functions
 *
 * @version		$Id: eventcal.xml.php 58 2006-09-02 19:56:27Z kay_messers $
 * @package		eventCal
 * @author		Kay Messerschmidt <kay_messers@email.de>
 * @copyright	Copyright (C) 2006 Kay Messerschmidt. All rights reserved.
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link		http://forge.joomla.org/sf/projects/eventcal
 */

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

/**
 * This class extends the standard mosParameters-class from the joomla.xml.php file
 * The original file can be found in /includes/.
 */
require_once( $mosConfig_absolute_path . '/includes/domit/xml_domit_lite_include.php' );

/**
 * Parameters handler
 * @package eventCal
 */
class eventCalParameters extends mosParameters{
	/** @var object */
	var $_params 	= null;
	/** @var string The raw params string */
	var $_raw 		= null;
	/** @var string Path to the xml setup file */
	var $_path 		= null;
	/** @var string The type of setup file */
	var $_type 		= null;
	/** @var object The xml params element */
	var $_xmlElem 	= null;

	/**
	 * Renders a single parameter from a XML file
	 *
	 * @param	object	$param			A param tag node [by Reference]
	 * @param	string	control_name	The control name
	 * @return	array	Any array with the label, the form element and the tooltip
	 */
	function renderParam( &$param, $control_name='params' ) {
		$result = array();

		$control_name	= $param->getAttribute( 'name', $control_name );
		$name			= $control_name;
		$label			= $param->getAttribute( 'label' );

		$value			= $this->get( $name, $param->getAttribute( 'default' ) );
		$description	= $param->getAttribute( 'description' );

		$result[0]		= $param->getAttribute( 'label', $name );

		if ($result[0] == '@spacer') {
			$result[0] = '';
		} else {
			$result[0] = mosToolTip( addslashes( $description ), addslashes( $result[0] ), '', '', $result[0], '#', 0 );
		}
		$type = $param->getAttribute( 'type' );

		if (in_array( '_form_' . $type, $this->_methods )) {
			$result[1] = call_user_func( array( &$this, '_form_' . $type ), $name, $value, $param, $control_name );
		} else {
			$result[1] = _HANDLER . ' = ' . $type;
		}

		if ( $description ) {
			$result[2] = mosToolTip( $description, "Title" );
		} else {
			$result[2] = '';
		}

		$result[3] = $name;

		return $result;
	}

	/**
	 * Renders all parameters from a XML file
	 *
	 * @author	Kay Messerschmidt
	 * @return	array		the generated HTML sourcecode
	 */
	function render() {
		global $mosConfig_absolute_path;	

		$component = mosGetParam( $_REQUEST, 'option', 'com_eventcal' );

		// Security check!
		if (!preg_match( '/^com_[\w\-]*$/', $component )) {
			die( 'Invalid params passed!' );
		}

		// Check if XML file exists
		$xmlfile = "$mosConfig_absolute_path/components/$component/eventcal.xml";
		if (!file_exists( $xmlfile )) {
			return array();
		}

		// Build up the array of parameter elements
		$xmlDoc = new DOMIT_Lite_Document();
		$xmlDoc->resolveErrors( true );
		if ($xmlDoc->loadXML( $xmlfile, false, true )) {
			$root = &$xmlDoc->documentElement;

			if ($root->getTagName() == 'eventcal' && $root->getAttribute( 'type' ) == 'mosparams' ) {
				$count_node_list = &$root->getElementsByPath( 'params' );
				$this->_methods = get_class_methods( 'eventCalParameters' );
				for ($i=1; $i <= $count_node_list->getLength(); $i++) {
					$this->_xmlElem =& $root->getElementsByPath( 'params', $i );
					$return[$i]->caption = $this->_xmlElem->attributes['caption'];
					if (isset( $this->_xmlElem->attributes['display'] )) {
						$return[$i]->display = $this->_xmlElem->attributes['display'];
					} else {
						$return[$i]->display = 'table-row';
					}
					$return[$i]->name = $this->_xmlElem->attributes['name'];
					foreach ($this->_xmlElem->childNodes as $childNode) {
						$return[$i]->fields[] = $this->renderParam( $childNode );
					}
				}
			}
		}

		return $return;
	}

	/**
	 * Extends the types of parameter elements to show a dropdown with some keys from a database table
	 *
	 * @param	string	The name of the form element
	 * @param	string	The value of the element
	 * @param	object	The xml element for the parameter
	 * @param	string	The control name
	 * @return	string	The html for the element
	 */
	function _form_db_list( $name, $value, &$node, $control_name ) {
		global $database;

		$size = $node->getAttribute( 'size' );
		$name = $node->getAttribute( 'name' );

		// database settings
		/**
		 * @TODO:
		 * Secure against SQL injections.
		 * $db->getEscaped is not good here, because $text etc. are not within quotes!
		 */
		$table	= /*$database->getEscaped( */ $node->getAttribute( 'dbs_table' ) /*)*/;
		$id		= intval( $node->getAttribute( 'dbs_value' ) );
		$text	= /*$database->getEscaped( */ $node->getAttribute( 'dbs_text' ) /*)*/;
		$where	= $node->getAttribute( 'dbs_where' );
		if ($where === null) {
			$where = '1';
		} else {
			//$where = $database->getEscaped( $where );
		}

		// initialising database
		$database->setQuery( "SELECT $id AS value, $text AS text FROM $table WHERE $where" );
		$db_options = $database->loadObjectList();

		// create a default entry
		$options[] = mosHTML::makeOption( "", "Select a $name" );

		// create entries from database result
		foreach ($db_options as $option) {
			$options[] = mosHTML::makeOption( $option->value, $option->text );
		}

		// in case the options array is empty there is nothing to choose, so we dont need to display the select-list
		// Kay: please take a look at this: $options has at least the standard option ("Select a $name"), count will always return > 1. Also, careful with return value, false is not a string. Enno
		if (!count($options)) {
			return false;
		} else {
			return mosHTML::selectList( $options, ''. $control_name , 'class="inputbox"', 'value', 'text');
		}
	}
}
?>
