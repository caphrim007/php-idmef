<?php

/**
* @author Tim Rupp
*
* Note that this class isn't documented in RFC 4765, so
* this is a best effort attempt at creating it.
*/
class Idmef_Heartbeat_Interval {
	protected $interval;

	public function __construct($interval = false) {
		$this->interval = false;
		$this->setInterval($interval);
	}

	public function setInterval($interval) {
		if (is_int($interval)) {
			$this->interval = $interval;
		}
	}

	public function toXml() {
		$document = DOMDocument('1.0', 'UTF-8');
		$interval = $document->createElement('HeartbeatInterval');

		if (!empty($this->interval)) {
			$integer = $document->createElement('integer', $this->interval);
			$interval->appendChild($integer);
		}

		$document->appendChild($interval);
		return $document->saveXML();
	}
}

?>
