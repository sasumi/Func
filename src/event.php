<?php
/**
 * Custom Event Functions
 */
namespace LFPhp\Func;

use Exception;

const EVENT_PAYLOAD_HIT = 1; //Event hit
const EVENT_PAYLOAD_BREAK_NEXT = 2; //Event hit, and interrupt subsequent execution
const EVENT_PAYLOAD_NULL = 3; //Event miss

global $__FUNC_EVENT_MAP__;
$__FUNC_EVENT_MAP__ = [];

/**
 * Trigger event (event trigger parameters are passed by reference and can be modified)
 * @param string $event
 * @return int return status flag: EVENT_PAYLOAD_
 */
function event_fire($event, &$p1 = null, &$p2 = null, &$p3 = null, &$p4 = null, &$p5 = null, &$p6 = null){
	$arg_limit = 7;
	$arg_count = func_num_args();
	if($arg_count > $arg_limit){
		throw new Exception("event fire arguments overload:$arg_count (limitation: $arg_limit)");
	}
	global $__FUNC_EVENT_MAP__;
	$handle_list = isset($__FUNC_EVENT_MAP__[$event]) ? $__FUNC_EVENT_MAP__[$event] : [];
	if(!$handle_list){
		return EVENT_PAYLOAD_NULL;
	}
	$hit = EVENT_PAYLOAD_NULL;
	foreach($handle_list as [$id, $payload]){
		$hit = EVENT_PAYLOAD_HIT;
		if($payload($p1, $p2, $p3, $p4, $p5, $p6) === false){
			return EVENT_PAYLOAD_BREAK_NEXT;
		}
	}
	return $hit;
}

/**
 * Event register
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
 * Unregister events based on event type
 * @param string $event
 */
function event_unregister_by_type($event){
	global $__FUNC_EVENT_MAP__;
	unset($__FUNC_EVENT_MAP__[$event]);
}

/**
 * Deregister event based on id
 * @param string $reg_id
 */
function event_unregister_by_id($reg_id){
	global $__FUNC_EVENT_MAP__;
	foreach($__FUNC_EVENT_MAP__ as $ev => $handle_list){
		$tmp = [];
		foreach($handle_list as [$id, $payload]){
			if($id !== $reg_id){
				$tmp[] = [$id, $payload];
			}
		}
		$__FUNC_EVENT_MAP__[$ev] = $tmp;
	}
}
