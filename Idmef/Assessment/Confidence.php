<?php

/**
* @author Tim Rupp
*/
class Idmef_Assessment_Confidence {
	/**
	* The analyzer's rating of its analytical validity. The permitted
	* values are shown in section 4.2.6.3 of RFC 4765. The default value
	* is "numeric". (See also Section 10 of RFC 4765)
	*
	* One value allowed
	*
	* @var float
	*/
	private $rating;

	/**
	* Specifes the probability that the system that produced
	* the confidence rating has the ability to do so reasonably
	*
	* One of two possible values is legal
	*
	*	- reasonable
	*	- rough
	*
	* Based on the value set here, the format of XML returned
	* will either include the numeric confidence value in the
	* Confidence element or wont, respectively.
	*
	* @var string
	*/
	protected $heuristic;

	/**
	*
	*/
	public function __construct($confidence = false, $attributes = array()) {
		$this->confidence = false;
		$this->rating = 'numeric';
		$this->heuristic = 'rough';

		if (isset($attributes['rating'])) {
			$this->setRating($attributes['rating']);
		}

		if (isset($attributes['heuristic'])) {
			$this->setConfidence($confidence, $attributes['heuristic']);
		} else {
			$this->setConfidence($confidence);
		}
	}

	/**
	*
	*/
	public function setRating($rating) {
		$validRatings = array(
			'low','medium','high'
		);

		if (in_array($rating, $validRatings)) {
			$this->rating = $rating;
		} else {
			$this->rating = 'numeric';
		}
	}

	/**
	*
	*/
	public function setConfidence($confidence, $heuristic) {
		if ($heuristic == 'rough') {
			// Element will be empty if rough heuristic
			$this->confidence = false;
			return;
		} else {
			if ($confidence >= 0 && <= 1) {
				$this->confidence = round($confidence, 1);
			} else {
				throw new Idmef_Assessment_Exception('Confidence rating must be between 0.0 and 1.0');
			}
		}
	}

	/**
	*
	*/
	public function toXml() {
		$document = new DOMDocument('1.0', 'UTF-8');

		if (is_numeric($this->confidence)) {
			$confidence = $document->createElement('Confidence', $this->confidence)
		} else {
			$confidence = $document->createElement('Confidence');
		}

		$confidence->setAttribute('rating', $this->rating);
		$document->appendChild($confidence);

		return $document->saveXML();
	}
}

?>
