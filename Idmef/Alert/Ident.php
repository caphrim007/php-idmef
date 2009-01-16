<?php

/**
* @author Tim Rupp
*/
class Idmef_Alert_Ident {
	private $analyzerId;
	protected $alertIdent;

	public function __construct($alertIdent, $attributes = array()) {
		$this->alertIdent = false;
		$this->analyzerId = false;

		$this->setIdent($alertIdent);

		if (!empty($attributes['analyzerid'])) {
			$this->setAnalyzerId($attributes['analyzerid']);
		}
	}

	public function setIdent($ident) {
		if (!empty($ident)) {
			$this->alertIdent = $ident;
		}
	}

	public function setAnalyzerId($id) {
		if (!empty($id)) {
			$this->analyzerId = $id;
		}
	}

	public function toXml() {
		$document = new DOMDocument('1.0', 'UTF-8');
		$ident = $document->createElement('analyzerident', $this->alertIdent);

		if (!empty($this->analyzerId)) {
			$ident->setAttribute('analyzerid', $this->analyzerId);
		}

		$document->appendChild($ident);
		return $document->saveXML();
	}
}

?>
