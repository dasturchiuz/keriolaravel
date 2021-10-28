<?php
/**
 * Administration API for Kerio Connect - Sample Application.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/src/KerioConnectApi.php');

/*
 * Set your Kerio Connect account
 */
$hostname = '';
$username = '';
$password = '';

$api = new KerioConnectApi('Sample application', 'Kerio Technologies s.r.o.', '1.4.0.234');

/* Main application */
try {

    /* Login */
    $session = $api->login($hostname, $username, $password);

    /*
     * You can continue writing code here
     * and add your custom code, e.g.
     * print fooBar();
     */

    /* Get who am I ? */
    $response = $api->sendRequest('Session.whoAmI');
    $fullname = $response['userDetails']['fullName'];

    printf('Success. You are logged in as <b>%s</b> using the Administration API.', $fullname);

} catch (KerioApiException $error) {

    /* Catch possible errors */
    print $error->getMessage();
}

/* Logout */
if (isset($session)) {
    $api->logout();
}
