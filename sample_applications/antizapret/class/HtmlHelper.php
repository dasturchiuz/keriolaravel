<?php
/**
 * HTML Class.
 *
 * HTML related methods
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
class HtmlHelper {

	/**
	 * Print page header.
	 *
	 * @param	void
	 * @return	void
	 */
	public function printHeader($title) {
		print '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Strict//EN">'
			. '<html>'
			. '<head>'
			. '<meta http-equiv="Content-type" content="text/html; charset=UTF-8">'
			. '<link rel="stylesheet" type="text/css" href="resources/style.css">'
			. '<link rel="shortcut icon" href="../../doc/resources/images/favicon.ico">'
			. '<title>AntiZapret | Kerio APIs Client Library for PHP</title>'
			. '</head>'
			. '<body>'
			. '<div id="content">'
			. '<h1>' . $title . '</h1>';
	}

	/**
	 * Print page footer.
	 *
	 * @param	void
	 * @return	void
	 */
	public function printFooter() {
		print '</div>'
			. '<div id="footer">'
			. '<hr>'
			. '<span class="date">' .date(DATE_RSS) . '</scipt>'
			. '<span class="alignright">' . basename($_SERVER['REQUEST_URI']) . ' | 1.4.0.234</span>'
			. '</div>'
			. '</body>'
			. '</html>';
	}

	/**
	 * Print message.
	 * 
	 * @param	string	Message
	 * @param	string	CSS class
	 * @return	void
	 */
	public function p($message, $class = null) {
		printf('<p class="%s">%s</p>', $class, $message);
	}

	/**
	 * Print info message.
	 * 
	 * @param	string	Message
	 * @return	void
	 */
	public function printInfo($message) {
		$this->p($message, 'info');
	}

	/**
	 * Print success message.
	 * 
	 * @param	string	Message
	 * @return	void
	 */
	public function printSuccess($message) {
		$this->p($message, 'success');
	}

	/**
	 * Print error message.
	 * 
	 * @param	string	Message
	 * @return	void
	 */
	public function printError($message) {
		$this->p($message, 'warning');
	}

	/**
	 * Print warning message.
	 * 
	 * @param	string	Message
	 * @return	void
	 */
	public function printWarning($message) {
		$this->p($message, 'warning');
	}

	/**
	 * Print debug message.
	 * 
	 * @param	string	Message
	 * @return	void
	 */
	public function printDebug($message) {
		$this->p(sprintf('<pre>%s</pre>', print_r($message, TRUE), 'prettyprint'));
	}

	/**
	 * Print message in p.note.
	 *
	 * @param	string
	 * @return	void
	 */
	public function printNote($message) {
		printf('<p class="note">%s</p>', $message);
	}

	/**
	 * Print add alias form.
	 *
	 * @param	array	Domain list
	 * @return	void
	 */
	function printAddAliasForm($domainList) {
		print '<form method="POST" action="#">';
		print 'Alias:';
		print '&nbsp;';
		print '<input type="text" size="15" name="alias">';
		print '&nbsp;';
		print '<select name="domain">';
		print '    <option value="">Please select</option>';
		foreach ($domainList as $domain) {
			printf('    <option value="%s">%s</option>', $domain['id'], $domain['name']);
		}
		print '</select>';
		print '&nbsp;';
		print 'deliver to:';
		print '&nbsp;';
		print '<input type="text" size="20" name="email">';
		print '&nbsp;';
		print '<input type="submit" value="Add">';
		print '</form>';
	}

	/**
	 * Print create user form.
	 *
	 * @param	array	Domain list
	 * @return	void
	 */
	function printCreateUserForm($domainList, $randomPassword) {
		print '<form method="POST" action="#">';
		print 'Username:';
		print '&nbsp;';
		print '<input type="text" size="15" name="username">';
		print '&nbsp;';
		print '<select name="domainId">';
		foreach ($domainList as $domain) {
			printf('    <option value="%s">%s</option>', $domain['id'], $domain['name']);
		}
		print '</select>';
		print '<br>';
		print 'Password:';
		print '&nbsp;';
		print '<input type="password" size="20" name="password">';
		print '&nbsp;';
		printf('(random password: %s)<br>', $randomPassword);
		print '<input type="submit" value="Create">';
		print '</form>';
	}

	/**
	 * Print create domain form.
	 *
	 * @param	string	Domain name
	 * @return	void
	 */
	function printCreateDomainForm() {
		print '<form method="POST" action="#">';
		print 'Domain:';
		print '&nbsp;';
		print '<input type="text" size="15" name="domain">';
		print '&nbsp;';
		print '<input type="submit" value="Create">';
		print '</form>';
	}

	/**
	 * Return given size in bytes
	 *
	 * @param	array	List of users
	 * @param   array   List of constants
	 *
	 * @return	number	Size in bytes
	 */
	public function getSizeInBytes($size, $constants) {

		$value = $size['value'];
		$units = $size['units'];

		switch ($units) {
			case $constants['kerio_web_Bytes']:
				$pow = 0;
				break;
			case $constants['kerio_web_KiloBytes']:
				$pow = 1;
				break;
			case $constants['kerio_web_MegaBytes']:
				$pow = 2;
				break;
			case $constants['kerio_web_GigaBytes']:
				$pow = 3;
				break;
			case $constants['kerio_web_TeraBytes']:
				$pow = 4;
				break;
			case $constants['kerio_web_PetaBytes']:
				$pow = 5;
				break;
			default:
				$pow = 0;
				break;
		}

		return ($value * pow(1024, $pow));
	}

	/**
	 * Print form to copy one group from another one
	 *
	 * @param	array	List of domains
	 * @param	array	List of groups
	 * @param	string	Id of selected domain
	 * @param	string	Id of selected group
	 * @param	string	New group name
	 *
	 * @return	void
	 */
	public function printCopyGroupForm($domainList, $groupList, $selectedDomain, $selectedGroup = null, $newGroupName = null) {

		print '<form method="POST" action="#">'
			. '<table>'
			. '<tr>'
			. '<td>Select domain:</td>'
			. '<td><select name="domain" style="width: 150px" onchange="javascript:this.form.isDomainChanged.value=true; this.form.submit();">';

		foreach ($domainList as $domain) {
			printf('<option value="%s"%s>%s</option>', $domain['id'], $selectedDomain == $domain['id'] ? 'selected' : '', $domain['name']);
		}

		print '</select></td>'
			. '</tr><tr>'
			. '<td>Select group:</td>'
			. '<td><select name="group" style="width: 150px">';

		foreach ($groupList as $group) {
			printf('<option value="%s"%s>%s</option>', $group['id'], $selectedGroup == $group['id'] ? 'selected' : '', $group['name']);
		}

		print '</select></td>'
			. '</tr><tr>'
			. '<td>Enter new group name:</td>'
			. '<td><input type="text" size="20" name="newGroup" value=' . $newGroupName . '></td>'
			. '</tr>'
			. '</table>'
			. '<input type="hidden" name="isDomainChanged" value="false">'
			. '<input type="submit" value="Copy">'
			. '</form>'
			. '<br>';
	}

	/**
	 * Print form to display user's membership
	 *
	 * @param	array	List of domains
	 * @param	array	List of users
	 * @param	string	Id of selected domain
	 * @param	string	Id of selected user
	 *
	 * @return	void
	 */
	public function printUserMembershipForm($domainList, $userList, $selectedDomain, $selectedUser) {

		print '<form method="POST" action="#">'
			. '<table>'
			. '<tr>'
			. '<td>Select domain:</td>'
			. '<td><select name="domain" style="width: 150px" onchange="javascript:this.form.isDomainChanged.value=true; this.form.submit();">';

		foreach ($domainList as $domain) {
			printf('<option value="%s"%s>%s</option>', $domain['id'], $selectedDomain == $domain['id'] ? 'selected' : '', $domain['name']);
		}

		print '</select></td>'
			. '</tr><tr>'
			. '<td>Select user:</td>'
			. '<td><select name="user" style="width: 150px">';

		foreach ($userList as $user) {
			printf('<option value="%s"%s>%s</option>', $user['id'], $selectedUser == $user['id'] ? 'selected' : '', $user['loginName']);
		}

		print '</select></td>'
			. '</tr>'
			. '</table>'
			. '<input type="hidden" name="isDomainChanged" value="false">'
			. '<input type="submit" value="Show">'
			. '</form>'
			. '<br>';
	}

	/**
	 * Print form to display user availability
	 * 
	 * @param	array	List of domains
	 * @return	void
	 */
	public function printCheckUserForm($domainList) {

		print '<form method="POST" action="#">'
			. 'Username: <input type="text" name="username">'
			. '<select name="domainId">';
		
		foreach ($domainList as $domain) {
				printf('<option value="%s">%s</option>', $domain['id'], $domain['name']);
		}
		
		print '</select>'
			. '<input type="submit" value="Check">'
			. '</form>';
	}

	/**
	 * Print Out of Office form
	 *
	 * @param	array	Out of Office settings
	 * @return	void
	 */
	public function printOutOfOfficeForm($settings) {
		
		$checked = ($settings['isEnabled']) ? 'checked' : '';
		
		print '<form method="POST" action="">'
			. '<input type="checkbox" name="enabled" ' . $checked . '> Send following Out of Office message<br>'
			. '<textarea rows="10" cols="50" name="message">' .  $settings['text'] . '</textarea><br>'
			. '<input type="submit" value="Set">'
			. '</form>';
	}

	/**
	 * Print form of recorded calls
	 *
	 * @param	array	List of calls
	 * @return	void
	 */
	public function printRecordedCalls($calls) {
		print '<form method="POST" action="">';
		print '<table>';
		print '  <thead>';
		print '    <th class="operator" style="width:30px">&nbsp;</th>';
		print '    <th class="operator" style="width:50px">From</th>';
		print '    <th class="operator" style="width:50px">To</th>';
		print '    <th class="operator" style="width:100px">Duration</th>';
		print '  </thead>';
		print '  <tbody>';
		
		foreach ($calls['recordingList'] as $record) {
			printf('<tr><td><input type="radio" name="id" value="%s"></td><td>%d</td><td>%d</td><td>%d seconds</td></tr>',
				$record['ID'], $record['EXTENSION'], $record['CALLER_ID'], $record['DURATION']);
		}
		print '  </tbody>';
		print '</table>';
		print '<br>';
		print '<input type="submit" value="Download call">';
		print '</form>';
	}

	/**
	 * Print table of two cells.
	 * 
	 * @param	array	Table data as an array key => value
	 * @return	void
	 */
	public function printTable($data) {
		print '<table>';
		foreach ($data as $key => $value) {
			printf('<tr><td>%s</td><td>%s</td></tr>', $key, $value);
		}
		print '</table>';
	}

	/**
	 * Print upload form.
	 * 
	 * @param	void
	 * @return	void
	 */
	public function printUploadForm() {
		print '<form class="prettyprint" action="" method="POST" enctype="multipart/form-data">';
		print 'File: <input type="file" name="filename" />';
		print '<input type="submit" value="Upload" />';
		print '</form>';
	}
}
