<?php
require_once(dirname(__FILE__) . '/Product.php');

/**
 * Product-specific class.
 *
 * Class with local functions for Kerio Workspace
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
class KerioWorkspace extends Product {

	/**
	 * Protuct shortname
	 * @var	string
	 */
	protected $product = 'workspace';

	/**
	 * Class constructor.
	 *
	 * @param	void
	 * @return	void
	 */
	public function KerioWorkspace() {
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
			$method = 'Server.getProductInfo';
			$response = $this->getApi()->sendRequest($method);
			$this->productInfo = $response['info'];
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
		return $this->productInfo['productName'];
	}

	/**
	 * Get product OS.
	 *
	 * @param	void
	 * @return	string	Operating system
	 */
	public function getOs() {
		$this->getProductInfo();
		return $this->productInfo['osName'];
	}

	/**
	 * Get product version.
	 *
	 * @param	void
	 * @return	string	Product version
	 */
	public function getVersion() {
		$this->getProductInfo();
		return $this->productInfo['version'];
	}

	/**
	 * Get product build number
	 *
	 * @param	void
	 * @return	integer	Build number
	 */
	public function getBuildNumber() {
		$this->getProductInfo();
		return $this->productInfo['buildNumber'];
	}
}
