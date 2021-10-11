<?php
namespace LFPhp\Func;

const CSP_PREFIX = 'Content-Security-Policy';
const CSP_REPORT_ONLY_PREFIX = 'Content-Security-Policy-Report-Only';

const CSP_POLICY_CUSTOM = '%s';
const CSP_POLICY_SELF = "'self'";
const CSP_POLICY_NONE = "'none'";
const CSP_POLICY_SCRIPT_UNSAFE_INLINE = 'unsafe-inline'; //允许执行页面内嵌的<script>标签和事件监听函数
const CSP_POLICY_SCRIPT_UNSAFE_EVAL = 'unsafe-eval'; //允许将字符串当作代码执行，比如使用eval、setTimeout、setInterval和Function等函数。
const CSP_POLICY_SCRIPT_NONCE = "'nonce-%s'"; //每次HTTP回应给出一个授权token，页面内嵌脚本必须有这个token，才会执行
const CSP_POLICY_SCRIPT_HASH = "'%s'"; //列出允许执行的脚本代码的Hash值，页面内嵌脚本的哈希值只有吻合的情况下，才能执行。

const CSP_RESOURCE_DEFAULT = 'default-src';
const CSP_RESOURCE_SCRIPT = 'script-src';   //外部脚本
const CSP_RESOURCE_STYLE = 'style-src'; //样式表
const CSP_RESOURCE_IMG = 'img-src'; //图像
const CSP_RESOURCE_MEDIA = 'media-src'; //媒体文件（音频和视频）
const CSP_RESOURCE_FONT = 'font-src';   //字体文件
const CSP_RESOURCE_OBJECT = 'object-src';   //插件（比如 Flash）
const CSP_RESOURCE_CHILD = 'child-src'; //框架
const CSP_RESOURCE_FRAME = 'frame-ancestors';   //嵌入的外部资源（比如<frame>、<iframe>、<embed>和<applet>）
const CSP_RESOURCE_CONNECT = 'connect-src'; //HTTP 连接（通过 XHR、WebSockets、EventSource等）
const CSP_RESOURCE_WORKER = 'worker-src';   //worker脚本
const CSP_RESOURCE_MANIFEST = 'manifest-src';   //manifest 文件

/**
 * 构建CSP规则
 * @param string $resource 资源
 * @param string $policy 策略
 * @param string $custom_defines 策略扩展数据（主要针对 CSP_POLICY_SCRIPT_NONCE CSP_POLICY_SCRIPT_HASH）
 * @return string
 */
function csp_content_rule($resource, $policy, $custom_defines = ''){
	return "$resource ".(strpos($policy, '%s') !== false ? printf($policy, $custom_defines) : $policy);
}

/**
 * 构建CSP上报规则
 * @param string $uri
 * @return string
 */
function csp_report_uri($uri){
	return " report-uri $uri";
}