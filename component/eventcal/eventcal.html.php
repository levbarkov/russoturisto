<?php

defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $mosConfig_absolute_path;
/* include the necessary files */
//	require_once( $mainframe->getPath( 'front_html' ) );
//	require_once( $mainframe->getPath( 'class' ) );
require_once ($mosConfig_absolute_path .'/component/eventcal/eventcal.html.php');
require_once ($mosConfig_absolute_path .'/component/eventcal/eventcal.class.php');
	require_once( 'includes/jscalendar.php' );

class HTML_EventCal {

	/**
	 * Displays a redirect dialog
	 * @param Caption of the box
	 * @param Message to be shown
	 * @param Buttons to be shown
	*/
	function redirect_dialog($caption, $message, $buttons = "") {
		global $class_suffix;
		?>
		<table class="<?php echo $class_suffix ?>redirect" width="100%">
			<tr>
				<th><?php echo $caption ?></th>
			</tr>
			<tr>
				<td class="message"><?php echo $message ?></td>
			</tr>
			<tr>
				<td class ="form">
					<form>
		<?php
			if (is_array ($buttons)) {
				echo implode( "&nbsp;", $buttons);
			} else {echo $buttons;}
		?>
					</form>	
				</td>
			</tr>
		</table>
		<?php
	}


	/**
	 * Displays an editor-form for editing or adding events
	 * @param ьbergibt Datum welches eingestellt werden soll
	 * @param ьbergibt Array mit Categories
	 * @param ьbergibt existierendes Event
	*/
	function event_form($date, $catlist, $existing = NULL) {
		global $class_suffix, $mosConfig_live_site, $mosConfig_absolute_path, $mainframe, $mosConfig_locale, $week_startingday;
		$week_days	= Array("sunday","monday","tuesday","wednesday","thursday","friday","saturday","sunday");
		
		//***********************************************************************************************/
		//*   EXTERNAL COMPONENT INTERFACE																*/
		//***********************************************************************************************/
		//this is the part where an external componten may call this input-form, for storing additional params
		//additional things for displaying may be stored in an xml-file of the external component

		// load XML libraries
			require_once( $mosConfig_absolute_path . '/includes/domit/xml_domit_lite_include.php' );
			require_once( $mosConfig_absolute_path . '/component/eventcal/includes/eventcal.xml.php' );

		$params_xml = new eventCalParameters("", "" );
		$parsed_form_field_groups = $params_xml->render();
		//***********************************************************************************************/


		//add custom head tag for css inclusion
			$linktag = '<link href="' . $mosConfig_live_site . '/component/eventcal/style.css" rel="stylesheet" type="text/css"/>';
			$mainframe->addCustomHeadTag($linktag);
		//import the calendar-css module
			mosEventCal_JSCalendar::loadCalendar( "calendar-$mosConfig_locale.js" );
		//display the component-header
			HTML_EventCal::component_header( _EDITOR_CAPTION ); 
		?>
		<script language="JavaScript">
			//toggles the display-property of parts of the form (on|off)
				function toggleRecurse(id) {
					recursetr = document.getElementById(id);
					if (recursetr.style.display == 'none') {
						recursetr.style.display = 'table-row';
					} else {
						recursetr.style.display = 'none';
					}
				}

			//is run at setting display:none of the weekly repition-part of the form to unset the checkboxes
				function elementHide() {
					recursetr = document.getElementById('weekly');
					recursetr.style.display = 'none';
					for (i=1;i<=7;i++) {
						item = eval("document.eventForm.wd" + i );
						item.checked = false;
					}
					addToList();
				}

			//adds an entry to the except-dates-list from the edit-form-field
				function addToList() {
					dates = document.eventForm.dateexcept.value;
					neu = new Option (dates, dates, false, false);
					document.eventForm.daten.options[document.eventForm.daten.options.length] = neu;
					document.eventForm.dateexcept.value = '';
					document.eventForm.dateexcept.focus();
					writeHiddenEntry();
				}

			//removes an entry from the excepts-list
				function remList() {
					document.eventForm.daten.options[document.eventForm.daten.selectedIndex] = null;
					writeHiddenEntry();
				}

			//writes hidden entries for the except-dates | here the dates are stored in raw / as timestamp
				function writeHiddenEntry() {
					document.eventForm.recur_except.value = '';
					for(i=0;i<document.eventForm.daten.length;i++) {
						nextEntry = document.eventForm.daten.options[i].value;
						document.eventForm.recur_except.value = document.eventForm.recur_except.value + ',' + nextEntry;
					}
				}
		</script>
		<form action="index.php" mehtod="get" name="eventForm">
			<input type="hidden" name="option" value="<?php echo mosGetParam( $_REQUEST, 'option', "com_eventcal" ); ?>">
			<input type="hidden" name="task" value="event_submit">
			<input type="hidden" name="published" value="<?php echo (isset($existing) && @$existing->published)?$existing->published:"0" ?>">				
			<input type="hidden" name="id" value="<?php echo (isset($existing) && @$existing->id)?$existing->id:"" ?>">				
		<table class="<?php echo $class_suffix ?>form" width="650">
			<tr>
				<th colspan="3"><?php echo _EDITOR_CAPTION ?></th>
			</tr>
			<tr>
				<td><?php echo _E_TITLE ?></td>
				<td><input type="text" name="title" size="39" value="<?php echo (isset($existing) && @$existing->title)?$existing->title:""  ?>" /></td>
				<td rowspan="5" valign="top" class="excepts">
					<?php echo _FORM_EXCEPTIONS ?><br />
					<nobr>
						<input style="margin-top:6px;" type="text" size="10" name="dateexcept" id="exceptions" />
						<input type="button" value="  ... " onClick="return showCalendar('exceptions','dd.mm.yyyy')" />
						<input type="button" value=" + " onClick="addToList()" /><input type="button" value=" - " onClick="remList()" />
					</nobr>
					<br />
					<select size="10" style="width:173px;" name="daten">
					<?php 
						if (isset($existing) && @$existing->recur_except) {
							$except_dates = split("\n",$existing->recur_except);
							$exceptdates = "";
							foreach ($except_dates AS $except) {
								if ($except > 1) {
									echo '<option value="' . strftime( _NORMAL_DATE_FORMAT, $except) . '">' . strftime( _NORMAL_DATE_FORMAT,$except) . "</option>";
									$exceptdates .= "," . strftime( _NORMAL_DATE_FORMAT,$except);
								}
							}
						}
					?>
					</select> 
					<input type="hidden" name="recur_except" value="<?php echo (isset($existing) && @$existing->recur_except)?$exceptdates:"" ?>">			 
				</td>
			</tr>
			<tr>
				<td><?php echo _FORM_CATEGORY ?></td>
				<td>
					<select size="1" name="catid">
					<?php foreach ($catlist AS $category) { ?>
						<option class="catlist" style="border-color:<?php echo (isset($category->color))?$category->color:"#FFFFFF" ?>;" value="<?php echo $category->id ?>" <?php echo (isset($existing) && $existing->catid == $category->id)?'selected="selected"':"" ?>>
							<?php echo $category->title ?>
						</option>
					<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td><?php echo _E_M_DESC ?></td>
				<td><textarea name="description" cols="35" rows="5" ><?php echo (isset($existing) && @$existing->description)?$existing->description:"" ?></textarea></td>
			</tr>
			<tr>
				<td><?php echo _FORM_START_DATE?></td>
				<td>
					<input type="text" id="startdate" name="startdate" size="10" value="<?php echo (isset($existing) && @$existing->start_date)?strftime( _NORMAL_DATE_FORMAT,$existing->start_date): $date->strftime( _NORMAL_DATE_FORMAT )?>" />&nbsp;
					<input type="text" name="starttime" size="5" value="<?php echo (isset($existing) && @$existing->start_date)?strftime( _DAYVIEW_EVENT_START,$existing->start_date): $date->strftime( _DAYVIEW_EVENT_START )?>" />
					<input type="button" value=" ... " onClick="return showCalendar('startdate','<?php echo _OVERLIB_CALENDAR ?>')" />
					<div id="Calendar"></div>
				</td>
			</tr>
			<tr>
				<td><?php echo _FORM_END_DATE?></td>
				<td>
					<input type="text" id="enddate" name="enddate" size="10" value="<?php echo (isset($existing) && @$existing->end_date)?strftime( _NORMAL_DATE_FORMAT,$existing->end_date): $date->strftime( _NORMAL_DATE_FORMAT )?>" />&nbsp;
					<input type="text" name="endtime" size="5" value="<?php echo (isset($existing) && @$existing->end_date)?strftime( _DAYVIEW_EVENT_START,$existing->end_date): $date->strftime( _DAYVIEW_EVENT_START )?>" />
					<input type="button" value=" ... " onClick="return showCalendar('enddate','dd.mm.yyyy')" />
				</td>
			</tr>

			<?php
			//***********************************************************************************************/
			//*   EXTERNAL COMPONENT INTERFACE																*/
			//***********************************************************************************************/
			?>
			<?php
				$i = 0;
				foreach ($parsed_form_field_groups AS $group ) { ?>
					<tr>
						<th colspan="3"><?php echo $group->caption ?></th>
					</tr>
					<?php
					foreach ($group->fields AS $field_array ) {
						echo	'<tr>' .
									"<td>$field_array[0]</td>" .
									"<td>$field_array[1]</td>" .
									'<td align="right">' . $field_array[2] . '</td>' .
								'</tr>'
						;
					}
					$i++; 
				}
			/************************************************************************************************/ ?>

			<tr>
				<th colspan="3">
					<a href="javascript:toggleRecurse('contact');"><?php echo _CONTACT_TITLE ?></a>
				</th>
			</tr>
			<tr>
				<td colspan="3" style="padding:0px;">
					<div id="contact" style="<?php echo (isset($existing))?"display:table-row;":"display:none;" ?>">
					<table width="100%">
						<tr>
							<td><?php echo _CONTACT_CONTACT ?></td>
							<td><input type="text" name="contact" size="40" value="<?php echo (isset($existing) && @$existing->contact)?$existing->contact:"" ?>" /></td>
						</tr>
						<tr>
							<td><?php echo _CONTACT_URL ?></td>
							<td><input type="text" name="url" size="60" value="<?php echo (isset($existing) && @$existing->url)?$existing->url:"" ?>" /></td>
						</tr>
						<tr>
							<td><?php echo _CONTACT_EMAIL ?></td>
							<td><input type="text" name="email" size="60" value="<?php echo (isset($existing) && @$existing->email)?$existing->email:"" ?>" /></td>
						</tr>
					</table>
					</div>
				</td>
			</tr>
			<tr>
				<th colspan="3">
					<a class="recurse" href="javascript:toggleRecurse('recurse');"><?php echo _FORM_REPEATCAP ?></a>
				</th>
			<tr>
				<td colspan="3" style="padding:0px;">	
					<div id="recurse" style="<?php echo (isset($existing))?"display:table-row;":"display:none;" ?>">
					<table width="100%">
						<tr>
							<td colspan="2"><?php echo _EVENT?></td>
							<td><input type="radio" name="recur_type" value="none" onChange="elementHide();" <?php echo (!isset($existing->recur_type) || (isset($existing->recur_type) && $existing->recur_type == "none") )?'checked="checked"':'' ?> /><?php echo _REP_NONE ?></td>
							<td><input type="radio" name="recur_type" value="day" onChange="elementHide();" <?php echo (isset($existing) && @$existing->recur_type == "day" )?'checked="checked"':'' ?> /><?php echo _REP_DAYLY ?></td>
							<td><input type="radio" name="recur_type" value="week" onChange="toggleRecurse('weekly')" <?php echo (isset($existing) && @$existing->recur_type == "week" )?'checked="checked"':'' ?> /><?php echo _REP_WEEKLY ?></td>
							<td><input type="radio" name="recur_type" value="month" onChange="elementHide();" <?php echo (isset($existing) && @$existing->recur_type == "month" )?'checked="checked"':'' ?> /><?php echo _REP_MONTHLY ?></td>
							<td><input type="radio" name="recur_type" value="year" onChange="elementHide();" <?php echo (isset($existing) && @$existing->recur_type == "year" )?'checked="checked"':'' ?> /><?php echo _REP_YEARLY ?></td>
							<td>wiederholen</td>
						</tr>
						<tr id="weekly" style="<?php echo (isset($existing) && @$existing->recur_type == "week")?"":"display:none;" ?>">
							<td></td>
							<?php 
							$weekday = $week_startingday;
							for ( $i=0; $i<=6; $i++ ) {
								$date = new mosEventCal_DateTimeObject( time() );
								$date->offset( $week_days[$weekday] );
								$weekday++;
								if (strrpos(@$existing->recur_week,(string)($weekday-1)) !== false) {
									$checked = 'checked="checked"';
								} else $checked = "";
								echo	'<td>' .
											'<input type="checkbox" name="recur_week[]" value="' . ($weekday-1) .'"' .
											$checked . '/>' .
											strftime( "%A", $date->timestamp ) .
										'</td>'
								;
								if ($weekday > 6) {
									$weekday = 0;
								}
							} ?>
						</tr>
						<tr>
							<td colspan="2" align="right"><input type="text" size="2" name="recur_count" /></td>
							<td colspan="6"><?php echo _COUNTER_DESC ?></td>
						</tr>
					</table>
					</div>
				</td>
			</tr>
			<tr>
				<td class="resetbutton">  
					<input type="reset" value="<?php echo _RESET_BUTTON ?>" />
				</td>
				<td class="submitbutton" colspan="2">
					<input type="submit" value="<?php echo _SUBMIT_BUTTON ?>" />
				</td>
			</tr>
		</table>
		</form>	
	<?php
	}


	//Displays a single event
	/**
	 *@param event on which details should be shown
	 *@param alternative date string if not start and end-date should be used
	**/
	
	function view_event($event) {	
	global $class_suffix, $showBackButton, $mosConfig_live_site;
	global $database, $mainframe;
	
	$linktag = '<link href="' . $mosConfig_live_site . '/component/eventcal/style.css" rel="stylesheet" type="text/css"/>';
	echo $linktag;
	$category = new mosCategory( $database );
	$category->load( $event->catid );
	HTML_EventCal::component_header( $category->name );
	?>	
	<table align="center" class="<?php echo $class_suffix ?>event" width="95%">
		<tr>
			<th >
				<span class="event" style="border-color:#99ddff">
					<form >
						<?php echo $event->title ?>
						<?php if (hasAccess( "edit_event" )) { ?>
							<input type="button" onClick="window.location.href='<?php echo $mosConfig_live_site . "/index.php?c=eventcal&task=eventform&eventid=$event->id&catid=$event->catid" ?>'" value="EDIT" />
						<?php } ?>		
					</form>
				</span>
			</th>
			<th class="date">
			<?php
				if(@$event->type == "complete" && $event->start_date == $event->end_date) { 
					echo strftime( _DAYVIEW_CAPTION, $event->start_date);
				} else if(@$event->type == "complete") { 
					echo strftime( _DAYVIEW_CAPTION . " " . _DAYVIEW_EVENT_START, $event->start_date) . strftime(_DAYVIEW_EVENT_END,$event->end_date);
				} else if (@$event->type == "middle") {
					echo strftime(_NORMAL_DATE_FORMAT . " " . _DAYVIEW_EVENT_START,$event->start_date) . " - " . strftime(_NORMAL_DATE_FORMAT . " " . _DAYVIEW_EVENT_START,$event->end_date);
				} else if (@$event->type == "start") {
					echo strftime( _NORMAL_DATE_FORMAT . " " . _DAYVIEW_EVENT_START,$event->start_date) . " - " . strftime(_NORMAL_DATE_FORMAT . " " . _DAYVIEW_EVENT_START,$event->end_date);
				} else if (@$event->type == "end") {
					echo strftime( _NORMAL_DATE_FORMAT . " " . _DAYVIEW_EVENT_START,$event->start_date) . " - " . strftime(_DAY_TODAY . " " . _DAYVIEW_EVENT_START,$event->end_date);
				} else {
					echo strftime( _NORMAL_DATE_FORMAT . " " . _DAYVIEW_EVENT_START,$event->start_date) . " - " . strftime(_NORMAL_DATE_FORMAT . " " . _DAYVIEW_EVENT_START,$event->end_date);
				}
			?>
			</th>
		</tr>
		<tr>
			<td colspan="2" class="description">
			<?php echo nl2br( $event->description ) ?>
			</td>
		</tr>
		<?php if ($event->contact <> "" || $event->email <> '' || $event->url <> '')  { ?>
			<tr>
				<th class="contact" colspan="2"><?php echo _CONTACT_TITLE ?></th>
			</tr>  
			<?php if ($event->contact <> '') { ?>
			<tr>
				<td class="contactdesc"><?php echo _CONTACT_CONTACT ?></td>
				<td class="contacttext"><?php echo $event->contact ?></td>
			</tr>				
			<?php } if ($event->email <> '') { ?>
			<tr>
				<td class="contactdesc"><?php echo _CONTACT_EMAIL ?></td>
				<td class="contacttext"><?php echo mosHTML::emailCloaking( $event->email ) ?></td>
			</tr>				
			<?php } if ($event->url <> '') { ?>
			<tr>
				<td class="contactdesc"><?php echo _CONTACT_URL ?></td>
				<td class="contacttext"><a href="<?php echo $event->url ?>" target="_blank"><?php echo $event->url ?></a></td>
			</tr>				
			<?php } ?>
		<?php } ?>
		<?php if ($showBackButton) { ?>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td class="backbutton" colspan="2">
					<form><input type="button" onClick="history.back()" value="<?php echo _CMN_PREV ?>" /></form>
				</td>
			</tr>
		<?php } ?>
	</table>
	<?php  
	}


	//Displays a week-view with spans for long events
	/**
	 *@param events as an array of mosEventCal_Event
	 *@param date of the currently displayed day
	 *@param the id/ids of the category/ies currently displayed
	**/
	function view_day($events, $date, $catid = "") {
	global $class_suffix, $mosConfig_live_site, $mainframe, $Itemid;
	
	//print components css information and header inclusions
		//mosCommonHTML::loadOverlib();
		$linktag = '<link href="' . $mosConfig_live_site . '/component/eventcal/style.css" rel="stylesheet" type="text/css"/>';
		if( mosGetParam( $_REQUEST, 'pop', 0))
			echo $linktag;
		else /*$mainframe->addCustomHeadTag($linktag)*/;

	//print components header
		HTML_EventCal::component_header();

	
	$today = getdate();
	$istoday = ($today["mday"] == $date["mday"]) && ($today["mon"] == $date["mon"]) && ($today["year"] == $date["year"])?true:false;
	$menuitem = ($Itemid <> '')?"&Itemid=$Itemid":"";		
	?>
	
	<table class="<?php echo $class_suffix ?>day">
		<tr>
			<th class="<?php echo $class_suffix ?>captionlast"><a href="<?php echo sefRelToAbs('index.php?c=eventcal&task=day&date=' . ($date[0] - 86400) . $catid . $menuitem ) ?>"><?php echo ($istoday)?_DAY_YESTERDAY:_DAY_SHOW_LAST ?></a></th>
			<th class="<?php echo $class_suffix ?>caption"><?php echo ($istoday)?_DAY_TODAY:strftime(_DAYVIEW_CAPTION,$date[0]);  ?></th>
			<th class="<?php echo $class_suffix ?>captionnext"><a href="<?php echo sefRelToAbs('index.php?c=eventcal&task=day&date=' . ($date[0] + 86400) . $catid . $menuitem ) ?>"><?php echo ($istoday)?_DAY_TOMORROW:_DAY_SHOW_NEXT ?></a></th>
		</tr>
	<?php foreach ($events AS $event) { ?>
		<tr>
			<td class="time"><?php echo strftime(_DAYVIEW_EVENT_START,$event->start_date) . strftime(_DAYVIEW_EVENT_END,$event->end_date); ?></td>
			<td colspan="2" class="event">
				<?php echo HTML_EventCal::EventCalOverlib( $event, $catid, array('colored', 'shortdesc', 'colorspan') );?>
			</td>
		</tr>				
	<?php } ?>
	</table>
	<?php
	}


	//Displays a week-view with spans for long events
	/**
	 *@param events as an array of mosEventCal_Event
	 *@param the timetable from the config-file which is displayed on the left side
	 *@param the starting date of the week
	 *@param the id/ids of the category/ies currently displayed
	**/
	function view_week($events, $timetable, $startdate, $catid = "") {
	global $class_suffix, $mosConfig_live_site, $mainframe, $Itemid;

	//print components css information and header inclusions	
		mosCommonHTML::loadOverlib();
		$linktag = '<link href="' . $mosConfig_live_site . '/component/eventcal/style.css" rel="stylesheet" type="text/css"/>';
		if( mosGetParam( $_REQUEST, 'pop', 0))
			echo $linktag;
		else $mainframe->addCustomHeadTag($linktag);
	//print components header
		HTML_EventCal::component_header();
	
	$menuitem = ($Itemid <> '')?"&Itemid=$Itemid":"";
	?>
	<table class="<?php echo $class_suffix ?>week">
		<tr>
			<th></th>
			<th class="<?php echo $class_suffix ?>captionlast" colspan="2"><a href="<?php echo sefRelToAbs('index.php?c=eventcal&task=week&date=' . ($startdate->timestamp - 604800) . $catid . $menuitem ) ?>"><?php echo _LAST_WEEK ?></a></th>		
			<th class="<?php echo $class_suffix ?>caption" colspan="3"><?php echo _WEEK_FROM . " " . strftime( _NORMAL_DATE_FORMAT,$startdate->timestamp) . " " . _WEEK_TILL . " " . strftime( _NORMAL_DATE_FORMAT,$startdate->timestamp + 604200) ?></th>
			<th class="<?php echo $class_suffix ?>captionnext" colspan="2"><a href="<?php echo sefRelToAbs('index.php?c=eventcal&task=week&date=' . ($startdate->timestamp + 604800) . $catid . $menuitem ) ?>"><?php echo _NEXT_WEEK ?></a></th>
		</tr>
		<tr>
			<th class="timetable"></th>
			<?php 
				$weekDay_date = clone( $startdate );
				for($i=0;$i<7;$i++) {
					echo '<th class="weekday">' . strftime("%A",$weekDay_date->timestamp) . "</th>";
					$weekDay_date->offset( "+1 day" );				
				}
			?>
		</tr>  
		<?php foreach($timetable AS $timespan) { ?>
		<tr>
			<td class="timetable"><?php echo $timespan[0] ?></td>
			<?php
				for($i=0;$i<7;$i++) {
					//evaluate the actual displayed time from $timespan
						$split_time = split(":",$timespan[0]);
						$starttime = mktime($split_time[0]+1,$split_time[1],0,1,1,1970);
						$split_time = split(":",$timespan[1]);
						$endtime = mktime($split_time[0]+1,$split_time[1],0,1,1,1970);
					//make dates for getDateEvents-call	
						$thisdate_start = new mosEventCal_DateTimeObject($startdate->timestamp + $i*86400 + $starttime);
						$thisdate_end = new mosEventCal_DateTimeObject($startdate->timestamp + $i*86400 + $endtime);
						$thisTimeEvents = eventCal_Recursion::getDateEvents($events,$thisdate_start,$thisdate_end);
					echo '<td class="weekday">';
					foreach($thisTimeEvents AS $dayEvent) { ?>
						<div class="event">
							<?php echo ($dayEvent->type == "middle")?"": HTML_EventCal::EventCalOverlib( $dayEvent, $catid, array('colored', 'rangeicon', 'startdate', 'starttime', 'enddate', 'endtime') ) ?> 
						</div>
					<?php }
				echo "</td>";
				}
			?>
		</tr>
		<?php } ?>
	</table>	
	<?php
	}


	//Displays a whole month
	/**
		* @param events as an array of mosEventCal_Event
		* @param a matrix of date-types for the calender; NULL means not day of this month
		* @param week-numbers dispayed in front of the rows
		* @param if no date is passed the actual month is taken
		* @param selected categories
	**/
	function view_month($events, $calmatrix, $weeknrs, $date = NULL, $catid="") {	
		global $class_suffix, $show_weeknumber, $week_number_links, $mainframe, $mosConfig_live_site, $Itemid;

	//print components css information and header inclusions
//		mosCommonHTML::loadOverlib();
//	ggtr ($mainframe);
		$linktag = '<link href="' . $mosConfig_live_site . '/component/eventcal/style.css" rel="stylesheet" type="text/css"/>';
		if( mosGetParam( $_REQUEST, 'pop', 0))
			echo $linktag;
//		else $mainframe->addCustomHeadTag($linktag);
	
		//print component header
			HTML_EventCal::component_header();

	$menuitem = (@$_REQUEST["Itemid"])?"&Itemid=$Itemid":"";
	$today = new mosEventCal_DateTimeObject( time() );
	if (!isset($date)) {
		$date = $today;
	}
	
	$catid = ($catid !== false)?"&catid=" . $catid:"";
	
		$thisMonth = $date->startOfMonth();
		$lastMonth = $thisMonth->offset_object( "1 month ago" );
		$nextMonth = $thisMonth->offset_object( "+ 1 month" );
	?>
	<table class="<?php echo $class_suffix ?>month">
		<tr>
		  <?php if ($show_weeknumber) { ?>
			<td rowspan="2"></td>
		  <?php } ?>
			<td colspan="2" class="lastm"><a href="<?php echo sefRelToAbs("index.php?c=eventcal&task=month&date=$lastMonth->timestamp" . $catid . $menuitem ) ?>"><?php echo $lastMonth->strftime("%B") ?></a></td>
			<td colspan="3" class="currentm"><?php echo $thisMonth->strftime("%B") . " " . $thisMonth->strftime("%Y") ?></td>
			<td colspan="2" class="nextm"><a href="<?php echo sefRelToAbs("index.php?c=eventcal&task=month&date=$nextMonth->timestamp" .$catid . $menuitem ) ?>"><?php echo $nextMonth->strftime("%B") ?></a></td>
		</tr>
		<tr>
		<?php /* show weekday-names */ ?>
		<?php for($i=0;$i<7;$i++) {?>  
			<td class="weekd"><?php echo $calmatrix[1][$i]->strftime("%A") ?></td>
			<?php }?>  
		</tr>
		<?php for ($wcount=1;$wcount<=count($weeknrs);$wcount++) { ?>
		<tr>
			<?php if ($show_weeknumber) { ?>
			<td class="weeknr">
				<?php 
					if ($week_number_links) {
						$timestamp_2 = ($calmatrix[$wcount-1][0] <> NULL)?$calmatrix[$wcount-1][0]->timestamp:$calmatrix[$wcount-1][6]->timestamp-604800; ?>
						<a href="<?php echo sefRelToAbs( $mosConfig_live_site . "/index.php?c=eventcal&task=week&date=" . $timestamp_2 . $menuitem ) ?>" class="weeknr">
							<?php echo $weeknrs[$wcount-1] ?>
						</a>	
				<?php 
					} else {
						echo $weeknrs[$wcount-1];
					} ?>
			</td>
			<?php }?>
			<?php 
				for ($weekday=1;$weekday<=7;$weekday++) {
					if(isset($calmatrix[$wcount-1][$weekday-1])) {
						$thisDayEvents = eventCal_Recursion::getDateEvents($events,$calmatrix[$wcount-1][$weekday-1]); ?>
						<td class="<?php echo ($calmatrix[$wcount-1][$weekday-1]->compare($today, array("mday","mon","year")))?"today":"currentd" ?>" VALIGN="top">
							<span class="<?php echo ($calmatrix[$wcount-1][$weekday-1]->date["wday"] == "0")?"sunday":"mday"; ?>">
								<?php echo $calmatrix[$wcount-1][$weekday-1]->date["mday"]; ?>
							</span>
							<?php foreach($thisDayEvents AS $dayEvent) { ?>
								<div class="events" style="border-color:<?php echo $dayEvent->getColor() ?>">
									<?php echo HTML_EventCal::EventCalOverlib( $dayEvent, $catid, array('colored', 'startdate', 'starttime', 'enddate', 'endtime') ) ?>
								</div>
							<?php } ?>
						</td>
				<?php } else {
							if($wcount-1 == 0) {echo '<td class="lastd">' . $lastMonth->strftime("%B") . "</td>"; }
							else {echo '<td class="nextd">' . $nextMonth->strftime("%B") . "</td>"; }
					  }
				} ?>
		</tr>
		<?php }?>
	</table> 
	<?php
	}	


	//Displays a list for one category
	/**
	 *@param category to be displayed
	 *@param an object list of events
	*/
	function showCategoryView( $category, $events ) {
	global $mosConfig_live_site, $mainframe, $class_suffix;
	
	//print components css information and header inclusions
		$linktag = '<link href="' . $mosConfig_live_site . '/component/eventcal/style.css" rel="stylesheet" type="text/css"/>';
		if( mosGetParam( $_REQUEST, 'pop', 0))
			echo $linktag;
		else $mainframe->addCustomHeadTag($linktag);
		?>
		<table class="<?php echo $class_suffix ?>catview">
			<tr>
				<th colspan="3" style="background-color:<?php echo getParam("color", $category->params) ?>;">
					<?php echo $category->title ?>
				</th>
			</tr>
			<tr>
				<td class="caption">Veranstaltung</td>
				<td width="90" class="caption">Uhrzeit</td>
				<td width="150" class="caption">Datum</td>
			</tr>
			<?php foreach ( $events AS $event ) {?>
			<tr>
				<td><?php echo $event->title ?></td>
				<td>
				<?php 
					if (strftime( _NORMAL_TIME_FORMAT, $event->start_date) <> strftime(_NORMAL_TIME_FORMAT, $event->end_date)) {
						echo strftime(_DAYVIEW_EVENT_START,$event->start_date) . strftime(_DAYVIEW_EVENT_END,$event->end_date); 
					}
				?>
				</td>
				<td>
				<?php 
					if ($event->recur_type == 'none') {
						if (strftime("%x",$event->start_date) == strftime("%x",$event->end_date)) { 
							echo strftime(_NORMAL_DATE_FORMAT,$event->start_date);
						} else {
							echo strftime(_NORMAL_DATE_FORMAT,$event->start_date) . " - " . strftime(_NORMAL_DATE_FORMAT,$event->end_date);
						}
					} else if ($event->recur_type == 'day') {
						echo strftime(_NORMAL_DATE_FORMAT,$event->start_date);
					} else if($event->recur_type == 'week') {
						echo strftime(_DAYVIEW_CAPTION,$event->start_date);
					} else if ($event->recur_type == 'month' || $event->recur_type == 'year') { 
						echo strftime(_NORMAL_DATE_FORMAT,$event->start_date);
					} ?>
				</td>	
			</tr>
			<?php } ?>	
		</table>
	<?php	
	}

	//Shows the component header
	function component_header( $title = false ) {
	global $class_suffix, $Itemid, $database, $mainframe, $mosConfig_live_site, $show_selectlist;
	
	// get params from menu-entry
		$menu =& new mosMenu( $database );
		$menu->load( $Itemid );
		$params =& new mosParameters( $menu->params );
		if ($title) {
			$title = _EVENT_CAL . " - " . $title;
		} else if($params->get( 'title' )) {
			$title = $params->get( 'title' );
		} elseif ($menu->name) {
			$title = _EVENT_CAL . " - " . $menu->name;
		} else {
			$title = _EVENT_CAL;
		}
		unset( $menu );
		//$mainframe->SetPageTitle( $title );
	?>
	<table cellspacing="0" class="contentpaneopen">
		<tr>
			<!--<td class="contentheading" width="100%">
				<?php echo $title ?>
			</td>-->
			<td align="right" class="buttonheading">
			<?php 
				$params->def( 'popup', intval( mosGetParam( $_REQUEST, 'pop', 0 ) ) );
				$params->def( 'icons', $mainframe->getCfg( 'icons' ) );
				$params->def( 'print', !$mainframe->getCfg( 'hidePrint' ) );
				$task = "&task=" . mosGetParam( $_REQUEST, "task", "month" );
				$date = ( mosGetParam( $_REQUEST, "date", "" ) )?("&date=" . mosGetParam( $_REQUEST, "date", "" )):"";
				$eventid = ( mosGetParam( $_REQUEST, "eventid", "" ) )?("&eventid=" . mosGetParam( $_REQUEST, "eventid", "" )):"";
				$endtime = ( mosGetParam( $_REQUEST, "endtime", "" ) )?("&endtime=" . mosGetParam( $_REQUEST, "endtime", "" )):"";
				$catid = ( mosGetParam( $_REQUEST, "catid", "" ) )?("&catid=" . mosGetParam( $_REQUEST, "catid", "" )):"";
				$menuitem = ($Itemid <> '')?"&Itemid=$Itemid":"";
				$print_link = $mosConfig_live_site . "/index2.php?c=eventcal&pop=1" . $task . $date . $endtime . $eventid . $catid . $menuitem;
				mosHTML::PrintIcon( $class_suffix, $params, false, $print_link );
			?>
			</td>
		</tr>
	</table>
	<?php 
		$popup = mosGetParam( $_REQUEST, "pop", false);
		if ($show_selectlist && !$popup) HTML_eventcal::show_selectlist();	
	}	

	/*
	*Shows the selectlist to choose a date to be displayed
	*/
	function show_selectlist() {
	global $class_suffix, $task, $mosConfig_live_site;
	global $database, $Itemid;
	
	$date = mosGetParam( $_REQUEST, "date", false);
	if ($date === false) {
		$date = getDate();
		$date = $date[0];
	}
	
	$today = getdate();
	$firstdayofyear = mktime(0,0,0,1,1,strftime("%Y",$date));
	$menuitem = ($Itemid <> '')?"&Itemid=$Itemid":"";
	
	// Parameters from Menuitem
		$menu =& new mosMenu( $database );
		$menu->load( $Itemid );
		$params =& new mosParameters( $menu->params );
		if (isset($_REQUEST["catid"])) {
			$catidRaw = mosGetParam( $_REQUEST, 'catid' , "");
			$catid = "&catid=" . $catidRaw;
		}
		else {
			$catidRaw = $params->get( 'catid', false );
			if ($catidRaw) $catid = "&catid=" . $catidRaw;
			else $catid = "";
		}
	?>
	<script language="JavaScript">
		function submitform() {
			document.selectlistForm.submit();
		}
	</script>
	<div class="selectlist" width="100%">
		<form name="selectlistForm" action="index.php" method="get">
			<input type="hidden" name="option" value="com_eventcal" />
			<input type="hidden" name="Itemid" value="<?php echo $Itemid ?>" />
			<input type="hidden" name="task" value="<?php echo $task ?>" />
			<input type="hidden" name="catid" value="<?php echo $catidRaw ?>" />
			<table border="0">
				<tr>
					<td>
					<?php if (!($task == "month")) { ?>
						<a class="<?php echo $class_suffix ?>selectlist_link" href="<?php echo sefRelToAbs("index.php?c=eventcal&task=month&date=$date" . $catid . $menuitem ) ?>" title="<?php echo _MONTH_SHOW_VIEW ?>" >
							<img src="<?php echo $mosConfig_live_site ?>/component/eventcal/images/view_month.gif" width="32" height="32" alt="<?php echo _MONTH_SHOW_VIEW ?>" />
						</a>
					<?php } else { ?>					
						<img src="<?php echo $mosConfig_live_site ?>/component/eventcal/images/view_month_f2.gif" width="32" height="32" alt="<?php echo _MONTH_SHOW_VIEW ?>" />
					<?php } ?>			
					</td>
					<td>
					<?php if (!($task == "week")) { ?>
						<a class="<?php echo $class_suffix ?>selectlist_link" href="<?php echo sefRelToAbs("index.php?c=eventcal&task=week&date=$date" . $catid . $menuitem ) ?>" title="<?php echo _WEEK_SHOW_VIEW ?>" >
							<img src="<?php echo $mosConfig_live_site ?>/component/eventcal/images/view_week.gif" width="32" height="32" alt="<?php echo _WEEK_SHOW_VIEW ?>" />
						</a>	
					<?php } else { ?>					
						<img src="<?php echo $mosConfig_live_site ?>/component/eventcal/images/view_week_f2.gif" width="32" height="32" alt="<?php echo _WEEK_SHOW_VIEW ?>" />			
					<?php } ?>			
					</td>
					<td>
					<?php if (!($task == "day")) { ?>
						<a class="<?php echo $class_suffix ?>selectlist_link" href="<?php echo sefRelToAbs("index.php?c=eventcal&task=day&date=$date" . $catid . $menuitem ) ?>" title="<?php echo _DAY_SHOW_VIEW ?>" >
							<img src="<?php echo $mosConfig_live_site ?>/component/eventcal/images/view_day.gif" width="32" height="32" alt="<?php echo _DAY_SHOW_VIEW ?>" />
						</a>
					<?php } else { ?>					
						<img src="<?php echo $mosConfig_live_site ?>/component/eventcal/images/view_day_f2.gif" width="32" height="32" alt="<?php echo _DAY_SHOW_VIEW ?>" />
					<?php } ?>			
					</td>
					<td>&nbsp;&nbsp;</td>
					<td>
					<?php if ( $task == "month" ) { ?>
						<select name="date" class="selectlist_selectbox" onChange="submitform();">
						<?php for ($i = strftime("%m",$date)-6; $i <= strftime("%m",$date)+5; $i++) { ?>
							<option value="<?php echo mktime(0,0,0,$i,1,strftime("%Y",$date)) ?>" <?php echo (strftime("%m",$date) == $i )?'selected="selected" class="selected" ':'class="listitem"' ?>>
								<?php echo strftime("%B %Y",mktime(0,0,0,$i,1,strftime("%Y",$date)))?>
							</option>
						<?php } ?>
						</select>
					<?php } ?>
					<?php if ( $task == "week" ) { ?>
						<select name="date" class="selectlist_selectbox" onChange="submitform();">
							<?php $current_month = 0;
							for ($i = strftime("%V", date)-25; $i <= strftime("%V",$date)+25; $i++) { 					
								if($current_month <> strftime("%m", ($i)*604800+$firstdayofyear)) { ?>
									<optgroup label="<?php echo strftime("%B %Y", ($i+1)*604800+$firstdayofyear) ?>"/>
								<?php } ?>					
								<option value="<?php echo $i*604800+$firstdayofyear ?>" <?php echo (strftime("%U", $date) == $i )?'selected="selected" class="selected" ':'class="listitem"' ?>>
									<?php echo strftime("%V", $i*604800+$firstdayofyear) . _WEEK_WEEK ?>
								</option>
								<?php if($current_month <> strftime("%m", ($i+1)*604800+$firstdayofyear)) { ?>
									</optgroup>							
								<?php }
								$current_month = strftime("%m", ($i)*604800+$firstdayofyear);										
							} ?>	
						</select>
					<?php } ?>			
					</td>
					<td>
						<noscript>
							<input type="Submit" class="button" value="GO" />
						</noscript>
					</td>
				</tr>
			</table>
		</form>
	</div>
	<?php
	}
	
	
	//Shows the Category list for the table-footer
	/**
	 *@param Count of Columns
	 *@param if the descriptive header should be displayed
	 *@param array of category-objects
	 *@param if active-displayed categories should be marked
	**/
	function category_list($columns, $showheader, $categories, $markactive ) {
	global $class_suffix, $task, $Itemid, $database;	
	
	//exit if there are no categories
	  if ( !is_array($categories) ) exit;
	
		$all_ids = array();
		foreach ($categories AS $cat_id)
			$all_ids[] = $cat_id->id;
		$all_ids = implode( "+", $all_ids );

	//add the select / deselect all link
		$all_link = new mosCategory( $database );
		$all_link->color = '#EFEFEF';
		$all_link->title = _SELECT_ALL;
		$all_link->id = 'all';
		array_unshift($categories, $all_link);
		if (count($categories) < $columns) {
			$columns = count($categories);
		}
	?>
	<script language="JavaScript">
		function toggleCatView(category) {
			var value = document.catForm.catid.value;
			var catids = value.split("+");
			var i = 0;
			var suche = true;
			while(i<catids.length) {
				if (catids[i] == category) {
					catids.splice(i,1);
					suche = false;
					break;
				} else {i++;}
			}
			if (suche) catids.push(category);
			document.catForm.catid.value = catids.join("+");
			if (category == "all") document.catForm.catid.value = "<?php echo $all_ids?>";
			document.catForm.submit();
		}
	</script>
	<form name="catForm" action="index.php" method="get">
		<input type="hidden" name="option" value="com_eventcal" />
		<input type="hidden" name="Itemid" value="<?php echo $Itemid ?>" />
		<input type="hidden" name="task" value="<?php echo $task ?>" />
		<?php 
		$date = trim( mosGetParam( $_REQUEST, 'date' , false) ) ;
		if ($date) {
			echo '<input type="hidden" name="date" value="' . $date . '">' . "\n";
		}
		?>
		<input type="hidden" name="catid" value="<?php echo (is_array($markactive))?implode("+", $markactive):"" ?>" />
	</form>
	<table width="100%" class="<?php echo $class_suffix ?>categories">
		<?php if ($showheader) { ?>
			<tr>
				<th colspan="<?php echo $columns*2 ?>">
					<?php echo _CATLIST_HEADER ?>
				</th>
			</tr>
		<?php	} ?>
		<?php 
		$maxcount = ceil(count($categories)/$columns);
		$count = 1;
		while ($count <= $maxcount) {
			echo "<tr>";
			for($cols=0;$cols < $columns;$cols++) {
				if (count($categories) > ($count-1)*$columns+$cols ) {
					echo '<td width="15" height="15" style="background-color:' . $categories[($count-1)*$columns+$cols]->color . '">&nbsp;</td>';
					$active = "";
					if ($markactive) {
						if (in_array($categories[($count-1)*$columns+$cols]->id,$markactive)) {
							$active = "active";
						}
					}
					echo '<td class="' . $class_suffix .'category' . $active .'" width="' . (100/$columns-2) . '%">';
					$linkstart = '<a class="categorylink' . $active . '" href="javascript:toggleCatView(\'' . $categories[($count-1)*$columns+$cols]->id . '\');">';
					echo $linkstart . $categories[($count-1)*$columns+$cols]->title . '</a>';
					echo '</td>';
				} else {
					echo '<td></td><td></td>';
				}	
			}
			echo "</tr>";
			$count++;
		} ?>
	</table>
	<?php  
	}	


	//Generates the links to the events with an overlib
	/**
	 *@param the event the link should point on
	 *@param these things which schould be displayed:
	 *colored	  this means, that rangeicon, colorspan an caption of the overlib will get the category-color
	 *rangeicon	if there is an icon with an arrow up or down or the complete image in front
	 *startdate	these mustn't be explained I hope
	 *starttime	
	 *enddate	  
	 *endtime	  
	 *shortdesc	the first 128 characters of the description text
	 *description  the complete description text
	 *colorspan	if there is a span with 3nbsps displaying the background color of the corresponding category
	**/	
	
	function EventCalOverlib( $event, $catid, $display = array('colored', 'rangeicon', 'startdate', 'starttime', 'enddate', 'endtime', 'shortdesc' ) ) {
	global $mosConfig_live_site, $Itemid;

	$colorstyle = (in_array( 'colored', $display ))?'style="background-color:' . $event->getColor() . ';"':"";
	$color = (in_array( 'colored', $display ))?$event->getColor():"";  
	switch ($event->type) {
		case "start":
			$icon = '<img align="left" src="' . $mosConfig_live_site . '/component/eventcal/images/start.png" width="5" height="10" ' . $colorstyle . '>';
		break;
		case "complete":
			$icon = '<img align="left" src="' . $mosConfig_live_site . '/component/eventcal/images/complete.png" width="5" height="10" ' . $colorstyle . '>';
		break;
		case "end":
			$icon = '<img align="left" src="' . $mosConfig_live_site . '/component/eventcal/images/end.png" width="5" height="10" ' . $colorstyle . '>';
		break;
		case "middle" :
		default :
			$icon = "";
		break;
	} 
	$linktext  = "index.php?c=eventcal&task=event" .
				 "&date=" . $event->start_date .
				 "&eventid=" . $event->id .
				 "&Itemid=" . $Itemid .
				 "&catid=" . $catid;
	
	//generate Overlib Text
		$text  = "<b>$event->title</b><br />";
		$text .= (in_array( 'description', $display ))?nl2br($event->description). "<br />":"";
		$tobecontinued = (substr($event->description, 128, 150))?"...":"";
		$text .= (in_array( 'shortdesc', $display ))?nl2br( substr($event->description, 0, 128)) ."$tobecontinued<br />":"";
	//for decision of identical values of date and / or time I need to split up them
		$startdate = new mosEventCal_DateTimeObject( $event->start_date );
		$enddate =  new mosEventCal_DateTimeObject( $event->end_date );
		$starttime = clone ( $startdate );
		$starttime->clearDate();
	//so lets decide wether we will display the things or not
		if ( $startdate->compare( $enddate, array("mday", "mon", "year")) ) {
			$text .= (in_array( 'startdate', $display ))? strftime( _OVERLIB_SINGLEDATE, $event->start_date ):"";
			if ( $startdate->compare( $enddate, array("minutes", "hours")) && $starttime->timestamp == 0 ) {
			} elseif ( $startdate->compare( $enddate, array("minutes", "hours")) ) {
				$text .= (in_array( 'starttime', $display ))? strftime( _OVERLIB_SINGLETIME_FROM, $event->start_date ):"";
				$text .= (in_array( 'startdate', $display ) || in_array( 'starttime', $display ))?"<br />":"";
			} else {
				$text .= (in_array( 'startdate', $display ) && (in_array( 'starttime', $display ) || in_array( 'endtime', $display )))?"<br />":"";
				$text .= (in_array( 'starttime', $display ))? strftime( _OVERLIB_TIME_FROM, $event->start_date ):"";
				$text .= (in_array( 'endtime', $display ))? strftime( _OVERLIB_TIME_TO, $event->end_date ):"";
				$text .= (in_array( 'startdate', $display ) || in_array( 'starttime', $display ) || in_array( 'enddate', $display ) || in_array( 'endtime', $display ))?"<br />":"";
			}
		} else {
			$text .= (in_array( 'startdate', $display ))? strftime( _OVERLIB_STARTDATE, $event->start_date ):"";			  
			if ( !$startdate->compare( $enddate, array( "minutes", "hours" )) ) {
				$text .= (in_array( 'starttime', $display ))? strftime( _OVERLIB_SINGLETIME, $event->start_date ):"";
			}
			$text .= (in_array( 'startdate', $display ) || in_array( 'starttime', $display ))?"<br />":"";
			$text .= (in_array( 'enddate', $display ))? strftime( _OVERLIB_ENDDATE, $event->end_date ):"";			  
			if ( !$startdate->compare( $enddate, array( "minutes", "hours" )) ) {
				$text .= (in_array( 'endtime', $display ))? strftime( _OVERLIB_SINGLETIME, $event->end_date ):"";
			}
			$text .= (in_array( 'enddate', $display ) || in_array( 'endtime', $display ))?"<br />":"";					
			if ( $startdate->compare( $enddate, array( "minutes", "hours" )) ) {
				$text .= (in_array( 'starttime', $display ))? strftime( _OVERLIB_STARTTIME, $event->start_date ):"";
			}
		}
		$text = addslashes( $text );
		$overlib_link = '<a href="' . sefRelToAbs( $linktext ) . '" onMouseOver="return overlib(\'' . $text . '\',CAPTION, \'' . $event->category  .'\', BELOW, RIGHT, BGCOLOR, \'' . $color . '\', OFFSETX, \'10\');" onMouseOut="return nd();">';
		$overlib_link .= (in_array( 'colorspan', $display ))?"<span $colorstyle >&nbsp;&nbsp;&nbsp;</span> ":"";
		$overlib_link .= (in_array( 'rangeicon', $display ))?$icon:"";
		$overlib_link .= $event->title;
		$overlib_link .= "</a>";

	return $overlib_link;
	}
	
} //end of sourcecode
?>
