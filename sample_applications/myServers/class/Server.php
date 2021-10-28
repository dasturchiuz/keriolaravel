<?php
require_once(dirname(__FILE__).'/SQLite.php');

/**
 * Server class.
 *
 * This class implements functions for dashboard and cron service.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
class Server {

	/**
	 * Database handler
	 * @var	PDO
	 */
	private $Db = '';

	/**
	 * Product handler
	 * @var	Object
	 */
	private $Product = '';

	/**
	 * Class constructor.
	 *
	 * @param	string	Filename
	 * @return	void
	 */
	public function Server($db) {
		$this->Db = new SQLite($db);
	}

	/**
	 * Set product-specific Api handler.
	 *
	 * @param	string	Product shortname
	 * @return	void
	 */
	private function setApi($product) {
		switch ($product) {
			case 'connect':
				require_once(dirname(__FILE__) . '/KerioConnect.php');
				$this->Product = new KerioConnect();
				break;
			case 'control':
				require_once(dirname(__FILE__) . '/KerioControl.php');
				$this->Product = new KerioControl();
				break;
			case 'operator':
				require_once(dirname(__FILE__) . '/KerioOperator.php');
				$this->Product = new KerioOperator();
				break;
			case 'workspace':
				require_once(dirname(__FILE__) . '/KerioWorkspace.php');
				$this->Product = new KerioWorkspace();
				break;
			case 'directory':
				require_once(dirname(__FILE__) . '/KerioDirectory.php');
				$this->Product = new KerioDirectory();
				break;
			default:
				break;
		}
	}

	/**
	 * Get server from database.
	 *
	 * @param	integer	Server ID, optional. Returns all records if empty
	 * @return	array	Servers from database
	 */
	public function getServersFromDb($id = NULL) {
		if (empty($id)) {
			$query = sprintf('SELECT * FROM servers');
		}
		else {
			$query = sprintf('SELECT * FROM servers WHERE id = %d', $id);
		}
		return $this->query($query);
	}

	/**
	 * Update server in database.
	 *
	 * @param	integer	Server ID, optional. Returns all records if empty
	 * @return	void
	 * @throws	Exception
	 */
	public function update($id = NULL) {
		foreach ($this->getServersFromDb($id) as $server) {
			try {
				/* Prepare required Api */
				$this->setApi($server['product']);

				/* Login */
				$this->Product->getApi()->login($server['hostname'], $server['username'], $server['password']);

				/* Get data */
				$product	= $this->Product->getProductName();
				$os			= $this->Product->getOs();
				$version	= $this->Product->getVersion();
				$build		= $this->Product->getBuildNumber();

				/* Display version together with build number */
				if ($build) $version = sprintf('%s build %d', $version, $build);

				/* Update DB */
				$this->setServerDbInfo($id, $product, $os, $version);

				/* Logout */
				$this->Product->getApi()->logout();
			}
			catch(Exception $error) {
				$this->setServerDbInfo($id, 'N/A', 'N/A', 'N/A');
				throw new Exception($error->getMessage());
			}
			unset($this->Product);
		}
	}

	/**
	 * Update last record in database.
	 *
	 * @param	void
	 * @return	void
	 */
	public function updateLast() {
		foreach ($this->getLastServerFromDb() as $server) {
			$this->update($server['id']);
		}
	}

	/**
	 * Call database query.
	 *
	 * @param	string	Database query
	 * @return	string	Database result
	 */
	private function query($query) {
		return $this->Db->getDb()->query($query);
	}

	/**
	 * Add server to database.
	 *
	 * @param	string	Product shortname
	 * @param	string	Hostname
	 * @param	string	Username
	 * @param	string	Password
	 * @return	string	Database result
	 */
	public function add($product, $hostname, $username, $password) {
		$query = sprintf('INSERT INTO servers (hostname, product, username, password) VALUES ("%s", "%s", "%s", "%s")', $hostname, $product, $username, $password);
		return $this->query($query);
	}

	/**
	 * Remove server from database.
	 *
	 * @param	integer	Server ID
	 * @return	string	Database result
	 */
	public function remove($id) {
		$query = sprintf('DELETE from servers WHERE id = %d', $id);
		return $this->query($query);
	}

	/**
	 * Get last server record from database.
	 *
	 * @param	void
	 * @return	string	Database result
	 */
	public function getLastServerFromDb() {
		$query = sprintf('SELECT * FROM servers ORDER BY id DESC LIMIT 1');
		return $this->query($query);
	}

	/**
	 * Set server info in database.
	 *
	 * @param integer	Server ID
	 * @param string	Product name
	 * @param string	Product OS
	 * @param string	Product version
	 */
	public function setServerDbInfo($id, $productname, $os, $version) {
		$query = sprintf('UPDATE servers SET productname="%s",os="%s",version="%s" WHERE id=%d', $productname, $os, $version, $id);
		return $this->query($query);
	}

	/**
	 * Set timestamp of last update.
	 *
	 * @param	void
	 * @return	Database result
	 */
	public function setDbTimestamp() {
		$query = sprintf('UPDATE log SET timestamp=datetime("NOW", "localtime") WHERE id=1');
		return $this->query($query);
	}

	/**
	 * Get timestamp of last update.
	 *
	 * @param	void
	 * @return	string	Timestamp message
	 */
	public function getDbTimestamp() {
		$query  = sprintf('SELECT timestamp FROM log WHERE id=1');
		$result = $this->query($query);
		foreach ($result as $row) {
			$timestamp = $row['timestamp'];
		}
		return sprintf('Last update: %s', $timestamp);
	}
}
