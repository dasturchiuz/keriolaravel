<?php
/**
 * Administration API for Kerio Connect - Sample Application.
 *
 * Get unique connections.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/config.php');

/* Get params */
if (isset($_GET['server'])) {
	$serverId = htmlspecialchars($_GET['server']);
}
else {
	die('[]');
}

/* Check server config */
if (empty($servers[$serverId])) {
	die('[]');
}

/* Set server from config */
$hostname = $servers[$serverId]['ip'];
$username = $servers[$serverId]['username'];
$password = $servers[$serverId]['password'];

/* Local function to remove duplicate connections */
function isUnique($array, $compareArray){

	/* Walk through $compareArray and check if $array already in */
	foreach ($compareArray as $compareRecord) {
		$compareRecordHostname = preg_split('/:/', $compareRecord['from']);
		$arrayHostname = preg_split('/:/', $array['from']);

		if (
			($compareRecord['proto'] == $array['proto']) &&
			($compareRecord['extension'] == $array['extension']) &&
			($compareRecord['user'] == $array['user']) &&
			($compareRecordHostname[0] == $arrayHostname[0])) {
				return FALSE;
		}
	}
	return TRUE;
}

/* Local function to remove local connections */
function isPrivateIp($ip){
    $private_ips = array (
		array('10.0.0.0','10.255.255.255'),
		array('172.16.0.0','172.31.255.255'),
		array('192.168.0.0','192.168.255.255')
    );

    $ip = ip2long($ip);
    foreach ($private_ips as $ipr) {
		$min = ip2long($ipr[0]);
		$max = ip2long($ipr[1]);
		if (($ip >= $min) && ($ip <= $max)) return TRUE;
    }

    return FALSE;
}

/* Main application */
require_once(dirname(__FILE__) . '/class/KerioConnect.php');
try {
	/* Login */
	$api = new KerioConnect($name, $vendor, $version);
	$session = $api->login($hostname, $username, $password);

	/* Get connections */
	$connectionList = $api->getConnections();
	$uniqueConnections = array();

	/* Return only unique connections (host, user, proto, extension) */
	foreach ($connectionList as $connection) {
		/* Skip local conntions */
		$ip = preg_split('/:/', $connection['from']);
		if (isPrivateIp($ip[0])) continue;

		/* Save record */
		if (isUnique($connection, $uniqueConnections)) {
			array_push($uniqueConnections, $connection);
		}
	}
	print json_encode($uniqueConnections);

}

/* Catch possible errors */
catch (KerioApiException $error) {
	print '[]';
}

/* Logout */
if(isset($session)) {
	$api->logout();
}
