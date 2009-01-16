<?php

/**
* @author Tim Rupp
*/
abstract class Idmef_Time_Abstract {
	protected $dateTime;
	protected $normalOffset = 2208988800;
	protected $fractionOffset = 4294967296;

	public function setTime($value) {
		if (empty($value)) {
			$this->dateTime = new Zend_Date;
		} else {
			$this->dateTime = new Zend_Date($value);
		}
	}

	/**
	* This code was incredibly difficult to figure out due to the
	* lack of resources available on the internet that describe
	* the correct way to calculate the 64bit NTP timestamp value
	*/
	public function unix2Ntp($value) {
		if ($value instanceof Zend_Date) {
			$datetime = $value;
		} else {
			$datetime = new Zend_Date($value);
		}

		$timestamp = $datetime->get(Zend_Date::TIMESTAMP);

		// Calculate the hex portion of the first 32 bit block
		$ntpTimestamp = $timestamp + $this->normalOffset;
		$ntpHex = dechex($ntpTimestamp);

		/**
		* Calculate hex portion of second 32 bit block
		*
		* Note that Zend_Date doesnt assign milliseconds out of the
		* box so you either need to assign them yourself or deal
		* without them.
		*/
		$millisecond = $datetime->get(Zend_Date::MILLISECOND);

		if ($millisecond == 0) {
			// If zero, hex will also be zero, but I need 8 places
			// here to be accurate I think ????
			$millisecondHex = '00000000';
		} else {
			// This turns the milliseconds value into a decimal
			// value that will be used when creating the ntpstamp
			// fractional seconds that are stored in hex
			$millisecond = round(sprintf('.%s', $millisecond) * $this->fractionOffset);
			$millisecondHex = dechex($millisecond);
		}

		$ntpstamp = sprintf('0x%s.0x%s', $ntpHex, $millisecondHex);
		return $ntpstamp;
	}

	public function ntp2Unix($value) {
		$tmp = explode('.', $value);
		$datetime = $tmp[0];
		$fraction = $tmp[1];

		$seconds = (hexdec($datetime) - $this->normalOffset);
		$milliseconds = (hexdec($fraction) / $this->fractionOffset);

		$datetime = new Zend_Date($seconds);
		$datetime->setMillisecond($milliseconds);

		return $datetime->get(Zend_Date::TIMESTAMP);
	}
}

?>
