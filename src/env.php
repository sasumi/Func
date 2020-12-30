<?php
namespace LFPhp\Func;

/**
 * 检测服务器是否在视窗系统中运行
 * @return bool
 */
function server_in_windows(){
	return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
}

/**
 * 获取PHP允许上传的最大文件尺寸
 * 依赖：最大上传文件尺寸，最大POST尺寸
 * @param bool $human_readable 是否以可读方式返回
 * @return string|number
 */
function get_upload_max_size($human_readable = false){
	$upload_sz = trim(ini_get('upload_max_filesize'));
	$upload_sz = resolve_size($upload_sz);
	$post_sz = trim(ini_get('post_max_size'));
	$post_sz = resolve_size($post_sz);
	$ret = min($upload_sz, $post_sz);
	if($human_readable){
		return format_size($ret);
	}
	return $ret;
}

/**
 * 获取PHP配置信息
 * @return array
 */
function get_php_info(){
	static $phpinfo;
	if($phpinfo){
		return $phpinfo;
	}

	$entitiesToUtf8 = function($input){
		return preg_replace_callback("/(&#[0-9]+;)/", function($m){
			return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES");
		}, $input);
	};
	$plainText = function($input) use ($entitiesToUtf8){
		return trim(html_entity_decode($entitiesToUtf8(strip_tags($input))));
	};
	$titlePlainText = function($input) use ($plainText){
		return '# '.$plainText($input);
	};

	ob_start();
	phpinfo(-1);

	$phpinfo = array('phpinfo' => array());

	// Strip everything after the <h1>Configuration</h1> tag (other h1's)
	if(!preg_match('#(.*<h1[^>]*>\s*Configuration.*)<h1#s', ob_get_clean(), $matches)){
		return array();
	}

	$input = $matches[1];
	$matches = array();

	if(preg_match_all('#(?:<h2.*?>(?:<a.*?>)?(.*?)(?:<\/a>)?<\/h2>)|'.'(?:<tr.*?><t[hd].*?>(.*?)\s*</t[hd]>(?:<t[hd].*?>(.*?)\s*</t[hd]>(?:<t[hd].*?>(.*?)\s*</t[hd]>)?)?</tr>)#s', $input, $matches, PREG_SET_ORDER)){
		foreach($matches as $match){
			$fn = strpos($match[0], '<th') === false ? $plainText : $titlePlainText;
			if(strlen($match[1])){
				$phpinfo[$match[1]] = array();
			}elseif(isset($match[3])){
				$keys1 = array_keys($phpinfo);
				$phpinfo[end($keys1)][$fn($match[2])] = isset($match[4]) ? array(
					$fn($match[3]),
					$fn($match[4]),
				) : $fn($match[3]);
			}else{
				$keys1 = array_keys($phpinfo);
				$phpinfo[end($keys1)][] = $fn($match[2]);
			}

		}
	}
	return $phpinfo;
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
		'default'      => '0:39',
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
	$reminds = '';
	if(!$start_time){
		static $inner_start_time;
		if(!$inner_start_time){
			$inner_start_time = time();
		}
		$start_time = $inner_start_time;
	}
	if($index){
		$reminds = ' in '.format_time_size((time() - $start_time)*($total - $index)/$index);
	}
	$fin_chars = round(($index/$total)*$progress_length);
	$left_chars = $progress_length - $fin_chars;

	$str = "\r$index/$total $pc% [".str_repeat('=', $fin_chars).str_repeat('.', $left_chars)."]{$reminds} $patch_text";
	$max_length = $max_length ?: strlen($str) + 10;
	$str = str_pad($str, $max_length, ' ', STR_PAD_RIGHT);
	echo $str;
	if($index >= $total){
		echo PHP_EOL;
	}
}

/**
 * 运行终端命令
 * @param string $command 命令
 * @param array $param 参数
 * @param bool $async 是否以异步方式执行
 * @return bool|string|null
 * @throws \Exception
 */
function run_command($command, array $param = [], $async = false){
	$descriptors_pec = array(
		0 => array("pipe", "r"),   // stdin is a pipe that the child will read from
		1 => array("pipe", "w"),   // stdout is a pipe that the child will write to
		2 => array("pipe", "w")    // stderr is a pipe that the child will write to
	);

	//WINDOWS环境：必须传递 $_SERVER给子进程，否则子进程内数据库连接可能出错 ？？
	$process = proc_open(build_command($command, $param), $descriptors_pec, $pipes, realpath('./'), $_SERVER);
	if($process === false || $process === null){
		throw new \Exception('Process create fail:'.$command);
	}
	if($async){
		return true;
	}
	if(is_resource($process)){
		$result_str = $error_str = '';
		while($s = fgets($pipes[1])){
			$result_str .= $s;
		}
		$has_error = false;
		while($e = fgets($pipes[2])){
			$has_error = true;
			$error_str .= $e;;
		}
		return $has_error ? $error_str : $result_str;
	}
	proc_close($process);
	return null;
}

/**
 * 构建命令行
 * @param $cmd_line
 * @param array $param
 * @return string
 */
function build_command($cmd_line, array $param = []){
	foreach($param as $k => $val){
		if(is_array($val)){
			foreach($val as $i => $vi){
				$vi = escapeshellarg($vi);
				$cmd_line .= " --{$k}[{$i}]={$vi}";
			}
		} else if(strlen($k)>0){
			$val = escapeshellarg($val);
			$cmd_line .= " --$k=$val";
		} else{
			$val = escapeshellarg($val);
			$cmd_line .= " -$k=$val";
		}
	}
	return $cmd_line;
}
