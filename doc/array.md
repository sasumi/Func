# Array
 > Array Enhancement Functions

## 1. is_assoc_array($arr): boolean
Check array is assoc array
#### Parameters
 - {array} *$arr* 

#### Returns
 - boolean 

## 2. array_group($arr,$group_key,$force_unique): array
Array group by function
group array by $group_key
#### Parameters
 - {array} *$arr* source array, multi-dimensional array (record set)
 - {scalar} *$group_key* key to group by
 - {boolean} *$force_unique* array key is unique in list

#### Returns
 - array 

## 3. range_slice($start,$end,$size): \Generator
Make chunk from specified number range
#### Parameters
 - {int} *$start* number start
 - {int} *$end* number end
 - {int} *$size* chunk size

#### Returns
 - \Generator 

## 4. object_shuffle($objects): array
Shuffle objects, key original assoc key
#### Parameters
 - {array|object} *$objects* 

#### Returns
 - array 

## 5. array_random($arr,$count): array
Get random array item
don't like array_rand, this function always return an array
#### Parameters
 - {array} *$arr* source array, can be associative array or indexed array
 - {int} *$count* random count

#### Returns
 - array random array, keys will be preserved

## 6. array_keys_exists($arr,$keys): bool
Check any keys exists in array
#### Parameters
 - {array} *$arr* source array
 - {array} *$keys* keys to check

#### Returns
 - bool 

## 7. array_flatten($arr): array
Flat array
#### Parameters
 - {array} *$arr* source array

#### Returns
 - array 

## 8. plain_items($arr,$original_key,$original_key_name): array
将多重数组值取出来，平铺成一维数组
#### Parameters
 - {array} *$arr* 
 - {string} *$original_key* 
 - {string} *$original_key_name* 

#### Returns
 - array 

## 9. array2object($arr): \stdClass
Convert array to standard object
#### Parameters
 - {array} *$arr* 

#### Returns
 - \stdClass 

## 10. object2array($obj): array
Convert object to array
#### Parameters
 - {object} *$obj* any object can convert, but not resource

#### Returns
 - array 

## 11. restructure_files($PHP_FILES): array
Restructure $_FILES array to correct name dimension
#### Parameters
 - {array} *$PHP_FILES* PHP $_FILES variable

#### Returns
 - array 

## 12. array_merge_recursive_distinct($array1,$array2): array
Merge two arrays recursively and distinctly
#### Parameters
 - {array} *$array1* multi-dimensional array (record set)
 - {array} *$array2* multi-dimensional array (record set)

#### Returns
 - array 

## 13. array_merge_assoc($org_arr,$new_arr,$recursive): array
Merge two arrays recursively
#### Parameters
 - {array} *$org_arr* multi-dimensional array (record set)
 - {array} *$new_arr* multi-dimensional array (record set)
 - {bool} *$recursive* is recursive merge

#### Returns
 - array 

## 14. array_clean($arr,$clean_empty,$clean_null,$trim_string,$recursive): array
Clean array
#### Parameters
 - {array} *$arr* multi-dimensional array (record set)
 - {bool} *$clean_empty* clear empty item(include empty array)
 - {bool} *$clean_null* clean while item is null
 - {bool} *$trim_string* trim string while item is string
 - {bool} *$recursive* 

#### Returns
 - array 

## 15. array_clean_null($data,$recursive): array
clean array null value
#### Parameters
 - {array|mixed} *$data* 
 - {bool} *$recursive* 

#### Returns
 - array 

## 16. array_clean_empty($data,$recursive): array
Clean empty item in array
#### Parameters
 - {array} *$data* 
 - {bool} *$recursive* 

#### Returns
 - array 

## 17. array_trim($arr,$specified_fields,$recursive): array
Trim array item
#### Parameters
 - {array} *$arr* source array
 - {scalar[]} *$specified_fields* specified fields, empty for all fields
 - {bool} *$recursive* 

#### Returns
 - array 

## 18. array_filter_fields($arr,$reserved_fields,$remove_fields,$recursive): array
Array filter by fields
#### Parameters
 - {array} *$arr* 
 - {scalar[]} *$reserved_fields* 
 - {scalar[]} *$remove_fields* 
 - {bool} *$recursive* multi-dimensional array support

#### Returns
 - array 

## 19. array_filter_values($arr,$reserved_values,$remove_values): array
Array filter by specified reserved values, or remove values
#### Parameters
 - {array} *$arr* 
 - {array} *$reserved_values* 
 - {array} *$remove_values* 

#### Returns
 - array 

## 20. array_filter_recursive($arr,$payload): array
Array filter recursive
#### Parameters
 - {array[]} *$arr* any-dimensional array
 - {callable} *$payload* $payload($val, $key), if return false, item will be removed

#### Returns
 - array 

## 21. array_move_item($arr,$target_index,$dir): array
Move specified index item
#### Parameters
 - {array} *$arr* 
 - {string|number} *$target_index* the item tobe handled
 - {number} *$dir* direction use constants: ARRAY_POSING_HEAD, ARRAY_POSING_BEFORE, ARRAY_POSING_AFTER, ARRAY_POSING_LAST

#### Returns
 - array 

## 22. array_last($arr,$key): null
Get last item of array
#### Parameters
 - {array} *$arr* 
 - {null} *$key* matched key

#### Returns
 - null 

## 23. array_shift_assoc($arr): array|false
Assoc-array shift
#### Parameters
 - {array} *$arr* 

#### Returns
 - array|false return matched [value, key], false while empty

## 24. array_orderby($arr): array
Array sort by specified key
#### Parameters
 - {array} *$arr* 

#### Returns
 - array 

## 25. array_orderby_values($src_arr,$field,$values,$pad_tail_on_mismatch): array
Array sort by values
#### Parameters
 - {array[]} *$src_arr* multi-dimensional array (record set)
 - {string} *$field* field name
 - {scalar[]} *$values* value list
 - {bool} *$pad_tail_on_mismatch* pad tail on mismatch

#### Returns
 - array 

## 26. array_orderby_keys($src_arr,$keys,$mismatch_to_head): array
Array sort by keys
#### Parameters
 - {array[]} *$src_arr* 
 - {string[]} *$keys* 
 - {bool} *$mismatch_to_head* set mismatch item to head

#### Returns
 - array 

## 27. array_index($array,$compare_fn_or_value): bool|int|string
Get item index
#### Parameters
 - {array} *$array* 
 - {callable|mixed} *$compare_fn_or_value* 

#### Returns
 - bool|int|string 

## 28. array_sumby($arr,$field): float|int
Sum array value
#### Parameters
 - {array[]} *$arr* multi-dimensional array (record set)
 - {string} *$field* 

#### Returns
 - float|int 

## 29. array_default($arr,$values,$reset_empty): array
Set default values to array
Usage:
<pre>
$_GET = array_default($_GET, array('page_size'=>10), true);
</pre>
#### Parameters
 - {array} *$arr* 
 - {array} *$values* 
 - {bool} *$reset_empty* reset empty value in array

#### Returns
 - array 

## 30. array_make_spreadsheet_columns($column_size): array
Create spreadsheet indexing columns
#### Parameters
 - {int} *$column_size* 

#### Returns
 - array 

## 31. array_push_by_path($arr,$path_str,$value,$glue)
Array push value by specified path
#### Parameters
 - {array} *$arr* target array
 - {string} *$path_str* path string
 - {mixed} *$value* 
 - {string} *$glue* path delimiter, default use dot(.)
## 32. array_fetch_by_path($arr,$path_str,$default,$delimiter): mixed
Fetch array value by path
#### Parameters
 - {array} *$arr* source array
 - {string} *$path_str* path string
 - {mixed} *$default* 缺省值
 - {string} *$delimiter* path delimiter, default use dot(.)

#### Returns
 - mixed 

## 33. array_get($arr,$path_str,$default,$delimiter): mixed
Alias for array_fetch_by_path()
#### Parameters
 - {array} *$arr* 
 - {string} *$path_str* 
 - {mixed} *$default* 
 - {string} *$delimiter* 

#### Returns
 - mixed 

## 34. assert_array_has_keys($arr,$keys)
断言数组是否用拥有指定键名
#### Parameters
 - {array} *$arr* 
 - {array} *$keys* 
## 35. array_filter_subtree($parent_id,$nodes,$opt,$level,$group_by_parents): array
Tree node list filter
#### Parameters
 - {string|int} *$parent_id* parent node id
 - {array[]} *$nodes* tree node list
 - {array} *$opt* filter option
 - {int} *$level* init level
 - {array} *$group_by_parents* 

#### Returns
 - array 

## 36. array_insert_after($src_array,$data,$rel_key): array|int
Array insert after specified key
#### Parameters
 - {array} *$src_array* 
 - {mixed} *$data* 
 - {string} *$rel_key* 

#### Returns
 - array|int 

## 37. array_merge_after($src_array,$new_array,$rel_key): array
Merge new array after specified key
#### Parameters
 - {array} *$src_array* 
 - {array} *$new_array* 
 - {string} *$rel_key* 

#### Returns
 - array 

## 38. array_transform($data,$patterns): mixed
Array transform by specified pattern
#### Parameters
 - {array} *$data* 
 - {array} *$patterns* new array key pattern, like: array('dddd' => array('aaaa', 'bbbb'))

#### Returns
 - mixed 

## 39. array_value_recursive($arr,$key): array
Get Array value recursive
#### Parameters
 - {array} *$arr* 
 - {string} *$key* if no key specified, return whole array values

#### Returns
 - array 



