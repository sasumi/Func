<?php
namespace LFPhp\Func\TestCase;


use PHPUnit\Framework\TestCase;
use function LFPhp\Func\get_time_left;
use function LFPhp\Func\int2str;
use function LFPhp\Func\mk_utc;

class FuncTest extends TestCase {
	public function testString(){
		$s = int2str(123);
		$this->assertEquals("123", $s);
	}

	public function testTime(){
		$r = get_time_left();
		$this->assertNull($r);

		$utc = mk_utc(time());
		$this->assertTrue(is_string($utc));
	}
}