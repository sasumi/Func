<?php
/**
 * CURL网络请求相关操作函数
 */
namespace LFPhp\Func;

use Exception;

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
	$ch = curl_instance($url, curl_default_option($url, $curl_option));
	return curl_query($ch);
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
	$curl_option = array_merge_assoc(curl_default_option($url, $curl_option), [
		CURLOPT_POST       => true,
		CURLOPT_POSTFIELDS => is_string($data) ? $data : http_build_query($data),
	]);
	$ch = curl_instance($url, $curl_option);
	return curl_query($ch);
}

/**
 * JSON方式发送POST请求
 * @param string $url
 * @param mixed $data
 * @param array $curl_option
 * @return array
 * @throws \Exception
 */
function curl_post_json($url, $data = null, array $curl_option = []){
	$data = ($data && !is_string($data)) ? json_encode($data) : $data;
	$curl_option = array_merge_assoc(curl_default_option($url, $curl_option), [
		CURLOPT_HTTPHEADER => [
			'Content-Type: application/json; charset=utf-8',
			'Content-Length: '.strlen($data),
		],
	]);
	return curl_post($url, $data, $curl_option);
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
	$curl_option = array_merge_assoc(curl_default_option($url, $curl_option), [
		CURLOPT_POST           => true,
		CURLOPT_POSTFIELDS     => $ext_param,
		CURLOPT_FOLLOWLOCATION => true, //允许重定向
		CURLOPT_MAXREDIRS      => 3, //最多允许3次重定向
		CURLOPT_ENCODING       => '', //开启gzip等编码支持
	]);
	$ch = curl_instance($url, $curl_option);
	return curl_query($ch);
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
	$ch = curl_instance($url, array_merge_assoc([
		CURLOPT_POSTFIELDS    => curl_data2str($data),
		CURLOPT_CUSTOMREQUEST => 'PUT',
	], $curl_option));
	return curl_query($ch);
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
	$ch = curl_instance($url, array_merge_assoc([
		CURLOPT_POSTFIELDS    => curl_data2str($data),
		CURLOPT_CUSTOMREQUEST => 'DELETE',
	], $curl_option));
	return curl_query($ch);
}

/**
 * 执行curl，并关闭curl连接
 * @param resource $ch
 * @return array [info=>[], head=>'', body=>''] curl_getinfo信息
 * @throws \Exception
 */
function curl_query($ch, &$error = ''){
	$raw_string = curl_exec($ch);
	$error = curl_error($ch);
	if($error){
		return [];
	}
	$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	$curl_info['info'] = curl_getinfo($ch);
	$curl_info['head'] = substr($raw_string, 0, $header_size);
	$curl_info['body'] = substr($raw_string, $header_size);
	curl_close($ch);
	return $curl_info;
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
	foreach($curl_option[CURLOPT_HTTPHEADER] as $k=>$item){
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
	$method = strtoupper($method);
	$line_sep = $multiple_line ? '\\'.PHP_EOL : '';
	if($method === 'GET' && $body_str){
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
	if($body_str && $method === 'POST'){
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
		list($account, $password) = explode(':', $matches[1]);
		$proxy_string = preg_replace('/.*?@/', '', $proxy_string);
	}
	list($host, $port) = explode(':', $proxy_string);

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
 * @param string $url 请求URL，该参数用于识别https协议请求，添加响应CURL选项
 * @param array $custom_option
 * @return array
 */
function curl_default_option($url = '', $custom_option = []){
	$curl_option = array_merge_assoc([
		CURLOPT_RETURNTRANSFER => true, //返回内容部分
		CURLOPT_HEADER         => true, //发送头部信息
		CURLOPT_USERAGENT      => $_SERVER['HTTP_USER_AGENT'], //缺省使用请求UA，如果是CLI模式，这里为空
		CURLOPT_FOLLOWLOCATION => true, //跟随服务端响应的跳转
		CURLOPT_MAXREDIRS      => 10, //最大跳转次数
		CURLOPT_ENCODING       => 'gzip, deflate', //缺省使用 gzip 传输
		CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1, //默认使用 HTTP1.1 版本
		CURLOPT_TIMEOUT        => 10, //默认超时时间 10s
	], $custom_option);

	if($url){
		$curl_option[CURLOPT_URL] = $url;
		//补充支持 https:// 协议
		if(stripos($url, 'https://') === 0){
			$curl_option[CURLOPT_SSL_VERIFYPEER] = 0;
			$curl_option[CURLOPT_SSL_VERIFYHOST] = 1;
		}
	}

	//处理HTTP头部，如果传入的是key => value数组，转换成字符串数组
	if($curl_option[CURLOPT_HTTPHEADER] && is_assoc_array($curl_option[CURLOPT_HTTPHEADER])){
		$tmp = [];
		foreach($curl_option[CURLOPT_HTTPHEADER] as $field=>$val){
			$tmp[] = "$field: $val";
		}
		$curl_option[CURLOPT_HTTPHEADER] = $tmp;
	}

	//设置缺省参数
	if($curl_option['USE_COOKIE']){
		$curl_option[CURLOPT_COOKIEJAR] = $curl_option['USE_COOKIE']; //连接结束后保存cookie信息的文件。
		$curl_option[CURLOPT_COOKIEFILE] = $curl_option['USE_COOKIE']; //包含cookie数据的文件名，cookie文件的格式可以是Netscape格式，或者只是纯HTTP头部信息存入文件。
		unset($curl_option['USE_COOKIE']);
	}

	if($curl_option[CURLOPT_TIMEOUT] && get_max_socket_timeout() < $curl_option[CURLOPT_TIMEOUT]){
		//warning timeout setting no taking effect
		error_log('warning timeout setting no taking effect');
	}

	return $curl_option;
}

/**
 * 获取CURL实例对象
 * @param string $url
 * @param array $curl_option CURL选项，会通过 curl_default_option() 添加额外默认选项
 * @return resource
 * @throws \Exception
 */
function curl_instance($url, array $curl_option){
	$ch = curl_init();
	$curl_option[CURLOPT_URL] = $url;
	curl_setopt_array($ch, $curl_option);
	if(!$ch){
		throw new Exception('Curl init fail');
	}
	return $ch;
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
					list($k, $v) = explode_by(':', $mv);
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
 * @param string[] $urls 请求链接数组
 * @param array $curl_option 通用CURL选项数组
 * @return \Closure
 */
function curl_urls_to_fetcher($urls, $curl_option){
	$options = [];
	foreach($urls as $url){
		$curl_option[CURLOPT_URL] = $url;
		$options[] = $curl_option;
	}
	return function()use(&$options){
		return array_shift($options);
	};
}

/**
 * CURL 并发请求
 * 注意：回调函数需尽快处理避免阻塞后续请求流程
 * @param callable $curl_option_fetcher : array 返回CURL选项映射数组
 * @param callable|null $on_item_start ($curl_option) 开始执行回调
 * @param callable|null $on_item_finish ($info, $error=null) 请求结束回调，参数1：返回结果数组，参数2：错误信息，为空表示成功
 * @param int $rolling_window 滚动请求数量
 * @return bool
 */
function curl_concurrent($curl_option_fetcher, $on_item_start = null, $on_item_finish = null, $rolling_window = 10){
	$mh = curl_multi_init();

	/**
	 * 添加任务
	 * @param int $count 添加数量
	 * @return int
	 */
	$add_task = function($count) use ($mh, $curl_option_fetcher, $on_item_start){
		$added = 0;
		for($i = 0; $i < $count; $i++){
			$curl_opt = $curl_option_fetcher();
			if(!$curl_opt){
				return false;
			}
			$added++;
			$on_item_start && $on_item_start($curl_opt);
			$ch = curl_init();
			curl_setopt_array($ch, $curl_opt);
			curl_multi_add_handle($mh, $ch);
		}
		return $added;
	};

	/**
	 * 获取结果
	 * @return void
	 */
	$get_result = function() use ($mh, $on_item_finish, &$running_count){
		//把所有已完成的任务都处理掉, curl_multi_info_read执行一次读取一条
		while($curl_result = curl_multi_info_read($mh)){
			$ch = $curl_result['handle'];
			$info = curl_getinfo($ch);

			$raw_string = curl_multi_getcontent($ch); //获取结果
			$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
			$info['head'] = substr($raw_string, 0, $header_size);
			$info['body'] = substr($raw_string, $header_size);
			$error = curl_error($ch) ?: null;
			$on_item_finish && $on_item_finish($info, $error);

			curl_multi_remove_handle($mh, $ch);
			curl_close($ch);
		}
	};

	$running_count = 0;
	do{
		$added = $add_task($rolling_window - $running_count);
		$state = curl_multi_exec($mh, $running_count);
		curl_multi_select($mh, 0.1);
		$get_result();

		if(!$added && !$running_count){
			break;
		}
	} while($state === CURLM_OK);
	curl_multi_close($mh);
	return true;
}

/**
 * 转换CURL信息到HAR格式文件
 * @todo
 * @param array $curl_options
 * @param array $curl_info
 * @param $response_header
 * @param $response_body
 * @return void
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
