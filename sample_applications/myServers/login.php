<?php
/**
 * Administration API for Kerio Connect - Sample Application.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/class/Server.php');
require_once(dirname(__FILE__) . '/include/config.php');

if (FALSE === $allowLogin) die('Sorry. Login not allowed.');

$Server = new Server($db);

if (isset($_GET['id'])) {
	foreach($Server->getServersFromDb($_GET['id']) as $server) {
		$hostname = $server['hostname'];
		$username = $server['username'];
		$password = $server['password'];
		$product  = $server['product'];
		$productname  = $server['productname'];
		$version  = $server['version'];
	}
	if (empty($hostname)) die('Invalid server id.');
	
	switch ($product) {
		case 'connect': $port = 4040; break;
		case 'control': $port = 4081; break;
		case 'operator': $port = 4021; break;
		case 'workspace': $port = 4060; break;
		case 'directory': $port = 4101; break;
		default: $port = 443;
	}
	
	$productversion = sprintf('%s %s', $productname, $version);
	$url = '/admin/dologin.php';

	/* Kerio Connect */
	if (preg_match('/^Kerio Connect 8*/', $productversion)) {
		$url = '/admin/login/dologin';
	}
	
	/* Kerio Control */
	if (preg_match('/Kerio Control 7.4*/', $productversion)) {
		$url = '/admin/internal/dologin.php';
	}
	elseif (preg_match('/Kerio Control 8*/', $productversion)) {
		$url = '/admin/internal/dologin.php';
	}

	/* Kerio Workspace */
	if (preg_match('/Kerio Workspace/', $productversion)) {
		$url = '/admin/api/login';
	}
	
	$url = sprintf('https://%s:%d%s', $hostname, $port, $url);
}
else {
	die("Missing server id.");
}

?>

<html>
<head>
<title>Auto-login form</title>
</head>

<body onload="document.forms[0].submit(); return false;">
<p>If you were not redirected in a few seconds, please click <a href="<?php print $url; ?>">here</a>.</p>
<form action="<?php print $url; ?>" method="post">
	<input name="kerio_username" type="hidden" value="<?php print $username; ?>"><br />
	<input name="kerio_password" type="hidden" value="<?php print $password; ?>"><br />
</form>

</body>
</html>