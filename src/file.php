<?php
/**
 * File Enhancement Functions
 */
namespace LFPhp\Func;

use DirectoryIterator;
use Exception;
use RuntimeException;

/**
 * Glob recursive
 * Does not support flag GLOB_BRACE
 * @param string $pattern
 * @param int $flags
 * @return array
 */
function glob_recursive($pattern, $flags = 0){
	$files = glob($pattern, $flags);
	foreach(glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir){
		$files = array_merge($files, glob_recursive($dir.'/'.basename($pattern), $flags));
	}

	//Fix directory separator
	array_walk($files, function(&$file){
		$file = str_replace(['/', '\\'], [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], $file);
	});
	return $files;
}

/**
 * Recursive unlink
 * @param string $path Folder to be deleted
 * @param bool $verbose Whether to print debug information
 * @return void
 */
function unlink_recursive($path, $verbose = false){
	if(!is_readable($path)){
		return;
	}
	if(is_file($path)){
		if($verbose){
			echo "unlink: $path\n";
		}
		if(!unlink($path)){
			throw new RuntimeException("Failed to unlink $path: ".var_export(error_get_last(), true));
		}
		return;
	}
	$foldersToDelete = [];
	$filesToDelete = [];
	// we should scan the entire directory before traversing deeper, to not have open handles to each directory:
	// on very large director trees you can actually get OS-errors if you have too many open directory handles.
	foreach(new DirectoryIterator($path) as $fileInfo){
		if($fileInfo->isDot()){
			continue;
		}
		if($fileInfo->isDir()){
			$foldersToDelete[] = $fileInfo->getRealPath();
		}else{
			$filesToDelete[] = $fileInfo->getRealPath();
		}
	}
	unset($fileInfo); // free file handle
	foreach($foldersToDelete as $folder){
		unlink_recursive($folder, $verbose);
	}
	foreach($filesToDelete as $file){
		if($verbose){
			echo "unlink: {$file}\n";
		}
		if(!unlink($file)){
			throw new RuntimeException("Failed to unlink {$file}: ".var_export(error_get_last(), true));
		}
	}
	if($verbose){
		echo "rmdir: {$path}\n";
	}
	if(!rmdir($path)){
		throw new RuntimeException("Failed to rmdir {$path}: ".var_export(error_get_last(), true));
	}
}

/**
 * Check if the file exists and the name strictly matches the upper and lower case
 * @param string $file
 * @return bool|null
 * true: the file exists, false: the file does not exist, null: the file exists but the case is inconsistent
 */
function file_exists_case_sensitive($file){
	//The Linux file system strictly matches upper and lower case, so you can just use is_file to judge
	if(!server_in_windows()){
		return !!is_file($file);
	}

	//[windows] If the file does not exist, no need to check
	if(!is_file($file)){
		return false;
	}
	$r_file = str_replace('\\', '/', $file);
	$realpath = str_replace('\\', '/', realpath($r_file));
	return strcmp($r_file, $realpath) == 0 ? true : null;
}

/**
 * Assert that the file is contained in the specified folder (the file must exist)
 * @param string $file
 * @param string $dir
 * @param string $exception_class
 */
function assert_file_in_dir($file, $dir, $exception_class = Exception::class){
	assert_via_exception(file_in_dir($file, $dir), 'File access deny', $exception_class);
}

/**
 * Determine whether the file is contained in the specified folder
 * @param string $file_path file path
 * @param string $dir_path directory path
 * @return bool The file does not exist in the directory, or the file does not actually exist
 */
function file_in_dir($file_path, $dir_path, $ignore_file_and_dir_exists = false){
	if(!$ignore_file_and_dir_exists){
		$file_path = realpath($file_path);
		$dir_path = realpath($dir_path);
	}else{
		$file_path = resolve_absolute_path($file_path);
		$dir_path = resolve_absolute_path($dir_path);
	}
	//windows is not case-sensitive for file names
	if(stripos(PHP_OS, 'win') !== false){
		return stripos($file_path, $dir_path) === 0;
	}
	return strpos($file_path, $dir_path) === 0;
}

/**
 * Parse the real path of the path string and remove the relative path information
 * Compared with realpath, this function does not need to check whether the file exists
 * <pre>
 * Calling format: resolve_absolute_path("c:/a/b/./../../windows/system32");
 * Return: c:/windows/system32
 * @param string $file_or_path directory path or file path string
 * @return string
 */
function resolve_absolute_path($file_or_path){
	$file_or_path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $file_or_path);
	$parts = array_filter(explode(DIRECTORY_SEPARATOR, $file_or_path), 'strlen');
	$absolutes = [];
	foreach($parts as $part){
		if('.' == $part)
			continue;
		if('..' == $part){
			array_pop($absolutes);
		}else{
			$absolutes[] = $part;
		}
	}
	return implode(DIRECTORY_SEPARATOR, $absolutes);
}

/**
 * Get file extension based on file name
 * @param string $filename file path, file name or url include file name
 * @param bool $to_lower_case whether to convert to lower case, default is to convert to lower case
 * @return string string
 */
function resolve_file_extension($filename, $to_lower_case = true){
	$filename = str_replace('\\', '/', $filename);
	if(strpos($filename, '/') !== false){
		$filename = preg_replace('/^.*\/([^\/]+)$/', '$1', $filename);
	}
	if(strpos($filename, '?') !== false){
		$filename = preg_replace('/(.*?)\?.*$/', '$1', $filename);
	}
	if(strpos($filename, '#') !== false){
		$filename = preg_replace('/(.*?)\#.*$/', '$1', $filename);
	}
	if(strpos($filename, '.') <= 0){
		return '';
	}
	$tmp = explode('.', $filename);
	return $to_lower_case ? strtolower(end($tmp)) : end($tmp);
}

/**
 * Check if the file exists and that the name can be case-insensitive
 * @param string $file
 * @param null $parent
 * @return bool
 */
function file_exists_case_insensitive($file, $parent = null){
	if(is_file($file)){
		return $file;
	}

	$file = str_replace('\\', '/', $file);
	if($parent){
		$parent = str_replace('\\', '/', $parent);
		$parent = rtrim($parent, '/');
	}else{
		$tmp = explode('/', $file);
		array_pop($tmp);
		$parent = join('/', $tmp);
	}

	static $fs = [];
	if(!isset($fs[$parent]) || !$fs[$parent]){
		$fs[$parent] = glob_recursive($parent.'/*', GLOB_NOSORT);
	}
	foreach($fs[$parent] as $f){
		if(strcasecmp($f, $file) === 0){
			return $f;
		}
	}
	return false;
}

function file_put_contents_safe($file, $data, $flags = 0, $context = null){
	if(file_put_contents($file, $data, $flags, $context) === false){
		throw new Exception('file save fail:'.error_get_last());
	}
}

/**
 * Copy directories recursively
 * @param string $src
 * @param string $dst
 * @throw Exception
 */
function copy_recursive($src, $dst){
	$dir = opendir($src);
	mkdir($dst);
	while(false !== ($file = readdir($dir))){
		if(($file != '.') && ($file != '..')){
			if(is_dir($src.'/'.$file)){
				copy_recursive($src.'/'.$file, $dst.'/'.$file);
			}else{
				copy($src.'/'.$file, $dst.'/'.$file);
			}
		}
	}
	closedir($dir);
}

/**
 * Create directories in batches
 * @param string[] $dirs Directory path list
 * @param bool $break_on_error Whether to throw an exception when creation fails
 * @param int $permissions Directory default permissions
 * @return string[] Directory list that failed to be created, and returns an empty array if successful
 * @throws \Exception
 */
function mkdir_batch($dirs, $break_on_error = true, $permissions = 0x777){
	$errors = [];
	foreach($dirs as $dir){
		if(!is_dir($dir) && !mkdir($dir, $permissions, true)){
			if($break_on_error){
				throw new Exception('mkdir fail:'.$dir);
			}
			$errors[] = $dir;
		}
	}
	return $errors;
}

/**
 * Create a folder based on the target file path
 * @param string $file
 * @param int $permissions directory permissions
 * @return string successfully created directory path
 * @throws \Exception
 */
function mkdir_by_file($file, $permissions = 0777){
	$dir = dirname($file);
	if(!is_dir($dir) && !mkdir($dir, $permissions, true)){
		throw new \Exception('directory create fail');
	}
	return $dir;
}

/**
 * Get directories recursive
 * @param string $dir
 * @return array
 **/
function get_dirs($dir){
	$dir_list = [];
	if(false != ($handle = opendir($dir))){
		$i = 0;
		while(false !== ($file = readdir($handle))){
			if($file != "." && $file != ".." && is_dir($dir.DIRECTORY_SEPARATOR.$file)){
				$dir_list[$i] = $dir.DIRECTORY_SEPARATOR.$file;
				$i++;
			}
		}
		closedir($handle);
	}
	return $dir_list;
}

/**
 * Get the number of lines in a file
 * @param string|resource $file file path or file handle
 * @param string $line_separator line break character
 * @return int
 */
function file_lines($file, $line_separator = "\n"){
	if(is_string($file)){
		$fp = fopen($file, 'rb');
	}else{
		$fp = $file;
	}
	$lines = 0;
	while(!feof($fp)){
		$lines += substr_count(fread($fp, 8192), $line_separator);
	}
	if(is_string($file)){
		fclose($fp);
	}
	return $lines;
}

/**
 * Tail
 * @param string|resource $file
 * @param int $lines Number of lines to read
 * @param int $buffer Buffer size
 * @return string[] Content of each line
 * @throws \Exception
 */
function tail($file, $lines = 10, $buffer = 4096){
	$TAIL_NL = "\n";
	// Open the file
	if(is_resource($file) && (get_resource_type($file) == 'file' || get_resource_type($file) == 'stream')){
		$f = $file;
	}else if(is_string($file)){
		$f = fopen($file, 'rb');
	}else{
		throw new Exception('$file must be either a resource (file or stream) or a filename.');
	}

	// Jump to last character
	fseek($f, -1, SEEK_END);

	// Prepare to collect output
	$output = '';
	$chunk = '';

	// Start reading it and adjust line number if necessary
	// (Otherwise the result would be wrong if file doesn't end with a blank line)
	if(fread($f, 1) != $TAIL_NL){
		$lines -= 1;
	}

	// While we would like more
	while(ftell($f) > 0 && $lines >= 0){
		// Figure out how far back we should jump
		$seek = min(ftell($f), $buffer);

		// Do the jump (backwards, relative to where we are)
		fseek($f, -$seek, SEEK_CUR);

		// Read a chunk and prepend it to our output
		$output = ($chunk = fread($f, $seek)).$output;

		// Jump back to where we started reading
		fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);

		// Decrease our line counter
		$lines -= substr_count($chunk, $TAIL_NL);
	}

	// While we have too many lines
	// (Because of buffer size we might have read too many)
	while($lines++ < 0){
		// Find first newline and remove all text before that
		$output = substr($output, strpos($output, $TAIL_NL) + 1);
	}

	// Close file and return
	fclose($f);
	return explode($TAIL_NL, $output);
}

/**
 * Read file line by line
 * @param string $file file name
 * @param callable $handle processing function, pass in parameters: ($line_str, $line), if the function returns false, the processing is interrupted
 * @param int $start_line start reading line number (starting from 1)
 * @param int $buff_size buffer size
 * @return bool whether it is a processing function interrupt return
 * @throws \Exception
 */
function file_read_by_line($file, $handle, $start_line = 1, $buff_size = 1024){
	if(!($hd = fopen($file, 'r'))){
		throw new Exception('file open fail');
	}
	$stop = false;
	$last_line_buff = '';
	$read_line_counter = 0;
	while(!feof($hd) && !$stop){
		$buff = $last_line_buff.fgets($hd, $buff_size);
		$buff = str_replace("\r", "", $buff);
		$lines = explode("\n", $buff);
		$last_line_buff = array_pop($lines);
		if($lines){
			foreach($lines as $text){
				$read_line_counter++;
				if($read_line_counter < $start_line){
					continue;
				}
				if($handle($text, $read_line_counter) === false){
					fclose($hd);
					return false;
				}
			}
		}
	}
	fclose($hd);
	if($last_line_buff && $handle($last_line_buff, ++$read_line_counter) === false){
		return false;
	}
	return true;
}

/**
 * Render php file and return as string
 * @param $php_file
 * @param array $vars
 * @return false|string
 */
function render_php_file($php_file, $vars = []){
	ob_start();
	extract($vars);
	include $php_file;
	$str = ob_get_contents();
	ob_end_clean();
	return $str;
}

/**
 * remove empty folder
 * @param string $path
 * @return bool
 */
function remove_empty_sub_folder($path){
	$empty = true;
	foreach(glob($path.DIRECTORY_SEPARATOR.'*') as $file){
		$empty &= is_dir($file) && remove_empty_sub_folder($file);
	}
	return $empty && rmdir($path);
}

/**
 * Calculate folder size recursively
 * @param string $path
 * @return int
 */
function get_folder_size($path){
	$total_size = 0;
	$files = scandir($path);
	foreach($files as $t){
		if(is_dir(rtrim($path, '/').'/'.$t)){
			if($t <> "." && $t <> ".."){
				$size = get_folder_size(rtrim($path, '/').'/'.$t);
				$total_size += $size;
			}
		}else{
			$size = filesize(rtrim($path, '/').'/'.$t);
			$total_size += $size;
		}
	}
	return $total_size;
}

/**
 * log records to file
 * @param string $file file
 * @param mixed $content record content
 * @param float|int $max_size maximum size of a single file, default
 * @param int $max_files maximum number of recorded files
 * @param string|null $pad_str record file name append string
 * @return bool|int whether the file is recorded successfully
 */
function log($file, $content, $max_size = 10*1024*1024, $max_files = 5, $pad_str = null){
	if(!is_string($content)){
		$content = json_encode($content, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
	}
	$content = date('Y-m-d H:i:s')."  ".$content."\n";
	$pad_str = isset($pad_str) ? $pad_str : '-'.date('YmdHis');

	if(is_file($file) && $max_size && $max_size < filesize($file)){
		rename($file, $file.$pad_str);
		if($max_files > 1){
			$fs = glob($file.'*');
			if(count($fs) >= $max_files){
				usort($fs, function($a, $b){
					return filemtime($a) > filemtime($b) ? 1 : -1;
				});
				foreach($fs as $k => $f){
					if($k < (count($fs) - $max_files + 1)){
						unlink($f);
					}
				}
			}
		}
	}
	if(!is_file($file)){
		$dir = dirname($file);
		if(!is_dir($dir)){
			mkdir($dir, 0777, true);
		}
		touch($file);
	}
	return file_put_contents($file, $content, FILE_APPEND);
}

/**
 * Read file lock
 * @param string $key
 * @return false|string|null
 */
function read_file_lock($key){
	$fp = init_file_lock($key, $new);
	if($new){
		return null;
	}
	return fgets($fp);
}

/**
 * Write file lock
 * @param string $key
 * @param string $lock_flag
 * @return string
 */
function write_file_lock($key, $lock_flag){
	$fp = init_file_lock($key);
	fwrite($fp, $lock_flag);
	return $lock_flag;
}

/**
 * remove file lock
 * @param string $key
 * @return bool
 */
function remove_file_lock($key){
	$var_key = __NAMESPACE__.'\\FUNC_FILE_LOCK';
	$lock_file_name = filename_sanitize($var_key);
	$file = sys_get_temp_dir().'/'.$lock_file_name.'/'.$key.'.lock';
	if(is_file($file)){
		return unlink($file);
	}
	return true;
}

/**
 * Init file lock
 * @param string $key
 * @param bool $is_new
 * @return resource 锁文件操作句柄
 */
function init_file_lock($key, &$is_new = false){
	$var_key = __NAMESPACE__.'\\FUNC_FILE_LOCK';
	$lock_file_name = filename_sanitize($var_key);
	if(!isset($GLOBALS[$var_key])){
		$GLOBALS[$var_key] = [];
	}
	if(!isset($GLOBALS[$var_key][$key])){
		$dir = sys_get_temp_dir().'/'.$lock_file_name;
		if(!is_dir($dir)){
			mkdir($dir, 0777, true);
		}
		$f = $dir.'/'.$key.'.lock';
		$is_new = !is_file($f);
		$fp = fopen($f, 'a+');
		$GLOBALS[$var_key][$key] = $fp;
	}
	rewind($GLOBALS[$var_key][$key]);
	return $GLOBALS[$var_key][$key];
}

/**
 * Log in temporary directory
 * if high performance required, support to use logrotate programme to process your log file
 * @param string $filename
 * @param mixed $content
 * @param float|int $max_size
 * @param int $max_files
 * @param string|null $pad_str
 * @return bool|int
 */
function log_tmp_file($filename, $content, $max_size = 10*1024*1024, $max_files = 5, $pad_str = null){
	$tmp_dir = sys_get_temp_dir();
	$file = $tmp_dir.'/'.$filename;
	return log($file, $content, $max_size, $max_files, $pad_str);
}

/**
 * Create a temporary file
 * @param string $dir The directory where the file is located, default use system temporary directory
 * @param string $prefix The file name prefix
 * @param string $ext The file name suffix
 * @param numeric $mod Permission, default is 777
 * @param bool $unlink_after_shutdown unlink tmp file after process shutdown
 * @return string
 */
function create_tmp_file($dir = '', $prefix = '', $ext = '', $mod = 0777, $unlink_after_shutdown = false){
	$dir = $dir ?: sys_get_temp_dir();
	if(!is_dir($dir) && !mkdir($dir, true)){
		throw new Exception('temp file directory create fail:'.$dir);
	}
	$file_name = $dir.'/'.$prefix.substr(md5(time().rand()), 0, 16).$ext;
	$fp = fopen($file_name, 'a');
	fclose($fp);
	chmod($file_name, $mod);
	if($unlink_after_shutdown){
		register_shutdown_function(function() use ($file_name){
			@unlink($file_name);
		});
	}
	return $file_name;
}

/**
 * Get file upload error message via PHP file upload error number
 * @param int $upload_error_no
 * @return string
 */
function upload_file_error($upload_error_no){
	return [
		UPLOAD_ERR_OK         => 'OK',
		UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
		UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
		UPLOAD_ERR_PARTIAL    => 'The uploaded file was only partially uploaded.',
		UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
		UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
		UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
		UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop; examining the list of loaded extensions with phpinfo() may help.',
	][$upload_error_no] ?: 'Unknown error:'.$upload_error_no;
}

/**
 * Upload file check by option
 * @param string $file
 * @param array $opt
 * @return void
 * @throws \Exception
 */
function upload_file_check($file, $opt = [
	'accept'         => 'image/*',
	//Allowed file formats, please refer to: https://developer.mozilla.org/en-US/docs/Web/HTML/Attributes/accept
	'max_size'       => 0,
	//Maximum file size
	'min_size'       => 0,
	//Minimum file size
	'image_max_size' => [],
	//Maximum image size (width, height)
	'image_min_size' => [],
	//Minimum image size (width, height)
]){
	if(!$file){
		throw new Exception('File is empty');
	}
	if(!is_uploaded_file($file)){
		throw new Exception('File access denied');
	}
	if($opt['accept'] && !file_match_accept($file, $opt['accept'])){
		throw new Exception('File format error');
	}
	if($opt['max_size'] || $opt['min_size']){
		$file_size = filesize($file);
		if($opt['max_size'] && $file_size > $opt['max_size']){
			throw new Exception('File size ('.format_size($opt['max_size']).') exceeds the allowed value');
		}
		if($opt['min_size'] && $file_size < $opt['min_size']){
			throw new Exception('File size ('.format_size($file_size).') is less than the allowed value');
		}
	}
	if($opt['image_max_size'] || $opt['image_min_size']){
		[$w, $h] = getimagesize($file);
		if($opt['image_max_size']){
			[$max_w, $max_h] = $opt['image_max_size'];
			if($max_w && $max_w < $w){
				throw new Exception('Image width ('.$w.'px) exceeds the limit size ('.$max_w.'px)');
			}
			if($max_h && $max_h < $h){
				throw new Exception('Image height ('.$w.'px) exceeds the limit size ('.$max_w.'px)');
			}
		}
		if($opt['image_min_size']){
			[$min_w, $min_h] = $opt['image_min_size'];
			if($min_w && $min_w > $w){
				throw new Exception('Image width ('.$w.'px) exceeds the limit size ('.$min_w.'px)');
			}
			if($min_h && $min_h > $h){
				throw new Exception('Image height ('.$w.'px) exceeds the limit size ('.$min_w.'px)');
			}
		}
	}
}

/**
 * Get a list of extensions that match the specified mime
 * @param string $mime
 * @return string[]
 */
function get_extensions_by_mime($mime){
	return MIME_EXTENSION_MAP[$mime];
}

/**
 * Get mime information by file suffix
 * @param string $ext file suffix
 * @return string[] mime list
 */
function get_mimes_by_extension($ext){
	$ext = strtolower(ltrim($ext, '.'));
	$mime_list = [];
	foreach(MIME_EXTENSION_MAP as $mime => $ext_list){
		if(in_array($ext, $ext_list)){
			$mime_list[] = $mime;
		}
	}
	return $mime_list;
}

/**
 * Check if the given mime information is in the specified extension list
 * This method is usually used to check whether the uploaded file meets the set file type
 * @param string $mime
 * @param string[] $extensions
 * @return bool
 */
function mime_match_extensions($mime, array $extensions){
	return !empty(array_intersect(MIME_EXTENSION_MAP[$mime] ?: [], $extensions));
}

/**
 * Check if the file mime information matches the accept string
 * @param string $mime file mime information
 * @param string $accept <input accept=""/> format reference：https://developer.mozilla.org/en-US/docs/Web/HTML/Attributes/accept
 * @return bool
 */
function mime_match_accept($mime, $accept){
	$acc_list = explode_by(',', $accept);
	foreach($acc_list as $acc){
		if(strcasecmp($acc, $mime) === 0){
			return true;
		}
		[$seg1, $seg2] = explode('/', $acc);
		if($seg2 === '*' && stripos($mime, $seg1."/") === 0){
			return true;
		}
		//后缀模式
		if($acc[0] === '.'){
			$ms = get_mimes_by_extension($acc);
			if(in_array($mime, $ms)){
				return true;
			}
		}
	}
	return false;
}

/**
 * Check if the file matches the specified accept definition
 * @param string $file file
 * @param string $accept <input accept=""/> information, please refer to the format: https://developer.mozilla.org/en-US/docs/Web/HTML/Attributes/accept
 * @return bool
 * @throws \Exception
 */
function file_match_accept($file, $accept){
	$file_ext = resolve_file_extension($file);
	$file_mime = mime_content_type($file);
	$acc_list = explode_by(',', $accept);

	// File mime comparison single accept
	$mime_compare = function($file_mime, $acc){
		[$seg1, $seg2] = explode('/', $acc);
		if($seg2 === '*' && stripos($file_mime, $seg1."/") === 0){
			return true;
		}
		return strcasecmp($acc, $file_mime) === 0;
	};
	foreach($acc_list as $acc){
		//mime模式
		if(strpos($acc, '/') !== false){
			if(!$file_mime){
				$file_mime = mime_content_type($file);
			}
			if($mime_compare($file_mime, $acc)){
				return true;
			}
		}//后缀模式
		else if($acc[0] === '.'){
			if(strcasecmp($file_ext, $acc) === 0){
				return true;
			}
		}else{
			throw new Exception('accept format error: '.$acc);
		}
	}
	return false;
}

/**
 * Extension mapping (from nginx mime.types)
 */
const MIME_EXTENSION_MAP = [
	'text/html'                                                                 => ['html', 'htm', 'shtml'],
	'text/css'                                                                  => ['css'],
	'text/xml'                                                                  => ['xml'],
	'application/javascript'                                                    => ['js'],
	'application/atom+xml'                                                      => ['atom'],
	'application/rss+xml'                                                       => ['rss'],
	'text/mathml'                                                               => ['mml'],
	'text/plain'                                                                => ['txt'],
	'text/vnd.sun.j2me.app-descriptor'                                          => ['jad'],
	'text/vnd.wap.wml'                                                          => ['wml'],
	'text/x-component'                                                          => ['htc'],
	'image/avif'                                                                => ['avif'],
	'image/png'                                                                 => ['png'],
	'image/svg+xml'                                                             => ['svg', 'svgz'],
	'image/tiff'                                                                => ['tif', 'tiff'],
	'image/vnd.wap.wbmp'                                                        => ['wbmp'],
	'image/webp'                                                                => ['webp'],
	'image/gif'                                                                 => ['gif'],
	'image/jpeg'                                                                => ['jpeg', 'jpg'],
	'image/x-icon'                                                              => ['ico'],
	'image/x-jng'                                                               => ['jng'],
	'image/x-ms-bmp'                                                            => ['bmp'],
	'font/woff'                                                                 => ['woff'],
	'font/woff2'                                                                => ['woff2'],
	'application/java-archive'                                                  => ['jar', 'war', 'ear'],
	'application/json'                                                          => ['json'],
	'application/mac-binhex40'                                                  => ['hqx'],
	'application/msword'                                                        => ['doc'],
	'application/pdf'                                                           => ['pdf'],
	'application/postscript'                                                    => ['ps', 'eps', 'ai'],
	'application/rtf'                                                           => ['rtf'],
	'application/vnd.apple.mpegurl'                                             => ['m3u8'],
	'application/vnd.google-earth.kml+xml'                                      => ['kml'],
	'application/vnd.google-earth.kmz'                                          => ['kmz'],
	'application/vnd.ms-excel'                                                  => ['xls'],
	'application/vnd.ms-fontobject'                                             => ['eot'],
	'application/vnd.ms-powerpoint'                                             => ['ppt'],
	'application/vnd.oasis.opendocument.graphics'                               => ['odg'],
	'application/vnd.oasis.opendocument.presentation'                           => ['odp'],
	'application/vnd.oasis.opendocument.spreadsheet'                            => ['ods'],
	'application/vnd.oasis.opendocument.text'                                   => ['odt'],
	'application/vnd.openxmlformats-officedocument.presentationml.presentation' => ['pptx'],
	'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'         => ['xlsx'],
	'application/vnd.openxmlformats-officedocument.wordprocessingml.document'   => ['docx'],
	'application/vnd.wap.wmlc'                                                  => ['wmlc'],
	'application/wasm'                                                          => ['wasm'],
	'application/x-7z-compressed'                                               => ['7z'],
	'application/x-cocoa'                                                       => ['cco'],
	'application/x-java-archive-diff'                                           => ['jardiff'],
	'application/x-java-jnlp-file'                                              => ['jnlp'],
	'application/x-makeself'                                                    => ['run'],
	'application/x-perl'                                                        => ['pl', 'pm'],
	'application/x-pilot'                                                       => ['prc', 'pdb'],
	'application/x-rar-compressed'                                              => ['rar'],
	'application/x-redhat-package-manager'                                      => ['rpm'],
	'application/x-sea'                                                         => ['sea'],
	'application/x-shockwave-flash'                                             => ['swf'],
	'application/x-stuffit'                                                     => ['sit'],
	'application/x-tcl'                                                         => ['tcl', 'tk'],
	'application/x-x509-ca-cert'                                                => ['der', 'pem', 'crt'],
	'application/x-xpinstall'                                                   => ['xpi'],
	'application/xhtml+xml'                                                     => ['xhtml'],
	'application/xspf+xml'                                                      => ['xspf'],
	'application/zip'                                                           => ['zip'],
	'application/octet-stream'                                                  => [
		'bin',
		'exe',
		'dll',
		'deb',
		'dmg',
		'iso',
		'img',
		'msi',
		'msp',
		'msm',
	],
	'audio/midi'                                                                => ['mid', 'midi', 'car'],
	'audio/mpeg'                                                                => ['mp3'],
	'audio/ogg'                                                                 => ['ogg'],
	'audio/x-m4a'                                                               => ['m4a'],
	'audio/x-realaudio'                                                         => ['ra'],
	'video/3gpp'                                                                => ['3gpp', '3gp'],
	'video/mp2t'                                                                => ['ts'],
	'video/mp4'                                                                 => ['mp4'],
	'video/mpeg'                                                                => ['mpeg', 'mpg'],
	'video/quicktime'                                                           => ['mov'],
	'video/webm'                                                                => ['webm'],
	'video/x-flv'                                                               => ['flv'],
	'video/x-m4v'                                                               => ['m4v'],
	'video/x-mng'                                                               => ['mng'],
	'video/x-ms-asf'                                                            => ['asx', 'asf'],
	'video/x-ms-wmv'                                                            => ['wmv'],
	'video/x-msvideo'                                                           => ['avi'],
];
