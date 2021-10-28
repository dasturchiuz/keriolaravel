<?php
/**
 * Administration API for Kerio Connect - Sample Configuration.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */

/* Application details */
$name = 'GeoIP Locator';
$vendor = 'Kerio Technologies s.r.o.';
$version = '1.4.0.234';

/* Server configuration */
$servers = array(
	array(
		'name' => '',
		'ip' => '',
		'username' => '',
		'password' => ''
	)
);

if (empty($servers[0]['name'])) die('Please, setup your Kerio Connect servers in config.php before running Geolocator. For more information see README.txt');
