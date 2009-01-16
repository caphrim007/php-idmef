<?php

/**
* @author Tim Rupp
*/
class Idmef_Support_Address {
	/**
	* The address information. The format of this data is governed
	* by the category attribute.
	*
	* Exactly one value allowed.
	*
	* @var string
	*/
	protected $address;

	/**
	* The network mask for the address, if appropriate.
	*
	* Zero or one value allowed
	*
	* @var string
	*/
	protected $netmask;

	/**
	* A unique identifier for the address; see Section 3.2.9 of
	* RFC 4765
	*
	* This value is optional
	*
	* @var string
	*/
	private $ident;

	/**
	* The type of address represented.  The permitted values for
	* this attribute are shown below.  The default value is "unknown".
	* (See also Section 10 of RFC 4765)
	*
	* This value is optional
	*
	* @var string
	*/
	private $category;

	/**
	* The name of the Virtual LAN to which the address belongs.
	*
	* This value is optional
	*
	* @var string
	*/
	private $vlanName;

	/**
	* The number of the Virtual LAN to which the address belongs.
	*
	* This value is optional
	*
	* @var string
	*/
	private $vlanNum;

	private $unsupportedCategories;

	/**
	*
	*/
	public function __construct($address, $attributes = array()) {
		// Items that can be zero'd
		$this->ident = null;
		$this->category = null;
		$this->vlanName = null;
		$this->vlanNum = null;
		$this->netmask = null;

		/**
		* The following category types are either too old
		* to be relevant, or I just don't care to support
		* them because there isn't enough information about
		* them to include a check for them.
		*
		* Therefore, if you specify them, I just take your
		* word for it.
		*/
		$this->unsupportedCategories = array('atm','lotus-notes','sna','vm');

		// Create defaults for class
		if (isset($attributes['category']) && in_array($attributes['category'], $this->unsupportedCategories)) {
			// Only unsupported categories can be set. Everything
			// else is automagically detected
			$this->setAddress($address, $attributes['category']);
		} else {
			$this->setAddress($address);
		}

		if (isset($attributes['ident'])) {
			$this->setIdent($attributes['ident']);
		}

		if (isset($attributes['vlan-name'])) {
			$this->setVlanName($attributes['vlan-name']);
		}

		if (isset($attributes['vlan-num'])) {
			$this->setVlanNumber($attributes['vlan-num']);
		}
	}

	/**
	*
	*/
	public function setAddress($address, $category = 'unknown') {
		if (is_string($address)) {
			if (MacAddress::isMac($address)) {
				$this->address = $address;
				$this->category = 'mac';
			} else if (Ip::isIPv4Address($address)) {
				$this->address = $address;
				$this->category = 'ipv4-addr';
			} else if (Ip::isIPv4AddressHex($address)) {
				$this->address = $address;
				$this->category = 'ipv4-addr-hex';
			} else if (Ip::isIPv4Net($address)) {
				$this->category = 'ipv4-net';

				// The RFC seems to assume that netmask is
				// a value implied by the category
				$tmp = explode('/', $address);
				$this->address = $tmp[0];
				$this->netmask = Ip::cidr2netmask($tmp[1]);
			} else if (Ip::isIPv4Netmask($address)) {
				$this->category = 'ipv4-net-mask';

				$tmp = explode('/', $address);
				$this->address = $tmp[0];
				$this->netmask = $tmp[1];
			} else if (Ip::isIPv6Address($address)) {
				$this->address = $address;
				$this->category = 'ipv6-addr';
			} else if (Ip::isIPv6AddressHex($address)) {
				$this->address = Ip::stripIPv6Prefix($address);
				$this->category = 'ipv6-addr-hex';
			} else if (Ip::isIPv6Net($address)) {
				$this->category = 'ipv6-net';

				$tmp = explode('/', $address);
				$this->address = $tmp[0];
				$this->netmask = Ip::cidr2netmask($tmp[1]);
			} else if (Ip::isIPv6Netmask($address)) {
				$this->category = 'ipv6-net-mask';

				$tmp = explode('/', $address);
				$this->address = $tmp[0];
				$this->netmask = $tmp[1];
			} else if (Email::isEmailAddress($address)) {
				$this->address = $address;
				$this->category = 'e-mail';
			} else {
				$this->address = $address;
				$this->category = 'unknown';
			}
		} else if (in_array($category, $this->unsupportedCategories)) {
			$this->address = $address;
			$this->category = $category;
		} else {
			$this->address = $address;
			$this->category = 'unknown';
		}
	}

	public function setCategory($category) {
		if (in_array($category, $this->unsupportedCategories)) {
			$this->category = $category;
		}
	}

	/**
	* Idents are much to vague in the RFC to be programatically
	* generated. Please refer to the RFC's section 3.2.9 for
	* instructions on how to create an ident string.
	*/
	public function setIdent($ident = null) {
		if (empty($ident)) {
			throw new Idmef_Support_Exception('Ident should not be empty');
		} else {
			$this->ident = $ident;
		}
	}

	/**
	*
	*/
	public function setVlanName($name) {
		if (empty($name)) {
			throw new Idmef_Support_Exception('VLAN name should not be empty');
		} else {
			$this->vlanName = $name;
		}
	}

	/**
	*
	*/
	public function setVlanNumber($number) {
		if (empty($number)) {
			throw new Idmef_Support_Exception('VLAN number should not be empty');
		} else {
			$this->vlanNum = $number;
		}
	}

	/**
	* ex:
	*	<idmef:Address ident="a1b2c3d4-002" category="ipv4-net-mask">
	*		<idmef:address>192.0.2.50</idmef:address>
	*		<idmef:netmask>255.255.255.255</idmef:netmask>
	*	</idmef:Address>
	*
	* ex:
	*	<idmef:Address category="ipv4-addr-hex">
	*		<idmef:address>0xde796f70</idmef:address>
	*	</idmef:Address>
	*
	* ex:
	*	<idmef:Address ident="a1a2-2" category="ipv4-addr">
	*		<idmef:address>192.0.2.200</idmef:address>
	*	</idmef:Address>
	*/
	public function toXml() {
		$document = new DOMDocument('1.0', 'UTF-8');
		$element = $document->createElement('Address');
		$element->setAttribute('category', $this->category);

		if (!empty($this->ident)) {
			$element->setAttribute('ident', $this->ident);
		}

		if (!empty($this->vlanName)) {
			$element->setAttribute('vlan-name', $this->vlanName);
		}

		if (!empty($this->vlanNum)) {
			$element->setAttribute('vlan-num', $this->vlanNum);
		}

		$address = $document->createElement('address', $this->address);
		$element->appendChild($address);

		if (!empty($this->netmask)) {
			$netmask  = $document->createElement('netmask', $this->netmask);
			$element->appendChild($netmask);
		}

		$document->appendChild($element);

		return $document->saveXML();
	}
}

?>
