<?php
require_once(dirname(__FILE__) . '/Product.php');

/**
 * Product-specific class.
 *
 * Class with local functions for Kerio Operator
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
class KerioOperator extends Product {

	/**
	 * Protuct shortname
	 * @var	string
	 */
	protected $product = 'operator';

	/**
	 * Class constructor.
	 *
	 * @param	void
	 * @return	void
	 */
	public function KerioOperator() {
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
			$method = 'Server.getConstantList';
			$response = $this->getApi()->sendRequest($method);
			$this->productInfo = $response['constantList'];
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
		return $this->productInfo['product']['SERVER_PRODUCT_FULL_NAME'];
	}

	/**
	 * Get product OS.
	 *
	 * @param	void
	 * @return	string	Operating system
	 */
	public function getOs() {
		$this->getProductInfo();
		return ($this->isBox()) ? $this->getBoxVersion() : 'Appliance Edition';
	}

	/**
	 * Check if is HW box.
	 *
	 * @param	void
	 * @return	boolean	True if a serial number is detected
	 */
	public function isBox() {
		$this->getProductInfo();
		return $this->productInfo['shared']['kerio_web_SerialNumber'];
	}

	/**
	 * Get HW box version.
	 *
	 * @param	void
	 * @return	string	Box version
	 */
	public function getBoxVersion() {
		if($this->isBox()) {
			$method = 'SystemHealth.get';
			$params = array( 'histogramType' => 'HistogramTwoHours' );
			$response = $this->getApi()->sendRequest($method, $params);

			$diskTotal = $response['systemHealth']['diskTotal'];
			$twelveGb  = 12288000000;

			switch ($diskTotal) {
				case ($diskTotal < $twelveGb):
					return 'Kerio Operator Box 1210';
				case ($diskTotal > $twelveGb):
					return 'Kerio Operator Box 3210';
				default:
					return 'Unknown HW Box';
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
		return $this->productInfo['product']['SERVER_VERSION'];
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
