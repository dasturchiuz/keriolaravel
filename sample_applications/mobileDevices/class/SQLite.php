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
		return $this->getDb();
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
		$this->db->exec('CREATE TABLE IF NOT EXISTS users (id TEXT, username TEXT, fullname TEXT, homeserver TEXT, devices INTEGER)');
		$this->db->exec('CREATE TABLE IF NOT EXISTS devices (username TEXT, protocolVersion TEXT, lastSyncDate TEXT, os TEXT, platform TEXT)');
		$this->db->exec('CREATE TABLE IF NOT EXISTS log (id INTEGER PRIMARY KEY AUTOINCREMENT, timestamp DATE, duration TEXT, userCount INTEGER, userCountWithDevice INTEGER, deviceCount INTEGER)');
	}

	/**
	 * Drop the database.
	 * 
	 * @param	void
	 * @return	void
	 */
	public function dropDb() {
		$this->db->exec('DROP TABLE IF EXISTS users');
		$this->db->exec('DROP TABLE IF EXISTS devices');
		$this->db->exec('DROP TABLE IF EXISTS log');
	}

	/**
	 * Open database.
	 *
	 * @param	string	Filename
	 * @return	void
	 */
	private function openDb($db) {
		$this->db = new PDO('sqlite:' . $db);
		$this->initialize();
	}

	/**
	 * Get database handler.
	 *
	 * @param	void
	 * @return	string	PDO object
	 */
	public function getDb() {
		return $this->db;
	}
}
