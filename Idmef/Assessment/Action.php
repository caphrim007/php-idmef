<?php

/**
* @author Tim Rupp
*/
class Idmef_Assessment_Action {
	/**
	* The type of action taken. The permitted values are shown below.
	* The default value is "other".  (See also Section 10 of RFC 4765)
	* 
	* Exactly one value allowed
	*
	* @var string
	*/
	private $category;

	/**
	*
	*/
	protected $description;

	/**
	*
	*/
	public function __construct($description = false, $attributes = array()) {
		$this->category = 'other';
		$this->description = false;

		if (isset($attributes['category'])) {
			$this->setCategory($attributes['category']);
		}

		$this->setDescription($description);
	}

	/**
	*
	*/
	public function setDescription($description) {
		// Can, technically be empty
		$this->description = $description;
	}

	/**
	*
	*/
	public function setCategory($category) {
		$validCategories = array(
			'block-installed','notification-sent','taken-offline'
		);

		if (in_array($category, $validCategories)) {
			$this->category = $category;
		} else {
			$this->category = 'other';
		}
	}

	/**
	*
	*/
	public function toXml() {
		$document = new DOMDocument('1.0', 'UTF-8');

		if (!empty($this->description)) {
			$action = $document->createElement('Action', $this->description);
		} else {
			$action = $document->createElement('Action');
		}

		$action->setAttribute('category', $this->category);

		$document->appendChild($action);

		return $document->saveXML();
	}
}

?>
