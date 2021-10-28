<?php
require_once(dirname(__FILE__) . '/../../../doc/resources/class/Html.php');

/**
 * HTML Form Class.
 *
 * HTML related methods
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
class HtmlForm extends Html {
	
	public function printForm($defaults) {
		
		$product = (isset($defaults['product'])) ? $defaults['product'] : '';
		$hostname = (isset($defaults['hostname'])) ? $defaults['hostname'] : '';
		$username = (isset($defaults['username'])) ? $defaults['username'] : '';
		$password = (isset($defaults['password'])) ? $defaults['password'] : '';
		
		print '<form method="POST" action="" autocomplete="off">';
		print '  <label class="login">Hostname:</label><input type="text" name="hostname" size="30" value="'. $hostname . '">&nbsp;';
		print '  <select name="product">';
		print '    <option value="">Please select</option>';
		foreach (array('connect', 'control', 'workspace', 'operator', 'samepage') as $option) {
			$name = ($option == 'samepage') ? 'Samepage' : sprintf('Kerio %s', ucfirst($option));
			$selected = ($option == $product) ? 'selected' : '';
			printf('    <option value="%s" %s>%s</option>', $option, $selected, $name);
		}
		print '  </select><br>';
		print '  <label class="login">Username:</label><input type="text" name="username" size="30" value="'. $username . '"><br>';
		print '  <label class="login">Password:</label><input type="password" name="password" size="30" value="'. $password . '"><br><br>';
		print '  <input type="submit" value="Save">';
		print '</form>';
	}
}