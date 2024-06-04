<?php
/**
 * 自定义事件函数
 */
namespace LFPhp\Func;

const EVENT_PAYLOAD_HIT = 1; //事件命中
const EVENT_PAYLOAD_BREAK_NEXT = 2; //事件命中，且中断后续执行
const EVENT_PAYLOAD_NULL = 3; //未命中事件

global $__FUNC_EVENT_MAP__;
$__FUNC_EVENT_MAP__ = [];

/**
 * 触发事件（事件触发参数采用引用方式传参，支持修改）
 * @param string $event
 * @return int 返回状态标记：EVENT_PAYLOAD_
 */
function event_fire($event, &$p1 = null, &$p2 = null, &$p3 = null, &$p4 = null, &$p5 = null, &$p6 = null){
	$arg_limit = 7;
	$arg_count = func_num_args();
	if($arg_count > $arg_limit){
		throw new \Exception("event fire arguments overload:$arg_count (limitation: $arg_limit)");
	}
	global $__FUNC_EVENT_MAP__;
	$handle_list = isset($__FUNC_EVENT_MAP__[$event]) ? $__FUNC_EVENT_MAP__[$event] : [];
	if(!$handle_list){
		return EVENT_PAYLOAD_NULL;
	}
	$hit = EVENT_PAYLOAD_NULL;
	foreach($handle_list as list($id, $payload)){
		$hit = EVENT_PAYLOAD_HIT;
		if($payload($p1, $p2, $p3, $p4, $p5, $p6) === false){
			return EVENT_PAYLOAD_BREAK_NEXT;
		}
	}
	return $hit;
}

/**
 * 注册事件
 * @param string $event
 * @param callable $payload
 * @return string
 */
function event_register($event, $payload){
	$id = __NAMESPACE__.'-event-'.guid();
	global $__FUNC_EVENT_MAP__;
	if(!isset($__FUNC_EVENT_MAP__[$event])){
		$__FUNC_EVENT_MAP__[$event] = [];
	}
	$__FUNC_EVENT_MAP__[$event][] = [$id, $payload];
	return $id;
}

/**
 * 根据事件类型反注册事件
 * @param string $event
 */
function event_unregister_by_type($event){
	global $__FUNC_EVENT_MAP__;
	unset($__FUNC_EVENT_MAP__[$event]);
}

/**
 * 根据id反注册事件
 * @param string $reg_id
 */
function event_unregister_by_id($reg_id){
	global $__FUNC_EVENT_MAP__;
	foreach($__FUNC_EVENT_MAP__ as $ev => $handle_list){
		$tmp = [];
		foreach($handle_list as list($id, $payload)){
			if($id !== $reg_id){
				$tmp[] = [$id, $payload];
			}
		}
		$__FUNC_EVENT_MAP__[$ev] = $tmp;
	}
}
