<?php

namespace LFPhp\Func;

/**
 * 获取Excel等电子表格中列名
 * @param string $column 列序号，由1开始
 * @return string
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
 * export csv download
 * @param $download_name
 * @param $data
 * @param array $fields
 * @param string $mime_type
 */
function download_sheet($download_name, $data, array $fields = [], $mime_type = 'application/vnd.ms-excel'){
	header("Content-Disposition: attachment; filename=\"$download_name\"");
	header("Content-Type: $mime_type");
	csv_output('echo', $data, $fields);
}

function download_sheet_chunk($download_name, callable $batch_fetcher, array $fields = [], $mime_type = 'application/vnd.ms-excel'){
	header("Content-Disposition: attachment; filename=\"$download_name\"");
	header("Content-Type: $mime_type");
	csv_output_chunk('echo', $batch_fetcher, $fields);
}

/**
 * @param $file
 * @param array $keys
 * @param int $ignore_head_lines
 * @return array
 */
function read_csv($file, $keys = [], $ignore_head_lines = 0){
	$fp = fopen($file, 'r');
	$delimiter = ',';
	$ret = [];
	$ln = 0;
	while($str = fgetcsv($fp, 0, $delimiter)){
		if($ignore_head_lines && $ignore_head_lines < $ln){
			$ln++;
			continue;
		}
		$ln++;
		$data = explode($delimiter, $str);
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
 * @param array $fields 映射字段名
 * @param int $chunk_size 分块大小
 * @param int $ignore_head_lines 忽略开始头部标题行数
 * @throws \Exception
 */
function read_csv_chunk(callable $output, $file, $fields = [], $chunk_size = 100, $ignore_head_lines = 0){
	$delimiter = ',';
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
 * save csv file
 * @param $file
 * @param $data
 * @param array $fields
 */
function save_csv($file, $data, array $fields = []){
	$fh = fopen($file, 'x');
	csv_output(function($line) use ($fh){
		fwrite($fh, $line);
	}, $data, $fields);
	fclose($fh);
}

/**
 * save csv chunk
 * @param $file
 * @param callable $batch_fetcher
 * @param array $fields
 */
function save_csv_chunk($file, callable $batch_fetcher, $fields = []){
	$fh = fopen($file, 'x');
	csv_output_chunk(function($line) use ($fh){
		fwrite($fh, $line);
	}, $batch_fetcher, $fields);
	fclose($fh);
}

/**
 * csv output chunk
 * @param callable $output
 * @param callable $batch_fetcher
 * @param array $fields
 * @param int $uniq_seed
 * @return bool|int
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
 * @param $data
 * @param array $fields
 * @return bool|int
 */
function csv_output(callable $output, array $data, array $fields = []){
	$tmp = $data;
	return csv_output_chunk($output, function() use ($tmp, &$export_flag){
		return array_unshift($tmp);
	}, $fields);
}

/**
 * format csv cell data
 * @param $str
 * @return array|string|string[]|null
 */
function format_csv_ceil($str){
	if(is_array($str)){
		foreach($str as $k => $v){
			$str[$k] = format_csv_ceil($v);
		}
		return $str;
	}
	$str = preg_replace("/\t/", "\\t", $str);
	$str = preg_replace("/\r?\n/", "\\n", $str);
	return $str;
}
