<?php
namespace LFPhp\Func\TestCase;


use PHPUnit\Framework\TestCase;
use function LFPhp\Func\int2str;

class FuncTest extends TestCase {
	public function testString(){
		$s = int2str(123);
		$this->assertEquals("123", $s);
	}
}