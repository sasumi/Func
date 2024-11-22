<?php
/**
 * Time Enhancement Functions
 */
namespace LFPhp\Func;

use DateTime;
use DateTimeZone;
use Exception;

const DATETIME_FMT = 'Ymd H:i:s';
const ONE_MINUTE = 60;
const ONE_HOUR = 3600;
const ONE_DAY = 86400;
const ONE_WEEK = 604800;
const ONE_MONTH30 = 2592000; //30 days
const ONE_MONTH31 = 2678400; //31 days
const ONE_YEAR365 = 31536000; //one year, 365 days
const ONE_YEAR366 = 31622400; //one year, 366 days

/**
 * Get the upper, middle and lower segment arrays of the specified start and end time
 * @param string $start_str
 * @param string $end_str
 * @return array [[period_th, start_time, end_time],...]
 * @throws \Exception
 */
function time_get_month_period_ranges($start_str, $end_str){
	$start = strtotime($start_str);
	$end = strtotime($end_str);
	$ranges = [];
	$period_map = ['-01 00:00:00', '-11 00:00:00', '-21 00:00:00'];

	if($start > $end){
		throw new Exception('time range parameter error: '.$start_str.'-'.$end_str);
	}

	$start_d = date('d', $start);
	$end_d = date('d', $end);
	$start_period = $start_d > 20 ? 2 : ($start_d > 10 ? 1 : 0);
	$end_period = $end_d > 20 ? 2 : ($end_d > 10 ? 1 : 0);

	//in the same month
	if(date('Ym', $start) == date('Ym', $end)){
		$ym_str = date('Ym', $end);
		for($i = $start_period; $i <= $end_period; $i++){
			$s = max(strtotime($ym_str.$period_map[$i]), $start);
			$e = $i == $end_period ? $end : min(strtotime($ym_str.$period_map[$i + 1]) - 1, $end);
			$ranges[] = [$i, date(DATETIME_FMT, $s), date(DATETIME_FMT, $e)];
		}
		return $ranges;
	}//in next month
	else if(date('Ym', strtotime('+1 month', strtotime(date('Ym-01', $start)))) == date('Ym', $end)){
		$st_ym_str = date('Ym', $start);
		$ranges = array_merge($ranges, time_get_month_period_ranges(date('Ymd H:i:s', $start), $st_ym_str.'-'.date("t", strtotime($st_ym_str.'-01')). '23:59:59'));
		$ranges = array_merge($ranges, time_get_month_period_ranges(date('Ym-01 00:00:00', $end), date('Ymd H:i:s', $end)));
	}//sep by months
	else{
		//start of first month
		$st_ym_str = date('Ym', $start);
		$ranges = array_merge($ranges, time_get_month_period_ranges(date('Ymd H:i:s', $start), $st_ym_str.'-'.date("t", strtotime($st_ym_str.'-01')). '23:59:59'));

		//middle months
		$s = new DateTime();
		$s->setTimestamp($start);
		$e = new DateTime();
		$e->setTimestamp($end);
		$months = $s->diff($e)->m + $s->diff($e)->y*12;
		for($m = 1; $m < $months; $m++){
			$tmp = strtotime("+$m month", strtotime(date('Ym-01', $start)));
			$month_st = date('Ym-01 00:00:00', $tmp);
			$month_ed = date('Ym-'.date('t', $tmp).' 23:59:59', $tmp);

			if(strtotime($month_ed) > $end){
				break;
			}
			$ranges = array_merge($ranges, time_get_month_period_ranges($month_st, $month_ed));
		}

		//end of last month
		$ranges = array_merge($ranges, time_get_month_period_ranges(date('Ym-01 00:00:00', $end), date('Ymd H:i:s', $end)));
	}
	return $ranges;
}

/**
 * Call interval guarantee
 * @param string $timer_key
 * @param int $interval
 */
function keep_interval($timer_key, $interval){
	$k = __FUNCTION__.$timer_key;
	if($GLOBALS[$k]){
		$need_sleep = $interval - (time() - $GLOBALS[$k]);
		if($need_sleep > 0){
			sleep($need_sleep);
		}
	}
	$GLOBALS[$k] = time();
}

/**
 * @param string $timezone_title
 * @return float|int
 * @throws \Exception
 */
function get_timezone_offset_min_between_gmt($timezone_title) {
	$dtz = new DateTimeZone($timezone_title);
	$dt = new DateTime("now", $dtz);
	return $dtz->getOffset($dt)/ONE_MINUTE;
}

/**
 * Get the remaining time (seconds)
 * If it is CLI mode, this function does not perform calculations
 * @return int|null seconds, null means unlimited
 */
function get_time_left(){
	$sys_max_exe_time = ini_get('max_execution_time');
	if(!$sys_max_exe_time){
		return null;
	}
	$cost = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
	return $sys_max_exe_time - $cost;
}

/**
 * Filter time range, add hours, minutes and seconds
 * @param array $ranges time range (start, end)
 * @param string|int $default_start default start time
 * @param string|int $default_end default end time
 * @param bool $as_datetime whether to return in date + time format
 * @return array [start time, end time]
 */
function filter_date_range($ranges, $default_start = null, $default_end = null, $as_datetime = false){
	[$start, $end] = $ranges ?: [];
	if(!isset($start) && $default_start){
		$start = is_numeric($default_start) ? date('Ymd', $default_start) : $default_start;
	}
	if($as_datetime && $start){
		$start .= '00:00:00';
	}
	if(!isset($end) && $default_end){
		$end = is_numeric($default_end) ? date('Ymd', $default_end) : $default_end;
	}
	if($as_datetime && $end){
		$end .= '23:59:59';
	}
	return [$start, $end];
}

/**
 * Calculate a precise time difference.
 * @param string $start result of microtime()
 * @param string $end result of microtime(); if NULL/FALSE/0/'' then it's now
 * @return float difference in seconds, calculated with minimum precision loss
 */
function microtime_diff($start, $end = null){
	if(!$end){
		$end = microtime();
	}
	[$start_usec, $start_sec] = explode(" ", $start);
	[$end_usec, $end_sec] = explode(" ", $end);
	$diff_sec = intval($end_sec) - intval($start_sec);
	$diff_usec = floatval($end_usec) - floatval($start_usec);
	return floatval($diff_sec) + $diff_usec;
}

/**
 * format time range
 * @param int $secs
 * @param bool $keep_zero_padding
 * @param bool $full_desc
 * @return string
 */
function format_time_size($secs, $keep_zero_padding = true, $full_desc = false){
	$tks = [
		ONE_YEAR365 => ['year', 'yr'],
		ONE_MONTH30 => ['month', 'mo'],
		ONE_WEEK => ['week', 'wk'],
		ONE_DAY => ['day', 'd'],
		ONE_HOUR => ['hour', 'h'],
		ONE_MINUTE => ['minute', 'm'],
		1 => ['second', 's'],
	];
	$text = '';
	foreach($tks as $s => [$fd, $sd]){
		if($secs > $s){
			$offset = round($secs/$s);
			$text .= $offset.($full_desc ? $fd : $sd);
			$secs -= $offset*$s;
		}else if($keep_zero_padding && $text){
			$text .= "0".($full_desc ? $fd : $sd);
		}
	}
	return $text;
}

/**
 * Convert microseconds to the specified time format
 * @param string $microtime microsecond string, generated by microtime(false)
 * @param string $format time format
 * @param int $precision precision (seconds later)
 * @return string
 */
function microtime_to_date($microtime, $format = 'Ymd H:i:s', $precision = 3){
	[$usec, $sec] = explode(' ', $microtime);
	$usec_str = '';
	if($precision){
		$usec_str = '.';
		$usec_str .= round($usec, $precision)*pow(10, $precision);
	}
	return date($format, $sec).$usec_str;
}

/**
 * Convert seconds (floating point number) to the specified time format
 * @param float $float_time time, generated by microtime(true)
 * @param string $format time format
 * @param int $precision precision (seconds later)
 * @return string
 */
function float_time_to_date($float_time, $format = 'Ymd H:i:s', $precision = 3){
	[$timestamp, $decimals] = explode('.', $float_time.'');
	if($precision){
		$decimals = $decimals ?: '0';
		$decimals = substr($decimals, 0, $precision);
		$decimals = str_pad($decimals, $precision, '0', STR_PAD_RIGHT);
	}
	return date($format, $timestamp).($decimals ? '.'.$decimals : '');
}

/**
 * check time string is empty (cmp to 1970)
 * @param string $time_str
 * @return bool
 */
function time_empty($time_str){
	return !$time_str || date('Ymd', strtotime($time_str)) === '19700101';
}

/**
 * Format the time to display in a friendly way
 * @param int $timestamp
 * @param bool $as_html whether to use span wrapping
 * @return string
 */
function pretty_time($timestamp, $as_html = false){
	$str = '';
	$offset = time() - $timestamp;
	$before = $offset > 0;
	$offset = abs($offset);
	$unit_cal = array(
		'Year' => ONE_YEAR365,
		'Month' => ONE_MONTH30,
		'day' => ONE_DAY,
		'hour' => ONE_HOUR,
		'minute' => ONE_MINUTE,
	);
	if($offset > 30 && $offset < 60){
		$str = $before ? 'Just now' : 'Wait';
	}else if($offset <= 30){
		$str = $before ? 'Just now' : 'Immediately';
	}else{
		$us = array();
		foreach($unit_cal as $u){
			$tmp = $offset >= $u ? floor($offset/$u) : 0;
			$offset -= $tmp ? $u : 0;
			$us[] = $tmp;
		}
		foreach($us as $k => $u){
			if($u){
				$str = $u.array_keys($unit_cal)[$k].($before ? 'before' : 'after');
				break;
			}
		}
	}
	return $as_html ? '<span title="'.date('Ymd H:i:s', $timestamp).'">'.$str.'</span>' : $str;
}

/**
 * Supplement the date range and fill in the blank days in the middle
 * @param string|int $start start time (start time is allowed to be greater than end time)
 * @param string|int $end end time
 * @param string $format result date format, if set to month, the function will automatically remove duplicates
 * @return array
 */
function make_date_ranges($start, $end = '', $format = 'Ymd'){
	$end = $end ?: time();
	$st = is_string($start) ? strtotime($start) : $start;
	$ed = is_string($end) ? strtotime($end) : $end;

	$tmp = [];
	$step_offset = $ed > $st ? ONE_DAY : (-1 * ONE_DAY);
	$offset_dates = ceil(abs($ed - $st)/ONE_DAY);
	while($offset_dates -- >= 0){
		$tmp[] = date($format, $st);
		$st += $step_offset;
	}
	return array_values(array_unique($tmp));
}

/**
 * Get the date after $days base on start day
 * Actual date = number of working days + number of weekend days - 1
 * @param string $start start date
 * @param int $days Number of working days: positive number means going backward, negative number means going forward
 * @return string Ymd date
 */
function calc_actual_date($start, $days){
	$t = date('N', strtotime($start));
	if($days == 0){
		return $start;
	}
	if($days > 0){
		//Forward
		$thisWeekWork = (6 - $t) > 0 ? (6 - $t) : 0; //Working days this week
		$weeks = ($days - $thisWeekWork)%5 ? floor(($days - $thisWeekWork)/5)*2 : ((($days - $thisWeekWork)/5) - 1)*2; //Total number of weekends starting from next Monday
		$diff_days = $weeks + $days + 1; //weekend number + weekdays + plus this weekend - 1
		$expect = date("Ymd", strtotime($start) + $diff_days*ONE_DAY);
	}else{
		$days = abs($days);
		//Reverse
		$thisWeekWork = $t > 5 ? 5 : $t; //Working day of this week
		$thisWeekends = $t > 5 ? ($t - 5) : 0; //Number of weekend days this week
		$weeks = ceil(($days - $thisWeekWork)/5)*2; // remaining weekends
		$diff_days = $thisWeekends + $weeks + $days - 1; //weekend days this week + remaining weekend days + working days - 1
		$expect = date("Ymd", strtotime($start) - $diff_days*ONE_DAY);
	}
	return $expect;
}

/**
 * Calculate time difference to text
 * @param string $start
 * @param string $end
 * @return string
 */
function time_range($start, $end){
	return time_range_v(strtotime($end) - strtotime($start));
}

/**
 * Calculate the estimated end time ETA
 * @param int $start_time start time
 * @param int $index Current processing sequence number
 * @param int $total total quantity
 * @param bool $pretty whether to return the remaining time in text format, set false to return seconds
 * @return int|string
 */
function time_get_eta($start_time, $index, $total, $pretty = true){
	$seconds = intval((time() - $start_time)*($total - $index)/$index);
	return $pretty ? format_time_size($seconds) : $seconds;
}

/**
 * Convert time length to string
 * <pre>
 * $str = time_range_v(3601);
 * //1H 0M 1S
 * </pre>
 * @param int $seconds
 * @return string
 */
function time_range_v($seconds){
	$d = floor($seconds/ONE_DAY);
	$seconds = $seconds - $d*ONE_DAY;
	$h = floor($seconds/ONE_HOUR);
	$seconds = $seconds - $h*ONE_HOUR;
	$m = floor($seconds/ONE_MINUTE);
	$seconds = $seconds - $m*ONE_MINUTE;
	$s = (int)$seconds;
	$str = $d ? $d.'d' : '';
	$str .= $h ? $h.'h' : ($str ? '0h' : '');
	$str .= $m ? $m.'m' : ($str ? '0m' : '');
	$str .= $s ? $s.'s' : ($str ? '0s' : '');
	return $str ?: '0';
}

/**
 * Make UTC time string
 * @param $timestamp
 * @param $short
 * @return array|false|string|string[]
 */
function mk_utc($timestamp = null, $short = false){
	$timestamp = $timestamp ?: time();
	if(!$short){
		$str = date('Ymd H:i:s', $timestamp);
		$str = str_replace(' ', 'T', $str).'.000Z';
	}else{
		$str = date('Ymd H:i', $timestamp);
		$str = str_replace(' ', 'T', $str);
	}
	return $str;
}
