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
	return curl_execute($ch);
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
		CURLOPT_POSTFIELDS => curl_data2str($data),
	], $curl_option));
	return curl_execute($ch);
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
 * @return array curl_execute返回结果，包含 [info=>[], head=>'', body=>''] 信息
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
	return curl_execute($ch);
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
	return curl_execute($ch);
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
	return curl_execute($ch);
}

/**
 * 执行curl，并关闭curl连接
 * @param resource $ch
 * @return array [info=>[], head=>'', body=>''] curl_getinfo信息
 */
function curl_execute($ch){
	$raw_string = curl_exec($ch);
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
	//use ssl
	$as_ssl = substr($url, 0, 8) == 'https://';

	$opt = array(
		CURLOPT_USERAGENT      => $_SERVER['HTTP_USER_AGENT'], //在HTTP请求中包含一个"User-Agent: "头的字符串。
		CURLOPT_FOLLOWLOCATION => true, //启用时会将服务器服务器返回的"Location: "放在header中递归的返回给服务器，使用CURLOPT_MAXREDIRS可以限定递归返回的数量。
		CURLOPT_RETURNTRANSFER => true, //文件流形式
		CURLOPT_ENCODING       => 'gzip', //支持gzip
		CURLOPT_HEADER         => 1, //响应支持携带头部信息
		CURLOPT_TIMEOUT        => 10,
		CURLOPT_URL            => $url,
	);

	if($as_ssl){
		$opt[CURLOPT_SSL_VERIFYPEER] = false;                   //对认证证书来源的检查
		$opt[CURLOPT_SSL_VERIFYHOST] = true;                    //从证书中检查SSL加密算法是否存在
	}

	//设置缺省参数
	$curl_option = curl_merge_options($opt, $curl_option);

	$sys_max_exe_time = ini_get('max_execution_time');
	if($sys_max_exe_time && $curl_option[CURLOPT_TIMEOUT] && $curl_option[CURLOPT_TIMEOUT] > $sys_max_exe_time){
		throw new Exception('curl timeout setting larger than php.ini setting: '.$curl_option[CURLOPT_TIMEOUT].' > '.$sys_max_exe_time);
	}
	$curl = curl_init();
	foreach($curl_option as $k => $val){
		if($k == 'USE_COOKIE'){
			curl_setopt($curl, CURLOPT_COOKIEJAR, $val);    //连接结束后保存cookie信息的文件。
			curl_setopt($curl, CURLOPT_COOKIEFILE, $val);   //包含cookie数据的文件名，cookie文件的格式可以是Netscape格式，或者只是纯HTTP头部信息存入文件。
		}else{
			curl_setopt($curl, $k, $val);
		}
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
