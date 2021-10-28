MY SERVERS
----------

A single place with all of your servers where you can easily see their installed versions.

Periodical update
-----------------
There is a script cron.php which will update all records automatically. It is only required
to call this script, e.g. by Cron Unix scheduler in certain period of time or run it manually.

# Every hour in week days, Mon-Fri
0 * * * 1-5 root ( php /path/to/kerio-api-php/sample_applications/myServers/cron.php ) 

Security
--------
It is highly recommended to protect access to this application because anyone who knows or
just guesses the URL might be able to login to your Web Administrations.

Basic security settings may be done in the include/config.php

$allowLogin  = true; // Allow guest to login to the Web Administration
$allowAdd    = true; // Allow guest to add new server
$allowRemove = true; // Allow guest to remove server

Other
-----
Make sure that db folder is writeable.
