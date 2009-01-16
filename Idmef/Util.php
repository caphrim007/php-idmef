<?php

/**
* @author Tim Rupp
*/
class Idmef_Util {
	public static function guessType($value) {
		if (is_int($value)) {
			return 'integer';
		} else if (is_float($value)) {
			return 'real';
		} else if (is_string($value)) {
			return 'string';
		} else if (is_string($value) && (strlen($value) == 1)) {
			return 'character';
		// } else if (is_binary($value)) {
		// 	Not supported until PHP 6
		//	return 'binary';
		// }
		} else if (is_bool($value)) {
			return 'boolean';
		} else {
			return 'string';
		}
	}

	/**
	*
	*/
	public static function validateType($type) {
		$type = strtolower($type);

		$valid = array(
			'boolean', 'byte', 'character', 'date-time', 'integer',
			'ntpstamp', 'portlist','real','string','byte-string','xmltext'
		);

		if (in_array($type, $valid)) {
			return $type;
		} else {
			return false;
		}
	}
}

?>
