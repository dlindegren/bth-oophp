<?php
/**
 * Config-file for DLINDE. Change settings here to affect installation.
 *
 */

/**
 * Set the error reporting.
 *
 */
error_reporting(-1);              // Report all type of errors
ini_set('display_errors', 1);     // Display all errors 
ini_set('output_buffering', 0);   // Do not buffer outputs, write directly


/**
 * Define DLINDE paths.
 *
 */
define('DLINDE_INSTALL_PATH', __DIR__ . '/..');
define('DLINDE_THEME_PATH', DLINDE_INSTALL_PATH . '/theme/render.php');


/**
 * Include bootstrapping functions.
 *
 */
include(DLINDE_INSTALL_PATH . '/src/bootstrap.php');


/**
 * Start the session.
 *
 */
session_name(preg_replace('/[^a-z\d]/i', '', __DIR__));
session_start();


/**
 * Create the DLINDE variable.
 *
 */
$dlinde = array();

/**
 * Creating the menu.
 *
 */
include ('navigation.php'); //deklarera array
$menu = CNavigation::GenerateMenu($menuItems);

/**
 * Creating the db-connection.
 *
 */
define('DB_USER', 'dald15'); //BTH
define('DB_PASSWORD', '93YN7.Cx'); //BTH
$dlinde ['database']['dsn']             = 'mysql:host=blu-ray.student.bth.se;dbname=dald15;'; 
$dlinde['database']['dsn']            	= 'mysql:host=localhost;dbname=dald15;';
$dlinde ['database']['username']        = DB_USER; 
$dlinde ['database']['password']        = DB_PASSWORD;
$dlinde['database']['driver_options'] 	= array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"); 



/**
 * Site wide settings.
 *
 */
$dlinde['lang']         = 'sv';
$dlinde['title_append'] = ' | RM Moviestore';

$dlinde['menu'] = $menu;

//Header
include "header.php";


$dlinde['footer'] = <<<EOD
<footer><span class='sitefooter'>© 2015 RM Rental Moviestore</span></footer>
EOD;



/**
 * Theme related settings.
 *
 */
//$dlinde['stylesheet'] = 'css/style.css';
$dlinde['stylesheets'] = array(
	'css/bootstrap.min.css',
	'http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300italic,700,300',
	'css/style.css'
);

$dlinde['favicon']    = 'favicon.ico';



/**
 * Settings for JavaScript.
 *
 */
$dlinde['modernizr'] = 'js/modernizr.js';
$dlinde['jquery'] = '//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js';
//$dlinde['jquery'] = null; // To disable jQuery
$dlinde['javascript_include'] = array('js/bootstrap.min.js');
//$dlinde['javascript_include'] = array('js/main.js'); // To add extra javascript files



/**
 * Google analytics.
 *
 */
$dlinde['google_analytics'] = 'UA-22093351-1'; // Set to null to disable google analytics
