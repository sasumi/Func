# Sheet
 > CSV, spreadsheet related operation functions
 > For normal open business, it is recommended to use XLSXBuilder (https://github.com/sasumi/XLSXBuilder)
 > Or other technical solutions similar to processing Excel.

## 1. spreadsheet_get_column_index($column): string
Get the column names in Excel and other spreadsheets
#### Parameters
 - {integer} *$column* column number, starting from 1

#### Returns
 - string The column name in the spreadsheet, format such as: A1, E3

## 2. csv_download($filename,$data,$headers,$delimiter)
Output CSV file to browser for download
#### Parameters
 - {string} *$filename* Download file name
 - {array} *$data* 
 - {array|string[]} *$headers* field list, format: [field=>alias,...], or ['name', 'password'] pure string array
 - {string} *$delimiter* delimiter
## 3. csv_download_chunk($filename,$rows_fetcher,$headers,$delimiter)
Output CSV files in chunks to browser for download
#### Parameters
 - {string} *$filename* Download file name
 - {callable} *$rows_fetcher* data acquisition function, returns a two-dimensional array
 - {array|string[]} *$headers* field list, format: [field=>alias,...], or ['name', 'password'] pure string array
 - {string} *$delimiter* delimiter
## 4. csv_read_file_chunk($file,$output,$headers,$chunk_size,$start_line,$delimiter)
Read CSV file in chunks
#### Parameters
 - {string} *$file* file name
 - {callable} *$output* data output processing function, input parameter: rows, if the return parameter is false, the reading will be interrupted
 - {array} *$headers* field list, format: [field=>alias,...] mapping field name
 - {int} *$chunk_size* chunk size
 - {int} *$start_line* The number of lines to start reading, the default is line 1
 - {string} *$delimiter* delimiter
## 5. csv_read_file($file,$keys,$start_line,$delimiter): array
CSV Reading
#### Parameters
 - {string} *$file* file path
 - {string[]} *$keys* returns the array key configuration, if empty, returns the natural index array
 - {int} *$start_line* The number of lines to start reading, the default is line 1
 - {string} *$delimiter* delimiter

#### Returns
 - array data, the format is: [[key1=>val, key2=>val, ...], ...], if no key is configured, return a two-dimensional natural index array

## 6. csv_save_file($file,$rows,$delimiter,$mode)
Write to file
#### Parameters
 - {string} *$file* file
 - {array[]} *$rows* two-dimensional array
 - {string} *$delimiter* delimiter
 - {string} *$mode* file opening mode fopen(, mode)
## 7. csv_save_file_handle($file_handle,$rows,$delimiter)
Use the file handle method to write to the file (the handle will not be closed after writing is completed)
Compared with csv_save_file(), this function can be used for scenarios where files are written periodically and continuously, such as data stream processing
#### Parameters
 - {resource} *$file_handle* file handle
 - {array[]} *$rows* two-dimensional array
 - {string} *$delimiter* delimiter
## 8. csv_format($val): string|array
Format CSV cell contents
#### Parameters
 - {mixed} *$val* 

#### Returns
 - string|array 



