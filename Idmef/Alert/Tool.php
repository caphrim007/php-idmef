<?php

/**
* @author Tim Rupp
*/
class Idmef_Alert_Tool {
	protected $name;
	protected $command;
	protected $alertIdent;

	public function __construct($name, $command = false);

	}

	public function addAlertIdent($value) {
		if ($value instanceof Idmef_Alert_Ident) {
			$this->alertIdent[] = $value;
		} else if (is_array($value)) {
			$this->alertIdent[] = new Idmef_Alert_Ident($value['value'], $value['analyzerid']);
		}
	}
}

?>
