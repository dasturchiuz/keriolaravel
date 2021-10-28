<?php

/**
 * Database class.
 *
 * This class works with an SQLite database over PDO connection.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
class SQLite {

	/**
	 * Database handler
	 * @var	string	PDO
	 */
	private $Db = '';

	/**
	 * Class constructor.
	 *
	 * @param	string	Filename
	 * @return	void
	 */
	public function SQLite($db) {
		$this->checkPdo();
		try {
			$this->openDb($db);
		}
		catch (Exception $error) {
			die(sprintf('Unable to open DB file %s. File or directory is not writeable.', $db));
		}
	}

	/**
	 * Check PHP environment.
	 *
	 * @param	void
	 * @return	void
	 */
	private function checkPdo() {
		if (!extension_loaded('pdo_sqlite')) {
			die("Error: Your PHP installation does not have PDO with SQLite enabled.<br>To configure SQLite support in PDO, please edit your php.ini config file and enable row with php_pdo_sqlite module, e.g. extension=php_pdo_sqlite.dll'<br>For more information see <a href='http://php.net/manual/en/pdo.installation.php'>http://php.net/manual/en/pdo.installation.php</a>");
		}
	}

	/**
	 * Create an empty database.
	 *
	 *  @param	void
	 *  @return	void
	 */
	private function initialize() {
		$this->Db->exec('CREATE TABLE IF NOT EXISTS servers (id INTEGER PRIMARY KEY AUTOINCREMENT, hostname TEXT, product TEXT, productname TEXT, version TEXT, os TEXT, username TEXT, password TEXT)');
		$this->Db->exec('CREATE TABLE IF NOT EXISTS log (id INTEGER PRIMARY KEY AUTOINCREMENT, timestamp DATE)');
		$this->Db->exec('INSERT INTO log (timestamp) VALUES ("never")');
	}

	/**
	 * User servers from Kerio Demo Service.
	 * 
	 * @param	void
	 * @return	void
	 */
	private function setDemoServers() {
		$username = 'admin-en';
		$password = 'kerio';

		$this->Db->exec(sprintf('INSERT INTO servers (hostname, product, productname, version, os, username, password) VALUES ("connect.demo.kerio.com", "connect", "Kerio Connect", "N/A", "N/A", "%s", "%s")', $username, $password));
		$this->Db->exec(sprintf('INSERT INTO servers (hostname, product, productname, version, os, username, password) VALUES ("control.demo.kerio.com", "control", "Kerio Control", "N/A", "N/A", "%s", "%s")', $username, $password));
		$this->Db->exec(sprintf('INSERT INTO servers (hostname, product, productname, version, os, username, password) VALUES ("operator.demo.kerio.com", "operator", "Kerio Operator", "N/A", "N/A", "%s", "%s")', $username, $password));
	}

	/**
	 * Open database.
	 *
	 * @param	string	Filename
	 * @return	void
	 */
	private function openDb($db) {
		$this->Db = new PDO('sqlite:' . $db);

		$this->initialize();

		$servers = $this->getDb()->query('SELECT * FROM servers')->fetchAll();
		if (empty($servers)) $this->setDemoServers();
	}

	/**
	 * Get database handler.
	 *
	 * @param	void
	 * @return	string	PDO object
	 */
	public function getDb() {
		return $this->Db;
	}
}
