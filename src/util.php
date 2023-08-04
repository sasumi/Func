<?php
/**
 * 杂项操作函数
 */
namespace LFPhp\Func;

use Closure;
use ErrorException;
use Exception;
use ReflectionClass;
use ReflectionObject;

/**
 * 步进方式调试
 * @param int $step 步长
 * @param string $fun 调试函数，默认使用dump
 * @deprecated PHP 7.2 已被官方禁用跨文件调用
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
			$comma = str_repeat('-', 80).PHP_EOL;
		}
	}

	//remove closure calling & print out location.
	$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
	if(isset($GLOBALS['DUMP_WITH_TRACE']) && $GLOBALS['DUMP_WITH_TRACE']){
		echo "[trace]", PHP_EOL;
		print_trace($trace, true, true);
	}else{
		print_trace([$trace[0]]);
	}
	echo str_repeat('=', 80), PHP_EOL, (!$cli ? '</pre>' : '');
	$exit && exit();
}

/**
 * 检测变量是否可以打印输出（如字符串、数字、包含toString方法对象等）
 * 布尔值、资源等属于不可打印输出变量
 * @param mixed $var
 * @param string $print_str 可打印字符串
 * @return bool 是否可打印
 */
function printable($var, &$print_str = ''){
	$type = gettype($var);
	if(in_array($type, ['integer', 'double', 'float', 'string'])){
		$print_str = $var.'';
		return true;
	}
	if(is_object($var) && method_exists($var, '__toString')){
		$print_str = call_user_func([$var, '__toString']);
		return true;
	}
	if(is_callable($var)){
		$ret = call_user_func($var);
		return printable($ret, $print_str);
	}
	return false;
}

/**
 * 打印异常信息
 * @param \Exception $ex
 * @param bool $include_external_properties
 * @param bool $as_return
 * @return string
 */
function print_exception(Exception $ex, $include_external_properties = false, $as_return = false){
	$class = get_class($ex);
	$msg = "[$class]\n\t".$ex->getMessage()."\n\n";
	$msg .= "[Location]\n\t".$ex->getFile().' #'.$ex->getLine()."\n\n";
	$msg .= "[Trace]\n\t".str_replace("\n", "\n\t", $ex->getTraceAsString());

	if($include_external_properties){
		$ignore_properties = ['message', 'code', 'line', 'file', 'trace'];
		$ro = new ReflectionObject($ex);
		foreach($ro->getProperties() as $property){
			if(in_array($property->getName(), $ignore_properties)){
				continue;
			}
			$msg .= "\n\n[".$property->getName()."]\n\t";
			$msg .= $property->isPublic() ? var_export_min($property->getValue($ex), true) : $property->getDeclaringClass();
		}
	}
	if(!$as_return){
		echo $msg;
	}
	return $msg;
}

/**
 * 打印trace信息
 * @param array $trace
 * @param bool $with_callee
 * @param bool $with_index
 * @param bool $as_return
 * @return string
 */
function print_trace($trace, $with_callee = false, $with_index = false, $as_return = false){
	$ct = count($trace);
	$str = '';
	foreach($trace as $k => $item){
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
			$str .= "[".($ct - $k)."] ";
		}
		$loc = $item['file'] ? "{$item['file']} #{$item['line']} " : '';
		$str .= $loc.$callee.PHP_EOL;
	}
	if($as_return){
		return $str;
	}
	echo $str;
	return null;
}

/**
 * 打印系统错误及trace跟踪信息
 * @param integer $code
 * @param string $msg
 * @param string $file
 * @param integer $line
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
			echo count($bs) - $k." {$b['class']}{$b['type']}{$b['function']}\n";
			echo "  {$b['file']}  #{$b['line']} \n\n";
		}
	}else{
		echo $trace_string;
	}
	die;
}

/**
 * 转换错误码值到字符串
 * @param int $code
 * @return string
 */
function error2string($code){
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
		E_USER_NOTICE     => 'E_USER_NOTICE',
	);
	if(defined('E_STRICT')){
		$level_names[E_STRICT] = 'E_STRICT';
	}
	$levels = array();
	if(($code&E_ALL) == E_ALL){
		$levels[] = 'E_ALL';
		$code &= ~E_ALL;
	}
	foreach($level_names as $level => $name){
		if(($code&$level) == $level){
			$levels[] = $name;
		}
	}
	return implode(' | ', $levels);
}

/**
 * 转换错误码到具体的码值
 * @param string $string
 * @return int
 * @example string2error('E_ALL')
 */
function string2error($string){
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
 * 注册将PHP错误转换异常抛出
 * @param int $error_levels
 * @param \ErrorException|null $exception_class
 * @return callable|null
 * @throws \ErrorException
 */
function register_error2exception($error_levels = E_ALL, ErrorException $exception_class = null){
	return set_error_handler(function($err_severity, $err_str, $err_file, $err_line) use ($exception_class){
		if(error_reporting() === 0){
			return false;
		}
		if($exception_class){
			throw new $exception_class($err_str, 0, $err_severity, $err_file, $err_line);
		}
		$err_severity_map = [
			E_ERROR             => ErrorException::class,
			E_WARNING           => WarningException::class,
			E_PARSE             => ParseException::class,
			E_NOTICE            => NoticeException::class,
			E_CORE_ERROR        => CoreErrorException::class,
			E_CORE_WARNING      => CoreWarningException::class,
			E_COMPILE_ERROR     => CompileErrorException::class,
			E_COMPILE_WARNING   => CoreWarningException::class,
			E_USER_ERROR        => UserErrorException::class,
			E_USER_WARNING      => UserWarningException::class,
			E_USER_NOTICE       => UserNoticeException::class,
			E_STRICT            => StrictException::class,
			E_RECOVERABLE_ERROR => RecoverableErrorException::class,
			E_DEPRECATED        => DeprecatedException::class,
			E_USER_DEPRECATED   => UserDeprecatedException::class,
		];
		$exp_class = isset($err_severity_map[$err_severity]) ? $err_severity_map[$err_severity] : ErrorException::class;
		throw new $exp_class($err_str, 0, $err_severity, $err_file, $err_line);
	}, $error_levels);
}

/**
 * 检测是否为函数
 * @param mixed $f
 * @return boolean
 */
function is_function($f){
	return (is_string($f) && function_exists($f)) || ($f instanceof Closure);
}

/**
 * 获取对象、类的所有继承的父类(包含 trait 类)
 * 如果无需trait,试用class_parents即可
 * @param string|object $class_or_object
 * @return string[]
 */
function class_uses_recursive($class_or_object){
	$class = is_object($class_or_object) ? get_class($class_or_object) : $class_or_object;
	$chains[] = $class;
	$chains += class_parents($class);

	foreach($chains as $cls){
		$chains += trait_uses_recursive($cls);
	}
	return array_unique($chains);
}

/**
 * 递归方式获取trait
 * @param string $trait
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
 * 获取指定类常量名称
 * @param string $class 类名
 * @param mixed $const_val 常量值
 * @return string|null
 * @throws \ReflectionException
 */
function get_constant_name($class, $const_val){
	$class = new ReflectionClass($class);
	$constants = $class->getConstants();
	foreach($constants as $name=>$value){
		if($value === $const_val){
			return $name;
		}
	}
	return null;
}

/**
 * 通过抛异常方式处理断言
 * @param mixed $expression 断言值
 * @param string $err_msg
 * @param string $exception_class 异常类，缺省使用 \Exception
 */
function assert_via_exception($expression, $err_msg, $exception_class = Exception::class){
	if(!$expression){
		throw new $exception_class($err_msg);
	}
}

/**
 * pdog
 * @param string $fun
 * @param callable|string $handler
 * @deprecated ticks no triggered in PHP 7.0+
 */
function pdog($fun, $handler){
	declare(ticks=1);
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
 * 获取当前上下文GUID
 * @return mixed
 */
function guid(){
	global $__guid__;
	return $__guid__++;
}

/**
 * 使用最小格式导出变量（类似var_export）
 * @param mixed $var
 * @param bool $return 是否以返回方式返回，缺省为输出到终端
 * @return string|null
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
 * 检测内存溢出，正式运行代码不建议开启该项检查，避免损失性能
 * @param int $threshold
 * @param callable|string $leak_payload 内存泄露时调用函数
 */
function memory_leak_check($threshold = 0, $leak_payload = 'print_r'){
	static $last_usage;
	$current_usage = memory_get_usage(true);
	if(isset($last_usage) && ($current_usage - $last_usage > $threshold)){
		$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
		$msg = sprintf("Memory Leak:+%s(%s)\n%s#%s %s%s%s()\n",
			format_size($current_usage - $last_usage),
			format_size($current_usage),
			$trace[1]['file'],
			$trace[1]['line'],
			$trace[1]['class'],
			$trace[1]['type'],
			$trace[1]['function']);
		$leak_payload($msg);
	}
	$last_usage = $current_usage;
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
	$tm = microtime(true);
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
	$list = $GLOBALS[$k];
	//补上PHP服务开始时间
	array_unshift($list, ['server request', $_SERVER['REQUEST_TIME_FLOAT'], null, 0]);
	if($as_return){
		return $list;
	}
	$str = '';
	foreach($list as list($tag, $float_time, $trace, $mem)){
		$time_txt = float_time_to_date($float_time);
		$mem_txt = $mem ? format_size($mem) : '';
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

/** Error mapping to exception class */
class WarningException extends ErrorException {
}

class ParseException extends ErrorException {
}

class NoticeException extends ErrorException {
}

class CoreErrorException extends ErrorException {
}

class CoreWarningException extends ErrorException {
}

class CompileErrorException extends ErrorException {
}

class CompileWarningException extends ErrorException {
}

class UserErrorException extends ErrorException {
}

class UserWarningException extends ErrorException {
}

class UserNoticeException extends ErrorException {
}

class StrictException extends ErrorException {
}

class RecoverableErrorException extends ErrorException {
}

class DeprecatedException extends ErrorException {
}

class UserDeprecatedException extends ErrorException {
}
