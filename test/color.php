<?php

use function LFPhp\Func\console_color;

include __DIR__.'/../vendor/autoload.php';

echo console_color("green text\n", 'green');
echo console_color('red test', 'red');
echo console_color("\nred bg\n", '', 'red');