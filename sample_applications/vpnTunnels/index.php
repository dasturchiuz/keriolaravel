<?php
/**
 * Administration API for Kerio Control - Sample Application.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/../../src/KerioControlApi.php');
require_once(dirname(__FILE__) . '/../../doc/resources/class/Html.php');

/*
 * Set your Kerio Control account
 */
$hostname = '';
$username = '';
$password = '';

$name = 'VPN Tunnels';
$api = new KerioControlApi($name, 'Kerio Technologies s.r.o.', '1.4.0.234');

$html = new Html();
$html->setResources('../../doc/resources/');
$html->printHeader($name);

/* Main application */
try {

    /* Login */
    $session = $api->login($hostname, $username, $password);
    

    /* A POST request for dial/hangup? */
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    	$id = htmlspecialchars($_POST['id']);
    	$action = htmlspecialchars($_POST['action']);

    	try {
	    	switch ($action) {
	    		case dial:
		    	case hangup:
		    		$api->sendRequest('Interfaces.'.$action, array('id' => $id)); break;
	    		default:
	    	}
    	}
    	catch (KerioApiException $error) {
    		$html->printWarning($error->getMessage());
    	}
    }

    $params = array(
    	'sortByGroup' => true,
		'query' => array(
			'combining' => 'Or',
	    	'conditions' => array(array(
	    		'fieldName' => 'type',
	    		'comparator' => 'Eq',
	    		'value' => 'VpnTunnel'
	    	)),
			'orderBy' => array(array(
				'columnName' => 'name',
				'direction' => 'Asc'
			))
		)
	);
    $result = $api->sendRequest('Interfaces.get', $params);

    if (count($result['list'])) {
    	
    	/* Result table */
    	print '<table>'
    		. '<thead>'
    		. '  <td width="80%"><b>Name</b></td>'
    		. '  <td width="10%"><b>Status</b></td>'
    		. '  <td width="10%">&nbsp;</th>'
    		. '</thead>'
    		. '<tbody>';
    	
    	/* Generate table */
	    foreach ($result['list'] as $interface) {
    		print '<tr>';
    		printf('<td style="border-bottom: 1px dotted #CCC;">%s</td>', $interface['name']);
    		printf('<td style="border-bottom: 1px dotted #CCC;">%s</td>', $interface['linkStatus']);

    		if ($interface['linkStatus'] == 'Down') {
    			$action = 'dial';
    			$buttonLabel = 'Connect';
    		}
    		else {
    			$action = 'hangup';
    			$buttonLabel = 'Disconnect';
    		}
    		
    		printf('<td style="border-bottom: 1px dotted #CCC;"><form method="POST" action=""><input type="submit" value="%s" style="width: 80px"><input type="hidden" name="id" value="%s"><input type="hidden" name="action" value="%s"></form></td>', $buttonLabel, $interface['id'], $action);

    		print '</tr>';
	    }
	    
	    print '</tbody></table>';
	    
    }
    else {
    	$html->printInfo('There is no VPN tunnel.');
    }
}
catch (KerioApiException $error) {

	/* Catch possible errors */
	$html->printWarning($error->getMessage());
}

/* Logout */
if (isset($session)) {
	$api->logout();
}

$html->printFooter();

