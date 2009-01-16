<?php

/**
* @author Tim Rupp
*/
class Idmef_Time_Detect extends Idmef_Time_Abstract {
	public function __construct($value = null) {
		$this->setTime($value);
	}

	public function toXml() {
		$datetime = $this->dateTime->get(Zend_Date::ISO_8601);

		$document = new DOMDocument("1.0", "UTF-8");
		$create = $document->createElement('DetectTime', $datetime);
		$create->setAttribute('ntpstamp', parent::unix2Ntp($datetime));

		$document->appendChild($create);
		return $document->saveXML();
	}
}

?>
