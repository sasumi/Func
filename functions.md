## 1. ARRAY
 > Array Enhancement Functions

### 1.1 is_assoc_array($arr): boolean
Check array is assoc array
#### Parameters
 - {array} *arr* 

#### Returns
 - boolean 

### 1.2 array_group($arr,$by_key,$force_unique): array
Array group by function
group array(); by by_key
#### Parameters
 - {array} *arr* source array, multi-dimensional array (record set)
 - {scalar} *by_key* key to group by
 - {boolean} *force_unique* array key is unique in list

#### Returns
 - array 

### 1.3 range_slice($start,$end,$size): \Generator
Make chunk from specified number range
#### Parameters
 - {int} *start* number start
 - {int} *end* number end
 - {int} *size* chunk size

#### Returns
 - \Generator 

### 1.4 object_shuffle($objects): array
Shuffle objects, key original assoc key
#### Parameters
 - {array|object} *objects* 

#### Returns
 - array 

### 1.5 array_random($arr,$count): array
Get random array item
don't like array_rand, this function always return an array
#### Parameters
 - {array} *arr* source array, can be associative array or indexed array
 - {int} *count* random count

#### Returns
 - array random array, keys will be preserved

### 1.6 array_keys_exists($arr,$keys): bool
Check any keys exists in array
#### Parameters
 - {array} *arr* source array
 - {array} *keys* keys to check

#### Returns
 - bool 

### 1.7 array_flatten($arr): array
Flat array
#### Parameters
 - {array} *arr* source array

#### Returns
 - array 

### 1.8 plain_items($arr,$original_key,$original_key_name): array
将多重数组值取出来，平铺成一维数组
#### Parameters
 - {array} *arr* 
 - {string} *original_key* 
 - {string} *original_key_name* 

#### Returns
 - array 

### 1.9 array2object($arr): \stdClass
Convert array to standard object
#### Parameters
 - {array} *arr* 

#### Returns
 - \stdClass 

### 1.10 object2array($obj): array
Convert object to array
#### Parameters
 - {object} *obj* any object can convert, but not resource

#### Returns
 - array 

### 1.11 restructure_files($PHP_FILES): array
Restructure $_FILES array to correct name dimension
#### Parameters
 - {array} *PHP_FILES* PHP $_FILES variable

#### Returns
 - array 

### 1.12 array_merge_recursive_distinct($array1,$array2): array
Merge two arrays recursively and distinctly
#### Parameters
 - {array} *array1* multi-dimensional array (record set)
 - {array} *array2* multi-dimensional array (record set)

#### Returns
 - array 

### 1.13 array_merge_assoc($org_arr,$new_arr,$recursive): array
Merge two arrays recursively
#### Parameters
 - {array} *org_arr* multi-dimensional array (record set)
 - {array} *new_arr* multi-dimensional array (record set)
 - {bool} *recursive* is recursive merge

#### Returns
 - array 

### 1.14 array_clean($arr,$clean_empty,$clean_null,$trim_string,$recursive): array
Clean array
#### Parameters
 - {array} *arr* multi-dimensional array (record set)
 - {bool} *clean_empty* clear empty item(include empty array)
 - {bool} *clean_null* clean while item is null
 - {bool} *trim_string* trim string while item is string
 - {bool} *recursive* 

#### Returns
 - array 

### 1.15 array_clean_null($data,$recursive): array
clean array null value
#### Parameters
 - {array|mixed} *data* 
 - {bool} *recursive* 

#### Returns
 - array 

### 1.16 array_clean_empty($data,$recursive): array
Clean empty item in array
#### Parameters
 - {array} *data* 
 - {bool} *recursive* 

#### Returns
 - array 

### 1.17 array_trim($arr,$specified_fields,$recursive): array
Trim array item
#### Parameters
 - {array} *arr* source array
 - {scalar[]} *specified_fields* specified fields, empty for all fields
 - {bool} *recursive* 

#### Returns
 - array 

### 1.18 array_filter_fields($arr,$reserved_fields,$remove_fields,$recursive): array
Array filter by fields
#### Parameters
 - {array} *arr* 
 - {scalar[]} *reserved_fields* 
 - {scalar[]} *remove_fields* 
 - {bool} *recursive* multi-dimensional array support

#### Returns
 - array 

### 1.19 array_filter_values($arr,$reserved_values,$remove_values): array
Array filter by specified reserved values, or remove values
#### Parameters
 - {array} *arr* 
 - {array} *reserved_values* 
 - {array} *remove_values* 

#### Returns
 - array 

### 1.20 array_filter_recursive($arr,$payload): array
Array filter recursive
#### Parameters
 - {array[]} *arr* any-dimensional array
 - {callable} *payload* $payload($val, $key), if return false, item will be removed

#### Returns
 - array 

### 1.21 array_move_item($arr,$target_index,$dir): array
Move specified index item
#### Parameters
 - {array} *arr* 
 - {string|number} *target_index* the item tobe handled
 - {number} *dir* direction use constants: ARRAY_POSING_HEAD, ARRAY_POSING_BEFORE, ARRAY_POSING_AFTER, ARRAY_POSING_LAST

#### Returns
 - array 

### 1.22 array_last($arr,$key): null
Get last item of array
#### Parameters
 - {array} *arr* 
 - {null} *key* matched key

#### Returns
 - null 

### 1.23 array_shift_assoc($arr): array|false
Assoc-array shift
#### Parameters
 - {array} *arr* 

#### Returns
 - array|false return matched [value, key], false while empty

### 1.24 array_orderby($arr): array
Array sort by specified key
#### Parameters
 - {array} *arr* 

#### Returns
 - array 

### 1.25 array_orderby_values($src_arr,$field,$values,$pad_tail_on_mismatch): array
Array sort by values
#### Parameters
 - {array[]} *src_arr* multi-dimensional array (record set)
 - {string} *field* field name
 - {scalar[]} *values* value list
 - {bool} *pad_tail_on_mismatch* pad tail on mismatch

#### Returns
 - array 

### 1.26 array_orderby_keys($src_arr,$keys,$mismatch_to_head): array
Array sort by keys
#### Parameters
 - {array[]} *src_arr* 
 - {string[]} *keys* 
 - {bool} *mismatch_to_head* set mismatch item to head

#### Returns
 - array 

### 1.27 array_index($array,$compare_fn_or_value): bool|int|string
Get item index
#### Parameters
 - {array} *array* 
 - {callable|mixed} *compare_fn_or_value* 

#### Returns
 - bool|int|string 

### 1.28 array_sumby($arr,$field): float|int
Sum array value
#### Parameters
 - {array[]} *arr* multi-dimensional array (record set)
 - {string} *field* 

#### Returns
 - float|int 

### 1.29 array_default($arr,$values,$reset_empty): array
Set default values to array
Usage:
<pre>
$_GET = array_default($_GET, array('page_size'=>10), true);
</pre>
#### Parameters
 - {array} *arr* 
 - {array} *values* 
 - {bool} *reset_empty* reset empty value in array

#### Returns
 - array 

### 1.30 array_make_spreadsheet_columns($column_size): array
Create spreadsheet indexing columns
#### Parameters
 - {int} *column_size* 

#### Returns
 - array 

### 1.31 array_push_by_path($arr,$path_str,$value,$glue)
Array push value by specified path
#### Parameters
 - {array} *arr* target array
 - {string} *path_str* path string
 - {mixed} *value* 
 - {string} *glue* path delimiter, default use dot(.)
### 1.32 array_fetch_by_path($arr,$path_str,$default,$delimiter): mixed
Fetch array value by path
#### Parameters
 - {array} *arr* source array
 - {string} *path_str* path string
 - {mixed} *default* 缺省值
 - {string} *delimiter* path delimiter, default use dot(.)

#### Returns
 - mixed 

### 1.33 array_get($arr,$path_str,$default,$delimiter): mixed
Alias for array_fetch_by_path()
#### Parameters
 - {array} *arr* 
 - {string} *path_str* 
 - {mixed} *default* 
 - {string} *delimiter* 

#### Returns
 - mixed 

### 1.34 assert_array_has_keys($arr,$keys)
断言数组是否用拥有指定键名
#### Parameters
 - {array} *arr* 
 - {array} *keys* 
### 1.35 array_filter_subtree($parent_id,$nodes,$opt,$level,$group_by_parents): array
Tree node list filter
#### Parameters
 - {string|int} *parent_id* parent node id
 - {array[]} *nodes* tree node list
 - {array} *opt* filter option
 - {int} *level* init level
 - {array} *group_by_parents* 

#### Returns
 - array 

### 1.36 array_insert_after($src_array,$data,$rel_key): array|int
Array insert after specified key
#### Parameters
 - {array} *src_array* 
 - {mixed} *data* 
 - {string} *rel_key* 

#### Returns
 - array|int 

### 1.37 array_merge_after($src_array,$new_array,$rel_key): array
Merge new array after specified key
#### Parameters
 - {array} *src_array* 
 - {array} *new_array* 
 - {string} *rel_key* 

#### Returns
 - array 

### 1.38 array_transform($data,$patterns): mixed
Array transform by specified pattern
#### Parameters
 - {array} *data* 
 - {array} *patterns* new array key pattern, like: array('dddd' => array('aaaa', 'bbbb'))

#### Returns
 - mixed 

### 1.39 array_value_recursive($arr,$key): array
Get Array value recursive
#### Parameters
 - {array} *arr* 
 - {string} *key* if no key specified, return whole array values

#### Returns
 - array 



## 2. COLOR
 > Color Enhancement Functions

### 2.1 color_hex2rgb($hex_color): array
Convert hexadecimal color format to RGB format (array)
#### Parameters
 - {string} *hex_color* #ff00bb

#### Returns
 - array 

### 2.2 color_rgb2hex($rgb,$prefix): string
Convert RGB format to hexadecimal color format
#### Parameters
 - {array} *rgb* [r,g,b]
 - {string} *prefix* 

#### Returns
 - string 

### 2.3 color_rgb2hsl($rgb): float[]
Convert RGB format to HSL format
#### Parameters
 - {array} *rgb* 

#### Returns
 - float[] [h,s,l]

### 2.4 color_hsl2rgb($hsl): int[]
Convert HSL format to RGB format
#### Parameters
 - {array} *hsl* [h,s,l]

#### Returns
 - int[] [r,g,b]

### 2.5 color_rgb2cmyk($rgb): array
Convert RGB format to CMYK format
#### Parameters
 - {array} *rgb* 

#### Returns
 - array [c,m,y,k]

### 2.6 cmyk_to_rgb($cmyk): int[]
Convert CMYK format to RGB format
#### Parameters
 - {array} *cmyk* 

#### Returns
 - int[] [r,g,b]

### 2.7 color_rgb2hsb($rgb,$accuracy): array
Convert RGB format to HSB format
#### Parameters
 - {array} *rgb* [r,g,b]
 - {int} *accuracy* 

#### Returns
 - array 

### 2.8 color_hsb2rgb($hsb,$accuracy): int[]
Convert HSB format to RGB format
#### Parameters
 - {array} *hsb* [h,s,b]
 - {int} *accuracy* 

#### Returns
 - int[] [r,g,b]

### 2.9 color_molarity($color_val,$inc_pec): array|string
Calculate the molarity of a color
#### Parameters
 - {string|array} *color_val* HEX color string, or RGB array
 - {float} *inc_pec* range of percent, from -99 to 99

#### Returns
 - array|string 

### 2.10 color_rand(): string
Random color

#### Returns
 - string 

### 2.11 _hsl_rgb_low()
### 2.12 _hsl_rgb_high()
### 2.13 _rgb_hsl_delta_rgb()
### 2.14 _rgb_hsl_hue()


## 3. CRON
 > Crontab Enhancement Functions

### 3.1 cron_match($format,$time,$error): bool
Check if the cron format matches the specified timestamp
#### Parameters
 - {string} *format* cron format. Currently not supporting year, format is: minutes hours days months weeks
 - {int} *time* Default is current timestamp
 - {string|null} *error* mismatch error info

#### Returns
 - bool 

### 3.2 cron_watch_commands($rules,$on_before_call,$check_interval)
#### Parameters
 - {array} *rules* 
 - {callable|null} *on_before_call* 
 - {int} *check_interval* seconds, must min than one minutes


## 4. CSP
 > HTTP CSP Functions

### 4.1 csp_content_rule($resource,$policy,$custom_defines): string
Build CSP rule
#### Parameters
 - {string} *resource* Resource
 - {string} *policy* Policy
 - {string} *custom_defines* Policy extension data (mainly for CSP_POLICY_SCRIPT_NONCE CSP_POLICY_SCRIPT_HASH)

#### Returns
 - string 

### 4.2 csp_report_uri($uri): string
Build CSP reporting rule
#### Parameters
 - {string} *uri* 

#### Returns
 - string 



## 5. CURL
 > CURL Enhancement Functions
 > Mark: standard structure for curl_get():
 > [info=>['url'='', ...], error='', head=>'', body=>'']

### 5.1 curl_get($url,$data,$curl_option): array
CURL GET Request
#### Parameters
 - {string} *url* 
 - {mixed|null} *data* 
 - {array} *curl_option* extra curl option

#### Returns
 - array [info=>[], head=>'', body=>'', ...] curl_getinfo structure

### 5.2 curl_post($url,$data,$curl_option): array
Post request
#### Parameters
 - {string} *url* 
 - {mixed|null} *data* 
 - {array} *curl_option* 

#### Returns
 - array 

### 5.3 curl_post_json($url,$data,$curl_option): array
post data in json format
#### Parameters
 - {string} *url* 
 - {mixed} *data* 
 - {array} *curl_option* 

#### Returns
 - array 

### 5.4 curl_post_file($url,$file_map,$ext_param,$curl_option): array
curl post file
#### Parameters
 - {string} *url* 
 - {array} *file_map* [filename=>file, filename=>[file, mime]...] File name mapping, if mime information is not provided here, the backend may receive application/octet-stream
 - {mixed} *ext_param* Other post parameters submitted at the same time
 - {array} *curl_option* curl option

#### Returns
 - array curl_query返回结果，包含 [info=>[], head=>'', body=>''] 信息

### 5.5 curl_put($url,$data,$curl_option): array
Put request
#### Parameters
 - {string} *url* 
 - {array} *data* 
 - {array} *curl_option* 

#### Returns
 - array 

### 5.6 curl_delete($url,$data,$curl_option): array
Delete request
#### Parameters
 - {string} *url* 
 - {array} *data* 
 - {array} *curl_option* 

#### Returns
 - array 

### 5.7 curl_query($url,$curl_option): array
Quickly execute a curl query then close the curl connection
#### Parameters
 - {string} *url* 
 - {array} *curl_option* 

#### Returns
 - array [info=>[], error='', head=>'', body=>'']

### 5.8 curl_patch_header($curl_option,$header_name,$header_value): void
CURL HTTP Header appends additional information. If it already exists, it will be replaced.
#### Parameters
 - {array} *curl_option* 
 - {string} *header_name* 
 - {string} *header_value* 

#### Returns
 - void 

### 5.9 curl_build_command($url,$body_str,$method,$headers): string
Build CURL command
#### Parameters
 - {string} *url* 
 - {string} *body_str* 
 - {string} *method* 
 - {string[]} *headers* header information, in the format of ['Content-Type: application/json'] or ['Content-Type‘=>'application/json']

#### Returns
 - string 

### 5.10 curl_get_proxy_option($proxy_string): array
Parse the proxy string and generate curl option
Agent string, in the format:
http://hostname:port
http://username:password@hostname:port
https://hostname:port (converted to http://)
https://username:password@hostname:port (converted to http://)
socks4://hostname:port
socks4://username:password@hostname:port
socks5://hostname:port
socks5://username:password@hostname:port
#### Parameters
 - {string} *proxy_string* 

#### Returns
 - array 

### 5.11 curl_get_default_option(): array
Get CURL default options

#### Returns
 - array 

### 5.12 curl_option_merge($old_option,$new_option): array
Merge curl options, especially handle the duplicate parts in CURLOPT_HTTPHEADER
#### Parameters
 - {array} *old_option* 
 - {array} *new_option* 

#### Returns
 - array 

### 5.13 curl_convert_http_header_to_assoc($headers): array
Convert http header array to associative array to facilitate modification operations
#### Parameters
 - {array} *headers* 

#### Returns
 - array 

### 5.14 curl_set_default_option($curl_option,$patch)
Set default options for curl_operations
#### Parameters
 - {array} *curl_option* 
 - {bool} *patch* Whether to add in append mode, the default is to overwrite
### 5.15 curl_instance($url,$ext_curl_option): array(resource,
Get CURL instance object
#### Parameters
 - {string} *url* 
 - {array} *ext_curl_option* curl option, additional default options will be added through curl_default_option()

#### Returns
 - array(resource, $curl_option)

### 5.16 curl_data2str($data): string
convert data to request string
#### Parameters
 - {mixed} *data* 

#### Returns
 - string 

### 5.17 curl_print_option($options,$as_return): array|null
打印curl option
#### Parameters
 - {array} *options* 
 - {bool} *as_return* 

#### Returns
 - array|null 

### 5.18 curl_option_to_request_header($options): string[]
转换curl option到标准HTTP头信息
#### Parameters
 - {array} *options* 

#### Returns
 - string[] array

### 5.19 curl_urls_to_fetcher($urls,$ext_curl_option): \Closure
Convert the request link into a closure function
#### Parameters
 - {string[]|array[]} *urls* request link array
 - {array} *ext_curl_option* curl option array

#### Returns
 - \Closure 

### 5.20 curl_cut_raw($ch,$raw_string): string[]
Cut CURL result string
#### Parameters
 - {resource} *ch* 
 - {string} *raw_string* 

#### Returns
 - string[] head,body

### 5.21 curl_error_message($error_no): string
Get curl error information
#### Parameters
 - {int} *error_no* 

#### Returns
 - string Empty indicates success

### 5.22 curl_query_success($query_result,$error,$allow_empty_body): bool
Determine whether curl_query is successful
#### Parameters
 - {array} *query_result* curl_query returns the standard structure
 - {string} *error* 
 - {bool} *allow_empty_body* allows body to be empty

#### Returns
 - bool 

### 5.23 curl_concurrent($curl_option_fetcher,$on_item_start,$on_item_finish,$rolling_window): bool
CURL concurrent requests
Note: The callback function needs to be processed as soon as possible to avoid blocking subsequent request processes.
#### Parameters
 - {callable|array} *curl_option_fetcher* array Returns the curl option mapping array. Even if there is only one url, [CURLOPT_URL=>$url] needs to be returned.
 - {callable|null} *on_item_start* ($curl_option) Start executing the callback. If false is returned, the task is ignored.
 - {callable|null} *on_item_finish* ($curl_ret, $curl_option) Request end callback, parameter 1: return result array, parameter 2: curl option
 - {int} *rolling_window* Number of rolling requests

#### Returns
 - bool 



## 6. DB
 > Database operation (PDO) related operation functions

### 6.1 db_connect($db_type,$host,$user,$password,$database,$port,$charsets,$persistence_connect): \PDO
PDO connect
#### Parameters
 - {string} *db_type* 
 - {string} *host* 
 - {string} *user* 
 - {string} *password* 
 - {string} *database* 
 - {int|null} *port* 
 - {string} *charsets* 
 - {bool} *persistence_connect* 

#### Returns
 - \PDO 

### 6.2 db_connect_via_ssh_proxy($db_config,$ssh_config,$proxy_config): \PDO
connect database via ssh proxy
#### Parameters
 - {array} *db_config* ['type', 'host', 'user', 'password', 'database', 'port']
 - {array} *ssh_config* ['host', 'user', 'password'', 'port']
 - {array} *proxy_config* ['host', 'port']

#### Returns
 - \PDO 

### 6.3 db_auto_ssh_port()
### 6.4 db_mysql_connect($host,$user,$password,$database,$port,$charsets,$persistence_connect): \PDO
#### Parameters
 - {string} *host* 
 - {string} *user* 
 - {string} *password* 
 - {string} *database* 
 - {null} *port* 
 - {string} *charsets* 
 - {bool} *persistence_connect* 

#### Returns
 - \PDO 

### 6.5 db_connect_dsn($dsn,$user,$password,$persistence_connect): \PDO
#### Parameters
 - {string} *dsn* 
 - {string} *user* 
 - {string} *password* 
 - {bool} *persistence_connect* 

#### Returns
 - \PDO 

### 6.6 db_build_dsn($db_type,$host,$database,$port,$charsets): string
build DSN
#### Parameters
 - {string} *db_type* 
 - {string} *host* 
 - {string} *database* 
 - {string} *port* 
 - {string} *charsets* 

#### Returns
 - string 

### 6.7 db_query($pdo,$sql): false|\PDOStatement
db query
#### Parameters
 - {\PDO} *pdo* 
 - {string} *sql* 

#### Returns
 - false|\PDOStatement 

### 6.8 db_query_all($pdo,$sql): array
db get all
#### Parameters
 - {\PDO} *pdo* 
 - {string} *sql* 

#### Returns
 - array 

### 6.9 db_query_one($pdo,$sql): array
database query one record
#### Parameters
 - {\PDO} *pdo* 
 - {string} *sql* 

#### Returns
 - array 

### 6.10 db_query_field($pdo,$sql,$field): mixed|null
database query one field
#### Parameters
 - {\PDO} *pdo* 
 - {string} *sql* 
 - {string|null} *field* 

#### Returns
 - mixed|null 

### 6.11 db_sql_patch_limit($sql,$start_offset,$size): string
Append limit statement to sql
#### Parameters
 - {string} *sql* 
 - {int} *start_offset* 
 - {int|null} *size* 

#### Returns
 - string 

### 6.12 db_query_count($pdo,$sql): int
Query count
#### Parameters
 - {\PDO} *pdo* 
 - {string} *sql* 

#### Returns
 - int 

### 6.13 db_query_paginate($pdo,$sql,$page,$page_size): array
Pagination Query
#### Parameters
 - {\PDO} *pdo* 
 - {string} *sql* 
 - {int} *page* 
 - {int} *page_size* 

#### Returns
 - array return [list, count]

### 6.14 db_query_chunk($pdo,$sql,$handler,$chunk_size): bool
Chunk reading
#### Parameters
 - {\PDO} *pdo* 
 - {string} *sql* 
 - {callable} *handler* Batch processing function, pass in parameters ($rows, $page, $finish), if false is returned, the execution is interrupted
 - {int} *chunk_size* 

#### Returns
 - bool Whether it is a normal end, false means that the batch processing function is interrupted

### 6.15 db_watch($pdo,$sql,$handler,$chunk_size): bool
Block reading
#### Parameters
 - {\PDO} *pdo* 
 - {string} *sql* 
 - {callable} *handler* Batch processing function, pass in parameters ($rows, $page, $finish), if false is returned, the execution is interrupted
 - {int} *chunk_size* 

#### Returns
 - bool Whether it is a normal end, false means that the batch processing function is interrupted

### 6.16 db_quote_value($data): array|string
Field escape, currently only supports strings
#### Parameters
 - {array|string|int} *data* 

#### Returns
 - array|string 

### 6.17 db_quote_field($fields): array|string
Database table field escape
#### Parameters
 - {string|array} *fields* 

#### Returns
 - array|string 

### 6.18 db_affect_rows($result): int|false
Get the number of rows affected by the query
#### Parameters
 - {\PDOStatement} *result* 

#### Returns
 - int|false 

### 6.19 db_insert($pdo,$table,$data): false|int
Insert data
#### Parameters
 - {\PDO} *pdo* 
 - {string} *table* 
 - {array} *data* 

#### Returns
 - false|int 

### 6.20 db_transaction($pdo,$handler): bool|mixed
Transaction processing
#### Parameters
 - {\PDO} *pdo* 
 - {callable} *handler* handler, if it returns false or throws an exception, it will interrupt the submission and perform a rollback operation

#### Returns
 - bool|mixed 



## 7. ENV
 > Platform function related operation functions

### 7.1 server_in_windows(): bool
Check if the server is running on Windows

#### Returns
 - bool 

### 7.2 server_in_https(): bool
Check if the server is running in HTTPS protocol

#### Returns
 - bool 

### 7.3 get_upload_max_size($human_readable): string|number
Get the maximum file size allowed for upload by PHP
Depends on: maximum upload file size, maximum POST size
#### Parameters
 - {bool} *human_readable* whether to return in readable mode

#### Returns
 - string|number 

### 7.4 get_max_socket_timeout($ttf): int
Get the maximum available socket timeout
#### Parameters
 - {int} *ttf* allowed advance time

#### Returns
 - int timeout (seconds), if 0, it means no timeout limit

### 7.5 get_client_ip(): string
Get the client IP
Prioritize the defined x-forward-for proxy IP (may have certain risks)

#### Returns
 - string client IP, return an empty string if failed

### 7.6 get_all_opt(): array
Get all command line options, the format rules are consistent with getopt

#### Returns
 - array 

### 7.7 get_php_info(): array
Get PHP configuration information

#### Returns
 - array 

### 7.8 console_color($text,$fore_color,$back_color,$override): string
Generate CLI strings with colors
#### Parameters
 - {string} *text* 
 - {string} *fore_color* 
 - {string} *back_color* 
 - {bool} *override* Whether to overwrite the original color setting

#### Returns
 - string 

### 7.9 console_color_clean($text): string
Clean up color control characters
#### Parameters
 - {string} *text* 

#### Returns
 - string 

### 7.10 show_progress($index,$total,$patch_text,$start_timestamp)
Show progress bar in console
Supplementary display text, can be a closure function, all echo strings in the function will be output as progress text, if it is a closure function, due to the ob of php cli, there will be a certain delay
#### Parameters
 - {int} *index* 
 - {int} *total* 
 - {string|callable} *patch_text* 
 - {int} *start_timestamp* start timestamp, initialize the global unique timestamp in the empty function
### 7.11 show_loading($patch_text,$loading_chars): void
Loading mode outputs console string
#### Parameters
 - {string} *patch_text* Display text
 - {string[]} *loading_chars* Loading character sequence, for example: ['\\', '|', '/', '-']

#### Returns
 - void 

### 7.12 run_command($command,$param,$async): bool|string|null
Run command
#### Parameters
 - {string} *command* command
 - {array} *param* parameter
 - {bool} *async* whether to execute asynchronously

#### Returns
 - bool|string|null 

### 7.13 run_command_parallel_width_progress($command,$param_batches,$options): bool
Run commands concurrently with progress text.
For calling parameters, please refer to the function: run_command_parallel()
#### Parameters
 - {string} *command* 
 - {array} *param_batches* 
 - {array} *options* 

#### Returns
 - bool 

### 7.14 run_command_parallel($command,$param_batches,$options): bool
Concurrently run commands
- callable|null $on_start($param, $param_index, $start_time) returns false to interrupt execution
- callable|null $on_running($param, $param_index) returns false to interrupt execution
- callable|null $on_finish($param, $param_index, $output, $cost_time, $status_code, $error) returns false to interrupt execution
- int $parallel_num concurrent number, default is 20
- int $check_interval status check interval (unit: milliseconds), default is 100ms
- int $process_max_execution_time maximum process execution time (unit: milliseconds), default is not set
#### Parameters
 - {string} *command* Execute command
 - {array} *param_batches* Task parameter list. Parameters are passed to command as long parameters. For specific implementation, please refer to: build_command() function implementation.
 - {array} *options* parameters are as follows:

#### Returns
 - bool whether it ends normally

### 7.15 build_command($cmd_line,$param): string
Build command line
#### Parameters
 - {string} *cmd_line* 
 - {array} *param* 

#### Returns
 - string 

### 7.16 escape_win32_argv($value): string
Escape argv parameters under Windows
#### Parameters
 - {string|int} *value* 

#### Returns
 - string 

### 7.17 escape_win32_cmd($value): string|string[]|null
Escape cmd.exe metacharacters with ^
#### Parameters
 - {mixed} *value* 

#### Returns
 - string|string[]|null 

### 7.18 noshell_exec($command): false|string
Like shell_exec() but bypass cmd.exe
#### Parameters
 - {string} *command* 

#### Returns
 - false|string 

### 7.19 command_exists($command): bool
Check if the command exists
#### Parameters
 - {string} *command* 

#### Returns
 - bool 

### 7.20 windows_get_port_usage($include_process_info): array
Get the network usage of Windows process
#### Parameters
 - {bool} *include_process_info* whether to include process information (title, program file name), this function requires Windows administrator mode

#### Returns
 - array format: [protocol='', local_ip='', local_port='', foreign_ip='', foreign_port='', state='', pid='', 'process_name'='', 'process_file_id'=>'']

### 7.21 unix_get_port_usage(): array
Get port occupancy status under Linux

#### Returns
 - array format: [protocol='', local_ip='', local_port='', foreign_ip='', foreign_port='', state='', pid='', 'process_name'='', 'process_file_id'=>'']

### 7.22 get_screen_size(): array|null
Get the width and height of the console screen

#### Returns
 - array|null Return format: [number of columns, number of rows], if the current environment does not support it, it will return null

### 7.23 process_kill($pid): bool
Kill process
#### Parameters
 - {int} *pid* Process ID

#### Returns
 - bool 

### 7.24 process_running($pid): bool
Check whether the specified process is running
#### Parameters
 - {int} *pid* Process ID

#### Returns
 - bool 

### 7.25 process_signal($signal,$handle): bool
Process signal monitoring
#### Parameters
 - {mixed} *signal* 
 - {mixed} *handle* 

#### Returns
 - bool 

### 7.26 process_send_signal($pid,$sig_num): bool
Send process semaphore
#### Parameters
 - {int} *pid* Process ID
 - {int} *sig_num* semaphore

#### Returns
 - bool 

### 7.27 replay_current_script(): false|int
Replay the current script command

#### Returns
 - false|int Returns the newly opened Process ID, false is returned if failed



## 8. EVENT
 > Custom Event Functions



## 9. FILE
 > File Enhancement Functions

### 9.1 glob_recursive($pattern,$flags): array
Glob recursive
Does not support flag GLOB_BRACE
#### Parameters
 - {string} *pattern* 
 - {int} *flags* 

#### Returns
 - array 

### 9.2 unlink_recursive($path,$verbose): void
Recursive unlink
#### Parameters
 - {string} *path* Folder to be deleted
 - {bool} *verbose* Whether to print debug information

#### Returns
 - void 

### 9.3 file_exists_case_sensitive($file): bool|null
Check if the file exists and the name strictly matches the upper and lower case
true: the file exists, false: the file does not exist, null: the file exists but the case is inconsistent
#### Parameters
 - {string} *file* 

#### Returns
 - bool|null 

### 9.4 assert_file_in_dir($file,$dir,$exception_class)
Assert that the file is contained in the specified folder (the file must exist)
#### Parameters
 - {string} *file* 
 - {string} *dir* 
 - {string} *exception_class* 
### 9.5 file_in_dir($file_path,$dir_path): bool
Determine whether the file is contained in the specified folder
#### Parameters
 - {string} *file_path* file path
 - {string} *dir_path* directory path

#### Returns
 - bool The file does not exist in the directory, or the file does not actually exist

### 9.6 resolve_absolute_path($file_or_path): string
Parse the real path of the path string and remove the relative path information
Compared with realpath, this function does not need to check whether the file exists
<pre>
Calling format: resolve_absolute_path("c:/a/b/./../../windows/system32");
Return: c:/windows/system32
#### Parameters
 - {string} *file_or_path* directory path or file path string

#### Returns
 - string 

### 9.7 resolve_file_extension($filename,$to_lower_case): string|null
Get file extension based on file name
#### Parameters
 - {string} *filename* file name
 - {bool} *to_lower_case* whether to convert to lower case, default is to convert to lower case

#### Returns
 - string|null string or null,no extension detected

### 9.8 file_exists_case_insensitive($file,$parent): bool
Check if the file exists and that the name can be case-insensitive
#### Parameters
 - {string} *file* 
 - {null} *parent* 

#### Returns
 - bool 

### 9.9 file_put_contents_safe()
### 9.10 copy_recursive($src,$dst)
Copy directories recursively
#### Parameters
 - {string} *src* 
 - {string} *dst* 
### 9.11 mkdir_batch($dirs,$break_on_error,$permissions): string[]
Create directories in batches
#### Parameters
 - {string[]} *dirs* Directory path list
 - {bool} *break_on_error* Whether to throw an exception when creation fails
 - {int} *permissions* Directory default permissions

#### Returns
 - string[] Directory list that failed to be created, and returns an empty array if successful

### 9.12 mkdir_by_file($file,$permissions): string
Create a folder based on the target file path
#### Parameters
 - {string} *file* 
 - {int} *permissions* directory permissions

#### Returns
 - string successfully created directory path

### 9.13 get_dirs($dir): array
Get directories recursive
#### Parameters
 - {string} *dir* 

#### Returns
 - array 

### 9.14 file_lines($file,$line_separator): int
Get the number of lines in a file
#### Parameters
 - {string|resource} *file* file path or file handle
 - {string} *line_separator* line break character

#### Returns
 - int 

### 9.15 tail($file,$lines,$buffer): string[]
Tail
#### Parameters
 - {string|resource} *file* 
 - {int} *lines* Number of lines to read
 - {int} *buffer* Buffer size

#### Returns
 - string[] Content of each line

### 9.16 file_read_by_line($file,$handle,$start_line,$buff_size): bool
Read file line by line
#### Parameters
 - {string} *file* file name
 - {callable} *handle* processing function, pass in parameters: ($line_str, $line), if the function returns false, the processing is interrupted
 - {int} *start_line* start reading line number (starting from 1)
 - {int} *buff_size* buffer size

#### Returns
 - bool whether it is a processing function interrupt return

### 9.17 render_php_file($php_file,$vars): false|string
Render php file and return as string
#### Parameters
 - {mixed} *php_file* 
 - {array} *vars* 

#### Returns
 - false|string 

### 9.18 get_folder_size($path): int
Calculate folder size recursively
#### Parameters
 - {string} *path* 

#### Returns
 - int 

### 9.19 log($file,$content,$max_size,$max_files,$pad_str): bool|int
log records to file
#### Parameters
 - {string} *file* file
 - {mixed} *content* record content
 - {float|int} *max_size* maximum size of a single file, default
 - {int} *max_files* maximum number of recorded files
 - {string|null} *pad_str* record file name append string

#### Returns
 - bool|int whether the file is recorded successfully

### 9.20 read_file_lock($key): false|string|null
Read file lock
#### Parameters
 - {string} *key* 

#### Returns
 - false|string|null 

### 9.21 write_file_lock($key,$lock_flag): string
Write file lock
#### Parameters
 - {string} *key* 
 - {string} *lock_flag* 

#### Returns
 - string 

### 9.22 remove_file_lock($key): bool
remove file lock
#### Parameters
 - {string} *key* 

#### Returns
 - bool 

### 9.23 init_file_lock($key,$is_new): resource
Init file lock
#### Parameters
 - {string} *key* 
 - {bool} *is_new* 

#### Returns
 - resource 锁文件操作句柄

### 9.24 log_tmp_file($filename,$content,$max_size,$max_files,$pad_str): bool|int
Log in temporary directory
if high performance required, support to use logrotate programme to process your log file
#### Parameters
 - {string} *filename* 
 - {mixed} *content* 
 - {float|int} *max_size* 
 - {int} *max_files* 
 - {string|null} *pad_str* 

#### Returns
 - bool|int 

### 9.25 create_tmp_file($dir,$prefix,$ext,$mod): string
Create a temporary file
#### Parameters
 - {string} *dir* The directory where the file is located
 - {string} *prefix* The file name prefix
 - {string} *ext* The file name suffix
 - {numeric} *mod* Permission, default is 777

#### Returns
 - string 

### 9.26 upload_file_error($upload_error_no): string
Get file upload error message via PHP file upload error number
#### Parameters
 - {int} *upload_error_no* 

#### Returns
 - string 

### 9.27 upload_file_check($file,$opt): void
Upload file check by option
#### Parameters
 - {string} *file* 
 - {array} *opt* 

#### Returns
 - void 

### 9.28 get_extensions_by_mime($mime): string[]
Get a list of extensions that match the specified mime
#### Parameters
 - {string} *mime* 

#### Returns
 - string[] 

### 9.29 get_mimes_by_extension($ext): string[]
Get mime information by file suffix
#### Parameters
 - {string} *ext* file suffix

#### Returns
 - string[] mime list

### 9.30 mime_match_extensions($mime,$extensions): bool
Check if the given mime information is in the specified extension list
This method is usually used to check whether the uploaded file meets the set file type
#### Parameters
 - {string} *mime* 
 - {string[]} *extensions* 

#### Returns
 - bool 

### 9.31 mime_match_accept($mime,$accept): bool
Check if the file mime information matches the accept string
#### Parameters
 - {string} *mime* file mime information
 - {string} *accept* <input accept=""/> format reference：https://developer.mozilla.org/en-US/docs/Web/HTML/Attributes/accept

#### Returns
 - bool 

### 9.32 file_match_accept($file,$accept): bool
Check if the file matches the specified accept definition
#### Parameters
 - {string} *file* file
 - {string} *accept* <input accept=""/> information, please refer to the format: https://developer.mozilla.org/en-US/docs/Web/HTML/Attributes/accept

#### Returns
 - bool 



## 10. FONT
 > Font Handle Functions

### 10.1 ttf_info($ttf_file): array
Get ttf font file info
#### Parameters
 - {mixed} *ttf_file* 

#### Returns
 - array 

### 10.2 get_windows_fonts(): string[]
Get the Windows system font list (in file format, not necessarily accurate)

#### Returns
 - string[] 

### 10.3 _ttf_dec2ord()
### 10.4 _ttf_dec2hex()


## 11. HTML
 > HTML quick operation functions

### 11.1 html_tag_select($name,$options,$current_value,$placeholder,$attributes): string
Build select node, support optgroup mode
If it is grouping mode, the format is: [value=>text, label=>options, ...]
If it is normal mode, the format is: options: [value1=>text, value2=>text,...]
#### Parameters
 - {string} *name* 
 - {array} *options* option data,
 - {string|array} *current_value* 
 - {string} *placeholder* 
 - {array} *attributes* 

#### Returns
 - string 

### 11.2 html_tag_options($options,$current_value): string
Build select options
#### Parameters
 - {array} *options* [value=>text,...] option data option array
 - {string|array} *current_value* current value

#### Returns
 - string 

### 11.3 html_tag_option($text,$value,$selected,$attributes): string
Build option node
#### Parameters
 - {string} *text* text, spaces will be escaped into &nbsp;
 - {string} *value* 
 - {bool} *selected* 
 - {array} *attributes* 

#### Returns
 - string 

### 11.4 html_tag_option_group($label,$options,$current_value): string
Build optgroup node
#### Parameters
 - {string} *label* 
 - {array} *options* 
 - {string|array} *current_value* current value

#### Returns
 - string 

### 11.5 html_tag_textarea($name,$value,$attributes): string
Build textarea
#### Parameters
 - {string} *name* 
 - {string} *value* 
 - {array} *attributes* 

#### Returns
 - string 

### 11.6 html_tag_hidden($name,$value): string
Build hidden form node
#### Parameters
 - {string} *name* 
 - {string} *value* 

#### Returns
 - string 

### 11.7 html_tag_hidden_list($data_list): string
Build data hidden list
#### Parameters
 - {array} *data_list* data list (can be multi-dimensional array)

#### Returns
 - string 

### 11.8 html_tag_number_input($name,$value,$attributes): string
Build html digital input
#### Parameters
 - {string} *name* 
 - {string} *value* 
 - {array} *attributes* 

#### Returns
 - string 

### 11.9 html_tag_radio_group($name,$options,$current_value,$wrapper_tag,$radio_extra_attributes): string
#### Parameters
 - {string} *name* 
 - {array} *options* options [value=>title,...] format
 - {string} *current_value* 
 - {string} *wrapper_tag* Each option wraps the tag outside, such as li, div, etc.
 - {array} *radio_extra_attributes* Extra custom attributes for each radio

#### Returns
 - string 

### 11.10 html_tag_radio($name,$value,$title,$checked,$attributes): string
Build the radio button
Use label>(input:radio+{text}) structure
#### Parameters
 - {string} *name* 
 - {mixed} *value* 
 - {string} *title* 
 - {bool} *checked* 
 - {array} *attributes* 

#### Returns
 - string 

### 11.11 html_tag_checkbox_group($name,$options,$current_value,$wrapper_tag,$checkbox_extra_attributes): string
#### Parameters
 - {string} *name* 
 - {array} *options* options [value=>title,...] format
 - {string|array} *current_value* 
 - {string} *wrapper_tag* Each option wraps the tag outside, such as li, div, etc.
 - {array} *checkbox_extra_attributes* Extra custom attributes for each checkbox

#### Returns
 - string 

### 11.12 html_tag_checkbox($name,$value,$title,$checked,$attributes): string
Build a checkbox button
Use label>(input:checkbox+{text}) structure
#### Parameters
 - {string} *name* 
 - {mixed} *value* 
 - {string} *title* 
 - {bool} *checked* 
 - {array} *attributes* 

#### Returns
 - string 

### 11.13 html_tag_progress($value,$max,$attributes): string
Build progress bar (if no value is set, it can be used as loading effect)
#### Parameters
 - {null|number} *value* 
 - {null|number} *max* 
 - {array} *attributes* 

#### Returns
 - string 

### 11.14 html_tag_img($src,$attributes): string
HTML <img> tag
#### Parameters
 - {mixed} *src* 
 - {mixed} *attributes* 

#### Returns
 - string 

### 11.15 html_loading_bar($attributes): string
Html loop scrolling progress bar
alias to htmlProgress
#### Parameters
 - {array} *attributes* 

#### Returns
 - string 

### 11.16 html_tag_range($name,$value,$min,$max,$step,$attributes): string
Html range selector
#### Parameters
 - {string} *name* 
 - {string} *value* current value
 - {int} *min* minimum value
 - {int} *max* maximum value
 - {int} *step* step length
 - {array} *attributes* 

#### Returns
 - string 

### 11.17 html_abstract($html_content,$len): string
Get HTML summary information
#### Parameters
 - {string} *html_content* 
 - {int} *len* 

#### Returns
 - string 

### 11.18 html_tag_input_text($name,$value,$attributes): string
Build html input:text text input box
#### Parameters
 - {string} *name* 
 - {string} *value* 
 - {array} *attributes* 

#### Returns
 - string 

### 11.19 html_tag_date($name,$date_or_timestamp,$attributes): string
Build html date input box
#### Parameters
 - {string} *name* 
 - {string} *date_or_timestamp* 
 - {array} *attributes* 

#### Returns
 - string 

### 11.20 html_tag_time($name,$time_str,$attributes): string
Build html date input box
#### Parameters
 - {string} *name* 
 - {string} *time_str* 
 - {array} *attributes* 

#### Returns
 - string 

### 11.21 html_tag_datetime($name,$datetime_or_timestamp,$attributes): string
Build html date + time input box
#### Parameters
 - {string} *name* 
 - {string} *datetime_or_timestamp* 
 - {array} *attributes* 

#### Returns
 - string 

### 11.22 html_tag_month_select($name,$current_month,$format,$attributes): string
Build html month selector
#### Parameters
 - {string} *name* 
 - {int|null} *current_month* Current month, range 1~12
 - {string} *format* Month format, consistent with the format accepted by the date function
 - {array} *attributes* attributes

#### Returns
 - string 

### 11.23 html_tag_year_select($name,$current_year,$start_year,$end_year,$attributes): string
Build html year selector
#### Parameters
 - {string} *name* 
 - {int|null} *current_year* Current year
 - {int} *start_year* starting year (default is 1970)
 - {string} *end_year* Ending year (default is this year)
 - {array} *attributes* 

#### Returns
 - string 

### 11.24 html_tag($tag,$attributes,$inner_html): string
Build html node
#### Parameters
 - {string} *tag* 
 - {array} *attributes* 
 - {string} *inner_html* 

#### Returns
 - string 

### 11.25 html_tag_link($inner_html,$href,$attributes): string
Construct HTML link
#### Parameters
 - {string} *inner_html* 
 - {string} *href* 
 - {array} *attributes* 

#### Returns
 - string 

### 11.26 html_tag_css()
### 11.27 html_tag_js()
### 11.28 html_tag_date_input($name,$value,$attributes): string
Build html date input
#### Parameters
 - {string} *name* 
 - {string} *value* 
 - {array} *attributes* 

#### Returns
 - string 

### 11.29 html_tag_date_time_input($name,$value,$attributes): string
Build html time input
#### Parameters
 - {string} *name* 
 - {string} *value* 
 - {array} *attributes* 

#### Returns
 - string 

### 11.30 html_tag_data_list($id,$data_map): string
Build DataList
#### Parameters
 - {string} *id* 
 - {array} *data_map* index array: [val=>title,...], or natural growth array: [title1, title2,...]

#### Returns
 - string 

### 11.31 html_tag_input_submit($value,$attributes): string
submit input
#### Parameters
 - {mixed} *value* 
 - {array} *attributes* 

#### Returns
 - string 

### 11.32 html_tag_no_script($html): string
no script support html
#### Parameters
 - {string} *html* 

#### Returns
 - string 

### 11.33 html_tag_button_submit($inner_html,$attributes): string
submit button
#### Parameters
 - {string} *inner_html* 
 - {array} *attributes* 

#### Returns
 - string 

### 11.34 html_tag_table($data,$headers,$caption,$attributes): string
Build table node
#### Parameters
 - {array} *data* 
 - {array|false} *headers* header list [field name => alias, ...], if false, it means do not display the header
 - {string} *caption* 
 - {array} *attributes* 

#### Returns
 - string 

### 11.35 html_attributes($attributes): string
Construct HTML node attributes
Fix pattern, disabled HTML display when false
#### Parameters
 - {array} *attributes* 

#### Returns
 - string 

### 11.36 h($str,$len,$tail,$length_exceeded): string
Escape and truncate strings in HTML
#### Parameters
 - {string} *str* 
 - {number|null} *len* truncation length, empty means no truncation
 - {null|string} *tail* append tail string character
 - {bool} *length_exceeded* Exceeded length

#### Returns
 - string 

### 11.37 ha($str,$len,$tail,$length_exceeded): string
Escape and truncate HTML node attribute string
#### Parameters
 - {string} *str* 
 - {int} *len* truncation length, empty means no truncation
 - {string} *tail* append tail string character
 - {bool} *length_exceeded* Exceeded length

#### Returns
 - string 

### 11.38 text_to_html($text,$len,$tail,$over_length): string
Convert plain text to HTML
#### Parameters
 - {string} *text* 
 - {null} *len* 
 - {string} *tail* 
 - {bool} *over_length* 

#### Returns
 - string 

### 11.39 html_fix_relative_path($html,$page_url): string
Correct relative paths in HTML
#### Parameters
 - {string} *html* 
 - {string} *page_url* 

#### Returns
 - string Return the original HTML if replacement fails

### 11.40 html_to_text($html,$option): void
todo
#### Parameters
 - {mixed} *html* 
 - {mixed} *option* 

#### Returns
 - void 

### 11.41 html_text_highlight($text,$keyword,$template): string
Highlight text
#### Parameters
 - {string} *text* 
 - {string} *keyword* 
 - {string} *template* 

#### Returns
 - string Returns the HTML escaped string

### 11.42 html_tag_meta($equiv,$content): string
Construct HTML meta tags
#### Parameters
 - {string} *equiv* 
 - {string} *content* 

#### Returns
 - string 

### 11.43 html_meta_redirect($url,$timeout_sec): string
Use html meta to redirect pages
#### Parameters
 - {string} *url* jump target path
 - {int} *timeout_sec* timeout

#### Returns
 - string html

### 11.44 html_meta_csp($csp_rules,$report_uri,$report_only): string
Build CSP meta tag
#### Parameters
 - {array} *csp_rules* 
 - {string} *report_uri* 
 - {bool} *report_only* 

#### Returns
 - string 

### 11.45 html_value_compare($str1,$data): bool
HTML numeric comparison (converted to a string and then strictly compared)
#### Parameters
 - {string|number} *str1* 
 - {string|number|array} *data* 

#### Returns
 - bool whether they are equal

### 11.46 static_version_set($patch_config): array
Set static resource version control items
#### Parameters
 - {array} *patch_config* version configuration table, format such as: abc/foo.js => '2020', priority is given to matching rules with shorter lengths

#### Returns
 - array all configurations

### 11.47 static_version_patch($src,$matched): string
Static resource version patch
#### Parameters
 - {string} *src* 
 - {bool} *matched* 

#### Returns
 - string 

### 11.48 static_version_statement_quote($str): string
Static resource version wildcard escape
#### Parameters
 - {string} *str* 

#### Returns
 - string 

### 11.49 fix_browser_datetime($datetime_str_from_h5,$fix_seconds): string
Fix the data submitted by input:datetime or input:datetime-local in HTML5 browser
The time format submitted by H5 may be Ymd\TH:i
#### Parameters
 - {string} *datetime_str_from_h5* 
 - {int} *fix_seconds* Second correction. The H5 input box does not have second precision when submitted. This can be set to 0 (such as the start time) or 59 (such as the end time) to correct the second unit value.

#### Returns
 - string 



## 12. HTTP
 > HTTP Enhancement Function

### 12.1 http_send_status($status): bool
Send HTTP status code
#### Parameters
 - {int} *status* http status code

#### Returns
 - bool 

### 12.2 http_chunk_on()
Enable httpd server chunk output
### 12.3 http_send_cors($allow_hosts,$http_origin)
Return cross-domain CORS header information
#### Parameters
 - {string[]} *allow_hosts* List of domain names allowed to pass through, empty means all source domain names are allowed
 - {string} *http_origin* origin request, format: http://www.abc.com, by default obtained from HTTP_ORIGIN or HTTP_REFERER
### 12.4 http_send_charset($charset): bool
Send HTTP header character set
#### Parameters
 - {string} *charset* 

#### Returns
 - bool whether it is successful

### 12.5 http_get_status_message($status): string|null
Get the corresponding description of the HTTP status code
#### Parameters
 - {int} *status* 

#### Returns
 - string|null 

### 12.6 http_redirect($url,$permanently)
HTTP redirect
#### Parameters
 - {string} *url* jump path
 - {bool} *permanently* whether it is a long-term resource redirection
### 12.7 http_get_request_headers(): array
Get HTTP request header information array

#### Returns
 - array [key=>val]

### 12.8 http_get_request_header($key): mixed|null
Get the specified key value in the HTTP request header
#### Parameters
 - {string} *key* case-insensitive

#### Returns
 - mixed|null 

### 12.9 http_parse_headers($header_str): array
Parse http header information
#### Parameters
 - {mixed} *header_str* 

#### Returns
 - array 

### 12.10 http_from_json_request(): bool
Determine whether the request method is JSON

#### Returns
 - bool 

### 12.11 http_get_content_type($directives): string
Get the content-type in the http request header
#### Parameters
 - {array} *directives* additional directives, such as ['charset'=>'utf-8'], or ['boundary'=>'ExampleBoundaryString']

#### Returns
 - string 

### 12.12 http_request_accept_json($include_generic_match): bool
Determine whether the request is received in SON format
#### Parameters
 - {bool} *include_generic_match* Whether to support generic matching. Since the client may not strictly process the format of the request, this option is generally not enabled.

#### Returns
 - bool 

### 12.13 http_parse_string_use_q_value($str): array
Parse http header information in a weighted manner, Accept, Accept-Encoding, Accept-Language, TE, Want-Digest
#### Parameters
 - {mixed} *str* 

#### Returns
 - array [[type, q], ...]

### 12.14 request_in_post(): bool
Request comes from POST

#### Returns
 - bool 

### 12.15 request_in_get(): bool
Request comes from GET

#### Returns
 - bool 

### 12.16 http_get_current_page_url($with_protocol): string
Get the current page address
#### Parameters
 - {bool} *with_protocol* whether to include the protocol header

#### Returns
 - string 

### 12.17 http_get_current_host($with_protocol,$with_port): string
Get the domain name of the current page
#### Parameters
 - {bool} *with_protocol* whether to include the protocol header: http, https
 - {bool} *with_port* whether to include the port (valid only for non-http:80, https:443)

#### Returns
 - string such as http://www.abc.com http://www.abc.com:81 does not contain a slash at the end

### 12.18 http_download_stream($file,$download_name,$disposition): false|int
Download files by file streaming
#### Parameters
 - {string} *file* file path
 - {string} *download_name* Download file name
 - {string} *disposition* header type

#### Returns
 - false|int Successfully downloaded file size, false means failed

### 12.19 http_json_response($json,$json_option): void
Response json data
#### Parameters
 - {mixed} *json* 
 - {int} *json_option* 

#### Returns
 - void 

### 12.20 http_header_json_response($charset)
Response JSON return header
#### Parameters
 - {string} *charset* 
### 12.21 http_header_download($download_name,$disposition)
Send file download header information
#### Parameters
 - {string} *download_name* 
 - {string} *disposition* 
### 12.22 http_header_csp($csp_rules,$report_uri,$report_only)
Send CSP header
#### Parameters
 - {string[]} *csp_rules* It is recommended to use the rules generated by the csp_content_rule() method
 - {string} *report_uri* 
 - {bool} *report_only* 
### 12.23 http_header_report_api($endpoint_urls,$group,$max_age_sec,$include_subdomains)
Send browser settings Report API
#### Parameters
 - {string[]} *endpoint_urls* 
 - {string} *group* 
 - {number} *max_age_sec* 
 - {bool} *include_subdomains* 
### 12.24 http_header_report_api_nel($endpoint_urls,$group,$max_age_sec,$include_subdomains): void
Send browser error logs to Report API
#### Parameters
 - {string[]} *endpoint_urls* 
 - {string} *group* 
 - {number} *max_age_sec* 
 - {bool} *include_subdomains* 

#### Returns
 - void 

### 12.25 generate_report_api($endpoint_urls,$group,$max_age_sec,$include_subdomains): array
Generate Report API
#### Parameters
 - {string[]} *endpoint_urls* 
 - {string} *group* 
 - {number} *max_age_sec* 
 - {bool} *include_subdomains* 

#### Returns
 - array 

### 12.26 http_parse_cookie($cookie_str): array
Parse the cookie string into a hash array
#### Parameters
 - {string} *cookie_str* 

#### Returns
 - array 

### 12.27 http_fix_relative_url($url,$base_url): string|string[]
Correct relative URL to absolute URL
#### Parameters
 - {string} *url* 
 - {string} *base_url* base url (such as page url)

#### Returns
 - string|string[] 



## 13. SESSION
 > Session Enhancement Functions

### 13.1 session_start_once(): bool
Open a session once
If the original session status is not open, the session will be automatically closed after reading to avoid session locking

#### Returns
 - bool 

### 13.2 session_write_once()
Submit session data immediately and selectively close the session based on the context
### 13.3 session_write_scope($handler): bool
Automatically determine the current session status and write data from $_SESSION to the session
If the original session status is not open, the session will be automatically closed after the write operation is completed to avoid session locking, otherwise it will remain unchanged
Calling method:
session_write_scope(function(){
$_SESSION['hello'] = 'world';
unset($_SESSION['info']);
});
#### Parameters
 - {callable} *handler* 

#### Returns
 - bool 

### 13.4 session_start_in_time($expire_seconds): void
Start the session at the specified time
#### Parameters
 - {int} *expire_seconds* seconds

#### Returns
 - void 



## 14. SHEET
 > CSV, spreadsheet related operation functions
 > For normal open business, it is recommended to use XLSXBuilder (https://github.com/sasumi/XLSXBuilder)
 > Or other technical solutions similar to processing Excel.

### 14.1 spreadsheet_get_column_index($column): string
Get the column names in Excel and other spreadsheets
#### Parameters
 - {integer} *column* column number, starting from 1

#### Returns
 - string The column name in the spreadsheet, format such as: A1, E3

### 14.2 csv_download($filename,$data,$headers,$delimiter)
Output CSV file to browser for download
#### Parameters
 - {string} *filename* Download file name
 - {array} *data* 
 - {array|string[]} *headers* field list, format: [field=>alias,...], or ['name', 'password'] pure string array
 - {string} *delimiter* delimiter
### 14.3 csv_download_chunk($filename,$rows_fetcher,$headers,$delimiter)
Output CSV files in chunks to browser for download
#### Parameters
 - {string} *filename* Download file name
 - {callable} *rows_fetcher* data acquisition function, returns a two-dimensional array
 - {array|string[]} *headers* field list, format: [field=>alias,...], or ['name', 'password'] pure string array
 - {string} *delimiter* delimiter
### 14.4 csv_read_file_chunk($file,$output,$headers,$chunk_size,$start_line,$delimiter)
Read CSV file in chunks
#### Parameters
 - {string} *file* file name
 - {callable} *output* data output processing function, input parameter: rows, if the return parameter is false, the reading will be interrupted
 - {array} *headers* field list, format: [field=>alias,...] mapping field name
 - {int} *chunk_size* chunk size
 - {int} *start_line* The number of lines to start reading, the default is line 1
 - {string} *delimiter* delimiter
### 14.5 csv_read_file($file,$keys,$start_line,$delimiter): array
CSV Reading
#### Parameters
 - {string} *file* file path
 - {string[]} *keys* returns the array key configuration, if empty, returns the natural index array
 - {int} *start_line* The number of lines to start reading, the default is line 1
 - {string} *delimiter* delimiter

#### Returns
 - array data, the format is: [[key1=>val, key2=>val, ...], ...], if no key is configured, return a two-dimensional natural index array

### 14.6 csv_save_file($file,$rows,$delimiter,$mode)
Write to file
#### Parameters
 - {string} *file* file
 - {array[]} *rows* two-dimensional array
 - {string} *delimiter* delimiter
 - {string} *mode* file opening mode fopen(, mode)
### 14.7 csv_save_file_handle($file_handle,$rows,$delimiter)
Use the file handle method to write to the file (the handle will not be closed after writing is completed)
Compared with csv_save_file(), this function can be used for scenarios where files are written periodically and continuously, such as data stream processing
#### Parameters
 - {resource} *file_handle* file handle
 - {array[]} *rows* two-dimensional array
 - {string} *delimiter* delimiter
### 14.8 csv_format($val): string|array
Format CSV cell contents
#### Parameters
 - {mixed} *val* 

#### Returns
 - string|array 



## 15. STRING
 > String Enhancement Functions

### 15.1 substr_utf8($string,$length,$tail,$length_exceeded): string
UTF-8 Chinese and English truncation (two English words for one unit of quantity)
#### Parameters
 - {string} *string* string
 - {int} *length* cutting length
 - {string} *tail* append string to the end
 - {bool} *length_exceeded* whether it is too long

#### Returns
 - string 

### 15.2 is_json($str): bool
Check if the string is JSON
#### Parameters
 - {mixed} *str* 

#### Returns
 - bool 

### 15.3 explode_by($delimiters,$str,$trim_and_clear): array
Split the string according to the specified boundary character list
#### Parameters
 - {array|string} *delimiters* eg: [',', '-'] or ",-"
 - {string} *str* 
 - {bool} *trim_and_clear* removes blanks and empty values

#### Returns
 - array 

### 15.4 get_namespace($class): string
Get the namespace part of the specified class name
#### Parameters
 - {mixed} *class* 

#### Returns
 - string 

### 15.5 get_class_without_namespace($class): string
Get the class name part of the specified class
#### Parameters
 - {string} *class* 

#### Returns
 - string 

### 15.6 parse_str_without_limitation($string,$extra_to_post): array
Break through the max_input_vars limit and get variables by parsing strings
#### Parameters
 - {string} *string* 
 - {bool} *extra_to_post* 

#### Returns
 - array 

### 15.7 __array_merge_distinct_with_dynamic_key($array1,$array2,$dynamicKey): array
merge data
#### Parameters
 - {array} *array1* 
 - {array} *array2* 
 - {string} *dynamicKey* 

#### Returns
 - array 

### 15.8 asprintf($str,$arr): string[]
Batch call sprintf
#### Parameters
 - {string} *str* 
 - {array} *arr* Each item represents the parameter passed to sprintf, which can be an array

#### Returns
 - string[] 

### 15.9 match_wildcard($wildcard_pattern,$haystack): boolean
PHP wildcard matching
#### Parameters
 - {string} *wildcard_pattern* 
 - {string} *haystack* 

#### Returns
 - boolean 

### 15.10 str_split_by_charset($str,$len,$charset): array
Split the string according to the specified character encoding
#### Parameters
 - {string} *str* 
 - {int} *len* 
 - {string} *charset* 

#### Returns
 - array 

### 15.11 str_start_with($str,$starts,$case_sensitive): bool
Check if a string starts with another string
#### Parameters
 - {string} *str* string to be detected
 - {string|array} *starts* matches string or string array
 - {bool} *case_sensitive* is it case sensitive

#### Returns
 - bool 

### 15.12 int2str($data): array|string
Convert integer (int array) to string (string array)
#### Parameters
 - {mixed} *data* 

#### Returns
 - array|string 

### 15.13 calc_formula($stm,$param,$result_decorator): array
Formula calculation
#### Parameters
 - {string} *stm* expression, the variable starts with a $ sign, the parentheses indicate the description text of the variable (can be empty), the structure is like: $var1 (variable 1)
 - {array} *param* passed in variable, [key=>val] structure
 - {callable|null} *result_decorator* calculation result decoration callback (only affects the result during the calculation process, not the actual calculation result)

#### Returns
 - array [calculation result, calculation formula, calculation process]

### 15.14 cut_string($str,$len,$tail,$length_exceeded): array|float|int|mixed|string|void
String cutting (UTF8 encoding)
#### Parameters
 - {string|array} *str* 
 - {int} *len* 
 - {string} *tail* 
 - {bool} *length_exceeded* 

#### Returns
 - array|float|int|mixed|string|void 

### 15.15 print_tree_to_options($tree,$prefix): array
Use tabs to generate multi-level option styles
#### Parameters
 - {array} *tree* menu tree structure, the structure is [{name, value, children=>[]}, ...]
 - {string} *prefix* prefix string (automatically calculated)

#### Returns
 - array [[text, value], ...]

### 15.16 xml_special_chars($val): string
XML character escape
#### Parameters
 - {string} *val* 

#### Returns
 - string 

### 15.17 remove_utf8_bom($text): string
Remove UTF-8 BOM header
#### Parameters
 - {string} *text* 

#### Returns
 - string 

### 15.18 get_traditional_currency($num): string
Function to convert digital amount into Chinese uppercase amount
#### Parameters
 - {int} *num* The lowercase number or lowercase string to be converted (unit: yuan)

#### Returns
 - string 

### 15.19 password_check($password,$rules)
Password detection
#### Parameters
 - {string} *password* 
 - {array} *rules* 
### 15.20 str_contains($str,$char_list): bool
Check whether the string contains the specified character set
#### Parameters
 - {string} *str* 
 - {string} *char_list* 

#### Returns
 - bool 

### 15.21 rand_string($len,$source): string
Random string
#### Parameters
 - {int} *len* length
 - {string} *source* character source

#### Returns
 - string 

### 15.22 format_size($size,$dot): string
Format size
#### Parameters
 - {int} *size* bit value
 - {int} *dot* reserved decimal places

#### Returns
 - string 

### 15.23 resolve_size($val): int
Parse the actual file size expression
#### Parameters
 - {string} *val* file size, such as 12.3m, 43k

#### Returns
 - int 

### 15.24 str_mixing($text,$param): string
Text obfuscation
#### Parameters
 - {string} *text* text template, the placeholder uses the {VAR.SUB_VAR} format
 - {array} *param* obfuscation variable, key => $var format

#### Returns
 - string 

### 15.25 is_url($url): bool
Check if the string is a URL, the format also contains // This mode omits the protocol
#### Parameters
 - {string} *url* 

#### Returns
 - bool 

### 15.26 url_safe_b64encode($str): string
URL base64 security encoding
Replace the + / = symbols in base64 with - _ ''
base64 encoding
#### Parameters
 - {string} *str* 

#### Returns
 - string 

### 15.27 url_safe_b64decode($str): string
URL base64 safe decoding
base64 decoding
#### Parameters
 - {string} *str* 

#### Returns
 - string 

### 15.28 check_php_var_name_legal($str): false|string
Check if the string complies with PHP variable naming rules
#### Parameters
 - {string} *str* 

#### Returns
 - false|string 

### 15.29 filename_sanitize($filename): string|string[]
File name cleaning (according to Windows standards)
#### Parameters
 - {string} *filename* 

#### Returns
 - string|string[] 

### 15.30 pascalcase_to_underscores($str): string
Pascal style converted to underscore format
(Clean up multiple underscores at the same time)
#### Parameters
 - {string} *str* 

#### Returns
 - string 

### 15.31 underscores_to_pascalcase($str,$capitalize_first): string
Convert underscore format to Pascal format
#### Parameters
 - {string} *str* 
 - {bool} *capitalize_first* whether to use upper camel case format

#### Returns
 - string 

### 15.32 json_decode_safe($str,$associative,$depth,$flags): mixed
Safely parse the json string and throw an exception if an error occurs.
It is recommended to use PHP's native json_decode instead in business code.
#### Parameters
 - {string} *str* 
 - {bool} *associative* 
 - {int} *depth* 
 - {int} *flags* 

#### Returns
 - mixed 

### 15.33 encodeURIComponent($string): string
PHP URL encoding/decoding functions for Javascript interaction V3.0
(C) 2006 www.captain.at - all rights reserved
License: GPL
#### Parameters
 - {string} *string* 

#### Returns
 - string 

### 15.34 encodeURIComponentByCharacter()
### 15.35 decodeURIComponent($string): string
#### Parameters
 - {string} *string* 

#### Returns
 - string 

### 15.36 decodeURIComponentByCharacter($str): array
#### Parameters
 - {string} *str* 

#### Returns
 - array 

### 15.37 encodeURI()
### 15.38 encodeURIByCharacter($char): string
#### Parameters
 - {string} *char* 

#### Returns
 - string 

### 15.39 decodeURI($string): string
#### Parameters
 - {string} *string* 

#### Returns
 - string 

### 15.40 decodeURIByCharacter()
### 15.41 escape()
### 15.42 escapeByCharacter()
### 15.43 unescape($string): string
#### Parameters
 - {string} *string* 

#### Returns
 - string 

### 15.44 unEscapeByCharacter()
### 15.45 generate_guid($trim): string
Returns a GUIDv4 string
Uses the best cryptographically secure method
for all supported platforms with fallback to an older,
less secure version.
#### Parameters
 - {bool} *trim* 

#### Returns
 - string 



## 16. TIME
 > Time Enhancement Functions

### 16.1 time_get_month_period_ranges($start_str,$end_str): array
Get the upper, middle and lower segment arrays of the specified start and end time
#### Parameters
 - {string} *start_str* 
 - {string} *end_str* 

#### Returns
 - array [[period_th, start_time, end_time],...]

### 16.2 keep_interval($timer_key,$interval)
Call interval guarantee
#### Parameters
 - {string} *timer_key* 
 - {int} *interval* 
### 16.3 get_timezone_offset_min_between_gmt($timezone_title): float|int
#### Parameters
 - {string} *timezone_title* 

#### Returns
 - float|int 

### 16.4 get_time_left(): int|null
Get the remaining time (seconds)
If it is CLI mode, this function does not perform calculations

#### Returns
 - int|null seconds, null means unlimited

### 16.5 filter_date_range($ranges,$default_start,$default_end,$as_datetime): array
Filter time range, add hours, minutes and seconds
#### Parameters
 - {array} *ranges* time range (start, end)
 - {string|int} *default_start* default start time
 - {string|int} *default_end* default end time
 - {bool} *as_datetime* whether to return in date + time format

#### Returns
 - array [start time, end time]

### 16.6 microtime_diff($start,$end): float
Calculate a precise time difference.
#### Parameters
 - {string} *start* result of microtime()
 - {string} *end* result of microtime(); if NULL/FALSE/0/'' then it's now

#### Returns
 - float difference in seconds, calculated with minimum precision loss

### 16.7 format_time_size($secs,$keep_zero_padding,$full_desc): string
format time range
#### Parameters
 - {int} *secs* 
 - {bool} *keep_zero_padding* 
 - {bool} *full_desc* 

#### Returns
 - string 

### 16.8 microtime_to_date($microtime,$format,$precision): string
Convert microseconds to the specified time format
#### Parameters
 - {string} *microtime* microsecond string, generated by microtime(false)
 - {string} *format* time format
 - {int} *precision* precision (seconds later)

#### Returns
 - string 

### 16.9 float_time_to_date($float_time,$format,$precision): string
Convert seconds (floating point number) to the specified time format
#### Parameters
 - {float} *float_time* time, generated by microtime(true)
 - {string} *format* time format
 - {int} *precision* precision (seconds later)

#### Returns
 - string 

### 16.10 time_empty($time_str): bool
check time string is empty (cmp to 1970)
#### Parameters
 - {string} *time_str* 

#### Returns
 - bool 

### 16.11 pretty_time($timestamp,$as_html): string
Format the time to display in a friendly way
#### Parameters
 - {int} *timestamp* 
 - {bool} *as_html* whether to use span wrapping

#### Returns
 - string 

### 16.12 make_date_ranges($start,$end,$format): array
Supplement the date range and fill in the blank days in the middle
#### Parameters
 - {string|int} *start* start time (start time is allowed to be greater than end time)
 - {string|int} *end* end time
 - {string} *format* result date format, if set to month, the function will automatically remove duplicates

#### Returns
 - array 

### 16.13 calc_actual_date($start,$days): string
Get the date after $days base on start day
Actual date = number of working days + number of weekend days - 1
#### Parameters
 - {string} *start* start date
 - {int} *days* Number of working days: positive number means going backward, negative number means going forward

#### Returns
 - string Ymd date

### 16.14 time_range($start,$end): string
Calculate time difference to text
#### Parameters
 - {string} *start* 
 - {string} *end* 

#### Returns
 - string 

### 16.15 time_get_eta($start_time,$index,$total,$pretty): int|string
Calculate the estimated end time ETA
#### Parameters
 - {int} *start_time* start time
 - {int} *index* Current processing sequence number
 - {int} *total* total quantity
 - {bool} *pretty* whether to return the remaining time in text format, set false to return seconds

#### Returns
 - int|string 

### 16.16 time_range_v($seconds): string
Convert time length to string
<pre>
$str = time_range_v(3601);
//1H 0M 1S
</pre>
#### Parameters
 - {int} *seconds* 

#### Returns
 - string 

### 16.17 mk_utc($timestamp,$short): array|false|string|string[]
Make UTC time string
#### Parameters
 - {mixed} *timestamp* 
 - {mixed} *short* 

#### Returns
 - array|false|string|string[] 



## 17. UTIL
 > Miscellaneous Functions

### 17.1 tick_dump($step,$fun)
Step-by-step debugging
#### Parameters
 - {int} *step* step length
 - {string} *fun* debug function, dump is used by default
### 17.2 readline()
Read console line input. If the system has an extension installed, the extension function is used first.
### 17.3 try_many_times($payload,$tries): int
Try calling the function
#### Parameters
 - {callable} *payload* processing function, returning FALSE means aborting subsequent attempts
 - {int} *tries* The number of additional attempts when an error occurs (excluding the first normal execution)

#### Returns
 - int total number of attempts (excluding the first normal execution)

### 17.4 dump()
Program debugging function
Calling method: dump($var1, $var2, ..., 1), when the last value is 1, it means to exit (die) the program
### 17.5 printable($var,$print_str): bool
Check whether the variable can be printed (such as strings, numbers, objects containing toString methods, etc.)
Boolean values, resources, etc. are not printable variables
#### Parameters
 - {mixed} *var* 
 - {string} *print_str* printable string

#### Returns
 - bool whether it is printable

### 17.6 print_exception($ex,$include_external_properties,$as_return): string
Print exception information
#### Parameters
 - {\Exception} *ex* 
 - {bool} *include_external_properties* whether to include additional exception information
 - {bool} *as_return* whether to process in return mode (not printing exceptions)

#### Returns
 - string 

### 17.7 print_trace($trace,$with_callee,$with_index,$as_return): string
Print trace information
#### Parameters
 - {array} *trace* 
 - {bool} *with_callee* 
 - {bool} *with_index* 
 - {bool} *as_return* 

#### Returns
 - string 

### 17.8 print_sys_error($code,$msg,$file,$line,$trace_string)
Print system errors and trace information
#### Parameters
 - {integer} *code* 
 - {string} *msg* 
 - {string} *file* 
 - {integer} *line* 
 - {string} *trace_string* 
### 17.9 error2string($code): string
Convert error code value to string
#### Parameters
 - {int} *code* 

#### Returns
 - string 

### 17.10 string2error($string): int
Convert error codes to specific code values
#### Parameters
 - {string} *string* 

#### Returns
 - int 

### 17.11 exception_convert($exception,$target_class): mixed
Convert the exception object to other specified exception class objects
#### Parameters
 - {Exception} *exception* 
 - {string} *target_class* 

#### Returns
 - mixed 

### 17.12 register_error2exception($error_levels,$exception_class): callable|null
Register to convert PHP errors into exceptions
#### Parameters
 - {int} *error_levels* 
 - {\ErrorException|null} *exception_class* 

#### Returns
 - callable|null 

### 17.13 is_function($f): boolean
Check if it is a function
#### Parameters
 - {mixed} *f* 

#### Returns
 - boolean 

### 17.14 class_uses_recursive($class_or_object): string[]
Get all inherited parent classes of objects and classes (including trait classes)
If you don't need traits, try class_parents
#### Parameters
 - {string|object} *class_or_object* 

#### Returns
 - string[] 

### 17.15 trait_uses_recursive($trait): array
Get traits recursively
#### Parameters
 - {string} *trait* 

#### Returns
 - array 

### 17.16 get_constant_name($class,$const_val): string|null
Get the name of the specified class constant
#### Parameters
 - {string} *class* class name
 - {mixed} *const_val* constant value

#### Returns
 - string|null 

### 17.17 assert_via_exception($expression,$err_msg,$exception_class)
Handle assertions by throwing exceptions
#### Parameters
 - {mixed} *expression* assertion value
 - {string} *err_msg* 
 - {string} *exception_class* exception class, default is \Exception
### 17.18 pdog($fun,$handler)
pdog
#### Parameters
 - {string} *fun* 
 - {callable|string} *handler* 
### 17.19 guid(): mixed
Get the current context GUID

#### Returns
 - mixed 

### 17.20 var_export_min($var,$return): string|null
Export variables using minimal format (similar to var_export)
#### Parameters
 - {mixed} *var* 
 - {bool} *return* whether to return in return mode, the default is to output to the terminal

#### Returns
 - string|null 

### 17.21 memory_leak_check($threshold,$leak_payload)
Detect memory overflow. It is not recommended to enable this check when running the code to avoid performance loss.
#### Parameters
 - {int} *threshold* 
 - {callable|string} *leak_payload* Function called when memory leaks
### 17.22 debug_mark($tag,$trace_location,$mem_usage): mixed
Code management
#### Parameters
 - {string} *tag* 
 - {bool} *trace_location* 
 - {bool} *mem_usage* 

#### Returns
 - mixed 

### 17.23 debug_mark_output($as_return): string|null
Output dot information
#### Parameters
 - {bool} *as_return* 

#### Returns
 - string|null 



