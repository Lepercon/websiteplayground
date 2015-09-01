<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = 'home';
$route['404_override'] = 'home/error_404';

// redirect feedback to contact
$route['feedback'] = 'contact';

// involved
$route['sports'] = 'involved/index/sports';
$route['societies'] = 'involved/index/societies';
$route['committees'] = 'involved/index/committees';
$route['charity'] = 'charities';

// calendar
$route['calendar/(:num)/(:num)'] = 'events/index/$1/$2';
$route['events/(:num)/(:num)'] = 'events/index/$1/$2';

// photos
$route['photos/(:num)'] = 'photos/index/$1';

// email
$route['contact/(:num)'] = 'contact/user/$1';

// finance
$route['claim'] = 'finance/claims_form';

// details
$route['profile'] = 'details';


$route['posters'] = 'home/banner';



/* End of file routes.php */
/* Location: ./application/config/routes.php */