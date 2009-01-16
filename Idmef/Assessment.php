<?php

/**
* @author Tim Rupp
*/
class Idmef_Assessment {
	/**
	* The analyzer's assessment of the impact of the event on the target(s).
	*
	* Zero or one value allowed
	*
	* @var object Idmef_Assessment_Impact
	*/
	protected $impact;

	/**
	* The action(s) taken by the analyzer in response to the event.
	*
	* Zero or more values allowed
	*
	* @var object Idmef_Assessment_Action
	*/
	protected $action;

	/**
	* A measurement of the confidence the analyzer has in its evaluation of the event.
	*
	* Zero or one value allowed
	*
	* @var object Idmef_Assessment_Confidence
	*/
	protected $confidence;

	public function __construct($impact = false, $action = false, $confidence = false) {
		$this->impact = false;
		$this->action = false;
		$this->confidence = false;

		if (!empty($impact)) {
			$this->setImpact($impact);
		}

		if (!empty($action)) {
			$this->addAction($action);
		}

		if (!empty($confidence)) {
			$this->setConfidence($confidence);
		}
	}

	public function setImpact($value) {
		if ($value instanceof Idmef_Assessment_Impact) {
			$this->impact = $value;
		} else if (is_array($value)) {
			$this->impact = new Idmef_Assessment_Impact($value['description'], $value['attributes']);
		}
	}

	public function addAction($value) {
		$action = false;

		if ($value instanceof Idmef_Assessment_Action) {
			$action = $value;
		} else if (is_array($value)) {
			$action = new Idmef_Assessment_Action($value['description'], $value['attributes']);
		}

		if (!empty($action)) {
			$this->action[] = $action;
			return $action;
		}
	}

	public function setConfidence($value) {
		if ($value instanceof Idmef_Assessment_Confidence) {
			$this->confidence = $value;
		} else if (is_array($value)) {
			$this->confidence = new Idmef_Assessment_Confidence($value['description'], $value['attributes']);
		}
	}

	public function toXml() {
		$document = new DOMDocument('1.0', 'UTF-8');
		$assessment = $document->createElement('Assessment');

		if (!empty($this->impact)) {
			$impact = new DOMDocument('1.0', 'UTF-8');
			$impact->loadXML($this->impact->toXml());
			$import = $impact->getElementsByTagName('*')->item(0);
			$import = $document->importNode($import, true);
			$assessment->appendChild($import);
		}

		if (!empty($this->action)) {
			foreach($this->action as $key => $val) {
				$action = new DOMDocument('1.0', 'UTF-8');
				$action->loadXML($val->toXml());
				$import = $action->getElementsByTagName('*')->item(0);
				$import = $document->importNode($import, true);
				$assessment->appendChild($import);
			}
		}

		if (!empty($this->confidence)) {
			$confidence = new DOMDocument('1.0', 'UTF-8');
			$confidence->loadXML($this->confidence->toXml());
			$import = $confidence->getElementsByTagName('*')->item(0);
			$import = $document->importNode($import, true);
			$assessment->appendChild($import);
		}

		$document->appendChild($assessment);
		return $document->saveXML();
	}
}

?>
