<?php
/**
 * CSV、电子表格相关操作函数
 * 如果正常开放性业务，建议使用 XLSXBuilder (https://github.com/sasumi/XLSXBuilder)
 * 或类似处理excel的其他技术方案。
 */
namespace LFPhp\Func;

const CSV_LINE_SEPARATOR = PHP_EOL; //CSV行分隔符
const CSV_COMMON_DELIMITER = ','; //默认数据分隔符

/**
 * 获取Excel等电子表格中列名
 * @param integer $column 列序号，由1开始
 * @return string 电子表格中的列名，格式如：A1、E3
 */
function spreadsheet_get_column_index($column){
	$numeric = ($column - 1)%26;
	$letter = chr(65 + $numeric);
	$num2 = intval(($column - 1)/26);
	if($num2 > 0){
		return spreadsheet_get_column_index($num2).$letter;
	}else{
		return $letter;
	}
}

/**
 * 输出CSV文件到浏览器下载
 * @param string $filename 下载文件名
 * @param array $data
 * @param array|string[] $headers 字段列表，格式为：[field=>alias,...]，或 [‘name', 'password'] 纯字符串数组
 * @param string $delimiter 分隔符
 */
function csv_download($filename, $data, array $headers = [], $delimiter = CSV_COMMON_DELIMITER){
	http_header_download($filename);
	if($headers){
		echo join($delimiter, csv_format($headers)).CSV_LINE_SEPARATOR;
	}
	$fields = is_assoc_array($headers) ? array_keys($headers) : [];
	foreach($data as $row){
		if($fields){
			echo join($delimiter, csv_format(array_filter_by_keys($row, $fields))).CSV_LINE_SEPARATOR;
		}else{
			echo join($delimiter, csv_format($row)).CSV_LINE_SEPARATOR;
		}
	}
}

/**
 * 分块输出CSV文件到浏览器下载
 * @param string $filename 下载文件名
 * @param callable $rows_fetcher 数据获取函数，返回二维数组
 * @param array|string[] $headers 字段列表，格式为：[field=>alias,...]，或 [‘name', 'password'] 纯字符串数组
 * @param string $delimiter 分隔符
 */
function csv_download_chunk($filename, callable $rows_fetcher, array $headers = [], $delimiter = CSV_COMMON_DELIMITER){
	http_header_download($filename);
	if($headers){
		echo join($delimiter, csv_format($headers)).CSV_LINE_SEPARATOR;
	}
	$fields = is_assoc_array($headers) ? array_keys($headers) : [];
	while($rows = $rows_fetcher()){
		foreach($rows as $row){
			$row = $fields ? array_filter_by_keys($row, $fields) : $row;
			echo join($delimiter, csv_format($row)), CSV_LINE_SEPARATOR;
		}
	}
}

/**
 * 分块读取CSV文件
 * @param string $file 文件名称
 * @param callable $output 数据输出处理函数，传入参数：rows， 返回参数若为false，则中断读取
 * @param array $headers 字段列表，格式为：[field=>alias,...] 映射字段名
 * @param int $chunk_size 分块大小
 * @param int $start_line 开始读取行数，默认为第1行
 * @param string $delimiter 分隔符
 * @throws \Exception
 */
function csv_read_file_chunk($file, callable $output, $headers = [], $chunk_size = 100, $start_line = 1, $delimiter = CSV_COMMON_DELIMITER){
	$key_size = count($headers);
	$chunk_tmp = [];
	assert_via_exception($chunk_size > 0, 'Chunk size must bigger than 0');
	file_read_by_line($file, function($text, $line_num) use ($delimiter, $output, &$chunk_tmp, $chunk_size, $headers, $key_size, $start_line){
		if($start_line && $line_num < $start_line){
			return null;
		}
		$data = explode($delimiter, $text);
		if($headers){
			$data_size = count($data);
			if($data_size > $key_size){
				$data = array_slice($data, 0, $key_size);
			} else if($data_size < $key_size){
				$data = array_pad($data, count($headers), '');
			}
			$data = array_combine($headers, $data);
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
 * CSV 读取
  * @param string $file 文件路径
 * @param string[] $keys 返回数组key配置，为空返回自然索引数组
 * @param int $start_line 开始读取行数，默认为第1行
 * @param string $delimiter 分隔符
 * @return array 数据，格式为：[[key1=>val, key2=>val, ...], ...]， 如果没有配置key，返回二维自然索引数组
 */
function csv_read_file($file, $keys = [], $start_line = 1, $delimiter = CSV_COMMON_DELIMITER){
	$fp = fopen($file, 'r');
	$ret = [];
	$line_num = 0;
	while($data = fgetcsv($fp, 0, $delimiter)){
		$line_num++;
		if($start_line && $start_line > $line_num){
			continue;
		}
		if($keys && count($data) < count($keys)){
			$data = array_pad($data, count($keys) - count($data), '');
		}
		if($keys){
			$ret[] = array_combine($keys, $data);
		} else {
			$ret[] = $data;
		}
	}
	return $ret;
}

/**
 * 写入文件
 * @param string $file 文件
 * @param array[] $rows 二维数组
 * @param string $delimiter 分隔符
 * @param string $mode 文件打开模式 fopen(, mode)
 */
function csv_save_file($file, $rows, $delimiter = CSV_COMMON_DELIMITER, $mode = 'a+'){
	$fh = fopen($file, $mode);
	csv_save_file_handle($fh, $rows, $delimiter);
	fclose($fh);
}

/**
 * 使用文件句柄方式写入文件（写入完成不会关闭句柄）
 * 相比 csv_save_file()，该函数可以提供给周期性连续写入文件的场景，例如数据流处理
 * @param resource $file_handle 文件句柄
 * @param array[] $rows 二维数组
 * @param string $delimiter 分隔符
 */
function csv_save_file_handle($file_handle, $rows, $delimiter = CSV_COMMON_DELIMITER){
	$buffer_rows = 100; //每100行缓冲成字符串后写入文件，提高写入性能
	$buffer_line = '';
	$bf_counter = 0;
	foreach($rows as $row){
		$buffer_line .= join($delimiter, csv_format($row)).CSV_LINE_SEPARATOR;
		$bf_counter ++;
		if($bf_counter >= $buffer_rows){
			fwrite($file_handle, $buffer_line);
			$buffer_line = '';
		}
	}
	if($buffer_line){
		fwrite($file_handle, $buffer_line);
	}
}

/**
 * 格式化CSV单元格内容
 * @param mixed $val
 * @return string|array
 */
function csv_format($val){
	if(is_array($val)){
		$ret = [];
		foreach($val as $k => $item){
			$ret[$k] = csv_format($item);
		}
		return $ret;
	}
	return '"'.str_replace('"', '""', $val).'"';
}
