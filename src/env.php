<?php

namespace LFPhp\Func;

function server_in_windows(){
	return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
}

/**
 * get console text colorize
 * @param $text
 * @param null $fore_color
 * @param null $back_color
 * @return string
 */
function console_color($text, $fore_color = null, $back_color = null){
	static $fore_color_map = [
		'black'        => '0;30',
		'dark_gray'    => '1;30',
		'blue'         => '0;34',
		'light_blue'   => '1;34',
		'green'        => '0;32',
		'light_green'  => '1;32',
		'cyan'         => '0;36',
		'light_cyan'   => '1;36',
		'red'          => '0;31',
		'light_red'    => '1;31',
		'purple'       => '0;35',
		'light_purple' => '1;35',
		'brown'        => '0;33',
		'yellow'       => '1;33',
		'light_gray'   => '0;37',
		'white'        => '1;37',
	], $back_color_map = [
		'black'      => '40',
		'red'        => '41',
		'green'      => '42',
		'yellow'     => '43',
		'blue'       => '44',
		'magenta'    => '45',
		'cyan'       => '46',
		'light_gray' => '47',
	];
	$color_str = '';
	if($fore_color){
		$color_str .= "\033[".$fore_color_map[$fore_color]."m";
	}
	if($back_color){
		$color_str .= "\033[".$back_color_map[$back_color]."m";
	}
	if($color_str){
		return $color_str.$text."\033[0m";
	}
	return $text;
}

/**
 * show progress in console
 * @param int $index
 * @param int $total
 * @param string $patch_text 补充显示文本
 * @param int $start_time 开始时间戳
 * @param int $progress_length
 * @param int $max_length
 */
function show_progress($index, $total, $patch_text = '', $start_time = null, $progress_length = 50, $max_length = 0){
	$pc = round(100*$index/$total);
	$fin = round(($index/$total)*$progress_length);
	$left = $progress_length - $fin;

	$reminds = '';
	if($start_time){
		$expired = time() - $start_time;
		$reminds = ' in '.round($expired*$left/$fin).'s';
	}
	$str = "\r$index/$total $pc% [".str_repeat('=', $fin).str_repeat('.', $left)."]{$reminds} $patch_text";
	$max_length = $max_length ?: strlen($str) + 10;
	$str = str_pad($str, $max_length, ' ', STR_PAD_RIGHT);
	echo $str;
	if($index >= $total){
		echo PHP_EOL;
	}
}