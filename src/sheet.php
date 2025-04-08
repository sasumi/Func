<?php
/**
 * CSV, spreadsheet related operation functions
 * For normal open business, it is recommended to use XLSXBuilder (https://github.com/sasumi/XLSXBuilder)
 * Or other technical solutions similar to processing Excel.
 */
namespace LFPhp\Func;

const CSV_LINE_SEPARATOR = PHP_EOL; //CSV line separator
const CSV_COMMON_DELIMITER = ','; //Default data separator
const CSV_HEADER_UTF8_BOM = "\xEF\xBB\xBF"; //csv file require BOM

/**
 * Get the column names in Excel and other spreadsheets
 * @param integer $column column number, starting from 1
 * @return string The column name in the spreadsheet, format such as: A1, E3
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
 * Output CSV file to browser for download
 * @param string $filename Download file name
 * @param array $data
 * @param array|string[] $headers field list, format: [field=>alias,...], or ['name', 'password'] pure string array
 * @param string $delimiter delimiter
 */
function csv_download($filename, $data, array $headers = [], $delimiter = CSV_COMMON_DELIMITER){
	http_header_download($filename);
	echo CSV_HEADER_UTF8_BOM;
	$headers && csv_rows($headers, false, $delimiter);
	$fields = is_assoc_array($headers) ? array_keys($headers) : [];
	foreach($data as $row){
		csv_rows([$fields ? array_filter_fields($row, $fields) : $row], false, $delimiter);
	}
}

/**
 * Output CSV files in chunks to browser for download
 * @param string $filename Download file name
 * @param callable $rows_fetcher data acquisition function, returns a two-dimensional array
 * @param array|string[] $headers field list, format: [field=>alias,...], or ['name', 'password'] pure string array
 * @param string $delimiter delimiter
 */
function csv_download_chunk($filename, callable $rows_fetcher, array $headers = [], $delimiter = CSV_COMMON_DELIMITER){
	http_header_download($filename);
	echo CSV_HEADER_UTF8_BOM;
	$headers && csv_rows($headers, false, $delimiter);
	$fields = is_assoc_array($headers) ? array_keys($headers) : [];
	while($rows = $rows_fetcher()){
		foreach($rows as $row){
			csv_rows([$fields ? array_filter_fields($row, $fields) : $row], false, $delimiter);
		}
	}
}

/**
 * Output CSV data in rows
 * @param array[] $rows two-dimensional array
 * @param bool $as_return Whether to return the result as a string
 * @param string $delimiter delimiter
 * @return string|null If $as_return is true, return the result as a string
 */
function csv_rows($rows, $as_return = false, $delimiter = CSV_COMMON_DELIMITER){
	$ret = '';
	foreach($rows as $row){
		$str = join($delimiter, csv_format($row)).CSV_LINE_SEPARATOR;
		if($as_return){
			$ret .= $str;
		}else{
			echo $str;
		}
	}
	if($as_return){
		return $ret;
	}
	return null;
}

/**
 * Read CSV file in chunks
 * @param string $file file name
 * @param callable $output data output processing function, input parameter: rows, if the return parameter is false, the reading will be interrupted
 * @param array $headers field list, format: [field=>alias,...] mapping field name
 * @param int $chunk_size chunk size
 * @param int $start_line The number of lines to start reading, the default is line 1
 * @param string $delimiter delimiter
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
			}else if($data_size < $key_size){
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
 * CSV Reading
 * @param string $file file path
 * @param string[] $keys returns the array key configuration, if empty, returns the natural index array
 * @param int $start_line The number of lines to start reading, the default is line 1
 * @param string $delimiter delimiter
 * @return array data, the format is: [[key1=>val, key2=>val, ...], ...], if no key is configured, return a two-dimensional natural index array
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
		}else{
			$ret[] = $data;
		}
	}
	return $ret;
}

/**
 * Write to file
 * @param string $file file
 * @param array[] $rows two-dimensional array
 * @param string $delimiter delimiter
 * @param string $mode file opening mode fopen(, mode)
 */
function csv_save_file($file, $rows, $delimiter = CSV_COMMON_DELIMITER, $mode = 'a+'){
	$fh = fopen($file, $mode);
	fwrite($fh, CSV_HEADER_UTF8_BOM);
	csv_save_file_handle($fh, $rows, $delimiter);
	fclose($fh);
}

/**
 * Use the file handle method to write to the file (the handle will not be closed after writing is completed)
 * Compared with csv_save_file(), this function can be used for scenarios where files are written periodically and continuously, such as data stream processing
 * @param resource $file_handle file handle
 * @param array[] $rows two-dimensional array
 * @param string $delimiter delimiter
 */
function csv_save_file_handle($file_handle, $rows, $delimiter = CSV_COMMON_DELIMITER){
	$buffer_rows = 100; // Buffer every 100 lines into strings and write them to the file to improve writing performance
	$buffer_line = '';
	$bf_counter = 0;
	foreach($rows as $row){
		$buffer_line .= csv_rows([$row], true, $delimiter);
		$bf_counter++;
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
 * Format CSV cell contents
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
