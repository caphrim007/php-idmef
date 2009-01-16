<?php

/**
* @author Tim Rupp
*/
class Idmef_Support_UserId {
	/**
	* A unique identifier for the user id, see Section 3.2.9 of
	* RFC 4765
	*
	* This value is optional
	*
	* @var string
	*/
	private $ident;

	/**
	* The type of user information represented. The permitted
	* values for this attribute are shown below. The default
	* value is "original-user". (See also Section 10 of RFC 4765)
	*
	* This value is optional
	*
	* @var string
	*/
	private $type;

	/**
	* The tty the user is using.
	*
	* This value is optional
	*
	* @var string
	*/
	private $tty;

	/**
	* A user or group name.
	*
	* Zero or one value allowed
	*
	* @var string
	*/
	protected $name;

	/**
	* A user or group number.
	*
	* Zero or one value allowed
	*
	* @var integer
	*/
	protected $number;

	public function __construct($name = false, $number = false, $attributes = array()) {
		// Attributes to zero
		$this->ident = false;
		$this->type = false;
		$this->tty = false;

		// Values that can be zero
		$this->name = false;
		$this->number = false;

		if (!empty($attributes['ident'])) {
			$this->setIdent($attributes['ident']);
		}

		if (!empty($attributes['type'])) {
			$this->setType($attributes['type']);
		}

		if (!empty($attributes['tty'])) {
			$this->setTty($attributes['tty']);
		}

		$this->setName($name);
		$this->setNumber($number);
	}

	public function setName($name) {
		if (!empty($name)) {
			$this->name = $name;
		}
	}

	public function setNumber($number) {
		if (is_numeric($number)) {
			$this->number = $number;
		}
	}

	public function setIdent($ident) {
		if (!empty($ident)) {
			$this->ident = $ident;
		}
	}

	public function setType($type) {
		$validTypes = array(
			'current-user', 'target-user', 'user-privs',
			'current-group', 'group-privs', 'other-privs'
		);

		if (in_array($type, $validTypes)) {
			$this->type = $type;
		} else {
			$this->type = 'original-user';
		}
	}

	public function setTty($tty) {
		if (!empty($tty)) {
			$this->tty = $tty;
		}
	}

	public function toXml() {
		$document = new DOMDocument('1.0', 'UTF-8');
		$userId = $document->createElement('UserId');

		if (!empty($this->ident)) {
			$userId->setAttribute('ident', $this->ident);
		}

		if (!empty($this->type)) {
			$userId->setAttribute('type', $this->type);
		}

		if (!empty($this->tty)) {
			$userId->setAttribute('tty', $this->tty);
		}

		if (!empty($this->name)) {
			$name = $document->createElement('name', $this->name);
			$userId->appendChild($name);
		}

		if (!empty($this->number)) {
			$number = $document->createElement('number', $this->number);
			$userId->appendChild($number);
		}

		$document->appendChild($userId);
		return $document->saveXML();
	}
}

?>
