<?php
/**
 * My Servers - Sample Configuration.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */

/* Basic ACL */
$allowLogin  = true; // Allow guest to login to the Web Administration
$allowAdd    = true; // Allow guest to add new server
$allowRemove = true; // Allow guest to remove server

/* Database */
$db = 'db/database.db';
$Server = new Server($db);
