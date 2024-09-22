<?php
/**
 * CURL网络请求相关操作函数
 * curl相关处理函数返回标准结构：[info=>['url'='', ...], error='', head=>'', body=>'']
 */
namespace LFPhp\Func;

use Exception;

/**
 * CURL 请求全局默认参数，可以通过 curl_get_default_option() 和 curl_set_default_option() 进行操作
 */
const CURL_DEFAULT_OPTION_GLOBAL_KEY = __NAMESPACE__.'/curl_default_option';
$GLOBALS[CURL_DEFAULT_OPTION_GLOBAL_KEY] = [
	CURLOPT_RETURNTRANSFER => true, //返回内容部分
	CURLOPT_HEADER         => true, //发送头部信息
	CURLOPT_USERAGENT      => $_SERVER['HTTP_USER_AGENT'], //缺省使用请求UA，如果是CLI模式，这里为空
	CURLOPT_FOLLOWLOCATION => true, //跟随服务端响应的跳转
	CURLOPT_MAXREDIRS      => 5, //最大跳转次数，跳转次数过多，可能会出现过度性能消耗
	CURLOPT_ENCODING       => 'gzip, deflate', //缺省使用 gzip 传输
	CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1, //默认使用 HTTP1.1 版本
	CURLOPT_TIMEOUT        => 10, //默认超时时间 10s
];

/**
 * 额外支持控制选项，使用当前 __NAMESPACE__ 作为前缀，会在curl初始化参数时去掉
 */
//响应页面部分编码做自动转换为UTF-8，可以设置为指定编码（如gbk, gb2312）或 ''(表示自动识别)。不设置该选项，或设置为NULL，CURL不进行页面编码转换
const CURLOPT_PAGE_ENCODING = __NAMESPACE__.'/CURL_PAGE_ENCODING';

//设置自动写入、读取cookie文件（跟随cookie文件）
const CURLOPT_FOLLOWING_COOKIE_FILE = __NAMESPACE__.'/CURLOPT_FOLLOWING_COOKIE_FILE';

//自动修复html中相对路径
const CURLOPT_HTML_FIX_RELATIVE_PATH = __NAMESPACE__.'/CURLOPT_HTML_FIX_RELATIVE_PATH';

/**
 * CURL GET请求
 * @param string $url
 * @param mixed|null $data
 * @param array $curl_option 额外CURL选项
 * @return array [info=>[], head=>'', body=>'', ...] curl_getinfo信息
 * @throws \Exception
 */
function curl_get($url, $data = null, array $curl_option = []){
	if($data){
		$url .= (strpos($url, '?') !== false ? '&' : '?').curl_data2str($data);
	}
	return curl_query($url, $curl_option);
}

/**
 * POST请求
 * @param string $url
 * @param mixed|null $data
 * @param array $curl_option
 * @return array
 * @throws \Exception
 */
function curl_post($url, $data = null, array $curl_option = []){
	return curl_query($url, array_merge_assoc($curl_option, [
		CURLOPT_POST       => true,
		CURLOPT_POSTFIELDS => is_string($data) ? $data : http_build_query($data),
	]));
}

/**
 * JSON方式发送POST请求
 * @param string $url
 * @param mixed $data
 * @param array $curl_option
 * @return array
 * @throws \Exception
 */
function curl_post_json($url, array $data = [], array $curl_option = []){
	$data = $data ? json_encode($data) : '';
	return curl_post($url, $data, array_merge_assoc([
		CURLOPT_HTTPHEADER => [
			'Content-Type: application/json; charset=utf-8',
			'Content-Length: '.strlen($data),
		],
	], $curl_option));
}

/**
 * curl post 提交文件
 * @param string $url
 * @param array $file_map [filename=>filepath,...]
 * @param mixed $ext_param 同时提交的其他post参数
 * @param array $curl_option curl选项
 * @return array curl_query返回结果，包含 [info=>[], head=>'', body=>''] 信息
 * @throws \Exception
 */
function curl_post_file($url, array $file_map, array $ext_param = [], array $curl_option = []){
	foreach($file_map as $name => $file){
		if(!is_file($file)){
			throw new Exception('file no found:'.$file);
		}
		$ext_param[$name] = curl_file_create($file);
	}
	return curl_query($url, array_merge_assoc([
		CURLOPT_POST       => true,
		CURLOPT_POSTFIELDS => $ext_param,
	], $curl_option));
}

/**
 * PUT请求
 * @param string $url
 * @param array $data
 * @param array $curl_option
 * @return array
 * @throws \Exception
 */
function curl_put($url, $data, array $curl_option = []){
	return curl_query($url, array_merge_assoc([
		CURLOPT_POSTFIELDS    => curl_data2str($data),
		CURLOPT_CUSTOMREQUEST => 'PUT',
	], $curl_option));
}

/**
 * DELETE请求
 * @param string $url
 * @param array $data
 * @param array $curl_option
 * @return array
 * @throws \Exception
 */
function curl_delete($url, $data, array $curl_option = []){
	return curl_query($url, array_merge_assoc([
		CURLOPT_POSTFIELDS    => curl_data2str($data),
		CURLOPT_CUSTOMREQUEST => 'DELETE',
	], $curl_option));
}

/**
 * 快速执行curl查询，并关闭curl连接
 * @param string $url
 * @param array $curl_option
 * @return array [info=>[], error='', head=>'', body=>'']
 * @throws \Exception
 */
function curl_query($url, array $curl_option){
	[$ch, $curl_option] = curl_instance($url, $curl_option);
	$raw_string = curl_exec($ch);

	$ret = [];
	$ret['info'] = curl_getinfo($ch);
	$error = curl_error($ch);
	if($error){
		$errno = curl_errno($ch);
		$ret['error'] = "Curl Error($errno) $error";
	}else{
		[$ret['head'], $ret['body']] = curl_cut_raw($ch, $raw_string);
		if(isset($curl_option[CURLOPT_PAGE_ENCODING])){
			$ret['body'] = mb_convert_encoding($ret['body'], 'utf8', $curl_option[CURLOPT_PAGE_ENCODING]);
		}

		if($curl_option[CURLOPT_HTML_FIX_RELATIVE_PATH]){
			//这里使用最后实际URL作为替换标准
			$ret['body'] = html_fix_relative_path($ret['body'], $ret['info']['url']);
		}
	}

	curl_close($ch);
	return $ret;
}

/**
 * CURL HTTP Header追加额外信息，如果原来已经存在，则会被替换
 * @param array $curl_option
 * @param string $header_name
 * @param string $header_value
 * @return void
 */
function curl_patch_header(&$curl_option, $header_name, $header_value){
	if(!$curl_option[CURLOPT_HTTPHEADER]){
		$curl_option[CURLOPT_HTTPHEADER] = [];
	}
	foreach($curl_option[CURLOPT_HTTPHEADER] as $k => $item){
		if(strcasecmp($item, $header_name) === 0){
			$curl_option[CURLOPT_HTTPHEADER][$k] = $header_value;
			break;
		}
	}
	$curl_option[CURLOPT_HTTPHEADER][] = "$header_name: $header_value";
}

/**
 * 搭建 CURL 命令
 * @param string $url
 * @param string $body_str
 * @param string $method
 * @param string[] $headers 头部信息，格式为 ['Content-Type: application/json'] 或 ['Content-Type‘=>'application/json']
 * @return string
 */
function curl_build_command($url, $body_str, $method, $headers, $multiple_line = true){
	$line_sep = $multiple_line ? '\\'.PHP_EOL : '';
	$method = strtoupper($method);
	if($method === HTTP_METHOD_GET && $body_str){
		$url .= (stripos($url, '?') !== false ? '&' : '?').$body_str;
	}
	$cmd = "curl -L -X $method '$url'".$line_sep;
	foreach($headers as $name => $value){
		if(is_numeric($name)){
			$cmd .= " -H '$value'".$line_sep;
		}else{
			$cmd .= " -H '$name: $value'".$line_sep;
		}
	}
	if($body_str && $method === HTTP_METHOD_POST){
		$body_str = addcslashes($body_str, "'");
		$cmd .= " --data-raw '$body_str'";
	}
	return $cmd;
}

/**
 * 解析代理字符串，生成CURL选项
 * @param string $proxy_string
 * 代理字符串，格式如：
 * http://hostname:port
 * http://username:password@hostname:port
 * https://hostname:port (converted to http://)
 * https://username:password@hostname:port (converted to http://)
 * socks4://hostname:port
 * socks4://username:password@hostname:port
 * socks5://hostname:port
 * socks5://username:password@hostname:port
 * @return array
 * @throws \Exception
 */
function curl_get_proxy_option($proxy_string){
	$type = 'http'; //默认使用 http 协议
	$account = '';
	$password = '';
	$CURL_TYPE_MAP = [
		'http'    => CURLPROXY_HTTP,
		'https'   => CURLPROXY_HTTP, //port 443
		'socks4'  => CURLPROXY_SOCKS4,
		'socks5'  => CURLPROXY_SOCKS5,
		'socks4a' => CURLPROXY_SOCKS4A,
	];
	if(preg_match('/^(\w+):\/\//', $proxy_string, $matches)){
		$type = strtolower($matches[1]);
		$proxy_string = preg_replace('/^\w+:\/\//', '', $proxy_string);
	}
	if(!isset($CURL_TYPE_MAP[$type])){
		throw new Exception('Proxy type no supported:'.$type);
	}
	if(preg_match('/^(.*?)@/', $proxy_string, $matches)){
		[$account, $password] = explode(':', $matches[1]);
		$proxy_string = preg_replace('/.*?@/', '', $proxy_string);
	}
	[$host, $port] = explode(':', $proxy_string);

	//https缺省使用443端口
	if($type === 'https' && !$port){
		$port = 443;
	}
	$curl_option = [
		CURLOPT_PROXY     => $host,
		CURLOPT_PROXYTYPE => $CURL_TYPE_MAP[$type],
	];
	if($port){
		$curl_option[CURLOPT_PROXYPORT] = (int)$port;
	}
	if($account){
		$curl_option[CURLOPT_PROXYUSERPWD] = $account.($password ? ':'.$password : '');
	}
	return $curl_option;
}

/**
 * 获取CURL默认选项
 * @return array
 */
function curl_get_default_option($ext_option = []){
	return array_merge_assoc($GLOBALS[CURL_DEFAULT_OPTION_GLOBAL_KEY], $ext_option);
}

/**
 * 设置 curl_* 操作默认选项
 * @param array $curl_option
 * @param bool $patch 是否以追加方式添加，默认为覆盖
 * @return array
 */
function curl_set_default_option(array $curl_option, $patch = false){
	$default = $patch ? curl_get_default_option() : [];
	$GLOBALS[CURL_DEFAULT_OPTION_GLOBAL_KEY] = array_merge_assoc($default, $curl_option);
	return $GLOBALS[CURL_DEFAULT_OPTION_GLOBAL_KEY];
}

/**
 * 获取CURL实例对象
 * @param string $url
 * @param array $ext_curl_option CURL选项，会通过 curl_default_option() 添加额外默认选项
 * @return array(resource, $curl_option)
 * @throws \Exception
 */
function curl_instance($url, array $ext_curl_option = []){
	$ch = curl_init();
	$curl_option = curl_get_default_option($ext_curl_option);
	$curl_option[CURLOPT_URL] = $url ?: $curl_option[CURLOPT_URL];

	//补充支持 https:// 协议
	if(stripos($curl_option[CURLOPT_URL], 'https://') === 0){
		$curl_option[CURLOPT_SSL_VERIFYPEER] = 0;
		$curl_option[CURLOPT_SSL_VERIFYHOST] = 1;
	}

	//修正HTTP头部，如果传入的是key => value数组，转换成字符串数组
	if($curl_option[CURLOPT_HTTPHEADER] && is_assoc_array($curl_option[CURLOPT_HTTPHEADER])){
		$tmp = [];
		foreach($curl_option[CURLOPT_HTTPHEADER] as $field => $val){
			$tmp[] = "$field: $val";
		}
		$curl_option[CURLOPT_HTTPHEADER] = $tmp;
	}

	//设置缺省参数
	if($curl_option[CURLOPT_FOLLOWING_COOKIE_FILE]){
		$curl_option[CURLOPT_COOKIEJAR] = $curl_option[CURLOPT_FOLLOWING_COOKIE_FILE]; //连接结束后保存cookie信息的文件。
		$curl_option[CURLOPT_COOKIEFILE] = $curl_option[CURLOPT_FOLLOWING_COOKIE_FILE]; //包含cookie数据的文件名，cookie文件的格式可以是Netscape格式，或者只是纯HTTP头部信息存入文件。
	}

	if($curl_option[CURLOPT_TIMEOUT] && get_max_socket_timeout() < $curl_option[CURLOPT_TIMEOUT]){
		//warning timeout setting no taking effect
		error_log('warning timeout setting no taking effect');
	}

	//忽略自定义选项
	foreach($curl_option as $k => $item){
		if(strpos($k, __NAMESPACE__) === false){
			curl_setopt($ch, $k, $item);
		}
	}
	if(!$ch){
		throw new Exception('Curl init fail');
	}
	return [$ch, $curl_option];
}

/**
 * convert data to request string
 * @param mixed $data
 * @return string
 * @throws \Exception
 */
function curl_data2str($data){
	if(is_scalar($data)){
		return (string)$data;
	}
	if(is_array($data)){
		$d = [];
		if(is_assoc_array($data)){
			foreach($data as $k => $v){
				if(is_null($v)){
					continue;
				}
				if(is_scalar($v)){
					$d[] = urlencode($k).'='.urlencode($v);
				}else{
					throw new Exception('Data type no support(more than 3 dimension array no supported)');
				}
			}
		}else{
			$d += $data;
		}
		return join('&', $d);
	}
	throw new Exception('Data type no supported');
}

/**
 * 打印CURL选项
 * @param array $options
 * @param bool $as_return
 * @return array|null
 */
function curl_print_option($options, $as_return = false){
	static $all_const_list;
	if(!$all_const_list){
		$all_const_list = get_defined_constants();
	}
	$prints = [];
	foreach($all_const_list as $text => $v){
		if(stripos($text, 'CURLOPT_') === 0 && isset($options[$v])){
			$prints[$text] = $options[$v];
		}
	}
	if(!$as_return){
		var_export_min($prints);
		return null;
	}else{
		return $prints;
	}
}

/**
 * 转换CURL选项到标准HTTP头信息
 * @param array $options
 * @return string[] array
 */
function curl_option_to_request_header($options){
	$headers = [];
	$simple_mapping = [
		CURLOPT_USERAGENT       => 'User-Agent',
		CURLOPT_ACCEPT_ENCODING => 'Accept-Encoding',
		CURLOPT_COOKIE          => 'Cookie',
	];
	$http_version_mapping = [
		CURL_HTTP_VERSION_1_0 => 'HTTP/1.0',
		CURL_HTTP_VERSION_1_1 => 'HTTP/1.1',
		CURL_HTTP_VERSION_2_0 => 'HTTP/2.0',
	];
	foreach($options as $opt => $mix_values){
		switch($opt){
			case $simple_mapping[$opt]:
				$headers[$simple_mapping[$opt]] = $mix_values;
				break;
			case CURLOPT_URL:
				$url_info = parse_url($options[CURLOPT_URL]);
				$headers['Host'] = $url_info['host'];
				$headers['Origin'] = $url_info['scheme']."://".$url_info['host'];
				break;
			case CURLOPT_POST:
				$headers['Content-Type'] = 'application/x-www-form-urlencoded';
				break;
			case CURLOPT_HTTPHEADER:
				foreach($mix_values as $mv){
					[$k, $v] = explode_by(':', $mv);
					$headers[$k] = $v;
				}
				break;
			case CURLOPT_POSTFIELDS:
				$headers['Content-Length'] = strlen($mix_values);
				break;
			case CURLOPT_HTTP_VERSION:
				$http_ver = $http_version_mapping[$mix_values];
				$headers['http'] = $http_ver ?: null;
				break;
			default:
				break;
		}
	}
	return $headers;
}

/**
 * 请求链接转换成闭包函数
 * @param string[]|array[] $urls 请求链接数组
 * @param array $ext_curl_option CURL选项数组
 * @return \Closure
 */
function curl_urls_to_fetcher($urls, $ext_curl_option = []){
	$options = [];
	//一维数组
	if(count($urls) === count($urls, true)){
		foreach($urls as $url){
			$ext_curl_option[CURLOPT_URL] = $url;
			$options[] = $ext_curl_option;
		}
	}//二维数组，当作CURL OPTION处理
	else{
		foreach($urls as $opt){
			$options[] = array_merge_assoc($opt, $ext_curl_option);
		}
	}
	return function() use (&$options){
		return array_shift($options);
	};
}

/**
 * 切割CURL结果字符串
 * @param resource $ch
 * @param string $raw_string
 * @return string[] head,body
 */
function curl_cut_raw($ch, $raw_string){
	if(!$raw_string){
		return ['', ''];
	}
	$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	$head = substr($raw_string, 0, $header_size);
	$body = substr($raw_string, $header_size);
	return [$head, $body];
}

/**
 * 判断 curl_query 是否成功
 * @param array $query_result curl_query返回标准结构
 * @param string $error
 * @param bool $allow_empty_body 允许body为空
 * @return bool
 */
function curl_query_success($query_result, &$error = '', $allow_empty_body = false){
	if($query_result['error']){
		$error = $query_result['error'];
	}
	if(!$error && $query_result['info']['http_code'] != 200){
		$error = 'http code error:'.$query_result['info']['http_code'];
	}
	if(!$error && !$allow_empty_body && !strlen($query_result['body'])){
		$error = 'body empty';
	}
	return !$error;
}

/**
 * 从 curl_query 结果中解析json对象
 * @param array $query_result curl_query返回标准结构
 * @param mixed $ret 返回结果
 * @param string $error 错误信息
 * @param bool $force_array
 * @return bool 是否成功
 * @example
 * if(!curl_query_json_success($ret, $data, $error)){
 *   die($error);
 * }
 * $msg = array_get($data, 'message');
 */
function curl_query_json_success($query_result, &$ret = null, &$error = '', $force_array = true){
	if(!curl_query_success($query_result, $error)){
		return false;
	}
	$tmp = @json_decode($query_result['body'], true);
	$error = json_last_error();
	if($error){
		return false;
	}
	if($force_array && !is_array($tmp)){
		$error = 'return format error:'.gettype($tmp);
		return false;
	}
	$ret = $tmp;
	return true;
}

/**
 * CURL 并发请求
 * 注意：回调函数需尽快处理避免阻塞后续请求流程
 * @param callable|array $curl_option_fetcher : array 返回CURL选项映射数组，即使只有一个url，也需要返回 [CURLOPT_URL=>$url]
 * @param callable|null $on_item_start ($curl_option) 开始执行回调，如果返回false，忽略该任务
 * @param callable|null $on_item_finish ($curl_ret, $curl_option) 请求结束回调，参数1：返回结果数组，参数2：CURL选项
 * @param int $rolling_window 滚动请求数量
 * @return bool
 * @throws \Exception
 */
function curl_concurrent($curl_option_fetcher, $on_item_start = null, $on_item_finish = null, $rolling_window = 10){
	if(is_array($curl_option_fetcher)){
		$curl_option_fetcher = curl_urls_to_fetcher($curl_option_fetcher);
	}

	$mh = curl_multi_init();
	$tmp_option_cache = [];

	/**
	 * 添加任务
	 * @param int $count 添加数量
	 * @return int 任务添加数量，-1表示没有任务了，0可能由于onstart中断原因导致的。
	 * @throws \Exception
	 */
	$add_task = function($count) use ($mh, $curl_option_fetcher, $on_item_start, &$tmp_option_cache){
		$added = 0;
		for($i = 0; $i < $count; $i++){
			$curl_opt = $curl_option_fetcher();
			if(!$curl_opt){
				return -1;
			}
			$added++;
			if($on_item_start && $on_item_start($curl_opt) === false){
				continue;
			}
			[$ch, $curl_option] = curl_instance('', $curl_opt);
			$resource_id = (int)$ch;
			$tmp_option_cache[$resource_id] = $curl_option;
			curl_multi_add_handle($mh, $ch);
		}
		return $added;
	};

	/**
	 * 获取结果
	 * @return void
	 */
	$get_result = function() use ($add_task, $rolling_window, $mh, $on_item_finish, &$running_count, &$tmp_option_cache){
		//把所有已完成的任务都处理掉, curl_multi_info_read执行一次读取一条
		while($curl_result = curl_multi_info_read($mh)){
			$ch = $curl_result['handle'];
			$resource_id = (int)$ch;

			$ret = [];
			$ret['info'] = curl_getinfo($ch);
			$curl_option = $tmp_option_cache[$resource_id];

			$raw_string = curl_multi_getcontent($ch); //获取结果
			[$ret['head'], $ret['body']] = curl_cut_raw($ch, $raw_string);

			//处理编码转换
			if(isset($curl_option[CURLOPT_PAGE_ENCODING])){
				$ret['body'] = mb_convert_encoding($ret['body'], 'utf8', $curl_option[CURLOPT_PAGE_ENCODING]);
			}

			$ret['error'] = curl_error($ch) ?: null;
			$on_item_finish && $on_item_finish($ret, $curl_option);

			curl_multi_remove_handle($mh, $ch);
			curl_close($ch);
			unset($tmp_option_cache[$resource_id]);
		}
	};

	$running_count = 0;
	do{
		$added_count = $add_task($rolling_window - $running_count);
		//没有任务可以继续添加
		if($added_count === -1){
			break;
		}
		$state = curl_multi_exec($mh, $running_count);
		curl_multi_select($mh, 0.1);
		$get_result();
	} while($state === CURLM_OK);
	curl_multi_close($mh);
	return true;
}

/**
 * 转换CURL信息到HAR格式文件
 * @param array $curl_options
 * @param array $curl_info
 * @param $response_header
 * @param $response_body
 * @return void
 * @todo
 */
function curl_to_har(array $curl_options, array $curl_info, $response_header, $response_body){
	$start_time = '';
	$json = [
		'log'     => [
			'version' => '1.2',
			'creator' => [
				'name'    => 'WebInspector',
				'version' => '537.36',
			],
		],
		'pages'   => [],
		'entries' => [
			[
				'startDateTime' => date('j', $start_time),
				'time'          => $start_time,
				'request'       => [
					'method'      => $curl_options[CURLOPT_POST],
					'url'         => $curl_options[CURLOPT_URL],
					'httpVersion' => 'http/1.0',
					'headers'     => [
						['name' => 'User-Agent', 'value' => $curl_options[CURLOPT_USERAGENT]],
						['name' => 'Accept', 'value' => $curl_options[CURLOPT_USERAGENT]],
						['name' => 'Accept-Encoding', 'value' => $curl_options[CURLOPT_USERAGENT]],
						['name' => 'Accept-Language', 'value' => $curl_options[CURLOPT_USERAGENT]],
						['name' => 'Cache-Control', 'value' => $curl_options[CURLOPT_USERAGENT]],
						['name' => 'Connection', 'value' => 'keep-alive'],
						['name' => 'Host', 'value' => 'host'],
					],
					'queryString' => [],
					'cookies'     => [],
					'headersSize' => 0,
					'bodySize'    => 0,
				],
				'response'      => [
					"status"      => 200,
					"statusText"  => "OK",
					"httpVersion" => "HTTP/1.1",
					'headers'     => [

					],
					'content'     => [
						'size'          => 33,
						'mimeType'      => 'text/html',
						'compression'   => 333,
						'text'          => 'dfasdfasdf',
						"redirectURL"   => "",
						"headersSize"   => 408,
						"bodySize"      => 1473,
						"_transferSize" => 1881,
						"_error"        => null,
					],
				],

				"serverIPAddress" => "127.0.0.1",
				"startedDateTime" => "2023-12-19T05:34:31.467Z",
				"timings"         => [
					"blocked"           => 2.5149999914132057,
					"dns"               => -1,
					"ssl"               => -1,
					"connect"           => -1,
					"send"              => 0.11099999999999999,
					"wait"              => 1.749999976620078,
					"receive"           => 0.438000017311424,
					"_blocked_queueing" => 1.7799999914132059,
				],
			],
		],
	];
}

/**
 * @todo
 * curl command to php
 * @see https://curlconverter.com/php/
 */
function curl_cmd_to_php(){
}
