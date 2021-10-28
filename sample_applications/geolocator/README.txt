GEOLOCATOR
----------

Geolocator is a simple application that displays markers on Google Map based
on the GPS coordinates of the incoming client connection represented by IP address.

Connections are grouped to one marker if all the hostname, username,
protocol and extension (of the particular established connection) are identical.
Then it is presumed that it is a single connection with other data channels opened.

Configuration
-------------
To configure Geolocator, open config.php and edit $servers, e.g.

array(
	'name' => 'My Server',
	'ip' => '10.0.0.1',
	'username' => 'admin',
	'password' => 'SecretPassword'
),
array(
	'name' => 'Other Server',
	'ip' => '10.0.1.1',
	'username' => 'admin',
	'password' => 'SecretPassword'
),

Security
--------
It is recommended to provide basic secured access to config.php and getConections.php files,
eventually the entire application, as described in the main documentation.