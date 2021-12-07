<?php
namespace LFPhp\Func\TestCase;
use function LFPhp\Func\csp_content_rule;
use function LFPhp\Func\csp_report_uri;
use function LFPhp\Func\html_meta_csp;
use const LFPhp\Func\CSP_POLICY_SELF;
use const LFPhp\Func\CSP_RESOURCE_DEFAULT;
use const LFPhp\Func\CSP_RESOURCE_SCRIPT;

class CSPTest extends \PHPUnit\Framework\TestCase {
	public function testCSP(){
		$rule1 = csp_content_rule(CSP_RESOURCE_SCRIPT, CSP_POLICY_SELF);
		$rule2 = csp_content_rule(CSP_RESOURCE_DEFAULT, CSP_POLICY_SELF);
		$str = html_meta_csp([$rule1, $rule2], csp_report_uri('http://www.baidu.com'));
		var_dump($str);
		$this->assertIsString($str);
	}
}