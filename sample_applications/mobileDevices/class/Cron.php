<?php
require_once(dirname(__FILE__) . '/MobileDevices.php');
require_once(dirname(__FILE__) . '/../../../src/KerioConnectApi.php');

/**
 * Mobile Devices using ActiveSync - Sample Application.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
class Cron {
	
	private $application;
	private $api;

	private $name = 'Mobile Devices Usage';
	private $vendor	= 'Kerio Technologies s.r.o.';
	private $version = '1.4.0.234';

	private $server = array();
	private $timer = array();

	private $userCount;
	private $userCountWithDevice;
	private $deviceCount;

	public function __construct($file) {
		$this->application = new MobileDevices($file);

		/* Set debug if GET var is set */
		if(isset($_GET['debug'])) {
			if($_GET['debug'] == 1) {
				$this->api->setDebug(TRUE);
			}
		}
	}

	public function start($server) {
		$this->server = $server;
		$this->api = new KerioConnectApi($this->name, $this->vendor, $this->version);
		$this->api->login($this->server['hostname'], $this->server['username'], $this->server['password']);
	}

	public function done() {
		$this->api->logout();
		unset($this->api);
		unset($this->server);
	}

	public function update() {
			$primaryDomain	= $this->getPrimaryDomain();
			$serverId		= $this->getServerId();
			$userList		= $this->getUserList($primaryDomain, $serverId);
				
			foreach ($userList as $user) {
		
				$userId   = $user['id'];
				$username = $user['loginName'];
				$fullname = $user['fullName'];
				$homeServer = $user['homeServer']['name'];
					
				/* Distributed domain hack */
				if(empty($homeServer)) continue;
					
				$mobileDevicesList  = $this->getMobileDeviceList($userId);
				$mobileDevicesCount = count($mobileDevicesList);
			
				if($mobileDevicesCount != 0) {
					$this->application->insertUser($userId, $username, $fullname, $homeServer, $mobileDevicesCount);
					$this->userCountWithDevice++;
						
					foreach ($mobileDevicesList as $device) {
						$protocolVersion = $device['protocolVersion'];
						$lastSyncDate    = $device['lastSyncDate'];
						$os				 = $device['os'];
						$platform		 = $device['platform'];

						$this->application->insertDevice($username, $protocolVersion, $lastSyncDate, $os, $platform);
						$this->deviceCount++;
					}
				}
				$this->userCount++;
				flush();
			}
	}

	public function getServerId() {
		$result = $this->api->sendRequest('Domains.getSettings');
		return $result['setting']['serverId'];
	}

	function getPrimaryDomain() {
		$method = 'Domains.get';
		$params = array(
			'query' => array(
				'conditions' => array(array(
					'fieldName' => 'isPrimary',
					'comparator' => 'Eq',
					'value' => 'TRUE'
				)),
				'fields' => array(
					'id',
					'name',
					'isPrimary'
				)
			)
		);
		$response = $this->api->sendRequest($method, $params);
		return $response['list'][0]['id'];
	}

	function getDomainList() {
		$method = 'Domains.get';
		$params = array(
			'query' => array(
				'fields' => array(
					'id',
					'name',
					'isPrimary'
				)
			)
		);
		$response = $this->api->sendRequest($method, $params);
		return $response['list'];
	}

	function getDistributedDomainServerList() {
		$response = $this->api->sendRequest('DistributedDomain.getServerList');
		return $response['servers'];
	}

	function getUserList($domainId, $serverId) {
		$method = 'Users.get';
		$params = array(
			'query' => array(
				'fields' => array(
					'id',
					'loginName',
					'fullName',
					'homeServer',
				),
				'conditions' => array(array(
					'fieldName' => 'homeServer',
					'comparator' => 'Eq',
					'value' => $serverId
				)),
				'orderBy' => array(array(
					'columnName' => 'loginName',
					'direction' => 'Asc'
				))
			),
			'domainId' => $domainId,
		);
		$response = $this->api->sendRequest($method, $params);
		return $response['list'];
	}

	function getMobileDeviceList($userId) {
		$method = 'Users.getMobileDeviceList';
		$params = array(
			'query' => array(
				'orderBy' => array(array(
					'columnName' => 'os',
					'direction' => 'Asc'
				))
			),
			'userId' => $userId,
		);
		$response = $this->api->sendRequest($method, $params);
		return $response['list'];
	}
	
	public function setTimestamp($duration) {
		$timestamp = date('Y-m-d H:i:s');
		$this->application->setTimestamp($timestamp, $duration, $this->userCount, $this->userCountWithDevice, $this->deviceCount);
	}
}
