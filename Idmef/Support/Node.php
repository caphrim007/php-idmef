<?php

/**
* @author Tim Rupp
*/
class Idmef_Support_Node {
	/**
	* A unique identifier for the node; see Section 3.2.9 of RFC 4765
	*
	* This value is optional
	*
	* @var string
	*/
	private $ident;

	/**
	* The "domain" from which the name information was obtained, if
	* relevant.  The permitted values for this attribute are shown
	* in section 4.2.7.2 of RFC 4765.  The default value is "unknown".
	* (See also Section 10 of RFC 4765 for extensions to the table.)
	*
	* This value is optional
	*
	* @var string
	*/
	private $category;

	/**
	* The location of the equipment.
	*
	* Zero or one value allowed
	*
	* @var string
	*/
	protected $location;

	/**
	* The name of the equipment.  This information MUST be provided
	* if no Address information is given.
	*
	* Zero or one value
	*
	* @var string
	*/
	protected $name;

	/**
	* The network or hardware address of the equipment. Unless a name
	* (above) is provided, at least one address must be specified.
	*
	* Zero or more values allowed
	*
	* @var array
	*/
	protected $address;

	/**
	*
	*/
	public function __construct($name = false, $location = false, $address = false, $attributes = array()) {
		// Attributes that can be zero'd
		$this->ident = false;
		$this->category = false;

		// Classes that can be zero'd
		$this->address = false;

		// Set available values
		$this->setLocation($location);
		$this->setName($name);
		$this->addAddress($address);

		// Set all available attributes
		if (isset($attributes['ident'])) {
			$this->setIdent($attributes['ident']);
		}

		if (isset($attributes['category'])) {
			$this->setCategory($attributes['category']);
		}
	}

	/**
	*
	*/
	public function setLocation($location) {
		if (!empty($location)) {
			$this->location = $location;
		}
	}

	/**
	*
	*/
	public function setName($name) {
		if (!empty($name)) {
			$this->name = $name;
		}
	}

	/**
	*
	*/
	public function addAddress($value) {
		$address = false;

		if ($value instanceof Idmef_Support_Address) {
			$address = $value;
		} else if (is_array($value)) {
			$address = new Idmef_Support_Address($value['address'], $value['attributes']);
		} else if (!empty($value)) {
			$address = new Idmef_Support_Address($value);
		}

		if (!empty($address)) {
			$this->address[] = $address;
			return $address;
		}
	}

	/**
	*
	*/
	public function setCategory($category) {
		$validCategories = array(
			'ads','afs','coda','dfs',
			'dns','hosts','kerberos','nds',
			'nis','nisplus','nt','wfw'
		);

		if (empty($this->name)) {
			throw new Idmef_Support_Exception('Cannot have a category without a name');
		}

		$category = strtolower($category);
		if (!in_array($category, $validCategories)) {
			$this->category = 'unknown';
		} else {
			$this->category = $category;
		}
	}

	/**
	*
	*/
	public function setIdent($ident) {
		if (!empty($ident)) {
			$this->ident = $ident;
		}
	}

	/**
	*
	*/
	public function toXml() {
		$document = new DOMDocument('1.0', 'UTF-8');
		$node = $document->createElement('Node');

		if (empty($this->name) && empty($this->address)) {
			throw new Idmef_Support_Exception('Address or name information must be provided');
		}

		if (!empty($this->location)) {
			$location = $document->createElement('location', $this->location);
			$node->appendChild($location);
		}

		if (!empty($this->name)) {
			$name = $document->createElement('name', $this->name);
			$node->appendChild($name);
		}

		if (!empty($this->address)) {
			foreach ($this->address as $key => $val) {
				$childAddress = new DOMDocument('1.0', 'UTF-8');
				$childAddress->loadXML($val->toXml());
				$address = $childAddress->getElementsByTagName('*')->item(0);

				// Import the node, and all its children, to the document
				$address = $document->importNode($address, true);

				// And then append it to the "<root>" node
				$node->appendChild($address);
			}
		}

		if (!empty($this->ident)) {
			$node->setAttribute('ident', $this->ident);
		}

		// Category information is not relevant (per RFC) if name
		// is not specified
		if(!empty($this->name) && !empty($this->category)) {
			$node->setAttribute('category', $this->category);
		}

		$document->appendChild($node);
		return $document->saveXML();
	}
}

?>
