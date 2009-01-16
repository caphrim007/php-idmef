<?php

/**
* @author Tim Rupp
*/
class Idmef_Alert extends Idmef_Message {
	/**
	* Identification information for the analyzer that originated the alert.
	* 
	* Exactly one value allowed.
	*
	* @var object Idmef_Core_Analyzer
	*/
	protected $analyzer;

	/**
	* The time the alert was created.  Of the three times that may be provided
	* with an Alert, this is the only one that is required.
	*
	* Exactly one value allowed.
	*
	* @var object Idmef_Time_Create
	*/
	protected $createTime;

	/**
	* The "name" of the alert, or other information allowing the manager to
	* determine what it is.
	*
	* Exactly one value allowed.
	*
	* @var object Idmef_Core_Classification
	*/
	protected $classification;

	/**
	* The time the event(s) leading up to the alert was detected. In the case
	* of more than one event, the time the first event was detected. In some
	* circumstances, this may not be the same value as CreateTime.
	*
	* Zero or one value allowed.
	*
	* @var object Idmef_Time_Detect
	*/
	protected $detectTime;

	/**
	* The current time on the analyzer (see Section 6.3 of RFC 4765).
	*
	* Zero or one value allowed.
	*
	* @var object Idmef_Time_Analyzer
	*/
	protected $analyzerTime;

	/**
	* The source(s) of the event(s) leading up to the alert.
	*
	* Zero or more values allowed.
	*
	* @var object Idmef_Core_Source
	*/
	protected $source;

	/**
	* The target(s) of the event(s) leading up to the alert.
	*
	* Zero or more values allowed.
	*
	* @var object Idmef_Core_Target
	*/
	protected $target;

	/**
	* Information about the impact of the event, actions taken by the
	* analyzer in response to it, and the analyzer's confidence in its
	* evaluation.
	*
	* Zero or one value allowed.
	*
	* @var object Idmef_Assessment
	*/
	protected $assessment;

	/**
	* Information included by the analyzer that does not fit into the
	* data model.  This may be an atomic piece of data, or a large amount
	* of data provided through an extension to the IDMEF (see Section 5
	* of RFC 4765).
	*
	* Zero or more values allowed
	*
	* @var object Idmef_Core_AdditionalData
	*/
	protected $additionalData;

	public function __construct($classification) {
		// Required classes
		$this->analyzer = new Idmef_Core_Analyzer;
		$this->createTime = new Idmef_Time_Create;
		$this->classification = new Idmef_Core_Classification($classification);

		// Classes that can be zero'd
		$this->detectTime = false;
		$this->analyzerTime = false;
		$this->source = false;
		$this->target = false;
		$this->assessment = false;
		$this->additionalData = false;
	}

	public function __get($key) {
		switch($key) {
			case 'analyzer':
			case 'createTime':
			case 'classification':
			case 'detectTime':
			case 'analyzerTime':
			case 'source':
			case 'target':
			case 'assessment':
			case 'additionalData':
				return $this->$key;
				break;
			default:
				return false;
		}
	}

	public function addAdditionalData($value) {
		if ($value instanceof Idmef_Core_AdditionalData) {
			$additionalData = $value;
		} else if (is_array($value)) {
			$additionalData = new Idmef_Core_AdditionalData($value['value'], $value['attributes']);
		}

		$this->additionalData[] = $additionalData;
		return $additionalData;
	}

	public function setAnalyzerTime($value = false) {
		if ($value instanceof Idmef_Time_Analyzer) {
			$analyzerTime = $value;
		} else {
			$analyzerTime = new Idmef_Time_Analyzer($value);
		}

		$this->analyzerTime = $analyzerTime;
		return $analyzerTime;
	}

	public function setAssessment($value = false) {
		if ($value instanceof Idmef_Assessment) {
			$assessment = $value;
		} else if (is_array($value)) {
			$assessment = new Idmef_Assessment($value['impact'], $value['action'], $value['confidence']);
		} else {
			$assessment = new Idmef_Assessment;
		}

		$this->assessment = $assessment;
		return $assessment;
	}

	public function setDetectTime($value = false) {
		if ($value instanceof Idmef_Time_Detect) {
			$detectTime = $value;
		} else {
			$detectTime = new Idmef_Time_Detect($value);
		}

		$this->detectTime = $detectTime;
		return $detectTime;
	}

	public function addSource($value = array()) {
		if ($value instanceof Idmef_Core_Source) {
			$source = $value;
		} else if (is_array($value)) {
			$source = new Idmef_Core_Source($value['attributes']);
		}

		$this->source[] = $source;
		return $source;
	}

	public function addTarget($value = array()) {
		if ($value instanceof Idmef_Core_Target) {
			$target = $value;
		} else if (is_array($value)) {
			$target = new Idmef_Core_Target($value['attributes']);
		}

		$this->target[] = $target;
		return $target;
	}

	public function clearSources() {
		$this->source = array();
	}

	public function clearTargets() {
		$this->target = array();
	}

	public function toXml() {
		$document = new DOMDocument($this->version, "UTF-8");
		$alert = $document->createElement('Alert');

		// Analyzer is a required class
		$analyzer = new DOMDocument('1.0', 'UTF-8');
		$analyzer->loadXML($this->analyzer->toXml());
		$import = $analyzer->getElementsByTagName('*')->item(0);
		$import = $document->importNode($import, true);
		$alert->appendChild($import);

		// CreateTime is a required class
		$createTime = new DOMDocument('1.0', 'UTF-8');
		$createTime->loadXML($this->createTime->toXml());
		$import = $createTime->getElementsByTagName('*')->item(0);
		$import = $document->importNode($import, true);
		$alert->appendChild($import);

		if (!empty($this->detectTime)) {
			$detectTime = new DOMDocument('1.0', 'UTF-8');
			$detectTime->loadXML($this->detectTime->toXml());
			$import = $detectTime->getElementsByTagName('*')->item(0);
			$import = $document->importNode($import, true);
			$alert->appendChild($import);
		}

		if (!empty($this->analyzerTime)) {
			$analyzerTime = new DOMDocument('1.0', 'UTF-8');
			$analyzerTime->loadXML($this->analyzerTime->toXml());
			$import = $analyzerTime->getElementsByTagName('*')->item(0);
			$import = $document->importNode($import, true);
			$alert->appendChild($import);
		}

		if (is_array($this->source)) {
			foreach($this->source as $key => $val) {
				$source = new DOMDocument('1.0', 'UTF-8');
				$source->loadXML($val->toXml());
				$import = $source->getElementsByTagName('*')->item(0);
				$import = $document->importNode($import, true);
				$alert->appendChild($import);
			}
		}

		if (is_array($this->target)) {
			foreach($this->target as $key => $val) {
				$target = new DOMDocument('1.0', 'UTF-8');
				$target->loadXML($val->toXml());
				$import = $target->getElementsByTagName('*')->item(0);
				$import = $document->importNode($import, true);
				$alert->appendChild($import);
			}
		}

		// Classification is a required class
		$classification = new DOMDocument('1.0', 'UTF-8');
		$classification->loadXML($this->classification->toXml());
		$import = $classification->getElementsByTagName('*')->item(0);
		$import = $document->importNode($import, true);
		$alert->appendChild($import);

		if (!empty($this->assessment)) {
			$assessment = new DOMDocument('1.0', 'UTF-8');
			$assessment->loadXML($this->assessment->toXml());
			$import = $assessment->getElementsByTagName('*')->item(0);
			$import = $document->importNode($import, true);
			$alert->appendChild($import);
		}

		if (is_array($this->additionalData)) {
			foreach($this->additionalData as $key => $val) {
				$additionalData = new DOMDocument('1.0', 'UTF-8');
				$additionalData->loadXML($val->toXml());
				$import = $additionalData->getElementsByTagName('*')->item(0);
				$import = $document->importNode($import, true);
				$alert->appendChild($import);
			}
		}

		$document->appendChild($alert);
		return $document->saveXml();
	}
}

?>
