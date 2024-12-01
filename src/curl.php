<?php

/**
 * CURL Enhancement Functions
 * Mark: standard structure for curl_get():
 * [info=>['url'='', ...], error='', head=>'', body=>'']
 */

namespace LFPhp\Func;

use Exception;

/**
 * CURL requests global default parameters, which can be operated through curl_get_default_option() and curl_set_default_option()
 */
const CURL_DEFAULT_OPTION_GLOBAL_KEY = __NAMESPACE__ . '/curl_default_option';
$GLOBALS[CURL_DEFAULT_OPTION_GLOBAL_KEY] = [
	CURLOPT_RETURNTRANSFER => true, //Return the content part instead of direct output
	CURLOPT_HEADER => true, //Return header information
	CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'], //Request UA is used by default. If it is CLI mode, this is empty.
	CURLOPT_FOLLOWLOCATION => true, //Follow the jump of the server response
	CURLOPT_MAXREDIRS => 5, //Maximum number of jumps. Too many jumps may cause excessive performance consumption.
	CURLOPT_ENCODING => 'gzip, deflate', //Gzip transmission is used by default
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, //Use HTTP1.1 version by default
	CURLOPT_TIMEOUT => 10, //Default timeout 10s
];

/**
 * Additional support for control options, using the current __NAMESPACE__ as a prefix, will be removed when curl initializes parameters.
 */

//The response page encoding is automatically converted to UTF-8, which can be set to a specified encoding (such as gbk, gb2312) or '' (indicating automatic identification). If this option is not set, or is set to NULL, CURL will not perform page encoding conversion.
const CURLOPT_PAGE_ENCODING = __NAMESPACE__ . '/CURL_PAGE_ENCODING';

//Set up automatic writing and reading of cookie files (follow cookie files)
const CURLOPT_FOLLOWING_COOKIE_FILE = __NAMESPACE__ . '/CURLOPT_FOLLOWING_COOKIE_FILE';

//Automatically repair relative paths in html
const CURLOPT_HTML_FIX_RELATIVE_PATH = __NAMESPACE__ . '/CURLOPT_HTML_FIX_RELATIVE_PATH';

//curl error message mapping
const CURL_ERROR_MAP = [
	1  => 'CURLE_UNSUPPORTED_PROTOCOL',
	2  => 'CURLE_FAILED_INIT',
	3  => 'CURLE_URL_MALFORMAT',
	4  => 'CURLE_URL_MALFORMAT_USER',
	5  => 'CURLE_COULDNT_RESOLVE_PROXY',
	6  => 'CURLE_COULDNT_RESOLVE_HOST',
	7  => 'CURLE_COULDNT_CONNECT',
	8  => 'CURLE_FTP_WEIRD_SERVER_REPLY',
	9  => 'CURLE_REMOTE_ACCESS_DENIED',
	11 => 'CURLE_FTP_WEIRD_PASS_REPLY',
	13 => 'CURLE_FTP_WEIRD_PASV_REPLY',
	14 => 'CURLE_FTP_WEIRD_227_FORMAT',
	15 => 'CURLE_FTP_CANT_GET_HOST',
	17 => 'CURLE_FTP_COULDNT_SET_TYPE',
	18 => 'CURLE_PARTIAL_FILE',
	19 => 'CURLE_FTP_COULDNT_RETR_FILE',
	21 => 'CURLE_QUOTE_ERROR',
	22 => 'CURLE_HTTP_RETURNED_ERROR',
	23 => 'CURLE_WRITE_ERROR',
	25 => 'CURLE_UPLOAD_FAILED',
	26 => 'CURLE_READ_ERROR',
	27 => 'CURLE_OUT_OF_MEMORY',
	28 => 'CURLE_OPERATION_TIMEDOUT',
	30 => 'CURLE_FTP_PORT_FAILED',
	31 => 'CURLE_FTP_COULDNT_USE_REST',
	33 => 'CURLE_RANGE_ERROR',
	34 => 'CURLE_HTTP_POST_ERROR',
	35 => 'CURLE_SSL_CONNECT_ERROR',
	36 => 'CURLE_BAD_DOWNLOAD_RESUME',
	37 => 'CURLE_FILE_COULDNT_READ_FILE',
	38 => 'CURLE_LDAP_CANNOT_BIND',
	39 => 'CURLE_LDAP_SEARCH_FAILED',
	41 => 'CURLE_FUNCTION_NOT_FOUND',
	42 => 'CURLE_ABORTED_BY_CALLBACK',
	43 => 'CURLE_BAD_FUNCTION_ARGUMENT',
	45 => 'CURLE_INTERFACE_FAILED',
	47 => 'CURLE_TOO_MANY_REDIRECTS',
	48 => 'CURLE_UNKNOWN_TELNET_OPTION',
	49 => 'CURLE_TELNET_OPTION_SYNTAX',
	51 => 'CURLE_PEER_FAILED_VERIFICATION',
	52 => 'CURLE_GOT_NOTHING',
	53 => 'CURLE_SSL_ENGINE_NOTFOUND',
	54 => 'CURLE_SSL_ENGINE_SETFAILED',
	55 => 'CURLE_SEND_ERROR',
	56 => 'CURLE_RECV_ERROR',
	58 => 'CURLE_SSL_CERTPROBLEM',
	59 => 'CURLE_SSL_CIPHER',
	60 => 'CURLE_SSL_CACERT',
	61 => 'CURLE_BAD_CONTENT_ENCODING',
	62 => 'CURLE_LDAP_INVALID_URL',
	63 => 'CURLE_FILESIZE_EXCEEDED',
	64 => 'CURLE_USE_SSL_FAILED',
	65 => 'CURLE_SEND_FAIL_REWIND',
	66 => 'CURLE_SSL_ENGINE_INITFAILED',
	67 => 'CURLE_LOGIN_DENIED',
	68 => 'CURLE_TFTP_NOTFOUND',
	69 => 'CURLE_TFTP_PERM',
	70 => 'CURLE_REMOTE_DISK_FULL',
	71 => 'CURLE_TFTP_ILLEGAL',
	72 => 'CURLE_TFTP_UNKNOWNID',
	73 => 'CURLE_REMOTE_FILE_EXISTS',
	74 => 'CURLE_TFTP_NOSUCHUSER',
	75 => 'CURLE_CONV_FAILED',
	76 => 'CURLE_CONV_REQD',
	77 => 'CURLE_SSL_CACERT_BADFILE',
	78 => 'CURLE_REMOTE_FILE_NOT_FOUND',
	79 => 'CURLE_SSH',
	80 => 'CURLE_SSL_SHUTDOWN_FAILED',
	81 => 'CURLE_AGAIN',
	82 => 'CURLE_SSL_CRL_BADFILE',
	83 => 'CURLE_SSL_ISSUER_ERROR',
	84 => 'CURLE_FTP_PRET_FAILED',
	85 => 'CURLE_RTSP_CSEQ_ERROR',
	86 => 'CURLE_RTSP_SESSION_ERROR',
	87 => 'CURLE_FTP_BAD_FILE_LIST',
	88 => 'CURLE_CHUNK_FAILED',
	89 => "CURLE_NO_CONNECTION_AVAILABLE", // No connection available, the session will be queued
	90 => "CURLE_SSL_PINNEDPUBKEYNOTMATCH", // specified pinned public key did not  match
	91 => "CURLE_SSL_INVALIDCERTSTATUS", // invalid certificate status
	92 => "CURLE_HTTP2_STREAM", // stream error in HTTP/2 framing layer
	93 => "CURLE_RECURSIVE_API_CALL", // an api function was called from inside a callback
	94 => "CURLE_AUTH_ERROR", // an authentication function returned an error
	95 => "CURLE_HTTP3", // An HTTP/3 layer problem
	96 => "CURLE_QUIC_CONNECT_ERROR", // QUIC connection error
	97 => "CURLE_PROXY", // proxy handshake error
	98 => "CURLE_SSL_CLIENTCERT", // client-side certificate required
];

/**
 * CURL GET Request
 * @param string $url
 * @param mixed|null $data
 * @param array $curl_option extra curl option
 * @return array [info=>[], head=>'', body=>'', ...] curl_getinfo structure
 * @throws \Exception
 */
function curl_get($url, $data = null, array $curl_option = []){
	if ($data) {
		$url .= (strpos($url, '?') !== false ? '&' : '?') . curl_data2str($data);
	}
	return curl_query($url, $curl_option);
}

/**
 * Post request
 * @param string $url
 * @param mixed|null $data
 * @param array $curl_option
 * @return array
 * @throws \Exception
 */
function curl_post($url, $data = null, array $curl_option = []){
	return curl_query($url, curl_option_merge([
		CURLOPT_POST       => true,
		CURLOPT_POSTFIELDS => is_string($data) ? $data : http_build_query($data),
		CURLOPT_HTTPHEADER => [
			//use form-urlencoded header in default
			'Content-Type: application/x-www-form-urlencoded'
		]
	], $curl_option));
}

/**
 * post data in json format
 * @param string $url
 * @param mixed $data
 * @param array $curl_option
 * @return array
 * @throws \Exception
 */
function curl_post_json($url, array $data = [], array $curl_option = []){
	$data = $data ? json_encode($data) : '';
	return curl_post($url, $data, curl_option_merge([
		CURLOPT_HTTPHEADER => [
			'Content-Type: application/json; charset=utf-8',
			'Content-Length: ' . strlen($data),
		],
	], $curl_option));
}

/**
 * curl post file
 * @param string $url
 * @param array $file_map [filename=>file, filename=>[file, mime]...] File name mapping, if mime information is not provided here, the backend may receive application/octet-stream
 * @param mixed $ext_param Other post parameters submitted at the same time
 * @param array $curl_option curl option
 * @return array curl_query返回结果，包含 [info=>[], head=>'', body=>''] 信息
 * @throws \Exception
 */
function curl_post_file($url, array $file_map, array $ext_param = [], array $curl_option = []){
	foreach ($file_map as $name => $file) {
		$mime = '';
		if (is_array($file)) {
			[$file, $mime] = $file;
		}
		if (!is_file($file)) {
			throw new Exception('file no found:' . $file);
		}
		$ext_param[$name] = curl_file_create($file, $mime);
	}
	return curl_query($url, curl_option_merge([
		CURLOPT_POST       => true,
		CURLOPT_POSTFIELDS => $ext_param,
	], $curl_option));
}

/**
 * Put request
 * @param string $url
 * @param array $data
 * @param array $curl_option
 * @return array
 * @throws \Exception
 */
function curl_put($url, $data, array $curl_option = []){
	return curl_query($url, curl_option_merge([
		CURLOPT_POSTFIELDS    => curl_data2str($data),
		CURLOPT_CUSTOMREQUEST => 'PUT',
	], $curl_option));
}

/**
 * Delete request
 * @param string $url
 * @param array $data
 * @param array $curl_option
 * @return array
 * @throws \Exception
 */
function curl_delete($url, $data, array $curl_option = []){
	return curl_query($url, curl_option_merge([
		CURLOPT_POSTFIELDS    => curl_data2str($data),
		CURLOPT_CUSTOMREQUEST => 'DELETE',
	], $curl_option));
}

/**
 * Quickly execute a curl query then close the curl connection
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
	$errno = curl_errno($ch);
	if ($errno) {
		$ret['error'] = curl_error_message($errno);
	} else {
		[$ret['head'], $ret['body']] = curl_cut_raw($ch, $raw_string);
		if (isset($curl_option[CURLOPT_PAGE_ENCODING])) {
			$ret['body'] = mb_convert_encoding($ret['body'], 'utf8', $curl_option[CURLOPT_PAGE_ENCODING]);
		}
		if ($curl_option[CURLOPT_HTML_FIX_RELATIVE_PATH]) {
			//Here the last actual URL is used as the replacement criterion
			$ret['body'] = html_fix_relative_path($ret['body'], $ret['info']['url']);
		}
	}
	curl_close($ch);
	return $ret;
}

/**
 * CURL HTTP Header appends additional information. If it already exists, it will be replaced.
 * @param array $curl_option
 * @param string $header_name
 * @param string $header_value
 * @return void
 */
function curl_patch_header(&$curl_option, $header_name, $header_value){
	if(!$curl_option[CURLOPT_HTTPHEADER]){
		$curl_option[CURLOPT_HTTPHEADER] = [];
	}
	foreach ($curl_option[CURLOPT_HTTPHEADER] as $k => $item) {
		if (strcasecmp($item, $header_name) === 0) {
			$curl_option[CURLOPT_HTTPHEADER][$k] = $header_value;
			break;
		}
	}
	$curl_option[CURLOPT_HTTPHEADER][] = "$header_name: $header_value";
}

/**
 * Build CURL command
 * @param string $url
 * @param string $body_str
 * @param string $method
 * @param string[] $headers header information, in the format of ['Content-Type: application/json'] or ['Content-Type‘=>'application/json']
 * @return string
 */
function curl_build_command($url, $body_str, $method, $headers, $multiple_line = true){
	$line_sep = $multiple_line ? '\\' . PHP_EOL : '';
	$method = strtoupper($method);
	if ($method === HTTP_METHOD_GET && $body_str) {
		$url .= (stripos($url, '?') !== false ? '&' : '?') . $body_str;
	}
	$cmd = "curl -L -X $method '$url'" . $line_sep;
	foreach ($headers as $name => $value) {
		if (is_numeric($name)) {
			$cmd .= " -H '$value'" . $line_sep;
		} else {
			$cmd .= " -H '$name: $value'" . $line_sep;
		}
	}
	if ($body_str && $method === HTTP_METHOD_POST) {
		$body_str = addcslashes($body_str, "'");
		$cmd .= " --data-raw '$body_str'";
	}
	return $cmd;
}

/**
 * Parse the proxy string and generate curl option
 * @param string $proxy_string
 * Agent string, in the format:
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
	$type = 'http'; //Use http protocol by default
	$account = '';
	$password = '';
	$CURL_TYPE_MAP = [
		'http'    => CURLPROXY_HTTP,
		'https'   => CURLPROXY_HTTP, //port 443
		'socks4'  => CURLPROXY_SOCKS4,
		'socks5'  => CURLPROXY_SOCKS5,
		'socks4a' => CURLPROXY_SOCKS4A,
	];
	if (preg_match('/^(\w+):\/\//', $proxy_string, $matches)) {
		$type = strtolower($matches[1]);
		$proxy_string = preg_replace('/^\w+:\/\//', '', $proxy_string);
	}
	if (!isset($CURL_TYPE_MAP[$type])) {
		throw new Exception('Proxy type no supported:' . $type);
	}
	if (preg_match('/^(.*?)@/', $proxy_string, $matches)) {
		[$account, $password] = explode(':', $matches[1]);
		$proxy_string = preg_replace('/.*?@/', '', $proxy_string);
	}
	[$host, $port] = explode(':', $proxy_string);

	//https uses port 443 by default
	if ($type === 'https' && !$port) {
		$port = 443;
	}
	$curl_option = [
		CURLOPT_PROXY     => $host,
		CURLOPT_PROXYTYPE => $CURL_TYPE_MAP[$type],
	];
	if ($port) {
		$curl_option[CURLOPT_PROXYPORT] = (int)$port;
	}
	if ($account) {
		$curl_option[CURLOPT_PROXYUSERPWD] = $account . ($password ? ':' . $password : '');
	}
	return $curl_option;
}

/**
 * Get CURL default options
 * @return array
 */
function curl_get_default_option(array $ext_option = []){
	return curl_option_merge($GLOBALS[CURL_DEFAULT_OPTION_GLOBAL_KEY], $ext_option);
}

/**
 * Merge curl options, especially handle the duplicate parts in CURLOPT_HTTPHEADER
 * @param array $old_option
 * @param array $new_option
 * @return array
 */
function curl_option_merge(array $old_option, array $new_option){
	if ($old_option[CURLOPT_HTTPHEADER] && $new_option[CURLOPT_HTTPHEADER]) {
		$old = curl_convert_http_header_to_assoc($old_option[CURLOPT_HTTPHEADER]);
		$new = curl_convert_http_header_to_assoc($new_option[CURLOPT_HTTPHEADER]);
		$old_option[CURLOPT_HTTPHEADER] = array_merge_assoc($old, $new);
		unset($new_option[CURLOPT_HTTPHEADER]);
	}
	return array_merge_assoc($old_option, $new_option, true);
}

/**
 * Convert http header array to associative array to facilitate modification operations
 * @param array $headers
 * @return array
 */
function curl_convert_http_header_to_assoc($headers){
	$ret = [];
	foreach ($headers as $key => $val) {
		//修正索引型header成索引数组
		if (is_numeric($key) && preg_match('/(.*?):\s*(.*)$/', $val, $matches)) {
			$ret[$matches[1]] = $matches[2];
		} else {
			$ret[$key] = $val;
		}
	}
	return $ret;
}

/**
 * Set default options for curl_* operations
 * @param array $curl_option
 * @param bool $patch Whether to add in append mode, the default is to overwrite
 */
function curl_set_default_option(array $curl_option, $patch = false){
	$default = $patch ? curl_get_default_option() : [];
	$GLOBALS[CURL_DEFAULT_OPTION_GLOBAL_KEY] = curl_option_merge($default, $curl_option);
}

/**
 * Get CURL instance object
 * @param string $url
 * @param array $ext_curl_option curl option, additional default options will be added through curl_default_option()
 * @return array(resource, $curl_option)
 * @throws \Exception
 */
function curl_instance($url, array $ext_curl_option = []){
	$ch = curl_init();
	$curl_option = curl_get_default_option($ext_curl_option);
	$curl_option[CURLOPT_URL] = $url ?: $curl_option[CURLOPT_URL];

	//Supplementary support for https:// protocol
	if (stripos($curl_option[CURLOPT_URL], 'https://') === 0) {
		$curl_option[CURLOPT_SSL_VERIFYPEER] = 0;
		$curl_option[CURLOPT_SSL_VERIFYHOST] = 1;
	}

	//Correct the HTTP header. If the key => value array is passed in, convert it into a string array.
	if ($curl_option[CURLOPT_HTTPHEADER] && is_assoc_array($curl_option[CURLOPT_HTTPHEADER])) {
		$tmp = [];
		foreach ($curl_option[CURLOPT_HTTPHEADER] as $field => $val) {
			$tmp[] = "$field: $val";
		}
		$curl_option[CURLOPT_HTTPHEADER] = $tmp;
	}

	//Set default parameters
	if ($curl_option[CURLOPT_FOLLOWING_COOKIE_FILE]) {
		$curl_option[CURLOPT_COOKIEJAR] = $curl_option[CURLOPT_FOLLOWING_COOKIE_FILE]; //A file to save cookie information after the connection is completed.
		$curl_option[CURLOPT_COOKIEFILE] = $curl_option[CURLOPT_FOLLOWING_COOKIE_FILE]; //The file name containing cookie data. The format of the cookie file can be Netscape format, or just pure HTTP header information can be stored in the file.
	}

	if ($curl_option[CURLOPT_TIMEOUT] && get_max_socket_timeout() < $curl_option[CURLOPT_TIMEOUT]) {
		//warning timeout setting no taking effect
		error_log('warning timeout setting no taking effect as get_max_socket_timeout() more larger.');
	}

	//Ignore custom options
	foreach($curl_option as $k => $item){
		if(strpos($k, __NAMESPACE__) === false){
			if(!is_numeric($k)){
				throw new Exception('curl option no support:'.$k);
			}
			if(!@curl_setopt($ch, $k, $item)){
				$curl_option_map = curl_option_map();
				if(!isset($curl_option_map[$k])){
					throw new Exception('curl option key no support:'.$k);
				}
				throw new Exception('curl set option fail:'.json_encode(curl_print_option([$k => $item], true), JSON_UNESCAPED_UNICODE));
			}
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
	if (is_scalar($data)) {
		return (string)$data;
	}
	if (is_array($data)) {
		$d = [];
		if (is_assoc_array($data)) {
			foreach ($data as $k => $v) {
				if (is_null($v)) {
					continue;
				}
				if (is_scalar($v)) {
					$d[] = urlencode($k) . '=' . urlencode($v);
				} else {
					throw new Exception('Data type no support(more than 3 dimension array no supported)');
				}
			}
		} else {
			$d += $data;
		}
		return join('&', $d);
	}
	throw new Exception('Data type no supported');
}

/**
 * print curl option
 * @param array $options
 * @param bool $as_return
 * @return array|null
 */
function curl_print_option($options, $as_return = false){
	$prints = [];
	$option_map = curl_option_map();
	foreach($options as $key => $val){
		if(isset($option_map[$key])){
			$prints[$option_map[$key]] = $val;
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
 * get all curl_option map
 * @return array [OPT_VAL=>const text, ...]
 */
function curl_option_map(){
	static $option_map;
	if(!$option_map){
		$all_const_list = get_defined_constants();
		foreach($all_const_list as $text => $v){
			if(stripos($text, 'CURLOPT_') === 0){
				$option_map[$v] = $text;
			}
		}
	}
	return $option_map;
}

/**
 * convert curl option to standard request header
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
	foreach ($options as $opt => $mix_values) {
		switch ($opt) {
			case $simple_mapping[$opt]:
				$headers[$simple_mapping[$opt]] = $mix_values;
				break;
			case CURLOPT_URL:
				$url_info = parse_url($options[CURLOPT_URL]);
				$headers['Host'] = $url_info['host'];
				$headers['Origin'] = $url_info['scheme'] . "://" . $url_info['host'];
				break;
			case CURLOPT_POST:
				$headers['Content-Type'] = 'application/x-www-form-urlencoded';
				break;
			case CURLOPT_HTTPHEADER:
				foreach ($mix_values as $mv) {
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
 * Convert the request link into a closure function
 * @param string[]|array[] $urls request link array
 * @param array $ext_curl_option curl option array
 * @return \Closure
 */
function curl_urls_to_fetcher($urls, $ext_curl_option = []){
	$options = [];
	//One-dimensional array
	if (count($urls) === count($urls, true)) {
		foreach ($urls as $url) {
			$ext_curl_option[CURLOPT_URL] = $url;
			$options[] = $ext_curl_option;
		}
	} //Two-dimensional array, treated as CURL OPTION
	else {
		foreach ($urls as $opt) {
			$options[] = curl_option_merge($opt, $ext_curl_option);
		}
	}
	return function () use (&$options) {
		return array_shift($options);
	};
}

/**
 * Cut CURL result string
 * @param resource $ch
 * @param string $raw_string
 * @return string[] head,body
 */
function curl_cut_raw($ch, $raw_string){
	if (!$raw_string) {
		return ['', ''];
	}
	$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	$head = substr($raw_string, 0, $header_size);
	$body = substr($raw_string, $header_size);
	return [$head, $body];
}

/**
 * Get curl error information
 * @param int $error_no
 * @return string Empty indicates success
 */
function curl_error_message($error_no){
	if(!$error_no){
		return '';
	}
	return CURL_ERROR_MAP[$error_no] ?: "curl error happens($error_no).";
}

/**
 * Determine whether curl_query is successful
 * @param array $query_result curl_query returns the standard structure
 * @param string $error
 * @param bool $allow_empty_body allows body to be empty
 * @return bool
 */
function curl_query_success($query_result, &$error = '', $allow_empty_body = false){
	if($query_result['error']){
		$error = $query_result['error'];
	}
	if (!$error && $query_result['info']['http_code'] != 200) {
		$error = 'http code error:' . $query_result['info']['http_code'];
	}
	if (!$error && !$allow_empty_body && !strlen($query_result['body'])) {
		$error = 'body empty';
	}
	return !$error;
}

/**
 * Parse json objects from curl_query results
 * @param array $query_result curl_query returns the standard structure
 * @param mixed $ret return result
 * @param string $error error message
 * @param bool $force_array
 * @return bool whether successful
 * @example
 * if(!curl_query_json_success($ret, $data, $error)){
 * die($error);
 * }
 * $msg = array_get($data, 'message');
 */
function curl_query_json_success($query_result, &$ret = null, &$error = '', $force_array = true){
	if (!curl_query_success($query_result, $error)) {
		return false;
	}
	$tmp = @json_decode($query_result['body'], true);
	if (json_last_error()) {
		$error = json_last_error_msg() . ' string:' . $query_result['body'];
		return false;
	}
	if ($force_array && !is_array($tmp)) {
		$error = 'return format error:' . gettype($tmp);
		return false;
	}
	$ret = $tmp;
	return true;
}

/**
 * CURL concurrent requests
 * Note: The callback function needs to be processed as soon as possible to avoid blocking subsequent request processes.
 * @param callable|array $curl_option_fetcher array Returns the curl option mapping array. Even if there is only one url, [CURLOPT_URL=>$url] needs to be returned.
 * @param callable|null $on_item_start ($curl_option) Start executing the callback. If false is returned, the task is ignored.
 * @param callable|null $on_item_finish ($curl_ret, $curl_option) Request end callback, parameter 1: return result array, parameter 2: curl option
 * @param int $rolling_window Number of rolling requests
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
	 * Add tasks
	 * @param int $count add quantity
	 * @return int The number of tasks added, -1 means there are no tasks, 0 may be due to onstart interruption.
	 * @throw an exception
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
	 * fetch result
	 * @return void
	 */
	$get_result = function () use ($add_task, $rolling_window, $mh, $on_item_finish, &$running_count, &$tmp_option_cache) {
		//Dispose of all completed tasks, curl_multi_info_read executes one read at a time
		while ($curl_result = curl_multi_info_read($mh)) {
			$ch = $curl_result['handle'];
			$resource_id = (int)$ch;

			$ret = [];
			$ret['info'] = curl_getinfo($ch);
			$curl_option = $tmp_option_cache[$resource_id];

			$raw_string = curl_multi_getcontent($ch);
			[$ret['head'], $ret['body']] = curl_cut_raw($ch, $raw_string);

			//Handle transcoding
			if (isset($curl_option[CURLOPT_PAGE_ENCODING])) {
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
		//no more task & running count is 0
		if($added_count === -1 && $running_count == 0){
			break;
		}

		$state = curl_multi_exec($mh, $running_count);
		curl_multi_select($mh, 0.1);
		$get_result();
	} while($state === CURLM_OK);
	curl_multi_close($mh);
	return true;
}
