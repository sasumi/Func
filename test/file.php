<?php

use function LFPhp\Func\dump;
use function LFPhp\Func\file_fix_extension;

include __DIR__.'/../vendor/autoload.php';
$f = __DIR__.'/f2';

dump(file_fix_extension($f, mime_content_type($f)));
