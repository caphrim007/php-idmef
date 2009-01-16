<?php

/**
* @author Tim Rupp
*/
class Idmef_Core_Source {
	/**
	* A unique identifier for this source; see Section 3.2.9 of RFC 4765
	*
	* This value is optional
	*/
	private $ident;

	/**
	* An indication of whether the source is, as far as the analyzer can
	* determine, a spoofed address used for hiding the real origin of the
	* attack. The default value is "unknown". (See also Section 10 of RFC
	* 4765.)
	*
	* This value is optional
	*/
	private $spoofed;

	/**
	* May be used by a network-based analyzer with multiple interfaces to
	* indicate which interface this source was seen on.
	*
	* This value is optional
	*/
	private $interface;

	/**
	* Information about the host or device that appears to
	* be causing the events (network address, network name, etc.).
	*
	* Zero or one value allowed
	*
	* @var object Idmef_Support_Node
	*/
	protected $node;

	/**
	* Information about the user that appears to be causing the event(s).
	*
	* Zero or one value allowed
	*
	* @var object Idmef_Support_User
	*/
	protected $user;

	/**
	* Information about the process that appears to be causing the event(s).
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

	public function __construct($attributes = array()) {
		// Attributes that can be zero'd
		$this->ident = false;
		$this->spoofed = false;
		$this->interface = false;

		// Classes that can be zero'd
		$this->node = false;
		$this->user = false;
		$this->process = false;
		$this->service = false;

		if (!empty($attributes['ident'])) {
			$this->setIdent($attributes['ident']);
		}

		if (!empty($attributes['spoofed'])) {
			$this->setSpoofed($attributes['spoofed']);
		}

		if (!empty($attributes['interface'])) {
			$this->setInterface($attributes['interface']);
		}
	}

	public function setIdent($ident = false) {
		if (!empty($ident)) {
			$this->ident = $ident;
		}
	}

	public function setSpoofed($spoofed = false) {
		$validSpoofed = array('yes','no');

		if (in_array($spoofed, $validSpoofed)) {
			$this->spoofed = $spoofed;
		} else {
			$this->spoofed = 'unknown';
		}
	}

	public function setInterface($interface = false) {
		if (!empty($interface)) {
			$this->interface = $interface;
		}
	}

	public function setNode($value) {
		if ($value instanceof Idmef_Support_Node) {
			$node = $value;
		} else if (is_array($value)) {
			$node = new Idmef_Support_Node($value['name'], $value['location'], $value['address'], $value['attributes']);
		} else {
			$node = new Idmef_Support_Node;
		}

		$this->node = $node;
		return $node;
	}

	public function setUser($value) {
		if ($value instanceof Idmef_Support_User) {
			$user = $value;
		} else if (is_array($value)) {
			$user = new Idmef_Support_User($value['userid'], $value['attributes']);
		} else {
			throw new Idmef_Core_Exception('When setting the user, a user ID value must be supplied');
		}

		$this->user = $user;
		return $user;
	}

	public function setProcess($value) {
		if ($value instanceof Idmef_Support_Process) {
			$process = $value;
		} else if (is_array($value)) {
			$process = new Idmef_Support_Process($value['name'],
				$value['pid'],
				$value['path'],
				$value['arg'],
				$value['env'],
				$value['attributes']
			);
		} else {
			throw new Idmef_Core_Exception('When seeting the process, a process name must be supplied');
		}

		$this->process = $process;
		return $process;
	}

	public function setService($value) {
		if ($value instanceof Idmef_Support_Service) {
			$service = $value;
		} else if (is_array($value)) {
			$service = new Idmef_Support_Service($value['name'],
				$value['port'],
				$value['portList'],
				$value['protocol'],
				$value['attributes']
			);
		} else {
			$service = new Idmef_Support_Service;
		}

		$this->service = $service;
		return $service;
	}

	public function toXml() {
		$document = new DOMDocument('1.0', 'UTF-8');
		$source = $document->createElement('Source');

		if (!empty($this->ident)) {
			$source->setAttribute('ident', $this->ident);
		}

		if (!empty($this->spoofed)) {
			$source->setAttribute('spoofed', $this->spoofed);
		}

		if (!empty($this->interface)) {
			$source->setAttribute('interface', $this->interface);
		}

		if (!empty($this->node)) {
			$node = new DOMDocument('1.0', 'UTF-8');
			$node->loadXML($this->node->toXml());
			$import = $node->getElementsByTagName('*')->item(0);
			$import = $document->importNode($import, true);
			$source->appendChild($import);
		}

		if (!empty($this->user)) {
			$user = new DOMDocument('1.0', 'UTF-8');
			$user->loadXML($this->user->toXml());
			$import = $user->getElementsByTagName('*')->item(0);
			$import = $document->importNode($import, true);
			$source->appendChild($import);
		}

		if (!empty($this->process)) {
			$process = new DOMDocument('1.0', 'UTF-8');
			$process->loadXML($this->process->toXml());
			$import = $process->getElementsByTagName('*')->item(0);
			$import = $document->importNode($import, true);
			$source->appendChild($import);
		}

		if (!empty($this->service)) {
			$service = new DOMDocument('1.0', 'UTF-8');
			$service->loadXML($this->service->toXml());
			$import = $service->getElementsByTagName('*')->item(0);
			$import = $document->importNode($import, true);
			$source->appendChild($import);
		}

		$document->appendChild($source);
		return $document->saveXML();
	}
}

?>
