<?php
/**
 * Administration API for Kerio Operator - Sample Configuration.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */

/* Application details */
$name = 'BLF';
$vendor = 'Kerio Technologies s.r.o.';
$version = '1.4.0.234';

/* Login credentials */
$hostname = '';
$username = '';
$password = '';

/* Login credentials control */
if(empty($hostname) || empty($username) || empty($password)) die('Please, setup your Kerio Operator login credentials in config.php before running BLF.');
