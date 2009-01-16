<?php

/**
* @author Tim Rupp
*/
class Idmef_Assessment_Impact {
	/**
	* An estimate of the relative severity of the event.  The permitted
	* values are shown in section 4.2.6.1 of RFC 4765. There is no default
	* value. (See also Section 10 of RFC 4765)
	*
	* Zero or one value allowed
	*
	* @var string
	*/
	private $severity;

	/**
	* An indication of whether the analyzer believes the attempt that
	* the event describes was successful or not. The permitted values
	* are shown in section 4.2.6.1 of RFC 4765. There is no default value.
	* (See also Section 10 of RFC 4765)
	*
	* Zero or one value allowed
	*
	* @var string
	*/
	private $completion;

	/**
	* The type of attempt represented by this event, in relatively broad
	* categories. The permitted values are shown in section 4.2.6.1 of
	* RFC 4765. The default value is "other". (See also Section 10 of RFC 4765)
	*
	* Zero or one value allowed
	*
	* @var string
	*/
	private $type;

	/**
	*
	*/
	protected $description;

	/**
	*
	*/
	public function __construct($description = false, $attributes = array()) {
		$this->description = false;
		$this->severity = false;
		$this->completion = false;
		$this->type = 'other';

		$this->setDescription($description);

		if (isset($attributes['severity'])) {
			$this->setSeverity($attributes['severity']);
		}

		if (isset($attributes['completion'])) {
			$this->setCompletion($attributes['completion']);
		}

		if (isset($attributes['type'])) {
			$this->setType($attributes['type']);
		}
	}

	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	*
	*/
	public function setSeverity($severity) {
		$validSeverity = array(
			'info', 'low', 'medium', 'high'
		);

		if (in_array($severity, $validSeverity)) {
			$this->severity = $severity;
		}
	}

	/**
	*
	*/
	public function setCompletion($completion) {
		$validCompletion = array(
			'failed', 'succeeded'
		);

		if (in_array($completion, $validCompletion)) {
			$this->completion = $completion;
		}
	}

	/**
	*
	*/
	public function setType($type) {
		$validTypes = array(
			'admin', 'dos', 'file',
			'recon', 'user'
		);

		if (in_array($type, $validTypes)) {
			$this->type = $type;
		} else {
			$this->type = 'other';
		}
	}

	/**
	*
	*/
	public function toXml() {
		$document = new DOMDocument('1.0', 'UTF-8');
		if (!empty($this->description)) {
			$impact = $document->createElement('Impact', $this->description);
		} else {
			$impact = $document->createElement('Impact');
		}

		if (!empty($this->severity)) {
			$impact->setAttribute('severity', $this->severity);
		}

		if (!empty($this->completion)) {
			$impact->setAttributes('completion', $this->completion);
		}

		if (!empty($this->type)) {
			$impact->setAttribute('type', $this->type);
		}

		$document->appendChild($impact);

		return $document->saveXML();
	}
}

?>
