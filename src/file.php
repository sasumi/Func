<?php
/**
 * 文件相关操作函数
 * User: sasumi
 * Date: 2015/5/8
 * Time: 14:07
 */
namespace LFPhp\Func;

use Exception;

/**
 * 递归的glob
 * Does not support flag GLOB_BRACE
 * @param $pattern
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
 * 检查文件是否存在，且名称严格匹配大小写
 * @param $file
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
 * 断言文件包含于指定文件夹中
 * @param string $file
 * @param string $dir
 * @param string $exception_class
 */
function assert_file_in_dir($file, $dir, $exception_class = Exception::class){
	assert_via_exception(file_in_dir($file, $dir), 'File access deny', $exception_class);
}

/**
 * 判断文件是否包含于指定文件夹中
 * @param string $file
 * @param string $dir
 * @return bool
 */
function file_in_dir($file, $dir){
	$dir = realpath($dir);
	$file = realpath($file);
	return strpos($file, $dir) === 0;
}

/**
 * 解析路径字符串真实路径，去除相对路径信息
 * 相对于realpath，该函数不需要检查文件是否存在
 * <pre>
 * 调用格式：resolve_absolute_path("c:/a/b/./../../windows/system32");
 * 返回：c:/windows/system32
 * @param string $path 路径字符串
 * @return string
 */
function resolve_absolute_path($path){
	$path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
	$parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
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
 * @param $file
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
 * @param $src
 * @param $dst
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
 * 回溯读取文件
 * @param string $file 文件
 * @param callable $callback 行处理函数
 * @param int $line_limit
 * @param string $line_separator 换行符
 */
function tail($file, callable $callback, $line_limit = 0, $line_separator = "\n"){
	$file_size = filesize($file);
	$fp = fopen($file, 'rb');
	$offset = 0;
	$text = '';
	$line_count = 0;
	while(($offset++) < $file_size){
		if(fseek($fp, -$offset, SEEK_END) === -1){
			break;
		}
		$t = fgetc($fp);
		if($t === $line_separator){
			//中断支持
			if($callback($text) === false){
				break;
			};
			$text = '';

			//行数限制
			if($line_limit && $line_count++ > $line_limit){
				break;
			}
		}else{
			$text = $t.$text;
		}
	}
	fclose($fp);
}

/**
 * 逐行读取文件
 * @param string $file 文件名称
 * @param callable $handle 处理函数，传入参数：($line_str, $line), 若函数返回false，则中断处理
 * @param int $buff_size 缓冲区大小
 * @return bool 是否为处理函数中断返回
 */
function read_line($file, callable $handle, $buff_size = 1024){
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
				if($handle($text, ++$read_line_counter) === false){
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
 * 递归查询文件夹大小
 * @param $path
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
