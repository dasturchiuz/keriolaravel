<?php
require_once(dirname(__FILE__) . '/SQLite.php');

/**
 * Mobile Devices using ActiveSync - Sample Application.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
class MobileDevices {

	private $db;

	public function __construct($file) {
		$this->db = new SQLite($file);
	}
	
	public function dbQuery($query) {
		return $this->db->getDb()->query($query)->fetchAll();
	}

	public function getFromTable($table, $column, $default = 'Any', $direction = 'ASC') {
		$query = sprintf('SELECT DISTINCT %s FROM %s ORDER BY %s COLLATE NOCASE %s', $column, $table, $column, $direction);
		return $this->dbQuery($query);
	}

	public function getTimestamp() {
		$result = $this->dbQuery('SELECT timestamp FROM log ORDER BY id DESC LIMIT 1');
		return (empty($result)) ? 'Never' : $result[0]['timestamp'];
	}
	
	public function setTimestamp($timestamp, $duration, $userCount, $userCountWithDevice, $deviceCount) {
		$query = sprintf('
			INSERT INTO log 
				(timestamp, duration, userCount, userCountWithDevice, deviceCount) 
			VALUES
				("%s", %d, %d, %d, %d)',
			$timestamp, $duration, $userCount, $userCountWithDevice, $deviceCount);

		$this->dbQuery($query);
	}

	public function getLog() {
		return $this->dbQuery('SELECT * FROM log');
	}
	
	public function getUser($username) {
		$query = sprintf('SELECT fullname FROM users WHERE username LIKE "%s"', $username);
		$result = $this->dbQuery($query);
		return $result[0];
	}

	public function getUserDevices($username) {
		$query = sprintf('SELECT * FROM devices WHERE username LIKE "%s" ORDER BY lastSyncDate DESC', $username);
		$result = $this->dbQuery($query);
		return $result;		
	}
	
	public function insertUser($userId, $username, $fullname, $homeServer, $mobileDevicesCount) {
		$query = sprintf('INSERT INTO users (id, username, fullname, homeserver, devices) VALUES ("%s", "%s", "%s", "%s", "%d")', $userId, $username, $fullname, $homeServer, $mobileDevicesCount);
		$this->dbQuery($query);
	}
	
	public function insertDevice($username, $protocolVersion, $lastSyncDate, $os, $platform) {
		$query = sprintf('INSERT INTO devices (username, protocolVersion, lastSyncDate, os, platform) VALUES ("%s", "%s", "%s", "%s", "%s")', $username, $protocolVersion, $lastSyncDate, $os, $platform);
		$this->dbQuery($query);
	}

	function buildQuery($query = '') {
		/* Use vars from POST request */
		$postVars   = array_keys($_POST); 
		$postValues = array_values($_POST);
		$postValuesCount = count(array_filter($postValues));
	
		/* Query defaults */
		$defaultQuery = 'SELECT DISTINCT u.* FROM users u';
		$orderBy = 'username';
		$direction = 'ASC';
		
		/* At least one POST data? */
		if(empty($postValues) || ($postValuesCount == 0)) {
			$query = $defaultQuery;
		}
		else {
			$query = sprintf('%s LEFT JOIN devices d ON u.username=d.username', $defaultQuery);
			
			/* Process POST data */
			$cnt = 0;
			foreach ($postVars as $index => $name) {
				if(isset($postValues[$index]) && !empty($postValues[$index])) {
					if($cnt < 1) {
						$state = 'WHERE';
						$cnt++;
					}
					else {
						$state = 'AND';
					}
	
					$defaultTable = 'd'; //table devices
					$table = $defaultTable;
					
					$defaultComparator = 'LIKE'; //LIKE
					$comparator = $defaultComparator;
					
					/* XSS filters */
					$name  = $this->filterInput($name);
					$value = $this->filterInput($postValues[$index]);
					
					/* Search for homeserver - distributed domain */ 
					if($name == 'homeserver') {
						$table = 'u'; //search in table of users
						$value = sprintf('"%s"', $value);
					}
					/* Search by date */
					elseif($name == 'lastSyncDate') {
						$comparator = '>';
						switch($value) {
							case 'today':
								$interval = '-1 day';
								break;
							case 'week':
								$interval = '-7 days';
								break;
							case 'month':
								$interval = '-30 days';
								break;
							case 'year':
								$interval = '-1 years';
								break;
							case 'yearago':
								$interval = '-2 years';
								$comparator = '<';
								break;
							default:
								continue;
						}
						$value = 'strftime("%s", "now", "' . $interval . '")';
					}
					/* Search by number of devices */
					elseif($name == 'devices') {
						switch ($value) {
							case 1: //just one
								$comparator = '=';
								$value = 1;
								break;
							case 2: //less than five
								$comparator = '<';
								$value = 5;
								break;
							case 3: //More than five
								$comparator = '>=';
								$value = 5;
								break;
							default:
								continue;
						}
						$table = 'u'; //search in table of users
					}
					/* Ordinary LIKE% */
					else {
						$value = sprintf('"%s"', $value . '%');
					}
					
					/* Query built uppon POST data */
					$query = sprintf('%s %s %s.%s %s %s', $query, $state, $table, $name, $comparator, $value);
				}
			}
		}	
		$query = sprintf('%s ORDER BY u.%s %s', $query, $orderBy, $direction);
		return $query;
	}

	function filterInput($input) {
		$input  = htmlspecialchars($input);
		return sprintf('%s', $input);
	}

	function dumpPostData() {
		$field_names = array_keys($_POST); 
		$field_values = array_values($_POST);
	
		print '<pre>';
		print_r($field_names);
		print_r($field_values);
		print '</pre>';
	}
}
