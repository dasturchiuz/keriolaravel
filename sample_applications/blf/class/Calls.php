<?php
require_once(dirname(__FILE__) . '/Blf.php');
require_once(dirname(__FILE__) . '/Call.php');

/**
 * Calls Class.
 *
 * Calls class
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
class Calls extends Blf {
	
	private $calls = array();
	private $peer = array();
	
	public function get() {
		$params = array(
			"query" => array(
				"fields" => array(
					"from",
					"to",
					"status",
					"answered_duration",
					"call_duration"
				),
				"limit" => -1,
				"orderBy" => array(array(
					"columnName" => "status",
					"direction" => "Asc"
				))
			)
		);
		$result = $this->api->sendRequest('Status.getCalls',$params);
		
		$calls = $result['calls'];
		$tmp = array();
		
		foreach ($calls as $line) {
			array_push($tmp, new Call($line));
		}
		
		$this->calls = $tmp;
		return $this->calls;
	}
	
	public function isActive($number) {
		foreach ($this->calls as $call) {
			$from = $call->getFrom();
			$to = $call->getTo();
			if ($number == $from || ($number == $to)) return $call;
		}
		return FALSE;
	}
}
