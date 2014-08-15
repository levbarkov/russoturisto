<?php

defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );
global $mosConfig_absolute_path;
require_once( $mosConfig_absolute_path . '/iadmin/component/categories/admin.categories.html.php' );
require_once( $mosConfig_absolute_path . '/component/eventcal/includes/jscalendar.php' );


class HTML_admin_eventcal {

	/**
	 * prints the event form to add or edit events
	 * @param	mosEventCal_Event			evnet			the object to be edited or null if new one should be created
	*/
	function mkEventForm($event = null) {
		global $mosConfig_live_site, $mosConfig_locale;
		mosEventCal_JSCalendar::loadCalendar( "calendar-" . $mosConfig_locale . ".js" );
		//mosCommonHTML::loadOverlib();
		?>
		<script language="javascript" type="text/javascript">
			function submitbutton(pressbutton, section) {
				var form = document.adminForm;
				if (pressbutton == 'cancel') {
					submitform( pressbutton );
					return;
				}

				if ( form.title.value == "" ) {
					alert("There must be a title for this event!");
				} else if ( form.catid.value == "0" ) {
					alert("You must select a category for this event!");
				} else if ( form.startdate.value == "" ) {
					alert("There must be a starting date for this event!");
				} else if ( form.starttime.value == "" ) {
					alert("There must be a starting time for this event!");
				} else if ( form.enddate.value == "" ) {
					alert("There must be a finishing date for this event!");
				} else if ( form.endtime.value == "" ) {
					alert("There must be a finishing time for this event!");
				} else {
					<?php getEditorContents( 'editor2', 'description' ) ; ?>
					submitform(pressbutton);
				}
			}

			//JavaScript functions for recur-exception-adding/-removing:
			//adds an entry to the except-dates-list from the edit-form-field
			function addToList() {
				var dates = document.adminForm.dateexcept.value;
				var neu = new Option (dates, dates, false, false);
				document.adminForm.daten.options[document.adminForm.daten.options.length] = neu;
				document.adminForm.dateexcept.value = '';
				document.adminForm.dateexcept.focus();
				writeHiddenEntry();
			}

			//removes an entry from the excepts-list
			function remList() {
				document.adminForm.daten.options[document.adminForm.daten.selectedIndex] = null;
				writeHiddenEntry();
			}

			//writes hidden entries for the except-dates | here the dates are stored in raw / as timestamp
			function writeHiddenEntry() {
				document.adminForm.recur_except.value = '';
				var i = 0;
				for(i=0;i<document.adminForm.daten.length;i++) {
					var nextEntry = document.adminForm.daten.options[i].value;
					document.adminForm.recur_except.value = document.adminForm.recur_except.value + ',' + nextEntry;
				}
			}
		</script>

		<form action="index2.php" method="post" name="adminForm">
		<input type="hidden" name="ca" value="eventcal" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="id" value="<?php echo (isset($event))?$event->id:""; ?>" />

			<div id="Calendar" class="overdiv"></div>

			<table width="100%">
				<tr>
					<td colspan="2">
						<table class="adminheader">
							<tr>
								<th>EventCal :: edit/add Event</th>
							</tr>
						</table>
					</td>
				</tr>
				<tr valign="top">
					<td width="60%">
						<table class="adminform">
							<tr>
								<th colspan="3" class="title">Details</th>
							</tr>
							<tr>
								<td>title:</td>
								<td><input type="text" name="title" size="60" class="inputbox" value="<?php echo (isset($event))?$event->title:"" ?>" /></td>
								<td></td>
							</tr>
							<tr>
								<td>category:</td>
								<td>
									<?php
										$active = (isset($event))?$event->catid:NULL;
										echo mosAdminMenus::ComponentCategory('catid','com_eventcal',$active);
									?>
								</td>
								<td></td>
							</tr>
							<tr>
								<td>starting-date / beginn publishing:</td>
								<td>
									<input type="text" name="startdate" id="startdate" size="10" class="inputbox" value="<?php echo (isset($event))?(strftime( _NORMAL_DATE_FORMAT, $event->start_date)):"" ?>" />
									&nbsp;<input type="button" value=" ... " class="button" onClick="return showCalendar('startdate','<?php echo _OVERLIB_CALENDAR ?>')" />
									&nbsp;&nbsp;<input type="text" name="starttime" size="5" class="inputbox" value="<?php echo (isset($event))?(strftime( _NORMAL_TIME_FORMAT,$event->start_date)):"" ?>" />
								</td>
								<td>
									<?php echo mosToolTip("Depending on your choose of repeating options this declaims a date range for publishing or the dates the event takes place.<br />Times are taken for every day except the event spans more than one day.","startdate"); ?>
								</td>
							</tr>
							<tr>
								<td>
									ending date / end publishing:<br />
								</td>
								<td>
									<input type="text" name="enddate" id="enddate" size="10" class="inputbox" value="<?php echo (isset($event))?(strftime( _NORMAL_DATE_FORMAT, $event->end_date)):"" ?>" />
									&nbsp;<input type="button" value=" ... " class="button" onClick="return showCalendar('enddate','<?php echo _OVERLIB_CALENDAR ?>')" />
									&nbsp;&nbsp;<input type="text" name="endtime" size="5" class="inputbox" value="<?php echo (isset($event))?(strftime( _NORMAL_TIME_FORMAT, $event->end_date)):"" ?>" />
								</td>
								<td>
									<?php echo mosToolTip("Depending on your choose of repeating options this declaims a date range for publishing or the dates the event takes place.<br />Times are taken for every day except the event spans more than one day.","enddate"); ?>
								</td>
							</tr>
							<tr>
								<td>Published:</td>
								<td colspan="2">
									<?php echo mosHTML::yesnoRadioList( 'published', '', intval( isset($event) && $event->published ) ); ?>
								</td>
								
							</tr>
							<tr>
								<td valign="top">Description:</td>
								<td colspan="2">
									<!--<textarea name="description" rows="15" cols="78"><?php echo (isset($event))?$event->description:"" ?></textarea>-->
									<? editorArea( 'editor2',  $event->description , 'description', '100%;', '400', '75', '30' ) ; ?>
								</td>
							</tr>
						</table>
					</td>
					<td width="40%">
						<table class="adminform">
							<tr>
								<th colspan="3" class="title">Repeating options</th>
							</tr>
							<tr valign="top">
								<td>repetition type:</td>
								<td>
									<select name="recur_type" size="1">
										<option value="none" <?php echo (isset($event) && $event->recur_type == 'none' || !isset($event))?'selected="selected"':"" ?>>no repeat</option>
										<option value="day" <?php echo (isset($event) && $event->recur_type == 'day')?'selected="selected"':"" ?>>repeat events every day</option>
										<option value="week" <?php echo (isset($event) && $event->recur_type == 'week')?'selected="selected"':"" ?>>repeat events weekly</option>
										<option value="month" <?php echo (isset($event) && $event->recur_type == 'month')?'selected="selected"':"" ?>>repeat one day in month</option>
										<option value="year" <?php echo (isset($event) && $event->recur_type == 'year')?'selected="selected"':"" ?>>repeat every year (birthdays)</option>
									</select>
								</td>
								<td>
									<?php echo mosToolTip("If your event repeats sequentially you can select one of the options. The dates at the left side then are publishing dates.","repetition type") ?>
								</td>
							</tr>
							<tr valign="top">
								<td>weekly repetition options:</td>
								<td>
									<select name="recur_week[]" size="7" multiple>
										<option value="1" <?php echo (isset($event) && strrpos($event->recur_week,'1') !== false)?'selected="selected"':"" ?>>monday</option>
										<option value="2" <?php echo (isset($event) && strrpos($event->recur_week,'2') !== false)?'selected="selected"':"" ?>>tuesday</option>
										<option value="3" <?php echo (isset($event) && strrpos($event->recur_week,'3') !== false)?'selected="selected"':"" ?>>wednesday</option>
										<option value="4" <?php echo (isset($event) && strrpos($event->recur_week,'4') !== false)?'selected="selected"':"" ?>>thursday</option>
										<option value="5" <?php echo (isset($event) && strrpos($event->recur_week,'5') !== false)?'selected="selected"':"" ?>>friday</option>
										<option value="6" <?php echo (isset($event) && strrpos($event->recur_week,'6') !== false)?'selected="selected"':"" ?>>saturday</option>
										<option value="0" <?php echo (isset($event) && strrpos($event->recur_week,'0') !== false)?'selected="selected"':"" ?>>sunday</option>
									</select>
								</td>
								<td>
									<?php echo mosToolTip("if events come up every week, you can choose one or more days, they should appear in.","weekly repetition options"); ?>
								</td>
							</tr>
							<tr>
								<td>repetition counter:</td>
								<td><input type="text" size="2" name="recur_count" value="<?php echo (isset($event))?$event->recur_count:'' ?>" /></td>
								<td><?php echo mosToolTip("If you don\'t know the correct date to stop publishing, you can make an event occur for an amount of times. If you submit exception dates they won\'t be counted.","repetition counter");?></td>
							</tr>
							<tr>
								<th colspan="3"><?php echo _FORM_EXCEPTIONS ?></th>
							</tr>
							<tr>
								<td valign="top">
									exception to add<br /><br />
									exception-list
								</td>
								<td>
									<input style="margin-top:6px;" type="text" size="10" name="dateexcept" id="exceptions" />
									<input type="button" value="  ... " onClick="return showCalendar('exceptions','dd.mm.yyyy')" />
									<input type="button" value=" + " onClick="addToList()" /><input type="button" value=" - " onClick="remList()" />
									<select size="10" style="width:173px;" name="daten">
									<?php 
										if (isset($event) && @$event->recur_except) {
											$except_dates = split("\n",$event->recur_except);
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
									<input type="hidden" name="recur_except" value="<?php echo (isset($event) && @$event->recur_except)?$exceptdates:"" ?>" />
								</td>
								<td valign="top"><?php echo mosToolTip("If you have got a repeated event and at some of the dates there are for example holidays and you do not want the event to take place, you may define an exception and add it to the exception-list.","exception-dates");?></td>
							</tr>
						</table>
						<br/>
						<table class="adminform">
							<tr>
								<th colspan="3">Contact Information</th>						
							</tr>
							<tr>
								<td>Contact Person</td>
								<td><input type="text" size="60" name="contact" value="<?php echo (isset($event))?$event->contact:'' ?>" /></td>
								<td><?php echo mosToolTip("This information is optional.<br /> You can add a person\'s name beeing involved in the events organisation.","Contact Person");?></td>
							</tr>
							<tr>
								<td>Contacts Homepage</td>
								<td><input type="text" size="60" name="url" value="<?php echo (isset($event))?$event->url:'' ?>" /></td>
								<td><?php echo mosToolTip("This information is optional.<br /> If there is a homepage for this event you can make it public here.","Homepage");?></td>
							</tr>
							<tr>
								<td>Contact eMail</td>
								<td><input type="text" size="60" name="email" value="<?php echo (isset($event))?$event->email:'' ?>" /></td>
								<td><?php echo mosToolTip("This information is optional.<br /> You can submit an email-adress supporting detailed information about the event. You should talk back with its owner because of caused traffic.","eMail");?></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</form>
		<?php
	}


	/**
	 * print the configuration page with links to "Manage Events" and "Manage Categories"
	 * @param		array			user_groups				array with valid joomla-user-groups
	*/
	function configPage( $config_values, $user_groups ) {
		global $mosConfig_live_site;

		//mosCommonHTML::loadOverlib();
		?>

		<table class="adminheading" width="100%">
			<tr>
				<th width="100%" style="background:url(<?php echo $mosConfig_live_site?>/administrator/component/eventcal/images/calendar.png) no-repeat" style="text-align:left;">
					EventCal <small>[Configuration]</small>
				</th>
			</tr>
		</table>

		<script type="text/javascript">
			function addTimeSpan() {
				if ((document.adminForm.addstart.value.match(/[0-9]?[0-9]:[0-9][0-9]/)) && (document.adminForm.addend.value.match(/[0-9]?[0-9]:[0-9][0-9]/))) {
					text = document.adminForm.addstart.value + " - " + document.adminForm.addend.value;
					value = document.adminForm.addstart.value + "-" + document.adminForm.addend.value;
					neu = new Option(text, value, false, false);
					listbox = document.getElementById('TimeTable');
					listbox.options[listbox.length] = neu;
				} else alert ('Wrong time format for starting or ending time!\n Please use hh:mm')
			}

			function submitbutton(task) {
				field = document.getElementById( 'TimeTable' );
				for (i=0;i<field.length;i++) {
					field.options[i].selected = true;
				}
				submitform(task);
			}
		</script>

		<form action="index2.php" method="post" name="adminForm">
			<input type="hidden" name="ca" value="eventcal" />
			<input type="hidden" name="task" value="storeconfig" />

		<table class="adminform">
			<tr>
				<td valign="top">
					<div id="cpanel">
						<div style="float: left;">
							<div class="icon">
								<a href="index2.php?ca=eventcal&task=categories" style="text-decoration: none;">
									<img src="images/categories.png" width="48" height="48" align="middle" border="0" alt="Category Manager" />
									<br />
									Category Manager
								</a>
							</div>
						</div>
						<div style="float: left;">
							<div class="icon">
								<a href="index2.php?ca=eventcal" style="text-decoration: none;">
									<img src="images/addedit.png" width="48" height="48" align="middle" border="0" alt="Event Manager" />
									<br />
									Event Manager
								</a>
							</div>
						</div>
					</div>
				</td>
				<td width="30%">
				</td>
				<td width="50%" valign="top">
					<?php 
						//$tabulator = new mosTabs( 0 ); 
						//$tabulator->startPane( "Configure" );
						//$tabulator->startTab( "General", "general" );
					?>
					<table width="100%">
						<tr>
							<td valign="top">
								Default view:
							</td>
							<td>
								<?php 
									$rows = Array( mosHTML::makeOption( "month", "Month-View" ), mosHTML::makeOption("week","Week-View"), mosHTML::makeOption("day","Day-View") );
									echo mosHTML::SelectList( $rows, "default_view", "class='inputbox' size='3'", "value", "text", $config_values->get('default_view', 'month') );
								?>
							</td>
							<td align="right" valign="top">
								<?php echo mosToolTip("You can set the sort of view selected by the script if there is passed none.","Default View"); ?>
							</td>
						</tr>
						<tr>
							<td valign="top">
								Who can post events: 
							</td>
							<td>
								<?php 
									$row->access = $config_values->get('who_can_post_events', 2);
									$access = mosAdminMenus::Access( $row );
									$access = preg_replace("/access/", "who_can_post_events", $access );
									echo $access;
								?>
							</td>
							<td align="right" valign="top">
								<?php echo mosToolTip("You can set wether it is possible to post events as public, registered or special user.","WHO_CAN_POST_EVENTS"); ?>
							</td>
						</tr>
						<tr>
							<td valign="top">
								Who can edit events: 
							</td>
							<td>
								<?php 
									$row->access = $config_values->get('who_can_edit_events', 2);
									$access = mosAdminMenus::Access( $row );
									$access = preg_replace("/access/", "who_can_edit_events", $access );
									echo $access;
								?>
							</td>
							<td align="right" valign="top">
								<?php echo mosToolTip("You can set wether it is possible to edit published events as public, registered or special user.","WHO_CAN_EDIT_EVENTS"); ?>
							</td>
						</tr>
					</table>
					<?php
					//	$tabulator->endTab();
						//$tabulator->startTab( "Date Format", "dateformat" );
					?>
					<table width="100%">
						<tr>
							<td valign="top">
								Starting Day of week:
							</td>
							<td>
								<?php 
									$weekdays[] = mosHTML::makeOption("1","monday");
									$weekdays[] = mosHTML::makeOption("2","tuesday");
									$weekdays[] = mosHTML::makeOption("3","wednesday");
									$weekdays[] = mosHTML::makeOption("4","thursday");
									$weekdays[] = mosHTML::makeOption("5","friday");
									$weekdays[] = mosHTML::makeOption("6","saturday");
									$weekdays[] = mosHTML::makeOption("0","sunday");
									echo mosHTML::selectList($weekdays, "week_startingday", "","value", "text", $config_values->get('week_startingday', 0));
								?>
							</td>
							<td align="right" valign="top">
								<?php echo mosToolTip("Sets the day weeks will start in week and month-view","WEEK_STARTINGDAY"); ?>
							</td>
						</tr>
					</table>
					<?php	
					//	$tabulator->endTab();
						//$tabulator->startTab( "Display", "display" );
					?>
					<table width="100%">
						<tr>
							<td valign="top">
								Display week-numbers: 
							</td>
							<td>
								<input type="radio" name="show_weeknumber" value="1" <?php echo ($config_values->get('show_weeknumber', false))?'checked="checked"':"" ?> />yes <input type="radio" name="show_weeknumber" value="0" <?php echo ($config_values->get('show_weeknumber', false))?"":'checked="checked"' ?> />no
							</td>
							<td align="right" valign="top">
								<?php echo mosToolTip("Wether the weeknumber in month-view is displayed in front of the rows or not.","SHOW_WEEKNUMBER"); ?>
							</td>
						</tr>
						<tr>
							<td valign="top">
								Display Back Button: 
							</td>
							<td>
								<input type="radio" name="showBackButton" value="1" <?php echo ($config_values->get('showBackButton'))?'checked="checked"':"" ?> />yes <input type="radio" name="showBackButton" value="0" <?php echo ($config_values->get('showBackButton', false))?"":'checked="checked"' ?> />no
							</td>
							<td align="right" valign="top">
								<?php echo mosToolTip("Wether there is a javascript-handled back button beneath the single event view or not.","SHOWBACKBUTTON"); ?>
							</td>
						</tr>
						<tr>
							<td valign="top">
								Display weeknumber as link: 
							</td>
							<td>
								<input type="radio" name="week_number_links" value="1" <?php echo ($config_values->get('week_number_links', false))?'checked="checked"':"" ?> />yes <input type="radio" name="week_number_links" value="0" <?php echo ($config_values->get('week_number_links', false))?"":'checked="checked"' ?> />no
							</td>
							<td align="right" valign="top">
								<?php echo mosToolTip("Wether the weeknumbers in the monthly view are displayed as links to corresponding week-views or not.","WEEK_NUMBER_LINKS"); ?>
							</td>
						</tr>
					</table>
					<?php
						//$tabulator->endTab();
						//$tabulator->startTab( "TimeTable", "timetable" );
					?>
					<table width="100%">
						<tr>
							<td valign="top">
								<select name="timetable[]" id="TimeTable" multiple="multiple" size="16" style="width:120px;">
								<?php 
									$timetable = unserialize( $config_values->get('timetable', 'a:1:{i:0;s:10:"0:00-23:59";}' ) );
									foreach ($timetable AS $timespan) {
										echo "<option>" . $timespan . "</option>";
									}
								?> 
								</select>
							</td>
							<td valign="top" style="border:1px solid #CFCFCF;padding:5px;text-align:left;">
								<br />
								starting time of block: <input type="text" name="addstart" value="" size="5" /><br /><br />
								ending time of block: &nbsp; <input type="text" name="addend" value="" size="5" /><br /><br />
								<input type="button" name="addTimestart" value="&lt;- add" onClick="addTimeSpan();" />
								<input type="button" name="addTimeend" value="X delete" onClick="document.getElementById('TimeTable').options[document.getElementById('TimeTable').selectedIndex] = null;" /><br />
							</td>
						</tr>
					</table>
					<?php
					//	$tabulator->endTab();
						//$tabulator->startTab( "Mailing (WiP)", "mailing" );
					?>
					<style type="text/css">
						table.roles td			{text-align:center;}
					</style>
					<table width="100%" cellspacing="0" class="roles">
						<tr>
							<th rowspan="2" valign="top">Usertype<br />feature not working yet!</th>
							<th rowspan="2"></th>
							<th colspan="3">new events</th>
							<th colspan="2">changes</th>
						</tr>
						<tr>
							<td width="70">autopublished</td>
							<td width="80">all</td>
							<td width="80">&nbsp;</td>
							<td width="70">autopublished</td>
							<td width="50">all</td>
						</tr>
						</tr>
						<?php
							$table_row_class = 1;
							foreach ($user_groups As $user_group) {
								if (strstr( $config_values->get( 'newauto' ), $user_group->id ))
									$newauto_checked = 'checked="checked"';
								else $newauto_checked = '';

								if (strstr( $config_values->get( 'newall' ), $user_group->id ))
									$newall_checked = 'checked="checked"';
								else $newall_checked = '';

								if (strstr( $config_values->get( 'changesauto' ), $user_group->id ))
									$changesauto_checked = 'checked="checked"';
								else $changesauto_checked = '';

								if (strstr( $config_values->get( 'changesall' ), $user_group->id ))
									$changesall_checked = 'checked="checked"';
								else $changesall_checked = '';
								
								echo	'<tr class="row' . $table_row_class . '">' .
										'<td style="text-align:left;">' . $user_group->name . '</td>' .
										'<td></td>' .
										'<td><input type="checkbox" name="newauto[]" value="' . $user_group->id . '" ' . $newauto_checked . ' /></td>' .
										'<td><input type="checkbox" name="newall[]" value="' . $user_group->id . '" ' . $newall_checked . ' /></td>' .
										'<td></td>' .
										'<td><input type="checkbox" name="changesauto[]" value="' . $user_group->id . '" ' . $changesauto_checked . ' /></td>' .
										'<td><input type="checkbox" name="changesall[]" value="' . $user_group->id . '" ' . $changesall_checked . ' /></td>' .
										'</tr>'
								;
								$table_row_class = !$table_row_class;
							}
						?>
					</table>
					<?php
						//$tabulator->endTab();
						//$tabulator->endPane();
					?>

				</td>
			</tr>
			<tr>
				<td></td>
				<td></td>
			</tr>
		</table>
		</form>
		<?php
	}





	/**
	 * prints a table with selected events
	 * @param	array						results			an array of event-objects to be printed
	 * @param	mosPageNavigation			pageNavigation	the pageNavigation to provide the next and previous functions
	 * @param	string						catlist			the html-source-code for the drop-down
	*/
	function mkStandardTable($results, $pageNavigation, $catlist ) {
		global $my, $mainframe, $mosConfig_live_site, $database;
		?>
		<form action="index2.php" method="get" name="adminForm">
		<input type="hidden" name="ca" value="eventcal" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="field" value="<?php echo mosGetParam( $_REQUEST, "field", "") ?>" />
		<input type="hidden" name="order" value="<?php echo mosGetParam( $_REQUEST, "order", "none") ?>" />

		<table class="adminheading" width="100%">
			<tr>
				<th rowspan="2" style="background:url(<?php echo $mosConfig_live_site?>/administrator/component/eventcal/images/calendar.png) no-repeat">
					EventCal <small><small> [Event Manager]</small></small>
				</th>
				<td align="right">
					<nobr>
					<?php echo $catlist; ?>
					</nobr>
					<br />
				</td>
			</tr>
			<tr>
				<td align="right"> 
					Filter: <input name="search" value="<?php echo mosGetParam($_REQUEST, "search", "") ?>" class="text_area" onchange="document.adminForm.submit();" type="text" />
				</td>
			</tr>
		</table>

		<table class="adminlist">
			<tr>
				<th width="2%">#</th>
				<th><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count($results); ?>);" /></th>
				<th width="30%" align="left"><?php echo HTML_admin_eventcal::sortIcon("title") ?>title</th>
				<th width="5%"><nobr><?php echo HTML_admin_eventcal::sortIcon("published") ?>Published</nobr></th>
				<th width="20%" align="left"><nobr><?php echo HTML_admin_eventcal::sortIcon("catid") ?>category</nobr></th>
				<th width="7%">eventID</th>
				<th width="10%"><?php echo HTML_admin_eventcal::sortIcon("start_date") ?>start</th>
				<th width="10%"><?php echo HTML_admin_eventcal::sortIcon("end_date") ?>ending</th>
				<th width="18%" align="left">recursion</th>
				<th width="2%"></th>
			</tr>
			<?php
				$toggle = true; 
				$count = -1;
				$unpublished = 0;
				
				foreach ($results AS $event) { 
					$count++;
					$toggle = ($toggle)?false:true
			?>
			<tr class="<?php echo ($toggle)?"row0":"row1" ?>">
				<td><?php echo $pageNavigation->rowNumber( $count ); ?></td>
				<td><?php echo mosCommonHTML::CheckedOutProcessing( $event, $count ); ?></td>
				<td>
				<?php
					if ( $event->checked_out && ( $event->checked_out != $my->id ) ) {
						echo '<b>' . $event->title . '</b>';
					} else {
					?>
						<a href="index2.php?ca=eventcal&task=edit&hidemainmenu=1&cid=<?php echo $event->id ?>" title="Edit Content">
							<b><?php echo htmlspecialchars($event->title, ENT_QUOTES); ?></b>
						</a>
					<?php
					}
				?>
				</td>
				<td align="center">
					<?php echo mosCommonHTML::PublishedProcessing( $event, $count ); ?>
				</td>
				<td class="category" style="font-weight:bold;color:<?php echo getParam("color",$event->cat_params) ?>;">
					<?php echo $event->cat_name ?>
				</td>
				<td align="center"><?php echo $event->id ?></td>
				<td align="center"><?php echo strftime("%x %R",$event->start_date) ?></td>
				<td align="center"><?php echo strftime("%x %R",$event->end_date) ?></td>
				<td>
					<?php
						switch ($event->recur_type) {
							case "day":
								echo "day";
								break;
							case "week":
								echo "every week on: ";
								echo (strrpos($event->recur_week,'1') === false)?"":"monday ";
								echo (strrpos($event->recur_week,'2') === false)?"":"tuesday ";
								echo (strrpos($event->recur_week,"3") === false)?"":"wednesday";
								echo (strrpos($event->recur_week,"4") === false)?"":"thurday";
								echo (strrpos($event->recur_week,"5") === false)?"":"friday";
								echo (strrpos($event->recur_week,"6") === false)?"":"saturday";
								echo (strrpos($event->recur_week,"0") === false)?"":"sunday";
								break;
							case "month":
								echo "month";
								break;
							case "year":
								echo "year";
								break;
							default:
								echo "no Repeat";
								break;
						}
						if ($event->recur_count) {
							echo " ($event->recur_count times)";
						}
					?>
				</td>
				<td>
					<a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $count?>','remove')" title="Delete Item">
					<img src="<?php echo $mosConfig_live_site?>/administrator/component/eventcal/images/trash.gif" title="delete" border="0" />
				</td>
			</tr>
			<?php
				}
				if ($unpublished) {
			?>
			<tr>
				<td colspan="10"></td>
			</tr>
			<?php
				}
			?>
		</table>
		<?php
			echo $pageNavigation->getListFooter();
		?>
		</form>
		<?php
	}

	/**
	 * prints a category-manager to set the params like autopublish and corresponding color
	 * @param	array					categories		an array with eventCal's categories
	 * @param	string					section_name	name of the section we are working in (Cross-Component)
	 * @param	mosPageNavigation		pageNav			the page-navigation object for page-navigation support
	*/
	function categoryParams($categories, $section_name, $pageNav) {
		global $mainframe, $my;
		
		//include the colorPicker
			echo '<script language="JavaScript" src="../component/eventcal/includes/js/colorpicker.js" type="text/javascript"></script>';
			echo '<div id="colorPicker" style="position:absolute;border:solid 1px #000000;width:140px;height:129px;visibility:hidden;"></div>';

		?>
		<form action="index2.php" method="post" name="adminForm">
		<input type="hidden" name="ca" value="eventcal" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<table class="adminheading">
			<tr>
				<th class="categories">
					Category Manager <small><small>[ <?php echo $section_name;?> ]</small></small>
				</th>
			</tr>
		</table>

		<table class="adminlist">
			<tr>
				<th width="10" align="left">#</th>
				<th width="20">
					<input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $categories );?>);" />
				</th>
				<th class="title">Category Name</th>
				<th>Published</th>
				<th width="5%" nowrap>Category ID</th>
				<th>Access</th>
				<th width="1%">
					<a href="javascript: saveorder( <?php echo count( $categories )-1; ?> )"><img src="images/filesave.png" border="0" width="16" height="16" alt="Save Order" /></a>
				</th>
				<th width="2%" colspan="2">Ordering</th>
				<th width="10%" nowrap>autopublish</th>
				<th width="12%">Color</th>
			</tr>
			<?php
				$k = 0;
				for ($i=0, $n=count( $categories ); $i < $n; $i++) {
					$row 	= &$categories[$i];

					$row->sect_link = 'index2.php?ca=sections&task=editA&hidemainmenu=1&id='. $row->section;

					$link = 'index2.php?ca=categories&section='. $section_name .'&task=editA&hidemainmenu=1&id='. $row->id;

					$access 	= mosCommonHTML::AccessProcessing( $row, $i );
					$checked 	= mosCommonHTML::CheckedOutProcessing( $row, $i );
					$published 	= mosCommonHTML::PublishedProcessing( $row, $i );
					$published = preg_replace( array("/'unpublish'/","/'publish'/"), array("'unpublishcategory'","'publishcategory'"), $published );
			?>
			<?php echo	'<tr class="row' . $k . '">' . 
						'<td>' .
						$pageNav->rowNumber( $i ) .
						'</td>' .
						'<td>' .
						$checked .
						'</td>' .
						'<td>';
					if ( $row->checked_out && ( $row->checked_out != $my->id ) ) {
						echo $row->name .' ( '. $row->title .' )';
					} else {
						echo '<a href="' . $link . '">' .
						$row->name .
						' ( '. $row->title .' )' .
						'</a>';
					}
			?>
				</td>
				<td align="center">
					<?php echo $published; ?>
				</td>
				<td align="center">
					<?php echo $row->id; ?>
				</td>
				<td align="center">
					<?php echo $access; ?>
				</td>
				<td>
					<?php echo $pageNav->orderUpIcon( $i ); ?>
				</td>
				<td>	
					<?php echo $pageNav->orderDownIcon( $i, $n ); ?>
				</td>
				<td align="center">
					<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center" />
				</td>
				<td align="center"> 
					<input type="checkbox" name="autopublish[]" <?php echo (getParam("autopublish",$row->params))?'checked="checked"':"" ?> value="<?php echo $row->id ?>" onClick="document.adminForm.cb<?php echo $i ?>.checked = true;" />
				</td>
				<td align="center">
					<input style="background-color:<?php echo getParam("color",$row->params, "#FFFFFF") ?>;" type="text" size="7" id="color<?php echo $i ?>display" value="<?php echo getParam("color",$row->params, "#FFFFFF") ?>" onClick="this.select(); document.getElementById('cb<?php echo $i ?>').checked = true;" onBlur="sendColor(this.value,'color<?php echo $i ?>display')" />&nbsp;<input type="button" value="..." onClick="ColorPicker('color<?php echo $i ?>display','cb<?php echo $i ?>');" />
					<input type="hidden" name="color[<?php echo $row->id ?>]" id="color<?php echo $i ?>" value="" />
				</td>
				<?php $k = 1 - $k; ?>
			</tr>
			<?php
			}
			?>
		</table>

		</form>
		<br />
		<div style="display:block;padding:2px;border:solid 1px #FF0000;">For adding new categories, please make use of joomla's category-manager, linked in the toolbar above. Afterwards come back and make your settings for color and autopublishing.</div>
		<?php
	}





	/**
	 * returns the source-code for a sort-icon in the header of the list-tables
	 * @param	string				param			the table-column's name the sort icon should be associated to
	 * @return	string								html-source-code
	*/
	function sortIcon( $field ) {
		
		//array with valid params -> params that will be passed when sort icon is clicked
			$valid_array = Array("option", "field", "state", "published", "catid");
		
		if ( mosGetParam( $_REQUEST, "field", "") == $field)
			$state = mosGetParam( $_REQUEST, "order", NULL);
		
		$params = array_keys( $_REQUEST );
		$base = "";

		for ( $i=0; $i < count( $_REQUEST ); $i++ ) {
			if ( in_array($params[$i], $valid_array) ) {
				$base = $base . "&" . $params[$i] . "=" . $_REQUEST[$params[$i]];
			}
		}

		//the first "&" needs to be extracted from base-url
			$base = "index2.php?" . substr( $base, 1 );

		if (isset($state)) {
			return mosHTML::sortIcon( $base, $field, $state ) . "&nbsp;";
		} else {
			return mosHTML::sortIcon( $base, $field ) . "&nbsp;";
		}
	}

}

?>
