<?php
/**
 * Crontab Enhancement Functions
 */
namespace LFPhp\Func;

use Exception;

/**
 * Check if the cron format matches the specified timestamp
 * @param string $format cron format. Currently not supporting year, format is: minutes hours days months weeks
 * @param int $time Default is current timestamp
 * @param string|null $error mismatch error info
 * @return bool
 * @throws \Exception
 */
function cron_match($format, $time, &$error = null){
	static $full_fills, $fix_ranges;
	if(!isset($full_fills)){
		$full_fills = [
			range(0, 59), //min
			range(0, 23), //hour
			range(1, 31), //date
			range(1, 12), //month
			range(0, 7), //day
		];
	}
	if(!isset($fix_ranges)){
		/**
		 * Fix range, such as 1 ~ 20, 23 ~ 7
		 * @param int $start Start calculation point
		 * @param int $end End calculation point
		 * @param array $all_ranges Limited range
		 * @return array
		 * @throws \Exception
		 */
		$fix_ranges = function($start, $end, $all_ranges = []){
			if($all_ranges && (!in_array($start, $all_ranges) || !in_array($end, $all_ranges))){
				throw new Exception('range must in ranges:'.join(',', $all_ranges));
			}
			if($start <= $end){
				return range($start, $end);
			}else{
				$min = $all_ranges[0];
				$max = $all_ranges[count($all_ranges) - 1];
				return array_merge(range($start, $max), range($min, $end));
			}
		};
	}

	$time_info = explode(' ', date('i H d m N Y', $time));
	$items = array_map('trim', explode(' ', trim($format)));

	foreach($items as $idx => $item){
		$parts = explode(',', $item);
		$matched = false;
		foreach($parts as $p){
			//*
			if($p == '*'){
				$matched = true;
				break;
			}

			//*/10 format
			else if(preg_match('/^\*\/(\d+)$/', $p, $matches)){
				if($time_info[$idx]%$matches[1] == 0){
					$matched = true;
					break;
				}
			}

			//23-9/3 format
			else if(preg_match('/^(\d+)-(\d+)\/(\d+)$/', $p, $matches)){
				list($_, $st, $ed, $mod) = $matches;
				$ranges = array_filter($fix_ranges($st, $ed, $full_fills[$idx]), function($item)use($mod){return $item%$mod == 0;});
				if(in_array($time_info[$idx], $ranges)){
					$matched = true;
					break;
				}
			}
			//3-7 range
			else if(preg_match('/^(\d+)-(\d+)$/', $p, $matches)){
				$ranges = $fix_ranges($matches[1], $matches[2], $full_fills[$idx]);
				if(in_array($time_info[$idx], $ranges)){
					$matched = true;
					break;
				}
			}else if(preg_match('/^\d+$/', $p, $matches) && in_array($matches[0], $full_fills[$idx])){
				if($time_info[$idx] == $matches[0]){
					$matched = true;
					break;
				}
			}
		}
		if(!$matched){
			$error = ['min', 'hour', 'date', 'month', 'day'][$idx]." ($item) no matched by format: $format";
			return false;
		}
	}
	return true;
}

/**
 * @param array $rules
 * @param callable $on_before_call
 * @param int $check_interval seconds, must min than one minutes
 * @throws \Exception
 */
function cron_watch_commands(array $rules, callable $on_before_call = null, $check_interval = 30){
	$list = [];
	foreach($rules as $rule){
		$rule = trim($rule);
		$tmp = explode(' ', $rule);
		$format = join(' ', array_slice($tmp, 0, 5));
		$cmd = join(' ', array_slice($tmp, 5));
		$list[] = [$format, $cmd];
	}
	while(true){
		$now = time();
		foreach($list as $k => list($format, $cmd)){
			if(cron_match($format, $now)){
				if($on_before_call && $on_before_call($cmd, $now) === false){
					unset($list[$k]);
					continue;
				}
				run_command($cmd, [], true);
			}
		}
		if(time() - $now - 60 < 0){
			continue;
		}
		$sleep_time = max(0, $check_interval - (time() - $now));
		if($sleep_time){
			sleep($sleep_time);
		}
	}
}
