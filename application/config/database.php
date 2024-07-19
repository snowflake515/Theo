<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');
/*
  | -------------------------------------------------------------------
  | DATABASE CONNECTIVITY SETTINGS
  | -------------------------------------------------------------------
  | This file will contain the settings needed to access your database.
  |
  | For complete instructions please consult the 'Database Connection'
  | page of the User Guide.
  |
  | -------------------------------------------------------------------
  | EXPLANATION OF VARIABLES
  | -------------------------------------------------------------------
  |
  |	['hostname'] The hostname of your database server.
  |	['username'] The username used to connect to the database
  |	['password'] The password used to connect to the database
  |	['database'] The name of the database you want to connect to
  |	['dbdriver'] The database type. ie: mysql.  Currently supported:
  mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
  |	['dbprefix'] You can add an optional prefix, which will be added
  |				 to the table name when using the  Active Record class
  |	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
  |	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
  |	['cache_on'] TRUE/FALSE - Enables/disables query caching
  |	['cachedir'] The path to the folder where cache files should be stored
  |	['char_set'] The character set used in communicating with the database
  |	['dbcollat'] The character collation used in communicating with the database
  |				 NOTE: For MySQL and MySQLi databases, this setting is only used
  | 				 as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7
  |				 (and in table creation queries made with DB Forge).
  | 				 There is an incompatibility in PHP with mysql_real_escape_string() which
  | 				 can make your site vulnerable to SQL injection if you are using a
  | 				 multi-byte character set and are running versions lower than these.
  | 				 Sites using Latin-1 or UTF-8 database character set and collation are unaffected.
  |	['swap_pre'] A default table prefix that should be swapped with the dbprefix
  |	['autoinit'] Whether or not to automatically initialize the database.
  |	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
  |							- good for ensuring strict SQL while developing
  |
  | The $active_group variable lets you choose which connection group to
  | make active.  By default there is only one group (the 'default' group).
  |
  | The $active_record variables lets you determine whether or not to load
  | the active record class
 */

$db_debug = env('APP_DB_DEBUG') == 'true';  
 
$server_db = env('THEO_DB_HOST');
$user_db = env('THEO_DB_USER');
$pass_db = env('THEO_DB_PASSWORD'); 

 
$active_group = 'theo';
$active_record = TRUE;
$db['theo']['hostname'] = $server_db;
$db['theo']['username'] = $user_db;
$db['theo']['password'] = $pass_db;
$db['theo']['database'] = 'noblemd20';
$db['theo']['dbdriver'] = 'postgre';
$db['theo']['dbprefix'] = '';
$db['theo']['pconnect'] = FALSE;
$db['theo']['db_debug'] = $db_debug;
$db['theo']['cache_on'] = FALSE;
$db['theo']['cachedir'] = '';
$db['theo']['char_set'] = 'utf8';
$db['theo']['dbcollat'] = 'utf8_general_ci';
$db['theo']['swap_pre'] = '';
$db['theo']['autoinit'] = FALSE;
$db['theo']['stricton'] = FALSE;


 
$server_db = env('APP_DB_HOST');
$user_db = env('APP_DB_USER');
$pass_db = env('APP_DB_PASSWORD');

$active_group = 'user';
$active_record = TRUE;
$db['user']['hostname'] = 'Driver={SQL Server};Server='.$server_db.';Database=Wellness_eCast_Data;Uid='.$user_db.';Pwd='.$pass_db.';';
$db['user']['username'] = $user_db;
$db['user']['password'] = $pass_db;
$db['user']['database'] = 'Wellness_eCast_Data';
$db['user']['dbdriver'] = 'odbc';
$db['user']['dbprefix'] = 'dbo.';
$db['user']['pconnect'] = FALSE;
$db['user']['db_debug'] = $db_debug;
$db['user']['cache_on'] = TRUE;
$db['user']['cachedir'] = '';
$db['user']['char_set'] = 'utf8';
$db['user']['dbcollat'] = 'utf8_general_ci';
$db['user']['swap_pre'] = '';
$db['user']['autoinit'] = FALSE;
$db['user']['stricton'] = FALSE;

$active_group = 'audit';
$active_record = TRUE;

$db['audit']['hostname'] = 'Driver={SQL Server};Server='.$server_db.';Database=Wellness_eCastEMR_Audit;Uid='.$user_db.';Pwd='.$pass_db.';';
$db['audit']['username'] = $user_db;
$db['audit']['password'] = $pass_db;
$db['audit']['database'] = 'Wellness_eCastEMR_Audit';
$db['audit']['dbdriver'] = 'odbc';
$db['audit']['dbprefix'] = 'dbo.';
$db['audit']['pconnect'] = FALSE;
$db['audit']['db_debug'] = $db_debug;
$db['audit']['cache_on'] = TRUE;
$db['audit']['cachedir'] = '';
$db['audit']['char_set'] = 'utf8';
$db['audit']['dbcollat'] = 'utf8_general_ci';
$db['audit']['swap_pre'] = '';
$db['audit']['autoinit'] = TRUE;
$db['audit']['stricton'] = FALSE;

$active_group = 'data';
$active_record = TRUE;

$db['data']['hostname'] = 'Driver={SQL Server};Server='.$server_db.';Database=Wellness_eCastEMR_Data;Uid='.$user_db.';Pwd='.$pass_db.';';
$db['data']['username'] = $user_db;
$db['data']['password'] = $pass_db;
$db['data']['database'] = 'Wellness_eCastEMR_Data';
$db['data']['dbdriver'] = 'odbc';
$db['data']['dbprefix'] = 'dbo.';
$db['data']['pconnect'] = FALSE;
$db['data']['db_debug'] = $db_debug;
$db['data']['cache_on'] = TRUE;
$db['data']['cachedir'] = '';
$db['data']['char_set'] = 'utf8';
$db['data']['dbcollat'] = 'utf8_general_ci';
$db['data']['swap_pre'] = '';
$db['data']['autoinit'] = TRUE;
$db['data']['stricton'] = FALSE;


$active_group = 'template';
$active_record = TRUE;

$db['template']['hostname'] = 'Driver={SQL Server};Server='.$server_db.';Database=Wellness_eCastEMR_Template;Uid='.$user_db.';Pwd='.$pass_db.';';
$db['template']['username'] = $user_db;
$db['template']['password'] = $pass_db;
$db['template']['database'] = 'Wellness_eCastEMR_Template';
$db['template']['dbdriver'] = 'odbc';
$db['template']['dbprefix'] = 'dbo.';
$db['template']['pconnect'] = FALSE;
$db['template']['db_debug'] = $db_debug;
$db['template']['cache_on'] = FALSE;
$db['template']['cachedir'] = '';
$db['template']['char_set'] = 'utf8';
$db['template']['dbcollat'] = 'utf8_general_ci';
$db['template']['swap_pre'] = '';
$db['template']['autoinit'] = TRUE;
$db['template']['stricton'] = FALSE;


$active_group = 'image';
$active_record = TRUE;

$db['image']['hostname'] = 'Driver={SQL Server};Server='.$server_db.';Database=Wellness_eCastEMR_Images;Uid='.$user_db.';Pwd='.$pass_db.';';
$db['image']['username'] = $user_db;
$db['image']['password'] = $pass_db;
$db['image']['database'] = 'Wellness_eCastEMR_Images';
$db['image']['dbdriver'] = 'odbc';
$db['image']['dbprefix'] = 'dbo.';
$db['image']['pconnect'] = FALSE;
$db['image']['db_debug'] = $db_debug;
$db['image']['cache_on'] = FALSE;
$db['image']['cachedir'] = '';
$db['image']['char_set'] = 'utf8';
$db['image']['dbcollat'] = 'utf8_general_ci';
$db['image']['swap_pre'] = '';
$db['image']['autoinit'] = TRUE;
$db['image']['stricton'] = FALSE;

$active_group = 'master';
$active_record = TRUE;
$db['master']['hostname'] = 'Driver={SQL Server};Server='.$server_db.';Database=eCastMaster;Uid='.$user_db.';Pwd='.$pass_db.';';
$db['master']['username'] = $user_db;
$db['master']['password'] = $pass_db;
$db['master']['database'] = 'eCastMaster';
$db['master']['dbdriver'] = 'odbc';
$db['master']['dbprefix'] = 'dbo.';
$db['master']['pconnect'] = FALSE;
$db['master']['db_debug'] = $db_debug;
$db['master']['cache_on'] = FALSE;
$db['master']['cachedir'] = '';
$db['master']['char_set'] = 'utf8';
$db['master']['dbcollat'] = 'utf8_general_ci';
$db['master']['swap_pre'] = '';
$db['master']['autoinit'] = TRUE;
$db['master']['stricton'] = FALSE;




