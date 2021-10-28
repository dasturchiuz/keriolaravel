<?php
/**
 * Kerio APIs Client Library for PHP - Config Assistant.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/../../doc/resources/class/JsonConfig.php');
require_once(dirname(__FILE__) . '/class/HtmlForm.php');

$html = new HtmlForm();
$html->setResources('../../doc/resources/');
$html->setBackpage('../../#code-examples', '&lt;&lt; Back');
$html->printHeader('Config Assistant');

$config = new JsonConfig('../../examples/servers.json');

try {
	/* A POST request? */
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$servers = $_POST;
		
		/* Validation check */
		if (empty($_POST['hostname']) || empty($_POST['username']) || empty($_POST['product']))	throw new Exception('Please, enter your login credentials.');
		
		/* Store configuration */
		try {
			$config->set(
				array(
					'product'  => $_POST['product'],
					'hostname' => $_POST['hostname'],
					'username' => $_POST['username'],
					'password' => $_POST['password']
				)
			);
		}
		catch (Exception $e) {
			throw new Exception('It seems there is an issue with write access. Would you like us to <a href="../setup-assistant">help</a> you with this?');
		}
		
		/* Login test */
		try {
			$class = ($_POST['product'] == 'samepage') ? 'SamepageApi' : sprintf('Kerio%sApi', ucfirst($_POST['product']));
			require_once(dirname(__FILE__) . '/../../src/'. $class . '.php');
			$api = new $class('Config Assistant', 'Kerio Technologies s.r.o.', '1.4.0.234');
		
			$session = $api->login($_POST['hostname'], $_POST['username'], $_POST['password']);
		
			/* Result */
			$redirectTo = (isset($_GET['product'])) ? $_POST['product'] : '';
			$html->printSuccess(sprintf('Great! Now you can continue and check out the examples... <a target="_top" href="../../examples/%s">Click here</a>', $redirectTo));
		}
		catch (Exception $e) {
			$html->printWarning($e->getMessage());
		}

	} //endof POST

	$servers = $config->get();
	
	/* Get configuration */
	if (empty($servers)) {
		$html->printInfo('Please, enter your login credentials.');
	}
	else {
		$html->printInfo('Please note that all code examples uses the same login credentials.');
	}

}
catch (Exception $e) {
	$html->printWarning($e->getMessage());
}

/* Logout */
if (isset($session)) $api->logout();

/* Print form */ 
$product = (isset($_GET['product'])) ? $_GET['product'] : '';
$defaults = (isset($servers)) ? $servers : array('product' => $product);

$html->printForm($defaults);

$html->printFooter();

