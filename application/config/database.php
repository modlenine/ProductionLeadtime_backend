<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if($_SERVER['HTTP_HOST'] == "localhost"){
	$mysqlServer = "192.168.20.21";
}else{
	$mysqlServer = "192.168.20.21";
}

$active_group = 'default';
$query_builder = TRUE;

$db['default'] = array(
	'dsn'	=> '',
	'hostname' => $mysqlServer,
	'username' => 'ant',
	'password' => 'Ant1234',
	'database' => 'mcp',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);


// Mix Database
$db['mix'] = array(
	'dsn'	=> '',
	'hostname' => $mysqlServer,
	'username' => 'ant',
	'password' => 'Ant1234',
	'database' => 'msd_mix',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);

// Mix Database
$db['ext'] = array(
	'dsn'	=> '',
	'hostname' => $mysqlServer,
	'username' => 'ant',
	'password' => 'Ant1234',
	'database' => 'prodplan',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);

$db['mssql_prodplan'] = array(
	'dsn' => '',
	'hostname' => "192.168.10.54",
	'username' => 'dataconnector',
	'password' => 'Admin1234',
	'database' => 'SLC_STD',
	'dbdriver' => 'sqlsrv',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => TRUE,
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
   );

