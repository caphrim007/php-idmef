<?php

/**
* @author Tim Rupp
*/
class Idmef_Support_Reference {
	/**
	* The name of the alert, from one of the origins listed below.
	*
	* Exactly one value allowed.
	*
	* @var string
	*/
	protected $name;

	/**
	* A URL at which the manager (or the human operator of the manager)
	* can find additional information about the alert. The document
	* pointed to by the URL may include an in-depth description of the
	* attack, appropriate countermeasures, or other information deemed
	* relevant by the vendor.
	*
	* Exactly one value allowed.
	*
	* @var string
	*/
	protected $url;

	/**
	* The source from which the name of the alert originates. The permitted
	* values for this attribute are shown below. The default value is "unknown".
	* (See also Section 10.)
	*
	* This value is required
	*
	* @var string
	*/
	protected $origin;

	/**
	* The meaning of the reference, as understood by the alert provider. This
	* field is only valid if the value of the <origin> attribute is set to
	* "vendor-specific" or "user-specific".
	*
	* This value is optional
	*
	* @var string
	*/
	protected $meaning;

	/**
	*
	*/
	public function __construct($name, $url, $attributes) {
		$this->setName($name);
		$this->setUrl($url);

		if (!empty($attributes['origin'])) {
			$this->setOrigin($attributes['origin']);
		} else {
			$this->setOrigin('unknown');
		}

		if (!empty($attributes['meaning'])) {
			$this->setMeaning($attributes['meaning']);
		}
	}

	/**
	*
	*/
	public function setName($name) {
		$this->name = $name;
	}

	/**
	*
	*/
	public function setUrl($url) {
		$this->url = htmlentities($url);
	}

	/**
	*
	*/
	public function setMeaning($value) {
		$this->meaning = $value;
	}

	/**
	*
	*/
	public function setOrigin($origin) {
		$origin = strtolower($origin);
		$validOrigin = array(
			'unknown', 'vendor-specific', 'user-specific',
			'bugtraqid', 'cve', 'osvdb'
		);

		if (in_array($origin, $validOrigin)) {
			return $this->origin = $origin;
		} else {
			return $this->origin = 'unknown';
		}
	}

	/**
	* ex:
	*	<idmef:Reference origin="bugtraqid">
	*		<idmef:name>124</idmef:name>
	*		<idmef:url>http://www.securityfocus.com/bid/124</idmef:url>
	*	</idmef:Reference>
	*/
	public function toXml() {
		$document = new DOMDocument('1.0', 'UTF-8');
		$element = $document->createElement('Reference');
		$element->setAttribute('origin', $this->origin);

		if ($this->origin == 'vendor-specific' || $this->origin == 'user-specific') {
			if (!empty($this->meaning)) {
				$element->setAttribute('meaning', $this->meaning);
			}
		}

		$name = $document->createElement('name', $this->name);
		$url = $document->createElement('url', $this->url);
		$element->appendChild($name);
		$element->appendChild($url);

		$document->appendChild($element);

		return $document->saveXML();
	}
}

?>
