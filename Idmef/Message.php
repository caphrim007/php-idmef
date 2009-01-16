<?php

/**
* @author Tim Rupp
*/
class Idmef_Message {
	protected $version;
	protected $alerts;
	protected $heartbeats;

	public function __construct() {
		$this->version = '1.0';
		$this->alerts = false;
		$this->heartbeats = false;
	}

	public function addAlert($classification = 'unknown') {
		$alert = new Idmef_Alert($classification);
		$this->alerts[] = $alert;
		return $alert;
	}

	public function addHeartbeat() {
		$this->heartbeats[] = new Idmef_Heartbeat;
		
	}

	public function toXml() {
		$imp = new DOMImplementation;
		$dtd = $imp->createDocumentType('IDMEF-Message', '-//IETF//DTD RFC XXXX IDMEF v1.0//EN', 'http://security.fnal.gov/idmef.dtd');

		$document = $imp->createDocument('idmef', '', $dtd);
		$document->encoding = 'UTF-8';
		
		$message = $document->createElement('IDMEF-Message');
		// Not defined in DTD, but specified in RFC; go figure
		// $message->setAttribute('idmef', 'http://iana.org/idmef');

		if (!empty($this->alerts)) {
			foreach($this->alerts as $key => $val) {
				$alert = new DOMDocument('1.0', 'UTF-8');
				$alert->loadXML($val->toXml());
				$node = $alert->getElementsByTagName('*')->item(0);

				// Import the node, and all its children, to the document
				$node = $document->importNode($node, true);
				$message->appendChild($node);
			}
		}

		if (!empty($this->heartbeats)) {
			foreach($this->heartbeats as $key => $val) {
				$heartbeat = new DOMDocument('1.0', 'UTF-8');
				$heartbeat->loadXML($val->toXml());
				$node = $heartbeat->getElementsByTagName('*')->item(0);
				$node = $document->importNode($node, true);
				$message->appendChild($node);
			}
		}

		$document->appendChild($message);
		return $document->saveXml();
	}
}

?>
