<?php
/**
 * BLF Class.
 *
 * Main class
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
class Blf {
	
	private $supportedVersions = array(
		'1.2.X' => 16908545,
		'2.0.0' => 33554432
	);

	protected $api;
	
	public function __construct($api) {
		$this->api = $api;
		
		if ($this->supportedVersions['2.0.0'] > $this->api->getApiVersion()) {
			die('Error: Kerio Operator 2.0.0 or newer is required.');
		}
	}
}
