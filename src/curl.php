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
 * @param array|null|callable $curl_option 额外CURL选项，如果是闭包函数，传入第一个参数为ch
 * @return array [head, body, ...] curl_getinfo信息
 * @throws \Exception
 */
function curl_get($url, $data = null, array $curl_option = []){
	if($data){
		$url .= (strpos($url, '?') !== false ? '&' : '?').curl_data2str($data);
	}
	$ch = curl_instance($url, $curl_option);
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
	$ch = curl_instance($url, curl_merge_options([
		CURLOPT_POST       => true,
		CURLOPT_POSTFIELDS => http_build_query($data),
	], $curl_option));

	return curl_query($ch);
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
 * JSON方式POST请求
 * @param string $url
 * @param mixed $data
 * @param array $curl_option
 * @return array
 * @throws \Exception
 */
function curl_post_json($url, $data = null, array $curl_option = []){
	$data = ($data && !is_string($data)) ? json_encode($data) : $data;
	$curl_option = curl_merge_options([
		CURLOPT_HTTPHEADER     => [
			'Content-Type: application/json; charset=utf-8',
			'Content-Length: '.strlen($data),
		],
		CURLOPT_FOLLOWLOCATION => true, //允许重定向
		CURLOPT_MAXREDIRS      => 3, //最多允许3次重定向
		CURLOPT_ENCODING       => '', //开启gzip等编码支持
	], $curl_option);
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
	$curl_option = curl_merge_options([
		CURLOPT_POST           => true,
		CURLOPT_POSTFIELDS     => $ext_param,
		CURLOPT_FOLLOWLOCATION => true, //允许重定向
		CURLOPT_MAXREDIRS      => 3, //最多允许3次重定向
		CURLOPT_ENCODING       => '', //开启gzip等编码支持
	], $curl_option);
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
	$ch = curl_instance($url, curl_merge_options([
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
	$ch = curl_instance($url, curl_merge_options([
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
function curl_query($ch){
	$raw_string = curl_exec($ch);
	$error = curl_error($ch);
	if($error){
		throw new Exception($error);
	}
	$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	$curl_info['info'] = curl_getinfo($ch);
	$curl_info['head'] = substr($raw_string, 0, $header_size);
	$curl_info['body'] = substr($raw_string, $header_size);
	curl_close($ch);
	return $curl_info;
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
 * 获取CURL实例对象
 * @param string $url
 * @param array $curl_option
 * @return false|resource
 * @throws \Exception
 */
function curl_instance($url, array $curl_option){
	$opt = array(
		//在HTTP请求中包含一个"User-Agent: "头的字符串，该选项会被HTTPHEADER里面的UA覆盖，仅做备份。
		//可参考：https://stackoverflow.com/questions/52392262/what-is-different-between-set-opt-curlopt-useragent-and-set-useragent-in-header
		CURLOPT_USERAGENT      => $_SERVER['HTTP_USER_AGENT'],

		//跟随服务端响应的跳转
		CURLOPT_FOLLOWLOCATION => true,

		//最大跳转次数
		CURLOPT_MAXREDIRS      => 10,

		//文件流形式
		CURLOPT_RETURNTRANSFER => true,

		//CURLOPT_ENCODING       => '',
		CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,

		//支持 http1.1 协议
		CURLOPT_HEADER         => 1,

		//响应支持携带头部信息
		CURLOPT_TIMEOUT        => 10,
		CURLOPT_URL            => $url,
	);

	//识别请求目标是否未SSL加密
	if(parse_url($url)['scheme'] == 'https'){
		$opt[CURLOPT_SSL_VERIFYPEER] = false;                   //对认证证书来源的检查
		$opt[CURLOPT_SSL_VERIFYHOST] = true;                    //从证书中检查SSL加密算法是否存在
	}

	//设置缺省参数
	$curl_option = curl_merge_options($opt, $curl_option);
	if($curl_option['USE_COOKIE']){
		$curl_option[CURLOPT_COOKIEJAR] = //连接结束后保存cookie信息的文件。
		$curl_option[CURLOPT_COOKIEFILE] = $curl_option['USE_COOKIE'];//包含cookie数据的文件名，cookie文件的格式可以是Netscape格式，或者只是纯HTTP头部信息存入文件。
		unset($curl_option['USE_COOKIE']);
	}

	$sys_max_exe_time = ini_get('max_execution_time');
	if($sys_max_exe_time && $curl_option[CURLOPT_TIMEOUT] && $curl_option[CURLOPT_TIMEOUT] > $sys_max_exe_time){
		throw new Exception('curl timeout setting larger than php.ini setting: '.$curl_option[CURLOPT_TIMEOUT].' > '.$sys_max_exe_time);
	}

	$curl = curl_init();
	foreach($curl_option as $k => $val){
		curl_setopt($curl, $k, $val);
	}
	return $curl;
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
 * 合并CURL选项
 * @param mixed ...$options CURL选项 [option=>value], [option2=>value2, option3=>value3], callable
 * @return array
 */
function curl_merge_options(...$options){
	$ret = [];
	$options = array_reverse($options);
	foreach($options as $option){
		if(is_callable($option)){
			$option = call_user_func($option);
		}
		foreach($option as $k => $v){
			$ret[$k] = $v;
		}
	}
	return $ret;
}

/**
 * 解析 http头信息
 * @param $header_str
 * @return array
 */
function http_parse_headers($header_str){
	$headers = [];
	foreach(explode("\n", $header_str) as $i => $h){
		list($k, $v) = explode(':', $h, 2);
		//由于HTTP HEADER没有约束大小写，这里为了避免传入数据不规范导致，全部格式化小写
		$k = strtolower($k);
		if(isset($v)){
			if(!isset($headers[$k])){
				$headers[$k] = trim($v);
			}else if(is_array($headers[$k])){
				$tmp = array_merge($headers[$k], array(trim($v)));
				$headers[$k] = $tmp;
			}else{
				$tmp = array_merge(array($headers[$k]), array(trim($v)));
				$headers[$k] = $tmp;
			}
		}
	}
	return $headers;
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
