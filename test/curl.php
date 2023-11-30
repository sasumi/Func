<?php

use function LFPhp\Func\curl_post_file;
use function LFPhp\Func\dump;

include __DIR__.'/../vendor/autoload.php';

if($_GET['a']){
	dump($_FILES, 1);
}

$ret = curl_post_file('http://localhost/func/test/curl.php?a=1', ['abc' => __FILE__]);
dump($ret);
