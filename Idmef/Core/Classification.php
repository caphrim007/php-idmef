<?php

/**
* @author Tim Rupp
*/
class Idmef_Core_Classification {
	/**
	* Information about the message, pointing to external documentation
	* sites, that will provide background information about the alert.
	*
	* Zero or more values allowed.
	*
	* @var object Idmef_Support_Reference
	*/
	protected $reference;

	/**
	* Textual description of the classification
	*
	* ex.
	* 	<idmef:Classification text="Teardrop detected">
	*
	* @var string
	*/
	private $text;

	/**
	*
	*/
	public function __construct($text) {
		// Classes that can be zero'd
		$this->reference = false;
		$this->text = false;

		$this->setText($text);
	}

	/**
	* Adds a new reference to the classification
	*/
	public function addReference($name, $url, $attributes = array()) {
		$this->reference[] = new Idmef_Support_Reference($name, $url, $attributes);
	}

	public function setText($text) {
		if (!empty($text)) {
			$this->text = $text;
		}
	}

	/**
	*
	*/
	public function toXml() {
		$document = new DOMDocument("1.0", "UTF-8");
		$classification = $document->createElement('Classification');

		if (!empty($this->text)) {
			$classification->setAttribute('text', $this->text);
		}

		if (is_array($this->reference)) {
			foreach($this->reference as $key => $val) {
				$reference = new DOMDocument('1.0', 'UTF-8');
				$reference->loadXML($val->toXml());
				$node = $reference->getElementsByTagName('*')->item(0);
				$node = $document->importNode($node, true);
				$classification->appendChild($node);
			}
		}

		$document->appendChild($classification);

		return $document->saveXML();
	}
}

?>
