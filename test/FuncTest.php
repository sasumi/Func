<?php
namespace LFPhp\Func\TestCase;

use PHPUnit\Framework\TestCase;
use function LFPhp\Func\csp_content_rule;
use function LFPhp\Func\debug_mark;
use function LFPhp\Func\debug_mark_output;
use function LFPhp\Func\dump;
use function LFPhp\Func\get_time_left;
use function LFPhp\Func\html_meta_csp;
use function LFPhp\Func\http_header_csp;
use function LFPhp\Func\int2str;
use function LFPhp\Func\mk_utc;
use const LFPhp\Func\CSP_POLICY_NONE;
use const LFPhp\Func\CSP_POLICY_SELF;
use const LFPhp\Func\CSP_RESOURCE_DEFAULT;
use const LFPhp\Func\CSP_RESOURCE_SCRIPT;

class FuncTest extends TestCase {
	public function __construct($name = null, array $data = [], $dataName = ''){
		debug_mark('hello');
		parent::__construct($name, $data, $dataName);
	}

	public function testString(){
		$s = int2str(123);
		$this->assertEquals("123", $s);
	}

	public function testCSP(){
		$rules[] = csp_content_rule(CSP_RESOURCE_DEFAULT, CSP_POLICY_SELF);
		$rules[] = csp_content_rule(CSP_RESOURCE_SCRIPT, CSP_POLICY_NONE);
		$str = http_header_csp($rules, '/report.php');
		dump($str);
		$this->assertIsString($str);
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