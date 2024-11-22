# Curl
 > CURL Enhancement Functions
 > Mark: standard structure for curl_get():
 > [info=>['url'='', ...], error='', head=>'', body=>'']

## 1. curl_get($url,$data,$curl_option): array
CURL GET Request
#### Parameters
 - {string} *$url* 
 - {mixed|null} *$data* 
 - {array} *$curl_option* extra curl option

#### Returns
 - array [info=>[], head=>'', body=>'', ...] curl_getinfo structure

## 2. curl_post($url,$data,$curl_option): array
Post request
#### Parameters
 - {string} *$url* 
 - {mixed|null} *$data* 
 - {array} *$curl_option* 

#### Returns
 - array 

## 3. curl_post_json($url,$data,$curl_option): array
post data in json format
#### Parameters
 - {string} *$url* 
 - {mixed} *$data* 
 - {array} *$curl_option* 

#### Returns
 - array 

## 4. curl_post_file($url,$file_map,$ext_param,$curl_option): array
curl post file
#### Parameters
 - {string} *$url* 
 - {array} *$file_map* [filename=>file, filename=>[file, mime]...] File name mapping, if mime information is not provided here, the backend may receive application/octet-stream
 - {mixed} *$ext_param* Other post parameters submitted at the same time
 - {array} *$curl_option* curl option

#### Returns
 - array curl_query返回结果，包含 [info=>[], head=>'', body=>''] 信息

## 5. curl_put($url,$data,$curl_option): array
Put request
#### Parameters
 - {string} *$url* 
 - {array} *$data* 
 - {array} *$curl_option* 

#### Returns
 - array 

## 6. curl_delete($url,$data,$curl_option): array
Delete request
#### Parameters
 - {string} *$url* 
 - {array} *$data* 
 - {array} *$curl_option* 

#### Returns
 - array 

## 7. curl_query($url,$curl_option): array
Quickly execute a curl query then close the curl connection
#### Parameters
 - {string} *$url* 
 - {array} *$curl_option* 

#### Returns
 - array [info=>[], error='', head=>'', body=>'']

## 8. curl_patch_header($curl_option,$header_name,$header_value): void
CURL HTTP Header appends additional information. If it already exists, it will be replaced.
#### Parameters
 - {array} *$curl_option* 
 - {string} *$header_name* 
 - {string} *$header_value* 

#### Returns
 - void 

## 9. curl_build_command($url,$body_str,$method,$headers): string
Build CURL command
#### Parameters
 - {string} *$url* 
 - {string} *$body_str* 
 - {string} *$method* 
 - {string[]} *$headers* header information, in the format of ['Content-Type: application/json'] or ['Content-Type‘=>'application/json']

#### Returns
 - string 

## 10. curl_get_proxy_option($proxy_string): array
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
 - {string} *$proxy_string* 

#### Returns
 - array 

## 11. curl_get_default_option(): array
Get CURL default options

#### Returns
 - array 

## 12. curl_option_merge($old_option,$new_option): array
Merge curl options, especially handle the duplicate parts in CURLOPT_HTTPHEADER
#### Parameters
 - {array} *$old_option* 
 - {array} *$new_option* 

#### Returns
 - array 

## 13. curl_convert_http_header_to_assoc($headers): array
Convert http header array to associative array to facilitate modification operations
#### Parameters
 - {array} *$headers* 

#### Returns
 - array 

## 14. curl_set_default_option($curl_option,$patch)
Set default options for curl_operations
#### Parameters
 - {array} *$curl_option* 
 - {bool} *$patch* Whether to add in append mode, the default is to overwrite
## 15. curl_instance($url,$ext_curl_option): array(resource,
Get CURL instance object
#### Parameters
 - {string} *$url* 
 - {array} *$ext_curl_option* curl option, additional default options will be added through curl_default_option()

#### Returns
 - array(resource, $curl_option)

## 16. curl_data2str($data): string
convert data to request string
#### Parameters
 - {mixed} *$data* 

#### Returns
 - string 

## 17. curl_print_option($options,$as_return): array|null
打印curl option
#### Parameters
 - {array} *$options* 
 - {bool} *$as_return* 

#### Returns
 - array|null 

## 18. curl_option_to_request_header($options): string[]
转换curl option到标准HTTP头信息
#### Parameters
 - {array} *$options* 

#### Returns
 - string[] array

## 19. curl_urls_to_fetcher($urls,$ext_curl_option): \Closure
Convert the request link into a closure function
#### Parameters
 - {string[]|array[]} *$urls* request link array
 - {array} *$ext_curl_option* curl option array

#### Returns
 - \Closure 

## 20. curl_cut_raw($ch,$raw_string): string[]
Cut CURL result string
#### Parameters
 - {resource} *$ch* 
 - {string} *$raw_string* 

#### Returns
 - string[] head,body

## 21. curl_error_message($error_no): string
Get curl error information
#### Parameters
 - {int} *$error_no* 

#### Returns
 - string Empty indicates success

## 22. curl_query_success($query_result,$error,$allow_empty_body): bool
Determine whether curl_query is successful
#### Parameters
 - {array} *$query_result* curl_query returns the standard structure
 - {string} *$error* 
 - {bool} *$allow_empty_body* allows body to be empty

#### Returns
 - bool 

## 23. curl_concurrent($curl_option_fetcher,$on_item_start,$on_item_finish,$rolling_window): bool
CURL concurrent requests
Note: The callback function needs to be processed as soon as possible to avoid blocking subsequent request processes.
#### Parameters
 - {callable|array} *$curl_option_fetcher* array Returns the curl option mapping array. Even if there is only one url, [CURLOPT_URL=>$url] needs to be returned.
 - {callable|null} *$on_item_start* ($curl_option) Start executing the callback. If false is returned, the task is ignored.
 - {callable|null} *$on_item_finish* ($curl_ret, $curl_option) Request end callback, parameter 1: return result array, parameter 2: curl option
 - {int} *$rolling_window* Number of rolling requests

#### Returns
 - bool 



