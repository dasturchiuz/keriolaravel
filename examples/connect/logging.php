<?php
/**
 * Sample Application.
 *
 * Log to a file.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/../class/Logger.php');
require_once(dirname(__FILE__) . '/../class/HtmlHelper.php');

$name = 'Example: Log to a file';

$html = new HtmlHelper();
$html->printHeader($name);
$html->printInfo('An auxiliary class for occasional logging to a file.<br>To display content of the file, click <a href="logging.txt">here</a>.');

$messages = array(
	'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit...',
	'Proin luctus vehicula est, in auctor quam dictum eu.',
	'Nulla sed mi nec ipsum gravida varius ut imperdiet est.',
	'Mauris est urna, tempor eget interdum vel, mollis sit amet est.',
	'Praesent vehicula pulvinar lacus quis commodo.',
	'Nullam gravida est sit amet dolor consequat sit amet luctus lacus ultrices.',
	'Proin faucibus dolor et nisl gravida nec lobortis enim dictum.',
	'Mauris feugiat nisi ac metus pretium a aliquet velit laoreet.',
	'Vestibulum posuere elit eget mauris fermentum aliquam.'
);

/* Main application */
try {
	$log = new Logger();
	$log->setFile('logging.txt');

	$random = array_rand($messages);
	$message = $messages[$random];

	$log->write($message);
	$html->printSuccess(sprintf('Writing a message: %s', $message));

}
catch (Exception $error) {

	/* Catch possible errors */
	$html->printError($error->getMessage());
}

$html->printFooter();
