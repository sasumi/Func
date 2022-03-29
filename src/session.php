<?php

namespace LFPhp\Func;

/**
 * 开启session一次
 * 如原session状态未开启，则读取完session自动关闭，避免session锁定
 * @return bool
 */
function session_start_once(){
	if(php_sapi_name() === 'cli' ||
		session_status() === PHP_SESSION_DISABLED ||
		headers_sent()){
		return false;
	}
	$initialized = session_status() === PHP_SESSION_ACTIVE;
	if(!$initialized && !headers_sent()){
		session_start();
		session_write_close();
	};
	return true;
}

/**
 * 立即提交session数据，同时根据上下文环境，选择性关闭session
 */
function session_write_once(){
	session_write_scope(function(){});
}

/**
 * 自动判断当前session状态，将$_SESSION写入数据到session中
 * 如原session状态时未开启，则写入操作完毕自动关闭session避免session锁定，否则保持不变
 * 调用方法：
 * session_write_scope(function(){
 *      $_SESSION['hello'] = 'world';
 *      unset($_SESSION['info']);
 * });
 * @param $handler
 * @return bool
 */
function session_write_scope(callable $handler){
	if(php_sapi_name() === 'cli' || session_status() === PHP_SESSION_DISABLED){
		call_user_func($handler);
		return false;
	}
	$initialized = session_status() === PHP_SESSION_ACTIVE;
	if(!$initialized && !headers_sent()){
		$exists_session = $_SESSION; //原PHP session_start()方法会覆盖 $_SESSION 变量，这里需要做一次恢复。
		session_start();
		$_SESSION = $exists_session;
	}
	call_user_func($handler);
	if(!$initialized){
		session_write_close();
	}
	return true;
}