<?php
/**
 * HTTP CSP Functions
 */
namespace LFPhp\Func;

const CSP_PREFIX = 'Content-Security-Policy';
const CSP_REPORT_ONLY_PREFIX = 'Content-Security-Policy-Report-Only';

const CSP_POLICY_CUSTOM = '%s'; //Policy rule: custom
const CSP_POLICY_SELF = "'self'"; //Policy rule: this domain
const CSP_POLICY_NONE = "'none'"; //Policy rule: disable
const CSP_POLICY_SCRIPT_UNSAFE_INLINE = 'unsafe-inline'; //Policy rule: allow execution of inline <script> tags and event listener functions
const CSP_POLICY_SCRIPT_UNSAFE_EVAL = 'unsafe-eval'; //Policy rule: allow execution of strings as code, such as using eval, setTimeout, setInterval, and Function functions.
const CSP_POLICY_SCRIPT_NONCE = "'nonce-%s'"; //Policy rule: each HTTP response gives an authorization token, the inline script must have this token to execute
const CSP_POLICY_SCRIPT_HASH = "'%s'"; //Policy rule: list the hash values of allowed script code, the hash value of the inline script must match in order to execute.

const CSP_RESOURCE_DEFAULT = 'default-src'; //Resource rule: default
const CSP_RESOURCE_SCRIPT = 'script-src';   //Resource rule: external script source
const CSP_RESOURCE_STYLE = 'style-src'; //Resource rule: style sheet source
const CSP_RESOURCE_IMG = 'img-src'; //Resource rule: image source
const CSP_RESOURCE_MEDIA = 'media-src'; //Resource rule: media file source (audio and video)
const CSP_RESOURCE_FONT = 'font-src';   //Resource rule: font file source
const CSP_RESOURCE_OBJECT = 'object-src';   //Resource rule: plugin source (e.g. Flash)
const CSP_RESOURCE_CHILD = 'child-src'; //Resource rule: frame source
const CSP_RESOURCE_FRAME = 'frame-ancestors';   //Resource rule: embedded external resources (e.g. <frame>, <iframe>, <embed>, and <applet>)
const CSP_RESOURCE_CONNECT = 'connect-src'; //Resource rule: HTTP connections (via XHR, WebSockets, EventSource, etc.)
const CSP_RESOURCE_WORKER = 'worker-src';   //Resource rule: worker script
const CSP_RESOURCE_MANIFEST = 'manifest-src';   //Resource rule: manifest file

/**
 * Build CSP rule
 * @param string $resource Resource
 * @param string $policy Policy
 * @param string $custom_defines Policy extension data (mainly for CSP_POLICY_SCRIPT_NONCE CSP_POLICY_SCRIPT_HASH)
 * @return string
 */
function csp_content_rule($resource, $policy, $custom_defines = ''){
	return "$resource ".(strpos($policy, '%s') !== false ? printf($policy, $custom_defines) : $policy);
}

/**
 * Build CSP reporting rule
 * @param string $uri
 * @return string
 */
function csp_report_uri($uri){
	return " report-uri $uri";
}
