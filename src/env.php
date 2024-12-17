<?php
/**
 * Platform function related operation functions
 */
namespace LFPhp\Func;

use Exception;

/**
 * Check if the server is running on Windows
 * @return bool
 */
function server_in_windows(){
	return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
}

/**
 * Check if the server is running in HTTPS protocol
 * @return bool
 */
function server_in_https(){
	return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
}

/**
 * Get the maximum file size allowed for upload by PHP
 * Depends on: maximum upload file size, maximum POST size
 * @param bool $human_readable whether to return in readable mode
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
 * Get the maximum available socket timeout
 * @param int $ttf allowed advance time
 * @return int timeout (seconds), if 0, it means no timeout limit
 */
function get_max_socket_timeout($ttf = 0){
	$max_execute_timeout = ini_get('max_execution_time') ?: 0;
	$max_socket_timeout = ini_get('default_socket_timeout') ?: 0;
	$max = (!$max_execute_timeout || !$max_socket_timeout) ? max($max_execute_timeout, $max_socket_timeout) : min($max_execute_timeout, $max_socket_timeout);
	if($ttf && $max){
		return max($max - $ttf, 1); //Keep at least 1s, avoid 0 value
	}
	return $max;
}

/**
 * Get the client IP
 * Prioritize the defined x-forward-for proxy IP (may have certain risks)
 * @return string client IP, return an empty string if failed
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
 * Get all command line options, the format rules are consistent with getopt
 * @return array
 */
function get_all_opt(){
	$options = [];
	$args = $_SERVER['argv'];
	array_shift($args); // 移除脚本名称

	while ($arg = array_shift($args)) {
		if (substr($arg, 0, 2) === '--') {
			// Handling long options
			$option = substr($arg, 2);
			if (strpos($option, '=') !== false) {
				[$key, $value] = explode('=', $option, 2);
				$options[$key] = $value;
			} else {
				$options[$option] = true;
			}
		} elseif (substr($arg, 0, 1) === '-') {
			// Handling short options
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
			// Handling positional parameters
			$options[] = $arg;
		}
	}

	return $options;
}

/**
 * Get PHP configuration information
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
 * Console foreground color map collection
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
 * Console background color mapping collection
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
 * Generate CLI strings with colors
 * @param string $text
 * @param string $fore_color
 * @param string $back_color
 * @param bool $override Whether to overwrite the original color setting
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
 * Clean up color control characters
 * @param string $text
 * @return string
 */
function console_color_clean($text){
	return preg_replace('/\033\\[.*?m/', '', $text);
}

/**
 * Show progress bar in console
 * @param int $index
 * @param int $total
 * @param string|callable $patch_text
 * Supplementary display text, can be a closure function, all echo strings in the function will be output as progress text, if it is a closure function, due to the ob of php cli, there will be a certain delay
 * @param int $start_timestamp start timestamp, initialize the global unique timestamp in the empty function
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
		$reminds = ' ETR:'.time_get_etr($start_timestamp, $index, $total);
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
 * Loading mode outputs console string
 * @param string $patch_text Display text
 * @param string[] $loading_chars Loading character sequence, for example: ['\\', '|', '/', '-']
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
 * Run command
 * @param string $command command
 * @param array $param parameter
 * @param bool $async whether to execute asynchronously
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
 * Run commands concurrently with progress text.
 * For calling parameters, please refer to the function: run_command_parallel()
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
		show_progress($done, $total, "Command started: $command ($param_index)", $start_time, 20);
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
 * Concurrently run commands
 * @param string $command Execute command
 * @param array $param_batches Task parameter list. Parameters are passed to command as long parameters. For specific implementation, please refer to: build_command() function implementation.
 * @param array $options parameters are as follows:
 * - callable|null $on_start($param, $param_index, $start_time) returns false to interrupt execution
 * - callable|null $on_running($param, $param_index) returns false to interrupt execution
 * - callable|null $on_finish($param, $param_index, $output, $cost_time, $status_code, $error) returns false to interrupt execution
 * - int $parallel_num concurrent number, default is 20
 * - int $check_interval status check interval (unit: milliseconds), default is 100ms
 * - int $process_max_execution_time maximum process execution time (unit: milliseconds), default is not set
 * @return bool whether it ends normally
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

	//There are still tasks left, or there are tasks in progress, the program continues
	while($param_batches || $running_process_list){
		//Check process status
		foreach($running_process_list as $k => [$process, $param, $param_index, $stdout, $stderr, $start_time]){
			$status = proc_get_status($process);

			//Execution ends
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

			//Execution timeout
			if($process_max_execution_time && microtime(true) - $start_time > $process_max_execution_time){
				unset($running_process_list[$k]);
				$close_process($process, $stdout, $stderr, true);
				if($on_finish && call_user_func($on_finish, $param, $param_index, null, $process_max_execution_time, -999, "Overload maximum execution time: $process_max_execution_time") === false){
					return false;
				}
				continue;
			}

			//Executing
			if($on_running && call_user_func($on_running, $param, $param_index) === false){
				unset($running_process_list[$k]);
				$close_process($process, $stdout, $stderr, true);
				continue;
			}
		}

		//Setup new process
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
 * Build command line
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
 * Escape argv parameters under Windows
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
 * Check if the command exists
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
 * Get the network usage of Windows process
 * @param bool $include_process_info whether to include process information (title, program file name), this function requires Windows administrator mode
 * @return array format: [protocol='', local_ip='', local_port='', foreign_ip='', foreign_port='', state='', pid='', 'process_name'='', 'process_file_id'=>'']
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
 * Get port occupancy status under Linux
 * @return array format: [protocol='', local_ip='', local_port='', foreign_ip='', foreign_port='', state='', pid='', 'process_name'='', 'process_file_id'=>'']
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
 * Get the width and height of the console screen
 * @return array|null Return format: [number of columns, number of rows], if the current environment does not support it, it will return null
 */
function get_screen_size(){
	if(command_exists('tput')){
		return [
			exec('tput cols'),
			exec('tput cols'),
		];
	}
	$cols = getenv('COLUMNS');
	if($cols){
		return [getenv('COLUMNS'), getenv('ROWS')];
	}
	return null;
}

/**
 * Kill process
 * @param int $pid Process ID
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
 * Check whether the specified process is running
 * @param int $pid Process ID
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
 * Process signal monitoring
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
 * Send process semaphore
 * @param int $pid Process ID
 * @param int $sig_num semaphore
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
 * Define directory where daemon status is saved
 */
if(!defined(__NAMESPACE__.'\DAEMON_PROCESS_STATE_PATH')){
	define(__NAMESPACE__.'\DAEMON_PROCESS_STATE_PATH', sys_get_temp_dir().'/daemon_task_process');
}

/**
 * Replay the current script command
 * @return false|int Returns the newly opened Process ID, false is returned if failed
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
