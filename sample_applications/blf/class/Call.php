<?php
/**
 * Call Class.
 *
 * Call definition class
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
class Call {
	
	public $callStatusUnknown = 0;
	public $callStatusConnecting = 1;
	public $callStatusRinging = 2;
	public $callStatusConnected = 3;
	public $callStatusDisconnecting = 4;
	public $callStatusDisconnected = 5;
	public $callStatusVoicemail = 6;
	public $callStatusFollowme = 7;
	public $callStatusQueueWaiting = 8;
	public $callStatusQueueConnected = 9;
	public $callStatusBusy = 10;
	public $callStatusForbidden = 11;
	public $callStatusPickUp = 12;

	private $call = array();
	
	public function __construct($call) {
		$this->call = $call;
	}
	
	public function getFrom() {
		return $this->call['FROM']['NUMBER'];
	}
	
	public function getTo() {
		return $this->call['TO']['NUMBER'];
	}

	public function getActiveDuration() {
		return $this->call['ANSWERED_DURATION'];
	}
	
	public function getCallDuration() {
		return $this->call['CALL_DURATION'];
	}

	public function getStatus() {
		return $this->call['STATUS'];
	}
}
