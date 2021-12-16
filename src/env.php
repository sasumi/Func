<?php
namespace LFPhp\Func;

use Exception;

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
 * 获取最大socket可用超时时间
 * @param int $ttf 允许提前时长
 * @return int 超时时间（秒），如为0，表示不限制超时时间
 */
function get_max_socket_timeout($ttf = 0){
	$max_execute_timeout = ini_get('max_execution_time') ?: 0;
	$max_socket_timeout = ini_get('default_socket_timeout') ?: 0;
	$max = (!$max_execute_timeout || !$max_socket_timeout) ? max($max_execute_timeout, $max_socket_timeout) : min($max_execute_timeout, $max_socket_timeout);
	if($ttf && $max){
		return max($max - $ttf, 1); //最低保持1s，避免0值
	}
	return $max;
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

	if(preg_match_all('#(?:<h2.*?>(?:<a.*?>)?(.*?)(?:</a>)?</h2>)|'.'(?:<tr.*?><t[hd].*?>(.*?)\s*</t[hd]>(?:<t[hd].*?>(.*?)\s*</t[hd]>(?:<t[hd].*?>(.*?)\s*</t[hd]>)?)?</tr>)#s', $input, $matches, PREG_SET_ORDER)){
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
		throw new Exception('Process create fail:'.$command);
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
 * 以携带进度文本方式，并发运行命令。
 * 调用参数请参考函数：run_command_parallel()
 * @param string $command
 * @param array $param_batches
 * @param array $options
 * @return bool
 * @throws \Exception
 */
function run_command_parallel_width_progress($command, array $param_batches, array $options = []){
	$total = count($param_batches);
	$start_time = time();
	$done = 0;
	$on_finish = function($param, $param_index, $output, $cost_time, $status_code, $error) use ($command, $options, $total,$start_time, &$done){
		$done++;
		if($options['on_start']){
			return call_user_func_array($options['on_finish'], func_get_args());
		}
		return null;
	};
	$on_start = function($param, $param_index) use ($command, $options, $total, &$done, $start_time){
		show_progress($done, $total, "Command started :$command ($param_index)", $start_time, 20);
		if($options['on_start']){
			return call_user_func_array($options['on_start'], func_get_args());
		}
		return null;
	};
	$new_options = $options;
	$new_options['on_start'] = $on_start;
	$new_options['on_finish'] = $on_finish;
	return run_command_parallel($command, $param_batches, $new_options);
}

/**
 * 并发运行命令
 * @param string $command 执行命令
 * @param array $param_batches 任务参数列表，参数按照长参数方式传入command，具体实现可参考：build_command() 函数实现。
 * @param array $options 参数如下：
 * - callable|null $on_start($param, $param_index, $start_time) 返回false中断执行
 * - callable|null $on_running($param, $param_index) 返回false中断执行
 * - callable|null $on_finish($param, $param_index, $output, $cost_time, $status_code, $error) 返回false中断执行
 * - int $parallel_num 并发数量，缺省为20
 * - int $check_interval 状态检测间隔（单位：毫秒），缺省为100ms
 * - int $process_max_execution_time 进程最大执行时间（单位：毫秒），缺省为不设置
 * @return bool 是否正常结束
 */
function run_command_parallel($command, array $param_batches, array $options = []){
	$parallel_num = isset($options['parallel_num']) ? $options['parallel_num'] : 20;
	$check_interval = isset($options['check_interval']) ? $options['check_interval'] : 100;
	$on_start = isset($options['on_start']) ? $options['on_start'] : null;
	$on_finish = isset($options['on_finish']) ? $options['on_finish'] : null;
	$on_running = isset($options['on_running']) ? $options['on_running'] : null;
	$process_max_execution_time = isset($options['process_max_execution_time']) ? (int)$options['process_max_execution_time'] : 0;

	$running_process_list = [/** 格式：[process, param, param_index, stdout, stderr, start_time] **/];

	$close_process = function($process, $stdout, $stderr, $as_terminate = false){
		fclose($stdout);
		fclose($stderr);
		if($as_terminate){
			return proc_terminate($process);
		} else {
			return proc_close($process);
		}
	};

	//剩余任务，或者还有任务执行中，程序继续
	while($param_batches || $running_process_list){
		//检测进程状态
		foreach($running_process_list as $k => list($process, $param, $param_index, $stdout, $stderr, $start_time)){
			$status = proc_get_status($process);

			//执行结束
			if(!$status['running']){
				unset($running_process_list[$k]);
				$status_code = (int)$status['exitcode'];
				$output = stream_get_contents($stdout);
				$error = stream_get_contents($stderr);
				$f_code = $close_process($process, $stdout, $stderr);
				$status_code = $status_code ?: $f_code;
				if($on_finish && call_user_func($on_finish, $param, $param_index, $output, microtime(true) - $start_time, $status_code, $error) === false){
					return false;
				}
				continue;
			}

			//执行超时
			if($process_max_execution_time && microtime(true) - $start_time > $process_max_execution_time){
				unset($running_process_list[$k]);
				$close_process($process, $stdout, $stderr, true);
				if($on_finish && call_user_func($on_finish, $param, $param_index, null, $process_max_execution_time, -999, "Overload maximum execution time: $process_max_execution_time") === false){
					return false;
				}
				continue;
			}

			//运行中
			if($on_running && call_user_func($on_running, $param, $param_index) === false){
				unset($running_process_list[$k]);
				$close_process($process, $stdout, $stderr, true);
				continue;
			}
		}

		//新启进程
		if(count($running_process_list) < $parallel_num && $param_batches){
			$start_count = min($parallel_num - count($running_process_list), count($param_batches));
			while($start_count-- > 0){
				list($param, $param_index) = array_shift_assoc($param_batches);
				$cmd = build_command($command, $param);
				$start_time = microtime(true);
				$descriptors = [
					0 => ['pipe', 'r'],
					1 => ['pipe', 'w'],
					2 => ['pipe', 'w'],
				];
				if($on_start && call_user_func($on_start, $param, $param_index, $start_time) === false){
					return false;
				}
				$process = proc_open($cmd, $descriptors, $pipes, null, null, ['bypass_shell' => true]);
				if(!$process){
					throw new Exception("Create new process failed: $cmd");
				}

				stream_set_blocking($pipes[0], 0);
				stream_set_blocking($pipes[1], 0);
				list($stdin, $stdout, $stderr) = $pipes;
				$running_process_list[] = [$process, $param, $param_index, $stdout, $stderr, $start_time];
				fclose($stdin);
			}
		}
		usleep($check_interval);
	}
	return true;
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
		}else if(strlen($k) > 0){
			$val = escapeshellarg($val);
			$cmd_line .= " --$k=$val";
		}else{
			$val = escapeshellarg($val);
			$cmd_line .= " -$k=$val";
		}
	}
	return $cmd_line;
}

/**
 * 检查命令是否存在
 * @param string $command
 * @return bool
 */
function command_exists($command){
	$where_is_command = server_in_windows() ? 'where' : 'which';
	$process = proc_open("$where_is_command $command", array(
		0 => array("pipe", "r"), //STDIN
		1 => array("pipe", "w"), //STDOUT
		2 => array("pipe", "w"), //STDERR
	), $pipes);
	if($process !== false){
		$stdout = stream_get_contents($pipes[1]);
//		$stderr = stream_get_contents($pipes[2]);
		fclose($pipes[1]);
		fclose($pipes[2]);
		proc_close($process);
		return $stdout != '';
	}
	return false;
}

/**
 * 获取控制台屏幕宽度及高度
 * @return array|null 返回格式：[列数，行数】，当前环境不支持则返回 null
 */
function get_screen_size(){
	if(command_exists('tput')){
		return [
			exec('tput cols'),
			exec('tput cols'),
		];
	}
	$cols = getenv('COLUMNS');
	if(isset($cols)){
		return [getenv('COLUMNS'), getenv('ROWS')];
	}
	return null;
}