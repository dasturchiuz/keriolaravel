<?php

/**
 * Product class.
 *
 * This class implements methods required for monitoring.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
class Product {

	/**
	 * Application name
	 * @var string
	 */
	private $name	 = 'My Servers';

	/**
	 * Application vendor
	 * @var	string
	 */
	private $vendor  = 'Kerio Technologies s.r.o.';

	/**
	 * Application version
	 * @var	string
	 */
	private $version = '1.4.0.234';

	/**
	 * Global Api handler
	 * @var	string
	 */
	private $Api = '';

	/**
	 * Product info
	 * @var	array
	 */
	protected $productInfo = array();

	/**
	 * Product shortname
	 * @var	string
	 */
	protected $product = '';

	/**
	 * Class constructor.
	 * @param	string	Product shortname
	 * @return	void
	 */
	public function Product($product) {
		/* API handler */
		switch ($product) {
			case 'connect':
				require_once(dirname(__FILE__) . '/../../../src/KerioConnectApi.php');
				$this->Api = new KerioConnectApi($this->name, $this->vendor, $this->version);
				break;
			case 'control':
				require_once(dirname(__FILE__) . '/../../../src/KerioControlApi.php');
				$this->Api = new KerioControlApi($this->name, $this->vendor, $this->version);
				break;
			case 'operator':
				require_once(dirname(__FILE__) . '/../../../src/KerioOperatorApi.php');
				$this->Api = new KerioOperatorApi($this->name, $this->vendor, $this->version);
				break;
			case 'workspace':
				require_once(dirname(__FILE__) . '/../../../src/KerioWorkspaceApi.php');
				$this->Api = new KerioWorkspaceApi($this->name, $this->vendor, $this->version);
				break;
			case 'directory':
				require_once(dirname(__FILE__) . '/../../../src/KerioDirectoryApi.php');
				$this->Api = new KerioDirectoryApi($this->name, $this->vendor, $this->version);
				break;
			default:
				break;
		}
	}

	/**
	 * Get global Api handler.
	 *
	 * @param	void
	 * @return	void
	 */
	public function getApi() {
		return $this->Api;
	}
}
