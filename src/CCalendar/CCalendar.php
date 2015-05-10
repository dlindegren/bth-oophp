<?php 

/**
* CCalendar - a calendar.
*/
class CCalendar {

	private $image;
	private $month;
	private $year;
	private $nextMonth;
	private $prevMonth;

	/**
	* @param string $month, string $day, string $year
	*/
	public function __construct($month, $day, $year) {
		$this->month 	= $month;
		$this->day 		= $day;
		$this->year 	= $year;
	}

	/**
	* get image
	* @return $this->image;
	*/
	public function getImage() {
		$this->setImage();
		return $this->image;
	}

	/**
	* get month
	* @return $this->month;
	*/
	public function getMonth() {
		$this->setMonth($this->month);
		return $this->month;
	}

	/**
	* get previous month
	* @return $this->prevMonth;
	*/
	public function getPrevMonth() {
		$prevMonth = strtotime($this->month . '+11 month');
		$this->prevMonth = date('F', $prevMonth);
		return $this->prevMonth;
	}

	/**
	* get next month
	* @return $this->nextMonth;
	*/
	public function getNextMonth() {
		$nextMonth = strtotime($this->month . '+1 month');
		$this->nextMonth = date('F', $nextMonth);
		return $this->nextMonth;
	}

	/**
	* get year
	* @return $this->year;
	*/
	public function getYear() {
		$this->setYear();
		return $this->year;
	}

	/**
	* A function that sets the month depending on $_GET
	* @param $month;
	*/
	private function setMonth($month) {
		if($_GET) {		
			if($_GET['month'] == "January") {$this->month = "January";}
			if($_GET['month'] == "February") {$this->month = "February";}
			if($_GET['month'] == "March") {$this->month = "March";}
			if($_GET['month'] == "April") {$this->month = "April";}
			if($_GET['month'] == "May") {$this->month = "May";}
			if($_GET['month'] == "June") {$this->month = "June";}
			if($_GET['month'] == "July") {$this->month = "July";}
			if($_GET['month'] == "August") {$this->month = "August";}
			if($_GET['month'] == "September") {$this->month = "September";}
			if($_GET['month'] == "October") {$this->month = "October";}
			if($_GET['month'] == "November") {$this->month = "November";}
			if($_GET['month'] == "December") {$this->month = "December";}
		} else {
			$this->month = date('F');
		}
	}

	/**
	* A function that sets the year depending on $_GETs
	*/
	private function setYear() {
		if($_GET) {
			if(isset($_GET['year'])) {
				$this->year = $_GET['year'];
			}

			if(isset($_GET['prevMonth']) && $_GET['month'] == "December") {
				$this->year = $this->year -1;
				$this->year = (string)$this->year;
				header("Location: calendar.php?month=December&year=" . $this->year);
			}

			if(isset($_GET['nextMonth']) && $_GET['month'] == "January") {
				$this->year = $this->year +1;
				$this->year = (string)$this->year;
				header("Location: calendar.php?month=January&year=" . $this->year);
			}
		} else {	
			$this->year = date('Y');
		}
	}

	/**
	* A function that sets the image depending on $this->month
	*/
	private function setImage() {
		if($this->month == "January") {$this->image = "jan";}
		if($this->month == "February") {$this->image = "feb";}
		if($this->month == "March") {$this->image = "mar";}
		if($this->month == "April") {$this->image = "apr";}
		if($this->month == "May") {$this->image = "may";}
		if($this->month == "June") {$this->image = "jun";}
		if($this->month == "July") {$this->image = "jul";}
		if($this->month == "August") {$this->image = "aug";}
		if($this->month == "September") {$this->image = "sep";}
		if($this->month == "October") {$this->image = "okt";}
		if($this->month == "November") {$this->image = "nov";}
		if($this->month == "December") {$this->image = "dec";}
	}

	/**
	* HTML Markup for calendar
	* @return string $calendar
	*/
	public function printCalendar() {

		//Converta månad och år - sätt variabler.
		//Convert month and year and put into variables.
		$month = date('m', strtotime($this->month));
		$year = $this->year;

		//Start HTML-markup
		$calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';

		//Header
		$headings = array('Söndag','Måndag','Tisdag','Onsdag','Torsdag','Fredag','Lördag');
		$calendar.= '<tr class="calendar-table"><td class="calendar-day-head">'.implode('</td><td class="calendar-day-head">',$headings).'</td></tr>';

		//Set variables (days and month), prepare array for dates.
		$running_day = date('w',mktime(0,0,0,$month,1,$year));
		$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
		$days_in_this_week = 1;
		$day_counter = 0;
		$dates_array = array();

		//Prepare row for one week
		$calendar.= '<tr class="calendar-row">';

		//HTML markup for "blank days" until first day of the week
		for($x = 0; $x < $running_day; $x++):
			$calendar.= '<td class="calendar-day-np"> </td>';
			$days_in_this_week++;
		endfor;

		//Add days
		for($list_day = 1; $list_day <= $days_in_month; $list_day++):
			$calendar.= '<td class="calendar-day">';
				
				$calendar.= '<div class="day-number">'.$list_day.'</div>';

				
			$calendar.= '</td>';
			if($running_day == 6):
				$calendar.= '</tr>';
				if(($day_counter+1) != $days_in_month):
					$calendar.= '<tr class="calendar-row">';
				endif;
				$running_day = -1;
				$days_in_this_week = 0;
			endif;
			$days_in_this_week++; $running_day++; $day_counter++;
		endfor;

		//Close with rest of the days in the week
		if($days_in_this_week < 8):
			for($x = 1; $x <= (8 - $days_in_this_week); $x++):
				$calendar.= '<td class="calendar-day-np"> </td>';
			endfor;
		endif;

		//Finish row
		$calendar.= '</tr>';

		//Finish table
		$calendar.= '</table>';
		return $calendar;
	}
}