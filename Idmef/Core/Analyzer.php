<?php

/**
* @author Tim Rupp
*/
class Idmef_Core_Analyzer {
	/**
	* A unique identifier for the analyzer; see Section 3.2.9
	* of RFC 4765
	*
	* This value is optional (but see section 4.2.4.1 of RFC
	* 4765 for caveats
	*
	* @var string
	*/
	private $analyzerId;

	/**
	* An explicit name for the analyzer that may be easier to
	* understand than the analyzerid.
	*
	* This value is optional
	*
	* @var string
	*/
	private $name;

	/**
	* The manufacturer of the analyzer software and/or hardware.
	*
	* This value is optional
	*
	* @var string
	*/
	private $manufacturer;

	/**
	* The model name/number of the analyzer software and/or hardware.
	*
	* This value is optional
	*
	* @var string
	*/
	private $model;

	/**
	* The version number of the analyzer software and/or hardware.
	*
	* This value is optional
	*
	* @var string
	*/
	private $version;

	/**
	* The class of analyzer software and/or hardware.
	*
	* This value is optional
	*
	* @var string
	*/
	private $class;

	/**
	* Operating system name.  On POSIX 1003.1 compliant systems,
	* this is the value returned in utsname.sysname by the uname()
	* system call, or the output of the "uname -s" command.
	*
	* This value is optional
	*
	* @var string
	*/
	private $osType;

	/**
	* Operating system version.  On POSIX 1003.1 compliant systems,
	* this is the value returned in utsname.release by the uname()
	* system call, or the output of the "uname -r" command.
	*
	* This value is optional
	*
	* @var string
	*/
	private $osVersion;

	/**
	* Information about the host or device on which the analyzer
	* resides (network address, network name, etc.).
	*
	* Zero or one value allowed.
	*
	* @var object Idmef_Support_Node
	*/
	protected $node;

	/**
	* Information about the process in which the analyzer is executing.
	*
	* Zero or one value allowed.
	*
	* @var object Idmef_Support_Process
	*/
	protected $process;

	/**
	* Information about the analyzer from which the message may have
	* gone through.  The idea behind this mechanism is that when a
	* manager receives an alert and wants to forward it to another
	* analyzer, it needs to substitute the original analyzer information
	* with its own.  To preserve the original analyzer information, it
	* may be included in the new analyzer definition. This will allow
	* analyzer path tracking.
	*
	* Zero or one value allowed.
	*
	* @var object Idmef_Core_Analyzer
	*/
	protected $analyzer;

	/**
	*
	*/
	public function __construct($attributes = array()) {
		// Attributes that can be zero'd
		$this->analyzerId = 0;
		$this->name = false;
		$this->manufacturer = false;
		$this->model = false;
		$this->version = false;
		$this->class = false;
		$this->osType = false;
		$this->osVersion = false;

		// Classes that can be zero'd
		$this->node = false;
		$this->process = false;
		$this->analyzer = false;

		// Set all available attributes
		if (isset($attributes['analyzerid'])) {
			$this->setAnalyzerId($attributes['analyzerid']);
		}

		if (isset($attributes['name'])) {
			$this->setName($attributes['name']);
		}

		if (isset($attributes['manufacturer'])) {
			$this->setManufacturer($attributes['manufacturer']);
		}

		if (isset($attributes['model'])) {
			$this->setModel($attributes['model']);
		}

		if (isset($attributes['version'])) {
			$this->setVersion($attributes['version']);
		}

		if (isset($attributes['class'])) {
			$this->setClass($attributes['class']);
		}

		if (isset($attributes['ostype'])) {
			$this->setOsType($attributes['ostype']);
		}

		if (isset($attributes['osversion'])) {
			$this->setOsVersion($attributes['osversion']);
		}
	}

	/**
	*
	*/
	public function setAnalyzerId($id) {
		if (!empty($id) && !is_int($id)) {
			// 0 is allowed here
			$this->analyzerId = $id;
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
	public function setManufacturer($manufacturer) {
		if (!empty($manufacturer)) {
			$this->manufacturer = $manufacturer;
		}
	}

	/**
	*
	*/
	public function setModel($model) {
		if (!empty($model)) {
			$this->model = $model;
		}
	}

	/**
	*
	*/
	public function setVersion($verion) {
		if (!empty($verison)) {
			$this->version = $version;
		}
	}

	/**
	*
	*/
	public function setClass($class) {
		if (!empty($class)) {
			$this->class = $class;
		}
	}

	/**
	*
	*/
	public function setOsType($osType) {
		if (!empty($osType)) {
			$this->osType = $osType;
		}
	}

	/**
	*
	*/
	public function setOsVersion($osVersion) {
		if (!empty($osVersion)) {
			$this->osVersion = $osVersion;
		}
	}

	/**
	*
	*/
	public function setNode($value = false) {
		if ($node instanceof Idmef_Support_Node) {
			$node = $value;
		} else if (is_array($value)) {
			// Create a new node via an array of parameters
			// to this function
			$node = new Idmef_Support_Node($value['location'], $value['name'], $value['address'], $value['attributes']);
		} else {
			$node = new Idmef_Support_Node;
		}
		$this->node = $node;
		return $node;
	}

	public function setProcess($process) {
		if ($process instanceof Idmef_Support_Process) {
			$this->process = $process;
		} else if (is_array($process)) {
			@$this->process = new Idmef_Support_Process(
				$process['name'],
				$process['pid'],
				$process['path'],
				$process['arg'],
				$process['env'],
				$process['attributes']
			);
		}
	}

	public function setAnalyzer($analyzer) {
		if ($analyzer instanceof Idmef_Core_Analyzer) {
			$this->analyzer = $analyzer;
		} else if (is_array($analyzer)) {
			$this->analyzer = new Idmef_Core_Analyzer($analyzer);
		}
	}

	/**
	*
	*/
	public function toXml() {
		$document = new DOMDocument('1.0', 'UTF-8');
		$analyzer = $document->createElement('Analyzer');

		if (!is_int($this->analyzerId)) {
			$analyzer->setAttribute('analyzerid', $this->analyzerId);
		}

		if (!empty($this->name)) {
			$analyzer->setAttribute('name', $this->name);
		}

		if (!empty($this->manufacturer)) {
			$analyzer->setAttribute('manufacturer', $this->manufacturer);
		}

		if (!empty($this->model)) {
			$analyzer->setAttribute('model', $this->model);
		}

		if (!empty($this->version)) {
			$analyzer->setAttribute('version', $this->version);
		}

		if (!empty($this->class)) {
			$analyzer->setAttribute('class', $this->class);
		}


		if (!empty($this->osType)) {
			$analyzer->setAttribute('ostype', $this->osType);
		}

		if (!empty($this->osVersion)) {
			$analyzer->setAttribute('osversion', $this->osVersion);
		}

		if (!empty($this->node)) {
			$node = new DOMDocument('1.0', 'UTF-8');
			$node->loadXML($this->node->toXml());
			$import = $node->getElementsByTagName('*')->item(0);
			$import = $document->importNode($import, true);
			$analyzer->appendChild($import);
		}

		if (!empty($this->process)) {
			$process = new DOMDocument('1.0', 'UTF-8');
			$process->loadXML($this->process->toXml());
			$import = $process->getElementsByTagName('*')->item(0);
			$import = $document->importNode($import, true);
			$analyzer->appendChild($import);
		}

		if (!empty($this->analyzer)) {
			$subanalyzer = new DOMDocument('1.0', 'UTF-8');
			$subanalyzer->loadXML($this->analyzer->toXml());
			$import = $subanalyzer->getElementsByTagName('*')->item(0);
			$import = $document->importNode($import, true);
			$analyzer->appendChild($import);
		}

		$document->appendChild($analyzer);

		return $document->saveXML();
	}
}

?>
