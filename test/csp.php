<?php

use function LFPhp\Func\csp_content_rule;
use function LFPhp\Func\csp_report_uri;
use function LFPhp\Func\http_header_csp;
use const LFPhp\Func\CSP_POLICY_SELF;
use const LFPhp\Func\CSP_RESOURCE_DEFAULT;

include __DIR__.'/../vendor/autoload.php';

$rpt_url = 'http://localhost/func/test/csp_rpt.php';

$rpt_id = 'default';
//\LFPhp\Func\http_header_report_api([$rpt_url], 'default');

http_header_csp([csp_content_rule(CSP_RESOURCE_DEFAULT, CSP_POLICY_SELF)], csp_report_uri($rpt_url));
//http_header_report_api_nel([$rpt_url], 'default', 10);
//http_header_csp([$rule]);
?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport"
	      content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Document</title>
</head>
<body>
asdfasdf
<script type="module">
	import {onReportApi} from "http://localhost/WebCom/dist/webcom.es.js";
	console.log('asdfasf',onReportApi);
	onReportApi.listen(reports => {
		console.log(reports);
	});

	setTimeout(()=>{
		document.getElementById('id').innerHTML = '<img src="a.jpg" alt="" style="display:block; border:10px solid green; width:100px; height:100px;">';
	}, 1000);
</script>

<div id="id"></div>
</body>
</html>
