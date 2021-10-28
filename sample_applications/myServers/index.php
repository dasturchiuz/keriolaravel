<?php
/**
 * Administration API for Kerio Connect - Sample Application.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
require_once(dirname(__FILE__) . '/class/Server.php');
require_once(dirname(__FILE__) . '/include/config.php');
require_once(dirname(__FILE__) . '/include/header.php');
?>

<h1 class="main">My Servers</h1>

<?php
/* Add server */
if (isset($_POST['add']) && ($allowAdd)) {
	try {
		$product  = htmlspecialchars($_POST['product']);
		$hostname = htmlspecialchars($_POST['hostname']);
		$username = htmlspecialchars($_POST['username']);
		$password = htmlspecialchars($_POST['password']);

		if ($product && $hostname && $username && $password) {
			$Server->add($product, $hostname, $username, $password);
			$Server->updateLast();
			print '<div class="success">Server has been added.</div>';
		}
		else {
			print '<div class="error">Please fill all fields.</div>';
		}
	}
	catch (Exception $error) {
		printf('<div class="error">Server has been added with an error: %s</div>', $error->getMessage());
	}
}
/* Remove server */
elseif (isset($_GET['remove']) && ($allowRemove)) {
	$Server->remove($_GET['remove']);
	print '<div class="success">Server has been removed.</div>';
}
?>

<div id="menu">
	<?php if ($allowAdd): ?>
		<a href="javascript:collapse('new-server');">Add server</a> |
	<?php endif; ?>
	<a href="index.php">Refresh page</a> |
	<a href="cron.php">Run update</a>
</div>

<!-- New server form -->
<div id="new-server" class="debug" style="display:none">
	<form method="POST" action="index.php" autocomplete="off">
		Hostname: <input type="text" name="hostname">
		<select name="product">
		<option selected value="">Please select</option>
			<option value="connect">Kerio Connect</option>
			<option value="control">Kerio Control</option>
			<option value="operator">Kerio Operator</option>
			<option value="workspace">Kerio Workspace</option>
			<option value="directory">Kerio Directory</option>
		</select>
		Username: <input type="text" name="username">
		Password: <input type="password" name="password">
		<input type="submit" name="add" value="Add">
	</form>
</div>

<div id="server-list">
	<table id="table-list">
		<thead>
			<tr>
				<th class="list">Hostname</th>
				<th class="list">Product</th>
				<th class="list">Version</th>
				<th class="list">Operating System</th>
				<th class="action">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
<?php
/* Print servers from database */
foreach ($Server->getServersFromDb() as $server) {
	$hostname		= $server['hostname'];
	$product		= $server['product'];
	$productName	= $server['productname'];
	$version		= $server['version'];
	$os				= $server['os'];

	/* Set product icon */
	$product = (isset($productName))
		? sprintf('<img width="16" src="images/%s.png">&nbsp;%s', $product, $productName)
		: '';

	/* Allow direct login */
	$linkLogin = ($allowLogin)
		? sprintf('<a title="Login" href="login.php?id=%d">%s</a>', $server['id'], $hostname)
		: $hostname;

	/* Allow remove */
	$linkRemove	= ($allowRemove)
		? sprintf('<a title="Remove" href="?remove=%d">Remove</a>', $server['id'])
		: '';

	/* Print record */
	print '<tr>';
	printf('<td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td>',
		$linkLogin,
		$product,
		$version,
		$os,
		$linkRemove);
	print '</tr>';
}
?>
		</tbody>
	</table>
</div>

<div>
	<?php print $Server->getDbTimestamp(); ?>
</div>

<?php require_once(dirname(__FILE__) . '/include/footer.php'); ?>
