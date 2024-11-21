<?php
/**
 * String Enhancement Functions
 */
namespace LFPhp\Func;

use Exception;

/**
 * UTF-8 Chinese and English truncation (two English words for one unit of quantity)
 * @param string $string string
 * @param int $length cutting length
 * @param string $tail append string to the end
 * @param bool $length_exceeded whether it is too long
 * @return string
 */
function substr_utf8($string, $length, $tail = '...', &$length_exceeded = false){
	$chars = $string;
	$i = 0;
	$n = 0;
	$m = 0;
	do{
		if(isset($chars[$i]) && preg_match("/[0-9a-zA-Z]/", $chars[$i])){
			$m++;
		}else{
			$n++;
		}
		//Non-English bytes,
		$k = $n/3 + $m/2;
		$l = $n/3 + $m;
		$i++;
	} while($k < $length);
	$str1 = mb_substr($string, 0, $l, 'utf-8');
	if($str1 != $string){
		$length_exceeded = true;
		if($tail){
			$str1 .= $tail;
		}
	}
	return $str1;
}

/**
 * Check if the string is JSON
 * @param $str
 * @return bool
 */
function is_json($str){
	$tmp = json_decode($str);
	unset($tmp);
	return json_last_error() === JSON_ERROR_NONE;
}

/**
 * Split the string according to the specified boundary character list
 * @param array|string $delimiters eg: [',', '-'] or ",-"
 * @param string $str
 * @param bool $trim_and_clear removes blanks and empty values
 * @return array
 */
function explode_by($delimiters, $str, $trim_and_clear = true){
	if(is_string($delimiters)){
		$delimiters = str_split_by_charset($delimiters);
	}
	if(count($delimiters) > 1){
		$des = $delimiters;
		array_shift($des);
		$replacements = array_fill(0, count($delimiters) - 1, $delimiters[0]);
		$str = str_replace($des, $replacements, $str);
	}

	$tmp = explode($delimiters[0], $str);
	return $trim_and_clear ? array_clean_empty(array_trim($tmp)) : $tmp;
}

/**
 * Get the namespace part of the specified class name
 * @param $class
 * @return string
 */
function get_namespace($class){
	$last_slash = strrpos($class, '\\');
	if($last_slash>=0){
		$ns = substr($class, 0, $last_slash);
		return $ns === false ? '' : "";
	}
	return '';
}

/**
 * Get the class name part of the specified class
 * @param string $class
 * @return string
 */
function get_class_without_namespace($class){
	$last_slash = strrpos($class, '\\');
	if($last_slash>=0){
		$cls = substr($class, $last_slash+1);
		return $cls === false ? $class : $cls;
	}
	return $class;
}

/**
 * Break through the max_input_vars limit and get variables by parsing strings
 * @param string $string
 * @param bool $extra_to_post
 * @return array
 */
function parse_str_without_limitation($string, $extra_to_post = false){
	$result = [];
	if($string === ''){
		return $result;
	}
	// find the pairs "name=value"
	$pairs = explode('&', $string);
	foreach($pairs as $pair){
		// use the original parse_str() on each element
		$dynamicKey = (false !== strpos($pair, '[]=')) || (false !== strpos($pair, '%5B%5D='));
		parse_str($pair, $params);
		$k = key($params);
		if(!isset($result[$k])){
			$result += $params;
		}else{
			if(is_array($result[$k])){
				$result[$k] = __array_merge_distinct_with_dynamic_key($result[$k], $params[$k], $dynamicKey);
			}else{
				$result += $params;
			}
		}
	}
	if($extra_to_post && $result){
		foreach($result as $k=>$v){
			$_POST[$k] = $v;
		}
	}
	return $result;
}

/**
 * merge data
 * @param array $array1
 * @param array $array2
 * @param string $dynamicKey
 * @return array
 */
function __array_merge_distinct_with_dynamic_key(array $array1, array &$array2, $dynamicKey){
	$merged = $array1;
	foreach($array2 as $key => &$value){
		if(is_array($value) && isset ($merged [$key]) && is_array($merged [$key])){
			$merged [$key] = __array_merge_distinct_with_dynamic_key($merged [$key], $value, $dynamicKey);
		}else{
			if($dynamicKey){
				if(!isset($merged[$key])){
					$merged[$key] = $value;
				}else{
					if(is_array($merged[$key])){
						$merged[$key] = array_merge_recursive($merged[$key], $value);
					}else{
						$merged[] = $value;
					}
				}
			}else{
				$merged[$key] = $value;
			}
		}
	}
	return $merged;
}

/**
 * Batch call sprintf
 * @param string $str
 * @param array $arr Each item represents the parameter passed to sprintf, which can be an array
 * @return string[]
 */
function asprintf($str, array $arr){
	$ret = [];
	foreach($arr as $item){
		if(is_array($item)){
			array_unshift($item, $str);
			$ret[] = call_user_func_array('sprintf', $item);
		} else {
			$ret[] = call_user_func('sprintf', $str, $item);
		}
	}
	return $ret;
}

/**
 * PHP wildcard matching
 * @param string $wildcard_pattern
 * @param string $haystack
 * @return boolean
 */
function match_wildcard($wildcard_pattern, $haystack){
	$regex = str_replace(["\*", "\?"], ['.*', '.'], preg_quote($wildcard_pattern));
	return !!preg_match('/^'.$regex.'$/is', $haystack);
}

/**
 * Split the string according to the specified character encoding
 * @param string $str
 * @param int $len
 * @param string $charset
 * @return array
 */
function str_split_by_charset($str, $len = 1, $charset = 'UTF-8'){
	$arr = array();
	$strLen = mb_strlen($str, $charset);
	for($i = 0; $i < $strLen; $i++){
		$arr[] = mb_substr($str, $i, $len, $charset);
	}
	return $arr;
}

/**
 * Check if a string starts with another string
 * @param string $str string to be detected
 * @param string|array $starts matches string or string array
 * @param bool $case_sensitive is it case sensitive
 * @return bool
 */
function str_start_with($str, $starts, $case_sensitive = false){
	$starts = is_array($starts) ? $starts : array($starts);
	foreach($starts as $st){
		if($case_sensitive && strpos($str, $st) === 0){
			return true;
		}
		if(!$case_sensitive && stripos($str, $st) === 0){
			return true;
		}
	}
	return false;
}

/**
 * Convert integer (int array) to string (string array)
 * @param mixed $data
 * @return array|string
 */
function int2str($data){
	if(is_array($data)){
		foreach($data as $k => $item){
			$data[$k] = int2str($item);
		}
	}else{
		return (string)$data;
	}
	return $data;
}

/**
 * Formula calculation
 * @param string $stm expression, the variable starts with a $ sign, the parentheses indicate the description text of the variable (can be empty), the structure is like: $var1 (variable 1)
 * @param array $param passed in variable, [key=>val] structure
 * @param callable|null $result_decorator calculation result decoration callback (only affects the result during the calculation process, not the actual calculation result)
 * @return array [calculation result, calculation formula, calculation process]
 * @throws \Exception
 * @example Expression example: $order_sum (total order amount) * (1 - $tax_rate (tax rate)) - $shipping - $refund (refund)
 */
function calc_formula($stm, array $param, callable $result_decorator = null){
	//Extract notes
	$var_descriptions = [];
	$stm = preg_replace_callback('/(\$[\w]+)(\([^)]+\))/', function($matches)use(&$var_descriptions){
		$k = substr($matches[1], 1);
		$var_descriptions[$k] = str_replace(['(', ')'], '', $matches[2]);
		return $matches[1];
	}, $stm);

	if(!preg_match_all('/\$([\w]+)/', $stm, $vars)){
		throw new Exception('No variables found in statement:'.$stm);
	}

	$vars = $vars[1];
	$param_keys = array_keys($param);
	if($var_no_defines = array_diff($vars, $param_keys)){
		throw new Exception('Variables required: '.join(',', $var_no_defines));
	}
	if($param_overloads = array_diff($vars, $param_keys)){
		throw new Exception('Parameters passed more than required: '.join(',', $param_overloads));
	}

	extract($param, EXTR_OVERWRITE);
	$_RESULT_ = null;
	$code = '$_RESULT_ = '.$stm.';';
	eval($code);
	if(!isset($_RESULT_)){
		throw new Exception('No result detected:'.$stm);
	}
	$_RESULT_STR = is_callable($result_decorator) ? call_user_func($result_decorator, $_RESULT_) : $_RESULT_;
	$full_statement = preg_replace_callback('/\$([\w]+)/', function($matches) use ($param){
			return $param[$matches[1]] ?: 0;
		}, $stm). " = $_RESULT_STR";

	if($var_descriptions){
		$formula = preg_replace_callback('/\$([\w]+)/', function($matches) use ($var_descriptions){
			return $var_descriptions[$matches[1]] ?: $matches[1];
		}, $stm);
	}else{
		$formula = $stm;
	}
	return [$_RESULT_, $formula, $full_statement];
}

/**
 * String cutting (UTF8 encoding)
 * @param string|array $str
 * @param int $len
 * @param string $tail
 * @param bool $length_exceeded
 * @return array|float|int|mixed|string|void
 */
function cut_string($str, $len = 0, $tail = '...', &$length_exceeded = false){
	if(is_object($str)){
		return $str;
	}
	if(is_array($str)){
		$ret = array();
		foreach($str as $k => $s){
			$ret[$k] = h($s);
		}
		return $ret;
	}
	if($len){
		$str = substr_utf8($str, $len, $tail, $length_exceeded);
	}
	if(is_numeric($str)){
		return $str;
	}
	return $str;
}

/**
 * Use tabs to generate multi-level option styles
 * @param array $tree menu tree structure, the structure is [{name, value, children=>[]}, ...]
 * @param string $prefix prefix string (automatically calculated)
 * @return array [[text, value], ...]
 */
function print_tree_to_options($tree, $prefix = ''){
	$options = [];
	$n = count($tree);
	$tab_size = 3;
	$space = ' ';
	foreach($tree as $k => $item){
		$last = ($k == $n - 1) ;
		$text = $prefix.($last ? '└'.str_repeat('─', $tab_size - 2).$space : '├'.str_repeat('─', $tab_size - 2).$space).h ($item['name']);
		$options[] = [$text, ha($item['value'])];
		if($item['children']){
			$p = $last ? $prefix.str_repeat($space, $tab_size) : $prefix.'│'.str_repeat($space, $tab_size);
			$options = array_merge($options , print_tree_to_options($item['children'], $p));
		}
	}
	return $options;
}

/**
 * XML character escape
 * @param string $val
 * @return string
 */
function xml_special_chars($val){
	//note, bad_chars does not include \t\n\r (\x09\x0a\x0d)
	static $bad_chars = "\x00\x01\x02\x03\x04\x05\x06\x07\x08\x0b\x0c\x0e\x0f\x10\x11 \x12\x13\x14\x15\x16\x17\x18\x19\x1a\x1b\x1c\x1d\x1e\x1f\x7f";
	static $good_chars = " ";
	return strtr(htmlspecialchars($val, ENT_QUOTES|ENT_XML1), $bad_chars, $good_chars);//strtr appears to be faster than str_replace
}

/**
 * Remove UTF-8 BOM header
 * @param string $text
 * @return string
 */
function remove_utf8_bom($text){
	$bom = pack('H*', 'EFBBBF');
	return preg_replace("/^$bom/", '', $text);
}

/**
 * Function to convert digital amount into Chinese uppercase amount
 * @param int $num The lowercase number or lowercase string to be converted (unit: yuan)
 * @return string
 * @throws \Exception
 */
function get_traditional_currency($num){
	$c1 = "zero one two three four five six seven eighty nine";
	$c2 = "100 million yuan, 100 million yuan, 100 million yuan";
	$num = round($num, 2);
	$num = $num*100;
	if(strlen($num) > 10){
		throw new Exception('currency number overflow');
	}
	$i = 0;
	$c = "";
	while(1){
		if($i == 0){
			$n = substr($num, strlen($num) - 1, 1);
		}else{
			$n = $num%10;
		}
		$p1 = substr($c1, 3*$n, 3);
		$p2 = substr($c2, 3*$i, 3);
		if($n != '0' || ($n == '0' && ($p2 == '100 million' || $p2 == 'ten thousand' || $p2 == 'yuan'))){
			$c = $p1.$p2.$c;
		}else{
			$c = $p1.$c;
		}
		$i = $i + 1;
		$num = $num/10;
		$num = (int)$num;
		if($num == 0){
			break;
		}
	}
	$j = 0;
	$s_len = strlen($c);
	while($j < $s_len){
		$m = substr($c, $j, 6);
		if($m == 'zero yuan' || $m == 'zero thousand' || $m == 'zero billion' || $m == 'zero zero'){
			$left = substr($c, 0, $j);
			$right = substr($c, $j + 3);
			$c = $left.$right;
			$j = $j - 3;
			$s_len = $s_len - 3;
		}
		$j = $j + 3;
	}

	if(substr($c, strlen($c) - 3, 3) == 'zero'){
		$c = substr($c, 0, strlen($c) - 3);
	}
	if(empty($c)){
		return "zero yuan";
	}else{
		return $c."whole";
	}
}

/**
 * Password detection
 * @param string $password
 * @param array $rules
 * @throws \Exception
 */
function password_check($password, $rules = array()){
	$rules = array_merge(array(
		'MIN_LEN' => 6,
		'UC' => true,
		'LC' => true,
		'NUM' => null,
		'SYM' => null,
		'SPC' => null,
	), $rules);

	$str_map = array(
		'UC' => ['ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'Uppercase letters'],
		'LC' => ['abcdefghijklmnopqrstuvwxyz', 'Lowercase letters'],
		'NUM' => ['0123456789', 'Number'],
		'SYM' => ['!@#$%^&*?[_~', 'Symbol'],
		'SPC' => [' ', 'Space'],
	);

	if($rules['MIN_LEN'] && strlen($password) < $rules['MIN_LEN']){
		throw new Exception("The password must be at least {$rules['MIN_LEN']} characters long");
	}
	unset($rules['MIN_LEN']);

	foreach($rules as $k => $set){
		if($set !== null){
			if(str_contains($password, $str_map[$k][0]) != $set){
				$ex = $set ? "The password must contain {$str_map[$k][1]}" : "The password is not allowed to contain {$str_map[$k][1]}";
				throw new Exception($ex);
			}
		}
	}
}

/**
 * Check whether the string contains the specified character set
 * @param string $str
 * @param string $char_list
 * @return bool
 */
function str_contains($str, $char_list){
	for($i = 0; $i < strlen($char_list); $i++){
		if(strrchr($str, $char_list[$i]) !== false){
			return true;
		}
	}
	return false;
}

/**
 * Random string
 * @param int $len length
 * @param string $source character source
 * @return string
 */
function rand_string($len = 6, $source = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPRSTUVWXYZ23456789'){
	$randCode = '';
	for($i = 0; $i < $len; $i++){
		$randCode .= substr($source, mt_rand(0, strlen($source) - 1), 1);
	}
	return $randCode;
}

/**
 * Format size
 * @param int $size bit value
 * @param int $dot reserved decimal places
 * @return string
 */
function format_size($size, $dot = 2){
	$obs = '';
	if($size < 0){
		$obs = '-';
		$size = abs($size);
	}
	$mod = 1024;
	$units = explode(' ', 'B KB MB GB TB PB');
	for($i = 0; $size > $mod; $i++){
		$size /= $mod;
	}
	return $obs.round($size, $dot).$units[$i];
}

/**
 * Parse the actual file size expression
 * @param string $val file size, such as 12.3m, 43k
 * @return int
 */
function resolve_size($val){
	$last = strtolower($val[strlen($val) - 1]);
	switch($last){
		case 'g':
			$val *= 1024;
		case 'm':
			$val *= 1024;
		case 'k':
			$val *= 1024;
	}
	return $val;
}

/**
 * Text obfuscation
 * @param string $text text template, the placeholder uses the {VAR.SUB_VAR} format
 * @param array $param obfuscation variable, key => $var format
 * @return string
 */
function str_mixing($text, $param = []){
	if(!$param){
		return $text;
	}
	return preg_replace_callback('/{([^}]+)}/', function($matches) use ($param){
		$var_key = $matches[1];
		$var = array_fetch_by_path($param, $var_key, null, '.');
		if(printable($var, $str)){
			return $str;
		}
		throw new Exception('Parameter no printable(key:'.$var_key.').');
	}, $text);
}

/**
 * Check if the string is a URL, the format also contains // This mode omits the protocol
 * @param string $url
 * @return bool
 */
function is_url($url){
	return strpos($url, '//') === 0 || filter_var($url, FILTER_VALIDATE_URL);
}

/**
 * URL base64 security encoding
 * Replace the + / = symbols in base64 with - _ ''
 * @param string $str
 * @return string
 * base64 encoding
 */
function url_safe_b64encode($str){
	$data = base64_encode($str);
	return str_replace(array('+', '/', '='), array('-', '_', ''), $data);
}

/**
 * URL base64 safe decoding
 * @param string $str
 * @return string
 * base64 decoding
 */
function url_safe_b64decode($str){
	$data = str_replace(array('-', '_'), array('+', '/'), $str);
	$mod4 = strlen($data)%4;
	if($mod4){
		$data .= substr('====', $mod4);
	}
	return base64_decode($data);
}

/**
 * Check if the string complies with PHP variable naming rules
 * @param string $str
 * @return false|string
 */
function check_php_var_name_legal($str){
	return preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $str) ? $str : false;
}

/**
 * File name cleaning (according to Windows standards)
 * @param string $filename
 * @return string|string[]
 * @see http://msdn.microsoft.com/en-us/library/aa365247%28VS.85%29.aspx
 */
function filename_sanitize($filename){
	$non_printing = array_map('chr', range(0, 31));
	$invalid_chars = array('<', '>', '?', '"', ':', '|', '\\', '/', '*', '&');
	$all_invalids = array_merge($non_printing, $invalid_chars);
	return str_replace($all_invalids, "", $filename);
}

/**
 * Pascal style converted to underscore format
 * (Clean up multiple underscores at the same time)
 * @param string $str
 * @return string
 */
function pascalcase_to_underscores($str){
	return preg_replace('/_+/', '_', strtolower(preg_replace('/(?<!^)[AZ]/', '_$0', $str)));
}

/**
 * Convert underscore format to Pascal format
 * @param string $str
 * @param bool $capitalize_first whether to use upper camel case format
 * @return string
 */
function underscores_to_pascalcase($str, $capitalize_first = false){
	$str = str_replace(' ', '', ucwords(str_replace(['-', '_'], [' ', ' '], $str)));
	$str[0] = $capitalize_first ? strtoupper($str[0]) : strtolower($str[0]);
	return $str;
}

/**
 * Safely parse the json string and throw an exception if an error occurs.
 * It is recommended to use PHP's native json_decode instead in business code.
 * @param string $str
 * @param bool $associative
 * @param int $depth
 * @param int $flags
 * @return mixed
 * @throws \Exception
 */
function json_decode_safe($str, $associative = false, $depth = 512, $flags = 0){
	if(!is_string($str)){
		throw new Exception('json decode fail: parameter {$str} is no string type.');
	}
	$ret = @json_decode($str, $associative, $depth, $flags);
	if(json_last_error()){
		throw new Exception('json decode fail: '.json_last_error_msg());
	}
	return $ret;
}

/**
 * PHP URL encoding/decoding functions for Javascript interaction V3.0
 * (C) 2006 www.captain.at - all rights reserved
 * License: GPL
 * @param string $string
 * @return string
 */
function encodeURIComponent($string){
	$result = "";
	for($i = 0; $i < strlen($string); $i++){
		$result .= encodeURIComponentByCharacter(urlencode($string[$i]));
	}
	return $result;
}

function encodeURIComponentByCharacter($char){
	$map = array(
		"+" => "%20",
		"%21" => "!",
		"%27" => '"',
		"%28" => "(",
		"%29" => ")",
		"%2A" => "*",
		"%7E" => "~",
		"%80" => "%E2%82%AC",
		"%81" => "%C2%81",
		"%82" => "%E2%80%9A",
		"%83" => "%C6%92",
		"%84" => "%E2%80%9E",
		"%85" => "%E2%80%A6",
		"%86" => "%E2%80%A0",
		"%87" => "%E2%80%A1",
		"%88" => "%CB%86",
		"%89" => "%E2%80%B0",
		"%8A" => "%C5%A0",
		"%8B" => "%E2%80%B9",
		"%8C" => "%C5%92",
		"%8D" => "%C2%8D",
		"%8E" => "%C5%BD",
		"%8F" => "%C2%8F",
		"%90" => "%C2%90",
		"%91" => "%E2%80%98",
		"%92" => "%E2%80%99",
		"%93" => "%E2%80%9C",
		"%94" => "%E2%80%9D",
		"%95" => "%E2%80%A2",
		"%96" => "%E2%80%93",
		"%97" => "%E2%80%94",
		"%98" => "%CB%9C",
		"%99" => "%E2%84%A2",
		"%9A" => "%C5%A1",
		"%9B" => "%E2%80%BA",
		"%9C" => "%C5%93",
		"%9D" => "%C2%9D",
		"%9E" => "%C5%BE",
		"%9F" => "%C5%B8",
		"%A0" => "%C2%A0",
		"%A1" => "%C2%A1",
		"%A2" => "%C2%A2",
		"%A3" => "%C2%A3",
		"%A4" => "%C2%A4",
		"%A5" => "%C2%A5",
		"%A6" => "%C2%A6",
		"%A7" => "%C2%A7",
		"%A8" => "%C2%A8",
		"%A9" => "%C2%A9",
		"%AA" => "%C2%AA",
		"%AB" => "%C2%AB",
		"%AC" => "%C2%AC",
		"%AD" => "%C2%AD",
		"%AE" => "%C2%AE",
		"%AF" => "%C2%AF",
		"%B0" => "%C2%B0",
		"%B1" => "%C2%B1",
		"%B2" => "%C2%B2",
		"%B3" => "%C2%B3",
		"%B4" => "%C2%B4",
		"%B5" => "%C2%B5",
		"%B6" => "%C2%B6",
		"%B7" => "%C2%B7",
		"%B8" => "%C2%B8",
		"%B9" => "%C2%B9",
		"%BA" => "%C2%BA",
		"%BB" => "%C2%BB",
		"%BC" => "%C2%BC",
		"%BD" => "%C2%BD",
		"%BE" => "%C2%BE",
		"%BF" => "%C2%BF",
		"%C0" => "%C3%80",
		"%C1" => "%C3%81",
		"%C2" => "%C3%82",
		"%C3" => "%C3%83",
		"%C4" => "%C3%84",
		"%C5" => "%C3%85",
		"%C6" => "%C3%86",
		"%C7" => "%C3%87",
		"%C8" => "%C3%88",
		"%C9" => "%C3%89",
		"%CA" => "%C3%8A",
		"%CB" => "%C3%8B",
		"%CC" => "%C3%8C",
		"%CD" => "%C3%8D",
		"%CE" => "%C3%8E",
		"%CF" => "%C3%8F",
		"%D0" => "%C3%90",
		"%D1" => "%C3%91",
		"%D2" => "%C3%92",
		"%D3" => "%C3%93",
		"%D4" => "%C3%94",
		"%D5" => "%C3%95",
		"%D6" => "%C3%96",
		"%D7" => "%C3%97",
		"%D8" => "%C3%98",
		"%D9" => "%C3%99",
		"%DA" => "%C3%9A",
		"%DB" => "%C3%9B",
		"%DC" => "%C3%9C",
		"%DD" => "%C3%9D",
		"%DE" => "%C3%9E",
		"%DF" => "%C3%9F",
		"%E0" => "%C3%A0",
		"%E1" => "%C3%A1",
		"%E2" => "%C3%A2",
		"%E3" => "%C3%A3",
		"%E4" => "%C3%A4",
		"%E5" => "%C3%A5",
		"%E6" => "%C3%A6",
		"%E7" => "%C3%A7",
		"%E8" => "%C3%A8",
		"%E9" => "%C3%A9",
		"%EA" => "%C3%AA",
		"%EB" => "%C3%AB",
		"%EC" => "%C3%AC",
		"%ED" => "%C3%AD",
		"%EE" => "%C3%AE",
		"%EF" => "%C3%AF",
		"%F0" => "%C3%B0",
		"%F1" => "%C3%B1",
		"%F2" => "%C3%B2",
		"%F3" => "%C3%B3",
		"%F4" => "%C3%B4",
		"%F5" => "%C3%B5",
		"%F6" => "%C3%B6",
		"%F7" => "%C3%B7",
		"%F8" => "%C3%B8",
		"%F9" => "%C3%B9",
		"%FA" => "%C3%BA",
		"%FB" => "%C3%BB",
		"%FC" => "%C3%BC",
		"%FD" => "%C3%BD",
		"%FE" => "%C3%BE",
		"%FF" => "%C3%BF",
	);
	return $map[$char] ?: $char;
}

/**
 * @param string $string
 * @return string
 */
function decodeURIComponent($string){
	$result = "";
	for($i = 0; $i < strlen($string); $i++){
		$dec_str = "";
		for($p = 0; $p <= 8; $p++){
			$dec_str .= $string[$i + $p];
		}
		[$ds, $num] = decodeURIComponentByCharacter($dec_str);
		$result .= urldecode($ds);
		$i += $num;
	}
	return $result;
}

/**
 * @param string $str
 * @return array
 */
function decodeURIComponentByCharacter($str){
	$char = $str;
	if($char == "%E2%82%AC"){
		return array("%80", 8);
	}
	if($char == "%E2%80%9A"){
		return array("%82", 8);
	}
	if($char == "%E2%80%9E"){
		return array("%84", 8);
	}
	if($char == "%E2%80%A6"){
		return array("%85", 8);
	}
	if($char == "%E2%80%A0"){
		return array("%86", 8);
	}
	if($char == "%E2%80%A1"){
		return array("%87", 8);
	}
	if($char == "%E2%80%B0"){
		return array("%89", 8);
	}
	if($char == "%E2%80%B9"){
		return array("%8B", 8);
	}
	if($char == "%E2%80%98"){
		return array("%91", 8);
	}
	if($char == "%E2%80%99"){
		return array("%92", 8);
	}
	if($char == "%E2%80%9C"){
		return array("%93", 8);
	}
	if($char == "%E2%80%9D"){
		return array("%94", 8);
	}
	if($char == "%E2%80%A2"){
		return array("%95", 8);
	}
	if($char == "%E2%80%93"){
		return array("%96", 8);
	}
	if($char == "%E2%80%94"){
		return array("%97", 8);
	}
	if($char == "%E2%84%A2"){
		return array("%99", 8);
	}
	if($char == "%E2%80%BA"){
		return array("%9B", 8);
	}

	$char = substr($str, 0, 6);

	if($char == "%C2%81"){
		return array("%81", 5);
	}
	if($char == "%C6%92"){
		return array("%83", 5);
	}
	if($char == "%CB%86"){
		return array("%88", 5);
	}
	if($char == "%C5%A0"){
		return array("%8A", 5);
	}
	if($char == "%C5%92"){
		return array("%8C", 5);
	}
	if($char == "%C2%8D"){
		return array("%8D", 5);
	}
	if($char == "%C5%BD"){
		return array("%8E", 5);
	}
	if($char == "%C2%8F"){
		return array("%8F", 5);
	}
	if($char == "%C2%90"){
		return array("%90", 5);
	}
	if($char == "%CB%9C"){
		return array("%98", 5);
	}
	if($char == "%C5%A1"){
		return array("%9A", 5);
	}
	if($char == "%C5%93"){
		return array("%9C", 5);
	}
	if($char == "%C2%9D"){
		return array("%9D", 5);
	}
	if($char == "%C5%BE"){
		return array("%9E", 5);
	}
	if($char == "%C5%B8"){
		return array("%9F", 5);
	}
	if($char == "%C2%A0"){
		return array("%A0", 5);
	}
	if($char == "%C2%A1"){
		return array("%A1", 5);
	}
	if($char == "%C2%A2"){
		return array("%A2", 5);
	}
	if($char == "%C2%A3"){
		return array("%A3", 5);
	}
	if($char == "%C2%A4"){
		return array("%A4", 5);
	}
	if($char == "%C2%A5"){
		return array("%A5", 5);
	}
	if($char == "%C2%A6"){
		return array("%A6", 5);
	}
	if($char == "%C2%A7"){
		return array("%A7", 5);
	}
	if($char == "%C2%A8"){
		return array("%A8", 5);
	}
	if($char == "%C2%A9"){
		return array("%A9", 5);
	}
	if($char == "%C2%AA"){
		return array("%AA", 5);
	}
	if($char == "%C2%AB"){
		return array("%AB", 5);
	}
	if($char == "%C2%AC"){
		return array("%AC", 5);
	}
	if($char == "%C2%AD"){
		return array("%AD", 5);
	}
	if($char == "%C2%AE"){
		return array("%AE", 5);
	}
	if($char == "%C2%AF"){
		return array("%AF", 5);
	}
	if($char == "%C2%B0"){
		return array("%B0", 5);
	}
	if($char == "%C2%B1"){
		return array("%B1", 5);
	}
	if($char == "%C2%B2"){
		return array("%B2", 5);
	}
	if($char == "%C2%B3"){
		return array("%B3", 5);
	}
	if($char == "%C2%B4"){
		return array("%B4", 5);
	}
	if($char == "%C2%B5"){
		return array("%B5", 5);
	}
	if($char == "%C2%B6"){
		return array("%B6", 5);
	}
	if($char == "%C2%B7"){
		return array("%B7", 5);
	}
	if($char == "%C2%B8"){
		return array("%B8", 5);
	}
	if($char == "%C2%B9"){
		return array("%B9", 5);
	}
	if($char == "%C2%BA"){
		return array("%BA", 5);
	}
	if($char == "%C2%BB"){
		return array("%BB", 5);
	}
	if($char == "%C2%BC"){
		return array("%BC", 5);
	}
	if($char == "%C2%BD"){
		return array("%BD", 5);
	}
	if($char == "%C2%BE"){
		return array("%BE", 5);
	}
	if($char == "%C2%BF"){
		return array("%BF", 5);
	}
	if($char == "%C3%80"){
		return array("%C0", 5);
	}
	if($char == "%C3%81"){
		return array("%C1", 5);
	}
	if($char == "%C3%82"){
		return array("%C2", 5);
	}
	if($char == "%C3%83"){
		return array("%C3", 5);
	}
	if($char == "%C3%84"){
		return array("%C4", 5);
	}
	if($char == "%C3%85"){
		return array("%C5", 5);
	}
	if($char == "%C3%86"){
		return array("%C6", 5);
	}
	if($char == "%C3%87"){
		return array("%C7", 5);
	}
	if($char == "%C3%88"){
		return array("%C8", 5);
	}
	if($char == "%C3%89"){
		return array("%C9", 5);
	}
	if($char == "%C3%8A"){
		return array("%CA", 5);
	}
	if($char == "%C3%8B"){
		return array("%CB", 5);
	}
	if($char == "%C3%8C"){
		return array("%CC", 5);
	}
	if($char == "%C3%8D"){
		return array("%CD", 5);
	}
	if($char == "%C3%8E"){
		return array("%CE", 5);
	}
	if($char == "%C3%8F"){
		return array("%CF", 5);
	}
	if($char == "%C3%90"){
		return array("%D0", 5);
	}
	if($char == "%C3%91"){
		return array("%D1", 5);
	}
	if($char == "%C3%92"){
		return array("%D2", 5);
	}
	if($char == "%C3%93"){
		return array("%D3", 5);
	}
	if($char == "%C3%94"){
		return array("%D4", 5);
	}
	if($char == "%C3%95"){
		return array("%D5", 5);
	}
	if($char == "%C3%96"){
		return array("%D6", 5);
	}
	if($char == "%C3%97"){
		return array("%D7", 5);
	}
	if($char == "%C3%98"){
		return array("%D8", 5);
	}
	if($char == "%C3%99"){
		return array("%D9", 5);
	}
	if($char == "%C3%9A"){
		return array("%DA", 5);
	}
	if($char == "%C3%9B"){
		return array("%DB", 5);
	}
	if($char == "%C3%9C"){
		return array("%DC", 5);
	}
	if($char == "%C3%9D"){
		return array("%DD", 5);
	}
	if($char == "%C3%9E"){
		return array("%DE", 5);
	}
	if($char == "%C3%9F"){
		return array("%DF", 5);
	}
	if($char == "%C3%A0"){
		return array("%E0", 5);
	}
	if($char == "%C3%A1"){
		return array("%E1", 5);
	}
	if($char == "%C3%A2"){
		return array("%E2", 5);
	}
	if($char == "%C3%A3"){
		return array("%E3", 5);
	}
	if($char == "%C3%A4"){
		return array("%E4", 5);
	}
	if($char == "%C3%A5"){
		return array("%E5", 5);
	}
	if($char == "%C3%A6"){
		return array("%E6", 5);
	}
	if($char == "%C3%A7"){
		return array("%E7", 5);
	}
	if($char == "%C3%A8"){
		return array("%E8", 5);
	}
	if($char == "%C3%A9"){
		return array("%E9", 5);
	}
	if($char == "%C3%AA"){
		return array("%EA", 5);
	}
	if($char == "%C3%AB"){
		return array("%EB", 5);
	}
	if($char == "%C3%AC"){
		return array("%EC", 5);
	}
	if($char == "%C3%AD"){
		return array("%ED", 5);
	}
	if($char == "%C3%AE"){
		return array("%EE", 5);
	}
	if($char == "%C3%AF"){
		return array("%EF", 5);
	}
	if($char == "%C3%B0"){
		return array("%F0", 5);
	}
	if($char == "%C3%B1"){
		return array("%F1", 5);
	}
	if($char == "%C3%B2"){
		return array("%F2", 5);
	}
	if($char == "%C3%B3"){
		return array("%F3", 5);
	}
	if($char == "%C3%B4"){
		return array("%F4", 5);
	}
	if($char == "%C3%B5"){
		return array("%F5", 5);
	}
	if($char == "%C3%B6"){
		return array("%F6", 5);
	}
	if($char == "%C3%B7"){
		return array("%F7", 5);
	}
	if($char == "%C3%B8"){
		return array("%F8", 5);
	}
	if($char == "%C3%B9"){
		return array("%F9", 5);
	}
	if($char == "%C3%BA"){
		return array("%FA", 5);
	}
	if($char == "%C3%BB"){
		return array("%FB", 5);
	}
	if($char == "%C3%BC"){
		return array("%FC", 5);
	}
	if($char == "%C3%BD"){
		return array("%FD", 5);
	}
	if($char == "%C3%BE"){
		return array("%FE", 5);
	}
	if($char == "%C3%BF"){
		return array("%FF", 5);
	}

	$char = substr($str, 0, 3);
	if($char == "%20"){
		return array("+", 2);
	}

	$char = substr($str, 0, 1);

	if($char == "!"){
		return array("%21", 0);
	}
	if($char == "\""){
		return array("%27", 0);
	}
	if($char == "("){
		return array("%28", 0);
	}
	if($char == ")"){
		return array("%29", 0);
	}
	if($char == "*"){
		return array("%2A", 0);
	}
	if($char == "~"){
		return array("%7E", 0);
	}

	if($char == "%"){
		return array(substr($str, 0, 3), 2);
	}else{
		return array($char, 0);
	}
}

function encodeURI($string){
	$result = "";
	for($i = 0; $i < strlen($string); $i++){
		$result .= encodeURIByCharacter(urlencode($string[$i]));
	}
	return $result;
}

/**
 * @param string $char
 * @return string
 */
function encodeURIByCharacter($char){
	if($char == "+"){
		return "%20";
	}
	if($char == "%21"){
		return "!";
	}
	if($char == "%23"){
		return "#";
	}
	if($char == "%24"){
		return "$";
	}
	if($char == "%26"){
		return "&";
	}
	if($char == "%27"){
		return "\"";
	}
	if($char == "%28"){
		return "(";
	}
	if($char == "%29"){
		return ")";
	}
	if($char == "%2A"){
		return "*";
	}
	if($char == "%2B"){
		return "+";
	}
	if($char == "%2C"){
		return ",";
	}
	if($char == "%2F"){
		return "/";
	}
	if($char == "%3A"){
		return ":";
	}
	if($char == "%3B"){
		return ";";
	}
	if($char == "%3D"){
		return "=";
	}
	if($char == "%3F"){
		return "?";
	}
	if($char == "%40"){
		return "@";
	}
	if($char == "%7E"){
		return "~";
	}
	if($char == "%80"){
		return "%E2%82%AC";
	}
	if($char == "%81"){
		return "%C2%81";
	}
	if($char == "%82"){
		return "%E2%80%9A";
	}
	if($char == "%83"){
		return "%C6%92";
	}
	if($char == "%84"){
		return "%E2%80%9E";
	}
	if($char == "%85"){
		return "%E2%80%A6";
	}
	if($char == "%86"){
		return "%E2%80%A0";
	}
	if($char == "%87"){
		return "%E2%80%A1";
	}
	if($char == "%88"){
		return "%CB%86";
	}
	if($char == "%89"){
		return "%E2%80%B0";
	}
	if($char == "%8A"){
		return "%C5%A0";
	}
	if($char == "%8B"){
		return "%E2%80%B9";
	}
	if($char == "%8C"){
		return "%C5%92";
	}
	if($char == "%8D"){
		return "%C2%8D";
	}
	if($char == "%8E"){
		return "%C5%BD";
	}
	if($char == "%8F"){
		return "%C2%8F";
	}
	if($char == "%90"){
		return "%C2%90";
	}
	if($char == "%91"){
		return "%E2%80%98";
	}
	if($char == "%92"){
		return "%E2%80%99";
	}
	if($char == "%93"){
		return "%E2%80%9C";
	}
	if($char == "%94"){
		return "%E2%80%9D";
	}
	if($char == "%95"){
		return "%E2%80%A2";
	}
	if($char == "%96"){
		return "%E2%80%93";
	}
	if($char == "%97"){
		return "%E2%80%94";
	}
	if($char == "%98"){
		return "%CB%9C";
	}
	if($char == "%99"){
		return "%E2%84%A2";
	}
	if($char == "%9A"){
		return "%C5%A1";
	}
	if($char == "%9B"){
		return "%E2%80%BA";
	}
	if($char == "%9C"){
		return "%C5%93";
	}
	if($char == "%9D"){
		return "%C2%9D";
	}
	if($char == "%9E"){
		return "%C5%BE";
	}
	if($char == "%9F"){
		return "%C5%B8";
	}
	if($char == "%A0"){
		return "%C2%A0";
	}
	if($char == "%A1"){
		return "%C2%A1";
	}
	if($char == "%A2"){
		return "%C2%A2";
	}
	if($char == "%A3"){
		return "%C2%A3";
	}
	if($char == "%A4"){
		return "%C2%A4";
	}
	if($char == "%A5"){
		return "%C2%A5";
	}
	if($char == "%A6"){
		return "%C2%A6";
	}
	if($char == "%A7"){
		return "%C2%A7";
	}
	if($char == "%A8"){
		return "%C2%A8";
	}
	if($char == "%A9"){
		return "%C2%A9";
	}
	if($char == "%AA"){
		return "%C2%AA";
	}
	if($char == "%AB"){
		return "%C2%AB";
	}
	if($char == "%AC"){
		return "%C2%AC";
	}
	if($char == "%AD"){
		return "%C2%AD";
	}
	if($char == "%AE"){
		return "%C2%AE";
	}
	if($char == "%AF"){
		return "%C2%AF";
	}
	if($char == "%B0"){
		return "%C2%B0";
	}
	if($char == "%B1"){
		return "%C2%B1";
	}
	if($char == "%B2"){
		return "%C2%B2";
	}
	if($char == "%B3"){
		return "%C2%B3";
	}
	if($char == "%B4"){
		return "%C2%B4";
	}
	if($char == "%B5"){
		return "%C2%B5";
	}
	if($char == "%B6"){
		return "%C2%B6";
	}
	if($char == "%B7"){
		return "%C2%B7";
	}
	if($char == "%B8"){
		return "%C2%B8";
	}
	if($char == "%B9"){
		return "%C2%B9";
	}
	if($char == "%BA"){
		return "%C2%BA";
	}
	if($char == "%BB"){
		return "%C2%BB";
	}
	if($char == "%BC"){
		return "%C2%BC";
	}
	if($char == "%BD"){
		return "%C2%BD";
	}
	if($char == "%BE"){
		return "%C2%BE";
	}
	if($char == "%BF"){
		return "%C2%BF";
	}
	if($char == "%C0"){
		return "%C3%80";
	}
	if($char == "%C1"){
		return "%C3%81";
	}
	if($char == "%C2"){
		return "%C3%82";
	}
	if($char == "%C3"){
		return "%C3%83";
	}
	if($char == "%C4"){
		return "%C3%84";
	}
	if($char == "%C5"){
		return "%C3%85";
	}
	if($char == "%C6"){
		return "%C3%86";
	}
	if($char == "%C7"){
		return "%C3%87";
	}
	if($char == "%C8"){
		return "%C3%88";
	}
	if($char == "%C9"){
		return "%C3%89";
	}
	if($char == "%CA"){
		return "%C3%8A";
	}
	if($char == "%CB"){
		return "%C3%8B";
	}
	if($char == "%CC"){
		return "%C3%8C";
	}
	if($char == "%CD"){
		return "%C3%8D";
	}
	if($char == "%CE"){
		return "%C3%8E";
	}
	if($char == "%CF"){
		return "%C3%8F";
	}
	if($char == "%D0"){
		return "%C3%90";
	}
	if($char == "%D1"){
		return "%C3%91";
	}
	if($char == "%D2"){
		return "%C3%92";
	}
	if($char == "%D3"){
		return "%C3%93";
	}
	if($char == "%D4"){
		return "%C3%94";
	}
	if($char == "%D5"){
		return "%C3%95";
	}
	if($char == "%D6"){
		return "%C3%96";
	}
	if($char == "%D7"){
		return "%C3%97";
	}
	if($char == "%D8"){
		return "%C3%98";
	}
	if($char == "%D9"){
		return "%C3%99";
	}
	if($char == "%DA"){
		return "%C3%9A";
	}
	if($char == "%DB"){
		return "%C3%9B";
	}
	if($char == "%DC"){
		return "%C3%9C";
	}
	if($char == "%DD"){
		return "%C3%9D";
	}
	if($char == "%DE"){
		return "%C3%9E";
	}
	if($char == "%DF"){
		return "%C3%9F";
	}
	if($char == "%E0"){
		return "%C3%A0";
	}
	if($char == "%E1"){
		return "%C3%A1";
	}
	if($char == "%E2"){
		return "%C3%A2";
	}
	if($char == "%E3"){
		return "%C3%A3";
	}
	if($char == "%E4"){
		return "%C3%A4";
	}
	if($char == "%E5"){
		return "%C3%A5";
	}
	if($char == "%E6"){
		return "%C3%A6";
	}
	if($char == "%E7"){
		return "%C3%A7";
	}
	if($char == "%E8"){
		return "%C3%A8";
	}
	if($char == "%E9"){
		return "%C3%A9";
	}
	if($char == "%EA"){
		return "%C3%AA";
	}
	if($char == "%EB"){
		return "%C3%AB";
	}
	if($char == "%EC"){
		return "%C3%AC";
	}
	if($char == "%ED"){
		return "%C3%AD";
	}
	if($char == "%EE"){
		return "%C3%AE";
	}
	if($char == "%EF"){
		return "%C3%AF";
	}
	if($char == "%F0"){
		return "%C3%B0";
	}
	if($char == "%F1"){
		return "%C3%B1";
	}
	if($char == "%F2"){
		return "%C3%B2";
	}
	if($char == "%F3"){
		return "%C3%B3";
	}
	if($char == "%F4"){
		return "%C3%B4";
	}
	if($char == "%F5"){
		return "%C3%B5";
	}
	if($char == "%F6"){
		return "%C3%B6";
	}
	if($char == "%F7"){
		return "%C3%B7";
	}
	if($char == "%F8"){
		return "%C3%B8";
	}
	if($char == "%F9"){
		return "%C3%B9";
	}
	if($char == "%FA"){
		return "%C3%BA";
	}
	if($char == "%FB"){
		return "%C3%BB";
	}
	if($char == "%FC"){
		return "%C3%BC";
	}
	if($char == "%FD"){
		return "%C3%BD";
	}
	if($char == "%FE"){
		return "%C3%BE";
	}
	if($char == "%FF"){
		return "%C3%BF";
	}
	return $char;
}

/**
 * @param string $string
 * @return string
 */
function decodeURI($string){
	$result = "";
	for($i = 0; $i < strlen($string); $i++){
		$dec_str = "";
		for($p = 0; $p <= 8; $p++){
			$dec_str .= $string[$i + $p];
		}
		[$ds, $num] = decodeURIByCharacter($dec_str);
		$result .= urldecode($ds);
		$i += $num;
	}
	return $result;
}

function decodeURIByCharacter($str){
	$char = $str;
	if($char == "%E2%82%AC"){
		return array("%80", 8);
	}
	if($char == "%E2%80%9A"){
		return array("%82", 8);
	}
	if($char == "%E2%80%9E"){
		return array("%84", 8);
	}
	if($char == "%E2%80%A6"){
		return array("%85", 8);
	}
	if($char == "%E2%80%A0"){
		return array("%86", 8);
	}
	if($char == "%E2%80%A1"){
		return array("%87", 8);
	}
	if($char == "%E2%80%B0"){
		return array("%89", 8);
	}
	if($char == "%E2%80%B9"){
		return array("%8B", 8);
	}
	if($char == "%E2%80%98"){
		return array("%91", 8);
	}
	if($char == "%E2%80%99"){
		return array("%92", 8);
	}
	if($char == "%E2%80%9C"){
		return array("%93", 8);
	}
	if($char == "%E2%80%9D"){
		return array("%94", 8);
	}
	if($char == "%E2%80%A2"){
		return array("%95", 8);
	}
	if($char == "%E2%80%93"){
		return array("%96", 8);
	}
	if($char == "%E2%80%94"){
		return array("%97", 8);
	}
	if($char == "%E2%84%A2"){
		return array("%99", 8);
	}
	if($char == "%E2%80%BA"){
		return array("%9B", 8);
	}

	$char = substr($str, 0, 6);

	if($char == "%C2%81"){
		return array("%81", 5);
	}
	if($char == "%C6%92"){
		return array("%83", 5);
	}
	if($char == "%CB%86"){
		return array("%88", 5);
	}
	if($char == "%C5%A0"){
		return array("%8A", 5);
	}
	if($char == "%C5%92"){
		return array("%8C", 5);
	}
	if($char == "%C2%8D"){
		return array("%8D", 5);
	}
	if($char == "%C5%BD"){
		return array("%8E", 5);
	}
	if($char == "%C2%8F"){
		return array("%8F", 5);
	}
	if($char == "%C2%90"){
		return array("%90", 5);
	}
	if($char == "%CB%9C"){
		return array("%98", 5);
	}
	if($char == "%C5%A1"){
		return array("%9A", 5);
	}
	if($char == "%C5%93"){
		return array("%9C", 5);
	}
	if($char == "%C2%9D"){
		return array("%9D", 5);
	}
	if($char == "%C5%BE"){
		return array("%9E", 5);
	}
	if($char == "%C5%B8"){
		return array("%9F", 5);
	}
	if($char == "%C2%A0"){
		return array("%A0", 5);
	}
	if($char == "%C2%A1"){
		return array("%A1", 5);
	}
	if($char == "%C2%A2"){
		return array("%A2", 5);
	}
	if($char == "%C2%A3"){
		return array("%A3", 5);
	}
	if($char == "%C2%A4"){
		return array("%A4", 5);
	}
	if($char == "%C2%A5"){
		return array("%A5", 5);
	}
	if($char == "%C2%A6"){
		return array("%A6", 5);
	}
	if($char == "%C2%A7"){
		return array("%A7", 5);
	}
	if($char == "%C2%A8"){
		return array("%A8", 5);
	}
	if($char == "%C2%A9"){
		return array("%A9", 5);
	}
	if($char == "%C2%AA"){
		return array("%AA", 5);
	}
	if($char == "%C2%AB"){
		return array("%AB", 5);
	}
	if($char == "%C2%AC"){
		return array("%AC", 5);
	}
	if($char == "%C2%AD"){
		return array("%AD", 5);
	}
	if($char == "%C2%AE"){
		return array("%AE", 5);
	}
	if($char == "%C2%AF"){
		return array("%AF", 5);
	}
	if($char == "%C2%B0"){
		return array("%B0", 5);
	}
	if($char == "%C2%B1"){
		return array("%B1", 5);
	}
	if($char == "%C2%B2"){
		return array("%B2", 5);
	}
	if($char == "%C2%B3"){
		return array("%B3", 5);
	}
	if($char == "%C2%B4"){
		return array("%B4", 5);
	}
	if($char == "%C2%B5"){
		return array("%B5", 5);
	}
	if($char == "%C2%B6"){
		return array("%B6", 5);
	}
	if($char == "%C2%B7"){
		return array("%B7", 5);
	}
	if($char == "%C2%B8"){
		return array("%B8", 5);
	}
	if($char == "%C2%B9"){
		return array("%B9", 5);
	}
	if($char == "%C2%BA"){
		return array("%BA", 5);
	}
	if($char == "%C2%BB"){
		return array("%BB", 5);
	}
	if($char == "%C2%BC"){
		return array("%BC", 5);
	}
	if($char == "%C2%BD"){
		return array("%BD", 5);
	}
	if($char == "%C2%BE"){
		return array("%BE", 5);
	}
	if($char == "%C2%BF"){
		return array("%BF", 5);
	}
	if($char == "%C3%80"){
		return array("%C0", 5);
	}
	if($char == "%C3%81"){
		return array("%C1", 5);
	}
	if($char == "%C3%82"){
		return array("%C2", 5);
	}
	if($char == "%C3%83"){
		return array("%C3", 5);
	}
	if($char == "%C3%84"){
		return array("%C4", 5);
	}
	if($char == "%C3%85"){
		return array("%C5", 5);
	}
	if($char == "%C3%86"){
		return array("%C6", 5);
	}
	if($char == "%C3%87"){
		return array("%C7", 5);
	}
	if($char == "%C3%88"){
		return array("%C8", 5);
	}
	if($char == "%C3%89"){
		return array("%C9", 5);
	}
	if($char == "%C3%8A"){
		return array("%CA", 5);
	}
	if($char == "%C3%8B"){
		return array("%CB", 5);
	}
	if($char == "%C3%8C"){
		return array("%CC", 5);
	}
	if($char == "%C3%8D"){
		return array("%CD", 5);
	}
	if($char == "%C3%8E"){
		return array("%CE", 5);
	}
	if($char == "%C3%8F"){
		return array("%CF", 5);
	}
	if($char == "%C3%90"){
		return array("%D0", 5);
	}
	if($char == "%C3%91"){
		return array("%D1", 5);
	}
	if($char == "%C3%92"){
		return array("%D2", 5);
	}
	if($char == "%C3%93"){
		return array("%D3", 5);
	}
	if($char == "%C3%94"){
		return array("%D4", 5);
	}
	if($char == "%C3%95"){
		return array("%D5", 5);
	}
	if($char == "%C3%96"){
		return array("%D6", 5);
	}
	if($char == "%C3%97"){
		return array("%D7", 5);
	}
	if($char == "%C3%98"){
		return array("%D8", 5);
	}
	if($char == "%C3%99"){
		return array("%D9", 5);
	}
	if($char == "%C3%9A"){
		return array("%DA", 5);
	}
	if($char == "%C3%9B"){
		return array("%DB", 5);
	}
	if($char == "%C3%9C"){
		return array("%DC", 5);
	}
	if($char == "%C3%9D"){
		return array("%DD", 5);
	}
	if($char == "%C3%9E"){
		return array("%DE", 5);
	}
	if($char == "%C3%9F"){
		return array("%DF", 5);
	}
	if($char == "%C3%A0"){
		return array("%E0", 5);
	}
	if($char == "%C3%A1"){
		return array("%E1", 5);
	}
	if($char == "%C3%A2"){
		return array("%E2", 5);
	}
	if($char == "%C3%A3"){
		return array("%E3", 5);
	}
	if($char == "%C3%A4"){
		return array("%E4", 5);
	}
	if($char == "%C3%A5"){
		return array("%E5", 5);
	}
	if($char == "%C3%A6"){
		return array("%E6", 5);
	}
	if($char == "%C3%A7"){
		return array("%E7", 5);
	}
	if($char == "%C3%A8"){
		return array("%E8", 5);
	}
	if($char == "%C3%A9"){
		return array("%E9", 5);
	}
	if($char == "%C3%AA"){
		return array("%EA", 5);
	}
	if($char == "%C3%AB"){
		return array("%EB", 5);
	}
	if($char == "%C3%AC"){
		return array("%EC", 5);
	}
	if($char == "%C3%AD"){
		return array("%ED", 5);
	}
	if($char == "%C3%AE"){
		return array("%EE", 5);
	}
	if($char == "%C3%AF"){
		return array("%EF", 5);
	}
	if($char == "%C3%B0"){
		return array("%F0", 5);
	}
	if($char == "%C3%B1"){
		return array("%F1", 5);
	}
	if($char == "%C3%B2"){
		return array("%F2", 5);
	}
	if($char == "%C3%B3"){
		return array("%F3", 5);
	}
	if($char == "%C3%B4"){
		return array("%F4", 5);
	}
	if($char == "%C3%B5"){
		return array("%F5", 5);
	}
	if($char == "%C3%B6"){
		return array("%F6", 5);
	}
	if($char == "%C3%B7"){
		return array("%F7", 5);
	}
	if($char == "%C3%B8"){
		return array("%F8", 5);
	}
	if($char == "%C3%B9"){
		return array("%F9", 5);
	}
	if($char == "%C3%BA"){
		return array("%FA", 5);
	}
	if($char == "%C3%BB"){
		return array("%FB", 5);
	}
	if($char == "%C3%BC"){
		return array("%FC", 5);
	}
	if($char == "%C3%BD"){
		return array("%FD", 5);
	}
	if($char == "%C3%BE"){
		return array("%FE", 5);
	}
	if($char == "%C3%BF"){
		return array("%FF", 5);
	}

	$char = substr($str, 0, 3);
	if($char == "%20"){
		return array("+", 2);
	}

	$char = substr($str, 0, 1);

	if($char == "!"){
		return array("%21", 0);
	}
	if($char == "#"){
		return array("%23", 0);
	}
	if($char == "$"){
		return array("%24", 0);
	}
	if($char == "&"){
		return array("%26", 0);
	}
	if($char == "\""){
		return array("%27", 0);
	}
	if($char == "("){
		return array("%28", 0);
	}
	if($char == ")"){
		return array("%29", 0);
	}
	if($char == "*"){
		return array("%2A", 0);
	}
	if($char == "+"){
		return array("%2B", 0);
	}
	if($char == ","){
		return array("%2C", 0);
	}
	if($char == "/"){
		return array("%2F", 0);
	}
	if($char == ":"){
		return array("%3A", 0);
	}
	if($char == ";"){
		return array("%3B", 0);
	}
	if($char == "="){
		return array("%3D", 0);
	}
	if($char == "?"){
		return array("%3F", 0);
	}
	if($char == "@"){
		return array("%40", 0);
	}
	if($char == "~"){
		return array("%7E", 0);
	}

	if($char == "%"){
		return array(substr($str, 0, 3), 2);
	}else{
		return array($char, 0);
	}
}

function escape($string){
	$result = "";
	for($i = 0; $i < strlen($string); $i++){
		$result .= escapeByCharacter(urlencode($string[$i]));
	}
	return $result;
}

function escapeByCharacter($char){
	if($char == '+'){
		return '%20';
	}
	if($char == '%2A'){
		return '*';
	}
	if($char == '%2B'){
		return '+';
	}
	if($char == '%2F'){
		return '/';
	}
	if($char == '%40'){
		return '@';
	}
	if($char == '%80'){
		return '%u20AC';
	}
	if($char == '%82'){
		return '%u201A';
	}
	if($char == '%83'){
		return '%u0192';
	}
	if($char == '%84'){
		return '%u201E';
	}
	if($char == '%85'){
		return '%u2026';
	}
	if($char == '%86'){
		return '%u2020';
	}
	if($char == '%87'){
		return '%u2021';
	}
	if($char == '%88'){
		return '%u02C6';
	}
	if($char == '%89'){
		return '%u2030';
	}
	if($char == '%8A'){
		return '%u0160';
	}
	if($char == '%8B'){
		return '%u2039';
	}
	if($char == '%8C'){
		return '%u0152';
	}
	if($char == '%8E'){
		return '%u017D';
	}
	if($char == '%91'){
		return '%u2018';
	}
	if($char == '%92'){
		return '%u2019';
	}
	if($char == '%93'){
		return '%u201C';
	}
	if($char == '%94'){
		return '%u201D';
	}
	if($char == '%95'){
		return '%u2022';
	}
	if($char == '%96'){
		return '%u2013';
	}
	if($char == '%97'){
		return '%u2014';
	}
	if($char == '%98'){
		return '%u02DC';
	}
	if($char == '%99'){
		return '%u2122';
	}
	if($char == '%9A'){
		return '%u0161';
	}
	if($char == '%9B'){
		return '%u203A';
	}
	if($char == '%9C'){
		return '%u0153';
	}
	if($char == '%9E'){
		return '%u017E';
	}
	if($char == '%9F'){
		return '%u0178';
	}
	return $char;
}

/**
 * @param string $string
 * @return string
 */
function unescape($string){
	$result = "";
	for($i = 0; $i < strlen($string); $i++){
		$dec_str = "";
		for($p = 0; $p <= 5; $p++){
			$dec_str .= $string[$i + $p];
		}
		[$ds, $num] = unEscapeByCharacter($dec_str);
		$result .= urldecode($ds);
		$i += $num;
	}
	return $result;
}

function unEscapeByCharacter($str){
	$char = $str;
	if($char == '%u20AC'){
		return array("%80", 5);
	}
	if($char == '%u201A'){
		return array("%82", 5);
	}
	if($char == '%u0192'){
		return array("%83", 5);
	}
	if($char == '%u201E'){
		return array("%84", 5);
	}
	if($char == '%u2026'){
		return array("%85", 5);
	}
	if($char == '%u2020'){
		return array("%86", 5);
	}
	if($char == '%u2021'){
		return array("%87", 5);
	}
	if($char == '%u02C6'){
		return array("%88", 5);
	}
	if($char == '%u2030'){
		return array("%89", 5);
	}
	if($char == '%u0160'){
		return array("%8A", 5);
	}
	if($char == '%u2039'){
		return array("%8B", 5);
	}
	if($char == '%u0152'){
		return array("%8C", 5);
	}
	if($char == '%u017D'){
		return array("%8E", 5);
	}
	if($char == '%u2018'){
		return array("%91", 5);
	}
	if($char == '%u2019'){
		return array("%92", 5);
	}
	if($char == '%u201C'){
		return array("%93", 5);
	}
	if($char == '%u201D'){
		return array("%94", 5);
	}
	if($char == '%u2022'){
		return array("%95", 5);
	}
	if($char == '%u2013'){
		return array("%96", 5);
	}
	if($char == '%u2014'){
		return array("%97", 5);
	}
	if($char == '%u02DC'){
		return array("%98", 5);
	}
	if($char == '%u2122'){
		return array("%99", 5);
	}
	if($char == '%u0161'){
		return array("%9A", 5);
	}
	if($char == '%u203A'){
		return array("%9B", 5);
	}
	if($char == '%u0153'){
		return array("%9C", 5);
	}
	if($char == '%u017E'){
		return array("%9E", 5);
	}
	if($char == '%u0178'){
		return array("%9F", 5);
	}

	$char = substr($str, 0, 3);
	if($char == "%20"){
		return array("+", 2);
	}

	$char = substr($str, 0, 1);

	if($char == '*'){
		return array("%2A", 0);
	}
	if($char == '+'){
		return array("%2B", 0);
	}
	if($char == '/'){
		return array("%2F", 0);
	}
	if($char == '@'){
		return array("%40", 0);
	}

	if($char == "%"){
		return array(substr($str, 0, 3), 2);
	}else{
		return array($char, 0);
	}
}

/**
 * Returns a GUIDv4 string
 * Uses the best cryptographically secure method
 * for all supported platforms with fallback to an older,
 * less secure version.
 * @param bool $trim
 * @return string
 */
function generate_guid($trim = true){
	// Windows
	if(function_exists('com_create_guid') === true){
		if($trim === true)
			return trim(com_create_guid(), '{}');else
			return com_create_guid();
	}
	// OSX/Linux
	if(function_exists('openssl_random_pseudo_bytes') === true){
		$data = openssl_random_pseudo_bytes(16);
		$data[6] = chr(ord($data[6])&0x0f|0x40); // set version to 0100
		$data[8] = chr(ord($data[8])&0x3f|0x80); // set bits 6-7 to 10
		return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
	}

	// Fallback (PHP 4.2+)
	mt_srand((double)microtime()*10000);
	$char_id = strtolower(md5(uniqid(rand(), true)));
	$hyphen = chr(45); // "-"
	$lbrace = $trim ? "" : chr(123); // "{"
	$rbrace = $trim ? "" : chr(125); // "}"
	return $lbrace.substr($char_id, 0, 8).$hyphen.substr($char_id, 8, 4).$hyphen.substr($char_id, 12, 4).$hyphen.substr($char_id, 16, 4).$hyphen.substr($char_id, 20, 12).$rbrace;
}
