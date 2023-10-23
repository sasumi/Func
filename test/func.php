<?php

use function LFPhp\Func\dump;
use function LFPhp\Func\make_date_ranges;

include __DIR__.'/../vendor/autoload.php';

$start = '2023-10-02';
$end = '2024-02-10';

dump(make_date_ranges($start, $end, 'Y-m'));