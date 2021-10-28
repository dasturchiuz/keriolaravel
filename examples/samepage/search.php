<?php
/**
 * Samepage - Sample Application.
 *
 * Invite users from a file.
 *
 * STATUS: In progress, might change in the future
 *
 * This api is currently under development.
 * The api is not intended for stable use yet.
 * Functionality might not be fully verified, documented, or even supported.
 *
 * Please note that changes can be made without further notice.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/../../src/SamepageApi.php');
require_once(dirname(__FILE__) . '/../class/HtmlHelper.php');
require_once(dirname(__FILE__) . '/../config.php');

$name = 'Example: Search';
$api = new SamepageApi($name, $vendor, $version);

$html = new HtmlHelper();
$html->printHeader($name);
$html->printInfo('Always want to search accros all spaces or just the one specific? No problem.');

print '<form class="prettyprint" method="POST" action="">';
print 'Query: <input type="text" size="30" name="query">';
print '<input type="submit" value="Search">';
print '</form>';

/* Main application */
try {
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		/* Query check */
		$query = (isset($_POST['query'])) ? htmlspecialchars($_POST['query']) : '';
		if (empty($query)) throw new KerioApiException('Please, set your query.');

		/* Login */
		$api->login($hostname, $username, $password);

		/* Search */
		$params = array('query' => $query, 'limit' => 100);
		$result = $api->sendRequest('Search.run', $params);
	
		/* Parse response */
		print '<h2>Top 100 results</h2>';
		if (count($result['items']) > 0) {
			print '<ul>';
			foreach ($result['items'] as $item) {
				$description = (isset($item['highlightedText'][0])) ? $item['highlightedText'][0] : '';
				printf('<li><a target="_blank" href="https://%s/%s/#page-%d">%s</a> <i>%s</i><br>%s</li>', $hostname, $api->getTenant(), $item['id'], $item['name'], $item['type'], $description);
			}
			print '</ul>';
		}
		else {
			print '<i>Your search did not match any documents.</i>';
		}
	}
}
catch(KerioApiException $e) {
	$html->printError($e->getMessage());
}
/* Logout */
if (isset($session)) $api->logout();

$html->printFooter();

