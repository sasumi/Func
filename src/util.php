<?php
/**
 * 杂项操作函数
 */
namespace LFPhp\Func;

use Closure;

/**
 * 步进方式调试
 * @deprecated PHP 7.2 已被官方禁用跨文件调用
 * @param int $step 步长
 * @param string $fun 调试函数，默认使用dump
 */
function tick_dump($step = 1, $fun = '\dump'){
	register_tick_function($fun);
	eval("declare(ticks = $step);");
}

/**
 * 程序调试函数
 * 调用方式：dump($var1, $var2, ..., 1) ，当最后一个数值为1时，表示退出（die）程序运行
 */
function dump(){
	$params = func_get_args();
	$cli = PHP_SAPI === 'cli';
	$exit = false;
	echo !$cli ? PHP_EOL.'<pre style="color:green;">'.PHP_EOL : PHP_EOL;

	if(count($params)){
		$tmp = $params;
		$exit = array_pop($tmp) === 1;
		$params = $exit ? array_slice($params, 0, -1) : $params;
		$comma = '';
		foreach($params as $var){
			echo $comma;
			var_dump($var);
			$comma = str_repeat('-',80).PHP_EOL;
		}
	}

	//remove closure calling & print out location.
	$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
	if(isset($GLOBALS['DUMP_WITH_TRACE']) && $GLOBALS['DUMP_WITH_TRACE']){
		echo "[trace]",PHP_EOL;
		print_trace($trace, true, true);
	} else {
		print_trace([$trace[0]]);
	}
	echo str_repeat('=', 80), PHP_EOL, (!$cli ? '</pre>' : '');
	$exit && exit();
}

/**
 * 打印trace信息
 * @param $trace
 * @param bool $with_callee
 * @param bool $with_index
 * @param bool $as_return
 * @return string
 */
function print_trace($trace, $with_callee = false, $with_index = false, $as_return = false){
	$ct = count($trace);
	$str = '';
	foreach($trace as $k=>$item){
		$callee = '';
		if($with_callee){
			$vs = [];
			foreach($item['args'] as $arg){
				$vs[] = var_export_min($arg, true);
			}
			$arg_statement = join(',', $vs);
			$arg_statement = substr(str_replace("\n", '', $arg_statement), 0, 50);
			$callee = $item['class'] ? "\t{$item['class']}{$item['type']}{$item['function']}($arg_statement)" : "\t{$item['function']}($arg_statement)";
		}
		if($with_index){
			$str .= "[". ($ct - $k). "] ";
		}
		$loc = $item['file'] ? "{$item['file']} #{$item['line']} " : '';
		$str .= "{$loc}{$callee}".PHP_EOL;
	}
	if($as_return){
		return $str;
	}
	echo $str;
}

/**
 * 打印系统错误及trace跟踪信息
 * @param $code
 * @param $msg
 * @param $file
 * @param $line
 * @param string $trace_string
 */
function print_sys_error($code, $msg, $file = null, $line = null, $trace_string = ''){
	echo "<pre>";
	$code = error2string($code);
	echo "[$code] $msg\n\n";
	echo "* $file #$line\n\n";

	if(!$trace_string){
		$bs = debug_backtrace();
		array_shift($bs);
		foreach($bs as $k => $b){
			echo count($bs)-$k." {$b['class']}{$b['type']}{$b['function']}\n";
			echo "  {$b['file']}  #{$b['line']} \n\n";
		}
	} else{
		echo $trace_string;
	}
	die;
}

/**
 * error code to string
 * @param $value
 * @return string
 */
function error2string($value){
	$level_names = array(
		E_ERROR           => 'E_ERROR',
		E_WARNING         => 'E_WARNING',
		E_PARSE           => 'E_PARSE',
		E_NOTICE          => 'E_NOTICE',
		E_CORE_ERROR      => 'E_CORE_ERROR',
		E_CORE_WARNING    => 'E_CORE_WARNING',
		E_COMPILE_ERROR   => 'E_COMPILE_ERROR',
		E_COMPILE_WARNING => 'E_COMPILE_WARNING',
		E_USER_ERROR      => 'E_USER_ERROR',
		E_USER_WARNING    => 'E_USER_WARNING',
		E_USER_NOTICE     => 'E_USER_NOTICE'
	);
	if(defined('E_STRICT')){
		$level_names[E_STRICT] = 'E_STRICT';
	}
	$levels = array();
	if(($value&E_ALL) == E_ALL){
		$levels[] = 'E_ALL';
		$value &= ~E_ALL;
	}
	foreach($level_names as $level => $name){
		if(($value&$level) == $level){
			$levels[] = $name;
		}
	}
	return implode(' | ', $levels);
}

/**
 * string to error code
 * @param $string
 * @return int
 */
function string2error($string){
	$level_names = array(
		'E_ERROR',
		'E_WARNING',
		'E_PARSE',
		'E_NOTICE',
		'E_CORE_ERROR',
		'E_CORE_WARNING',
		'E_COMPILE_ERROR',
		'E_COMPILE_WARNING',
		'E_USER_ERROR',
		'E_USER_WARNING',
		'E_USER_NOTICE',
		'E_ALL'
	);
	if(defined('E_STRICT')){
		$level_names[] = 'E_STRICT';
	}
	$value = 0;
	$levels = explode('|', $string);

	foreach($levels as $level){
		$level = trim($level);
		if(defined($level)){
			$value |= (int)constant($level);
		}
	}
	return $value;
}

/**
 * check is function
 * @param mixed $f
 * @return boolean
 */
function is_function($f){
	return (is_string($f) && function_exists($f)) || (is_object($f) && ($f instanceof Closure));
}

/**
 * get class(also trait) uses recursive
 * @param $class_or_object
 * @return array
 */
function class_uses_recursive($class_or_object){
	if(is_object($class_or_object)){
		$class = get_class($class_or_object);
	}else{
		$class = $class_or_object;
	}
	$results = [];
	foreach(array_reverse(class_parents($class)) + [$class => $class] as $class){
		$results += trait_uses_recursive($class);
	}
	return array_unique($results);
}

/**
 * get trait uses recursive
 * @param $trait
 * @return array
 */
function trait_uses_recursive($trait){
	$traits = class_uses($trait);
	foreach($traits as $trait){
		$traits += trait_uses_recursive($trait);
	}
	return $traits;
}

/**
 * pdog
 * @deprecated ticks no triggered in PHP 7.0+
 * @param $fun
 * @param $handler
 */
function pdog($fun, $handler){
	declare(ticks = 1);
	register_tick_function(function() use ($fun, $handler){
		$debug_list = debug_backtrace();
		foreach($debug_list as $info){
			if($info['function'] == $fun){
				call_user_func($handler, $info['args']);
			}
		}
	});
}

/**
 * get GUID
 * @return mixed
 */
function guid(){
	global $__guid__;
	return $__guid__++;
}

/**
 * var_export in minimal format
 * @param $var
 * @param bool $return
 * @return mixed|string|null
 */
function var_export_min($var, $return = false){
	if(is_array($var)){
		$toImplode = array();
		foreach($var as $key => $value){
			$toImplode[] = var_export($key, true).'=>'.var_export_min($value, true);
		}
		$code = 'array('.implode(',', $toImplode).')';
		if($return){
			return $code;
		}else{
			echo $code;
		}
	}else{
		return var_export($var, $return);
	}
	return null;
}

/**
 * 代码打点
 * @param string $tag
 * @param bool $trace_location
 * @param bool $mem_usage
 * @return mixed
 */
function debug_mark($tag = '', $trace_location = true, $mem_usage = true){
	$k = __FUNCTION__;
	$tm = microtime();
	$trace = $mem = null;
	if($mem_usage){
		$mem = memory_get_usage(true);
	}
	if($trace_location){
		$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
	}

	if(!isset($GLOBALS[$k])){
		$GLOBALS[$k] = [];
	}
	$GLOBALS[$k][] = [$tag, $tm, $trace, $mem];
	return $GLOBALS[$k];
}

/**
 * 输出打点信息
 * @param bool $as_return
 * @return string|null
 */
function debug_mark_output($as_return = false){
	$k = __NAMESPACE__.'\\debug_mark';
	if($as_return){
		return $GLOBALS[$k];
	}
	$str = '';
	$last_time = null;
	foreach($GLOBALS[$k] as $idx=>list($tag, $tm, $trace, $mem)){
		$t = explode(' ', $tm);
		$time_txt = date('Y/m/d H:i:s', $t[1]).substr($t[0], 1,4);
		if($last_time){
			$time_txt .= '('.microtime_diff($last_time, $t).')';
		}
		$mem_txt = '';
		if($mem){
			$mem_txt = format_size($mem);
		}
		$callee = $file_loc = '';
		if($trace){
			$callee = $trace['class'] ? "{$trace['class']}{$trace['type']}{$trace['function']}()" : "{$trace['function']}()";
			$file_loc = $trace['file'].'#'.$trace['line'];
		}
		$str .= "{$time_txt} [{$mem_txt}] $tag $callee $file_loc".PHP_EOL;
	}
	if($as_return){
		return $str;
	}
	echo $str;
	return null;
}