<?php
$opt = getopt('', ['str::']);

$s = rand(0, 5);
sleep($s);

echo "DONE IN $s seconds.";