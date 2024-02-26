<?php

use function LFPhp\Func\launch_daemon_task;

include __DIR__.'/../vendor/autoload.php';

function html_console_out(...$msg_list){
	$json_str_list = [];
	foreach($msg_list as $msg){
		$json_str_list[] = json_encode($msg, JSON_UNESCAPED_UNICODE);
	}
	file_put_contents(sys_get_temp_dir().'/daemon_task_launcher/runtime.log', date('Y-m-d H:i:s ').join(',', $json_str_list).PHP_EOL, FILE_APPEND);
	flush();
}

$pid = launch_daemon_task(function($heartbeat){
	while(true){
		sleep(2);
		$heartbeat(false);
		html_console_out("next loop");
	}
}, 'daemon_test', 10);

echo 'task start, pid:'.$pid;
