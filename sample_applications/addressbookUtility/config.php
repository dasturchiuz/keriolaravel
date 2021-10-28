<?php
/**
 * Sample Configuration.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */

/* Application details */
$name = 'Addressbook.app Utility';
$vendor = 'Kerio Technologies s.r.o.';
$version = '1.4.0.234';

/* Login credentials */
$hostname = '';
$username = '';
$password = '';

/* Login credentials control */
if(empty($hostname) || empty($username) || empty($password)) die('Please, setup your Kerio Connect login credentials in config.php before running Addressbook.app utility.');
