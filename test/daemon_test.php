<?php

use function LFPhp\Func\daemon_process_alive;
use function LFPhp\Func\daemon_process_keepalive;
use function LFPhp\Func\daemon_process_mark_exit;
use function LFPhp\Func\process_signal;
use function LFPhp\Func\replay_current_script;
use const LFPhp\Func\DAEMON_PROCESS_STATE_PATH;

include __DIR__.'/../vendor/autoload.php';

echo 'PATH:', DAEMON_PROCESS_STATE_PATH, PHP_EOL;

if($e_pid = daemon_process_alive()){
	die("process already running:".$e_pid);
}

!defined('SIGTERM') && define('SIGTERM', 15);

echo "start new process:".getmypid(), PHP_EOL;

replay_current_script();

while(true){
	sleep(2);
	echo date('Y-m-d H:i:s'), " next loop", PHP_EOL;
	process_signal(SIGTERM, function(){
		echo "exit normal";
		daemon_process_mark_exit();
		die;
	});

	if(rand(0, 10) == 3){
		daemon_process_mark_exit(); //主动退出
		break;
	}
	daemon_process_keepalive();
}
