<?php
require_once(dirname(__FILE__) . '/Product.php');

/**
 * Product-specific class.
 *
 * Class with local functions for Kerio Control
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
class KerioControl extends Product {

	/**
	 * Product shortname
	 * @var	string
	 */
	protected $product = 'control';

	/**
	 * Product name
	 * @var	string
	 */
	protected $name = 'Kerio Control';

	/**
	 * Class constructor.
	 *
	 * @param	void
	 * @return	void
	 */
	public function KerioControl() {
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
			$method = 'ProductInfo.get';
			$response = $this->getApi()->sendRequest($method);
			$this->productInfo = $response['productInfo'];
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
		return $this->name;
	}

	/**
	 * Get product OS.
	 *
	 * @param	void
	 * @return	string	Operating system
	 */
	public function getOs() {
		$this->getProductInfo();
		if(preg_match('/Linux/', $this->productInfo['osDescription'])) {
			return ($this->isBox()) ? $this->getBoxVersion() : 'Appliance Edition';
		}
		else {
			return $this->productInfo['osDescription'];
		}
	}

	/**
	 * Check if is HW box.
	 *
	 * @param	void
	 * @return	boolean	True if a serial number is detected
	 */
	public function isBox() {
		$this->getProductInfo();
		return $this->productInfo['boxEdition'];
	}

	/**
	 * Get HW box version.
	 *
	 * @param	void
	 * @return	string	Box version
	 */
	public function getBoxVersion() {
		if($this->isBox()) {
			$response = $this->getApi()->sendRequest('Ports.get');

			$portCount = 0;
			foreach($response['ports'] as $port) {
				$portCount++;
			}

			switch ($portCount) {
				case 4:	 return $this->name . ' Box 1110';
				case 8:	 return $this->name . ' Box 3110';
				default: return $this->name . 'Unknown Box';
			}
		}
	}

	/**
	 * Get product version.
	 *
	 * @param	void
	 * @return	string	Product version
	 */
	public function getVersion() {
		$this->getProductInfo();
		return $this->productInfo['versionString'];
	}

	/**
	 * Get product build number
	 *
	 * @param	void
	 * @return	integer	Build number
	 */
	public function getBuildNumber() {
		return NULL;
	}
}
