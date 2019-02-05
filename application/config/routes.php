<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/


$route['default_controller'] = 'web';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;


/* My Router */
$route['login'] 									= 'web/index';
$route['forget'] 									= 'web/forget';
$route['register'] 									= 'web/register';

$route['panel'] 									= 'user/user/index';
$route['panel/out']									= 'user/user/logout';
$route['panel/setting']								= 'user/user/setting';
$route['panel/profile']								= 'user/user/profile';
$route['panel/notification']						= 'user/user/notification';
$route['panel/message']								= 'user/user/message';

$route['panel/profile/edit/person'] 				= 'user/user/edit_person';

$route['panel/profile/edit/bio'] 					= 'user/user/edit_bio';

$route['panel/profile/edit/experience'] 			= 'user/user/edit_experience';
$route['panel/profile/edit/experience/edit/(:num)'] = 'user/user/single_experience/$1';
$route['panel/profile/edit/experience/delete/(:num)']= 'user/form/delete_experience/$1';

$route['panel/profile/edit/education'] 				= 'user/user/edit_education';
$route['panel/profile/edit/education/edit/(:num)'] = 'user/user/single_education/$1';
$route['panel/profile/edit/education/delete/(:num)']= 'user/form/delete_education/$1';

$route['panel/profile/edit/skills'] 				= 'user/user/edit_skills';
$route['panel/profile/edit/skills/edit/(:num)'] 	= 'user/user/single_skills/$1';
$route['panel/profile/edit/skills/delete/(:num)']	= 'user/form/delete_skills/$1';

$route['panel/profile/edit/project'] 				= 'user/user/edit_project';
$route['panel/profile/edit/project/edit/(:num)'] 	= 'user/user/single_project/$1';
$route['panel/profile/edit/project/delete/(:num)']	= 'user/form/delete_project/$1';


$route['user/(:any)']								= 'web/profile/$1';