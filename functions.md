## 1. ARRAY
 > 数组相关操作函数

### 1.1 array_group($array,$by_key,$force_unique): array
Array group by function
group array(); by by_key
#### 参数
 - {array} *array* 数组
 - {string} *by_key* 合并key字符串
 - {boolean} *force_unique* 

#### 返回值
 - array handle result

### 1.2 range_slice($start,$end,$size): \Generator
划分范围，
将指定开始···结束数值按照一定size进行分组。
如对：2 ~ 9 数字进行分组，每组最大个数为3，则结果为： [2,3,4],[5,6,7],[8,9]
<pre>
用法：
foreach(range_slice(2,9) as list($s, $e)){
echo "$s ~ $e", PHP_EOL;
}
</pre>
#### 参数
 - {int} *start* 开始下标
 - {int} *end* 结束下标
 - {int} *size* 每页大小

#### 返回值
 - \Generator 

### 1.3 object_shuffle($objects): array
shuffle objects, key original assoc key
#### 参数
 - {array|object} *objects* 

#### 返回值
 - array 

### 1.4 array_random($arr,$count): array
随机返回数组元素列表
任何时候返回的都是数组列表，而不是key或者keys，该方法不会对结果进行混淆
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

### 1.6 array_flatten($arr): array
数组平铺
#### 参数
 - {array} *arr* 

#### 返回值
 - array 

### 1.7 plain_items($arr,$original_key,$original_key_name): array
将多重数组值取出来，平铺成一维数组
#### 参数
 - {array} *arr* 
 - {string} *original_key* 
 - {string} *original_key_name* 

#### 返回值
 - array 

### 1.8 array2object($arr): \stdClass
将数组转换成对象
#### 参数
 - {array} *arr* 

#### 返回值
 - \stdClass 

### 1.9 object2array($obj): array
将对象转换成数组
#### 参数
 - {object} *obj* 

#### 返回值
 - array 

### 1.10 restructure_files($input): array
重新组织PHP $_FILES数组格式
以正确name维度返回数组
#### 参数
 - {array} *input* 

#### 返回值
 - array 

### 1.11 array_copy_by_fields($array,$fields): array
array copy by fields
#### 参数
 - {array} *array* 
 - {array} *fields* 

#### 返回值
 - array 

### 1.12 array_merge_recursive_distinct($array1,$array2): array
检测KEY合并数组，增强array_merge
#### 参数
 - {array} *array1* 
 - {array} *array2* 

#### 返回值
 - array 

### 1.13 array_clear_null($data,$recursive): array|mixed
清理数组中null的元素
#### 参数
 - {array|mixed} *data* 
 - {bool} *recursive* 

#### 返回值
 - array|mixed 

### 1.14 array_clear_empty($data,$recursive): array|mixed
清理数组中empty的元素
#### 参数
 - {array|mixed} *data* 
 - {bool} *recursive* 

#### 返回值
 - array|mixed 

### 1.15 array_move_item($arr,$item_index_key,$dir): array
数组元素切换（支持关联数组）
#### 参数
 - {array} *arr* 数组
 - {string|number} *item_index_key* 需要切换元素的key值（可以是关联数组的key）
 - {number} *dir* 移动方向

#### 返回值
 - array 

### 1.16 array_clear_fields($keep_fields,$data): array
清理数组字段
keep_fields 格式：
array(
'id',
'title',
'url',
'tags',
'categories' => function($data){
if(!empty($data)){
foreach($data as $k=>$cat){
$data[$k] = array_clear_fields(array('id', 'name', 'url'), $cat);
}
}
return $data;
},
'album' => array(
'id',
'cover_image_id',
'cover_image'=>array(
'id',
'title',
'url',
'thumb_url'
),
'url'
),
'liked',
'like_url',
'fav_url',
'thumb_url',
'like_data_url',
'link',
'counter' => array(
'visit_count',
'like_count',
'share_count',
'collect_count'
),
)
#### 参数
 - {array} *keep_fields* 
 - {array} *data* 

#### 返回值
 - array 

### 1.17 array_unset($arr,$values)
根据数组项值，删除数组
#### 参数
 - {array} *arr* 
 - {mixed} *values* 
### 1.18 array_trim_fields($data,$fields,$recursive): array
对数组进行去空白
#### 参数
 - {array} *data* 数据
 - {array} *fields* 指定字段，为空表示所有字段
 - {bool} *recursive* 是否递归处理，如果递归，则data允许为任意维数组

#### 返回值
 - array 

### 1.19 array_last($data,$key): null
获取数组最后一个数据
#### 参数
 - {array} *data* 
 - {null} *key* 

#### 返回值
 - null 

### 1.20 array_shift_assoc($arr): array|bool
获取数组第一个项键值对
#### 参数
 - {array} *arr* 

#### 返回值
 - array|bool [value, key] 键值对，不存在则返回false

### 1.21 array_orderby($src_arr): array
array sort by specified key
#### 参数
 - {array} *src_arr* 

#### 返回值
 - array 

### 1.22 array_orderby_keys($src_arr,$keys,$miss_match_in_head): array
数组按照指定key排序
#### 参数
 - {array} *src_arr* 
 - {string[]} *keys* 键值数组
 - {bool} *miss_match_in_head* 未命中值是否排列在头部

#### 返回值
 - array 

### 1.23 array_index($array,$compare_fn_or_value): bool|int|string
获取数组元素key
#### 参数
 - {array} *array* 
 - {callable|mixed} *compare_fn_or_value* 

#### 返回值
 - bool|int|string 

### 1.24 array_sumby($arr,$key): mixed
根据指定数组下标进行求和
#### 参数
 - {array} *arr* 
 - {string} *key* 

#### 返回值
 - mixed 

### 1.25 array_default($arr,$values,$reset_empty): array
Set default values to array
Usage:
<pre>
$_GET = array_default($_GET, array('page_size'=>10), true);
</pre>
#### 参数
 - {array} *arr* 
 - {array} *values* 
 - {bool} *reset_empty* reset empty value in array

#### 返回值
 - array 

### 1.26 null_in_array($arr): bool
check null in array
matched exp: [], [null], ['',null]
mismatched exp: ['']
#### 参数
 - {array} *arr* 

#### 返回值
 - bool 

### 1.27 array_filter_by_keys($arr,$keys): array
filter array by specified keys
array_filter_by_keys($data, 'key1', 'key2');
#### 参数
 - {array} *arr* 
 - {array} *keys* 

#### 返回值
 - array 

### 1.28 array_make_spreadsheet_columns($column_size): array
创建Excel等电子表格里面的表头序列列表
#### 参数
 - {int} *column_size* 

#### 返回值
 - array 

### 1.29 array_push_by_path($data,$path_str,$value,$glue)
根据xpath，将数据压入数组
#### 参数
 - {array} *data* 目标数组
 - {string} *path_str* 路径表达式，如：企微.企业.正式企业数量
 - {mixed} *value* 项目值
 - {string} *glue* 分隔符
### 1.30 array_fetch_by_path($data,$path_str,$default,$delimiter): mixed
根据路径获取数组中的数据
#### 参数
 - {array} *data* 源数据
 - {string} *path_str* 路径
 - {mixed} *default* 缺省值
 - {string} *delimiter* 分隔符

#### 返回值
 - mixed 

### 1.31 array_get($data,$path_str,$default,$delimiter): mixed
根据路径获取数组中的数据
#### 参数
 - {array} *data* 源数据
 - {string} *path_str* 路径
 - {mixed} *default* 缺省值
 - {string} *delimiter* 分隔符

#### 返回值
 - mixed 

### 1.32 assert_array_has_keys($arr,$keys)
断言数组是否用拥有指定键名
#### 参数
 - {array} *arr* 
 - {array} *keys* 
### 1.33 array_filter_subtree($parent_id,$all,$opt,$level,$group_by_parents): array
过滤子节点，以目录树方式返回
#### 参数
 - {string|int} *parent_id* 
 - {array} *all* 
 - {array} *opt* 
 - {int} *level* 
 - {array} *group_by_parents* 

#### 返回值
 - array 

### 1.34 array_insert_after($src_array,$data,$rel_key): array|int
插入指定数组在指定位置
#### 参数
 - {array} *src_array* 
 - {mixed} *data* 
 - {string} *rel_key* 

#### 返回值
 - array|int 

### 1.35 array_merge_after($src_array,$new_array,$rel_key): array
合并数组到指定位置之后
#### 参数
 - {array} *src_array* 
 - {array} *new_array* 
 - {string} *rel_key* 

#### 返回值
 - array 

### 1.36 is_assoc_array($array): boolean
检测数组是否为关联数组
#### 参数
 - {array} *array* 

#### 返回值
 - boolean 

### 1.37 array_transform($data,$rules): mixed
array_transform 支持嵌套转换
转换为 : array['aaaaa']['bbb'] =  xxxx
#### 参数
 - {array} *data* 
 - {array} *rules* = array('dddd' => array('aaaa', 'bbbb'))

#### 返回值
 - mixed 

### 1.38 array_value_recursive($key,$arr): array|mixed
根据指定下标获取多维数组所有值，无下标时获取所有
#### 参数
 - {string} *key* 
 - {array} *arr* 

#### 返回值
 - array|mixed 



## 2. COLOR
 > 颜色相关操作函数

### 2.1 color_hex2rgb($hex_color): array
转换16进制颜色格式到rbg格式（数组）
#### 参数
 - {string} *hex_color* #ff00bb

#### 返回值
 - array 

### 2.2 color_rgb2hex($rgb,$prefix): string
转换rbg格式到16进制颜色格式
#### 参数
 - {array} *rgb* [r,g,b]
 - {string} *prefix* 前缀

#### 返回值
 - string 

### 2.3 color_rgb2hsl($rgb): float[]
转换RGB格式到HSL格式
#### 参数
 - {array} *rgb* 

#### 返回值
 - float[] [h,s,l]

### 2.4 color_hsl2rgb($hsl): int[]
HSL颜色转换成RGB颜色
#### 参数
 - {array} *hsl* [h,s,l]

#### 返回值
 - int[] [r,g,b]

### 2.5 color_rgb2cmyk($rgb): array
转换rgb颜色到CMYK颜色
#### 参数
 - {array} *rgb* 

#### 返回值
 - array [c,m,y,k]

### 2.6 cmyk_to_rgb($cmyk): int[]
转换CMYK颜色到RGB颜色
#### 参数
 - {array} *cmyk* 

#### 返回值
 - int[] [r,g,b]

### 2.7 color_rgb2hsb($rgb,$accuracy): array
转换RGB到HSB
#### 参数
 - {array} *rgb* [r,g,b]
 - {int} *accuracy* 

#### 返回值
 - array 

### 2.8 color_hsb2rgb($hsb,$accuracy): int[]
转换HSB到RGB
#### 参数
 - {array} *hsb* [h,s,b]
 - {int} *accuracy* 精确度

#### 返回值
 - int[] [r,g,b]

### 2.9 color_molarity($color_val,$inc_pec): array|string
颜色浓度计算
#### 参数
 - {string|array} *color_val* 16进制颜色或rgb数组
 - {float} *inc_pec* 百分比，范围：-99 ~ 99

#### 返回值
 - array|string 

### 2.10 color_rand(): string
随机颜色

#### 返回值
 - string 

### 2.11 _hsl_rgb_low()
### 2.12 _hsl_rgb_high()
### 2.13 _rgb_hsl_delta_rgb()
### 2.14 _rgb_hsl_hue()


## 3. CRON
 > crontab相关操作函数

### 3.1 cron_match($format,$time,$error): bool
检测cron格式是否匹配指定时间戳
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
构建CSP规则
#### 参数
 - {string} *resource* 资源
 - {string} *policy* 策略
 - {string} *custom_defines* 策略扩展数据（主要针对 CSP_POLICY_SCRIPT_NONCE CSP_POLICY_SCRIPT_HASH）

#### 返回值
 - string 

### 4.2 csp_report_uri($uri): string
构建CSP上报规则
#### 参数
 - {string} *uri* 

#### 返回值
 - string 



## 5. CURL
 > CURL网络请求相关操作函数

### 5.1 curl_get($url,$data,$curl_option): array
CURL GET请求
#### 参数
 - {string} *url* 
 - {mixed|null} *data* 
 - {array|null|callable} *curl_option* 额外CURL选项，如果是闭包函数，传入第一个参数为ch

#### 返回值
 - array [head, body, ...] curl_getinfo信息

### 5.2 curl_post($url,$data,$curl_option): array
POST请求
#### 参数
 - {string} *url* 
 - {mixed|null} *data* 
 - {array} *curl_option* 

#### 返回值
 - array 

### 5.3 curl_patch_header($curl_option,$header_name,$header_value): void
CURL HTTP Header追加额外信息，如果原来已经存在，则会被替换
#### 参数
 - {array} *curl_option* 
 - {string} *header_name* 
 - {string} *header_value* 

#### 返回值
 - void 

### 5.4 curl_post_json($url,$data,$curl_option): array
JSON方式POST请求
#### 参数
 - {string} *url* 
 - {mixed} *data* 
 - {array} *curl_option* 

#### 返回值
 - array 

### 5.5 curl_post_file($url,$file_map,$ext_param,$curl_option): array
curl post 提交文件
#### 参数
 - {string} *url* 
 - {array} *file_map* [filename=>filepath,...]
 - {mixed} *ext_param* 同时提交的其他post参数
 - {array} *curl_option* curl选项

#### 返回值
 - array curl_query返回结果，包含 [info=>[], head=>'', body=>''] 信息

### 5.6 curl_put($url,$data,$curl_option): array
PUT请求
#### 参数
 - {string} *url* 
 - {array} *data* 
 - {array} *curl_option* 

#### 返回值
 - array 

### 5.7 curl_delete($url,$data,$curl_option): array
DELETE请求
#### 参数
 - {string} *url* 
 - {array} *data* 
 - {array} *curl_option* 

#### 返回值
 - array 

### 5.8 curl_query($ch): array
执行curl，并关闭curl连接
#### 参数
 - {resource} *ch* 

#### 返回值
 - array [info=>[], head=>'', body=>''] curl_getinfo信息

### 5.9 curl_build_command($url,$body_str,$method,$headers): string
搭建 CURL 命令
#### 参数
 - {string} *url* 
 - {string} *body_str* 
 - {string} *method* 
 - {string[]} *headers* 头部信息，格式为 ['Content-Type: application/json'] 或 ['Content-Type‘=>'application/json']

#### 返回值
 - string 

### 5.10 curl_instance($url,$curl_option): false|resource
获取CURL实例对象
#### 参数
 - {string} *url* 
 - {array} *curl_option* 

#### 返回值
 - false|resource 

### 5.11 curl_data2str($data): string
convert data to request string
#### 参数
 - {mixed} *data* 

#### 返回值
 - string 

### 5.12 curl_print_option($options,$as_return): array|null
打印CURL选项
#### 参数
 - {array} *options* 
 - {bool} *as_return* 

#### 返回值
 - array|null 

### 5.13 http_parse_headers($header_str): array
解析 http头信息
#### 参数
 - {mixed} *header_str* 

#### 返回值
 - array 

### 5.14 curl_option_to_request_header($options): string[]
转换CURL选项到标准HTTP头信息
#### 参数
 - {array} *options* 

#### 返回值
 - string[] array

### 5.15 curl_to_har($curl_options,$curl_info,$response_header,$response_body): void
转换CURL信息到HAR格式文件
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
PDO connect
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
connect database via ssh proxy
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
build DSN
#### 参数
 - {string} *db_type* 
 - {string} *host* 
 - {string} *database* 
 - {string} *port* 
 - {string} *charsets* 

#### 返回值
 - string 

### 6.7 db_query($pdo,$sql): false|\PDOStatement
db query
#### 参数
 - {\PDO} *pdo* 
 - {string} *sql* 

#### 返回值
 - false|\PDOStatement 

### 6.8 db_query_all($pdo,$sql): array
db get all
#### 参数
 - {\PDO} *pdo* 
 - {string} *sql* 

#### 返回值
 - array 

### 6.9 db_query_one($pdo,$sql): array
database query one record
#### 参数
 - {\PDO} *pdo* 
 - {string} *sql* 

#### 返回值
 - array 

### 6.10 db_query_field($pdo,$sql,$field): mixed|null
database query one field
#### 参数
 - {\PDO} *pdo* 
 - {string} *sql* 
 - {string|null} *field* 

#### 返回值
 - mixed|null 

### 6.11 db_sql_patch_limit($sql,$start_offset,$size): string
追加limit语句到sql上
#### 参数
 - {string} *sql* 
 - {int} *start_offset* 
 - {int|null} *size* 

#### 返回值
 - string 

### 6.12 db_query_count($pdo,$sql): int
查询记录数
#### 参数
 - {\PDO} *pdo* 
 - {string} *sql* 

#### 返回值
 - int 

### 6.13 db_query_paginate($pdo,$sql,$page,$page_size): array
分页查询
#### 参数
 - {\PDO} *pdo* 
 - {string} *sql* 
 - {int} *page* 
 - {int} *page_size* 

#### 返回值
 - array [列表, 总数]

### 6.14 db_query_chunk($pdo,$sql,$handler,$chunk_size): bool
分块读取
#### 参数
 - {\PDO} *pdo* 
 - {string} *sql* 
 - {callable} *handler* 批次处理函数，传入参数($rows, $page, $finish)，如返回false，则中断执行
 - {int} *chunk_size* 

#### 返回值
 - bool 是否为正常结束，false表示为批处理函数中断导致

### 6.15 db_watch($pdo,$sql,$watcher,$chunk_size,$sleep_interval): bool
数据监听
#### 参数
 - {\PDO} *pdo* 
 - {string} *sql* 
 - {callable} *watcher* 批次处理函数，传入参数($rows)，如返回false，则中断执行
 - {int} *chunk_size* 分块大小
 - {int} *sleep_interval* 睡眠间隔时间（秒）

#### 返回值
 - bool 是否为正常结束，false表示为批处理函数中断导致

### 6.16 db_quote_value($data): array|string
字段转义，目前仅支持字符串
#### 参数
 - {array|string|int} *data* 

#### 返回值
 - array|string 

### 6.17 db_quote_field($fields): array|string
数据库表字段转义
#### 参数
 - {string|array} *fields* 

#### 返回值
 - array|string 

### 6.18 db_affect_rows($result): int|false
获取查询影响行数
#### 参数
 - {\PDOStatement} *result* 

#### 返回值
 - int|false 

### 6.19 db_insert($pdo,$table,$data): false|int
插入数据
#### 参数
 - {\PDO} *pdo* 
 - {string} *table* 
 - {array} *data* 

#### 返回值
 - false|int 

### 6.20 db_transaction($pdo,$handler): bool|mixed
事务处理
#### 参数
 - {\PDO} *pdo* 
 - {callable} *handler* 处理器，如果返回false或抛出异常，将中断提交，执行回滚操作

#### 返回值
 - bool|mixed 



## 7. ENV
 > 平台函数相关操作函数

### 7.1 server_in_windows(): bool
检测服务器是否在视窗系统中运行

#### 返回值
 - bool 

### 7.2 server_in_https(): bool
检测服务器是否在HTTPS协议中运行

#### 返回值
 - bool 

### 7.3 get_upload_max_size($human_readable): string|number
获取PHP允许上传的最大文件尺寸
依赖：最大上传文件尺寸，最大POST尺寸
#### 参数
 - {bool} *human_readable* 是否以可读方式返回

#### 返回值
 - string|number 

### 7.4 get_max_socket_timeout($ttf): int
获取最大socket可用超时时间
#### 参数
 - {int} *ttf* 允许提前时长

#### 返回值
 - int 超时时间（秒），如为0，表示不限制超时时间

### 7.5 get_client_ip(): string
获取客户端IP
优先获取定义的 x-forward-for 代理IP（可能有一定风险）

#### 返回值
 - string 客户端IP，获取失败返回空字符串

### 7.6 get_all_opt(): array
获取所有命令行选项，格式规则与 getopt 一致

#### 返回值
 - array 

### 7.7 get_php_info(): array
获取PHP配置信息

#### 返回值
 - array 

### 7.8 console_color($text,$fore_color,$back_color): string
get console text colorize
#### 参数
 - {string} *text* 
 - {null} *fore_color* 
 - {null} *back_color* 

#### 返回值
 - string 

### 7.9 show_progress($index,$total,$patch_text,$start_time)
show progress in console
#### 参数
 - {int} *index* 
 - {int} *total* 
 - {string} *patch_text* 补充显示文本
 - {int} *start_time* 开始时间戳
### 7.10 run_command($command,$param,$async): bool|string|null
运行终端命令
#### 参数
 - {string} *command* 命令
 - {array} *param* 参数
 - {bool} *async* 是否以异步方式执行

#### 返回值
 - bool|string|null 

### 7.11 run_command_parallel_width_progress($command,$param_batches,$options): bool
以携带进度文本方式，并发运行命令。
调用参数请参考函数：run_command_parallel()
#### 参数
 - {string} *command* 
 - {array} *param_batches* 
 - {array} *options* 

#### 返回值
 - bool 

### 7.12 run_command_parallel($command,$param_batches,$options): bool
并发运行命令
- callable|null $on_start($param, $param_index, $start_time) 返回false中断执行
- callable|null $on_running($param, $param_index) 返回false中断执行
- callable|null $on_finish($param, $param_index, $output, $cost_time, $status_code, $error) 返回false中断执行
- int $parallel_num 并发数量，缺省为20
- int $check_interval 状态检测间隔（单位：毫秒），缺省为100ms
- int $process_max_execution_time 进程最大执行时间（单位：毫秒），缺省为不设置
#### 参数
 - {string} *command* 执行命令
 - {array} *param_batches* 任务参数列表，参数按照长参数方式传入command，具体实现可参考：build_command() 函数实现。
 - {array} *options* 参数如下：

#### 返回值
 - bool 是否正常结束

### 7.13 build_command($cmd_line,$param): string
构建命令行
#### 参数
 - {string} *cmd_line* 
 - {array} *param* 

#### 返回值
 - string 

### 7.14 escape_win32_argv($value): string
转义window下argv参数
#### 参数
 - {string|int} *value* 

#### 返回值
 - string 

### 7.15 escape_win32_cmd($value): string|string[]|null
Escape cmd.exe metacharacters with ^
#### 参数
 - {mixed} *value* 

#### 返回值
 - string|string[]|null 

### 7.16 noshell_exec($command): false|string
Like shell_exec() but bypass cmd.exe
#### 参数
 - {string} *command* 

#### 返回值
 - false|string 

### 7.17 command_exists($command): bool
检查命令是否存在
#### 参数
 - {string} *command* 

#### 返回值
 - bool 

### 7.18 windows_get_port_usage($include_process_info): array
获取Windows进程网络占用情况
#### 参数
 - {bool} *include_process_info* 是否包含进程信息（标题、程序文件名），该功能需要Windows管理员模式

#### 返回值
 - array 格式:[protocol='', local_ip='', local_port='', foreign_ip='', foreign_port='', state='', pid='', 'process_name'='', 'process_file_id'=>'']

### 7.19 unix_get_port_usage(): array
获取Linux下端口占用情况

#### 返回值
 - array 格式:[protocol='', local_ip='', local_port='', foreign_ip='', foreign_port='', state='', pid='', 'process_name'='', 'process_file_id'=>'']

### 7.20 get_screen_size(): array|null
获取控制台屏幕宽度及高度

#### 返回值
 - array|null 返回格式：[列数，行数】，当前环境不支持则返回 null

### 7.21 process_kill($pid): bool
杀死进程
#### 参数
 - {int} *pid* 进程ID

#### 返回值
 - bool 

### 7.22 process_running($pid): bool
检测指定进程是否运行中
#### 参数
 - {int} *pid* 进程ID

#### 返回值
 - bool 

### 7.23 process_signal($signal,$handle): bool
进程信号监听
#### 参数
 - {mixed} *signal* 
 - {mixed} *handle* 

#### 返回值
 - bool 

### 7.24 process_send_signal($pid,$sig_num): bool
发送进程信号量
#### 参数
 - {int} *pid* 进程ID
 - {int} *sig_num* 信号量

#### 返回值
 - bool 

### 7.25 replay_current_script(): false|int
重播当前脚本命令

#### 返回值
 - false|int 返回新开启的进程ID，false 为失败返回



## 8. EVENT
 > 自定义事件函数



## 9. FILE
 > 文件相关操作函数

### 9.1 glob_recursive($pattern,$flags): array
递归的glob
Does not support flag GLOB_BRACE
#### 参数
 - {string} *pattern* 
 - {int} *flags* 

#### 返回值
 - array 

### 9.2 file_exists_case_sensitive($file): bool|null
检查文件是否存在，且名称严格匹配大小写
true：文件存在，false：文件不存在，null：文件存在但大小写不一致
#### 参数
 - {string} *file* 

#### 返回值
 - bool|null 

### 9.3 assert_file_in_dir($file,$dir,$exception_class)
断言文件包含于指定文件夹中（文件必须存在）
#### 参数
 - {string} *file* 
 - {string} *dir* 
 - {string} *exception_class* 
### 9.4 file_in_dir($file_path,$dir_path): bool
判断文件是否包含于指定文件夹中
#### 参数
 - {string} *file_path* 文件路径
 - {string} *dir_path* 目录路径

#### 返回值
 - bool 文件不存在目录当中，或文件实际不存在

### 9.5 resolve_absolute_path($file_or_path): string
解析路径字符串真实路径，去除相对路径信息
相对于realpath，该函数不需要检查文件是否存在
<pre>
调用格式：resolve_absolute_path("c:/a/b/./../../windows/system32");
返回：c:/windows/system32
#### 参数
 - {string} *file_or_path* 目录路径或文件路径字符串

#### 返回值
 - string 

### 9.6 resolve_file_extension($filename,$to_lower_case): string|null
根据文件名获取文件扩展
#### 参数
 - {string} *filename* 文件名
 - {bool} *to_lower_case* 是否转换成小写，缺省为转换为小写

#### 返回值
 - string|null string or null,no extension detected

### 9.7 file_exists_case_insensitive($file,$parent): bool
检查文件是否存在，且名称允许大小写混淆
#### 参数
 - {string} *file* 
 - {null} *parent* 

#### 返回值
 - bool 

### 9.8 copy_recursive($src,$dst)
递归拷贝目录
#### 参数
 - {string} *src* 
 - {string} *dst* 
### 9.9 mkdir_batch($dirs,$break_on_error,$permissions): string[]
批量创建目录
#### 参数
 - {string[]} *dirs* 目录路径列表
 - {bool} *break_on_error* 是否在创建失败时抛出异常
 - {int} *permissions* 目录缺省权限

#### 返回值
 - string[] 创建失败的目录清单，成功则返回空数组

### 9.10 get_dirs($dir): array
获取模块文件夹列表
#### 参数
 - {string} *dir* 

#### 返回值
 - array 

### 9.11 file_lines($file,$line_separator): int
获取文件行数
#### 参数
 - {string|resource} *file* 文件路径或文件句柄
 - {string} *line_separator* 换行符

#### 返回值
 - int 

### 9.12 tail($file,$lines,$buffer): string[]
文件tail功能
#### 参数
 - {string|resource} *file* 
 - {int} *lines* 读取行数
 - {int} *buffer* 缓冲大小

#### 返回值
 - string[] 每一行内容

### 9.13 file_read_by_line($file,$handle,$start_line,$buff_size): bool
逐行读取文件
#### 参数
 - {string} *file* 文件名称
 - {callable} *handle* 处理函数，传入参数：($line_str, $line), 若函数返回false，则中断处理
 - {int} *start_line* 开始读取行数（由 1 开始）
 - {int} *buff_size* 缓冲区大小

#### 返回值
 - bool 是否为处理函数中断返回

### 9.14 render_php_file($php_file,$vars): false|string
渲染PHP文件
#### 参数
 - {mixed} *php_file* 
 - {array} *vars* 

#### 返回值
 - false|string 

### 9.15 get_folder_size($path): int
递归查询文件夹大小
#### 参数
 - {string} *path* 

#### 返回值
 - int 

### 9.16 log($file,$content,$max_size,$max_files,$pad_str): bool|int
log 记录到文件
#### 参数
 - {string} *file* 文件
 - {mixed} *content* 记录内容
 - {float|int} *max_size* 单文件最大尺寸，默认
 - {int} *max_files* 最大记录文件数
 - {string|null} *pad_str* 记录文件名追加字符串

#### 返回值
 - bool|int 文件是否记录成功

### 9.17 read_file_lock($key): false|string|null
读取文件锁
#### 参数
 - {string} *key* 

#### 返回值
 - false|string|null 

### 9.18 write_file_lock($key,$lock_flag): string
写入文件锁
#### 参数
 - {string} *key* 
 - {string} *lock_flag* 

#### 返回值
 - string 

### 9.19 remove_file_lock($key): bool
remove file lock
#### 参数
 - {string} *key* 

#### 返回值
 - bool 

### 9.20 init_file_lock($key,$is_new): resource
初始化文件锁
#### 参数
 - {string} *key* 
 - {bool} *is_new* 

#### 返回值
 - resource 锁文件操作句柄

### 9.21 log_tmp_file($filename,$content,$max_size,$max_files,$pad_str): bool|int
Log in temporary directory
if high performance required, support to use logrotate programme to process your log file
#### 参数
 - {string} *filename* 
 - {mixed} *content* 
 - {float|int} *max_size* 
 - {int} *max_files* 
 - {string|null} *pad_str* 

#### 返回值
 - bool|int 

### 9.22 create_tmp_file($dir,$prefix,$ext,$mod): string
创建临时文件
#### 参数
 - {string} *dir* 文件所在目录
 - {string} *prefix* 文件名前缀
 - {string} *ext* 文件名后缀
 - {numeric} *mod* 权限，缺省为777

#### 返回值
 - string 

### 9.23 upload_file_check($file,$opt): void
#### 参数
 - {string} *file* 文件
 - {array} *opt* 控制选项

#### 返回值
 - void 

### 9.24 get_extensions_by_mime($mime): string[]
获取匹配指定mime的扩展名列表
#### 参数
 - {string} *mime* 

#### 返回值
 - string[] 

### 9.25 get_mimes_by_extension($ext): string[]
通过文件后缀获取mime信息
#### 参数
 - {string} *ext* 文件后缀

#### 返回值
 - string[] mime 列表

### 9.26 mime_match_extensions($mime,$extensions): bool
检查给定mime信息，是否在指定扩展名清单中
该方法通常用于检查上传文件是否符合设定文件类型
#### 参数
 - {string} *mime* 
 - {string[]} *extensions* 

#### 返回值
 - bool 

### 9.27 mime_match_accept($mime,$accept): bool
检测文件mime信息是否匹配accept字符串
#### 参数
 - {string} *mime* 文件mime信息
 - {string} *accept* <input accept=""/> 信息，格式请参考：https://developer.mozilla.org/en-US/docs/Web/HTML/Attributes/accept

#### 返回值
 - bool 

### 9.28 file_match_accept($file_name,$accept): bool
检测文件是否匹配指定accept定义
#### 参数
 - {string} *file_name* 文件路径
 - {string} *accept* <input accept=""/> 信息，格式请参考：https://developer.mozilla.org/en-US/docs/Web/HTML/Attributes/accept

#### 返回值
 - bool 



## 10. FONT
 > 字体类相关操作函数

### 10.1 ttf_info()
### 10.2 get_windows_fonts(): string[]
获取Windows系统字体列表（以文件方式，不一定准确）

#### 返回值
 - string[] 

### 10.3 _ttf_dec2ord()
### 10.4 _ttf_dec2hex()


## 11. HTML
 > Html 快速操作函数

### 11.1 html_tag_select($name,$options,$current_value,$placeholder,$attributes): string
构建select节点，支持optgroup模式
如果是分组模式，格式为：[value=>text, label=>options, ...]
如果是普通模式，格式为：options: [value1=>text, value2=>text,...]
#### 参数
 - {string} *name* 
 - {array} *options* 选项数据，
 - {string|array} *current_value* 
 - {string} *placeholder* 
 - {array} *attributes* 

#### 返回值
 - string 

### 11.2 html_tag_options($options,$current_value): string
构建select选项
#### 参数
 - {array} *options* [value=>text,...] option data 选项数组
 - {string|array} *current_value* 当前值

#### 返回值
 - string 

### 11.3 html_tag_option($text,$value,$selected,$attributes): string
构建option节点
#### 参数
 - {string} *text* 文本，空白将被转义成&nbsp;
 - {string} *value* 
 - {bool} *selected* 
 - {array} *attributes* 

#### 返回值
 - string 

### 11.4 html_tag_option_group($label,$options,$current_value): string
构建optgroup节点
#### 参数
 - {string} *label* 
 - {array} *options* 
 - {string|array} *current_value* 当前值

#### 返回值
 - string 

### 11.5 html_tag_textarea($name,$value,$attributes): string
构建textarea
#### 参数
 - {string} *name* 
 - {string} *value* 
 - {array} *attributes* 

#### 返回值
 - string 

### 11.6 html_tag_hidden($name,$value): string
构建hidden表单节点
#### 参数
 - {string} *name* 
 - {string} *value* 

#### 返回值
 - string 

### 11.7 html_tag_hidden_list($data_list): string
构建数据hidden列表
#### 参数
 - {array} *data_list* 数据列表（可以多维数组）

#### 返回值
 - string 

### 11.8 html_tag_number_input($name,$value,$attributes): string
构建Html数字输入
#### 参数
 - {string} *name* 
 - {string} *value* 
 - {array} *attributes* 

#### 返回值
 - string 

### 11.9 html_tag_radio_group($name,$options,$current_value,$wrapper_tag,$radio_extra_attributes): string
#### 参数
 - {string} *name* 
 - {array} *options* 选项[value=>title,...]格式
 - {string} *current_value* 
 - {string} *wrapper_tag* 每个选项外部包裹标签，例如li、div等
 - {array} *radio_extra_attributes* 每个radio额外定制属性

#### 返回值
 - string 

### 11.10 html_tag_radio($name,$value,$title,$checked,$attributes): string
构建 radio按钮
使用 label>(input:radio+{text}) 结构
#### 参数
 - {string} *name* 
 - {mixed} *value* 
 - {string} *title* 
 - {bool} *checked* 
 - {array} *attributes* 

#### 返回值
 - string 

### 11.11 html_tag_checkbox_group($name,$options,$current_value,$wrapper_tag,$checkbox_extra_attributes): string
#### 参数
 - {string} *name* 
 - {array} *options* 选项[value=>title,...]格式
 - {string|array} *current_value* 
 - {string} *wrapper_tag* 每个选项外部包裹标签，例如li、div等
 - {array} *checkbox_extra_attributes* 每个checkbox额外定制属性

#### 返回值
 - string 

### 11.12 html_tag_checkbox($name,$value,$title,$checked,$attributes): string
构建 checkbox按钮
使用 label>(input:checkbox+{text}) 结构
#### 参数
 - {string} *name* 
 - {mixed} *value* 
 - {string} *title* 
 - {bool} *checked* 
 - {array} *attributes* 

#### 返回值
 - string 

### 11.13 html_tag_progress($value,$max,$attributes): string
构建进度条（如果没有设置value，可充当loading效果使用）
#### 参数
 - {null|number} *value* 
 - {null|number} *max* 
 - {array} *attributes* 

#### 返回值
 - string 

### 11.14 html_tag_img($src,$attributes): string
HTML <img> 标签
#### 参数
 - {mixed} *src* 
 - {mixed} *attributes* 

#### 返回值
 - string 

### 11.15 html_loading_bar($attributes): string
Html循环滚动进度条
alias to htmlProgress
#### 参数
 - {array} *attributes* 

#### 返回值
 - string 

### 11.16 html_tag_range($name,$value,$min,$max,$step,$attributes): string
Html范围选择器
#### 参数
 - {string} *name* 
 - {string} *value* 当前值
 - {int} *min* 最小值
 - {int} *max* 最大值
 - {int} *step* 步长
 - {array} *attributes* 

#### 返回值
 - string 

### 11.17 html_abstract($html_content,$len): string
获取HTML摘要信息
#### 参数
 - {string} *html_content* 
 - {int} *len* 

#### 返回值
 - string 

### 11.18 html_tag_input_text($name,$value,$attributes): string
构建Html input:text文本输入框
#### 参数
 - {string} *name* 
 - {string} *value* 
 - {array} *attributes* 

#### 返回值
 - string 

### 11.19 html_tag_date($name,$date_or_timestamp,$attributes): string
构建Html日期输入框
#### 参数
 - {string} *name* 
 - {string} *date_or_timestamp* 
 - {array} *attributes* 

#### 返回值
 - string 

### 11.20 html_tag_time($name,$time_str,$attributes): string
构建Html日期输入框
#### 参数
 - {string} *name* 
 - {string} *time_str* 
 - {array} *attributes* 

#### 返回值
 - string 

### 11.21 html_tag_datetime($name,$datetime_or_timestamp,$attributes): string
构建Html日期+时间输入框
#### 参数
 - {string} *name* 
 - {string} *datetime_or_timestamp* 
 - {array} *attributes* 

#### 返回值
 - string 

### 11.22 html_tag_month_select($name,$current_month,$format,$attributes): string
构建Html月份选择器
#### 参数
 - {string} *name* 
 - {int|null} *current_month* 当前月份，范围1~12表示
 - {string} *format* 月份格式，与date函数接受格式一致
 - {array} *attributes* 属性

#### 返回值
 - string 

### 11.23 html_tag_year_select($name,$current_year,$start_year,$end_year,$attributes): string
构建Html年份选择器
#### 参数
 - {string} *name* 
 - {int|null} *current_year* 当前年份
 - {int} *start_year* 开始年份（缺省为1970）
 - {string} *end_year* 结束年份（缺省为今年）
 - {array} *attributes* 

#### 返回值
 - string 

### 11.24 html_tag($tag,$attributes,$inner_html): string
构建HTML节点
#### 参数
 - {string} *tag* 
 - {array} *attributes* 
 - {string} *inner_html* 

#### 返回值
 - string 

### 11.25 html_tag_link($inner_html,$href,$attributes): string
构建HTML链接
#### 参数
 - {string} *inner_html* 
 - {string} *href* 
 - {array} *attributes* 

#### 返回值
 - string 

### 11.26 html_tag_css()
### 11.27 html_tag_js()
### 11.28 html_tag_date_input($name,$value,$attributes): string
构建Html日期输入
#### 参数
 - {string} *name* 
 - {string} *value* 
 - {array} *attributes* 

#### 返回值
 - string 

### 11.29 html_tag_date_time_input($name,$value,$attributes): string
构建Html时间输入
#### 参数
 - {string} *name* 
 - {string} *value* 
 - {array} *attributes* 

#### 返回值
 - string 

### 11.30 html_tag_data_list($id,$data_map): string
构建DataList
#### 参数
 - {string} *id* 
 - {array} *data_map* 索引数组：[val=>title,...]，或者自然增长数组：[title1, title2,...]

#### 返回值
 - string 

### 11.31 html_tag_input_submit($value,$attributes): string
submit input
#### 参数
 - {mixed} *value* 
 - {array} *attributes* 

#### 返回值
 - string 

### 11.32 html_tag_no_script($html): string
no script support html
#### 参数
 - {string} *html* 

#### 返回值
 - string 

### 11.33 html_tag_button_submit($inner_html,$attributes): string
submit button
#### 参数
 - {string} *inner_html* 
 - {array} *attributes* 

#### 返回值
 - string 

### 11.34 html_tag_table($data,$headers,$caption,$attributes): string
构建table节点
#### 参数
 - {array} *data* 
 - {array|false} *headers* 表头列表 [字段名 => 别名, ...]，如为false，表示不显示表头
 - {string} *caption* 
 - {array} *attributes* 

#### 返回值
 - string 

### 11.35 html_attributes($attributes): string
构建HTML节点属性
修正pattern，disabled在false情况下HTML表现
#### 参数
 - {array} *attributes* 

#### 返回值
 - string 

### 11.36 text_to_html($text,$len,$tail,$over_length): string
转化明文文本到HTML
#### 参数
 - {string} *text* 
 - {null} *len* 
 - {string} *tail* 
 - {bool} *over_length* 

#### 返回值
 - string 

### 11.37 html_text_highlight($text,$keyword,$template): string
高亮文本
#### 参数
 - {string} *text* 
 - {string} *keyword* 
 - {string} *template* 

#### 返回值
 - string 返回HTML转义过的字符串

### 11.38 html_tag_meta($equiv,$content): string
构建HTML meta标签
#### 参数
 - {string} *equiv* 
 - {string} *content* 

#### 返回值
 - string 

### 11.39 html_meta_redirect($url,$timeout_sec): string
使用 html meta 进行页面跳转
#### 参数
 - {string} *url* 跳转目标路径
 - {int} *timeout_sec* 超时时间

#### 返回值
 - string html

### 11.40 html_meta_csp($csp_rules,$report_uri,$report_only): string
构建 CSP meta标签
#### 参数
 - {array} *csp_rules* 
 - {string} *report_uri* 
 - {bool} *report_only* 

#### 返回值
 - string 

### 11.41 html_value_compare($str1,$data): bool
HTML数值比较（通过转换成字符串之后进行严格比较）
#### 参数
 - {string|number} *str1* 
 - {string|number|array} *data* 

#### 返回值
 - bool 是否相等

### 11.42 static_version_set($patch_config): array
设置静态资源版本控制项
#### 参数
 - {array} *patch_config* 版本配置表，格式如：abc/foo.js => '2020'，优先匹配长度短的规则

#### 返回值
 - array 所有配置

### 11.43 static_version_patch($src,$matched): string
静态资源版本补丁
#### 参数
 - {string} *src* 
 - {bool} *matched* 

#### 返回值
 - string 

### 11.44 static_version_statement_quote($str): string
静态资源版本通配符转义
#### 参数
 - {string} *str* 

#### 返回值
 - string 

### 11.45 fix_browser_datetime($datetime_str_from_h5): string|null
修正浏览器 HTML5 中 input:datetime或者 input:datetime-local 提交过来的数据
#### 参数
 - {string} *datetime_str_from_h5* 

#### 返回值
 - string|null 



## 12. HTTP
 > HTTP 快速操作函数

### 12.1 http_send_status($status): bool
发送HTTP状态码
#### 参数
 - {int} *status* http 状态码

#### 返回值
 - bool 

### 12.2 request_in_post(): bool
请求来自POST

#### 返回值
 - bool 

### 12.3 request_in_get(): bool
请求来自于GET

#### 返回值
 - bool 

### 12.4 http_send_cors($allow_hosts,$http_origin)
返回跨域CORS头信息
#### 参数
 - {string[]} *allow_hosts* 允许通过的域名列表，为空表示允许所有来源域名
 - {string} *http_origin* 来源请求，格式为：http://www.abc.com，缺省从 HTTP_ORIGIN 或 HTTP_REFERER获取
### 12.5 http_send_charset($charset): bool
发送 HTTP 头部字符集
#### 参数
 - {string} *charset* 

#### 返回值
 - bool 是否成功

### 12.6 http_get_status_message($status): string|null
获取HTTP状态码对应描述
#### 参数
 - {int} *status* 

#### 返回值
 - string|null 

### 12.7 http_redirect($url,$permanently)
HTTP方式跳转
#### 参数
 - {string} *url* 跳转路径
 - {bool} *permanently* 是否为长期资源重定向
### 12.8 http_get_request_headers(): array
获取HTTP请求头信息数组

#### 返回值
 - array [key=>val]

### 12.9 http_get_request_header($key): mixed|null
获取HTTP请求头中指定key值
#### 参数
 - {string} *key* 不区分大小写

#### 返回值
 - mixed|null 

### 12.10 http_from_json_request(): bool
判断请求方式是否为 JSON 方式

#### 返回值
 - bool 

### 12.11 http_request_accept_json(): bool
判断请求接受格式是否为 JSON

#### 返回值
 - bool 

### 12.12 http_get_current_page_url($with_protocol): string
获取当前页面地址
#### 参数
 - {bool} *with_protocol* 是否包含协议头

#### 返回值
 - string 

### 12.13 http_download_stream($file,$download_name,$disposition): false|int
文件流方式下载文件
#### 参数
 - {string} *file* 文件路径
 - {string} *download_name* 下载文件名
 - {string} *disposition* 头类型

#### 返回值
 - false|int 成功下载文件尺寸，false为失败

### 12.14 http_header_json_response($charset)
响应JSON返回头
#### 参数
 - {string} *charset* 
### 12.15 http_json_response($json,$json_option): void
响应json数据
#### 参数
 - {mixed} *json* 
 - {int} *json_option* 

#### 返回值
 - void 

### 12.16 http_header_download($download_name,$disposition)
发送文件下载头信息
#### 参数
 - {string} *download_name* 
 - {string} *disposition* 
### 12.17 http_header_csp($csp_rules,$report_uri,$report_only)
发送CSP头
#### 参数
 - {string[]} *csp_rules* 建议使用csp_content_rule()方法产生的规则
 - {string} *report_uri* 
 - {bool} *report_only* 
### 12.18 generate_report_api($endpoint_urls,$group,$max_age_sec,$include_subdomains): array
生成 Report API
#### 参数
 - {string[]} *endpoint_urls* 
 - {string} *group* 
 - {number} *max_age_sec* 
 - {bool} *include_subdomains* 

#### 返回值
 - array 

### 12.19 http_header_report_api($endpoint_urls,$group,$max_age_sec,$include_subdomains)
发送浏览器设置 Report API
#### 参数
 - {string[]} *endpoint_urls* 
 - {string} *group* 
 - {number} *max_age_sec* 
 - {bool} *include_subdomains* 
### 12.20 http_header_report_api_nel($endpoint_urls,$group,$max_age_sec,$include_subdomains): void
发送浏览器错误日志上报 Report API
#### 参数
 - {string[]} *endpoint_urls* 
 - {string} *group* 
 - {number} *max_age_sec* 
 - {bool} *include_subdomains* 

#### 返回值
 - void 



## 13. SESSION
 > session 相关操作函数

### 13.1 session_start_once(): bool
开启session一次
如原session状态未开启，则读取完session自动关闭，避免session锁定

#### 返回值
 - bool 

### 13.2 session_write_once()
立即提交session数据，同时根据上下文环境，选择性关闭session
### 13.3 session_write_scope($handler): bool
自动判断当前session状态，将$_SESSION写入数据到session中
如原session状态时未开启，则写入操作完毕自动关闭session避免session锁定，否则保持不变
调用方法：
session_write_scope(function(){
$_SESSION['hello'] = 'world';
unset($_SESSION['info']);
});
#### 参数
 - {callable} *handler* 

#### 返回值
 - bool 

### 13.4 session_start_in_time($expire_seconds): void
以指定时间启动session
#### 参数
 - {int} *expire_seconds* 秒

#### 返回值
 - void 



## 14. SHEET
 > CSV、电子表格相关操作函数
 > 如果正常开放性业务，建议使用 XLSXBuilder (https://github.com/sasumi/XLSXBuilder)
 > 或类似处理excel的其他技术方案。

### 14.1 get_spreadsheet_column($column): string
获取Excel等电子表格中列名
#### 参数
 - {integer} *column* 列序号，由1开始

#### 返回值
 - string 电子表格中的列名，格式如：A1、E3

### 14.2 download_csv($download_name,$data,$fields,$mime_type)
输出CSV文件到浏览器下载
#### 参数
 - {string} *download_name* 下载文件名
 - {array} *data* 
 - {array} *fields* 字段列表，格式为：[field=>alias,...]
 - {string} *mime_type* 
### 14.3 download_csv_chunk($download_name,$batch_fetcher,$fields,$mime_type)
分块输出CSV文件到浏览器下载
#### 参数
 - {string} *download_name* 下载文件名
 - {callable} *batch_fetcher* 
 - {array} *fields* 字段列表，格式为：[field=>alias,...]
 - {string} *mime_type* 
### 14.4 read_csv($file,$keys,$ignore_head_lines): array
CSV 读取
#### 参数
 - {string} *file* 文件路径
 - {array} *keys* 
 - {int} *ignore_head_lines* 

#### 返回值
 - array 数据，格式为：[[字段1,字段2,...],...]

### 14.5 read_csv_chunk($output,$file,$fields,$chunk_size,$ignore_head_lines)
分块读取CSV文件
#### 参数
 - {callable} *output* 数据输出处理函数，传入参数：chunks， 返回参数若为false，则中断读取
 - {string} *file* 文件名称
 - {array} *fields* 字段列表，格式为：[field=>alias,...] 映射字段名
 - {int} *chunk_size* 分块大小
 - {int} *ignore_head_lines* 忽略开始头部标题行数
### 14.6 save_csv($file,$data,$field_map)
保存CSV文件
#### 参数
 - {string} *file* 文件路径
 - {array} *data* 
 - {array} *field_map* 字段别名映射列表，格式为：[field=>alias,...]
### 14.7 save_csv_chunk($file,$data_fetcher,$field_map)
分块保存CSV文件
#### 参数
 - {string} *file* 文件路径
 - {callable} *data_fetcher* 
 - {array} *field_map* 字段别名映射列表，格式为：[field=>alias,...]
### 14.8 csv_output_chunk($output,$batch_fetcher,$fields,$uniq_seed): int
分块输出CSV数据
#### 参数
 - {callable} *output* 
 - {callable} *batch_fetcher* 
 - {array} *fields* 字段列表，格式为：[field=>alias,...]
 - {int} *uniq_seed* 

#### 返回值
 - int 数据行数

### 14.9 csv_output($output,$data,$fields): bool|int
输出CSV
#### 参数
 - {callable} *output* 
 - {array} *data* 二维数组
 - {array} *fields* 字段列表，格式为：[field=>alias,...]

#### 返回值
 - bool|int 

### 14.10 format_csv_ceil($val): string|array
格式化CSV单元格内容
#### 参数
 - {mixed} *val* 

#### 返回值
 - string|array 



## 15. STRING
 > 字符串相关操作函数

### 15.1 substr_utf8($string,$length,$tail,$over_length): string
utf-8中英文截断（两个英文一个数量单位）
#### 参数
 - {string} *string* 串
 - {int} *length* 切割长度
 - {string} *tail* 尾部追加字符串
 - {bool} *over_length* 是否超长

#### 返回值
 - string 

### 15.2 is_json($str): bool
检测字符串是否为JSON
#### 参数
 - {mixed} *str* 

#### 返回值
 - bool 

### 15.3 explode_by($delimiters,$str,$trim_and_clear): array
按照指定边界字符列表，拆分字符串
#### 参数
 - {array|string} *delimiters* eg: [',', '-'] or ",-"
 - {string} *str* 
 - {bool} *trim_and_clear* 去除空白及空值

#### 返回值
 - array 

### 15.4 get_namespace($class): string
获取指定 class 名称中的命名空间部分
#### 参数
 - {mixed} *class* 

#### 返回值
 - string 

### 15.5 get_class_without_namespace($class): string
获取指定 class 中类名部分
#### 参数
 - {string} *class* 

#### 返回值
 - string 

### 15.6 parse_str_without_limitation($string,$extra_to_post): array
突破 max_input_vars 限制，通过解析字符串方式获取变量
#### 参数
 - {string} *string* 
 - {bool} *extra_to_post* 

#### 返回值
 - array 

### 15.7 __array_merge_distinct_with_dynamic_key($array1,$array2,$dynamicKey): array
merge data
#### 参数
 - {array} *array1* 
 - {array} *array2* 
 - {string} *dynamicKey* 

#### 返回值
 - array 

### 15.8 match_wildcard($wildcard_pattern,$haystack): boolean
PHP 匹配通配符
#### 参数
 - {string} *wildcard_pattern* 
 - {string} *haystack* 

#### 返回值
 - boolean 

### 15.9 str_split_by_charset($str,$len,$charset): array
按照指定字符编码拆分字符串
#### 参数
 - {string} *str* 
 - {int} *len* 
 - {string} *charset* 

#### 返回值
 - array 

### 15.10 str_start_with($str,$starts,$case_sensitive): bool
检测字符串是否以另一个字符串开始
#### 参数
 - {string} *str* 待检测字符串
 - {string|array} *starts* 匹配字符串或字符串数组
 - {bool} *case_sensitive* 是否大小写敏感

#### 返回值
 - bool 

### 15.11 int2str($data): array|string
转换整型（整型数组）到字符串（字符串数组）
#### 参数
 - {mixed} *data* 

#### 返回值
 - array|string 

### 15.12 calc_formula($stm,$param,$result_decorator): array
公式运算
#### 参数
 - {string} *stm* 表达式，变量以$符号开始，小括号中表示该变量的描述文本（可为空）,结构如：$var1(变量1)
 - {array} *param* 传入变量，[key=>val]结构
 - {callable|null} *result_decorator* 计算结果修饰回调（仅影响计算过程中的结果，不影响真实计算结果）

#### 返回值
 - array [计算结果, 计算公式， 计算过程]

### 15.13 h($str,$len,$tail,$over_length): string|array
输出html变量
#### 参数
 - {array|string} *str* 
 - {number|null} *len* 截断长度，为空表示不截断
 - {null|string} *tail* 追加尾串字符
 - {bool} *over_length* 超长长度

#### 返回值
 - string|array 

### 15.14 ha($str,$len,$tail,$over_length): string|array
输出html节点属性变量
#### 参数
 - {array|string} *str* 
 - {number|null} *len* 截断长度，为空表示不截断
 - {null|string} *tail* 追加尾串字符
 - {bool} *over_length* 超长长度

#### 返回值
 - string|array 

### 15.15 __h($str,$len,$tail,$over_length,$flags): array|string
#### 参数
 - {string} *str* 
 - {null} *len* 
 - {string} *tail* 
 - {bool} *over_length* 
 - {null} *flags* ENT_QUOTES|ENT_SUBSTITUTE

#### 返回值
 - array|string 

### 15.16 xml_special_chars($val): string
XML字符转义
#### 参数
 - {string} *val* 

#### 返回值
 - string 

### 15.17 remove_utf8_bom($text): string
移除 UTF-8 BOM头
#### 参数
 - {string} *text* 

#### 返回值
 - string 

### 15.18 get_traditional_currency($num): string
数字金额转换成中文大写金额的函数
#### 参数
 - {int} *num* 要转换的小写数字或小写字符串（单位：元）

#### 返回值
 - string 

### 15.19 password_check($password,$rules)
密码检测
#### 参数
 - {string} *password* 
 - {array} *rules* 
### 15.20 str_contains($str,$char_list): bool
检测字符串中是否包含指定字符集
#### 参数
 - {string} *str* 
 - {string} *char_list* 

#### 返回值
 - bool 

### 15.21 rand_string($len,$source): string
随机字符串
#### 参数
 - {int} *len* 长度
 - {string} *source* 字符源

#### 返回值
 - string 

### 15.22 format_size($size,$dot): string
格式化大小
#### 参数
 - {int} *size* 比特值
 - {int} *dot* 预留小数点位数

#### 返回值
 - string 

### 15.23 resolve_size($val): int
解析文件实际大小表达式
#### 参数
 - {string} *val* 文件大小，如 12.3m, 43k

#### 返回值
 - int 

### 15.24 str_mixing($text,$param): string
文字混淆
#### 参数
 - {string} *text* 文字模板，占位符采用 {VAR.SUB_VAR} 格式
 - {array} *param* 混淆变量 ,key => $var 格式

#### 返回值
 - string 

### 15.25 is_url($url): bool
检测字符串是否为 URL， 格式同时包含 // 这种省略协议的模式
#### 参数
 - {string} *url* 

#### 返回值
 - bool 

### 15.26 url_safe_b64encode($str): string
url base64 安全编码
将base64中 + / = 符号分别替换成 - _ ''
base64编码
#### 参数
 - {string} *str* 

#### 返回值
 - string 

### 15.27 url_safe_b64decode($str): string
url base64 安全解码
base64解码
#### 参数
 - {string} *str* 

#### 返回值
 - string 

### 15.28 check_php_var_name_legal($str): false|string
检测字符串是否符合 PHP 变量命名规则
#### 参数
 - {string} *str* 

#### 返回值
 - false|string 

### 15.29 filename_sanitize($filename): string|string[]
文件名清洗（根据Windows标准）
#### 参数
 - {string} *filename* 

#### 返回值
 - string|string[] 

### 15.30 pascalcase_to_underscores($str): string
帕斯卡式转化成下划线格式
(同时清理多个下划线连在一起的情况）
#### 参数
 - {string} *str* 

#### 返回值
 - string 

### 15.31 underscores_to_pascalcase($str,$capitalize_first): string
下划线格式转化成帕斯卡式
#### 参数
 - {string} *str* 
 - {bool} *capitalize_first* 是否使用大驼峰格式

#### 返回值
 - string 

### 15.32 json_decode_safe($str,$associative,$depth,$flags): mixed
安全地解析json字符串，错误则抛出异常。
建议在业务代码中代替使用php原生的json_decode。
#### 参数
 - {string} *str* 
 - {bool} *associative* 
 - {int} *depth* 
 - {int} *flags* 

#### 返回值
 - mixed 

### 15.33 encodeURIComponent($string): string
PHP URL encoding/decoding functions for Javascript interaction V3.0
(C) 2006 www.captain.at - all rights reserved
License: GPL
#### 参数
 - {string} *string* 

#### 返回值
 - string 

### 15.34 encodeURIComponentByCharacter()
### 15.35 decodeURIComponent($string): string
#### 参数
 - {string} *string* 

#### 返回值
 - string 

### 15.36 decodeURIComponentByCharacter($str): array
#### 参数
 - {string} *str* 

#### 返回值
 - array 

### 15.37 encodeURI()
### 15.38 encodeURIByCharacter($char): string
#### 参数
 - {string} *char* 

#### 返回值
 - string 

### 15.39 decodeURI($string): string
#### 参数
 - {string} *string* 

#### 返回值
 - string 

### 15.40 decodeURIByCharacter()
### 15.41 escape()
### 15.42 escapeByCharacter()
### 15.43 unescape($string): string
#### 参数
 - {string} *string* 

#### 返回值
 - string 

### 15.44 unEscapeByCharacter()
### 15.45 generate_guid($trim): string
Returns a GUIDv4 string
Uses the best cryptographically secure method
for all supported platforms with fallback to an older,
less secure version.
#### 参数
 - {bool} *trim* 

#### 返回值
 - string 



## 16. TIME
 > 时间相关操作函数

### 16.1 time_get_month_period_ranges($start_str,$end_str): array
获取制定开始时间、结束时间的上中下旬分段数组
#### 参数
 - {string} *start_str* 
 - {string} *end_str* 

#### 返回值
 - array [[period_th, start_time, end_time],...]

### 16.2 get_timezone_offset_min_between_gmt($timezone_title): float|int
#### 参数
 - {string} *timezone_title* 

#### 返回值
 - float|int 

### 16.3 get_time_left(): int|null
获取剩余时间（秒）
如果是CLI模式，该函数不进行计算

#### 返回值
 - int|null 秒，null表示无限制

### 16.4 filter_date_range($ranges,$default_start,$default_end,$as_datetime): array
过滤时间范围，补充上时分秒
#### 参数
 - {array} *ranges* 时间范围（开始，结束）
 - {string|int} *default_start* 默认开始时间
 - {string|int} *default_end* 默认结束时间
 - {bool} *as_datetime* 是否以日期+时间形式返回

#### 返回值
 - array [开始时间,结束时间]

### 16.5 microtime_diff($start,$end): float
Calculate a precise time difference.
#### 参数
 - {string} *start* result of microtime()
 - {string} *end* result of microtime(); if NULL/FALSE/0/'' then it's now

#### 返回值
 - float difference in seconds, calculated with minimum precision loss

### 16.6 format_time_size($secs,$keep_zero_padding,$full_desc): string
format time range
#### 参数
 - {int} *secs* 
 - {bool} *keep_zero_padding* 
 - {bool} *full_desc* 

#### 返回值
 - string 

### 16.7 microtime_to_date($microtime,$format,$precision): string
转换微秒到指定时间格式
#### 参数
 - {string} *microtime* 微秒字符串，通过 microtime(false) 产生
 - {string} *format* 时间格式
 - {int} *precision* 精度（秒之后）

#### 返回值
 - string 

### 16.8 float_time_to_date($float_time,$format,$precision): string
转换秒（浮点数）到指定时间格式
#### 参数
 - {float} *float_time* 时间，通过 microtime(true) 产生
 - {string} *format* 时间格式
 - {int} *precision* 精度（秒之后）

#### 返回值
 - string 

### 16.9 time_empty($time_str): bool
check time string is empty (cmp to 1970)
#### 参数
 - {string} *time_str* 

#### 返回值
 - bool 

### 16.10 pretty_time($timestamp,$as_html): string
格式化友好显示时间
#### 参数
 - {int} *timestamp* 
 - {bool} *as_html* 是否使用span包裹

#### 返回值
 - string 

### 16.11 make_date_ranges($start,$end,$format): array
补充日期范围，填充中间空白天数
#### 参数
 - {string|int} *start* 开始时间（允许开始时间大于结束时间）
 - {string|int} *end* 结束时间
 - {string} *format* 结果日期格式，如果设置为月份，函数自动去重

#### 返回值
 - array 

### 16.12 calc_actual_date($start,$days): string
获取从$start开始经过$days个工作日后的日期
实际日期 = 工作日数 + 周末天数 -1
#### 参数
 - {string} *start* 开始日期
 - {int} *days* 工作日天数 正数为往后，负数为往前

#### 返回值
 - string Y-m-d 日期

### 16.13 time_range($start,$end): string
计算时间差到文本
#### 参数
 - {string} *start* 
 - {string} *end* 

#### 返回值
 - string 

### 16.14 time_get_eta($start_time,$index,$total,$pretty): int|string
计算预计结束时间 ETA
#### 参数
 - {int} *start_time* 开始时间
 - {int} *index* 当前处理序号
 - {int} *total* 总数量
 - {bool} *pretty* 是否以文字方式返回剩余时间，设置false则返回秒数

#### 返回值
 - int|string 

### 16.15 time_range_v($seconds): string
转化时间长度到字符串
<pre>
$str = time_range_v(3601);
//1H 0M 1S
</pre>
#### 参数
 - {int} *seconds* 

#### 返回值
 - string 

### 16.16 mk_utc()


## 17. UTIL
 > 杂项操作函数

### 17.1 tick_dump($step,$fun)
步进方式调试
#### 参数
 - {int} *step* 步长
 - {string} *fun* 调试函数，默认使用dump
### 17.2 readline()
读取控制台行输入，如果系统安装了扩展，优先使用扩展函数
### 17.3 try_many_times($payload,$tries): int
尝试调用函数
#### 参数
 - {callable} *payload* 处理函数，返回 FALSE 表示中断后续尝试
 - {int} *tries* 出错时额外尝试次数（不包含第一次正常执行）

#### 返回值
 - int 总尝试次数（不包含第一次正常执行）

### 17.4 dump()
程序调试函数
调用方式：dump($var1, $var2, ..., 1) ，当最后一个数值为1时，表示退出（die）程序运行
### 17.5 printable($var,$print_str): bool
检测变量是否可以打印输出（如字符串、数字、包含toString方法对象等）
布尔值、资源等属于不可打印输出变量
#### 参数
 - {mixed} *var* 
 - {string} *print_str* 可打印字符串

#### 返回值
 - bool 是否可打印

### 17.6 print_exception($ex,$include_external_properties,$as_return): string
打印异常信息
#### 参数
 - {\Exception} *ex* 
 - {bool} *include_external_properties* 是否包含额外异常信息
 - {bool} *as_return* 是否以返回方式（不打印异常）处理

#### 返回值
 - string 

### 17.7 print_trace($trace,$with_callee,$with_index,$as_return): string
打印trace信息
#### 参数
 - {array} *trace* 
 - {bool} *with_callee* 
 - {bool} *with_index* 
 - {bool} *as_return* 

#### 返回值
 - string 

### 17.8 print_sys_error($code,$msg,$file,$line,$trace_string)
打印系统错误及trace跟踪信息
#### 参数
 - {integer} *code* 
 - {string} *msg* 
 - {string} *file* 
 - {integer} *line* 
 - {string} *trace_string* 
### 17.9 error2string($code): string
转换错误码值到字符串
#### 参数
 - {int} *code* 

#### 返回值
 - string 

### 17.10 string2error($string): int
转换错误码到具体的码值
#### 参数
 - {string} *string* 

#### 返回值
 - int 

### 17.11 register_error2exception($error_levels,$exception_class): callable|null
注册将PHP错误转换异常抛出
#### 参数
 - {int} *error_levels* 
 - {\ErrorException|null} *exception_class* 

#### 返回值
 - callable|null 

### 17.12 is_function($f): boolean
检测是否为函数
#### 参数
 - {mixed} *f* 

#### 返回值
 - boolean 

### 17.13 class_uses_recursive($class_or_object): string[]
获取对象、类的所有继承的父类(包含 trait 类)
如果无需trait,试用class_parents即可
#### 参数
 - {string|object} *class_or_object* 

#### 返回值
 - string[] 

### 17.14 trait_uses_recursive($trait): array
递归方式获取trait
#### 参数
 - {string} *trait* 

#### 返回值
 - array 

### 17.15 get_constant_name($class,$const_val): string|null
获取指定类常量名称
#### 参数
 - {string} *class* 类名
 - {mixed} *const_val* 常量值

#### 返回值
 - string|null 

### 17.16 assert_via_exception($expression,$err_msg,$exception_class)
通过抛异常方式处理断言
#### 参数
 - {mixed} *expression* 断言值
 - {string} *err_msg* 
 - {string} *exception_class* 异常类，缺省使用 \Exception
### 17.17 pdog($fun,$handler)
pdog
#### 参数
 - {string} *fun* 
 - {callable|string} *handler* 
### 17.18 guid(): mixed
获取当前上下文GUID

#### 返回值
 - mixed 

### 17.19 var_export_min($var,$return): string|null
使用最小格式导出变量（类似var_export）
#### 参数
 - {mixed} *var* 
 - {bool} *return* 是否以返回方式返回，缺省为输出到终端

#### 返回值
 - string|null 

### 17.20 memory_leak_check($threshold,$leak_payload)
检测内存溢出，正式运行代码不建议开启该项检查，避免损失性能
#### 参数
 - {int} *threshold* 
 - {callable|string} *leak_payload* 内存泄露时调用函数
### 17.21 debug_mark($tag,$trace_location,$mem_usage): mixed
代码打点
#### 参数
 - {string} *tag* 
 - {bool} *trace_location* 
 - {bool} *mem_usage* 

#### 返回值
 - mixed 

### 17.22 debug_mark_output($as_return): string|null
输出打点信息
#### 参数
 - {bool} *as_return* 

#### 返回值
 - string|null 



