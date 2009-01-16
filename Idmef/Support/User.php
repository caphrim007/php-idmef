<?php

/**
* @author Tim Rupp
*/
class Idmef_Support_User {
	/**
	* A unique identifier for the user; see Section 3.2.9 of RFC 4765
	*
	* This value is optional
	*
	* @var string
	*/
	private $ident;

	/**
	* The type of user represented. The permitted values for this
	* attribute are shown below. The default value is "unknown".
	* (See also Section 10 of RFC 4765)
	*
	* This value is optional
	*
	* @var string
	*/
	private $category;

	/**
	* Identification of a user, as indicated by its type attribute
	* (see Section 4.2.7.3.1 of RFC 4765).
	*
	* One or more values allowed
	*
	* @var object Idmef_Support_UserId
	*/
	protected $userId;

	public function __construct($userId, $attributes = array()) {
		// Attributes that can be zero'd
		$this->ident = false;
		$this->category = false;

		// Values that can be zero'd
		$this->userId = false;

		if (!empty($attributes['ident'])) {
			$this->setIdent($attributes['ident']);
		}

		if (!empty($attributes['category'])) {
			$this->setCategory($attributes['category']);
		}

		$this->setUserId($userId);
	}

	public function setIdent($ident) {
		if (!empty($ident)) {
			$this->ident = $ident;
		}
	}

	public function setCategory($category) {
		$validCategories = array('application', 'os-device');

		if (in_array($category, $validCategories)) {
				$this->category = $category;
		} else {
			$this->category = 'unknown';
		}
	}

	public function setUserId($value) {
		if ($value instanceof Idmef_Support_UserId) {
			$userId = $value;
		} else if (is_array($userId)) {
			$userId = new Idmef_Support_UserId($value['name'],
				$value['number'],
				$value['attributes']
			);
		} else {
			$userId = new Idmef_SupportUserId;
		}

		$this->userId[] = $userId;
		return $userId;
	}

	public function toXml() {
		$document = new DOMDocument('1.0', 'UTF-8');
		$user = $document->createElement('User');

		if (!empty($this->ident)) {
			$user->setAttribute('ident', $this->ident);
		}

		if (!empty($this->category)) {
			$user->setAttribute('category', $this->category);
		}

		if (!empty($this->userId)) {
			foreach($this->userId as $key => $val) {
				$userId = new DOMDocument('1.0', 'UTF-8');
				$userId->loadXML($val->toXml());
				$import = $userId->getElementsByTagName('*')->item(0);
				$import = $document->importNode($import, true);
				$user->appendChild($import);
			}
		}

		$document->appendChild($user);
		return $document->saveXML();
	}
}

?>
