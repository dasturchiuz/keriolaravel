<?php
/**
 * Administration API for Kerio Connect - Sample Application.
 *
 * Check SSL certificates and expiration date
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/../config.php');
require_once(dirname(__FILE__) . '/../class/MyApi.php');
require_once(dirname(__FILE__) . '/../class/HtmlHelper.php');

$name = 'Example: Check SSL certificates';
$api = new MyApi($name, $vendor, $version);

$html = new HtmlHelper();
$html->printHeader($name);
$html->printInfo('Users say that browser is reporting the site as invalid? Check this to avoid the certificates expire without notice.');

/* Main application */
try {
	/* Login */
	$session = $api->login($hostname, $username, $password);

	/* Get SSL certificates */
	$result = $api->sendRequest('ConnectCertificate.get');
	
	foreach ($result['certificates'] as $certificate) {
		$name = $certificate['subject'][0]['value'];

		/* Skip invalid certificates */
		if (empty($name)) continue;
		if (1 != $certificate['valid']) continue;
		
		/* Skip certificate requests */
		if ('CertificateRequest' == $certificate['type']) continue;
		
		/* Prepare valid date&time */
		$validToDate = $certificate['validPeriod']['validToDate'];
		$validToTime = $certificate['validPeriod']['validToTime'];
		
		$today = strtotime(date('Y-m-d'));
		$expires = strtotime(date(sprintf('%d-%d-%d', $validToDate['year'], $validToDate['month'], $validToDate['day'])));
		
		/* Check if already expired */
		$isExpired = ($expires < $today) ? TRUE : FALSE;
		$daysToExpire = round(abs($expires - $today) /60/60/24);

		$formatExpireDate = date('d.m.Y H:i', strtotime(sprintf('%d.%d.%d %d:%d',
			$validToDate['day'], $validToDate['month'], $validToDate['year'],
			$validToTime['hour'], $validToTime['min'])));
		
		/* Distinguish valid and expired certificates */
		$displayInfo = ($isExpired)
			? sprintf('Certificate for %s is expired, valid to %s.</li>', $name, $formatExpireDate)
			: sprintf('Certificate for %s expires in %d day(s), valid to %s.</li>', $name, $daysToExpire, $formatExpireDate);
		
		/* Make the active certificate bold */
		if ('ActiveCertificate' == $certificate['type']) {
			$displayInfo = sprintf('<b>%s</b>', $displayInfo);
		}
		/* Make expired certificates gray */
		if ($isExpired) {
			$displayInfo = sprintf('<font color="gray">%s</font>', $displayInfo);
		}
		/* Make certificate red if expires within 5 days */
		if (5 > $daysToExpire) {
			$displayInfo = sprintf('<font color="red">%s</font>', $displayInfo);
		}

		/* Print info */
		printf('<li>%s</li>', $displayInfo);
	}
}
catch (KerioApiException $error) {

	/* Catch possible errors */
	$html->printError($error->getMessage());
}

/* Logout */
if (isset($session)) {
	$api->logout();
}

$html->printFooter();
