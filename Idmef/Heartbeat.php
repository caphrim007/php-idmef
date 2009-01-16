<?php

/**
* @author Tim Rupp
*/
class Idmef_Heartbeat {
	/**
	* Identification information for the analyzer that originated the heartbeat.
	*
	* Exactly one value allowed.
	*
	* @var object Idmef_Core_Analyzer
	*/
	protected $analyzer;

	/**
	*The time the heartbeat was created.
	*
	* Exactly one value allowed.
	*
	* @var object Idmef_Time_Create
	*/
	protected $createTime;

	/**
	* The interval in seconds at which heartbeats are generated.
	*
	* Zero or one value allowed.
	*
	* @var object Idmef_Heartbeat_Interval
	*/
	protected $heartbeatInterval;

	/**
	* The current time on the analyzer (see Section 6.3 of RFC 4765).
	*
	* Zero or one value allowed
	*
	* @var object Idmef_Time_Analyzer
	*/
	protected $analyzerTime;

	/**
	* Information included by the analyzer that does not fit into the
	* data model.  This may be an atomic piece of data or a large amount
	* of data provided through an extension to the IDMEF (see Section 5
	* of RFC 4765).
	*
	* Zero or more values allowed.
	*
	* @var object Idmef_Core_AdditionalData
	*/
	protected $additionalData;

	/**
	* A unique identifier for the heartbeat; see Section 3.2.9 of RFC 4765.
	*
	* This value is optional
	*
	* @var string
	*/
	private $messageId;

	/**
	*
	*/
	public function __construct($attributes = array()) {
		$analyzer = new Idmef_Core_Analyzer;
		$this->createTime = new Idmef_Time_Create;
		$this->setAnalyzer($analyzer);

		// Attributes that can be zero'd
		$this->messageId = false;

		// Class aggregates that can be zero'd
		$this->heartbeatInterval = false;
		$this->analyzerTime = false;
		$this->additionalData = false;

		if (!empty($attributes['messageId'])) {
			$this->setMessageId($attributes['messageId']);
		}
	}

	/**
	*
	*/
	public function setAnalyzer($analyzer) {
		if ($analyzer instanceof Idmef_Core_Analyzer) {
			$this->analyzer = $analyzer;
		} else if (is_array($analyzer)) {
			$this->analyzer = new Idmef_Core_Analyzer($analyzer['attributes']);
		}
	}

	/**
	* Sets the current time of the analyzer.
	*
	* Time can be specified as either an object of type Idmef_Time_analyzer
	* or a specific date-time string in the format specified in section
	* 3.2.6.5 of RFC 4765
	*/
	public function setAnalyzerTime($analyzerTime) {
		if ($analyzerType instanceof Idmef_Time_Analyzer) {
			$this->analyzerType = $analyzerType
		} else {
			$dateTime = new Zend_Date($analyzerTime);
		}
	}

	/**
	*
	*/
	public function addAdditionalData($additionalData) {
		if ($additionalData instanceof Idmef_Core_AdditionalData) {
			$this->additionalData[] = $additionalData;
		}
	}

	public function setMessageId($messageId) {
		if (!empty($messageId)) {
			$this->messageId = $messageId;
		}
	}

	/**
	* ex:
	*	<idmef:Heartbeat messageid="abc123456789">
	*		<idmef:Analyzer analyzerid="hq-dmz-analyzer01">
	*			<idmef:Node category="dns">
	*				<idmef:location>Headquarters DMZ Network</idmef:location>
	*				<idmef:name>analyzer01.example.com</idmef:name>
	*			</idmef:Node>
	*		</idmef:Analyzer>
	*		<idmef:CreateTime ntpstamp="0xbc722ebe.0x00000000">
	*			2000-03-09T14:07:58Z
	*		</idmef:CreateTime>
	*		<idmef:AdditionalData type="real" meaning="%memused">
	*			<idmef:real>62.5</idmef:real>
	*		</idmef:AdditionalData>
	*		<idmef:AdditionalData type="real" meaning="%diskused">
	*			<idmef:real>87.1</idmef:real>
	*		</idmef:AdditionalData>
	*	</idmef:Heartbeat>
	*/
	public function toXml() {
		$document = new DOMDocument('1.0', 'UTF-8');
		$heartbeat = $document->createElement('Heartbeat');

		if (!empty($this->messageId)) {
			$heartbeat->setAttribute('messageid', $this->messageId);
		}

		// Analyzer is required
		$analyzer = new DOMDocument('1.0', 'UTF-8');
		$analyzer->loadXML($this->analyzer->toXml());
		$import = $analyzer->getElementsByTagName('*')->item(0);
		$import = $document->importNode($import, true);
		$heartbeat->appendChild($import);

		// CreateTime is required
		$createTime = new DOMDocument('1.0', 'UTF-8');
		$createTime->loadXML($this->createTime->toXml());
		$import = $createTime->getElementsByTagName('*')->item(0);
		$import = $document->importNode($import, true);
		$heartbeat->appendChild($import);

		if (!empty($this->heartbeatInterval)) {
			$heartbeatInterval = new DOMDocument('1.0', 'UTF-8');
			$heartbeatInterval->loadXML($this->heartbeatInterval->toXml());
			$import = $heartbeatInterval->getElementsByTagName('*')->item(0);
			$import = $document->importNode($import, true);
			$heartbeat->appendChild($import);
		}

		if (!empty($this->analyzerTime)) {
			$analyzerTime = new DOMDocument('1.0', 'UTF-8');
			$analyzerTime->loadXML($this->analyzerTime->toXml());
			$import = $analyzerTime->getElementsByTagName('*')->item(0);
			$import = $document->importNode($import, true);
			$heartbeat->appendChild($import);
		}

		foreach($this->additionalData as $key => $val) {
			$result .= $val->toXml();
		}

		return $result;
	}
}

?>
