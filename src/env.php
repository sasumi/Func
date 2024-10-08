<?php
/**
 * 平台函数相关操作函数
 */
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
 * 检测服务器是否在HTTPS协议中运行
 * @return bool
 */
function server_in_https(){
	return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
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
 * 获取客户端IP
 * 优先获取定义的 x-forward-for 代理IP（可能有一定风险）
 * @return string 客户端IP，获取失败返回空字符串
 */
function get_client_ip(){
	$ip = '';
	if(getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")){
		$ip = getenv("HTTP_CLIENT_IP");
	}else if(getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")){
		$ip = getenv("HTTP_X_FORWARDED_FOR");
	}else if(getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")){
		$ip = getenv("REMOTE_ADDR");
	}else if(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")){
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}

/**
 * 获取所有命令行选项，格式规则与 getopt 一致
 * @return array
 */
function get_all_opt(){
	$options = [];
	$args = $_SERVER['argv'];
	array_shift($args); // 移除脚本名称

	while ($arg = array_shift($args)) {
		if (substr($arg, 0, 2) === '--') {
			// 处理长选项
			$option = substr($arg, 2);
			if (strpos($option, '=') !== false) {
				[$key, $value] = explode('=', $option, 2);
				$options[$key] = $value;
			} else {
				$options[$option] = true;
			}
		} elseif (substr($arg, 0, 1) === '-') {
			// 处理短选项
			$option = substr($arg, 1);
			if (strlen($option) > 1) {
				foreach (str_split($option) as $char) {
					$options[$char] = true;
				}
			} else {
				$nextArg = array_shift($args);
				if ($nextArg && substr($nextArg, 0, 1) !== '-') {
					$options[$option] = $nextArg;
				} else {
					$options[$option] = true;
					array_unshift($args, $nextArg);
				}
			}
		} else {
			// 处理位置参数
			$options[] = $arg;
		}
	}

	return $options;
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
 * 控制台前景色映射集合
 */
const CONSOLE_FOREGROUND_COLOR_MAP = [
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
];

/**
 * 控制台背景色映射集合
 */
const CONSOLE_BACKGROUND_COLOR_MAP = [
	'black'      => '40',
	'red'        => '41',
	'green'      => '42',
	'yellow'     => '43',
	'blue'       => '44',
	'magenta'    => '45',
	'cyan'       => '46',
	'light_gray' => '47',
];

/**
 * 生成携带颜色的CLI字符串
 * @param string $text
 * @param string $fore_color
 * @param string $back_color
 * @param bool $override 是否覆盖原来的颜色设置
 * @return string
 */
function console_color($text, $fore_color = '', $back_color = '', $override = false){
	if(preg_match("/\033\\[0m/", $text)){
		if(!$override){
			return $text;
		} else {
			$text = console_color_clean($text);
		}
	}

	$color_prefix = '';
	if($fore_color){
		$color_prefix .= "\033[".CONSOLE_FOREGROUND_COLOR_MAP[$fore_color]."m";
	}
	if($back_color){
		$color_prefix .= "\033[".CONSOLE_BACKGROUND_COLOR_MAP[$back_color]."m";
	}
	if($color_prefix){
		return $color_prefix.$text."\033[0m";
	}
	return $text;
}

/**
 * 清理颜色控制字符
 * @param string $text
 * @return string
 */
function console_color_clean($text){
	return preg_replace('/\033\\[.*?m/', '', $text);
}

/**
 * 显示进度条
 * @param int $index
 * @param int $total
 * @param string|callable $patch_text
 * 补充显示文本，可以是一个闭包函数，函数内所有echo字符串均会被当成进度文本输出，如果是闭包函数由于php cli的ob会造成一定的延时
 * @param int $start_timestamp 开始时间戳，为空函数内初始化全局唯一一个时间戳
 */
function show_progress($index, $total, $patch_text = '', $start_timestamp = null){
	if(is_callable($patch_text)){
		try {
			ob_start();
			call_user_func($patch_text);
			$patch_text = ob_get_clean();
		} catch(Exception $e){
			$patch_text = $e->getMessage();
		}
	}
	$patch_text = preg_replace("/[\n\r]/", '', trim($patch_text));
	$pc = str_pad(round(100*$index/$total), 2, ' ', STR_PAD_LEFT);
	$progress_length = 10;
	$reminds = '';
	if(!$start_timestamp){
		static $inner_start_time;
		if(!$inner_start_time){
			$inner_start_time = time();
		}
		$start_timestamp = $inner_start_time;
	}
	if($index){
		$reminds = ' ETA:'.time_get_eta($start_timestamp, $index, $total);
	}
	$fin_chars = round(($index/$total)*$progress_length);
	$left_chars = $progress_length - $fin_chars;

	$str = "\r\r".str_pad($index.'', strlen($total.''), '0', STR_PAD_LEFT)."/$total $pc% ".str_repeat('█', $fin_chars).str_repeat('▒', $left_chars)."$reminds $patch_text";
	[$colum] = get_screen_size();
	if($colum){
		$left_space = $colum - mb_strwidth($str) - 1;
		if($left_space > 0){
			$str .= str_repeat(' ', $left_space);
		}
		$str = mb_strimwidth($str, 0, $colum);
	}else{
		$str = str_pad($str, strlen($str) + 20, ' ', STR_PAD_RIGHT);
	}
	echo $str;
	if($index >= $total){
		echo PHP_EOL;
	}
}

/**
 * loading 方式输出控制台字符串
 * @param string $patch_text 显示文本
 * @param string[] $loading_chars loading字符序列，例如可以为：['\\', '|', '/', '-']
 * @return void
 */
function show_loading($patch_text, $loading_chars = ''){
	if($patch_text){
		$patch_text = preg_replace("/[\n\r]/", '', trim($patch_text));
	}
	$loading_chars = $loading_chars ?: ["⠙", "⠘", "⠰", "⠴", "⠤", "⠦", "⠆", "⠃", "⠋", "⠉"];
	global $__last_loading_chart_idx;
	[$colum] = get_screen_size();
	if($colum){
		$patch_text = mb_strimwidth($patch_text, 0, $colum-5);
	}
	echo "\r\r".$loading_chars[$__last_loading_chart_idx++]." ".$patch_text;
	$__last_loading_chart_idx = ($__last_loading_chart_idx == count($loading_chars) ? 0 : $__last_loading_chart_idx);
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

	if(!function_exists('proc_open') || !function_exists('proc_close') || !function_exists('fgets')){
		throw new Exception('run_command required: proc_open, proc_close, fgets function.');
	}

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
			$error_str .= $e;
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
	$on_finish = function($param, $param_index, $output, $cost_time, $status_code, $error) use ($command, $options, $total, $start_time, &$done){
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
 * @throws \Exception
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
		}else{
			return proc_close($process);
		}
	};

	//剩余任务，或者还有任务执行中，程序继续
	while($param_batches || $running_process_list){
		//检测进程状态
		foreach($running_process_list as $k => [$process, $param, $param_index, $stdout, $stderr, $start_time]){
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
				[$param, $param_index] = array_shift_assoc($param_batches);
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
				[$stdin, $stdout, $stderr] = $pipes;
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
 * @param string $cmd_line
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
 * 转义window下argv参数
 * @param string|int $value
 * @return string
 * @throws \Exception
 */
function escape_win32_argv($value){
	static $expr = '(
        [\x00-\x20\x7F"] # control chars, whitespace or double quote
      | \\\\++ (?=("|$)) # backslashes followed by a quote or at the end
    )ux';

	if($value === ''){
		return '""';
	}

	$quote = false;
	$replacer = function($match) use ($value, &$quote){
		switch($match[0][0]){ // only inspect the first byte of the match
			case '"': // double quotes are escaped and must be quoted
				$match[0] = '\\"';
			case ' ':
			case "\t": // spaces and tabs are ok but must be quoted
				$quote = true;
				return $match[0];

			case '\\': // matching backslashes are escaped if quoted
				return $match[0].$match[0];

			default:
				throw new Exception(sprintf("Invalid byte at offset %d: 0x%02X", strpos($value, $match[0]), ord($match[0])));
		}
	};

	$escaped = preg_replace_callback($expr, $replacer, (string)$value);

	if($escaped === null){
		throw preg_last_error() === PREG_BAD_UTF8_ERROR ? new Exception("Invalid UTF-8 string") : new Exception("PCRE error: ".preg_last_error());
	}

	return $quote // only quote when needed
		? '"'.$escaped.'"' : $value;
}

/**
 * Escape cmd.exe metacharacters with ^
 * @param $value
 * @return string|string[]|null
 */
function escape_win32_cmd($value){
	return preg_replace('([()%!^"<>&|])', '^$0', $value);
}

/**
 * Like shell_exec() but bypass cmd.exe
 * @param string $command
 * @return false|string
 */
function noshell_exec($command){
	static $descriptors = [['pipe', 'r'], ['pipe', 'w'], ['pipe', 'w']], $options = ['bypass_shell' => true];

	if(!$proc = proc_open($command, $descriptors, $pipes, null, null, $options)){
		throw new Exception('Creating child process failed');
	}

	fclose($pipes[0]);
	$result = stream_get_contents($pipes[1]);
	fclose($pipes[1]);
	stream_get_contents($pipes[2]);
	fclose($pipes[2]);
	proc_close($proc);

	return $result;
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
 * 获取Windows进程网络占用情况
 * @param bool $include_process_info 是否包含进程信息（标题、程序文件名），该功能需要Windows管理员模式
 * @return array 格式:[protocol='', local_ip='', local_port='', foreign_ip='', foreign_port='', state='', pid='', 'process_name'='', 'process_file_id'=>'']
 * @throws \Exception
 */
function windows_get_port_usage($include_process_info = false){
	if(!server_in_windows()){
		throw new Exception(__FUNCTION__.'() only run in windows server');
	}
	$str = run_command('netstat -ano'.($include_process_info ? 'b' : ''));
	$str = preg_replace("/.*\s{2}PID\n/s", '', trim(str_replace("\r", '', ($str))));
	$rows = explode_by("\n", $str);
	$ret = [];

	$patch_last_process_info = function($ret, $process_name, $process_file_id){
		for($i = count($ret) - 1; $i >= 0; $i--){
			if(isset($ret[$i]['process_name'])){
				break;
			}
			$ret[$i]['process_name'] = $process_name;
			$ret[$i]['process_file_id'] = $process_file_id;
		}
		return $ret;
	};

	$match_process_info = function($r){
		if(preg_match("/^\[([^]]+)]$/", $r, $id_ms)){
			return [null, $id_ms[1]];
		}
		if(preg_match("/^(\S+)$/", $r, $name_ms)){
			return [$name_ms[1], null];
		}
		return [];
	};

	for($row_idx = 0; $row_idx < count($rows); $row_idx++){
		$r = trim($rows[$row_idx]);
		if(!$r){
			continue;
		}
		if(preg_match("/([\S]+)\s+([\S]+)\s+([\S]+)\s+([\S]+)\s+([\S]+)/", $r, $matches)){
			$ret[] = [
				'protocol'     => $matches[1],
				'local_ip'     => substr($matches[2], 0, strrpos($matches[2], ':', -1)),
				'local_port'   => substr($matches[2], strrpos($matches[2], ':', -1) + 1),
				'foreign_ip'   => substr($matches[3], 0, strrpos($matches[3], ':', -1)),
				'foreign_port' => substr($matches[3], strrpos($matches[3], ':', -1) + 1),
				'state'        => $matches[4],
				'pid'          => $matches[5],
			];
			continue;
		}
		if(preg_match("/([\S]+)\s+([\S]+)\s+([\S]+)\s+([\d]+)/", $r, $matches)){
			$ret[] = [
				'protocol'     => $matches[1],
				'local_ip'     => substr($matches[2], 0, strrpos($matches[2], ':', -1)),
				'local_port'   => substr($matches[2], strrpos($matches[2], ':', -1) + 1),
				'foreign_ip'   => substr($matches[3], 0, strrpos($matches[3], ':', -1)),
				'foreign_port' => substr($matches[3], strrpos($matches[3], ':', -1) + 1),
				'state'        => null,
				'pid'          => $matches[4],
			];
		}

		//process info mode
		if($include_process_info && $current_ms = $match_process_info($r)){
			$next_row_ms = $match_process_info(trim($r[$row_idx + 1]));
			$ret = $patch_last_process_info($ret, $current_ms[0] ?: $next_row_ms[0], $current_ms[1] ?: $next_row_ms[1]);
			if($next_row_ms){
				$row_idx++;
			}
			continue;
		}
	}
	return $ret;
}

/**
 * 获取Linux下端口占用情况
 * @return array 格式:[protocol='', local_ip='', local_port='', foreign_ip='', foreign_port='', state='', pid='', 'process_name'='', 'process_file_id'=>'']
 * @throws \Exception
 */
function unix_get_port_usage(){
	if(server_in_windows()){
		throw new Exception(__FUNCTION__.'() only run in *nix server');
	}
	$str = run_command('netstat -anop');
	$rows = explode_by("\n", $str);
	$ret = [];
	$rows = array_slice($rows, 2); //remove header lines
	foreach($rows as $row){
		$row = trim($row);
		//               Pro     RecV     Send    LAddr   FAdd    State    PID/P tail
		if(preg_match("/^(\S+)\s+(\d+)\s+(\d+)\s+(\S+)\s+(\S+)\s+(\w+)\s+(\S+).*$/", $row, $matches)){
			$pid = $process_name = $process_file_id = null;
			if(strpos($matches[7], '/')){
				[$pid, $pif] = explode('/', $matches[7]);
				[$process_file_id] = explode(':', $pif);
				$process_name = $process_file_id;
			}
			$ret[] = [
				'protocol'        => $matches[1],
				'local_ip'        => substr($matches[4], 0, strrpos($matches[4], ':', -1)),
				'local_port'      => substr($matches[4], strrpos($matches[4], ':', -1) + 1),
				'foreign_ip'      => substr($matches[5], 0, strrpos($matches[5], ':', -1)),
				'foreign_port'    => substr($matches[5], strrpos($matches[5], ':', -1) + 1),
				'state'           => $matches[6],
				'pid'             => $pid,
				'process_file_id' => $process_file_id,
				'process_name'    => $process_name,
			];
		}//不包含State格式:     Pro     RecV     Send    LAddr   FAdd    PID/P tail
		elseif(preg_match("/^(\S+)\s+(\d+)\s+(\d+)\s+(\S+)\s+(\S+)\s+(\S+).*$/", $row, $matches)){
			$pid = $process_name = $process_file_id = null;
			if(strpos($matches[6], '/')){
				[$pid, $pif] = explode('/', $matches[6]);
				[$process_file_id] = explode(':', $pif);
				$process_name = $process_file_id;
			}
			$ret[] = [
				'protocol'        => $matches[1],
				'local_ip'        => substr($matches[4], 0, strrpos($matches[4], ':', -1)),
				'local_port'      => substr($matches[4], strrpos($matches[4], ':', -1) + 1),
				'foreign_ip'      => substr($matches[5], 0, strrpos($matches[5], ':', -1)),
				'foreign_port'    => substr($matches[5], strrpos($matches[5], ':', -1) + 1),
				'state'           => null,
				'pid'             => $pid,
				'process_file_id' => $process_file_id,
				'process_name'    => $process_name,
			];
		}else{
			break;
		}
	}
	return $ret;
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

/**
 * 杀死进程
 * @param int $pid 进程ID
 * @return bool
 */
function process_kill($pid){
	if(function_exists("posix_kill")){
		return posix_kill($pid, 9);
	}
	if(server_in_windows()){
		exec("taskkill /PID $pid", $junk, $return_code);
	}else{
		exec("kill -s 9 $pid 2>&1", $junk, $return_code);
	}
	return !$return_code;
}

/**
 * 检测指定进程是否运行中
 * @param int $pid 进程ID
 * @return bool
 */
function process_running($pid){
	if(server_in_windows()){
		$out = [];
		exec("TASKLIST /FO LIST /FI \"PID eq $pid\"", $out);
		return count($out) > 1;
	}
	elseif(function_exists("posix_kill")){
		return posix_kill($pid, 0);
	}
	return false;
}

/**
 * 进程信号监听
 * @param $signal
 * @param $handle
 * @return bool
 * @throws \Exception
 */
function process_signal($signal, $handle){
	if(!function_exists('pcntl_signal')){
		throw new Exception('pcntl_signal function no supported, ext-_signal required.');
	}
	return pcntl_signal($signal, $handle);
}

/**
 * 发送进程信号量
 * @param int $pid 进程ID
 * @param int $sig_num 信号量
 * @return bool
 */
function process_send_signal($pid, $sig_num){
	if(function_exists("posix_kill")){
		return posix_kill($pid, $sig_num);
	}
	exec("/usr/bin/kill -s $sig_num $pid 2>&1", $junk, $return_code);
	return !$return_code;
}

/**
 * 守护进程状态保存目录
 */
if(!defined(__NAMESPACE__.'\DAEMON_PROCESS_STATE_PATH')){
	define(__NAMESPACE__.'\DAEMON_PROCESS_STATE_PATH', sys_get_temp_dir().'/daemon_task_process');
}

/**
 * 重播当前脚本命令
 * @return false|int 返回新开启的进程ID，false 为失败返回
 */
function replay_current_script(){
	$cmd = "php ".join(' ', $_SERVER['argv']);
	$descriptors_pec = array(
		0 => array("pipe", "r"),
		1 => array("pipe", "w"),
		2 => array("pipe", "w"),
	);
	$process = proc_open($cmd, $descriptors_pec, $pipes, realpath('./'), $_SERVER);
	if(is_resource($process)){
		$status = proc_get_status($process);
		return $status['pid'];
	}
	return false;
}
