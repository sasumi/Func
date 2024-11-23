# Db
 > Database Operation (PDO) Functions

## 1. db_connect($db_type,$host,$user,$password,$database,$port,$charsets,$persistence_connect): \PDO
Connect to database use PDO
#### Parameters
 - {string} *$db_type* 
 - {string} *$host* 
 - {string} *$user* 
 - {string} *$password* 
 - {string} *$database* 
 - {int|null} *$port* 
 - {string} *$charsets* 
 - {bool} *$persistence_connect* 

#### Returns
 - \PDO 

## 2. db_connect_via_ssh_proxy($db_config,$ssh_config,$proxy_config): \PDO
Connect database via ssh proxy
#### Parameters
 - {array} *$db_config* ['type', 'host', 'user', 'password', 'database', 'port']
 - {array} *$ssh_config* ['host', 'user', 'password'', 'port']
 - {array} *$proxy_config* ['host', 'port']

#### Returns
 - \PDO 

## 3. db_auto_ssh_port($db_config,$port_init): int|mixed
#### Parameters
 - {mixed} *$db_config* 
 - {mixed} *$port_init* 

#### Returns
 - int|mixed 

## 4. db_mysql_connect($host,$user,$password,$database,$port,$charsets,$persistence_connect): \PDO
Connect to MySQL database
#### Parameters
 - {string} *$host* 
 - {string} *$user* 
 - {string} *$password* 
 - {string} *$database* 
 - {null} *$port* 
 - {string} *$charsets* 
 - {bool} *$persistence_connect* 

#### Returns
 - \PDO 

## 5. db_connect_dsn($dsn,$user,$password,$persistence_connect): \PDO
Connect to database via DSN
#### Parameters
 - {string} *$dsn* 
 - {string} *$user* 
 - {string} *$password* 
 - {bool} *$persistence_connect* 

#### Returns
 - \PDO 

## 6. db_build_dsn($db_type,$host,$database,$port,$charsets): string
Build DSN string
#### Parameters
 - {string} *$db_type* 
 - {string} *$host* 
 - {string} *$database* 
 - {string} *$port* 
 - {string} *$charsets* 

#### Returns
 - string 

## 7. db_query($pdo,$sql): false|\PDOStatement
Query SQL
#### Parameters
 - {\PDO} *$pdo* 
 - {string} *$sql* 

#### Returns
 - false|\PDOStatement 

## 8. db_query_all($pdo,$sql): array
Get all records
#### Parameters
 - {\PDO} *$pdo* 
 - {string} *$sql* 

#### Returns
 - array 

## 9. db_query_one($pdo,$sql): array
Get one record
#### Parameters
 - {\PDO} *$pdo* 
 - {string} *$sql* 

#### Returns
 - array 

## 10. db_query_field($pdo,$sql,$field): mixed|null
Get one field
#### Parameters
 - {\PDO} *$pdo* 
 - {string} *$sql* 
 - {string|null} *$field* 

#### Returns
 - mixed|null 

## 11. db_sql_patch_limit($sql,$start_offset,$size): string
Append limit statement to SQL string
#### Parameters
 - {string} *$sql* 
 - {int} *$start_offset* 
 - {int|null} *$size* 

#### Returns
 - string 

## 12. db_query_count($pdo,$sql): int
Query count
#### Parameters
 - {\PDO} *$pdo* 
 - {string} *$sql* 

#### Returns
 - int 

## 13. db_query_paginate($pdo,$sql,$page,$page_size): array
Get by pagination
#### Parameters
 - {\PDO} *$pdo* 
 - {string} *$sql* 
 - {int} *$page* 
 - {int} *$page_size* 

#### Returns
 - array return [list, count]

## 14. db_query_chunk($pdo,$sql,$handler,$chunk_size): bool
Query SQL in chunk mode
#### Parameters
 - {\PDO} *$pdo* 
 - {string} *$sql* 
 - {callable} *$handler* Batch processing function, pass in parameters ($rows, $page, $finish), if false is returned, the execution is interrupted
 - {int} *$chunk_size* 

#### Returns
 - bool Whether it is a normal end, false means that the batch processing function is interrupted

## 15. db_watch($pdo,$sql,$watcher,$chunk_size,$sleep_interval): bool
Watch changed on database records
#### Parameters
 - {\PDO} *$pdo* 
 - {string} *$sql* 
 - {callable} *$watcher* 
 - {int} *$chunk_size* 
 - {int} *$sleep_interval* 

#### Returns
 - bool Whether it is a normal end, false means that the batch processing function is interrupted

## 16. db_quote_value($data): array|string
Field escape, currently only supports strings
#### Parameters
 - {array|string|int} *$data* 

#### Returns
 - array|string 

## 17. db_quote_field($fields): array|string
Database table field escape
#### Parameters
 - {string|array} *$fields* 

#### Returns
 - array|string 

## 18. db_affect_rows($result): int|false
Get the number of rows affected by the query
#### Parameters
 - {\PDOStatement} *$result* 

#### Returns
 - int|false 

## 19. db_insert($pdo,$table,$data): false|int
Insert data
#### Parameters
 - {\PDO} *$pdo* 
 - {string} *$table* 
 - {array} *$data* 

#### Returns
 - false|int 

## 20. db_transaction($pdo,$handler): bool|mixed
Transaction processing
#### Parameters
 - {\PDO} *$pdo* 
 - {callable} *$handler* handler, if it returns false or throws an exception, it will interrupt the submission and perform a rollback operation

#### Returns
 - bool|mixed 



