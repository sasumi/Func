<?php

/**
 * Session Enhancement Functions
 */
namespace LFPhp\Func;

/**
 * Open a session once
 * If the original session status is not open, the session will be automatically closed after reading to avoid session locking
 * @return bool
 */
function session_start_once(){
	if(php_sapi_name() === 'cli' || session_status() === PHP_SESSION_DISABLED || headers_sent()){
		return false;
	}
	$initialized = session_status() === PHP_SESSION_ACTIVE;
	if(!$initialized && !headers_sent()){
		session_start();
		session_write_close();
	}
	return true;
}

/**
 * Submit session data immediately and selectively close the session based on the context
 */
function session_write_once(){
	session_write_scope(function(){});
}

/**
 * Automatically determine the current session status and write data from $_SESSION to the session
 * If the original session status is not open, the session will be automatically closed after the write operation is completed to avoid session locking, otherwise it will remain unchanged
 * Calling method:
 * session_write_scope(function(){
 * $_SESSION['hello'] = 'world';
 * unset($_SESSION['info']);
 * });
 * @param callable $handler
 * @return bool
 */
function session_write_scope(callable $handler){
	if(php_sapi_name() === 'cli' || session_status() === PHP_SESSION_DISABLED){
		call_user_func($handler);
		return false;
	}
	$initialized = session_status() === PHP_SESSION_ACTIVE;
	if(!$initialized && !headers_sent()){
		$exists_session = $_SESSION; //The original PHP session_start() method will overwrite the $_SESSION variable, so a recovery is required here.
		session_start();
		$_SESSION = $exists_session;
	}
	call_user_func($handler);
	if(!$initialized){
		session_write_close();
	}
	return true;
}

/**
 * Start the session at the specified time
 * @param int $expire_seconds seconds
 * @return void
 */
function session_start_in_time($expire_seconds = 0){
	//Set session.gc_maxlifetime
	if($expire_seconds == 0){
		$expire_seconds = ini_get('session.gc_maxlifetime');
	}else{
		ini_set('session.gc_maxlifetime', $expire_seconds);
	}

	//Set session.cache_expire
	session_cache_expire($expire_seconds);
	if(empty($_COOKIE['PHPSESSID'])){
		session_set_cookie_params($expire_seconds);
		session_start();
	}else{
		session_start();
		setcookie('PHPSESSID', session_id(), time() + $expire_seconds);
	}
}
