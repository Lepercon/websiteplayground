<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the "Database Connection"
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|    ['hostname'] The hostname of your database server.
|    ['username'] The username used to connect to the database
|    ['password'] The password used to connect to the database
|    ['database'] The name of the database you want to connect to
|    ['dbdriver'] The database type. ie: mysql.  Currently supported:
                 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|    ['dbprefix'] You can add an optional prefix, which will be added
|                 to the table name when using the  Active Record class
|    ['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|    ['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|    ['cache_on'] TRUE/FALSE - Enables/disables query caching
|    ['cachedir'] The path to the folder where cache files should be stored
|    ['char_set'] The character set used in communicating with the database
|    ['dbcollat'] The character collation used in communicating with the database
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the "default" group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/

$active_group = 'dev';
$active_record = TRUE;

$db['dev']['hostname'] = "localhost";
$db['dev']['username'] = "root";
$db['dev']['password'] = "root";
$db['dev']['database'] = "xdjb8jcr_jcr_new";
$db['dev']['dbdriver'] = "mysqli";
$db['dev']['dbprefix'] = "";
$db['dev']['pconnect'] = TRUE;
$db['dev']['db_debug'] = TRUE;
$db['dev']['cache_on'] = FALSE;
$db['dev']['cachedir'] = "";
$db['dev']['char_set'] = "utf8";
$db['dev']['dbcollat'] = "utf8_general_ci";

// public
$db['public']['hostname'] = "mysql.dur.ac.uk";
$db['public']['username'] = "nobody";
$db['public']['password'] = "";
$db['public']['database'] = "Pdcl0www_userdata";
$db['public']['dbdriver'] = "mysqli";
$db['public']['dbprefix'] = "";
$db['public']['pconnect'] = TRUE;
$db['public']['db_debug'] = TRUE;
$db['public']['cache_on'] = FALSE;
$db['public']['cachedir'] = "";
$db['public']['char_set'] = "utf8";
$db['public']['dbcollat'] = "utf8_general_ci";

//family
$db['family']['hostname'] = "localhost";
$db['family']['username'] = "root";
$db['family']['password'] = "root";
$db['family']['database'] = "xdjb8jcr_family";
$db['family']['dbdriver'] = "mysqli";
$db['family']['dbprefix'] = "";
$db['family']['pconnect'] = TRUE;
$db['family']['db_debug'] = TRUE;
$db['family']['cache_on'] = FALSE;
$db['family']['cachedir'] = "";
$db['family']['char_set'] = "utf8";
$db['family']['dbcollat'] = "utf8_general_ci";

/* End of file database.php */
/* Location: ./system/application/config/database.php */