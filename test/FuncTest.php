<?php
namespace LFPhp\Func\TestCase;

use PHPUnit\Framework\TestCase;
use function LFPhp\Func\debug_mark;
use function LFPhp\Func\debug_mark_output;
use function LFPhp\Func\get_time_left;
use function LFPhp\Func\int2str;
use function LFPhp\Func\mk_utc;

class FuncTest extends TestCase {
	public function __construct($name = null, array $data = [], $dataName = ''){
		debug_mark('hello');
		parent::__construct($name, $data, $dataName);
	}

	public function testString(){
		$s = int2str(123);
		$this->assertEquals("123", $s);
	}

	public function testDebugMark(){
		$a = function(){
			debug_mark('111');
		};

		sleep(1);
		debug_mark('1231');
		debug_mark_output();

	}

	public function testTime(){
		$r = get_time_left();
		$this->assertNull($r);

		$utc = mk_utc(time());
		$this->assertTrue(is_string($utc));
	}
}