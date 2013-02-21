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
| 	example.com/class/method/id/
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
|	$route['scaffolding_trigger'] = 'scaffolding';
|
| This route lets you set a "secret" word that will trigger the
| scaffolding feature for added security. Note: Scaffolding must be
| enabled in the controller in which you intend to use it.   The reserved
| routes must come before any wildcard or regular expression routes.
|
*/

$route['default_controller'] = "home";
$route['scaffolding_trigger'] = "";

$route['offset'] = "home";
$route['offset/(:num)'] = "home/index/$1";
$route['group/(:num)'] = "group/index/$1";
$route['board/(:any)'] = "home/board/$1";
$route['board/(:any)/offset'] = "home/board/$1";
$route['posts/(:any)'] = "home/posts/$1";
$route['posts/(:any)/offset'] = "home/posts/$1";
$route['board/(:any)/offset/(:num)'] = "home/board/$1/$2";
$route['thread/(:num)'] = "thread/view/$1";
$route['thread/(:num)/offset'] = "thread/view/$1";
$route['thread/(:num)/offset/(:num)'] = "thread/view/$1/$2";
$route['thread/(:num)/post/(:num)'] = "thread/gotopost/$1/$2";
$route['user/(:num)'] = "profile/pubp/$1";
$route['user/(:any)'] = "profile/pubp/$1";
$route['chat/channel/(:any)'] = "chat/index/$1";
$route['connect/(:any)'] = "connect/index/$1";
$route['connect/go'] = "connect/go";
$route['r/(:any)'] = "r/index/$1";
$route['settings'] = "profile/settings";
$route['i'] = "image/results";
$route['i/offset/(:num)'] = "image/results/offset/$1";
$route['i/(:any)/edit'] = "image/edit/$1";
$route['i/(:any)'] = "image/index/$1";
$route['dashboard/offset/(:num)'] = "dashboard/index/$1";
$route['tags/(:any)'] = "home/tags/$1";
$route['tags/(:any)/offset'] = "home/tags/$1";

$route['fetch/(:any)/offset/(:num)'] = "/home/fetch/$1/$2";
$route['fetch'] = "/home/fetch";
$route['fetch/offset/(:num)'] = "/home/fetch/all/$1";
$route['fetch/(:any)'] = "/home/fetch/$1";


/* End of file routes.php */
/* Location: ./system/application/config/routes.php */
