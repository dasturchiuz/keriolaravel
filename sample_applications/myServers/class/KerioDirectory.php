<?php
require_once(dirname(__FILE__) . '/Product.php');

/**
 * Product-specific class.
 *
 * Class with local functions for Kerio Directory
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
class KerioDirectory extends Product {

	/**
	 * Protuct shortname
	 * @var	string
	 */
	protected $product = 'directory';

	/**
	 * Class constructor.
	 *
	 * @param	void
	 * @return	void
	 */
	public function KerioDirectory() {
		$this->Product($this->product);
	}

	/**
	 * Get product info.
	 *
	 * @param	void
	 * @return	array	Product info
	 */
	private function getProductInfo() {
		if (empty($this->productInfo)) {
			$method = 'Server.getInfo';
			$response = $this->getApi()->sendRequest($method);
			$this->productInfo = $response['serverInfo'];
		}
		return $this->productInfo;
	}

	/**
	 * Get product name.
	 *
	 * @param	void
	 * @return	string	Product name
	 */
	public function getProductName() {
		$this->getProductInfo();
		return $this->productInfo['app']['product'];
	}

	/**
	 * Get product OS.
	 *
	 * @param	void
	 * @return	string	Operating system
	 */
	public function getOs() {
		$this->getProductInfo();
		return $this->productInfo['sys']['osName'];
	}

	/**
	 * Get product version.
	 *
	 * @param	void
	 * @return	string	Product version
	 */
	public function getVersion() {
		$this->getProductInfo();
		return $this->productInfo['app']['version'];
	}

	/**
	 * Get product build number
	 *
	 * @param	void
	 * @return	integer	Build number
	 */
	public function getBuildNumber() {
		$this->getProductInfo();
		return $this->productInfo['app']['buildNumber'];
	}
}
