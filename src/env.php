<?php

namespace LFPhp\Func;

function server_in_windows(){
	return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
}