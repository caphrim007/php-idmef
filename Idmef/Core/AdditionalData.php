<?php

/**
* @author Tim Rupp
*/
class Idmef_Core_AdditionalData {
	/**
	* Value to store in the Element
	*
	* @var mixed
	*/
	private $value;

	/**
	* A string describing the meaning of the element content.
	* These values will be vendor/implementation dependent
	* This is an optional attribute. If not specified, it will
	* not be included in the Element
	*
	* @var string
	*/
	private $meaning;

	/**
	* Specifies the type of data that is stored in the class
	* If not specified, the value will be determined automatically
	*
	* @var string;
	*/
	private $type;

	/**
	*
	*/
	public function __construct($value, $attributes = array()) {
		$this->meaning = false;
		$this->type = false;
		$this->value = $value;

		if (isset($attributes['type'])) {
			$this->setType($attributes['type']);
		} else {
			$this->type = Idmef_Util::guessType($value);
		}

		if (isset($attributes['meaning'])) {
			$this->meaning = $attributes['meaning'];
		}
	}

	/**
	*
	*/
	public function setType($type) {
		$type = Idmef_Util::validateType($type);

		if ($type !== false) {
			$this->type = $type;
			return true;
		} else {
			$this->type = false;
			return false;
		}
	}

	/**
	*
	*/
	public function toXml() {
		$document = new DOMDocument('1.0', 'UTF-8');

		$element = $document->createElement('AdditionalData');
		$element->setAttribute('type', $this->type);
		$type = $document->createElement($this->type, $this->value);
		$element->appendChild($type);

		if ($this->meaning !== false) {
			$element->setAttribute('meaning', $this->meaning);
		}

		$document->appendChild($element);
		return $document->saveXML();
	}
}

?>
