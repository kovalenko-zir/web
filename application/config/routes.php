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
$route['default_controller'] = 'Api/view';
$route['404_override'] = '';
$route['api/fb_login_and_registration'] = '/api/social_network/fb';
$route['api/g_login_and_registration'] =  '/api/social_network/google';
$route['success'] = '/api/registration_successful/success';
$route['failed'] = '/api/registration_successful/failed';
$route['buy/(:any)'] = 'purchases/buy/$1';
$route['download_digital_copy/(:any)/(:any)'] = 'purchases/download_digital_copy/$1/$2';
$route['add_card'] = 'Purchases/add_credit_card';
$route['delete_card'] = 'Purchases/delete_credit_card';
$route['direct'] = 'Purchases/direct_payment';
$route['pay_check'] = 'purchases/payment_check';
$route['paypal'] = 'purchases/listener';
$route['get_cards'] = 'api/get_all_users_payment_cards';
$route['translate_uri_dashes'] = FALSE;
