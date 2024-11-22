# Env
 > Platform function related operation functions

## 1. server_in_windows(): bool
Check if the server is running on Windows

#### Returns
 - bool 

## 2. server_in_https(): bool
Check if the server is running in HTTPS protocol

#### Returns
 - bool 

## 3. get_upload_max_size($human_readable): string|number
Get the maximum file size allowed for upload by PHP
Depends on: maximum upload file size, maximum POST size
#### Parameters
 - {bool} *$human_readable* whether to return in readable mode

#### Returns
 - string|number 

## 4. get_max_socket_timeout($ttf): int
Get the maximum available socket timeout
#### Parameters
 - {int} *$ttf* allowed advance time

#### Returns
 - int timeout (seconds), if 0, it means no timeout limit

## 5. get_client_ip(): string
Get the client IP
Prioritize the defined x-forward-for proxy IP (may have certain risks)

#### Returns
 - string client IP, return an empty string if failed

## 6. get_all_opt(): array
Get all command line options, the format rules are consistent with getopt

#### Returns
 - array 

## 7. get_php_info(): array
Get PHP configuration information

#### Returns
 - array 

## 8. console_color($text,$fore_color,$back_color,$override): string
Generate CLI strings with colors
#### Parameters
 - {string} *$text* 
 - {string} *$fore_color* 
 - {string} *$back_color* 
 - {bool} *$override* Whether to overwrite the original color setting

#### Returns
 - string 

## 9. console_color_clean($text): string
Clean up color control characters
#### Parameters
 - {string} *$text* 

#### Returns
 - string 

## 10. show_progress($index,$total,$patch_text,$start_timestamp)
Show progress bar in console
Supplementary display text, can be a closure function, all echo strings in the function will be output as progress text, if it is a closure function, due to the ob of php cli, there will be a certain delay
#### Parameters
 - {int} *$index* 
 - {int} *$total* 
 - {string|callable} *$patch_text* 
 - {int} *$start_timestamp* start timestamp, initialize the global unique timestamp in the empty function
## 11. show_loading($patch_text,$loading_chars): void
Loading mode outputs console string
#### Parameters
 - {string} *$patch_text* Display text
 - {string[]} *$loading_chars* Loading character sequence, for example: ['\\', '|', '/', '-']

#### Returns
 - void 

## 12. run_command($command,$param,$async): bool|string|null
Run command
#### Parameters
 - {string} *$command* command
 - {array} *$param* parameter
 - {bool} *$async* whether to execute asynchronously

#### Returns
 - bool|string|null 

## 13. run_command_parallel_width_progress($command,$param_batches,$options): bool
Run commands concurrently with progress text.
For calling parameters, please refer to the function: run_command_parallel()
#### Parameters
 - {string} *$command* 
 - {array} *$param_batches* 
 - {array} *$options* 

#### Returns
 - bool 

## 14. run_command_parallel($command,$param_batches,$options): bool
Concurrently run commands
- callable|null $on_start($param, $param_index, $start_time) returns false to interrupt execution
- callable|null $on_running($param, $param_index) returns false to interrupt execution
- callable|null $on_finish($param, $param_index, $output, $cost_time, $status_code, $error) returns false to interrupt execution
- int $parallel_num concurrent number, default is 20
- int $check_interval status check interval (unit: milliseconds), default is 100ms
- int $process_max_execution_time maximum process execution time (unit: milliseconds), default is not set
#### Parameters
 - {string} *$command* Execute command
 - {array} *$param_batches* Task parameter list. Parameters are passed to command as long parameters. For specific implementation, please refer to: build_command() function implementation.
 - {array} *$options* parameters are as follows:

#### Returns
 - bool whether it ends normally

## 15. build_command($cmd_line,$param): string
Build command line
#### Parameters
 - {string} *$cmd_line* 
 - {array} *$param* 

#### Returns
 - string 

## 16. escape_win32_argv($value): string
Escape argv parameters under Windows
#### Parameters
 - {string|int} *$value* 

#### Returns
 - string 

## 17. escape_win32_cmd($value): string|string[]|null
Escape cmd.exe metacharacters with ^
#### Parameters
 - {mixed} *$value* 

#### Returns
 - string|string[]|null 

## 18. noshell_exec($command): false|string
Like shell_exec() but bypass cmd.exe
#### Parameters
 - {string} *$command* 

#### Returns
 - false|string 

## 19. command_exists($command): bool
Check if the command exists
#### Parameters
 - {string} *$command* 

#### Returns
 - bool 

## 20. windows_get_port_usage($include_process_info): array
Get the network usage of Windows process
#### Parameters
 - {bool} *$include_process_info* whether to include process information (title, program file name), this function requires Windows administrator mode

#### Returns
 - array format: [protocol='', local_ip='', local_port='', foreign_ip='', foreign_port='', state='', pid='', 'process_name'='', 'process_file_id'=>'']

## 21. unix_get_port_usage(): array
Get port occupancy status under Linux

#### Returns
 - array format: [protocol='', local_ip='', local_port='', foreign_ip='', foreign_port='', state='', pid='', 'process_name'='', 'process_file_id'=>'']

## 22. get_screen_size(): array|null
Get the width and height of the console screen

#### Returns
 - array|null Return format: [number of columns, number of rows], if the current environment does not support it, it will return null

## 23. process_kill($pid): bool
Kill process
#### Parameters
 - {int} *$pid* Process ID

#### Returns
 - bool 

## 24. process_running($pid): bool
Check whether the specified process is running
#### Parameters
 - {int} *$pid* Process ID

#### Returns
 - bool 

## 25. process_signal($signal,$handle): bool
Process signal monitoring
#### Parameters
 - {mixed} *$signal* 
 - {mixed} *$handle* 

#### Returns
 - bool 

## 26. process_send_signal($pid,$sig_num): bool
Send process semaphore
#### Parameters
 - {int} *$pid* Process ID
 - {int} *$sig_num* semaphore

#### Returns
 - bool 

## 27. replay_current_script(): false|int
Replay the current script command

#### Returns
 - false|int Returns the newly opened Process ID, false is returned if failed



