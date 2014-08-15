<?php

defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

/**
 * Classes for eventCal Database Access
 */
class mosEventCal_Event extends mosDBTable {
	/** @var int */
	var $id;
	/** @var varchar */
	var $title;
	/** @var text */
	var $description;
	/** @var text */
	var $contact;
	/** @var varchar */
	var $url;
	/** @var varchar */
	var $email;
	/** @var varchar */
	var $catid;
	/** @var tinyint */
	var $published;
	/** @var datetime */
	var $start_date; 
	/** @var datetime */
	var $end_date;
	/** @var varchar */
	var $recur_type;
	/** @var int */
	var $recur_week;
	/** @var tinyint */
	var $recur_count;
	/** @var text */
	var $recur_except;
	/** @var date */
	var $checked_out = 0;
	/** @var date */
	var $checked_out_time	= 0;

	//extended fields not in eventcal-database
	/** @var varchar */
	var $category;
	/** @var text */
	var $cat_params;

	function mosEventCal_Event() {
		global $database;
		$this->mosDBTable( '#__eventcal', 'id', $database );
	}

	/**
	 * Overload load function to have additional stuff like category and color loaded as well as type
	 *
	 * @param	oid		integer		the id of the object to be loaded
	 * @return	false|object		False on error, DB table otherwise
	 */
	function load( $oid = null ) {
		$k = $this->_tbl_key;

		if ($oid !== null) {
			$oid = intval( $oid );
			$this->$k = $oid;
		}

		$oid = $this->$k;

		if ($oid === null) {
			return false;
		}

		// Note: Prior to PHP 4.2.0, Uninitialized class variables will not be reported by get_class_vars().
		$class_vars = get_class_vars( get_class( $this ) );
		foreach ($class_vars as $name => $value) {
			if (($name != $k) and ($name != "_db") and ($name != "_tbl") and ($name != "_tbl_key")) {
				$this->$name = $value;
			}
		}

		$this->reset();

		$query = "SELECT e.*, c.name AS category, c.params AS cat_params"
			. "\n FROM $this->_tbl AS e"
			. "\n LEFT JOIN #__categories AS c ON c.id = e.catid"
			. "\n WHERE e.$this->_tbl_key = $oid"
		;
		$this->_db->setQuery( $query );

		return $this->_db->loadObject( $this );
	}

	/**
	 * Overload store function
	 *
	 * @param	boolean		updateNulls		If false, null object variables are not updated
	 * @return	boolean						True if successful, false otherwise
	 */
	function store( $updateNulls=false ) {
		$k = $this->_tbl_key;

		//eliminate the unvalid params
		$this->category	= null;
		$this->cat_params	= null;

		if ($this->$k) {
			$ret = $this->_db->updateObject( $this->_tbl, $this, $this->_tbl_key, $updateNulls );
		} else {
			$ret = $this->_db->insertObject( $this->_tbl, $this, $this->_tbl_key );
		}
		if( !$ret ) {
			$this->_error = strtolower( get_class( $this ) ) . '::store failed <br />' . $this->_db->getErrorMsg();
			return false;
		} else {
			return true;
		}
	}

	/**
	 * encodes the color of a category from its params
	 * @return	hex				a hexadecimal color value
	*/
	function getColor() {
		$params		=& new mosParameters( $this->cat_params );
		return $params->get( 'color' , '');
	}

	/**
	 * Overlaod the database check function
	 *
	 * @param	string		error_msg	ByRef! Returns the error message
	 * @return	boolean					True on success, false otherwise
	 */
	function check( &$error_msg ) {
		$error_msg = "";

		if ($this->title == '') {
			$error_msg .= "misssing title ";
		}

		/**
		 * @TODO:
		 * url validation $this->url;
		 * email validation $this->email;
		 * $recur_type;
		 * $recur_week;
		 * $recur_count;
		 * $recur_except; 
		 */
		if ( !is_numeric( $this->catid ) ) {
			$error_msg .= "select a category ";
		}
		if ( !is_numeric( $this->start_date ) ) {
			$error_msg .= "missing start-date or wrong format ";
		}
		if ( !is_numeric( $this->end_date ) ) {
			$error_msg .= "missing end-date or wrong format ";
		}
		if ($error_msg) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Overlaod the database bind function
	 * de- / encoding of some passed variables as dates to timestamps and arrays to strings is necessary
	 *
	 * @param	array		$array		the array to be bind to the object
	 * @param	string		$ignore		a list of items in $array to be ignored when binding to object
	 * @return	null|string				null if operation was satisfactory, otherwise returns an error
	 */
	function bind( $array, $ignore='' ) {
		global $database;

		if (!is_array( $array )) {
			$this->_error = strtolower(get_class( $this ))."::bind failed.";
			return false;
		}

		/*
		 * preprocessing of data before binding
		 */
		$date = new mosEventCal_DateTimeObject( time() );
		
		// starting and ending-dates are encoded to Timestamps
		if (!$date->update( $array['enddate'] . $date->date_time_seperator . $array['endtime'] )) {
			echo "An error occured: Wrong endddate-format. This is displayed to help in debugging:<br/><pre>";
			print_r($array);
			echo "\n _NORMAL_DATE_FORMAT: " . _NORMAL_DATE_FORMAT;
			echo "\n _DATE_TIME_FORMAT: " . _DATE_TIME_FORMAT . "<br/>";
			print_r( $date );
			echo "</pre>";			
			exit;
		}
		$array['end_date'] = $date->timestamp;
		if (!$date->update( $array['startdate'] . $date->date_time_seperator . $array['starttime'] )) {
			echo "An error occured. This is displayed to help in debugging:<br/><pre>";
			print_r($array);
			echo "\n _NORMAL_DATE_FORMAT: " . _NORMAL_DATE_FORMAT;
			echo "\n _DATE_TIME_FORMAT: " . _DATE_TIME_FORMAT . "<br/>";
			print_r( $date );
			echo "</pre>";			
			exit;
		}
		$array['start_date'] = $date->timestamp;

		// recur_week array to string conversion
		if (array_key_exists( 'recur_week', $array )) {
			if (is_array( $array['recur_week'] )) {
				$array['recur_week'] = implode( $array['recur_week'] );
			}
			$array['recur_week'] = intval( $array['recur_week'] );
		}

		// recur exception needs to be imploded for saving as params-string in recur_excepts
		$recur_except = split( ',', $array['recur_except'] );

		// if the first item of the array is empty that item has to be deleted
		if (is_array( $recur_except ) && (count( $recur_except ) >= 1) && $recur_except[0] == '') {
			array_shift($recur_except);
		}

		foreach($recur_except as $exception) {
			$date->update( $exception );
			$exceptions[] = clone( $date->timestamp );
		}
		unset( $date );

		$array['recur_except'] = isset( $exceptions ) ? implode( "\n", $exceptions ) : '';

		//check if events for this category have to be published by an administrator
		$category = new mosCategory ( $database );
		$category->load( $array['catid'] );
		$parameter = new mosParameters( $category->params );
		if ($parameter->get( 'autopublish' )) {
			$array['published'] = '1';
		}

		return mosBindArrayToObject( $array, $this, $ignore );
	}

	/**
	 * bindRaw - bind this object to an array of a raw database result
	 * No time-conversions etc. will be done
	 *
	 * @param	array		$array		the array to be bind to the object
	 * @param	string		$ignore		a list of items in $array to be ignored when binding to object
	 * @return	boolean					True on success
	 */
	function bindRaw( $array, $ignore='' ) {
		return mosBindArrayToObject( $array, $this, $ignore, null, false );
	}
}

/**
 * DateTimeObject
 * @TODO: add and remove some functions
 * @TODO: more phpdoc comments throughout the whole class
 */
class mosEventCal_DateTimeObject {
	/** @var integer */
	var $timestamp;
	/** @var date object (associated array php is working with */
	var $date;
	var $date_format_short = "%x";
	var $time_format = "%X";
	var $date_time_format = "%c";
	var $date_time_seperator;
	var $date_splitter;
	var $time_splitter;
	var $day_pos;
	var $month_pos;
	var $year_pos;
	var $hour_pos;
	var $min_pos;

	/**
	 * Creator
	 *
	 * @TODO: Coding standards (only this function)
	 *
	 * @param	array		value		asociated array with timestamp or date or splitted date
	 * @return	boolean		True on success, false otherwise
	 */
	function mosEventCal_DateTimeObject( $value = null ) {

		//try to get the language-specific splitter characters from a test date
		/*	"try to" means that there may occur some problems on finding the splitters
			This would mean, that some elements of the array are not available and would produce errors on eccess.
			That's why I use the @ prefix. This prevents the php from printing the error
			and would cause the if-else-statement to "return false" */
		$testdate = _DATE_TIME_FORMAT;//strftime( "%x %X", 1234567890 ); //german: 14.02.2009 00:31:30
		$splitter = preg_replace( "/[0-9{1,2,4}]/", "", $testdate );
		if (@$splitter[0] == @$splitter[1]) {
			$this->date_splitter = $splitter[0];
		} else {
			return false;
		}
		if (@$splitter[2] != @$splitter[1] && @$splitter[2] != @$splitter[3]) {
			$this->date_time_seperator = $splitter[2];
		} else {
			return false;
		}
		if (isset($splitter[3])) {
			$this->time_splitter = $splitter[3];
		} else {
			return false;
		}

		//try to get the language specific positions of month, day and year
		$positions = split( '[' . $this->date_splitter . $this->date_time_seperator . $this->time_splitter . ']', $testdate );
		$this->day_pos = array_search( '14', $positions );
		$this->month_pos = array_search( '02', $positions );
		if (array_search( "2009", $positions) === false) {
			$this->year_pos = array_search( '09', $positions );
		} else {
			$this->year_pos = array_search( '2009', $positions );
		}
		$this->hour_pos = array_search( '00', $positions );
		$this->min_pos = array_search( '31', $positions );
		if ( ($this->day_pos === false) || ($this->month_pos === false) || ($this->year_pos === false) || ($this->hour_pos === false) || ($this->min_pos === false) ) {
			return false;
		}

		return $this->_convert( $value );
	}

	/**
	 * try to convert the input data into a timestamp
	 * @param	mixed any kind of date and / or time representing variable
	 * @return	boolean wether converting was successfull or not
	 */
	function _convert( $value = null ) {
	
		if (!isset($value)) {
			return false;
		} elseif (is_array( $value ) && count( $value ) == 5) {
			$this->timestamp = mktime( $value[0], $value[1], 0, $value[2], $value[3], $value[4] );
		} elseif (is_numeric( $value )) {
			$this->timestamp = intval( $value );
		/* this may be interesting on php 5 @ version 2.0 of eventCal
		} elseif (function_exists( 'variant_date_to_timestamp' )) {
			$this->timestamp = variant_date_to_timestamp( $value );
		*/
		} elseif (preg_match( '/^[0-9]{1,4}' . preg_quote( $this->date_splitter, '/' ) . '[0-9]{1,4}' . preg_quote( $this->date_splitter, '/' ) . '[0-9]{1,4}(' . preg_quote( $this->date_time_seperator, '/' ) . '[0-9]{1,2}' . preg_quote( $this->time_splitter, '/' ) . '[0-9]{1,2}(' . preg_quote( $this->time_splitter, '/' ) . '[0-9]{1,2})?)?/', $value )) {
			$value = split( '[' . $this->date_splitter . $this->date_time_seperator . $this->time_splitter . ']', $value );
			/*	Here again I have got the problems with perhaps not set elements of the array.
				This will happen if e.g. no time value is within your passed string.
				To prevent php to print the error on access to each user, the @-statement is used. */
			$this->timestamp = mktime(@$value[$this->hour_pos], @$value[$this->min_pos], 0, $value[$this->month_pos], $value[$this->day_pos], $value[$this->year_pos]);
		} else {
			return false;
		}
		if ($this->timestamp < 0) return false;
		$this->date = getdate( $this->timestamp );
		return true;
	}

	/*
	 *
	 */
	function update( $value ) {
		if ( $this->_convert( $value ) ) {
			$this->date = getdate( $this->timestamp );
			return true;
		} else return false;
	}

	/**
	 * function removes time values from timestamp; that means you get the timestamp of 0:00 o'clock 
	 */
	function clearTime() {
		$this->update( mktime( 0, 0, 0, $this->date['mon'], $this->date['mday'], $this->date['year'] ) );
	}

	/**
	 * function removes time values from timestamp; that means you get the timestamp of 0:00 o'clock 
	 */
	function clearDate() {
		$diff = mktime( 0, 0, 0, $this->date['mon'], $this->date['mday'], $this->date['year'] );
		$this->update( $this->timestamp - $diff );
	}

	/*
	 *
	 */
	function endOfDay() {
		return mktime( 23, 59, 59, $this->date['mon'], $this->date['mday'], $this->date['year'] );
	}

	/*
	 *
	 */
	function &startOfMonth() {
		$return = new mosEventCal_DateTimeObject( mktime( 0, 0, 0, $this->date['mon'], 1, $this->date['year'] ) );
		return $return;
	}

	/*
	 *
	 */
	function &endOfMonth() {
		$date = getdate( mktime( 0, 0, 0, $this->date['mon'] + 1, 1, $this->date['year'] ) );
		$return = new mosEventCal_DateTimeObject( mktime( 0, 0, 0, $date['mon'], $date['mday'] - 1, $date['year'] ) );
		return $return;
	}

	/**
	 * increases the actual objects variables by offset
	 * @param	string		offset		defining an offset with syntax of GNU date
	 */
	function offset( $offset ) {
		$this->update( strtotime( $offset, $this->timestamp ) );
	}

	/**
	 * @return	object					a new object with variables increased by offset
	 */
	function &offset_object( $offset ) {
		$return = new mosEventCal_DateTimeObject( strtotime( $offset, $this->timestamp ) );
		return $return;
	}

	/**
	 * compares two objects corresponding to the keys
	 * @param	object		compare		comparison
	 * @param	array		to_compare	key values to be compared
	 * @return	boolean					wether the objects are equal in the by $to_compare specified keys or not
	 */
	function compare( $compare, $to_compare ) {
		foreach ($to_compare as $key) {
			if ($compare->date[$key] != $this->date[$key]) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Sets the language / the local PHP settings by LC_TIME
	 *
	 * @param	string		locale		specifying the locale scheme php should use
	 * @return	boolean					Is set true if if was successfull and false if default joomla-language was used
	 */
	function setLanguage( $locale = null ) {
		global $mosConfig_locale;

		// I don't know where to get this on an other way...
		$valid_locales = Array( "german" => "de_DE", "american" => "en_US", "english" => "en_GB", "finnish" => "fi_FI", "others" => "..." );

		if ( isset($locale) && ((in_array( $locale, $valid_locales ) || $valid_locales[$locale] != '' )) ) {
			setlocale( LC_TIME, $locale );
			return true;
		} else {
			setlocale( LC_TIME, $mosConfig_locale );
			return false;
		}
	}

	/*
	 *
	 */
	function strftime( $format = null ) {
		if ($format === null) {
			$format = $this->date_time_format;
		}
		return strftime( $format, $this->timestamp );
	}
}
?>
