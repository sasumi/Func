<?php

use function LFPhp\Func\cron_watch_commands;

require_once dirname(__DIR__).'/autoload.php';

cron_watch_commands([
	'* * * * * php -i',
	'* * * * * echo "helloworld"',
]);