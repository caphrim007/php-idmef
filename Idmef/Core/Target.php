<?php

/**
* @author Tim Rupp
*/
class Idmef_Core_Target {
	/**
	* A unique identifier for this target, see Section 3.2.9 of RFC 4765
	*
	* This value is optional
	*
	* @var string
	*/
	private $ident;

	/**
	* An indication of whether the target is, as far as the
	* analyzer can determine, a decoy. The permitted values for this
	* attribute are shown below. The default value is "unknown". (See
	* also Section 10 of RFC 4765)
	*
	* This value is optional
	*
	* @var string
	*/
	private $decoy;

	/**
	* May be used by a network-based analyzer with multiple
	* interfaces to indicate which interface this target was seen on.
	*
	* This value is optional
	*
	* @var string
	*/
	private $interface;

	/**
	* Information about the host or device at which the event(s)
	* (network address, network name, etc.) is being directed.
	*
	* Zero or one value allowed
	*
	* @var object Idmef_Support_Node
	*/
	protected $node;

	/**
	* Information about the user at which the event(s) is being directed.
	*
	* Zero or one value allowed
	*
	* @var object Idmef_Support_User
	*/
	protected $user;

	/**
	* Information about the process at which the event(s) is being directed.
	*
	* Zero or one value allowed
	*
	* @var object Idmef_Support_Process
	*/
	protected $process;

	/**
	* Information about the network service involved in the event(s).
	*
	* Zero or one value allowed
	*
	* @var object Idmef_Support_Service
	*/
	protected $service;

	/**
	* Information about file(s) involved in the event(s).
	*
	* This value is optional. Many values allowed
	*
	* @var array Idmef_Support_File
	*/
	protected $file;

	public function __construct($attributes = array()) {
		// Attributes that can be zero'd
		$this->ident = false;
		$this->decoy = false;
		$this->interface = false;

		// Values that can be zero'd
		$this->node = false;
		$this->user = false;
		$this->process = false;
		$this->service = false;
		$this->file = false;
	}

	public function setNode($node) {
		if ($node instanceof Idmef_Support_Node) {
			$this->node = $node;
		} else if (is_array($node)) {
			$this->node = new Idmef_Support_Node($node['name'], $node['location'], $node['address'], $node['attributes']);
		}
	}

	public function setUser($user) {
		if ($user instanceof Idmef_Support_User) {
			$this->user = $user;
		} else if(is_array($user)) {
			// TODO: Add user class assignment by array her
			$this->user = new Idmef_Support_User;
		}
	}

	public function setProcess($process) {
		if ($process instanceof Idmef_Support_Process) {
			$this->process = $process;
		} else if (is_array($process)) {
			$this->process = new Idmef_Support_Process($process['name'],
				$process['pid'],
				$process['path'],
				$process['arg'],
				$process['env'],
				$process['attributes']
			);
		}
	}

	public function setService($service) {
		if ($service instanceof Idmef_Support_Service) {
			$this->service = $service;
		} else if (is_array($service)) {
			$this->service = new Idmef_Support_Service($service['name'],
				$service['port'],
				$service['portlist'],
				$service['protocol']
			);
		}
	}

	public function addFile($file) {
		if ($file instanceof Idmef_Support_File) {
			$this->file[] = $file;
		} else if (is_array($file)) {
			// TODO: Add file addition via array
			$this->file[] = new Idmef_Support_File;
		}
	}

	public function setDecoy($decoy) {
		$validDecoys = array('yes','no');

		if (in_array($decoy, $validDecoys)) {
			$this->decoy = $decoy;
		} else {
			$this->decoy = 'unknown';
		}
	}

	public function toXml() {
		$document = new DOMDocument('1.0', 'UTF-8');
		$target = $document->createElement('Target');

		if (!empty($this->ident)) {
			$target->setAttribute('ident', $this->ident);
		}

		if (!empty($this->decoy)) {
			$target->setAttribute('decoy', $this->decoy);
		}

		if (!empty($this->interface)) {
			$target->setAttributes('interface', $this->interface);
		}

		if (!empty($this->node)) {
			$node = new DOMDocument('1.0', 'UTF-8');
			$node->loadXML($this->node->toXml());
			$import = $node->getElementsByTagName('*')->item(0);
			$import = $document->importNode($import, true);
			$target->appendChild($import);
		}

		if (!empty($this->user)) {
			$user = new DOMDocument('1.0', 'UTF-8');
			$user->loadXML($this->user->toXml());
			$import = $user->getElementsByTagName('*')->item(0);
			$import = $document->importNode($import, true);
			$target->appendChild($import);
		}

		if (!empty($this->process)) {
			$process = new DOMDocument('1.0', 'UTF-8');
			$process->loadXML($this->process->toXml());
			$import = $process->getElementsByTagName('*')->item(0);
			$import = $document->importNode($import, true);
			$target->appendChild($import);
		}

		if (!empty($this->service)) {
			$service = new DOMDocument('1.0', 'UTF-8');
			$service->loadXML($this->service->toXml());
			$import = $service->getElementsByTagName('*')->item(0);
			$import = $document->importNode($import, true);
			$target->appendChild($import);
		}

		if (is_array($this->file)) {
			foreach($this->file as $key => $val) {
				$file = new DOMDocument('1.0', 'UTF-8');
				$file->loadXML($val->toXml());
				$import = $file->getElementsByTagName('*')->item(0);
				$import = $document->importNode($import, true);
				$target->appendChild($import);
			}
		}

		$document->appendChild($target);
		return $document->saveXML();
	}
}

?>
