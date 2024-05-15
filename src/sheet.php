<?php
/**
 * CSV、电子表格相关操作函数
 * 如果正常开放性业务，建议使用 XLSXBuilder (https://github.com/sasumi/XLSXBuilder)
 * 或类似处理excel的其他技术方案。
 */
namespace LFPhp\Func;

/**
 * 获取Excel等电子表格中列名
 * @param integer $column 列序号，由1开始
 * @return string 电子表格中的列名，格式如：A1、E3
 */
function get_spreadsheet_column($column){
	$numeric = ($column - 1)%26;
	$letter = chr(65 + $numeric);
	$num2 = intval(($column - 1)/26);
	if($num2 > 0){
		return get_spreadsheet_column($num2).$letter;
	}else{
		return $letter;
	}
}

/**
 * 输出CSV文件到浏览器下载
 * @param string $download_name 下载文件名
 * @param array $data
 * @param array $fields 字段列表，格式为：[field=>alias,...]
 * @param string $mime_type
 */
function download_csv($download_name, $data, array $fields = [], $mime_type = 'application/vnd.ms-excel'){
	header("Content-Disposition: attachment; filename=\"$download_name\"");
	header("Content-Type: $mime_type");
	csv_output('echo', $data, $fields);
}

/**
 * 分块输出CSV文件到浏览器下载
 * @param string $download_name 下载文件名
 * @param callable $batch_fetcher
 * @param array $fields 字段列表，格式为：[field=>alias,...]
 * @param string $mime_type
 */
function download_csv_chunk($download_name, callable $batch_fetcher, array $fields = [], $mime_type = 'application/vnd.ms-excel'){
	header("Content-Disposition: attachment; filename=\"$download_name\"");
	header("Content-Type: $mime_type");
	csv_output_chunk('echo', $batch_fetcher, $fields);
}

/**
 * CSV 读取
  * @param string $file 文件路径
 * @param array $keys
 * @param int $ignore_head_lines
 * @return array 数据，格式为：[[字段1,字段2,...],...]
 */
function read_csv($file, $keys = [], $ignore_head_lines = 0){
	$fp = fopen($file, 'r');
	$delimiter = ',';
	$ret = [];
	$ln = 0;
	while($data = fgetcsv($fp, 0, $delimiter)){
		if($ignore_head_lines && $ignore_head_lines < $ln){
			$ln++;
			continue;
		}
		$ln++;
		if($keys && count($data) < count($keys)){
			$data = array_pad($data, count($keys) - count($data), '');
		}
		$ret[] = $data;
	}
	return $ret;
}

/**
 * 分块读取CSV文件
 * @param callable $output 数据输出处理函数，传入参数：chunks， 返回参数若为false，则中断读取
 * @param string $file 文件名称
 * @param array $fields 字段列表，格式为：[field=>alias,...] 映射字段名
 * @param int $chunk_size 分块大小
 * @param int $ignore_head_lines 忽略开始头部标题行数
 * @throws \Exception
 */
function read_csv_chunk(callable $output, $file, $fields = [], $chunk_size = 100, $ignore_head_lines = 0, $delimiter = ','){
	$key_size = count($fields);
	$chunk_tmp = [];
	assert_via_exception($chunk_size > 0, 'Chunk size must bigger than 0');
	read_line($file, function($text, $line_num) use ($delimiter, $output, &$chunk_tmp, $chunk_size, $fields, $key_size, $ignore_head_lines){
		if($ignore_head_lines && $line_num <= $ignore_head_lines){
			return null;
		}
		$data = explode($delimiter, $text);
		if($fields){
			$data_size = count($data);
			if($data_size > $key_size){
				$data = array_slice($data, 0, $key_size);
			} else if($data_size < $key_size){
				$data = array_pad($data, count($fields), '');
			}
			$data = array_combine($fields, $data);
		}
		$chunk_tmp[] = $data;
		if(count($chunk_tmp) >= $chunk_size){
			if($output($chunk_tmp) === false){
				return false;
			}
			$chunk_tmp = [];
		}
		return null;
	});
	if($chunk_tmp){
		$output($chunk_tmp);
	}
	unset($chunk_tmp);
}

/**
 * 保存CSV文件
  * @param string $file 文件路径
 * @param array $data
 * @param array $field_map 字段别名映射列表，格式为：[field=>alias,...]
 */
function save_csv($file, $data, array $field_map = []){
	$fh = fopen($file, 'x');
	csv_output(function($line) use ($fh){
		fwrite($fh, $line);
	}, $data, $field_map);
	fclose($fh);
}

/**
 * 分块保存CSV文件
 * @param string $file 文件路径
 * @param callable $data_fetcher
 * @param array $field_map 字段别名映射列表，格式为：[field=>alias,...]
 */
function save_csv_chunk($file, callable $data_fetcher, $field_map = []){
	$fh = fopen($file, 'x');
	csv_output_chunk(function($line) use ($fh){
		fwrite($fh, $line);
	}, $data_fetcher, $field_map);
	fclose($fh);
}

/**
 * 分块输出CSV数据
 * @param callable $output
 * @param callable $batch_fetcher
 * @param array $fields 字段列表，格式为：[field=>alias,...]
 * @param int $uniq_seed
 * @return int 数据行数
 */
function csv_output_chunk(callable $output, callable $batch_fetcher, array $fields = [], &$uniq_seed = 0){
	$comma = "\t";
	$line_sep = PHP_EOL;
	$list = $batch_fetcher();

	//first batch empty, return false
	if(!$list){
		return false;
	}

	if($uniq_seed++ < 1){
		$output($fields ? implode($comma, format_csv_ceil($fields)).$line_sep : implode($comma, array_keys($list[0])).$line_sep);
	}

	$row_count = 0;
	while($list || $list = $batch_fetcher()){
		$row_count += count($list);
		foreach($list as $row){
			if($fields){
				$tmp = [];
				foreach($fields as $field => $alias){
					$tmp[] = format_csv_ceil($row[$field]);
				}
				$output(join($comma, $tmp).$line_sep);
			}else{
				$output(join($comma, format_csv_ceil($row)).$line_sep);
			}
		}
		$list = [];
	}
	return $row_count;
}

/**
 * 输出CSV
 * @param callable $output
 * @param array $data 二维数组
 * @param array $fields 字段列表，格式为：[field=>alias,...]
 * @return bool|int
 */
function csv_output(callable $output, array $data, array $fields = []){
	$tmp = $data;
	return csv_output_chunk($output, function() use ($tmp, &$export_flag){
		return array_shift($tmp);
	}, $fields);
}

/**
 * 格式化CSV单元格内容
 * @param mixed $val
 * @return string|array
 */
function format_csv_ceil($val){
	if(is_array($val)){
		$ret = [];
		foreach($val as $k => $item){
			$ret[$k] = format_csv_ceil($item);
		}
		return $ret;
	}
	return '"'.str_replace('"', '""', $val).'"';
}
