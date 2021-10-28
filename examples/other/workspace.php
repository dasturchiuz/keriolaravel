<?php
/**
 * Kerio Workspace login.
 *
 * STATUS: In progress, might change in the future
 *
 * This example is currently under development.
 * The example is not intended for stable use yet.
 * Functionality might not be fully verified, documented, or even supported.
 *
 * Please note that changes can be made without further notice.
 *
 * @copyright    Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/../../src/KerioWorkspaceApi.php');
require_once(dirname(__FILE__) . '/../config.php');

$example = new KerioWorkspaceApi($name, $vendor, $version);

print '<h1>Example: Kerio Workspace login</h1>';

/* Main application */
try {
	/* Login */
	$session = $example->login($hostname, $username, $password);
	print 'Logged in.';

}
catch (KerioApiException $error) {

	/* Catch possible errors */
	print $error->getMessage();
}

/* Logout */
if (isset($session)) {
	$example->logout();
}
