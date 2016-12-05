<?php
/**
 * Gezmo
 *
 * PHP version 5.3+ / Zend Framework 2+
 *
 * @category  PHP
 * @package   Gezmo
 * @author    Ge Zuidema <Gezmo@gezmo.info>
 * @copyright 2015 Gezmo
 * @link      http://www.gezmo.info
 */

// Define
define( 'REQUEST_MICROTIME', microtime(true) );
	
// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Timezone
date_default_timezone_set( 'Europe/Amsterdam' );
 
// Extend include path
if (APPLICATION_ENV == 'gez')
{
	set_include_path(get_include_path() . ':' . __DIR__ . '/../vendor/zendframework/zendframework/library');
}
else
{
	set_include_path(get_include_path() . ';' . __DIR__ . '/../vendor/zendframework/zendframework/library');
}
chdir(dirname(__DIR__));

// Composer autoload
require 'vendor/autoload.php';

// Run the application!
Zend\Mvc\Application::init(include 'config/application.config.php')->run();

?>
