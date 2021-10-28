MOBILE DEVICES
--------------

Your mobile devices using ActiveSync(r). Discover who uses which device, version or just when the device has been last synchronized.

Configuration
-------------
To configure Mobile Devices, open the config/config.php and edit $servers, e.g.
array(
	'hostname' => '10.0.0.1',
	'username' => 'admin',
	'password' => 'SecretPassword'
),
array(
	'hostname' => '10.0.1.1',
	'username' => 'admin',
	'password' => 'SecretPassword'
),

Security
--------
It is recommended to provide basic secured access to the config.php file and database folder,
eventually the entire application, as described in the main documentation.

Periodical update
-----------------
There is a script cron.php which will update all records automatically. It is only required
to call this script, e.g. by Cron Unix scheduler in certain period of time or run it manually.

# Every hour in week days, Mon-Fri
0 * * * 1-5 root ( php /path/to/kerio-api-php/sample_applications/mobileDevices/cron.php?runUpdate ) 