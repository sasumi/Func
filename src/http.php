<?php
/**
 * HTTP操作函数
 */
namespace LFPhp\Func;

use Exception;

const HTTP_STATUS_MESSAGE_MAP = [
	100 => 'Continue',
	101 => 'Switching Protocols',
	200 => 'OK',
	201 => 'Created',
	202 => 'Accepted',
	203 => 'Non-Authoritative Information',
	204 => 'No Content',
	205 => 'Reset Content',
	206 => 'Partial Content',
	300 => 'Multiple Choices',
	301 => 'Moved Permanently',
	302 => 'Moved Temporarily ',
	303 => 'See Other',
	304 => 'Not Modified',
	305 => 'Use Proxy',
	307 => 'Temporary Redirect',
	400 => 'Bad Request',
	401 => 'Unauthorized',
	402 => 'Payment Required',
	403 => 'Forbidden',
	404 => 'Not Found',
	405 => 'Method Not Allowed',
	406 => 'Not Acceptable',
	407 => 'Proxy Authentication Required',
	408 => 'Request Timeout',
	409 => 'Conflict',
	410 => 'Gone',
	411 => 'Length Required',
	412 => 'Precondition Failed',
	413 => 'Request Entity Too Large',
	414 => 'Request-URI Too Long',
	415 => 'Unsupported Media Type',
	416 => 'Requested Range Not Satisfiable',
	417 => 'Expectation Failed',
	500 => 'Internal Server Error',
	501 => 'Not Implemented',
	502 => 'Bad Gateway',
	503 => 'Service Unavailable',
	504 => 'Gateway Timeout',
	505 => 'HTTP Version Not Supported',
	509 => 'Bandwidth Limit Exceeded',
];

/**
 * 发送HTTP状态码
 * @param int $status http 状态码
 * @return bool
 */
function http_send_status($status){
	$message = http_get_status_message($status);
	if(!headers_sent() && $message){
		if(substr(php_sapi_name(), 0, 3) == 'cgi'){//CGI 模式
			header("Status: $status $message");
		}else{ //FastCGI模式
			header("{$_SERVER['SERVER_PROTOCOL']} $status $message");
		}
		return true;
	}
	return false;
}

/**
 * 开启 httpd 服务器分块输出
 */
function http_chunk_on(){
	@ob_end_clean(); //强制php直接输出内容到浏览器，不加入缓冲区
	ob_implicit_flush(true); //设置nginx或apache不缓冲，直接输出
	header('X-Accel-Buffering: no'); //关键是加了这一行。
}

/**
 * 返回跨域CORS头信息
 * @param string[] $allow_hosts 允许通过的域名列表，为空表示允许所有来源域名
 * @param string $http_origin 来源请求，格式为：http://www.abc.com，缺省从 HTTP_ORIGIN 或 HTTP_REFERER获取
 * @throws \Exception
 */
function http_send_cors($allow_hosts = [], $http_origin = null){
	$http_origin = $http_origin ?: $_SERVER['HTTP_ORIGIN'] ?: $_SERVER['HTTP_REFERER'];
	if(!$http_origin){
		throw new Exception('no http origin detected');
	}
	$ret = parse_url($http_origin);
	$request_host = $ret['host'];
	$http_scheme = $ret['scheme'];

	if($allow_hosts && !in_array(strtolower($request_host), array_map('strtolower', $allow_hosts))){
		throw new Exception('request host:'.$request_host.' no in allow host list('.json_encode($allow_hosts).')');
	}
	if(headers_sent()){
		throw new Exception('header already sent');
	}
	header("Access-Control-Allow-Origin: $http_scheme://$request_host");
	header('Access-Control-Allow-Credentials: true');
	header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
}

/**
 * 发送 HTTP 头部字符集
 * @param string $charset
 * @return bool 是否成功
 */
function http_send_charset($charset){
	if(!headers_sent()){
		header('Content-Type:text/html; charset='.$charset);
		return true;
	}
	return false;
}

/**
 * 获取HTTP状态码对应描述
 * @param int $status
 * @return string|null
 */
function http_get_status_message($status){
	return HTTP_STATUS_MESSAGE_MAP[$status];
}

/**
 * HTTP方式跳转
 * @param string $url 跳转路径
 * @param bool $permanently 是否为长期资源重定向
 */
function http_redirect($url, $permanently = false){
	http_send_status($permanently ? 301 : 302);
	header('Location:'.$url);
}

/**
 * 获取HTTP请求头信息数组
 * @return array [key=>val]
 */
function http_get_request_headers(){
	if(function_exists('\http_get_request_headers')){
		return call_user_func('\http_get_request_headers');
	}
	$headers = array();
	foreach($_SERVER as $key => $value){
		if('HTTP_' == substr($key, 0, 5)){
			$headers[str_replace('_', '-', substr($key, 5))] = $value;
		}
		if(isset($_SERVER['PHP_AUTH_DIGEST'])){
			$headers['AUTHORIZATION'] = $_SERVER['PHP_AUTH_DIGEST'];
		}elseif(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])){
			$headers['AUTHORIZATION'] = base64_encode($_SERVER['PHP_AUTH_USER'].':'.$_SERVER['PHP_AUTH_PW']);
		}
		if(isset($_SERVER['CONTENT_LENGTH'])){
			$headers['CONTENT-LENGTH'] = $_SERVER['CONTENT_LENGTH'];
		}
		if(isset($_SERVER['CONTENT_TYPE'])){
			$headers['CONTENT-TYPE'] = $_SERVER['CONTENT_TYPE'];
		}
	}
	return $headers;
}

/**
 * 获取HTTP请求头中指定key值
 * @param string $key 不区分大小写
 * @return mixed|null
 */
function http_get_request_header($key){
	$headers = http_get_request_headers();
	foreach($headers as $k => $val){
		if(strcasecmp($k, $key) === 0){
			return $val;
		}
	}
	return null;
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
 * 判断请求方式是否为 JSON 方式
 * @return bool
 */
function http_from_json_request(){
	return http_get_request_header('Content-Type') == 'application/json';
}

/**
 * 判断请求接受格式是否为 JSON
 * @return bool
 */
function http_request_accept_json(){
	return http_get_request_header('Accept') == 'application/json';
}

/**
 * 请求来自POST
 * @return bool
 */
function request_in_post(){
	return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * 请求来自于GET
 * @return bool
 */
function request_in_get(){
	return $_SERVER['REQUEST_METHOD'] === 'GET';
}

/**
 * 获取当前页面地址
 * @param bool $with_protocol 是否包含协议头
 * @return string
 */
function http_get_current_page_url($with_protocol = true, $with_port = false){
	$port_str = $_SERVER['SERVER_PORT'] == '80' ? '' : ':'.$_SERVER['SERVER_PORT'];
	$protocol_str = $with_protocol ? (server_in_https() ? 'https:' : 'http:') : '';
	return $protocol_str.'//'.$_SERVER['HTTP_HOST'].($with_port ? $port_str:'').$_SERVER['REQUEST_URI'];
}

/**
 * 文件流方式下载文件
 * @param string $file 文件路径
 * @param string $download_name 下载文件名
 * @param string $disposition 头类型
 * @return false|int 成功下载文件尺寸，false为失败
 */
function http_download_stream($file, $download_name = '', $disposition = 'attachment'){
	http_header_download($download_name, $disposition);
	$CHUNK_SIZE = 1024*1024;
	$handle = fopen($file, 'rb');
	if($handle === false){
		return false;
	}
	$cnt = 0;
	while(!feof($handle)){
		$buffer = fread($handle, $CHUNK_SIZE);
		echo $buffer;
		ob_flush();
		flush();
		$cnt += strlen($buffer);
	}
	$status = fclose($handle);
	if($status){
		return $cnt;
	}
	return false;
}

/**
 * 响应json数据
 * @param mixed $json
 * @param int $json_option
 * @return void
 */
function http_json_response($json, $json_option = JSON_UNESCAPED_UNICODE){
	http_header_json_response();
	echo json_encode($json, $json_option);
}

/**
 * 响应JSON返回头
 * @param string $charset
 */
function http_header_json_response($charset = 'utf-8'){
	header('Content-Type: application/json;'.($charset ? " charset=$charset" : ''));
}

/**
 * 发送文件下载头信息
 * @param string $download_name
 * @param string $disposition
 */
function http_header_download($download_name = '', $disposition = 'attachment'){
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Type: application/force-download");
	header("Content-Type: application/octet-stream");
	header("Content-Type: application/download");
	header("Content-Disposition: $disposition".($download_name ? ";filename=$download_name" : ''));
	header("Content-Transfer-Encoding: binary");
}

/**
 * 发送CSP头
 * @param string[] $csp_rules 建议使用csp_content_rule()方法产生的规则
 * @param string $report_uri
 * @param bool $report_only
 * @throws \Exception
 */
function http_header_csp(array $csp_rules, $report_uri = '', $report_only = false){
	if($report_only && !$report_uri){
		throw new Exception('CSP report uri required.');
	}
	$str = ($report_only ? CSP_REPORT_ONLY_PREFIX : CSP_PREFIX).': ';
	$str .= join('; ', $csp_rules).';';
	$str .= $report_uri ? csp_report_uri($report_uri).';' : '';
	header($str);
}

/**
 * 发送浏览器设置 Report API
 * @param string[] $endpoint_urls
 * @param string $group
 * @param number $max_age_sec
 * @param bool $include_subdomains
 */
function http_header_report_api(array $endpoint_urls, $group = 'default', $max_age_sec = ONE_DAY, $include_subdomains = true){
	header('Report-To: '.json_encode(generate_report_api($endpoint_urls, $group, $max_age_sec, $include_subdomains)));
}

/**
 * 发送浏览器错误日志上报 Report API
 * @param string[] $endpoint_urls
 * @param string $group
 * @param number $max_age_sec
 * @param bool $include_subdomains
 * @return void
 */
function http_header_report_api_nel(array $endpoint_urls, $group = 'network-error', $max_age_sec = ONE_DAY, $include_subdomains = true){
	header('NEL: '.json_encode(generate_report_api($endpoint_urls, $group, $max_age_sec, $include_subdomains)));
}

/**
 * 生成 Report API
 * @param string[] $endpoint_urls
 * @param string $group
 * @param number $max_age_sec
 * @param bool $include_subdomains
 * @return array
 */
function generate_report_api(array $endpoint_urls, $group = 'default', $max_age_sec = ONE_DAY, $include_subdomains = true){
	$endpoints_obj = [];
	foreach($endpoint_urls as $url){
		$endpoints_obj[] = ['url' => $url];
	};
	return [
		'group'              => $group,
		'max_age'            => $max_age_sec,
		'include_subdomains' => $include_subdomains,
		'endpoints'          => $endpoints_obj,
	];
}

/**
 * 解析cookie字符串为一个哈希数组
 * @param string $cookie_str
 * @return array
 */
function http_parse_cookie($cookie_str){
	parse_str(strtr($cookie_str, array('&' => '%26', '+' => '%2B', ';' => '&')), $cookies);
	return $cookies;
}
