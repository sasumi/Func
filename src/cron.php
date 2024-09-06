<?php
/**
 * crontab相关操作函数
 */
namespace LFPhp\Func;

use Exception;

/**
 * 检测cron格式是否匹配指定时间戳
 * @param string $format cron格式。暂不支持年份，格式为：分钟 时钟 天数 月数 星期
 * @param int $time 默认为当前时间戳
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
		 * 修正范围，如 1 ~ 20, 23 ~ 7
		 * @param int $start 开始计算点
		 * @param int $end 结束计算点
		 * @param array $all_ranges 限定范围
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

			//*/10 格式
			else if(preg_match('/^\*\/(\d+)$/', $p, $matches)){
				if($time_info[$idx]%$matches[1] == 0){
					$matched = true;
					break;
				}
			}

			//23-9/3 格式
			else if(preg_match('/^(\d+)-(\d+)\/(\d+)$/', $p, $matches)){
				list($_, $st, $ed, $mod) = $matches;
				$ranges = array_filter($fix_ranges($st, $ed, $full_fills[$idx]), function($item)use($mod){return $item%$mod == 0;});
				if(in_array($time_info[$idx], $ranges)){
					$matched = true;
					break;
				}
			}
			//3-7 范围
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
