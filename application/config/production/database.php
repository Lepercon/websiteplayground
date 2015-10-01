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

$active_group = 'live';
$active_record = TRUE;

// live, dur.ac.uk/butler.jcr
$db['live']['hostname'] = "mysql.dur.ac.uk";
$db['live']['username'] = "djb8jcr";
$db['live']['password'] = "VP4GL6dhF3rYdUGB";
$db['live']['database'] = "Xdjb8jcr_jcr_new";
$db['live']['dbdriver'] = "mysql";
$db['live']['dbprefix'] = "";
$db['live']['pconnect'] = TRUE;
$db['live']['db_debug'] = TRUE;
$db['live']['cache_on'] = FALSE;
$db['live']['cachedir'] = "";
$db['live']['char_set'] = "utf8";
$db['live']['dbcollat'] = "utf8_general_ci";

// public
$db['public']['hostname'] = "mysql.dur.ac.uk";
$db['public']['username'] = "nobody";
$db['public']['password'] = "";
$db['public']['database'] = "Pdcl0www_userdata";
$db['public']['dbdriver'] = "mysql";
$db['public']['dbprefix'] = "";
$db['public']['pconnect'] = TRUE;
$db['public']['db_debug'] = TRUE;
$db['public']['cache_on'] = FALSE;
$db['public']['cachedir'] = "";
$db['public']['char_set'] = "utf8";
$db['public']['dbcollat'] = "utf8_general_ci";

//family
$db['family']['hostname'] = "mysql.dur.ac.uk";
$db['family']['username'] = "djb8jcr";
$db['family']['password'] = "VP4GL6dhF3rYdUGB";
$db['family']['database'] = "Xdjb8jcr_family";
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