<?php

namespace LFPhp\Func;

use Exception;

/**
 * CURL GET请求
 * @param $url
 * @param mixed|null $data
 * @param array|null|callable $curl_option 额外CURL选项，如果是闭包函数，传入第一个参数为ch
 * @return array [head, body, ...] curl_getinfo信息
 */
function curl_get($url, $data = null, array $curl_option = []){
	if($data){
		$url .= (strpos($url, '?') !== false ? '&' : '?').curl_data2str($data);
	}
	$ch = curl_instance($url, $curl_option);
	return curl_execute($ch);
}

function curl_get_only(){

}

function curl_post_only(){
	
}

/**
 * POST请求
 * @param $url
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
 * @param $url
 * @param null $data
 * @param array $curl_option
 * @return array
 * @throws \Exception
 */
function curl_post_json($url, $data = null, array $curl_option = []){
	$data = is_array($data) ? json_encode($data) : $data;
	$curl_option = curl_merge_options([
		CURLOPT_HTTPHEADER => [
			'Content-Type: application/json; charset=utf-8',
			'Content-Length: '.strlen($data),
		],
	], $curl_option);
	return curl_post($url, $data, $curl_option);
}

/**
 * PUT请求
 * @param $url
 * @param $data
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
 * @param $url
 * @param $data
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
 * @param resource $ch
 * @return array [head, body, ...] curl_getinfo信息
 */
function curl_execute($ch){
	$raw_string = curl_exec($ch);
	$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	$curl_info['head'] = substr($raw_string, 0, $header_size);
	$curl_info['body'] = substr($raw_string, $header_size);
	curl_close($ch);
	return $curl_info;
}

/**
 * get curl instance by options
 * @param string $url
 * @param array $curl_option
 * @return false|resource
 * @throws \Exception
 */
function curl_instance($url, array $curl_option){
	//use ssl
	$ssl = substr($url, 0, 8) == 'https://' ? true : false;

	$opt = array(
		CURLOPT_USERAGENT      => $_SERVER['HTTP_USER_AGENT'], //在HTTP请求中包含一个"User-Agent: "头的字符串。
		CURLOPT_FOLLOWLOCATION => true, //启用时会将服务器服务器返回的"Location: "放在header中递归的返回给服务器，使用CURLOPT_MAXREDIRS可以限定递归返回的数量。
		CURLOPT_RETURNTRANSFER => true, //文件流形式
		CURLOPT_ENCODING       => 'gzip', //支持gzip
		CURLOPT_TIMEOUT        => 10,
		CURLOPT_URL            => $url,
	);

	if($ssl){
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
 * @param $options
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