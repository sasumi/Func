<?php

namespace LFPhp\Func;

use Exception;
use PDO;
use PDOException;

/**
 * 数据库类型，当前只支持MySQL
 */
const DB_TYPE_MYSQL = 'mysql';

/**
 * PDO connect
 * @param string $db_type
 * @param string $host
 * @param string $user
 * @param string $password
 * @param string $database
 * @param int|null $port
 * @param string $charsets
 * @param bool $persistence_connect
 * @return \PDO
 */
function db_connect($db_type, $host, $user, $password, $database, $port = null, $charsets = '', $persistence_connect = false){
	$dsn = db_build_dsn($db_type, $host, $database, $port, $charsets);
	return db_connect_dsn($dsn, $user, $password, $persistence_connect);
}

/**
 * connect database via ssh proxy
 * @desc ssh2 extension required
 * @param $db_config ['type', 'host', 'user', 'password', 'database', 'port']
 * @param $ssh_config ['host', 'user', 'password'', 'port']
 * @param array $proxy_config ['host', 'port']
 * @return \PDO
 * @throws \Exception
 */
function db_connect_via_ssh_proxy($db_config, $ssh_config, $proxy_config = []){
	$ssh_conn = ssh2_connect($ssh_config['host'], $ssh_config['port']);
	if(!ssh2_auth_password($ssh_conn, $ssh_config['user'], $ssh_config['password'])){
		throw new Exception('SSH connect fail');
	}
	$proxy_config = array_merge($proxy_config, [
		'host' => 'localhost',
		'port' => db_auto_ssh_port($db_config),
	]);

	$tunnel = ssh2_tunnel($ssh_conn, $proxy_config['host'], $proxy_config['port']);
	if(!is_resource($tunnel)){
		throw new Exception('SSH tunnel bind error('.$proxy_config['host'].':'.$proxy_config['port'].')');
	}
	return db_connect($db_config['type'],
		$proxy_config['host'],
		$db_config['user'],
		$proxy_config['password'],
		$db_config['database'],
		$proxy_config['port']);
}

function db_auto_ssh_port($db_config, $port_init = 9999){
	static $ps = [];
	$k = serialize($db_config);
	if(!isset($ps[$k])){
		$ls = array_last($ps) ?: ($port_init - 1);
		$ps[$k] = $ls + 1;
	}
	return $ps[$k];
}

/**
 * @param string $host
 * @param string $user
 * @param string $password
 * @param string $database
 * @param null $port
 * @param string $charsets
 * @param bool $persistence_connect
 * @return \PDO
 */
function db_mysql_connect($host, $user, $password, $database, $port = null, $charsets = '', $persistence_connect = false){
	return db_connect(DB_TYPE_MYSQL, $host, $user, $password, $database, $port, $charsets, $persistence_connect);
}

/**
 * @param $dsn
 * @param $user
 * @param $password
 * @param bool $persistence_connect
 * @return \PDO
 */
function db_connect_dsn($dsn, $user, $password, $persistence_connect = false){
	$opt = [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
	];
	if($persistence_connect){
		$opt[PDO::ATTR_PERSISTENT] = true;
	}
	try {
		$conn = new PDO($dsn, $user, $password, $opt);
	} catch(PDOException $e){
		$msg = $e->getMessage();
		if(stripos(PHP_OS, 'win') !== false){
			$msg = mb_convert_encoding($msg, 'utf-8','gbk');
		}
		throw new PDOException($msg."\n[DSN: $dsn]", $e->getCode(), $e->getPrevious());
	}
	return $conn;
}

/**
 * build DSN
 * @param string $db_type
 * @param string $host
 * @param string $database
 * @param string $port
 * @param string $charsets
 * @return string
 */
function db_build_dsn($db_type, $host, $database, $port = '', $charsets = ''){
	$db_type = $db_type ?: 'mysql';
	$dns = "$db_type:host=$host;dbname={$database}";
	if($port){
		$dns .= ";port={$port}";
	}
	if($charsets){
		$dns .= ";charset={$charsets}";
	}
	return $dns;
}

/**
 * db query
 * @param \PDO $pdo
 * @param string $sql
 * @return false|\PDOStatement
 */
function db_query(PDO $pdo, $sql){
	try{
		return $pdo->query($sql);
	}catch(PDOException $e){
		$msg = $e->getMessage();
		if(stripos(PHP_OS, 'win') !== false){
			$msg = mb_convert_encoding($msg, 'utf-8','gbk');
		}
		throw new PDOException($msg."\n[SQL: \"$sql\"]", $e->getCode(), $e->getPrevious());
	}
}

/**
 * db get all
 * @param \PDO $pdo
 * @param string $sql
 * @return array
 */
function db_query_all(PDO $pdo, $sql){
	$result = db_query($pdo, $sql);
	$result->setFetchMode(PDO::FETCH_ASSOC);
	return $result->fetchAll();
}

/**
 * database query one record
 * @param \PDO $pdo
 * @param string $sql
 * @return array
 * @throws \Exception
 */
function db_query_one(PDO $pdo, $sql){
	$sql = db_sql_patch_limit($sql, 0, 1);
	$rows = db_query_all($pdo, $sql);
	return $rows[0] ? $rows[0] : [];
}

/**
 * database query one field
 * @param \PDO $pdo
 * @param string $sql
 * @param string|null $field
 * @return mixed|null
 * @throws \Exception
 */
function db_query_field(PDO $pdo, $sql, $field = null){
	$one = db_query_one($pdo, $sql);
	if(!$one){
		return null;
	}
	return $field ? $one[$field] : array_first($one);
}

/**
 * 追加limit语句到sql上
 * @param string $sql
 * @param int $start_offset
 * @param int|null $size
 * @return string
 * @throws \Exception
 */
function db_sql_patch_limit($sql, $start_offset, $size = null){
	if(preg_match("/limit\s\W+$/", $sql)){
		throw new Exception('SQL already has limitation:'.$sql);
	}
	$sql .= " LIMIT $start_offset".($size ? ",$size" : "");
	return $sql;
}

/**
 * 查询记录数
 * @param \PDO $pdo
 * @param string $sql
 * @return int
 * @throws \Exception
 */
function db_query_count(PDO $pdo, $sql){
	$sql = str_replace(array("\n", "\r"), '', trim($sql));

	//为了避免order中出现field，在select里面定义，select里面被删除了，导致order里面的field未定义。
	//同时提升Count性能
	$sql = preg_replace('/\sorder\s+by\s.*$/i', '', $sql);

	if(preg_match('/^\s*SELECT.*?\s+FROM\s+/i', $sql)){
		if(preg_match('/\sGROUP\s+by\s/i', $sql) || preg_match('/^\s*SELECT\s+DISTINCT\s/i', $sql)){
			$sql = "SELECT COUNT(*) AS __NUM_COUNT__ FROM ($sql) AS cnt_";
		}else{
			$sql = preg_replace('/^\s*select.*?\s+from/i', 'SELECT COUNT(*) AS __NUM_COUNT__ FROM', $sql);
		}
		$result = db_query_one($pdo, $sql);
		if($result){
			return (int)$result['__NUM_COUNT__'];
		}
	}
	return 0;
}

/**
 * 分页查询
 * @param \PDO $pdo
 * @param string $sql
 * @param int $page
 * @param int $page_size
 * @return array [列表, 总数]
 * @throws \Exception
 */
function db_query_paginate(PDO $pdo, $sql, $page, $page_size){
	$total = db_query_count($pdo, $sql);
	if(!$total){
		return [[], 0];
	}
	$start = ($page - 1)*$page_size;
	$sql = db_sql_patch_limit($sql, $start, $page_size);
	return [db_query_all($pdo, $sql), $total];
}

/**
 * 分块读取
 * @param \PDO $pdo
 * @param string $sql
 * @param callable $handler 批次处理函数，传入参数($rows, $page, $finish)，如返回false，则中断执行
 * @param int $chunk_size
 * @return bool 是否为正常结束，false表示为批处理函数中断导致
 * @throws \Exception
 */
function db_query_chunk(PDO $pdo, $sql, callable $handler, $chunk_size = 100){
	$page = 1;
	$total = db_query_count($pdo, $sql);
	while($rows = db_query_all($pdo, db_sql_patch_limit($sql, ($page - 1)*$chunk_size, $chunk_size))){
		if($handler($rows, $page, ceil($total/$chunk_size), $total) === false){
			return false;
		}
		$page++;
	}
	return true;
}

/**
 * 数据监听
 * @param \PDO $pdo
 * @param string $sql
 * @param callable $watcher 批次处理函数，传入参数($rows)，如返回false，则中断执行
 * @param int $chunk_size 分块大小
 * @param int $sleep_interval 睡眠间隔时间（秒）
 * @return bool 是否为正常结束，false表示为批处理函数中断导致
 * @throws \Exception
 */
function db_watch(PDO $pdo, $sql, callable $watcher, $chunk_size = 50, $sleep_interval = 3){
	while(true){
		$hit = false;
		$keep = db_query_chunk($pdo, $sql, function($rows) use ($watcher, &$hit){
			$hit = true;
			if(call_user_func($watcher, $rows) === false){
				return false;
			}
			return true;
		}, $chunk_size);
		if($keep === false){
			return false;
		}
		if(!$hit){
			sleep($sleep_interval);
		}
	}
	return true;
}

/**
 * 字段转义，目前仅支持字符串
 * @param array|string|int $data
 * @return array|string
 */
function db_quote_value($data){
	if(is_array($data)){
		foreach($data as $k => $item){
			$data[$k] = db_quote_value($item);
		}
	}else if(is_string($data)){
		return "'".addslashes($data)."'";
	}
	return $data;
}

/**
 * 数据库表字段转义
 * @param string|array $fields
 * @return array|string
 * @throws \Exception
 */
function db_quote_field($fields){
	if(is_array($fields)){
		foreach($fields as $k => $field){
			$fields[$k] = db_quote_field($field);
		}
	}else if(!is_string($fields)){
		throw new Exception('Fields must be array or string');
	}else if(strpos($fields, ' ')){
		$tmp = explode(' ', $fields);
		$f = array_shift($tmp);
		return db_quote_field($f).' '.join($tmp);
	}
	return $fields;
}

/**
 * 获取查询影响行数
 * @param \PDOStatement $result
 * @return int|false
 */
function db_affect_rows($result = null){
	return $result ? $result->rowCount() : false;
}

/**
 * sql prepare
 * @param mixed ...$args
 * @return mixed|string|null
 */
function db_sql_prepare(...$args){
	$statement = isset($args[0]) ? $args[0] : null;
	$args = array_slice($args, 1);
	if($args && $statement){
		$arr = explode('?', $statement);
		$rst = '';
		foreach($args as $key => $val){
			if(is_array($val)){
				$val = db_quote_value($val);
				if(!empty($val)){
					$rst .= $arr[$key].'('.join(',', $val).')';
				}else{
					$rst .= $arr[$key].'(NULL)'; //This will never match, since nothing is equal to null (not even null itself.)
				}
			}else{
				$rst .= $arr[$key].db_quote_value($val);
			}
		}
		$rst .= array_pop($arr);
		$statement = $rst;
	}
	return $statement;
}

/**
 * 删除数据
 * @param \PDO $pdo
 * @param $limit
 * @param $table
 * @param mixed ...$statement
 * @return false|int
 * @throws \Exception
 */
function db_delete(PDO $pdo, $limit, $table, ...$statement){
	$table = db_quote_field($table);
	$sql = db_sql_prepare("DELETE FROM {$table}", $statement);
	$sql = db_sql_patch_limit($sql, 0, $limit);
	$result = db_query($pdo, $sql);
	return db_affect_rows($result);
}

/**
 * 插入数据
 * @param \PDO $pdo
 * @param $table
 * @param array $data
 * @return false|int
 * @throws \Exception
 */
function db_insert(PDO $pdo, $table, array $data){
	$fields = join(',', db_quote_field(array_keys(current($data))));
	$sql = "INSERT INTO ".db_quote_field($table)." ($fields) VALUES";
	$comma = '';
	$data_list = count($data) == count($data, 1) ? array($data) : $data;
	foreach($data_list as $row){
		$str = array();
		foreach($row as $val){
			$str[] = $val !== null ? db_quote_value($val) : 'NULL';
		}
		$value_str = implode(",", $str);
		$sql .= $comma."($value_str)";
		$comma = ',';
	}
	$result = db_query($pdo, $sql);
	return db_affect_rows($result);
}

/**
 * 更新数据
 * @param \PDO $pdo
 * @param array $data
 * @param string $table
 * @param mixed ...$statement
 * @return false|\PDOStatement
 * @throws \Exception
 */
function db_update(PDO $pdo, array $data, $table, ...$statement){
	$data_list = count($data) == count($data, 1) ? array($data) : $data;
	$sets = array();
	foreach($data_list as $row){
		$sets = array();
		foreach($row as $field_name => $value){
			$field_name = db_quote_field($field_name);
			if($value === null){
				$sets[] = "$field_name = NULL";
			}else{
				$sets[] = "$field_name = ".db_quote_value($value);
			}
		}
	}
	$sql = 'UPDATE '.db_quote_field($table).' SET '.implode(',', $sets).db_sql_prepare($statement);
	return db_query($pdo, $sql);
}

/**
 * increase specified field
 * @todo to be test
 * @param \PDO $pdo
 * @param $table
 * @param $increase_field
 * @param int $increment_count
 * @param mixed ...$statement
 * @return false|\PDOStatement
 * @throws \Exception
 */
function db_increase(PDO $pdo, $table, $increase_field, $increment_count = 1, ...$statement){
	$table = db_quote_field($table);
	$increase_field = db_quote_field($increase_field);
	$offset_str = ($increment_count > 0 ? "+" : "-").$increase_field;
	$sql = db_sql_prepare("UPDATE $table SET $increase_field = $increase_field $offset_str", $statement);
	return db_query($pdo, $sql);
}

/**
 * 事务处理
 * @param \PDO $pdo
 * @param callable $handler 处理器，如果返回false或抛出异常，将中断提交，执行回滚操作
 * @return bool|mixed
 * @throws \Exception
 */
function db_transaction(PDO $pdo, callable $handler){
	$result = null;
	try{
		$pdo->beginTransaction();
		$result = call_user_func($handler);
		if($result === false){
			return false;
		}
		$pdo->commit();
	}catch(Exception $e){
		$pdo->rollBack();
		throw $e;
	} finally {
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, true);
	}
	return $result;
}