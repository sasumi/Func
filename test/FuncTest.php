<?php
namespace LFPhp\Func\TestCase;

use PHPUnit\Framework\TestCase;
use function LFPhp\Func\array_random;
use function LFPhp\Func\command_exists;
use function LFPhp\Func\csp_content_rule;
use function LFPhp\Func\debug_mark;
use function LFPhp\Func\debug_mark_output;
use function LFPhp\Func\dump;
use function LFPhp\Func\get_screen_size;
use function LFPhp\Func\get_time_left;
use function LFPhp\Func\get_windows_fonts;
use function LFPhp\Func\http_header_csp;
use function LFPhp\Func\int2str;
use function LFPhp\Func\memory_leak_check;
use function LFPhp\Func\mk_utc;
use function LFPhp\Func\rand_string;
use function LFPhp\Func\ttf_info;
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
		http_header_csp($rules, '/report.php');
		$this->assertIsString("OK");
	}

	public function testDebugMark(){
		$a = function(){
			debug_mark('111');
		};

		sleep(1);
		debug_mark('1231');
		debug_mark_output();
	}

	public function testGetSize(){
		$rows = getenv('ROWS');
		dump($rows, 1);
		dump(command_exists('git'), 1);
		$i = get_screen_size();
		dump($i, empty($i));
	}

	public function testTime(){
		$r = get_time_left();
		$this->assertNull($r);

		$utc = mk_utc(time());
		$this->assertTrue(is_string($utc));
	}

	public function testWinFonts(){
		$fonts = get_windows_fonts();
		dump($fonts);
		$this->assertIsArray($fonts);
		$this->assertNotEmpty($fonts);
	}

	public function testMemLeak(){
		$big_data = [];
		$i = 0;
		memory_leak_check();
		while($i++ < 10000){
			$big_data[] = json_decode('{}');
			$big_data[] = rand_string(10000);
			memory_leak_check();
		}
		memory_leak_check();
		$this->assertTrue(true);
	}

	public function testTTFInfo(){
		$fonts = get_windows_fonts();
		$ttfs = [];
		foreach($fonts as $font){
			if(strcasecmp(substr($font, -4), '.ttf') === 0){
				$ttfs[] = $font;
			}
		}
		$ttf = array_random($ttfs, 1);
		$ttf_info = ttf_info(current($ttf));
		dump($ttf_info, 1);
	}
}