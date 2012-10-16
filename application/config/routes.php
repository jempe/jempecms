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
| There area two reserved routes:
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

$route['default_controller'] = "jempe";
$route['404_override'] = '';

$route['sitemap.xml'] = "admin/sitemap";
$route['jempe_uploader.xml'] = "admin/jempe_uploader";
$route['jempe_is_unique.xml'] = "admin/jempe_is_unique";

$route['admin/install'] = "install/index" ;
$route['admin/install/create_db'] = "install/create_db" ;

$route['admin/tohtml/'] = "publish/tohtml";
$route['admin/tohtml/:any'] = "publish/tohtml";

$route['admin'] = "admin/index";
$route['admin/([a-z_]+)'] = "admin/$1";
$route['admin/file_manager'] = "admin/image_manager";
$route['admin/([a-z_]+).xml'] = "admin/$1";
$route['admin/([a-z_]+).js'] = "admin/$1";
$route['admin/([a-z_]+)/:any'] = "admin/$1";

$route[':any'] = "jempe/index";



/* End of file routes.php */
/* Location: ./application/config/routes.php */