# String
 > String Enhancement Functions

## 1. substr_utf8($string,$length,$tail,$length_exceeded): string
UTF-8 Chinese and English truncation (two English words for one unit of quantity)
#### Parameters
 - {string} *$string* string
 - {int} *$length* cutting length
 - {string} *$tail* append string to the end
 - {bool} *$length_exceeded* whether it is too long

#### Returns
 - string 

## 2. is_json($str): bool
Check if the string is JSON
#### Parameters
 - {mixed} *$str* 

#### Returns
 - bool 

## 3. explode_by($delimiters,$str,$trim_and_clear): array
Split the string according to the specified boundary character list
#### Parameters
 - {array|string} *$delimiters* eg: [',', '-'] or ",-"
 - {string} *$str* 
 - {bool} *$trim_and_clear* removes blanks and empty values

#### Returns
 - array 

## 4. get_namespace($class): string
Get the namespace part of the specified class name
#### Parameters
 - {mixed} *$class* 

#### Returns
 - string 

## 5. get_class_without_namespace($class): string
Get the class name part of the specified class
#### Parameters
 - {string} *$class* 

#### Returns
 - string 

## 6. parse_str_without_limitation($string,$extra_to_post): array
Break through the max_input_vars limit and get variables by parsing strings
#### Parameters
 - {string} *$string* 
 - {bool} *$extra_to_post* 

#### Returns
 - array 

## 7. __array_merge_distinct_with_dynamic_key($array1,$array2,$dynamicKey): array
merge data
#### Parameters
 - {array} *$array1* 
 - {array} *$array2* 
 - {string} *$dynamicKey* 

#### Returns
 - array 

## 8. asprintf($str,$arr): string[]
Batch call sprintf
#### Parameters
 - {string} *$str* 
 - {array} *$arr* Each item represents the parameter passed to sprintf, which can be an array

#### Returns
 - string[] 

## 9. match_wildcard($wildcard_pattern,$haystack): boolean
PHP wildcard matching
#### Parameters
 - {string} *$wildcard_pattern* 
 - {string} *$haystack* 

#### Returns
 - boolean 

## 10. str_split_by_charset($str,$len,$charset): array
Split the string according to the specified character encoding
#### Parameters
 - {string} *$str* 
 - {int} *$len* 
 - {string} *$charset* 

#### Returns
 - array 

## 11. str_start_with($str,$starts,$case_sensitive): bool
Check if a string starts with another string
#### Parameters
 - {string} *$str* string to be detected
 - {string|array} *$starts* matches string or string array
 - {bool} *$case_sensitive* is it case sensitive

#### Returns
 - bool 

## 12. int2str($data): array|string
Convert integer (int array) to string (string array)
#### Parameters
 - {mixed} *$data* 

#### Returns
 - array|string 

## 13. calc_formula($stm,$param,$result_decorator): array
Formula calculation
#### Parameters
 - {string} *$stm* expression, the variable starts with a $ sign, the parentheses indicate the description text of the variable (can be empty), the structure is like: $var1 (variable 1)
 - {array} *$param* passed in variable, [key=>val] structure
 - {callable|null} *$result_decorator* calculation result decoration callback (only affects the result during the calculation process, not the actual calculation result)

#### Returns
 - array [calculation result, calculation formula, calculation process]

## 14. cut_string($str,$len,$tail,$length_exceeded): array|float|int|mixed|string|void
String cutting (UTF8 encoding)
#### Parameters
 - {string|array} *$str* 
 - {int} *$len* 
 - {string} *$tail* 
 - {bool} *$length_exceeded* 

#### Returns
 - array|float|int|mixed|string|void 

## 15. print_tree_to_options($tree,$prefix): array
Use tabs to generate multi-level option styles
#### Parameters
 - {array} *$tree* menu tree structure, the structure is [{name, value, children=>[]}, ...]
 - {string} *$prefix* prefix string (automatically calculated)

#### Returns
 - array [[text, value], ...]

## 16. xml_special_chars($val): string
XML character escape
#### Parameters
 - {string} *$val* 

#### Returns
 - string 

## 17. remove_utf8_bom($text): string
Remove UTF-8 BOM header
#### Parameters
 - {string} *$text* 

#### Returns
 - string 

## 18. get_traditional_currency($num): string
Function to convert digital amount into Chinese uppercase amount
#### Parameters
 - {int} *$num* The lowercase number or lowercase string to be converted (unit: yuan)

#### Returns
 - string 

## 19. password_check($password,$rules)
Password detection
#### Parameters
 - {string} *$password* 
 - {array} *$rules* 
## 20. str_contains($str,$char_list): bool
Check whether the string contains the specified character set
#### Parameters
 - {string} *$str* 
 - {string} *$char_list* 

#### Returns
 - bool 

## 21. rand_string($len,$source): string
Random string
#### Parameters
 - {int} *$len* length
 - {string} *$source* character source

#### Returns
 - string 

## 22. format_size($size,$dot): string
Format size
#### Parameters
 - {int} *$size* bit value
 - {int} *$dot* reserved decimal places

#### Returns
 - string 

## 23. resolve_size($val): int
Parse the actual file size expression
#### Parameters
 - {string} *$val* file size, such as 12.3m, 43k

#### Returns
 - int 

## 24. str_mixing($text,$param): string
Text obfuscation
#### Parameters
 - {string} *$text* text template, the placeholder uses the {VAR.SUB_VAR} format
 - {array} *$param* obfuscation variable, key => $var format

#### Returns
 - string 

## 25. is_url($url): bool
Check if the string is a URL, the format also contains // This mode omits the protocol
#### Parameters
 - {string} *$url* 

#### Returns
 - bool 

## 26. url_safe_b64encode($str): string
URL base64 security encoding
Replace the + / = symbols in base64 with - _ ''
base64 encoding
#### Parameters
 - {string} *$str* 

#### Returns
 - string 

## 27. url_safe_b64decode($str): string
URL base64 safe decoding
base64 decoding
#### Parameters
 - {string} *$str* 

#### Returns
 - string 

## 28. check_php_var_name_legal($str): false|string
Check if the string complies with PHP variable naming rules
#### Parameters
 - {string} *$str* 

#### Returns
 - false|string 

## 29. filename_sanitize($filename): string|string[]
File name cleaning (according to Windows standards)
#### Parameters
 - {string} *$filename* 

#### Returns
 - string|string[] 

## 30. pascalcase_to_underscores($str): string
Pascal style converted to underscore format
(Clean up multiple underscores at the same time)
#### Parameters
 - {string} *$str* 

#### Returns
 - string 

## 31. underscores_to_pascalcase($str,$capitalize_first): string
Convert underscore format to Pascal format
#### Parameters
 - {string} *$str* 
 - {bool} *$capitalize_first* whether to use upper camel case format

#### Returns
 - string 

## 32. json_decode_safe($str,$associative,$depth,$flags): mixed
Safely parse the json string and throw an exception if an error occurs.
It is recommended to use PHP's native json_decode instead in business code.
#### Parameters
 - {string} *$str* 
 - {bool} *$associative* 
 - {int} *$depth* 
 - {int} *$flags* 

#### Returns
 - mixed 

## 33. encodeURIComponent($string): string
PHP URL encoding/decoding functions for Javascript interaction V3.0
(C) 2006 www.captain.at - all rights reserved
License: GPL
#### Parameters
 - {string} *$string* 

#### Returns
 - string 

## 34. encodeURIComponentByCharacter()
## 35. decodeURIComponent($string): string
#### Parameters
 - {string} *$string* 

#### Returns
 - string 

## 36. decodeURIComponentByCharacter($str): array
#### Parameters
 - {string} *$str* 

#### Returns
 - array 

## 37. encodeURI()
## 38. encodeURIByCharacter($char): string
#### Parameters
 - {string} *$char* 

#### Returns
 - string 

## 39. decodeURI($string): string
#### Parameters
 - {string} *$string* 

#### Returns
 - string 

## 40. decodeURIByCharacter()
## 41. escape()
## 42. escapeByCharacter()
## 43. unescape($string): string
#### Parameters
 - {string} *$string* 

#### Returns
 - string 

## 44. unEscapeByCharacter()
## 45. generate_guid($trim): string
Returns a GUIDv4 string
Uses the best cryptographically secure method
for all supported platforms with fallback to an older,
less secure version.
#### Parameters
 - {bool} *$trim* 

#### Returns
 - string 



