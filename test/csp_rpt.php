<?php
$str = json_encode($_REQUEST);
file_put_contents(__DIR__.'/csp.log', $str, FILE_APPEND);
