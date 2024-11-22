# File
 > File Enhancement Functions

## 1. glob_recursive($pattern,$flags): array
Glob recursive
Does not support flag GLOB_BRACE
#### Parameters
 - {string} *$pattern* 
 - {int} *$flags* 

#### Returns
 - array 

## 2. unlink_recursive($path,$verbose): void
Recursive unlink
#### Parameters
 - {string} *$path* Folder to be deleted
 - {bool} *$verbose* Whether to print debug information

#### Returns
 - void 

## 3. file_exists_case_sensitive($file): bool|null
Check if the file exists and the name strictly matches the upper and lower case
true: the file exists, false: the file does not exist, null: the file exists but the case is inconsistent
#### Parameters
 - {string} *$file* 

#### Returns
 - bool|null 

## 4. assert_file_in_dir($file,$dir,$exception_class)
Assert that the file is contained in the specified folder (the file must exist)
#### Parameters
 - {string} *$file* 
 - {string} *$dir* 
 - {string} *$exception_class* 
## 5. file_in_dir($file_path,$dir_path): bool
Determine whether the file is contained in the specified folder
#### Parameters
 - {string} *$file_path* file path
 - {string} *$dir_path* directory path

#### Returns
 - bool The file does not exist in the directory, or the file does not actually exist

## 6. resolve_absolute_path($file_or_path): string
Parse the real path of the path string and remove the relative path information
Compared with realpath, this function does not need to check whether the file exists
<pre>
Calling format: resolve_absolute_path("c:/a/b/./../../windows/system32");
Return: c:/windows/system32
#### Parameters
 - {string} *$file_or_path* directory path or file path string

#### Returns
 - string 

## 7. resolve_file_extension($filename,$to_lower_case): string|null
Get file extension based on file name
#### Parameters
 - {string} *$filename* file name
 - {bool} *$to_lower_case* whether to convert to lower case, default is to convert to lower case

#### Returns
 - string|null string or null,no extension detected

## 8. file_exists_case_insensitive($file,$parent): bool
Check if the file exists and that the name can be case-insensitive
#### Parameters
 - {string} *$file* 
 - {null} *$parent* 

#### Returns
 - bool 

## 9. file_put_contents_safe()
## 10. copy_recursive($src,$dst)
Copy directories recursively
#### Parameters
 - {string} *$src* 
 - {string} *$dst* 
## 11. mkdir_batch($dirs,$break_on_error,$permissions): string[]
Create directories in batches
#### Parameters
 - {string[]} *$dirs* Directory path list
 - {bool} *$break_on_error* Whether to throw an exception when creation fails
 - {int} *$permissions* Directory default permissions

#### Returns
 - string[] Directory list that failed to be created, and returns an empty array if successful

## 12. mkdir_by_file($file,$permissions): string
Create a folder based on the target file path
#### Parameters
 - {string} *$file* 
 - {int} *$permissions* directory permissions

#### Returns
 - string successfully created directory path

## 13. get_dirs($dir): array
Get directories recursive
#### Parameters
 - {string} *$dir* 

#### Returns
 - array 

## 14. file_lines($file,$line_separator): int
Get the number of lines in a file
#### Parameters
 - {string|resource} *$file* file path or file handle
 - {string} *$line_separator* line break character

#### Returns
 - int 

## 15. tail($file,$lines,$buffer): string[]
Tail
#### Parameters
 - {string|resource} *$file* 
 - {int} *$lines* Number of lines to read
 - {int} *$buffer* Buffer size

#### Returns
 - string[] Content of each line

## 16. file_read_by_line($file,$handle,$start_line,$buff_size): bool
Read file line by line
#### Parameters
 - {string} *$file* file name
 - {callable} *$handle* processing function, pass in parameters: ($line_str, $line), if the function returns false, the processing is interrupted
 - {int} *$start_line* start reading line number (starting from 1)
 - {int} *$buff_size* buffer size

#### Returns
 - bool whether it is a processing function interrupt return

## 17. render_php_file($php_file,$vars): false|string
Render php file and return as string
#### Parameters
 - {mixed} *$php_file* 
 - {array} *$vars* 

#### Returns
 - false|string 

## 18. get_folder_size($path): int
Calculate folder size recursively
#### Parameters
 - {string} *$path* 

#### Returns
 - int 

## 19. log($file,$content,$max_size,$max_files,$pad_str): bool|int
log records to file
#### Parameters
 - {string} *$file* file
 - {mixed} *$content* record content
 - {float|int} *$max_size* maximum size of a single file, default
 - {int} *$max_files* maximum number of recorded files
 - {string|null} *$pad_str* record file name append string

#### Returns
 - bool|int whether the file is recorded successfully

## 20. read_file_lock($key): false|string|null
Read file lock
#### Parameters
 - {string} *$key* 

#### Returns
 - false|string|null 

## 21. write_file_lock($key,$lock_flag): string
Write file lock
#### Parameters
 - {string} *$key* 
 - {string} *$lock_flag* 

#### Returns
 - string 

## 22. remove_file_lock($key): bool
remove file lock
#### Parameters
 - {string} *$key* 

#### Returns
 - bool 

## 23. init_file_lock($key,$is_new): resource
Init file lock
#### Parameters
 - {string} *$key* 
 - {bool} *$is_new* 

#### Returns
 - resource 锁文件操作句柄

## 24. log_tmp_file($filename,$content,$max_size,$max_files,$pad_str): bool|int
Log in temporary directory
if high performance required, support to use logrotate programme to process your log file
#### Parameters
 - {string} *$filename* 
 - {mixed} *$content* 
 - {float|int} *$max_size* 
 - {int} *$max_files* 
 - {string|null} *$pad_str* 

#### Returns
 - bool|int 

## 25. create_tmp_file($dir,$prefix,$ext,$mod): string
Create a temporary file
#### Parameters
 - {string} *$dir* The directory where the file is located
 - {string} *$prefix* The file name prefix
 - {string} *$ext* The file name suffix
 - {numeric} *$mod* Permission, default is 777

#### Returns
 - string 

## 26. upload_file_error($upload_error_no): string
Get file upload error message via PHP file upload error number
#### Parameters
 - {int} *$upload_error_no* 

#### Returns
 - string 

## 27. upload_file_check($file,$opt): void
Upload file check by option
#### Parameters
 - {string} *$file* 
 - {array} *$opt* 

#### Returns
 - void 

## 28. get_extensions_by_mime($mime): string[]
Get a list of extensions that match the specified mime
#### Parameters
 - {string} *$mime* 

#### Returns
 - string[] 

## 29. get_mimes_by_extension($ext): string[]
Get mime information by file suffix
#### Parameters
 - {string} *$ext* file suffix

#### Returns
 - string[] mime list

## 30. mime_match_extensions($mime,$extensions): bool
Check if the given mime information is in the specified extension list
This method is usually used to check whether the uploaded file meets the set file type
#### Parameters
 - {string} *$mime* 
 - {string[]} *$extensions* 

#### Returns
 - bool 

## 31. mime_match_accept($mime,$accept): bool
Check if the file mime information matches the accept string
#### Parameters
 - {string} *$mime* file mime information
 - {string} *$accept* <input accept=""/> format reference：https://developer.mozilla.org/en-US/docs/Web/HTML/Attributes/accept

#### Returns
 - bool 

## 32. file_match_accept($file,$accept): bool
Check if the file matches the specified accept definition
#### Parameters
 - {string} *$file* file
 - {string} *$accept* <input accept=""/> information, please refer to the format: https://developer.mozilla.org/en-US/docs/Web/HTML/Attributes/accept

#### Returns
 - bool 



