<?php

/**
* @author Tim Rupp
*/
class Idmef_Support_Process {
	/**
	* A unique identifier for the process; see Section 3.2.9. of RFC 4765
	*
	* This value is optional
	*
	* @var string
	*/
	private $ident;

	/**
	* The name of the program being executed. This is a short name; path
	* and argument information are provided elsewhere.
	*
	* Exactly one value allowed
	*
	* @var string
	*/
	protected $name;

	/**
	* The process identifier of the process.
	*
	* Zero or one value allowed
	*
	* @var integer
	*/
	protected $pid;

	/**
	* The full path of the program being executed.
	*
	* Zero or one value allowed
	*
	* @var string
	*/
	protected $path;

	/**
	* A command-line argument to the program. Multiple arguments may be
	* specified (they are assumed to have occurred in the same order they
	* are provided) with multiple uses of arg.
	*
	* Zero or more values allowed
	*
	* @var array
	*/
	protected $arg;

	/**
	* An environment string associated with the process; generally of the
	* format "VARIABLE=value". Multiple environment strings may be specified
	* with multiple uses of env.
	*
	* Zero or more values allowed
	*
	* @var array
	*/
	protected $env;

	/**
	*
	*/
	public function __construct($name, $pid = false, $path = false, $arg = array(), $env = array(), $attributes = array()) {
		// Set attributes that can be zero'd
		$this->ident = false;

		// Set default values
		$this->name = false;
		$this->pid = false;
		$this->path = false;
		$this->arg = false;
		$this->env = false;

		$this->setName($name);
		$this->setPid($pid);
		$this->setPath($path);

		$this->addArg($arg);
		$this->addEnv($env);

		// Set available attributes
		if (isset($attributes['ident'])) {
			$this->setIdent($attributes['ident']);
		}
	}

	/**
	*
	*/
	public function setIdent($ident) {
		if (!empty($ident)) {
			$this->ident = $ident;
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
	public function setPid($pid) {
		if (is_numeric($pid)) {
			$this->pid = $pid;
		}
	}

	/**
	*
	*/
	public function setPath($path) {
		if(!empty($path)) {
			$this->path = $path;
		}
	}

	/**
	*
	*/
	public function addArg($arg) {
		if (!is_array($arg)) {
			$arg = array($arg);
		}

		foreach($arg as $key => $val) {
			if (!empty($val)) {
				$this->arg[] = $val;
			}
		}
	}

	/**
	*
	*/
	public function addEnv($env) {
		if (!is_array($env)) {
			$env = array($env);
		}

		foreach($env as $key => $val) {
			if (!empty($val)) {
				$this->env[] = $val;
			}
		}
	}

	/**
	*
	*/
	public function toXml() {
		$document = DOMDocument('1.0', 'UTF-8');
		$process = $document->createElement('Process');

		if (!empty($this->ident)) {
			$process->setAttribute('ident', $this->ident);
		}

		if (!empty($this->name)) {
			$name = $document->createElement('name', $this->name);
			$process->appendChild($name);
		}

		if (is_numeric($this->pid)) {
			$pid = $document->createElement('pid', $this->pid);
			$process->appendChild($pid);
		}

		if (!empty($this->path)) {
			$path = $document->createElement('path', $this->path);
			$process->appendChild($path);
		}

		foreach($this->arg as $key => $val) {
			$arg = $document->createElement('arg', $val);
			$process->appendChild($arg);
		}

		foreach($this->env as $key => $val) {
			$env = $document->createElement('env', $val);
			$process->appendChild($env);
		}

		$document->appendChild($process);

		return $document->saveXML();
	}
}

?>
