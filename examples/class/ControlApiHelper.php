<?php
/**
 *
 * API Helper Class.
 *
 * @copyright	Copyright &copy; 2014 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */

class ControlApiHelper {
	
	/**
	 *
	 * @var KerioControlApi
	 */
	private $api;
	
	/**
	 * Class constructor.
	 * Register application
	 *
	 * @param   KerioControlApi API instance
	 * @return  KerioControlApi API instance
	 */
	public function __construct($api) {
		$this->api = $api;

		return $this->api;
	}
	
	/**
	 * Get config timestamp as cut-off prevention.
	 *
	 * @param	void
	 * @return	array	Timestamp
	 */
	public function getConfigTimestamp() {
		$result = $this->api->sendRequest('Session.getConfigTimestamp');
		return $result['clientTimestampList'];
	}

	/**
	 * Confirm new config as cut-off prevention.
	 *
	 * @param	array	Timestamp
	 * @return	array	Result
	 */
	public function confirmConfig($timestamp) {
		$result = $this->api->sendRequest('Session.confirmConfig', array('clientTimestampList' => $timestamp));
		return $result;
	}
	
	/**
	 * Apply configuration.
	 *
	 * @param	string	Method interface
	 * @return	array	Result
	 */
	private function applyConfig($interface) {
		$result = $this->api->sendRequest($interface.'.apply');
		return $result;
	}
	
	/**
	 * Get list of IP addresses from a file.
	 *
	 * Local function used in example spam_blacklist
	 *
	 * @param	string	Filename
	 * @return	array	List of IP addresses
	 * @throws	KerioApiException
	 */
	public function getBlacklistRecords($file) {
		$blacklist = array();
		if(file_exists($file) && is_readable($file)) {
			$data = file_get_contents($file);
			foreach (preg_split("/\n/", $data) as $record) {
				if (empty($record)) continue;
				array_push($blacklist, $record);
			}
		}
		else {
			throw new KerioApiException(sprintf('Cannot open file %s', $file));
		}
		return $blacklist;
	}

	/**
	 * Get list of IP addesses from a group
	 *
	 * @param	string	Group name
	 * @return	array	List of IP addresses
	 */
	public function getIpGroupList($name) {
		$params = array(
			"query" => array(
				"conditions" => array(array(
					"fieldName" => "name",
					"comparator" => "Like",
					"value" => $name
				)),
				"orderBy" => array(array(
					"columnName" => "item",
					"direction" => "Asc"
				))
			)
		);
		$result = $this->api->sendRequest('IpAddressGroups.get', $params);
		return $result['list'];
	}
	
	/**
	 * Get list of URLs from a group
	 *
	 * @param	void
	 * @return	array	List of IP addresses
	 */
	public function getUrlGroupList() {
		$params = array(
			"query" => array(
				"orderBy" => array(array(
					"columnName" => "item",
					"direction" => "Asc"
				))
			)
		);
		$result = $this->api->sendRequest('UrlGroups.get', $params);
		return $result['list'];
	}
	
	/**
	 * Add a IP address to an URL Group
	 *
	 * @param	string	Group name
	 * @param	string	URL
	 * @param	string	Description, optional
	 * @return	array	Result
	 */
	public function addHostToUrlGroup($group, $url, $description = '') {
		if(empty($description)) {
			$description = sprintf('Automatically added on %s', date(DATE_RFC822));
		}
		$params = array(
			"groups" => array(array(
				"groupId" => "",
				"groupName" => $group,
				"url" => $url,
				"isRegex" => FALSE,
				"type" => "Url",
				"description" => $description,
				"enabled" => TRUE
			))
		);
		$result = $this->api->sendRequest('UrlGroups.create', $params);
		
		return $result;
	}

	/**
	 * Add a IP address to an URL Group
	 *
	 * @param	string	Group name
	 * @param	array	URLs
	 * @param	string	Description, optional
	 * @return	array	Result
	 */
	public function addHostToUrlGroupFromArray($group, $hosts, $description = '') {
		if(empty($description)) {
			$description = sprintf('Automatically added on %s', date(DATE_RFC822));
		}
		
		$groups = array();
		foreach ($hosts as $url) {
			if (empty($url)) continue;
			array_push($groups, array(
				"groupId" => "",
				"groupName" => $group,
				"url" => sprintf("*%s/*", $url),
				"isRegex" => FALSE,
				"type" => "Url",
				"description" => $description,
				"enabled" => TRUE
			));
		}
		
		$params = array(
			"groups" => $groups
		);
		$result = $this->api->sendRequest('UrlGroups.create', $params);
		
		return $result;
	}
	
	/**
	 * Remove a IP address from a IP Group
	 *
	 * @param	string	Group name
	 * @param	string	URL
	 * @return	array	Result
	 */
	public function removeHostFromUrlGroup($group, $url) {
		$list = $this->getUrlGroupList();
		foreach ($list as $record) {
			if(($record['groupName'] != $group) || ($record['url'] != $url)) continue;
			$urlId = $record['id'];
		}
		if (empty($urlId)) return;
		
		$params = array("groupIds" => array($urlId));
		$result = $this->api->sendRequest('UrlGroups.remove', $params);
		
		return $result;
	}
	
	/**
	 * Remove a IP address from a IP Group
	 *
	 * @param	string	Group name
	 * @param	string	URL
	 * @return	array	Result
	 */
	public function removeUrlGroup($groupName) {
		$list = $this->getUrlGroupList();
		$groupIds = array();
		foreach ($list as $record) {
			if($record['groupName'] != $groupName) continue;
			array_push($groupIds, $record['id']);
		}
		if (empty($groupIds)) return;
		
		$params = array("groupIds" => $groupIds);
		$result = $this->api->sendRequest('UrlGroups.remove', $params);
		
		return $result;
	}

	/**
	 * Apply URL Groups config
	 *
	 * @param	void
	 * @return	void
	 */
	public function applyUrlGroups() {
		$this->applyConfig('UrlGroups');
		$timestamp = $this->getConfigTimestamp();
		$this->confirmConfig($timestamp);
	}
	
	public function createDenyHttpRule($name, $url) {
		/* get current http rules */
		$result = $this->api->sendRequest('HttpPolicy.get');
		$rulesList = $result['list'];

		/* create a new rule */
		$newRule = array(
			'enabled' => TRUE,
			'name' => $name,
			'urlCondition' => array(
				'type' => 'UrlString',
				'url' => $url),
			'action' => 'Deny',
			'denialCondition' => array(
				'denialText' => sprintf('Sorry, access to %s is not allowed!', $url),
				'canUnlockRule' => TRUE)
		);
		$rulesList[] = $newRule;

		/* save rules list */
		$params = array('rules' => $rulesList);
		
		return $this->api->sendRequest('HttpPolicy.set', $params);
	}
}