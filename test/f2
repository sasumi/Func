<?php

use function LFPhp\Func\get_screen_size;
use function LFPhp\Func\show_progress;

include __DIR__.'/../vendor/autoload.php';

$screen_size = get_screen_size();

$source_text = 'Zero-based AI Business Illustration Master Class [From Beginner to Mastery] Distribution courses have been cleaned up: Zero-based AI Business Illustration Master Class [From Beginner to Mastery] Distribution courses have been cleaned up: Zero-based AI Business Illustration Master Class [From Beginner to Mastery] Distribution courses have been cleaned up: Zero-based AI Business Illustration Master Class [From Beginner to Mastery]';

$i = 0;
//while($i++ < 1000000){
//	usleep(10);
//	$txt = substr($source_text, 0, rand(20, 30));
//	show_progress($i, 100, $txt);
//}

$tt = 1000;
$k = 0;
while($k++<$tt){
	show_progress($k, $tt, function()use($source_text){
		echo substr($source_text, 0, rand(10, 30));
	});
}

echo "DONE";
