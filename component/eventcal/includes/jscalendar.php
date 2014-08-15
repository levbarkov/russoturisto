<?php

defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

class mosEventCal_JSCalendar {
	/**
	 * Loads all necessary files for JS Calendar
	 *
	 * @param	string		language-file		deprecated
	 * @return	null
	 */
	function loadCalendar( $language_file = 'this parameter is only here for backwards compatibility' ) {
		global  $mosConfig_live_site, $mosConfig_absolute_path, $mosConfig_lang;
		if (file_exists( $mosConfig_absolute_path . '/components/com_eventcal/includes/js/calendar-' . $mosConfig_lang . '.js' )) {
			$language_file = 'calendar-' . $mosConfig_lang . '.js';
		} else {
			$language_file = 'calendar-english.js';
		}
		?>
		<link rel="stylesheet" type="text/css" media="all" href="<?php echo $mosConfig_live_site; ?>/includes/js/calendar/calendar-mos.css" title="green" />
		<script type="text/javascript" src="<?php echo $mosConfig_live_site; ?>/includes/js/joomla.javascript.js"></script>
		<script type="text/javascript" src="<?php echo $mosConfig_live_site; ?>/includes/js/calendar/calendar.js"></script>
		<script type="text/javascript" src="<?php echo $mosConfig_live_site; ?>/components/com_eventcal/includes/js/<?php echo $language_file; ?>"></script>
		<script type="text/javascript">
			Calendar._TT["DEF_DATE_FORMAT"] = '<?php echo _OVERLIB_CALENDAR ?>';
		</script>
		<?php
	}
}
?>