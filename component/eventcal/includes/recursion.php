<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

class eventCal_Recursion {

	/**
	 * adds each recurrent event into the array each time it happens
	 * @param		array			events				the array with events directly from database
	*/
	function mkRecurrent(&$events) {
		$week_days = Array("sunday","monday","tuesday","wednesday","thursday","friday","saturday","sunday");
		$newEvents = array();
		foreach ($events AS $event) {
			switch ($event->recur_type) {
				case "day":
					if(is_numeric($event->recur_count)) {
						//if date is excepted it will not be counted so we need to add this amount of repeating-times
							$count = intval($event->recur_count) + intval(count(eventCal_Recursion::getExceptDates($event))-1);
					}
					else {
						$count = ceil(($event->end_date - $event->start_date)/86399);
					}

					$start_date = new mosEventCal_DateTimeObject( $event->start_date );
					$end_date = new mosEventCal_DateTimeObject( $event->end_date );

					for ($i = 0;$i < $count;$i++) {
						$date = clone( $start_date );
						$date->clearTime();
						//if date is one of the exceptiondates do not continue
							if( !in_array($date->timestamp, eventCal_Recursion::getExceptDates( $event )) ) {
								//calculate end_date at the same day, just the given hours later
									$event->start_date = $start_date->timestamp;
									$event->end_date = mktime($end_date->date["hours"],$end_date->date["minutes"],0,$date->date["mon"],$date->date["mday"],$date->date["year"]);
								$newEvents[] = clone( $event );
							}
						$start_date->offset( "+ 1 day" );
					}
				break;
				case "week":
					//clear arrays from old values
						$starting_days	= NULL;
					//every seven days from first correct weekday in time-range until date > end
						$start_date		= new mosEventCal_DateTimeObject( $event->start_date );
						$end_date		= new mosEventCal_DateTimeObject( $event->end_date );

					//do for every selected week-day
						for ( $char=0; $char < strlen( $event->recur_week ); $char++ ) {
							$date = new mosEventCal_DateTimeObject( $start_date->timestamp );

							$date->clearDate();
							$time_offset = $date->timestamp;
							$date->update( $start_date->timestamp );
							//look for first correct week day in date range
								$week_day = $event->recur_week[$char];
								if (!($date->date["wday"] == $week_day)) {
									$date->offset("this $week_days[$week_day]" );
								} else {
									$time_offset = 0;
								}

							//calculate repetition from recur_count or set to false 
								if (is_numeric($event->recur_count)) {
									$count = $event->recur_count;
								}
								else {
									$count = false;
								}
							//create dates for following weeks in range 
								while ($date->timestamp <= $end_date->timestamp || $count > 0) {
									$thisday = clone( $date );
									$thisday->clearTime();
									//if $count <> 0 then repeat event just as often as given, otherwise its false repeat until the end of date-range
										if ($count > 0 || ($count === false)) {
											//check exception dates
												if( !in_array( $thisday->timestamp,eventCal_Recursion::getExceptDates($event) ) ) {
													$starting_days[] = $date;
													$event->start_date = $date->timestamp + $time_offset;
													$event->end_date = mktime($end_date->date["hours"],$end_date->date["minutes"],0,$date->date["mon"],$date->date["mday"],$date->date["year"]);
													$newEvents[] = clone( $event );
													$count--; //may result in errors in later php versions when tried with $count = false
												}
										}
										else break;
								$date->offset( "+ 1 week" );
								}
						}
				break;
				case "month":
					//every mday in between starting and ending time
						$start_date = new mosEventCal_DateTimeObject($event->start_date);
						$end_date = new mosEventCal_DateTimeObject($event->end_date);

						if(is_numeric($event->recur_count)) {
							//if date is excepted it will not be countet so we need to add this amount of repeating-times
								$count = intval($event->recur_count) + intval(count(eventCal_Recursion::getExceptDates($event))-1);
						}
						else {
							$diff_timestamp = new mosEventCal_DateTimeobject($end_date->timestamp - $start_date->timestamp);
							$count = ($diff_timestamp->date["year"] - 1970) * 12 + $diff_timestamp->date["month"] + 1;
						}

						for ($i = 0; $i < $count; $i++) {

							$date = clone( $start_date );
							$date->clearTime();
							//if date is one of the exceptiondates do not continue
								if( !in_array($date->timestamp,eventCal_Recursion::getExceptDates($event)) ) {
									$event->start_date = $start_date->timestamp;
									$event->end_date = mktime($end_date->date["hours"],$end_date->date["minutes"],0,$start_date->date["mon"],$start_date->date["mday"],$start_date->date["year"]);
									$newEvents[] = clone( $event );
								}
							$start_date->offset( "+ 1 month");
						}
				break;
				case "year":
					//every yday in between starting and ending time
						$start_date = new mosEventCal_DateTimeObject($event->start_date);
						$end_date = getdate($event->end_date);
					
					if(is_numeric($event->recur_count)) {
					//if date is excepted it will not be counted so we need to add this amount of repeating-times
						$count = intval($event->recur_count) + intval(count(eventCal_Recursion::getExceptDates($event))-1);
					}
					else {
						$date = new mosEventCal_DateTimeObject($end_date[0] - $start_date->timestamp);
						$count = $date->date["year"] - 1969;
					}
					for ($i = 0;$i < $count;$i++) {
						$date = clone( $start_date );
						$date->clearTime();
						//if date is one of the exceptiondates do not continue
							if( !is_numeric($event->recur_except) || !in_array($date->timestamp,eventCal_Recursion::getExceptDates($event)) ) {
								$event->start_date = $start_date->timestamp;
								$event->end_date = mktime($end_date["hours"],$end_date["minutes"],0,$start_date->date["mon"],$start_date->date["mday"],$start_date->date["year"]);
								$newEvents[] = clone( $event );
					}
						$start_date->offset( "+ 1 year" );
					}
				break;
				case "none": //do not do anything
				default: //none
					$newEvents[] = clone( $event );
				break;    
			} //end of switch-block
		}  
		$events = $newEvents;
	}



	/**
	 * returns the events of the time-range from start till end or the events of the day of the start date
	 * adds the attribute type to the events to claim wether it is ending, starting aso. inside the given time-range
	 * @author	Kay Messerschmidt
	 * @version	1.6
	 * @param	events | array list with all events
	 * @param 	DateTimeObject	date			optional date-object for starting-date (otherwise "today" is taken)
	 * @param	DateTimeObject	end				optional date-object with ending-date 
	 * @return	array							list of events taking place in between the times or (if no ending date is given) at the day of start partly or completely
	*/
	function getDateEvents($events, $date = NULL, $end = NULL) {
	
		if (!isset($date)) $date = new mosEventCal_DateTimeObject();
		//check if timespan or day-events
			if (!isset($end)) {
			//make start and end date for whole day events
				$end = $date->endOfDay();
				$date->clearTime();
				$start = $date->timestamp;
			}
			else {
				$start = $date->timestamp;
				$end = $end->timestamp;
			}

		$return = array();

		foreach($events AS $event) {
			// <----|--->    |
			if ( ($event->start_date < $start) && ($event->end_date <= $end) && ($event->end_date >= $start) ) {
				$event->type = "end";
				$return[] = $event;
			}
			// | <----> |
			elseif ( ($event->start_date >= $start) && ($event->start_date <= $end) && ($event->end_date >= $start) && ($event->end_date <= $end) ) {
				$event->type = "complete"; 
				$return[] = $event;
			}
			// | <-----|----->
			elseif ( ($event->start_date >= $start) && ($event->start_date <= $end) && ($event->end_date >= $end) ) {
				$event->type = "start";
				$return[] = $event;
			}
			// <----|--------|----->
			elseif ( ($event->start_date <= $start) && ($event->end_date >= $end) ) {
				$event->type = "middle";
				$return[] = $event;
			}
		}
		return $return;
	}


	/**
	 * returns all dates as unix-timestamps for the given event
	 * @author	Kay Messerschmidt
	 * @version	1.0
	 * @param	mosEventCal_Event	event
	 * @return	array							all timestamps of exception-dates
	*/
	function getExceptDates($event) {
		return split("\n",$event->recur_except);
	}


}