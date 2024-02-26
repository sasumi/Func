## 1. ARRAY
 > 数组相关操作函数

### 1.1 array_group($array,$by_key,$force_unique): array
#### 参数
 - {array} *array* 数组
 - {string} *by_key* 合并key字符串
 - {boolean} *force_unique* 

#### 返回值
 - array handle result

### 1.2 range_slice($start,$end,$size): \Generator
#### 参数
 - {int} *start* 开始下标
 - {int} *end* 结束下标
 - {int} *size* 每页大小

#### 返回值
 - \Generator 

### 1.3 object_shuffle($objects): array
#### 参数
 - {array|object} *objects* 

#### 返回值
 - array 

### 1.4 array_random($arr,$count): array
#### 参数
 - {array} *arr* 源数组，支持自然索引数组与关联数组
 - {int} *count* 获取数量

#### 返回值
 - array 返回指定数量的数组

### 1.5 array_keys_exists($keys,$arr): bool
#### 参数
 - {array} *keys* 
 - {array} *arr* 

#### 返回值
 - bool 

### 1.6 plain_items($arr,$original_key,$original_key_name): array
#### 参数
 - {array} *arr* 
 - {string} *original_key* 
 - {string} *original_key_name* 

#### 返回值
 - array 

### 1.7 array2object($arr): \stdClass
#### 参数
 - {array} *arr* 

#### 返回值
 - \stdClass 

### 1.8 object2array($obj): array
#### 参数
 - {object} *obj* 

#### 返回值
 - array 

### 1.9 restructure_files($input): array
#### 参数
 - {array} *input* 

#### 返回值
 - array 

### 1.10 array_copy_by_fields($array,$fields): array
#### 参数
 - {array} *array* 
 - {array} *fields* 

#### 返回值
 - array 

### 1.11 array_merge_recursive_distinct($array1,$array2): array
#### 参数
 - {array} *array1* 
 - {array} *array2* 

#### 返回值
 - array 

### 1.12 array_clear_null($data,$recursive): array|mixed
#### 参数
 - {array|mixed} *data* 
 - {bool} *recursive* 

#### 返回值
 - array|mixed 

### 1.13 array_clear_empty($data,$recursive): array|mixed
#### 参数
 - {array|mixed} *data* 
 - {bool} *recursive* 

#### 返回值
 - array|mixed 

### 1.14 array_move_item($arr,$item_index_key,$dir): array
#### 参数
 - {array} *arr* 数组
 - {string|number} *item_index_key* 需要切换元素的key值（可以是关联数组的key）
 - {number} *dir* 移动方向

#### 返回值
 - array 

### 1.15 array_clear_fields($keep_fields,$data): array
#### 参数
 - {array} *keep_fields* 
 - {array} *data* 

#### 返回值
 - array 

### 1.16 array_unset_by_value($arr,$del_val)
#### 参数
 - {array} *arr* 
 - {mixed} *del_val* 
### 1.17 array_trim_fields($data,$fields,$recursive): array
#### 参数
 - {array} *data* 数据
 - {array} *fields* 指定字段，为空表示所有字段
 - {bool} *recursive* 是否递归处理，如果递归，则data允许为任意维数组

#### 返回值
 - array 

### 1.18 array_first()
### 1.19 array_last($data,$key): null
#### 参数
 - {array} *data* 
 - {null} *key* 

#### 返回值
 - null 

### 1.20 array_unshift_assoc()
### 1.21 array_shift_assoc($arr): array|bool
#### 参数
 - {array} *arr* 

#### 返回值
 - array|bool [value, key] 键值对，不存在则返回false

### 1.22 array_orderby($src_arr): array
#### 参数
 - {array} *src_arr* 

#### 返回值
 - array 

### 1.23 array_orderby_keys($src_arr,$keys,$miss_match_in_head): array
#### 参数
 - {array} *src_arr* 
 - {string[]} *keys* 键值数组
 - {bool} *miss_match_in_head* 未命中值是否排列在头部

#### 返回值
 - array 

### 1.24 array_index($array,$compare_fn_or_value): bool|int|string
#### 参数
 - {array} *array* 
 - {callable|mixed} *compare_fn_or_value* 

#### 返回值
 - bool|int|string 

### 1.25 array_sumby($arr,$key): mixed
#### 参数
 - {array} *arr* 
 - {string} *key* 

#### 返回值
 - mixed 

### 1.26 array_default($arr,$values,$reset_empty): array
#### 参数
 - {array} *arr* 
 - {array} *values* 
 - {bool} *reset_empty* reset empty value in array

#### 返回值
 - array 

### 1.27 null_in_array($arr): bool
#### 参数
 - {array} *arr* 

#### 返回值
 - bool 

### 1.28 array_filter_by_keys($arr,$keys): array
#### 参数
 - {array} *arr* 
 - {array} *keys* 

#### 返回值
 - array 

### 1.29 array_make_spreadsheet_columns($column_size): array
#### 参数
 - {int} *column_size* 

#### 返回值
 - array 

### 1.30 array_push_by_path($data,$path_str,$value,$glue)
#### 参数
 - {array} *data* 目标数组
 - {string} *path_str* 路径表达式，如：企微.企业.正式企业数量
 - {mixed} *value* 项目值
 - {string} *glue* 分隔符
### 1.31 array_fetch_by_path($data,$path_str,$default,$delimiter): mixed
#### 参数
 - {array} *data* 源数据
 - {string} *path_str* 路径
 - {mixed} *default* 缺省值
 - {string} *delimiter* 分隔符

#### 返回值
 - mixed 

### 1.32 array_get($data,$path_str,$default,$delimiter): mixed
#### 参数
 - {array} *data* 源数据
 - {string} *path_str* 路径
 - {mixed} *default* 缺省值
 - {string} *delimiter* 分隔符

#### 返回值
 - mixed 

### 1.33 assert_array_has_keys($arr,$keys)
#### 参数
 - {array} *arr* 
 - {array} *keys* 
### 1.34 array_filter_subtree($parent_id,$all,$opt,$level,$group_by_parents): array
#### 参数
 - {string|int} *parent_id* 
 - {array} *all* 
 - {array} *opt* 
 - {int} *level* 
 - {array} *group_by_parents* 

#### 返回值
 - array 

### 1.35 array_insert_after($src_array,$data,$rel_key): array|int
#### 参数
 - {array} *src_array* 
 - {mixed} *data* 
 - {string} *rel_key* 

#### 返回值
 - array|int 

### 1.36 array_merge_after($src_array,$new_array,$rel_key): array
#### 参数
 - {array} *src_array* 
 - {array} *new_array* 
 - {string} *rel_key* 

#### 返回值
 - array 

### 1.37 is_assoc_array($array): boolean
#### 参数
 - {array} *array* 

#### 返回值
 - boolean 

### 1.38 array_transform($data,$rules): mixed
#### 参数
 - {array} *data* 
 - {array} *rules* = array('dddd' => array('aaaa', 'bbbb'))

#### 返回值
 - mixed 

### 1.39 array_value_recursive($key,$arr): array|mixed
#### 参数
 - {string} *key* 
 - {array} *arr* 

#### 返回值
 - array|mixed 



## 2. COLOR
 > 颜色相关操作函数

### 2.1 color_hex2rgb($hex_color): array
#### 参数
 - {string} *hex_color* #ff00bb

#### 返回值
 - array 

### 2.2 color_rgb2hex($rgb,$prefix): string
#### 参数
 - {array} *rgb* [r,g,b]
 - {string} *prefix* 前缀

#### 返回值
 - string 

### 2.3 color_rgb2hsl($rgb): float[]
#### 参数
 - {array} *rgb* 

#### 返回值
 - float[] [h,s,l]

### 2.4 color_hsl2rgb($hsl): int[]
#### 参数
 - {array} *hsl* [h,s,l]

#### 返回值
 - int[] [r,g,b]

### 2.5 color_rgb2cmyk($rgb): array
#### 参数
 - {array} *rgb* 

#### 返回值
 - array [c,m,y,k]

### 2.6 cmyk_to_rgb($cmyk): int[]
#### 参数
 - {array} *cmyk* 

#### 返回值
 - int[] [r,g,b]

### 2.7 color_rgb2hsb($rgb,$accuracy): array
#### 参数
 - {array} *rgb* [r,g,b]
 - {int} *accuracy* 

#### 返回值
 - array 

### 2.8 color_hsb2rgb($hsb,$accuracy): int[]
#### 参数
 - {array} *hsb* [h,s,b]
 - {int} *accuracy* 精确度

#### 返回值
 - int[] [r,g,b]

### 2.9 color_molarity($color_val,$inc_pec): array|string
#### 参数
 - {string|array} *color_val* 16进制颜色或rgb数组
 - {float} *inc_pec* 百分比，范围：-99 ~ 99

#### 返回值
 - array|string 

### 2.10 color_rand(): string

#### 返回值
 - string 

### 2.11 _hsl_rgb_low()
### 2.12 _hsl_rgb_high()
### 2.13 _rgb_hsl_delta_rgb()
### 2.14 _rgb_hsl_hue()


## 3. CRON
 > crontab相关操作函数

### 3.1 cron_match($format,$time,$error): bool
#### 参数
 - {string} *format* cron格式。暂不支持年份，格式为：分钟 时钟 天数 月数 星期
 - {int} *time* 默认为当前时间戳
 - {string|null} *error* mismatch error info

#### 返回值
 - bool 

### 3.2 cron_watch_commands($rules,$on_before_call,$check_interval)
#### 参数
 - {array} *rules* 
 - {callable} *on_before_call* 
 - {int} *check_interval* seconds, must min than one minutes


## 4. CSP
 > CSP相关操作函数

### 4.1 csp_content_rule($resource,$policy,$custom_defines): string
#### 参数
 - {string} *resource* 资源
 - {string} *policy* 策略
 - {string} *custom_defines* 策略扩展数据（主要针对 CSP_POLICY_SCRIPT_NONCE CSP_POLICY_SCRIPT_HASH）

#### 返回值
 - string 

### 4.2 csp_report_uri($uri): string
#### 参数
 - {string} *uri* 

#### 返回值
 - string 



## 5. CURL
 > CURL网络请求相关操作函数

### 5.1 curl_get($url,$data,$curl_option): array
#### 参数
 - {string} *url* 
 - {mixed|null} *data* 
 - {array|null|callable} *curl_option* 额外CURL选项，如果是闭包函数，传入第一个参数为ch

#### 返回值
 - array [head, body, ...] curl_getinfo信息

### 5.2 curl_post($url,$data,$curl_option): array
#### 参数
 - {string} *url* 
 - {mixed|null} *data* 
 - {array} *curl_option* 

#### 返回值
 - array 

### 5.3 curl_post_json($url,$data,$curl_option): array
#### 参数
 - {string} *url* 
 - {mixed} *data* 
 - {array} *curl_option* 

#### 返回值
 - array 

### 5.4 curl_post_file($url,$file_map,$ext_param,$curl_option): array
#### 参数
 - {string} *url* 
 - {array} *file_map* [filename=>filepath,...]
 - {mixed} *ext_param* 同时提交的其他post参数
 - {array} *curl_option* curl选项

#### 返回值
 - array curl_execute返回结果，包含 [info=>[], head=>'', body=>''] 信息

### 5.5 curl_put($url,$data,$curl_option): array
#### 参数
 - {string} *url* 
 - {array} *data* 
 - {array} *curl_option* 

#### 返回值
 - array 

### 5.6 curl_delete($url,$data,$curl_option): array
#### 参数
 - {string} *url* 
 - {array} *data* 
 - {array} *curl_option* 

#### 返回值
 - array 

### 5.7 curl_execute($ch): array
#### 参数
 - {resource} *ch* 

#### 返回值
 - array [info=>[], head=>'', body=>''] curl_getinfo信息

### 5.8 curl_build_command($url,$body_str,$method,$headers): string
#### 参数
 - {string} *url* 
 - {string} *body_str* 
 - {string} *method* 
 - {string[]} *headers* 头部信息，格式为 ['Content-Type: application/json'] 或 ['Content-Type‘=>'application/json']

#### 返回值
 - string 

### 5.9 curl_instance($url,$curl_option): false|resource
#### 参数
 - {string} *url* 
 - {array} *curl_option* 

#### 返回值
 - false|resource 

### 5.10 curl_data2str($data): string
#### 参数
 - {mixed} *data* 

#### 返回值
 - string 

### 5.11 curl_print_option($options,$as_return): array|null
#### 参数
 - {array} *options* 
 - {bool} *as_return* 

#### 返回值
 - array|null 

### 5.12 curl_merge_options()
### 5.13 http_parse_headers($header_str): array
#### 参数
 - {mixed} *header_str* 

#### 返回值
 - array 

### 5.14 curl_option_to_request_header($options): string[]
#### 参数
 - {array} *options* 

#### 返回值
 - string[] array

### 5.15 curl_to_har($curl_options,$curl_info,$response_header,$response_body): void
#### 参数
 - {array} *curl_options* 
 - {array} *curl_info* 
 - {mixed} *response_header* 
 - {mixed} *response_body* 

#### 返回值
 - void 



## 6. DB
 > 数据库操作（PDO）相关操作函数

### 6.1 db_connect($db_type,$host,$user,$password,$database,$port,$charsets,$persistence_connect): \PDO
#### 参数
 - {string} *db_type* 
 - {string} *host* 
 - {string} *user* 
 - {string} *password* 
 - {string} *database* 
 - {int|null} *port* 
 - {string} *charsets* 
 - {bool} *persistence_connect* 

#### 返回值
 - \PDO 

### 6.2 db_connect_via_ssh_proxy($db_config,$ssh_config,$proxy_config): \PDO
#### 参数
 - {array} *db_config* ['type', 'host', 'user', 'password', 'database', 'port']
 - {array} *ssh_config* ['host', 'user', 'password'', 'port']
 - {array} *proxy_config* ['host', 'port']

#### 返回值
 - \PDO 

### 6.3 db_auto_ssh_port()
### 6.4 db_mysql_connect($host,$user,$password,$database,$port,$charsets,$persistence_connect): \PDO
#### 参数
 - {string} *host* 
 - {string} *user* 
 - {string} *password* 
 - {string} *database* 
 - {null} *port* 
 - {string} *charsets* 
 - {bool} *persistence_connect* 

#### 返回值
 - \PDO 

### 6.5 db_connect_dsn($dsn,$user,$password,$persistence_connect): \PDO
#### 参数
 - {string} *dsn* 
 - {string} *user* 
 - {string} *password* 
 - {bool} *persistence_connect* 

#### 返回值
 - \PDO 

### 6.6 db_build_dsn($db_type,$host,$database,$port,$charsets): string
#### 参数
 - {string} *db_type* 
 - {string} *host* 
 - {string} *database* 
 - {string} *port* 
 - {string} *charsets* 

#### 返回值
 - string 

### 6.7 db_query($pdo,$sql): false|\PDOStatement
#### 参数
 - {\PDO} *pdo* 
 - {string} *sql* 

#### 返回值
 - false|\PDOStatement 

### 6.8 db_query_all($pdo,$sql): array
#### 参数
 - {\PDO} *pdo* 
 - {string} *sql* 

#### 返回值
 - array 

### 6.9 db_query_one($pdo,$sql): array
#### 参数
 - {\PDO} *pdo* 
 - {string} *sql* 

#### 返回值
 - array 

### 6.10 db_query_field($pdo,$sql,$field): mixed|null
#### 参数
 - {\PDO} *pdo* 
 - {string} *sql* 
 - {string|null} *field* 

#### 返回值
 - mixed|null 

### 6.11 db_sql_patch_limit($sql,$start_offset,$size): string
#### 参数
 - {string} *sql* 
 - {int} *start_offset* 
 - {int|null} *size* 

#### 返回值
 - string 

### 6.12 db_query_count($pdo,$sql): int
#### 参数
 - {\PDO} *pdo* 
 - {string} *sql* 

#### 返回值
 - int 

### 6.13 db_query_paginate($pdo,$sql,$page,$page_size): array
#### 参数
 - {\PDO} *pdo* 
 - {string} *sql* 
 - {int} *page* 
 - {int} *page_size* 

#### 返回值
 - array [列表, 总数]

### 6.14 db_query_chunk($pdo,$sql,$handler,$chunk_size): bool
#### 参数
 - {\PDO} *pdo* 
 - {string} *sql* 
 - {callable} *handler* 批次处理函数，传入参数($rows, $page, $finish)，如返回false，则中断执行
 - {int} *chunk_size* 

#### 返回值
 - bool 是否为正常结束，false表示为批处理函数中断导致

### 6.15 db_watch($pdo,$sql,$watcher,$chunk_size,$sleep_interval): bool
#### 参数
 - {\PDO} *pdo* 
 - {string} *sql* 
 - {callable} *watcher* 批次处理函数，传入参数($rows)，如返回false，则中断执行
 - {int} *chunk_size* 分块大小
 - {int} *sleep_interval* 睡眠间隔时间（秒）

#### 返回值
 - bool 是否为正常结束，false表示为批处理函数中断导致

### 6.16 db_quote_value($data): array|string
#### 参数
 - {array|string|int} *data* 

#### 返回值
 - array|string 

### 6.17 db_quote_field($fields): array|string
#### 参数
 - {string|array} *fields* 

#### 返回值
 - array|string 

### 6.18 db_affect_rows($result): int|false
#### 参数
 - {\PDOStatement} *result* 

#### 返回值
 - int|false 

### 6.19 db_sql_prepare()
### 6.20 db_delete()
### 6.21 db_insert($pdo,$table,$data): false|int
#### 参数
 - {\PDO} *pdo* 
 - {string} *table* 
 - {array} *data* 

#### 返回值
 - false|int 

### 6.22 db_update()
### 6.23 db_increase()
### 6.24 db_transaction($pdo,$handler): bool|mixed
#### 参数
 - {\PDO} *pdo* 
 - {callable} *handler* 处理器，如果返回false或抛出异常，将中断提交，执行回滚操作

#### 返回值
 - bool|mixed 



## 7. ENV
 > 平台函数相关操作函数

### 7.1 server_in_windows(): bool

#### 返回值
 - bool 

### 7.2 server_in_https(): bool

#### 返回值
 - bool 

### 7.3 get_upload_max_size($human_readable): string|number
#### 参数
 - {bool} *human_readable* 是否以可读方式返回

#### 返回值
 - string|number 

### 7.4 get_max_socket_timeout($ttf): int
#### 参数
 - {int} *ttf* 允许提前时长

#### 返回值
 - int 超时时间（秒），如为0，表示不限制超时时间

### 7.5 get_client_ip(): string

#### 返回值
 - string 客户端IP，获取失败返回空字符串

### 7.6 get_all_opt(): array

#### 返回值
 - array 

### 7.7 get_php_info(): array

#### 返回值
 - array 

### 7.8 console_color($text,$fore_color,$back_color): string
#### 参数
 - {string} *text* 
 - {null} *fore_color* 
 - {null} *back_color* 

#### 返回值
 - string 

### 7.9 show_progress($index,$total,$patch_text,$start_time,$progress_length,$max_length)
#### 参数
 - {int} *index* 
 - {int} *total* 
 - {string} *patch_text* 补充显示文本
 - {int} *start_time* 开始时间戳
 - {int} *progress_length* 
 - {int} *max_length* 
### 7.10 run_command($command,$param,$async): bool|string|null
#### 参数
 - {string} *command* 命令
 - {array} *param* 参数
 - {bool} *async* 是否以异步方式执行

#### 返回值
 - bool|string|null 

### 7.11 run_command_parallel_width_progress($command,$param_batches,$options): bool
#### 参数
 - {string} *command* 
 - {array} *param_batches* 
 - {array} *options* 

#### 返回值
 - bool 

### 7.12 run_command_parallel($command,$param_batches,$options): bool
#### 参数
 - {string} *command* 执行命令
 - {array} *param_batches* 任务参数列表，参数按照长参数方式传入command，具体实现可参考：build_command() 函数实现。
 - {array} *options* 参数如下：

#### 返回值
 - bool 是否正常结束

### 7.13 build_command($cmd_line,$param): string
#### 参数
 - {string} *cmd_line* 
 - {array} *param* 

#### 返回值
 - string 

### 7.14 escape_win32_argv($value): string
#### 参数
 - {string|int} *value* 

#### 返回值
 - string 

### 7.15 escape_win32_cmd($value): string|string[]|null
#### 参数
 - {mixed} *value* 

#### 返回值
 - string|string[]|null 

### 7.16 noshell_exec($command): false|string
#### 参数
 - {string} *command* 

#### 返回值
 - false|string 

### 7.17 command_exists($command): bool
#### 参数
 - {string} *command* 

#### 返回值
 - bool 

### 7.18 windows_get_port_usage($include_process_info): array
#### 参数
 - {bool} *include_process_info* 是否包含进程信息（标题、程序文件名），该功能需要Windows管理员模式

#### 返回值
 - array 格式:[protocol='', local_ip='', local_port='', foreign_ip='', foreign_port='', state='', pid='', 'process_name'='', 'process_file_id'=>'']

### 7.19 unix_get_port_usage(): array

#### 返回值
 - array 格式:[protocol='', local_ip='', local_port='', foreign_ip='', foreign_port='', state='', pid='', 'process_name'='', 'process_file_id'=>'']

### 7.20 get_screen_size(): array|null

#### 返回值
 - array|null 返回格式：[列数，行数】，当前环境不支持则返回 null

### 7.21 pkill($pid,$sig_num): bool
#### 参数
 - {number} *pid* 
 - {numeric} *sig_num* 

#### 返回值
 - bool 

### 7.22 launch_daemon_task($payload,$keep_alive_timeout,$id): int
#### 参数
 - {callable} *payload* 处理逻辑，入参1为心跳函数，调用者必须周期性调用，避免程序被判定为休眠
 - {int} *keep_alive_timeout* 保活时效（秒）
 - {string} *id* 处理函数唯一ID

#### 返回值
 - int 启动后的进程ID，如果原来的进程没有超时，返回原来进程ID



## 8. FILE
 > 文件相关操作函数

### 8.1 glob_recursive($pattern,$flags): array
#### 参数
 - {string} *pattern* 
 - {int} *flags* 

#### 返回值
 - array 

### 8.2 file_exists_case_sensitive($file): bool|null
#### 参数
 - {string} *file* 

#### 返回值
 - bool|null 

### 8.3 assert_file_in_dir($file,$dir,$exception_class)
#### 参数
 - {string} *file* 
 - {string} *dir* 
 - {string} *exception_class* 
### 8.4 file_in_dir($file_path,$dir_path): bool
#### 参数
 - {string} *file_path* 文件路径
 - {string} *dir_path* 目录路径

#### 返回值
 - bool 文件不存在目录当中，或文件实际不存在

### 8.5 resolve_absolute_path($file_or_path): string
#### 参数
 - {string} *file_or_path* 目录路径或文件路径字符串

#### 返回值
 - string 

### 8.6 resolve_file_extension($filename,$to_lower_case): string|null
#### 参数
 - {string} *filename* 文件名
 - {bool} *to_lower_case* 是否转换成小写，缺省为转换为小写

#### 返回值
 - string|null string or null,no extension detected

### 8.7 file_exists_case_insensitive($file,$parent): bool
#### 参数
 - {string} *file* 
 - {null} *parent* 

#### 返回值
 - bool 

### 8.8 copy_recursive($src,$dst)
#### 参数
 - {string} *src* 
 - {string} *dst* 
### 8.9 get_dirs($dir): array
#### 参数
 - {string} *dir* 

#### 返回值
 - array 

### 8.10 file_lines($file,$line_separator): int
#### 参数
 - {string|resource} *file* 文件路径或文件句柄
 - {string} *line_separator* 换行符

#### 返回值
 - int 

### 8.11 tail($file,$callback,$line_limit,$line_separator)
#### 参数
 - {string} *file* 文件
 - {callable} *callback* 行处理函数
 - {int} *line_limit* 
 - {string} *line_separator* 换行符
### 8.12 read_line($file,$handle,$start_line,$buff_size): bool
#### 参数
 - {string} *file* 文件名称
 - {callable} *handle* 处理函数，传入参数：($line_str, $line), 若函数返回false，则中断处理
 - {int} *start_line* 开始读取行数（由 1 开始）
 - {int} *buff_size* 缓冲区大小

#### 返回值
 - bool 是否为处理函数中断返回

### 8.13 render_php_file($php_file,$vars): false|string
#### 参数
 - {mixed} *php_file* 
 - {array} *vars* 

#### 返回值
 - false|string 

### 8.14 get_folder_size($path): int
#### 参数
 - {string} *path* 

#### 返回值
 - int 

### 8.15 log($file,$content,$max_size,$max_files,$pad_str): bool|int
#### 参数
 - {string} *file* 文件
 - {mixed} *content* 记录内容
 - {float|int} *max_size* 单文件最大尺寸，默认
 - {int} *max_files* 最大记录文件数
 - {string|null} *pad_str* 记录文件名追加字符串

#### 返回值
 - bool|int 文件是否记录成功

### 8.16 read_file_lock($key): false|string|null
#### 参数
 - {string} *key* 

#### 返回值
 - false|string|null 

### 8.17 write_file_lock($key,$lock_flag): string
#### 参数
 - {string} *key* 
 - {string} *lock_flag* 

#### 返回值
 - string 

### 8.18 remove_file_lock($key): bool
#### 参数
 - {string} *key* 

#### 返回值
 - bool 

### 8.19 init_file_lock($key,$is_new): resource
#### 参数
 - {string} *key* 
 - {bool} *is_new* 

#### 返回值
 - resource 锁文件操作句柄

### 8.20 log_tmp_file($filename,$content,$max_size,$max_files,$pad_str): bool|int
#### 参数
 - {string} *filename* 
 - {mixed} *content* 
 - {float|int} *max_size* 
 - {int} *max_files* 
 - {string|null} *pad_str* 

#### 返回值
 - bool|int 

### 8.21 create_tmp_file($dir,$prefix,$ext,$mod): string
#### 参数
 - {string} *dir* 文件所在目录
 - {string} *prefix* 文件名前缀
 - {string} *ext* 文件名后缀
 - {numeric} *mod* 权限，缺省为777

#### 返回值
 - string 



## 9. FONT
 > 字体类相关操作函数

### 9.1 ttf_info()
### 9.2 get_windows_fonts(): string[]

#### 返回值
 - string[] 

### 9.3 _ttf_dec2ord()
### 9.4 _ttf_dec2hex()


## 10. HTML
 > Html 快速操作函数

### 10.1 html_tag_select($name,$options,$current_value,$placeholder,$attributes): string
#### 参数
 - {string} *name* 
 - {array} *options* 选项数据，
 - {string|array} *current_value* 
 - {string} *placeholder* 
 - {array} *attributes* 

#### 返回值
 - string 

### 10.2 html_tag_options($options,$current_value): string
#### 参数
 - {array} *options* [value=>text,...] option data 选项数组
 - {string|array} *current_value* 当前值

#### 返回值
 - string 

### 10.3 html_tag_option($text,$value,$selected,$attributes): string
#### 参数
 - {string} *text* 文本，空白将被转义成&nbsp;
 - {string} *value* 
 - {bool} *selected* 
 - {array} *attributes* 

#### 返回值
 - string 

### 10.4 html_tag_option_group($label,$options,$current_value): string
#### 参数
 - {string} *label* 
 - {array} *options* 
 - {string|array} *current_value* 当前值

#### 返回值
 - string 

### 10.5 html_tag_textarea($name,$value,$attributes): string
#### 参数
 - {string} *name* 
 - {string} *value* 
 - {array} *attributes* 

#### 返回值
 - string 

### 10.6 html_tag_hidden($name,$value): string
#### 参数
 - {string} *name* 
 - {string} *value* 

#### 返回值
 - string 

### 10.7 html_tag_hidden_list($data_list): string
#### 参数
 - {array} *data_list* 数据列表（可以多维数组）

#### 返回值
 - string 

### 10.8 html_tag_number_input($name,$value,$attributes): string
#### 参数
 - {string} *name* 
 - {string} *value* 
 - {array} *attributes* 

#### 返回值
 - string 

### 10.9 html_tag_radio_group($name,$options,$current_value,$wrapper_tag,$radio_extra_attributes): string
#### 参数
 - {string} *name* 
 - {array} *options* 选项[value=>title,...]格式
 - {string} *current_value* 
 - {string} *wrapper_tag* 每个选项外部包裹标签，例如li、div等
 - {array} *radio_extra_attributes* 每个radio额外定制属性

#### 返回值
 - string 

### 10.10 html_tag_radio($name,$value,$title,$checked,$attributes): string
#### 参数
 - {string} *name* 
 - {mixed} *value* 
 - {string} *title* 
 - {bool} *checked* 
 - {array} *attributes* 

#### 返回值
 - string 

### 10.11 html_tag_checkbox_group($name,$options,$current_value,$wrapper_tag,$checkbox_extra_attributes): string
#### 参数
 - {string} *name* 
 - {array} *options* 选项[value=>title,...]格式
 - {string|array} *current_value* 
 - {string} *wrapper_tag* 每个选项外部包裹标签，例如li、div等
 - {array} *checkbox_extra_attributes* 每个checkbox额外定制属性

#### 返回值
 - string 

### 10.12 html_tag_checkbox($name,$value,$title,$checked,$attributes): string
#### 参数
 - {string} *name* 
 - {mixed} *value* 
 - {string} *title* 
 - {bool} *checked* 
 - {array} *attributes* 

#### 返回值
 - string 

### 10.13 html_tag_progress($value,$max,$attributes): string
#### 参数
 - {null|number} *value* 
 - {null|number} *max* 
 - {array} *attributes* 

#### 返回值
 - string 

### 10.14 html_loading_bar($attributes): string
#### 参数
 - {array} *attributes* 

#### 返回值
 - string 

### 10.15 html_tag_range($name,$value,$min,$max,$step,$attributes): string
#### 参数
 - {string} *name* 
 - {string} *value* 当前值
 - {int} *min* 最小值
 - {int} *max* 最大值
 - {int} *step* 步长
 - {array} *attributes* 

#### 返回值
 - string 

### 10.16 html_abstract($html_content,$len): string
#### 参数
 - {string} *html_content* 
 - {int} *len* 

#### 返回值
 - string 

### 10.17 html_tag_input_text($name,$value,$attributes): string
#### 参数
 - {string} *name* 
 - {string} *value* 
 - {array} *attributes* 

#### 返回值
 - string 

### 10.18 html_tag_date($name,$date_or_timestamp,$attributes): string
#### 参数
 - {string} *name* 
 - {string} *date_or_timestamp* 
 - {array} *attributes* 

#### 返回值
 - string 

### 10.19 html_tag_datetime($name,$datetime_or_timestamp,$attributes): string
#### 参数
 - {string} *name* 
 - {string} *datetime_or_timestamp* 
 - {array} *attributes* 

#### 返回值
 - string 

### 10.20 html_tag_month_select($name,$current_month,$format,$attributes): string
#### 参数
 - {string} *name* 
 - {int|null} *current_month* 当前月份，范围1~12表示
 - {string} *format* 月份格式，与date函数接受格式一致
 - {array} *attributes* 属性

#### 返回值
 - string 

### 10.21 html_tag_year_select($name,$current_year,$start_year,$end_year,$attributes): string
#### 参数
 - {string} *name* 
 - {int|null} *current_year* 当前年份
 - {int} *start_year* 开始年份（缺省为1970）
 - {string} *end_year* 结束年份（缺省为今年）
 - {array} *attributes* 

#### 返回值
 - string 

### 10.22 html_tag($tag,$attributes,$inner_html): string
#### 参数
 - {string} *tag* 
 - {array} *attributes* 
 - {string} *inner_html* 

#### 返回值
 - string 

### 10.23 html_tag_link($inner_html,$href,$attributes): string
#### 参数
 - {string} *inner_html* 
 - {string} *href* 
 - {array} *attributes* 

#### 返回值
 - string 

### 10.24 html_tag_css()
### 10.25 html_tag_js()
### 10.26 html_tag_date_input($name,$value,$attributes): string
#### 参数
 - {string} *name* 
 - {string} *value* 
 - {array} *attributes* 

#### 返回值
 - string 

### 10.27 html_tag_date_time_input($name,$value,$attributes): string
#### 参数
 - {string} *name* 
 - {string} *value* 
 - {array} *attributes* 

#### 返回值
 - string 

### 10.28 html_tag_data_list($id,$data): string
#### 参数
 - {string} *id* 
 - {array} *data* [val=>title,...]

#### 返回值
 - string 

### 10.29 html_tag_input_submit($value,$attributes): string
#### 参数
 - {mixed} *value* 
 - {array} *attributes* 

#### 返回值
 - string 

### 10.30 html_tag_no_script($html): string
#### 参数
 - {string} *html* 

#### 返回值
 - string 

### 10.31 html_tag_button_submit($inner_html,$attributes): string
#### 参数
 - {string} *inner_html* 
 - {array} *attributes* 

#### 返回值
 - string 

### 10.32 html_tag_table($data,$headers,$caption,$attributes): string
#### 参数
 - {array} *data* 
 - {array|false} *headers* 表头列表 [字段名 => 别名, ...]，如为false，表示不显示表头
 - {string} *caption* 
 - {array} *attributes* 

#### 返回值
 - string 

### 10.33 html_attributes($attributes): string
#### 参数
 - {array} *attributes* 

#### 返回值
 - string 

### 10.34 text_to_html($text,$len,$tail,$over_length): string
#### 参数
 - {string} *text* 
 - {null} *len* 
 - {string} *tail* 
 - {bool} *over_length* 

#### 返回值
 - string 

### 10.35 html_text_highlight($text,$keyword,$template): string
#### 参数
 - {string} *text* 
 - {string} *keyword* 
 - {string} *template* 

#### 返回值
 - string 返回HTML转义过的字符串

### 10.36 html_tag_meta($equiv,$content): string
#### 参数
 - {string} *equiv* 
 - {string} *content* 

#### 返回值
 - string 

### 10.37 html_meta_redirect($url,$timeout_sec): string
#### 参数
 - {string} *url* 跳转目标路径
 - {int} *timeout_sec* 超时时间

#### 返回值
 - string html

### 10.38 html_meta_csp($csp_rules,$report_uri,$report_only): string
#### 参数
 - {array} *csp_rules* 
 - {string} *report_uri* 
 - {bool} *report_only* 

#### 返回值
 - string 

### 10.39 html_value_compare($str1,$data): bool
#### 参数
 - {string|number} *str1* 
 - {string|number|array} *data* 

#### 返回值
 - bool 是否相等

### 10.40 static_version_set($patch_config): array
#### 参数
 - {array} *patch_config* 版本配置表，格式如：abc/foo.js => '2020'，优先匹配长度短的规则

#### 返回值
 - array 所有配置

### 10.41 static_version_patch($src,$matched): string
#### 参数
 - {string} *src* 
 - {bool} *matched* 

#### 返回值
 - string 

### 10.42 static_version_statement_quote($str): string
#### 参数
 - {string} *str* 

#### 返回值
 - string 

### 10.43 fix_browser_datetime($datetime_str_from_h5): string|null
#### 参数
 - {string} *datetime_str_from_h5* 

#### 返回值
 - string|null 



## 11. HTTP
 > HTTP 快速操作函数

### 11.1 http_send_status($status): bool
#### 参数
 - {int} *status* http 状态码

#### 返回值
 - bool 

### 11.2 request_in_post(): bool

#### 返回值
 - bool 

### 11.3 request_in_get(): bool

#### 返回值
 - bool 

### 11.4 http_send_cors($allow_hosts,$http_origin)
#### 参数
 - {string[]} *allow_hosts* 允许通过的域名列表，为空表示允许所有来源域名
 - {string} *http_origin* 来源请求，格式为：http://www.abc.com，缺省从 HTTP_ORIGIN 或 HTTP_REFERER获取
### 11.5 http_send_charset($charset): bool
#### 参数
 - {string} *charset* 

#### 返回值
 - bool 是否成功

### 11.6 http_get_status_message($status): string|null
#### 参数
 - {int} *status* 

#### 返回值
 - string|null 

### 11.7 http_redirect($url,$permanently)
#### 参数
 - {string} *url* 跳转路径
 - {bool} *permanently* 是否为长期资源重定向
### 11.8 http_get_request_headers(): array

#### 返回值
 - array [key=>val]

### 11.9 http_get_request_header($key): mixed|null
#### 参数
 - {string} *key* 不区分大小写

#### 返回值
 - mixed|null 

### 11.10 http_from_json_request(): bool

#### 返回值
 - bool 

### 11.11 http_request_accept_json(): bool

#### 返回值
 - bool 

### 11.12 http_get_current_page_url($with_protocol): string
#### 参数
 - {bool} *with_protocol* 是否包含协议头

#### 返回值
 - string 

### 11.13 http_download_stream($file,$download_name,$disposition)
#### 参数
 - {string} *file* 
 - {string} *download_name* 
 - {string} *disposition* 
### 11.14 http_header_json_response($charset)
#### 参数
 - {string} *charset* 
### 11.15 http_json_response($json,$json_option): void
#### 参数
 - {mixed} *json* 
 - {int} *json_option* 

#### 返回值
 - void 

### 11.16 http_header_download($download_name,$disposition)
#### 参数
 - {string} *download_name* 
 - {string} *disposition* 
### 11.17 http_header_csp($csp_rules,$report_uri,$report_only)
#### 参数
 - {string[]} *csp_rules* 建议使用csp_content_rule()方法产生的规则
 - {string} *report_uri* 
 - {bool} *report_only* 
### 11.18 generate_report_api($endpoint_urls,$group,$max_age_sec,$include_subdomains): array
#### 参数
 - {string[]} *endpoint_urls* 
 - {string} *group* 
 - {number} *max_age_sec* 
 - {bool} *include_subdomains* 

#### 返回值
 - array 

### 11.19 http_header_report_api($endpoint_urls,$group,$max_age_sec,$include_subdomains)
#### 参数
 - {string[]} *endpoint_urls* 
 - {string} *group* 
 - {number} *max_age_sec* 
 - {bool} *include_subdomains* 
### 11.20 http_header_report_api_nel($endpoint_urls,$group,$max_age_sec,$include_subdomains): void
#### 参数
 - {string[]} *endpoint_urls* 
 - {string} *group* 
 - {number} *max_age_sec* 
 - {bool} *include_subdomains* 

#### 返回值
 - void 



## 12. SESSION
 > session 相关操作函数

### 12.1 session_start_once(): bool

#### 返回值
 - bool 

### 12.2 session_write_once()
### 12.3 session_write_scope($handler): bool
#### 参数
 - {callable} *handler* 

#### 返回值
 - bool 

### 12.4 session_start_in_time($expire_seconds): void
#### 参数
 - {int} *expire_seconds* 秒

#### 返回值
 - void 



## 13. SHEET
 > CSV、电子表格相关操作函数
 > 如果正常开放性业务，建议使用 XLSXBuilder (https://github.com/sasumi/XLSXBuilder)
 > 或类似处理excel的其他技术方案。

### 13.1 get_spreadsheet_column($column): string
#### 参数
 - {integer} *column* 列序号，由1开始

#### 返回值
 - string 电子表格中的列名，格式如：A1、E3

### 13.2 download_csv($download_name,$data,$fields,$mime_type)
#### 参数
 - {string} *download_name* 下载文件名
 - {array} *data* 
 - {array} *fields* 字段列表，格式为：[field=>alias,...]
 - {string} *mime_type* 
### 13.3 download_csv_chunk($download_name,$batch_fetcher,$fields,$mime_type)
#### 参数
 - {string} *download_name* 下载文件名
 - {callable} *batch_fetcher* 
 - {array} *fields* 字段列表，格式为：[field=>alias,...]
 - {string} *mime_type* 
### 13.4 read_csv($file,$keys,$ignore_head_lines): array
#### 参数
 - {string} *file* 文件路径
 - {array} *keys* 
 - {int} *ignore_head_lines* 

#### 返回值
 - array 数据，格式为：[[字段1,字段2,...],...]

### 13.5 read_csv_chunk($output,$file,$fields,$chunk_size,$ignore_head_lines)
#### 参数
 - {callable} *output* 数据输出处理函数，传入参数：chunks， 返回参数若为false，则中断读取
 - {string} *file* 文件名称
 - {array} *fields* 字段列表，格式为：[field=>alias,...] 映射字段名
 - {int} *chunk_size* 分块大小
 - {int} *ignore_head_lines* 忽略开始头部标题行数
### 13.6 save_csv($file,$data,$fields)
#### 参数
 - {string} *file* 文件路径
 - {array} *data* 
 - {array} *fields* 字段列表，格式为：[field=>alias,...]
### 13.7 save_csv_chunk($file,$batch_fetcher,$fields)
#### 参数
 - {string} *file* 文件路径
 - {callable} *batch_fetcher* 
 - {array} *fields* 字段列表，格式为：[field=>alias,...]
### 13.8 csv_output_chunk($output,$batch_fetcher,$fields,$uniq_seed): int
#### 参数
 - {callable} *output* 
 - {callable} *batch_fetcher* 
 - {array} *fields* 字段列表，格式为：[field=>alias,...]
 - {int} *uniq_seed* 

#### 返回值
 - int 数据行数

### 13.9 csv_output($output,$data,$fields): bool|int
#### 参数
 - {callable} *output* 
 - {array} *data* 二维数组
 - {array} *fields* 字段列表，格式为：[field=>alias,...]

#### 返回值
 - bool|int 

### 13.10 format_csv_ceil($str): string|array
#### 参数
 - {mixed} *str* 

#### 返回值
 - string|array 



## 14. STRING
 > 字符串相关操作函数

### 14.1 substr_utf8($string,$length,$tail,$over_length): string
#### 参数
 - {string} *string* 串
 - {int} *length* 切割长度
 - {string} *tail* 尾部追加字符串
 - {bool} *over_length* 是否超长

#### 返回值
 - string 

### 14.2 is_json($str): bool
#### 参数
 - {mixed} *str* 

#### 返回值
 - bool 

### 14.3 explode_by($delimiters,$str,$trim_and_clear): array
#### 参数
 - {array|string} *delimiters* eg: [',', '-'] or ",-"
 - {string} *str* 
 - {bool} *trim_and_clear* 去除空白及空值

#### 返回值
 - array 

### 14.4 get_namespace($class): string
#### 参数
 - {mixed} *class* 

#### 返回值
 - string 

### 14.5 get_class_without_namespace($class): string
#### 参数
 - {string} *class* 

#### 返回值
 - string 

### 14.6 parse_str_without_limitation($string,$extra_to_post): array
#### 参数
 - {string} *string* 
 - {bool} *extra_to_post* 

#### 返回值
 - array 

### 14.7 __array_merge_distinct_with_dynamic_key($array1,$array2,$dynamicKey): array
#### 参数
 - {array} *array1* 
 - {array} *array2* 
 - {string} *dynamicKey* 

#### 返回值
 - array 

### 14.8 match_wildcard($wildcard_pattern,$haystack): boolean
#### 参数
 - {string} *wildcard_pattern* 
 - {string} *haystack* 

#### 返回值
 - boolean 

### 14.9 str_split_by_charset($str,$len,$charset): array
#### 参数
 - {string} *str* 
 - {int} *len* 
 - {string} *charset* 

#### 返回值
 - array 

### 14.10 str_start_with($str,$starts,$case_sensitive): bool
#### 参数
 - {string} *str* 待检测字符串
 - {string|array} *starts* 匹配字符串或字符串数组
 - {bool} *case_sensitive* 是否大小写敏感

#### 返回值
 - bool 

### 14.11 int2str($data): array|string
#### 参数
 - {mixed} *data* 

#### 返回值
 - array|string 

### 14.12 calc_formula($stm,$param,$result_decorator): array
#### 参数
 - {string} *stm* 表达式，变量以$符号开始，小括号中表示该变量的描述文本（可为空）,结构如：$var1(变量1)
 - {array} *param* 传入变量，[key=>val]结构
 - {callable|null} *result_decorator* 计算结果修饰回调（仅影响计算过程中的结果，不影响真实计算结果）

#### 返回值
 - array [计算结果, 计算公式， 计算过程]

### 14.13 h($str,$len,$tail,$over_length): string|array
#### 参数
 - {array|string} *str* 
 - {number|null} *len* 截断长度，为空表示不截断
 - {null|string} *tail* 追加尾串字符
 - {bool} *over_length* 超长长度

#### 返回值
 - string|array 

### 14.14 ha($str,$len,$tail,$over_length): string|array
#### 参数
 - {array|string} *str* 
 - {number|null} *len* 截断长度，为空表示不截断
 - {null|string} *tail* 追加尾串字符
 - {bool} *over_length* 超长长度

#### 返回值
 - string|array 

### 14.15 __h($str,$len,$tail,$over_length,$flags): array|string
#### 参数
 - {string} *str* 
 - {null} *len* 
 - {string} *tail* 
 - {bool} *over_length* 
 - {null} *flags* ENT_QUOTES|ENT_SUBSTITUTE

#### 返回值
 - array|string 

### 14.16 xml_special_chars($val): string
#### 参数
 - {string} *val* 

#### 返回值
 - string 

### 14.17 remove_utf8_bom($text): string
#### 参数
 - {string} *text* 

#### 返回值
 - string 

### 14.18 get_traditional_currency($num): string
#### 参数
 - {int} *num* 要转换的小写数字或小写字符串（单位：元）

#### 返回值
 - string 

### 14.19 password_check($password,$rules)
#### 参数
 - {string} *password* 
 - {array} *rules* 
### 14.20 str_contains($str,$char_list): bool
#### 参数
 - {string} *str* 
 - {string} *char_list* 

#### 返回值
 - bool 

### 14.21 rand_string($len,$source): string
#### 参数
 - {int} *len* 长度
 - {string} *source* 字符源

#### 返回值
 - string 

### 14.22 format_size($size,$dot): string
#### 参数
 - {int} *size* 比特值
 - {int} *dot* 预留小数点位数

#### 返回值
 - string 

### 14.23 resolve_size($val): int
#### 参数
 - {string} *val* 文件大小，如 12.3m, 43k

#### 返回值
 - int 

### 14.24 str_mixing($text,$param): string
#### 参数
 - {string} *text* 文字模板，占位符采用 {VAR.SUB_VAR} 格式
 - {array} *param* 混淆变量 ,key => $var 格式

#### 返回值
 - string 

### 14.25 url_safe_b64encode($str): string
#### 参数
 - {string} *str* 

#### 返回值
 - string 

### 14.26 url_safe_b64decode($str): string
#### 参数
 - {string} *str* 

#### 返回值
 - string 

### 14.27 check_php_var_name_legal($str): false|string
#### 参数
 - {string} *str* 

#### 返回值
 - false|string 

### 14.28 filename_sanitize($filename): string|string[]
#### 参数
 - {string} *filename* 

#### 返回值
 - string|string[] 

### 14.29 pascalcase_to_underscores($str): string
#### 参数
 - {string} *str* 

#### 返回值
 - string 

### 14.30 underscores_to_pascalcase($str,$capitalize_first): string
#### 参数
 - {string} *str* 
 - {bool} *capitalize_first* 是否使用大驼峰格式

#### 返回值
 - string 

### 14.31 json_decode_safe($str,$associative,$depth,$flags): mixed
#### 参数
 - {string} *str* 
 - {bool} *associative* 
 - {int} *depth* 
 - {int} *flags* 

#### 返回值
 - mixed 

### 14.32 encodeURIComponent($string): string
#### 参数
 - {string} *string* 

#### 返回值
 - string 

### 14.33 encodeURIComponentByCharacter()
### 14.34 decodeURIComponent($string): string
#### 参数
 - {string} *string* 

#### 返回值
 - string 

### 14.35 decodeURIComponentByCharacter($str): array
#### 参数
 - {string} *str* 

#### 返回值
 - array 

### 14.36 encodeURI()
### 14.37 encodeURIByCharacter($char): string
#### 参数
 - {string} *char* 

#### 返回值
 - string 

### 14.38 decodeURI($string): string
#### 参数
 - {string} *string* 

#### 返回值
 - string 

### 14.39 decodeURIByCharacter()
### 14.40 escape()
### 14.41 escapeByCharacter()
### 14.42 unescape($string): string
#### 参数
 - {string} *string* 

#### 返回值
 - string 

### 14.43 unEscapeByCharacter()
### 14.44 generate_guid($trim): string
#### 参数
 - {bool} *trim* 

#### 返回值
 - string 



## 15. TIME
 > 时间相关操作函数

### 15.1 time_get_month_period_ranges($start_str,$end_str): array
#### 参数
 - {string} *start_str* 
 - {string} *end_str* 

#### 返回值
 - array [[period_th, start_time, end_time],...]

### 15.2 get_timezone_offset_min_between_gmt($timezone_title): float|int
#### 参数
 - {string} *timezone_title* 

#### 返回值
 - float|int 

### 15.3 get_time_left(): int|null

#### 返回值
 - int|null 秒，null表示无限制

### 15.4 filter_date_range($ranges,$default_start,$default_end,$as_datetime): array
#### 参数
 - {array} *ranges* 时间范围（开始，结束）
 - {string|int} *default_start* 默认开始时间
 - {string|int} *default_end* 默认结束时间
 - {bool} *as_datetime* 是否以日期+时间形式返回

#### 返回值
 - array [开始时间,结束时间]

### 15.5 microtime_diff($start,$end): float
#### 参数
 - {string} *start* result of microtime()
 - {string} *end* result of microtime(); if NULL/FALSE/0/'' then it's now

#### 返回值
 - float difference in seconds, calculated with minimum precision loss

### 15.6 format_time_size($secs,$keep_zero_padding,$full_desc): string
#### 参数
 - {int} *secs* 
 - {bool} *keep_zero_padding* 
 - {bool} *full_desc* 

#### 返回值
 - string 

### 15.7 microtime_to_date($microtime,$format,$precision): string
#### 参数
 - {string} *microtime* 微秒字符串，通过 microtime(false) 产生
 - {string} *format* 时间格式
 - {int} *precision* 精度（秒之后）

#### 返回值
 - string 

### 15.8 float_time_to_date($float_time,$format,$precision): string
#### 参数
 - {float} *float_time* 时间，通过 microtime(true) 产生
 - {string} *format* 时间格式
 - {int} *precision* 精度（秒之后）

#### 返回值
 - string 

### 15.9 time_empty($time_str): bool
#### 参数
 - {string} *time_str* 

#### 返回值
 - bool 

### 15.10 pretty_time($timestamp,$as_html): string
#### 参数
 - {int} *timestamp* 
 - {bool} *as_html* 是否使用span包裹

#### 返回值
 - string 

### 15.11 make_date_ranges($start,$end,$format): array
#### 参数
 - {string|int} *start* 开始时间（允许开始时间大于结束时间）
 - {string|int} *end* 结束时间
 - {string} *format* 结果日期格式，如果设置为月份，函数自动去重

#### 返回值
 - array 

### 15.12 calc_actual_date($start,$days): string
#### 参数
 - {string} *start* 开始日期
 - {int} *days* 工作日天数 正数为往后，负数为往前

#### 返回值
 - string Y-m-d 日期

### 15.13 time_range($start,$end): string
#### 参数
 - {string} *start* 
 - {string} *end* 

#### 返回值
 - string 

### 15.14 time_range_v($seconds): string
#### 参数
 - {int} *seconds* 

#### 返回值
 - string 

### 15.15 mk_utc()


## 16. UTIL
 > 杂项操作函数

### 16.1 tick_dump($step,$fun)
#### 参数
 - {int} *step* 步长
 - {string} *fun* 调试函数，默认使用dump
### 16.2 dump()
### 16.3 printable($var,$print_str): bool
#### 参数
 - {mixed} *var* 
 - {string} *print_str* 可打印字符串

#### 返回值
 - bool 是否可打印

### 16.4 print_exception($ex,$include_external_properties,$as_return): string
#### 参数
 - {\Exception} *ex* 
 - {bool} *include_external_properties* 是否包含额外异常信息
 - {bool} *as_return* 是否以返回方式（不打印异常）处理

#### 返回值
 - string 

### 16.5 print_trace($trace,$with_callee,$with_index,$as_return): string
#### 参数
 - {array} *trace* 
 - {bool} *with_callee* 
 - {bool} *with_index* 
 - {bool} *as_return* 

#### 返回值
 - string 

### 16.6 print_sys_error($code,$msg,$file,$line,$trace_string)
#### 参数
 - {integer} *code* 
 - {string} *msg* 
 - {string} *file* 
 - {integer} *line* 
 - {string} *trace_string* 
### 16.7 error2string($code): string
#### 参数
 - {int} *code* 

#### 返回值
 - string 

### 16.8 string2error($string): int
#### 参数
 - {string} *string* 

#### 返回值
 - int 

### 16.9 register_error2exception($error_levels,$exception_class): callable|null
#### 参数
 - {int} *error_levels* 
 - {\ErrorException|null} *exception_class* 

#### 返回值
 - callable|null 

### 16.10 is_function($f): boolean
#### 参数
 - {mixed} *f* 

#### 返回值
 - boolean 

### 16.11 class_uses_recursive($class_or_object): string[]
#### 参数
 - {string|object} *class_or_object* 

#### 返回值
 - string[] 

### 16.12 trait_uses_recursive($trait): array
#### 参数
 - {string} *trait* 

#### 返回值
 - array 

### 16.13 get_constant_name($class,$const_val): string|null
#### 参数
 - {string} *class* 类名
 - {mixed} *const_val* 常量值

#### 返回值
 - string|null 

### 16.14 assert_via_exception($expression,$err_msg,$exception_class)
#### 参数
 - {mixed} *expression* 断言值
 - {string} *err_msg* 
 - {string} *exception_class* 异常类，缺省使用 \Exception
### 16.15 pdog($fun,$handler)
#### 参数
 - {string} *fun* 
 - {callable|string} *handler* 
### 16.16 guid(): mixed

#### 返回值
 - mixed 

### 16.17 var_export_min($var,$return): string|null
#### 参数
 - {mixed} *var* 
 - {bool} *return* 是否以返回方式返回，缺省为输出到终端

#### 返回值
 - string|null 

### 16.18 memory_leak_check($threshold,$leak_payload)
#### 参数
 - {int} *threshold* 
 - {callable|string} *leak_payload* 内存泄露时调用函数
### 16.19 debug_mark($tag,$trace_location,$mem_usage): mixed
#### 参数
 - {string} *tag* 
 - {bool} *trace_location* 
 - {bool} *mem_usage* 

#### 返回值
 - mixed 

### 16.20 debug_mark_output($as_return): string|null
#### 参数
 - {bool} *as_return* 

#### 返回值
 - string|null 



