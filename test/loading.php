<?php

use function LFPhp\Func\show_loading;

include __DIR__.'/../vendor/autoload.php';

$i = 0;
while($i++ < 1000000){
	usleep(100);
	show_loading("Loading···", ["\\", "|", "/", "-"]);
}

echo "DONE";
