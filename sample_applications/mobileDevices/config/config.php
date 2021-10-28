<?php
/**
 * Mobile Devices using ActiveSync - Sample Configuration.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */

/* Servers configuration */
$servers = array(
	array(
		'hostname' => '',
		'username' => '',
		'password' => ''
	)
);

/* Database */
$db_file = 'db/database.db';

if (empty($servers[0]['hostname'])) die('Please, setup your Kerio Connect servers in config.php before running Mobile Devices. For more information see README.txt');

