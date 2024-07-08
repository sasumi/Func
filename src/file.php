<?php
/**
 * 文件相关操作函数
 */
namespace LFPhp\Func;

use DirectoryIterator;
use Exception;

/**
 * 递归的glob
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

	//修正目录分隔符
	array_walk($files, function(&$file){
		$file = str_replace(array('/', '\\'), array(DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR), $file);
	});
	return $files;
}

/**
 * 递归的unlink
 * @param string $path 需要删除的文件夹
 * @param bool $verbose 是否打印调试信息
 * @return void
 */
function unlink_recursive($path, $verbose = false){
	if(!is_readable($path)){
		return;
	}
	if(is_file($path)){
		if($verbose){
			echo "unlink: {$path}\n";
		}
		if(!unlink($path)){
			throw new \RuntimeException("Failed to unlink {$path}: ".var_export(error_get_last(), true));
		}
		return;
	}
	$foldersToDelete = array();
	$filesToDelete = array();
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
			throw new \RuntimeException("Failed to unlink {$file}: ".var_export(error_get_last(), true));
		}
	}
	if($verbose){
		echo "rmdir: {$path}\n";
	}
	if(!rmdir($path)){
		throw new \RuntimeException("Failed to rmdir {$path}: ".var_export(error_get_last(), true));
	}
}

/**
 * 检查文件是否存在，且名称严格匹配大小写
 * @param string $file
 * @return bool|null
 * true：文件存在，false：文件不存在，null：文件存在但大小写不一致
 */
function file_exists_case_sensitive($file){
	//Linux文件系统严格匹配大小写，因此直接使用is_file判断即可
	if(!server_in_windows()){
		return !!is_file($file);
	}

	//windows如果文件不存在，不需要检查
	if(!is_file($file)){
		return false;
	}
	$r_file = str_replace('\\', '/', $file);
	$realpath = str_replace('\\', '/', realpath($r_file));
	return strcmp($r_file, $realpath) == 0 ? true : null;
}

/**
 * 断言文件包含于指定文件夹中（文件必须存在）
 * @param string $file
 * @param string $dir
 * @param string $exception_class
 */
function assert_file_in_dir($file, $dir, $exception_class = Exception::class){
	assert_via_exception(file_in_dir($file, $dir), 'File access deny', $exception_class);
}

/**
 * 判断文件是否包含于指定文件夹中
 * @param string $file_path 文件路径
 * @param string $dir_path 目录路径
 * @return bool 文件不存在目录当中，或文件实际不存在
 */
function file_in_dir($file_path, $dir_path, $ignore_file_and_dir_exists = false){
	if(!$ignore_file_and_dir_exists){
		$file_path = realpath($file_path);
		$dir_path = realpath($dir_path);
	}else{
		$file_path = resolve_absolute_path($file_path);
		$dir_path = resolve_absolute_path($dir_path);
	}
	//windows 平台不区分文件名称大小写
	if(stripos(PHP_OS, 'win') !== false){
		return stripos($file_path, $dir_path) === 0;
	}
	return strpos($file_path, $dir_path) === 0;
}

/**
 * 解析路径字符串真实路径，去除相对路径信息
 * 相对于realpath，该函数不需要检查文件是否存在
 * <pre>
 * 调用格式：resolve_absolute_path("c:/a/b/./../../windows/system32");
 * 返回：c:/windows/system32
 * @param string $file_or_path 目录路径或文件路径字符串
 * @return string
 */
function resolve_absolute_path($file_or_path){
	$file_or_path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $file_or_path);
	$parts = array_filter(explode(DIRECTORY_SEPARATOR, $file_or_path), 'strlen');
	$absolutes = array();
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
 * 根据文件名获取文件扩展
 * @param string $filename 文件名
 * @param bool $to_lower_case 是否转换成小写，缺省为转换为小写
 * @return string|null string or null,no extension detected
 */
function resolve_file_extension($filename, $to_lower_case = true){
	if(strpos($filename, '.') <= 0){
		return null;
	}
	$tmp = explode('.', $filename);
	return $to_lower_case ? strtolower(end($tmp)) : end($tmp);
}

/**
 * 检查文件是否存在，且名称允许大小写混淆
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

/**
 * 递归拷贝目录
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
 * 批量创建目录
 * @param string[] $dirs 目录路径列表
 * @param bool $break_on_error 是否在创建失败时抛出异常
 * @param int $permissions 目录缺省权限
 * @return string[] 创建失败的目录清单，成功则返回空数组
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
 * 获取模块文件夹列表
 * @param string $dir
 * @return array
 **/
function get_dirs($dir){
	$dir_list = array();
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
 * 获取文件行数
 * @param string|resource $file 文件路径或文件句柄
 * @param string $line_separator 换行符
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
 * 文件tail功能
 * @param string|resource $file
 * @param int $lines 读取行数
 * @param int $buffer 缓冲大小
 * @return string[] 每一行内容
 * @throws \Exception
 */
function tail($file, $lines = 10, $buffer = 4096){
	$TAIL_NL = "\n";
	// Open the file
	if(is_resource($file) && (get_resource_type($file) == 'file' || get_resource_type($file) == 'stream')){
		$f = $file;
	}elseif(is_string($file)){
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
 * 逐行读取文件
 * @param string $file 文件名称
 * @param callable $handle 处理函数，传入参数：($line_str, $line), 若函数返回false，则中断处理
 * @param int $start_line 开始读取行数（由 1 开始）
 * @param int $buff_size 缓冲区大小
 * @return bool 是否为处理函数中断返回
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
 * 渲染PHP文件
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
 * 递归查询文件夹大小
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
 * log 记录到文件
 * @param string $file 文件
 * @param mixed $content 记录内容
 * @param float|int $max_size 单文件最大尺寸，默认
 * @param int $max_files 最大记录文件数
 * @param string|null $pad_str 记录文件名追加字符串
 * @return bool|int 文件是否记录成功
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
 * 读取文件锁
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
 * 写入文件锁
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
 * 初始化文件锁
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
 * 创建临时文件
 * @param string $dir 文件所在目录
 * @param string $prefix 文件名前缀
 * @param string $ext 文件名后缀
 * @param numeric $mod 权限，缺省为777
 * @return string
 */
function create_tmp_file($dir = null, $prefix = '', $ext = '', $mod = 0777){
	$dir = $dir ?: sys_get_temp_dir();
	if(!is_dir($dir) && !mkdir($dir, true)){
		throw new Exception('temp file directory create fail:'.$dir);
	}
	$file_name = $dir.'/'.$prefix.substr(md5(time().rand()), 0, 16).$ext;
	$fp = fopen($file_name, 'a');
	fclose($fp);
	chmod($file_name, $mod);
	return $file_name;
}

/**
 * @param string $file 文件
 * @param array $opt 控制选项
 * @return void
 * @throws \Exception
 */
function upload_file_check($file, $opt = [
	'accept'         => 'image/*', //允许文件格式，具体请参考：https://developer.mozilla.org/en-US/docs/Web/HTML/Attributes/accept
	'max_size'       => 0, //最大文件大小
	'min_size'       => 0, //最小文件大小
	'image_max_size' => [0, 0], //图片最大尺寸(宽,高)
	'image_min_size' => [0, 0], //图片最小尺寸(宽,高)
]){
	if(!$file){
		throw new Exception('文件为空');
	}
	if(!is_uploaded_file($file)){
		throw new Exception('文件禁止访问');
	}
	if($opt['accept'] && !file_match_accept($file, $opt['accept'])){
		throw new Exception('文件格式错误');
	}
	if($opt['max_size'] || $opt['min_size']){
		$file_size = filesize($file);
		if($opt['max_size'] && $file_size > $opt['max_size']){
			throw new Exception('文件大小('.format_size($opt['max_size']).')超过允许值');
		}
		if($opt['min_size'] && $file_size < $opt['min_size']){
			throw new Exception('文件大小('.format_size($file_size).')小于允许值');
		}
	}
	if($opt['image_max_size'] || $opt['image_min_size']){
		list($w, $h) = getimagesize($file);
		if($opt['image_max_size']){
			list($max_w, $max_h) = $opt['image_max_size'];
			if($max_w && $max_w < $w){
				throw new Exception('图片宽度('.$w.'px)超过限制大小('.$max_w.'px)');
			}
			if($max_h && $max_h < $h){
				throw new Exception('图片高度('.$w.'px)超过限制大小('.$max_w.'px)');
			}
		}
		if($opt['image_min_size']){
			list($min_w, $min_h) = $opt['image_min_size'];
			if($min_w && $min_w > $w){
				throw new Exception('图片宽度('.$w.'px)超过限制大小('.$min_w.'px)');
			}
			if($min_h && $min_h > $h){
				throw new Exception('图片高度('.$w.'px)超过限制大小('.$min_w.'px)');
			}
		}
	}
}

/**
 * 获取匹配指定mime的扩展名列表
 * @param string $mime
 * @return string[]
 */
function get_extensions_by_mime($mime){
	return MIME_EXTENSION_MAP[$mime];
}

/**
 * 通过文件后缀获取mime信息
 * @param string $ext 文件后缀
 * @return string[] mime 列表
 */
function get_mimes_by_extension($ext){
	$ext = strtolower(ltrim($ext, '.'));
	$mime_list = [];
	foreach(MIME_EXTENSION_MAP as $mime=>$ext_list){
		if(in_array($ext, $ext_list)){
			$mime_list[] = $mime;
		}
	}
	return $mime_list;
}

/**
 * 检查给定mime信息，是否在指定扩展名清单中
 * 该方法通常用于检查上传文件是否符合设定文件类型
 * @param string $mime
 * @param string[] $extensions
 * @return bool
 */
function mime_match_extensions($mime, array $extensions){
	return !empty(array_intersect(MIME_EXTENSION_MAP[$mime] ?: [], $extensions));
}

/**
 * 检测文件mime信息是否匹配accept字符串
 * @param string $mime 文件mime信息
 * @param string $accept <input accept=""/> 信息，格式请参考：https://developer.mozilla.org/en-US/docs/Web/HTML/Attributes/accept
 * @return bool
 */
function mime_match_accept($mime, $accept){
	$acc_list = explode_by(',', $accept);
	foreach($acc_list as $acc){
		if(strcasecmp($acc, $mime) === 0){
			return true;
		}
		list($seg1, $seg2) = explode('/', $acc);
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
 * 检测文件是否匹配指定accept定义
 * @param string $file_name 文件路径
 * @param string $accept <input accept=""/> 信息，格式请参考：https://developer.mozilla.org/en-US/docs/Web/HTML/Attributes/accept
 * @return bool
 * @throws \Exception
 */
function file_match_accept($file_name, $accept){
	$file_ext = resolve_file_extension($file_name);
	$file_mime = null;
	$acc_list = explode_by(',', $accept);

	//文件mime对比单个accept
	$mime_compare = function($file_mime, $acc){
		list($seg1, $seg2) = explode('/', $acc);
		if($seg2 === '*' && stripos($file_mime, $seg1."/") === 0){
			return true;
		}
		return strcasecmp($acc, $file_mime) === 0;
	};
	foreach($acc_list as $acc){
		//mime模式
		if(strpos($acc, '/') !== false){
			if(!$file_mime){
				$file_mime = mime_content_type($file_name);
			}
			if($mime_compare($file_mime, $acc)){
				return true;
			}
		}
		//后缀模式
		else if($acc[0] === '.'){
			if(strcasecmp($file_ext, $acc)===0){
				return true;
			}
		} else {
			throw new Exception('accept 格式错误：'.$acc);
		}
	}
	return false;
}

/**
 * 扩展名映射（来自于nginx mime.types）
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
	'audio/midi'                                                                => ['mid', 'midi', 'kar'],
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
