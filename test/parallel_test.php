<?php

use function LFPhp\Func\dump;
use function LFPhp\Func\rand_string;
use function LFPhp\Func\run_command_parallel_width_progress;

include "../vendor/autoload.php";
$params = [];

$c = 1000;
while($c-- > 0){
	$params[] = ['str' => "word $c:".rand_string()];
}
$cmd = 'php hello.php';
run_command_parallel_width_progress($cmd, $params);
dump('DONE');