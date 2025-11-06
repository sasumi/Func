<?php

/**
 * Miscellaneous Functions
 */

namespace LFPhp\Func;

use Closure;
use ErrorException;
use Exception;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionObject;

/**
 * Step-by-step debugging
 * @param int $step step length
 * @param string $fun debug function, dump is used by default
 * @deprecated PHP 7.2 has officially disabled cross-file calls
 */
function tick_dump($step = 1, $fun = '\dump') {
	register_tick_function($fun);
	eval("declare(ticks = $step);");
}

/**
 * Read console line input. If the system has an extension installed, the extension function is used first.
 */
function readline($msg) {
	$fh = fopen('php://stdin', 'r');
	if ($msg) {
		echo $msg;
	}
	$userInput = trim(fgets($fh));
	fclose($fh);
	return $userInput;
}

/**
 * Try calling the function
 * @param callable $payload processing function, returning FALSE means aborting subsequent attempts
 * @param int $tries The number of additional attempts when an error occurs (excluding the first normal execution)
 * @return int total number of attempts (excluding the first normal execution)
 */
function try_many_times($payload, $tries = 0) {
	$tryCount = 0;
	while ($tryCount++ < $tries) {
		try {
			if ($payload() === false) {
				return $tryCount - 1;
			}
		} catch (\Exception $e) {
		}
	}
	return $tryCount - 1;
}

/**
 * Check if the parameter is empty
 * @param array $params
 * @param string[] $check_fields
 * @param bool $allow_empty whether to allow empty values
 * @throws InvalidArgumentException
 */
function param_check_required(array $params, array $check_fields, $allow_empty = false) {
	$check_fields = array_flip($check_fields);
	$check_fields = array_intersect_key($params, $check_fields);
	if (count($check_fields) != count($check_fields)) {
		throw new InvalidArgumentException('Missing required parameters: ' . join(',', array_keys($check_fields)));
	}
	foreach ($check_fields as $k => $v) {
		if (!$allow_empty && empty($v)) {
			throw new InvalidArgumentException("Parameter [$k] cannot be empty");
		}
	}
}

/**
 * Program debugging function
 * Calling method: dump($var1, $var2, ..., 1), when the last value is 1, it means to exit (die) the program
 */
function dump() {
	$params = func_get_args();
	$cli = PHP_SAPI === 'cli';
	$exit = false;
	echo !$cli ? PHP_EOL . '<pre style="color:green;">' . PHP_EOL : PHP_EOL;

	if (count($params)) {
		$tmp = $params;
		$exit = array_pop($tmp) === 1;
		$params = $exit ? array_slice($params, 0, -1) : $params;
		$comma = '';
		foreach ($params as $var) {
			echo $comma;
			var_dump($var);
			$comma = str_repeat('-', 80) . PHP_EOL;
		}
	}

	//remove closure calling & print out location.
	$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
	if (isset($GLOBALS['DUMP_WITH_TRACE']) && $GLOBALS['DUMP_WITH_TRACE']) {
		echo "[trace]", PHP_EOL;
		print_trace($trace, true, true);
	} else {
		print_trace([$trace[0]]);
	}
	echo str_repeat('=', 80), PHP_EOL, (!$cli ? '</pre>' : '');
	$exit && exit();
}

/**
 * Check whether the variable can be printed (such as strings, numbers, objects containing toString methods, etc.)
 * Boolean values, resources, etc. are not printable variables
 * @param mixed $var
 * @param string $print_str printable string
 * @return bool whether it is printable
 */
function printable($var, &$print_str = '') {
	$type = gettype($var);
	if (in_array($type, ['integer', 'double', 'float', 'string'])) {
		$print_str = $var . '';
		return true;
	}
	if (is_object($var) && method_exists($var, '__toString')) {
		$print_str = call_user_func([$var, '__toString']);
		return true;
	}
	if (is_callable($var)) {
		$ret = call_user_func($var);
		return printable($ret, $print_str);
	}
	return false;
}

/**
 * Print exception information
 * @param \Exception $ex
 * @param bool $include_external_properties whether to include additional exception information
 * @param bool $as_return whether to process in return mode (not printing exceptions)
 * @return string
 */
function print_exception(Exception $ex, $include_external_properties = false, $as_return = false) {
	$class = get_class($ex);
	$msg = "[$class]\n\t" . $ex->getMessage() . "\n\n";
	$msg .= "[Location]\n\t" . $ex->getFile() . ' #' . $ex->getLine() . "\n\n";
	$msg .= "[Trace]\n\t" . str_replace("\n", "\n\t", $ex->getTraceAsString());

	if ($include_external_properties) {
		$ignore_properties = ['message', 'code', 'line', 'file', 'trace'];
		$ro = new ReflectionObject($ex);
		foreach ($ro->getProperties() as $property) {
			if (in_array($property->getName(), $ignore_properties)) {
				continue;
			}
			$msg .= "\n\n[" . $property->getName() . "]\n\t";
			$msg .= $property->isPublic() ? var_export_min($property->getValue($ex), true) : $property->getDeclaringClass();
		}
	}
	if (!$as_return) {
		echo $msg;
	}
	return $msg;
}

/**
 * Print trace information
 * @param array $trace
 * @param bool $with_callee
 * @param bool $with_index
 * @param bool $as_return
 * @return string
 */
function print_trace($trace, $with_callee = false, $with_index = false, $as_return = false) {
	$ct = count($trace);
	$str = '';
	foreach ($trace as $k => $item) {
		$callee = '';
		if ($with_callee) {
			$vs = [];
			foreach ($item['args'] as $arg) {
				$vs[] = var_export_min($arg, true);
			}
			$arg_statement = join(',', $vs);
			$arg_statement = substr(str_replace("\n", '', $arg_statement), 0, 50);
			$callee = $item['class'] ? "\t{$item['class']}{$item['type']}{$item['function']}($arg_statement)" : "\t{$item['function']}($arg_statement)";
		}
		if ($with_index) {
			$str .= "[" . ($ct - $k) . "] ";
		}
		$loc = $item['file'] ? "{$item['file']} #{$item['line']} " : '';
		$str .= $loc . $callee . PHP_EOL;
	}
	if ($as_return) {
		return $str;
	}
	echo $str;
	return null;
}

/**
 * Print system errors and trace information
 * @param integer $code
 * @param string $msg
 * @param string $file
 * @param integer $line
 * @param string $trace_string
 */
function print_sys_error($code, $msg, $file = null, $line = null, $trace_string = '') {
	echo "<pre>";
	$code = error2string($code);
	echo "[$code] $msg\n\n";
	echo "* $file #$line\n\n";

	if (!$trace_string) {
		$bs = debug_backtrace();
		array_shift($bs);
		foreach ($bs as $k => $b) {
			echo count($bs) - $k . " {$b['class']}{$b['type']}{$b['function']}\n";
			echo " {$b['file']} #{$b['line']} \n\n";
		}
	} else {
		echo $trace_string;
	}
	die;
}

/**
 * Convert error code value to string
 * @param int $code
 * @return string
 */
function error2string($code) {
	$level_names = array(
		E_ERROR => 'E_ERROR',
		E_WARNING => 'E_WARNING',
		E_PARSE => 'E_PARSE',
		E_NOTICE => 'E_NOTICE',
		E_CORE_ERROR => 'E_CORE_ERROR',
		E_CORE_WARNING => 'E_CORE_WARNING',
		E_COMPILE_ERROR => 'E_COMPILE_ERROR',
		E_COMPILE_WARNING => 'E_COMPILE_WARNING',
		E_USER_ERROR => 'E_USER_ERROR',
		E_USER_WARNING => 'E_USER_WARNING',
		E_USER_NOTICE => 'E_USER_NOTICE',
	);
	if (defined('E_STRICT')) {
		$level_names[E_STRICT] = 'E_STRICT';
	}
	$levels = array();
	if (($code & E_ALL) == E_ALL) {
		$levels[] = 'E_ALL';
		$code &= ~E_ALL;
	}
	foreach ($level_names as $level => $name) {
		if (($code & $level) == $level) {
			$levels[] = $name;
		}
	}
	return implode(' | ', $levels);
}

/**
 * Convert error codes to specific code values
 * @param string $string
 * @return int
 * @example string2error('E_ALL')
 */
function string2error($string) {
	$value = 0;
	$levels = explode('|', $string);
	foreach ($levels as $level) {
		$level = trim($level);
		if (defined($level)) {
			$value |= (int)constant($level);
		}
	}
	return $value;
}

/**
 * check obj is instance of class list
 * @param object $obj
 * @param string[] $class_list
 * @return string|null matched class name，null for mismatch
 */
function instanceof_list($obj, array $class_list) {
	foreach ($class_list as $class) {
		if ($obj instanceof $class) {
			return $class;
		}
	}
	return null;
}

/**
 * Convert the exception object to other specified exception class objects
 * @param Exception $exception
 * @param string $target_class
 * @return mixed
 */
function exception_convert(Exception $exception, $target_class) {
	/** @var Exception $new_exception */
	return new $target_class($exception->getMessage(), $exception->getCode(), $exception);
}

/**
 * override current exception with more information
 * @param Exception $e
 * @param string|null $message if not null, new message was set
 * @param string|null $file if not null, new file location was set
 * @param string|null $line if not null, new file-line number was set
 * @return Exception
 */
function exception_override($e, $message = null, $file = null, $line = null) {
	return RetweetException::retweetException($e, $message, $file, $line);
}

/**
 * Register to convert PHP errors into exceptions
 * php fatal error was un-catchable
 * @param int $error_levels error levels to be converted, default is current error_reporting() value
 * @param \ErrorException|null $exception_class
 * @return callable|null
 * @throws \ErrorException
 */
function register_error2exception($error_levels = null, ErrorException $exception_class = null) {
	$error_levels = $error_levels ?: error_reporting();
	return set_error_handler(function ($err_severity, $err_str, $err_file, $err_line) use ($exception_class) {
		if (error_reporting() === 0) {
			throw new ErrorException('Error suppressed by @ operator, Err:' . $err_str, 0, $err_severity, $err_file, $err_line);
			return false;
		}
		if ($exception_class) {
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
 * Check if it is a function
 * @param mixed $f
 * @return boolean
 */
function is_function($f) {
	return (is_string($f) && function_exists($f)) || ($f instanceof Closure);
}

/**
 * Get all inherited parent classes of objects and classes (including trait classes)
 * If you don't need traits, try class_parents
 * @param string|object $class_or_object
 * @return string[]
 */
function class_uses_recursive($class_or_object) {
	$class = is_object($class_or_object) ? get_class($class_or_object) : $class_or_object;
	$chains[] = $class;
	$chains += class_parents($class);

	foreach ($chains as $cls) {
		$chains += trait_uses_recursive($cls);
	}
	return array_unique($chains);
}

/**
 * Get traits recursively
 * @param string $trait
 * @return array
 */
function trait_uses_recursive($trait) {
	$traits = class_uses($trait);
	foreach ($traits as $trait) {
		$traits += trait_uses_recursive($trait);
	}
	return $traits;
}

/**
 * Get the name of the specified class constant
 * @param string $class class name
 * @param mixed $const_val constant value
 * @return string|null
 * @throws \ReflectionException
 */
function get_constant_name($class, $const_val) {
	$class = new ReflectionClass($class);
	$constants = $class->getConstants();
	foreach ($constants as $name => $value) {
		if ($value === $const_val) {
			return $name;
		}
	}
	return null;
}

/**
 * Handle assertions by throwing exceptions
 * @param mixed $expression assertion value
 * @param string $err_msg
 * @param string $exception_class exception class, default is \Exception
 */
function assert_via_exception($expression, $err_msg, $exception_class = Exception::class) {
	if (!$expression) {
		throw new $exception_class($err_msg);
	}
}

/**
 * pdog
 * @param string $fun
 * @param callable|string $handler
 * @deprecated ticks no triggered in PHP 7.0+
 */
function pdog($fun, $handler) {

	declare(ticks=1);
	register_tick_function(function () use ($fun, $handler) {
		$debug_list = debug_backtrace();
		foreach ($debug_list as $info) {
			if ($info['function'] == $fun) {
				call_user_func($handler, $info['args']);
			}
		}
	});
}

/**
 * Get the current context GUID
 * @return mixed
 */
function guid() {
	global $__guid__;
	return $__guid__++;
}

/**
 * Export variables using minimal format (similar to var_export)
 * @param mixed $var
 * @param bool $return whether to return in return mode, the default is to output to the terminal
 * @return string|null
 */
function var_export_min($var, $return = false) {
	if (is_array($var)) {
		$toImplode = array();
		foreach ($var as $key => $value) {
			$toImplode[] = var_export($key, true) . '=>' . var_export_min($value, true);
		}
		$code = 'array(' . implode(',', $toImplode) . ')';
		if ($return) {
			return $code;
		} else {
			echo $code;
		}
	} else {
		return var_export($var, $return);
	}
	return null;
}

/**
 * Detect memory overflow. It is not recommended enabling this check when running the code to avoid performance loss.
 * @param bool $with_trace_info
 */
function memory_leak_check($with_trace_info = true) {
	static $last_usage;
	$current_usage = memory_get_usage(true);
	$trace_msg = '';
	if ($with_trace_info) {
		$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
		$trace_msg = sprintf(" %s#%s %s%s%s()", $trace[1]['file'], $trace[1]['line'], $trace[1]['class'], $trace[1]['type'], $trace[1]['function']);
	}
	if (!isset($last_usage)) {
		print_r(sprintf("[MEM] Start %s%s\n", format_size($current_usage), $trace_msg));
	} else {
		print_r(sprintf("[MEM] %s(+%s)%s\n", format_size($current_usage), format_size($current_usage - $last_usage), $trace_msg));
	}
	$last_usage = $current_usage;
}

/**
 * Code management
 * @param string $tag
 * @param bool $trace_location
 * @param bool $mem_usage
 * @return mixed
 */
function debug_mark($tag = '', $trace_location = true, $mem_usage = true) {
	$k = __FUNCTION__;
	$tm = microtime(true);
	$trace = $mem = null;
	if ($mem_usage) {
		$mem = memory_get_usage(true);
	}
	if ($trace_location) {
		$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
	}

	if (!isset($GLOBALS[$k])) {
		$GLOBALS[$k] = [];
	}
	$GLOBALS[$k][] = [$tag, $tm, $trace, $mem];
	return $GLOBALS[$k];
}

/**
 * Output dot information
 * @param bool $as_return
 * @return string|null
 */
function debug_mark_output($as_return = false) {
	$k = __NAMESPACE__ . '\\debug_mark';
	$list = $GLOBALS[$k];
	//Fill in the PHP service start time
	array_unshift($list, ['server request', $_SERVER['REQUEST_TIME_FLOAT'], null, 0]);
	if ($as_return) {
		return $list;
	}
	$str = '';
	foreach ($list as [$tag, $float_time, $trace, $mem]) {
		$time_txt = float_time_to_date($float_time);
		$mem_txt = $mem ? format_size($mem) : '';
		$callee = $file_loc = '';
		if ($trace) {
			$callee = $trace['class'] ? "{$trace['class']}{$trace['type']}{$trace['function']}()" : "{$trace['function ']}()";
			$file_loc = $trace['file'] . '#' . $trace['line'];
		}
		$str .= "{$time_txt} [{$mem_txt}] $tag $callee $file_loc" . PHP_EOL;
	}
	echo $str;
	return null;
}

/**
 * retweet exception with more information
 */
class RetweetException extends Exception {
	public static function retweetException(Exception $e, $message = null, $file = null, $line = null) {
		$ex = new self($message ?: $e->getMessage(), $e->getCode(), $e);
		$ex->file = $file ?: $e->getFile();
		$ex->line = $line ?: $e->getLine();
		return $ex;
	}
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
