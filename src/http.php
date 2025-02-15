<?php
/**
 * HTTP Enhancement Function
 */
namespace LFPhp\Func;

use Exception;

//HTTP status code translation
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
	302 => 'Moved Temporarily',
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

//HTTP Method definition. Since HTTP_METH_GET is in the http extension, it is not convenient to use. Here is a simple definition
const HTTP_METHOD_GET = 'GET';
const HTTP_METHOD_HEAD = 'HEAD';
const HTTP_METHOD_POST = 'POST';
const HTTP_METHOD_PUT = 'PUT';
const HTTP_METHOD_DELETE = 'DELETE';
const HTTP_METHOD_OPTIONS = 'OPTIONS';
const HTTP_METHOD_TRACE = 'TRACE';
const HTTP_METHOD_CONNECT = 'CONNECT';
const HTTP_METHOD_PROPFIND = 'PROPFIND';
const HTTP_METHOD_PROPPATCH = 'PROPPATCH';
const HTTP_METHOD_MKCOL = 'MKCOL';
const HTTP_METHOD_COPY = 'COPY';
const HTTP_METHOD_MOVE = 'MOVE';
const HTTP_METHOD_LOCK = 'LOCK';
const HTTP_METHOD_UNLOCK = 'UNLOCK';
const HTTP_METHOD_VERSION_CONTROL = 'VERSION_CONTROL';
const HTTP_METHOD_REPORT = 'REPORT';
const HTTP_METHOD_CHECKOUT = 'CHECKOUT';
const HTTP_METHOD_CHECKIN = 'CHECKIN';
const HTTP_METHOD_UNCHECKOUT = 'UNCHECKOUT';
const HTTP_METHOD_MKWORKSPACE = 'MKWORKSPACE';
const HTTP_METHOD_UPDATE = 'UPDATE';
const HTTP_METHOD_LABEL = 'LABEL';
const HTTP_METHOD_MERGE = 'MERGE';
const HTTP_METHOD_BASELINE_CONTROL = 'BASELINE_CONTROL';
const HTTP_METHOD_MKACTIVITY = 'MKACTIVITY';
const HTTP_METHOD_ACL = 'ACL';

/**
 * Send HTTP status code
 * @param int $status http status code
 * @return bool
 */
function http_send_status($status){
	$message = http_get_status_message($status);
	if(!headers_sent() && $message){
		if(substr(php_sapi_name(), 0, 3) == 'cgi'){//CGI mode
			header("Status: $status $message");
		}else{ //FastCGI mode
			header("{$_SERVER['SERVER_PROTOCOL']} $status $message");
		}
		return true;
	}
	return false;
}

/**
 * Enable httpd server chunk output
 */
function http_chunk_on(){
	@ob_end_clean(); //Force PHP to output content directly to the browser without adding buffer
	ob_implicit_flush(true); //Set nginx or apache to not buffer and output directly
	header('X-Accel-Buffering: no'); //The key is to add this line.
}

/**
 * Return cross-domain CORS header information
 * @param string[] $allow_hosts List of domain names allowed to pass through, empty means all source domain names are allowed
 * @param string $http_origin origin request, format: http://www.abc.com, by default obtained from HTTP_ORIGIN or HTTP_REFERER
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
	if (headers_sent()) {
		throw new Exception('header already sent');
	}
	header("Access-Control-Allow-Origin: $http_scheme://$request_host");
	header('Access-Control-Allow-Credentials: true');
	header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
}

/**
 * Send HTTP header character set
 * @param string $charset
 * @return bool whether it is successful
 */
function http_send_charset($charset){
	if (!headers_sent()) {
		header('Content-Type:text/html; charset='.$charset);
		return true;
	}
	return false;
}

/**
 * Get the corresponding description of the HTTP status code
 * @param int $status
 * @return string|null
 */
function http_get_status_message($status){
	return HTTP_STATUS_MESSAGE_MAP[$status];
}

/**
 * HTTP redirect
 * @param string $url jump path
 * @param bool $permanently whether it is a long-term resource redirection
 */
function http_redirect($url, $permanently = false){
	http_send_status($permanently ? 301 : 302);
	header('Location:'.$url);
}

/**
 * Get HTTP request header information array
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
 * Get the specified key value in the HTTP request header
 * @param string $key case-insensitive
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
 * Parse http header information
 * @param $header_str
 * @return array
 */
function http_parse_headers($header_str){
	$headers = [];
	foreach(explode("\n", $header_str) as $h){
		[$k, $v] = explode(':', $h, 2);
		//Since HTTP HEADER has no case constraints, in order to avoid irregular data input, all the data is formatted in lower case
		$k = strtolower($k);
		if(isset($v)){
			if (!isset($headers[$k])){
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
 * Determine whether the request method is JSON
 * @return bool
 */
function http_from_json_request(){
	return http_get_content_type() == 'application/json';
}

/**
 * Get the content-type in the http request header
 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Type#directives
 * @param array $directives additional directives, such as ['charset'=>'utf-8'], or ['boundary'=>'ExampleBoundaryString']
 * @return string
 */
function http_get_content_type(&$directives = []){
	$type_str = http_get_request_header('Content-Type');
	[$media_type, $directives_str] = explode_by(';', $type_str);
	if($directives_str && preg_match('/(\w+)=(.*)$/', $directives_str, $matches)){
		$directives[$matches[1]] = $matches[2];
	}
	return strtolower($media_type);
}

/**
 * Determine whether the request is received in SON format
 * @param bool $include_generic_match Whether to support generic matching. Since the client may not strictly process the format of the request, this option is generally not enabled.
 * @return bool
 */
function http_request_accept_json($include_generic_match = false){
	$str = http_get_request_header('Accept');
	$tmp = http_parse_string_use_q_value($str);
	$accept_list = array_column($tmp, 'type');
	return in_array('application/json', $accept_list) ||
		($include_generic_match && in_array('*/*', $accept_list)); //generic match
}

/**
 * Parse http header information in a weighted manner, Accept, Accept-Encoding, Accept-Language, TE, Want-Digest
 * @see https://developer.mozilla.org/en-US/docs/Glossary/Quality_values
 * @param $str
 * @return array [[type, q], ...]
 */
function http_parse_string_use_q_value($str){
	$accepts = explode(',', $str);
	$result = [];
	foreach ($accepts as $accept) {
		$parts = explode(';', trim($accept));
		$type = $parts[0];
		$q = 1.0; // default weight
		if (isset($parts[1]) && strpos($parts[1], 'q=') === 0) {
			$q = floatval(substr($parts[1], 2));
		}
		$result[] = ['type' => strtolower($type), 'q' => $q];
	}
	usort($result, function($a, $b) {
		return $b['q'] <=> $a['q'];
	});
	return $result;
}

/**
 * Request comes from POST
 * @return bool
 */
function request_in_post(){
	return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Request comes from GET
 * @return bool
 */
function request_in_get(){
	return $_SERVER['REQUEST_METHOD'] === 'GET';
}

/**
 * Get the current page address
 * @param bool $with_protocol whether to include the protocol header
 * @return string
 */
function http_get_current_page_url($with_protocol = true, $with_port = false){
	return http_get_current_host($with_protocol, $with_port).$_SERVER['REQUEST_URI'];
}

/**
 * Get the domain name of the current page
 * @param bool $with_protocol whether to include the protocol header: http, https
 * @param bool $with_port whether to include the port (valid only for non-http:80, https:443)
 * @return string such as http://www.abc.com http://www.abc.com:81 does not contain a slash at the end
 */
function http_get_current_host($with_protocol = true, $with_port = false){
	$server_port = $_SERVER['SERVER_PORT'];
	$port_str = '';
	if($with_port && ((server_in_https() && $server_port != 443) || (!server_in_https() && $server_port != 80))){
		$port_str = ':'.$server_port;
	}
	$protocol_str = $with_protocol ? (server_in_https() ? 'https:' : 'http:') : '';
	return $protocol_str.'//'.$_SERVER['HTTP_HOST'].$port_str;
}

/**
 * Download files by file streaming
 * @param string $file file path
 * @param string $download_name Download file name
 * @param string $disposition header type
 * @return false|int Successfully downloaded file size, false means failed
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
 * Response json data
 * @param mixed $json
 * @param int $json_option
 * @return void
 */
function http_json_response($json, $json_option = JSON_UNESCAPED_UNICODE){
	http_header_json_response();
	echo json_encode($json, $json_option);
}

/**
 * Response JSON return header
 * @param string $charset
 */
function http_header_json_response($charset = 'utf-8'){
	header('Content-Type: application/json;'.($charset ? " charset=$charset" : ''));
}

/**
 * Send file download header information
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
 * Send CSP header
 * @param string[] $csp_rules It is recommended to use the rules generated by the csp_content_rule() method
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
 * Send browser settings Report API
 * @param string[] $endpoint_urls
 * @param string $group
 * @param number $max_age_sec
 * @param bool $include_subdomains
 */
function http_header_report_api(array $endpoint_urls, $group = 'default', $max_age_sec = ONE_DAY, $include_subdomains = true){
	header('Report-To: '.json_encode(generate_report_api($endpoint_urls, $group, $max_age_sec, $include_subdomains)));
}

/**
 * Send browser error logs to Report API
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
 * Generate Report API
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
	}
	return [
		'group' => $group,
		'max_age' => $max_age_sec,
		'include_subdomains' => $include_subdomains,
		'endpoints' => $endpoints_obj,
	];
}

/**
 * Parse the cookie string into a hash array
 * @param string $cookie_str
 * @return array
 */
function http_parse_cookie($cookie_str){
	parse_str(strtr($cookie_str, array('&' => '%26', '+' => '%2B', ';' => '&')), $cookies);
	return $cookies;
}

/**
 * Correct relative URL to absolute URL
 * @param string $url
 * @param string $base_url base url (such as page url)
 * @return string|string[]
 */
function http_fix_relative_url($url, $base_url){
	if(is_array($url)){
		foreach($url as $k => $u){
			$url[$k] = http_fix_relative_url($u, $base_url);
		}
		return $url;
	}

	//The url already contains a schema, or uses a relative protocol [//], so no further matching is required
	if (parse_url($url, PHP_URL_SCHEME) != '' || substr($url, 0, 2) == '//'){
		return $url;
	}

	//Query statement, or anchor
	if ($url[0]=='#' || $url[0]=='?'){
		return $base_url.$url;
	}

	// [../] is corrected to [./]
	if(strpos($url, '../') === 0){
		$url = './'.$url;
	}

	/* parse base URL and convert to local variables:
	$scheme, $host, $path */
	extract(parse_url($base_url));

	/* remove non-directory element from path */
	$path = preg_replace('#/[^/]*$#', '', $path);

	/* destroy path if relative url points to root */
	if ($url[0] == '/') $path = '';

	/* dirty absolute URL */
	$abs = "$host$path/$url";

	/* replace '//' or '/./' or '/foo/../' with '/' */
	$re = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');
	for($n=1; $n>0; $abs=preg_replace($re, '/', $abs, -1, $n)) {}

	/* absolute URL is ready! */
	return $scheme.'://'.$abs;
}
