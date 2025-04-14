<?php
/**
 * Array Enhancement Functions
 */
namespace LFPhp\Func;

use ArrayAccess;
use Exception;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use stdClass;

/**
 * Define array posing
 */
const ARRAY_POSING_HEAD = 0x001;
const ARRAY_POSING_BEFORE = 0x002;
const ARRAY_POSING_AFTER = 0x003;
const ARRAY_POSING_LAST = 0x004;

/**
 * Check array is assoc array
 * @param array $arr
 * @return boolean
 */
function is_assoc_array($arr){
	return is_array($arr) && array_values($arr) != $arr;
}

/**
 * Array group by function
 * group array by $group_key
 * @param array $arr source array, multi-dimensional array (record set)
 * @param scalar $group_key key to group by
 * @param boolean $force_unique array key is unique in list
 * @return array
 */
function array_group($arr, $group_key, $force_unique = false){
	if(empty ($arr) || !is_array($arr)){
		return $arr;
	}
	if($force_unique){
		return array_column($arr, null, $group_key);
	}
	$_result = [];
	foreach($arr as $item){
		$_result[$item[$group_key]][] = $item;
	}
	return $_result;
}

/**
 * Make chunk from specified number range
 * @param int $start number start
 * @param int $end number end
 * @param int $size chunk size
 * @return \Generator
 * @example sprite number range: 2-9, chunk size is 3, so function export a generator: [2,3,4],[5,6,7],[8,9]
 */
function range_slice($start, $end, $size){
	$page_count = ceil(($end - $start)/$size);
	for($i = 0; $i < $page_count; $i++){
		yield [$start + $i*$size, min($start + ($i + 1)*$size, $end)];
	}
}

/**
 * Shuffle objects, key original assoc key
 * @param array|object $objects
 * @return array
 */
function object_shuffle($objects){
	$keys = array_keys($objects);
	shuffle($keys);
	$tmp = [];
	foreach($keys as $k){
		$tmp[$k] = $objects[$k];
	}
	return $tmp;
}

/**
 * Get random array item
 * don't like array_rand, this function always return an array
 * @link http://php.net/manual/zh/function.array-rand.php
 * @param array $arr source array, can be associative array or indexed array
 * @param int $count random count
 * @return array random array, keys will be preserved
 */
function array_random(array $arr = [], $count = 1){
	if(!$arr){
		return [];
	}
	if($count == 1){
		$key = array_rand($arr, 1);
		return [$key => $arr[$key]];
	}
	if(count($arr) <= $count){
		return $arr;
	}
	$keys = array_rand($arr, $count);
	$ret = [];
	foreach($keys as $k){
		$ret[$k] = $arr[$k];
	}
	return $ret;
}

/**
 * Check any keys exists in array
 * @param array $arr source array
 * @param array $keys keys to check
 * @return bool
 */
function array_keys_exists(array $arr, array $keys){
	return !array_diff_key(array_flip($keys), $arr);
}

/**
 * Flat array
 * @param array $arr source array
 * @return array
 */
function array_flatten(array $arr){
	$ret = [];
	array_walk_recursive($arr, function($item) use (&$ret){
		$ret[] = $item;
	});
	return $ret;
}

/**
 * 将多重数组值取出来，平铺成一维数组
 * @param array $arr
 * @param string $original_key
 * @param string $original_key_name
 * @return array
 */
function plain_items($arr, $original_key = '', $original_key_name = 'original_key'){
	if(count($arr) == count($arr, COUNT_RECURSIVE)){
		$arr[$original_key_name] = $original_key;
		return [$arr];
	}else{
		$ret = [];
		foreach($arr as $k => $item){
			$ret = array_merge($ret, plain_items($item, $k, $original_key_name));
		}
		return $ret;
	}
}

/**
 * Convert array to standard object
 * @param array $arr
 * @return \stdClass
 */
function array2object($arr){
	if(is_array($arr)){
		$obj = new StdClass();
		foreach($arr as $key => $val){
			$obj->$key = $val;
		}
	}else{
		$obj = $arr;
	}
	return $obj;
}

/**
 * Convert object to array
 * @param object $obj any object can convert, but not resource
 * @return array
 */
function object2array($obj){
	$obj = (array)$obj;
	foreach($obj as $k => $v){
		if(gettype($v) == 'resource'){
			return [];
		}
		if(gettype($v) == 'object' || gettype($v) == 'array'){
			$obj[$k] = object2array($v);
		}
	}
	return $obj;
}

/**
 * Restructure $_FILES array to correct name dimension
 * @param array $PHP_FILES PHP $_FILES variable
 * @return array
 */
function restructure_files(array $PHP_FILES){
	$output = [];
	foreach($PHP_FILES as $name => $array){
		foreach($array as $field => $value){
			$pointer = &$output[$name];
			if(!is_array($value)){
				$pointer[$field] = $value;
				continue;
			}
			$stack = [&$pointer];
			$iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($value), RecursiveIteratorIterator::SELF_FIRST);
			foreach($iterator as $key => $v){
				array_splice($stack, $iterator->getDepth() + 1);
				$pointer = &$stack[count($stack) - 1];
				$pointer = &$pointer[$key];
				$stack[] = &$pointer;
				if(!$iterator->callHasChildren()){
					$pointer[$field] = $v;
				}
			}
		}
	}
	return $output;
}

/**
 * Merge two arrays recursively and distinctly
 * @param array $array1 multi-dimensional array (record set)
 * @param array $array2 multi-dimensional array (record set)
 * @return array
 */
function array_merge_recursive_distinct(array $array1, array &$array2){
	$merged = $array1;
	foreach($array2 as $key => &$value){
		if(is_array($value) && isset ($merged [$key]) && is_array($merged [$key])){
			$merged [$key] = array_merge_recursive_distinct($merged [$key], $value);
		}else{
			$merged [$key] = $value;
		}
	}

	return $merged;
}

/**
 * Merge two arrays recursively
 * @param array $org_arr multi-dimensional array (record set)
 * @param array $new_arr multi-dimensional array (record set)
 * @param bool $recursive is recursive merge
 * @return array
 */
function array_merge_assoc(array $org_arr, array $new_arr, $recursive = false){
	foreach($new_arr as $k => $val){
		if($recursive && isset($org_arr[$k]) && is_array($val) && is_array($org_arr[$k])){
			$org_arr[$k] = array_merge_assoc($org_arr[$k], $val);
		}else{
			$org_arr[$k] = $val;
		}
	}
	return $org_arr;
}

/**
 * Clean array
 * @param array $arr multi-dimensional array (record set)
 * @param bool $clean_empty clear empty item(include empty array)
 * @param bool $clean_null clean while item is null
 * @param bool $trim_string trim string while item is string
 * @param scalar[] $specified_fields specified fields, empty for all fields
 * @param bool $recursive
 * @return array
 */
function array_clean($arr, $clean_empty = true, $clean_null = true, $trim_string = true, $specified_fields = [], $recursive = true){
	if(empty($arr) || !is_array($arr)){
		return $arr;
	}
	foreach($arr as $k => $item){
		if(!$specified_fields || in_array($k, $specified_fields)){
			if($trim_string && is_string($item)){
				$arr[$k] = trim($item);
			}
			if($clean_empty && empty($item)){
				unset($arr[$k]);
				continue;
			}
			if($clean_null && $item === null){
				unset($arr[$k]);
			}
		}
		if($recursive && is_array($item)){
			$arr[$k] = array_clean($item);
		}
	}
	return $arr;
}

/**
 * clean array null value
 * @param array|mixed $arr
 * @param bool $recursive
 * @return array
 */
function array_clean_null($arr, $recursive = true){
	return array_clean($arr, false, true, false, [], $recursive);
}

/**
 * Clean empty item in array
 * @param array $arr
 * @param bool $recursive
 * @return array
 */
function array_clean_empty($arr, $recursive = true){
	return array_clean($arr, true, false, false, [], $recursive);
}

/**
 * Trim array item
 * @param array $arr source array
 * @param scalar[] $specified_fields specified fields, empty for all fields
 * @param bool $recursive
 * @return array
 */
function array_trim($arr, $specified_fields = [], $recursive = true){
	return array_clean($arr, false, false, true, $specified_fields, $recursive);
}

/**
 * Array filter by fields
 * @param array $arr
 * @param scalar[] $reserved_fields
 * @param scalar[] $remove_fields
 * @param bool $recursive multi-dimensional array support
 * @return array
 */
function array_filter_fields($arr, $reserved_fields = [], $remove_fields = [], $recursive = false){
	if(!$reserved_fields && !$remove_fields){
		return $arr;
	}
	$ret = [];
	foreach($arr as $k => $item){
		//in remove fields, or no in reserved fields(if provided)
		if(($remove_fields && in_array($k, $remove_fields)) || ($reserved_fields && !in_array($k, $reserved_fields))){
			//remove item
		}else{
			if($recursive && is_array($item)){
				$item = array_filter_fields($item, $reserved_fields, $remove_fields);
			}
			$ret[$k] = $item;
		}
	}
	return $ret;
}

/**
 * Array filter by specified reserved values, or remove values
 * @param array $arr
 * @param array $reserved_values
 * @param array $remove_values
 * @return array
 */
function array_filter_values($arr, $reserved_values = [], $remove_values = []){
	return array_filter_recursive($arr, function($val) use ($reserved_values, $remove_values){
		if($remove_values && in_array($val, $remove_values)){
			return false;
		}
		if($reserved_values && !in_array($val, $reserved_values)){
			return false;
		}
		return true;
	});
}

/**
 * Array filter recursive
 * @param array[] $arr any-dimensional array
 * @param callable $payload $payload($val, $key), if return false, item will be removed
 * @return array
 */
function array_filter_recursive($arr, $payload){
	$ret = [];
	foreach($arr as $k => $item){
		if($payload($item, $k) === false){
			continue;
		}
		if(is_array($item)){
			$ret[$k] = array_filter_recursive($item, $payload);
		}
	}
	return $ret;
}

/**
 * Move specified index item
 * @param array $arr
 * @param string|number $target_index the item tobe handled
 * @param number $dir direction use constants: ARRAY_POSING_HEAD, ARRAY_POSING_BEFORE, ARRAY_POSING_AFTER, ARRAY_POSING_LAST
 * @return array
 * @throws \Exception
 */
function array_move_item($arr, $target_index, $dir){
	if($dir == ARRAY_POSING_HEAD){
		$tmp = $arr[$target_index];
		unset($arr[$target_index]);
		array_unshift_assoc($arr, $target_index, $tmp);
		return $arr;
	}else if($dir == ARRAY_POSING_LAST){
		$tmp = $arr[$target_index];
		unset($arr[$target_index]);
		$arr[$target_index] = $tmp;
		return $arr;
	}else if($dir == ARRAY_POSING_BEFORE){
		$keys = array_keys($arr);
		$values = array_values($arr);
		$new_idx = array_index($keys, $target_index);
		if($new_idx == 0){
			return $arr;
		}
		$before = array_combine(array_slice($keys, 0, $new_idx - 1), array_slice($values, 0, $new_idx - 1));
		$before[$target_index] = $arr[$target_index]; //當前
		$before[$keys[$new_idx - 1]] = $values[$new_idx - 1]; //上一個
		$after = array_combine(array_slice($keys, $new_idx + 1), array_slice($values, $new_idx + 1));
		return array_merge($before, $after);
	}else if($dir == ARRAY_POSING_AFTER){
		$keys = array_keys($arr);
		$values = array_values($arr);
		$new_idx = array_index($keys, $target_index);
		if($new_idx == count($arr) - 1){
			return $arr;
		}
		$tmp = array_slice($values, 0, $new_idx);
		$before = array_combine(array_slice($keys, 0, $new_idx), $tmp);
		$before[$keys[$new_idx + 1]] = $values[$new_idx + 1]; //下一個
		$before[$target_index] = $arr[$target_index]; //當前
		$after = array_combine(array_slice($keys, $new_idx + 2), array_slice($values, $new_idx + 2));
		return array_merge($before, $after);
	}else{
		throw new Exception('Array move direction no support:'.$dir);
	}
}

/**
 * Get first item of array
 * @param array $arr
 * @param null &$key matched key
 * @return mixed|null
 */
function array_first(array $arr = [], &$key = null){
	foreach($arr as $key => $item){
		return $item;
	}
	return null;
}

/**
 * Get last item of array
 * @param array $arr
 * @param null $key matched key
 * @return null
 */
function array_last(array $arr = [], &$key = null){
	if(!empty($arr)){
		$keys = array_keys($arr);
		$key = array_pop($keys);
		return $arr[$key];
	}
	return null;
}

/**
 * Assoc-array unshift
 * @param array &$arr
 * @param string $key
 * @param mixed $val
 * @return int
 */
function array_unshift_assoc(&$arr, $key, $val){
	$arr = array_reverse($arr, true);
	$arr[$key] = $val;
	$arr = array_reverse($arr, true);
	return count($arr);
}

/**
 * Assoc-array shift
 * @param array $arr
 * @return array|false return matched [value, key], false while empty
 */
function array_shift_assoc(array &$arr){
	foreach($arr as $key => $val){
		unset($arr[$key]);
		return [$val, $key];
	}
	return false;
}

/**
 * Array sort by specified key
 * @param array $arr
 * @return array
 * @example: array_orderby($data, 'volume', SORT_DESC, 'edition', SORT_ASC);
 */
function array_orderby($arr){
	if(empty($arr)){
		return $arr;
	}
	$args = func_get_args();
	$data = array_shift($args);
	foreach($args as $n => $field){
		if(is_string($field)){
			$tmp = [];
			foreach($data as $key => $row){
				$tmp[$key] = $row[$field];
			}
			$args[$n] = $tmp;
		}
	}
	$args[] = &$data;
	call_user_func_array('array_multisort', $args);
	return array_pop($args);
}

/**
 * calculate rank in array, return the compared position in collection
 * @param number $compareTo
 * @param number[] $collection
 * @param bool $lower_first start rank from lower number
 * @return int
 * @throws \Exception
 */
function rank($compareTo, $collection, $lower_first = false){
	if($lower_first){
		sort($collection);
		$rank = 1;
		foreach($collection as $val){
			if($val == $compareTo || $val < $compareTo){
				return $rank;
			}
			$rank++;
		}
		return $rank;
	}
	rsort($collection);
	$rank = 1;
	foreach($collection as $val){
		if($val == $compareTo || $val > $compareTo){
			return $rank;
		}
		$rank++;
	}
	return $rank;
}

/**
 * Array sort by values
 * @param array[] $src_arr multi-dimensional array (record set)
 * @param string $field field name
 * @param scalar[] $values value list
 * @param bool $pad_tail_on_mismatch pad tail on mismatch
 * @return array
 */
function array_orderby_values(array $src_arr, $field, array $values, $pad_tail_on_mismatch = false){
	$ret = [];
	foreach($values as $v){
		foreach($src_arr as $k => $item){
			if($item[$field] == $v){
				$ret[] = $item;
				unset($src_arr[$k]);
			}
		}
	}
	if($pad_tail_on_mismatch && $src_arr){
		$ret = array_merge($ret, $src_arr);
	}
	return $ret;
}

/**
 * Array sort by keys
 * @param array[]|array $src_arr
 * @param string[] $keys
 * @param bool $mismatch_to_head set mismatch item to head
 * @return array
 */
function array_orderby_keys($src_arr, $keys, $mismatch_to_head = false){
	if(empty($src_arr)){
		return $src_arr;
	}
	$tmp = [];
	foreach($keys as $k){
		if(isset($src_arr[$k])){
			$tmp[$k] = $src_arr[$k];
			unset($src_arr[$k]);
		}
	}
	if($src_arr){
		$tmp = $mismatch_to_head ? array_merge_assoc($src_arr, $tmp) : array_merge_assoc($tmp, $src_arr);
	}
	return $tmp;
}

/**
 * Get item index
 * @param array $array
 * @param callable|mixed $compare_fn_or_value
 * @return bool|int|string
 */
function array_index($array, $compare_fn_or_value){
	foreach($array as $k => $v){
		if(is_callable($compare_fn_or_value)){
			if($compare_fn_or_value($v) === true){
				return $k;
			}
		}else{
			if($compare_fn_or_value == $v){
				return $k;
			}
		}
	}
	return false;
}

/**
 * Sum array value
 * @param array[] $arr multi-dimensional array (record set)
 * @param string $field
 * @return float|int
 */
function array_sumby(array $arr, $field){
	return array_sum(array_column($arr, $field));
}

/**
 * array fix size
 * @param array $arr
 * @param int $length
 * @return array
 */
function array_fix_size(array $arr, $length){
	$len = count($arr);
	if($len == $length){
		return $arr;
	}
	if($len < $length){
		$tmp = [];
		for($i = 0; $i < ceil($length/$len); $i++){
			$tmp = array_merge($tmp, $arr);
		}
		$arr = $tmp;
	}
	return array_slice($arr, 0, $length);
}

/**
 * Set default values to array
 * Usage:
 * <pre>
 * $_GET = array_default($_GET, array('page_size'=>10), true);
 * </pre>
 * @param array $arr
 * @param array $values
 * @param bool $reset_empty reset empty value in array
 * @return array
 */
function array_default(array $arr, array $values, $reset_empty = false){
	foreach($values as $k => $v){
		if(!isset($arr[$k]) || ($reset_empty && empty($arr[$k]))){
			$arr[$k] = $v;
		}
	}
	return $arr;
}

/**
 * Create spreadsheet indexing columns
 * @param int $column_size
 * @return array
 */
function array_make_spreadsheet_columns($column_size){
	$ret = [];
	for($column = 1; $column <= $column_size; $column++){
		$ret[] = spreadsheet_get_column_index($column);
	}
	return $ret;
}

/**
 * Array push value by specified path
 * @param array $arr target array
 * @param string $path_str path string
 * @param mixed $value
 * @param string $glue path delimiter, default use dot(.)
 */
function array_push_by_path(&$arr, $path_str, $value, $glue = '.'){
	$paths = explode($glue, trim($path_str, $glue));
	$path_stm = '';
	foreach($paths as $path){
		$path_stm .= "['".addslashes($path)."']";
	}
	$val = var_export($value, true);
	$statement = "\$arr$path_stm = $val;";
	eval($statement);
}

/**
 * Fetch array value by path
 * @param array $arr source array
 * @param string $path_str path string
 * @param mixed $default 缺省值
 * @param string $delimiter path delimiter, default use dot(.)
 * @return mixed
 */
function array_fetch_by_path($arr, $path_str, $default = null, $delimiter = '.'){
	if(!$path_str){
		return $arr;
	}
	if(isset($arr[$path_str])){
		return $arr[$path_str];
	}
	foreach(explode($delimiter, $path_str) as $segment){
		if((!is_array($arr) || !array_key_exists($segment, $arr)) && (!$arr instanceof ArrayAccess || !$arr->offsetExists($segment))){
			return $default;
		}
		$arr = $arr[$segment];
	}
	return $arr;
}

/**
 * Alias for array_fetch_by_path()
 * @alias for array_fetch_by_path
 * @param array $arr
 * @param string $path_str
 * @param mixed $default
 * @param string $delimiter
 * @return mixed
 */
function array_get($arr, $path_str, $default = null, $delimiter = '.'){
	return array_fetch_by_path($arr, $path_str, $default, $delimiter);
}

/**
 * 断言数组是否用拥有指定键名
 * @param array $arr
 * @param array $keys
 * @throws \Exception
 */
function assert_array_has_keys($arr, $keys){
	foreach($keys as $key){
		if(!array_key_exists($key, $arr)){
			throw new Exception('Array key no exists:'.$key);
		}
	}
}

/**
 * Tree node list filter
 * @param string|int $parent_id parent node id
 * @param array[] $nodes tree node list
 * @param array $opt filter option
 * @param int $level init level
 * @param array $group_by_parents
 * @return array
 */
function array_filter_subtree($parent_id, $nodes, $opt = [], $level = 0, $group_by_parents = []){
	$opt = array_merge([
		'return_as_tree' => false,             //以目录树返回，还是以平铺数组形式返回
		'level_key'      => 'tree_level',      //返回数据中是否追加等级信息,如果选项为空, 则不追加等级信息
		'id_key'         => 'id',              //主键键名
		'parent_id_key'  => 'parent_id',       //父级键名
		'children_key'   => 'children'         //返回子集key(如果是平铺方式返回,该选项无效
	], $opt);

	$pn_k = $opt['parent_id_key'];
	$lv_k = $opt['level_key'];
	$id_k = $opt['id_key'];
	$as_tree = $opt['return_as_tree'];
	$c_k = $opt['children_key'];

	$result = [];
	$group_by_parents = $group_by_parents ?: array_group($nodes, $pn_k);

	foreach($nodes as $item){
		if($item[$pn_k] == $parent_id){
			$item[$lv_k] = $level;  //set level
			if(!$opt['return_as_tree']){
				$result[] = $item;
			}
			if(isset($item[$id_k]) && isset($group_by_parents[$item[$id_k]]) && $group_by_parents[$item[$id_k]]){
				$sub = array_filter_subtree($item[$id_k], $nodes, $opt, $level + 1, $group_by_parents);
				if(!empty($sub)){
					if($as_tree){
						$item[$c_k] = $sub;
					}else{
						$result = array_merge($result, $sub);
					}
				}
			}
			if($as_tree){
				$result[] = $item;
			}
		}
	}
	return $result;
}

/**
 * Array insert after specified key
 * @param array $src_array
 * @param mixed $data
 * @param string $rel_key
 * @return array|int
 */
function array_insert_after(array $src_array, $data, $rel_key){
	if(!in_array($rel_key, array_keys($src_array))){
		return array_push($src_array, $data);
	}else{
		$tmp_array = [];
		$len = 0;
		foreach($src_array as $key => $src){
			$tmp_array[$key] = $src;
			$len++;
			if($rel_key === $key){
				break;
			}
		}
		$tmp_array[] = $data;
		return array_merge($tmp_array, array_slice($src_array, $len));
	}
}

/**
 * Merge new array after specified key
 * @param array $src_array
 * @param array $new_array
 * @param string $rel_key
 * @return array
 */
function array_merge_after(array $src_array, array $new_array, $rel_key = ''){
	if(!in_array($rel_key, array_keys($src_array))){
		return array_merge($src_array, $new_array);
	}else{
		$tmp_array = [];
		$len = 0;

		foreach($src_array as $key => $src){
			$tmp_array[$key] = $src;
			$len++;
			if($rel_key === $key){
				break;
			}
		}
		$tmp_array = array_merge($tmp_array, $new_array);
		return array_merge($tmp_array, array_slice($src_array, $len));
	}
}

/**
 * Array transform by specified pattern
 * @param array $data
 * @param array $patterns new array key pattern, like: array('dddd' => array('aaaa', 'bbbb'))
 * @return mixed
 */
function array_transform(array $data, array $patterns){
	$ret_array = [];
	foreach($patterns as $key => $value){
		if(!is_int($key) && isset($data[$key])){
			if(is_array($value) && !empty($value)){
				$tmp = &$ret_array;
				foreach($value as $v){
					$tmp = &$tmp[$v];
				}
				$tmp = $data[$key];
			}else if(is_string($value)){
				$ret_array[$value] = $data[$key];
			}
		}else if(is_int($key) && isset($data[$value])){
			$ret_array[$value] = $data[$value];
		}
	}
	return $ret_array;
}

/**
 * Get Array value recursive
 * @param array $arr
 * @param string $key if no key specified, return whole array values
 * @return array
 */
function array_value_recursive(array $arr, $key = ''){
	$val = [];
	array_walk_recursive($arr, function($v, $k) use ($key, &$val){
		if(!$key || $k == $key){
			$val[] = $v;
		}
	});
	return $val;
}

/**
 * Convert assoc array to yaml string simple
 * @param array $data
 * @param int $indent init indent
 * @return string
 */
function array_to_yaml_simple($data, $indent = 0){
	$yaml = '';
	foreach($data as $key => $value){
		$yaml .= str_repeat('  ', $indent).$key.': ';
		if(is_array($value)){
			$yaml .= PHP_EOL.array_to_yaml_simple($value, $indent + 1);
		}else{
			$yaml .= $value.PHP_EOL;
		}
	}
	return $yaml;
}
