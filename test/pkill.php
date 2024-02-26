<?php

use function LFPhp\Func\dump;
use function LFPhp\Func\make_date_ranges;

include __DIR__.'/../vendor/autoload.php';

$ret = \LFPhp\Func\pkill(1991);
dump($ret);
