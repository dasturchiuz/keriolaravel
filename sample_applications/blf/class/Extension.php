<?php
/**
 * Extension Class.
 *
 * Extension definition class
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
class Extension {

	public $singleLine = 0;
	public $multipleLine = 1;
	public $multipleGroup = 2;

	private $line = array();

	public function __construct($line) {
		$this->line = $line;
	}

	public function get() {
		return $this->line;
	}

	public function getUsername() {
		$username = (isset($this->line['FULL_NAME'])) ? $this->line['FULL_NAME'] : $this->line['USERNAME'];
		return ($username) ? $username : '(Not-assigned)';
	}

	public function getNumber() {
		return $this->line['telNum'];
	}

	public function getSipNumber() {
		return $this->line['sipUsername'];
	}

	public function getLineType() {
		return $this->line['lineType'];
	}

	public function isRegistered() {
		return (1 == $this->line['sipStatus']['registered']) ? TRUE : FALSE;
	}
}
