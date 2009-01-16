<?php

/**
* @author Tim Rupp
*/
class Idmef_Support_Service {
	/**
	* A unique identifier for the service; see Section 3.2.9
	* of RFC 4765
	*
	* This value is optional
	*
	* @var string
	*/
	private $ident;

	/**
	* The IP version number.
	*
	* This value is optional
	*
	* @var integer
	*/
	private $ipVersion;

	/**
	* The IANA protocol number.
	*
	* This value is optional
	*
	* @var integer
	*/
	private $ianaProtocolNumber;

	/**
	* The IANA protocol name.
	*
	* This value is optional
	*
	* @var string
	*/
	private $ianaProtocolName;

	/**
	* The name of the service.  Whenever possible, the name from the
	* IANA list of well-known ports SHOULD be used
	*
	* Zero or one value allowed
	*
	* @var string
	*/
	protected $name;

	/**
	* The port number being used.
	*
	* Zero or one value allowed
	*
	* @var integer
	*/
	protected $port;

	/**
	* A list of port numbers being used; see Section 3.2.8 of RFC 4765
	* for formatting rules. If a portlist is given, the iana_protocol_number
	* and iana_protocol_name MUST apply to all the elements of the list.
	*
	* Zero or one value allowed
	*
	* @var string
	*/
	protected $portList;

	/**
	* Additional information about the protocol being used. The intent
	* of the protocol field is to carry additional information related
	* to the protocol being used when the <Service> attributes iana_protocol_number
	* or/and iana_protocol_name are filed.
	*
	* Zero or one value allowed
	*
	* @var string
	*/
	protected $protocol;

	protected $ianaProtocols;

	public function __construct($name = false, $port = false, $portList = false, $protocol = false, $attributes = array()) {
		$this->name = false;
		$this->port = false;
		$this->portList = false;
		$this->protocol = false;
		$this->ianaProtocols = array(
			'0'	=> 'HOPOPT',		// IPv6 Hop-by-Hop Option
			'1'	=> 'ICMP',		// Internet Control Message
			'2'	=> 'IGMP',		// Internet Group Management
			'3'	=> 'GGP',		// Gateway-to-Gateway
			'4'	=> 'IP',		// IP in IP (encapsulation)
			'5'	=> 'ST',		// Stream
			'6'	=> 'TCP',		// Transmission Control
			'7'	=> 'CBT',		// CBT
			'8'	=> 'EGP',		// Exterior Gateway Protocol
			'9'	=> 'IGP',		// any private interior gateway (used by Cisco for their IGRP)  
			'10'	=> 'BBN-RCC-MON',	// BBN RCC Monitoring
			'11'	=> 'NVP-II',		// Network Voice Protocol
			'12'	=> 'PUP',		// PUP
			'13'	=> 'ARGUS',		// ARGUS
			'14'	=> 'EMCON',		// EMCON
			'15'	=> 'XNET',		// Cross Net Debugger
			'16'	=> 'CHAOS',		// Chaos
			'17'	=> 'UDP',		// User Datagram
			'18'	=> 'MUX',		// Multiplexing
			'19'	=> 'DCN-MEAS',		// DCN Measurement Subsystems
			'20'	=> 'HMP',		// Host Monitoring
			'21'	=> 'PRM',		// Packet Radio Measurement
			'22'	=> 'XNS-IDP',		// XEROX NS IDP
			'23'	=> 'TRUNK-1',		// Trunk-1
			'24'	=> 'TRUNK-2',		// Trunk-2
			'25'	=> 'LEAF-1',		// Leaf-1
			'26'	=> 'LEAF-2',		// Leaf-2
			'27'	=> 'RDP',		// Reliable Data Protocol
			'28'	=> 'IRTP',		// Internet Reliable Transaction
			'29'	=> 'ISO-TP4',		// ISO Transport Protocol Class 4
			'30'	=> 'NETBLT',		// Bulk Data Transfer Protocol
			'31'	=> 'MFE-NSP',		// MFE Network Services Protocol
			'32'	=> 'MERIT-INP',		// MERIT Internodal Protocol
			'33'	=> 'DCCP',		// Datagram Congestion Control Protocol
			'34'	=> '3PC',		// Third Party Connect Protocol
			'35'	=> 'IDPR',		// Inter-Domain Policy Routing Protocol
			'36'	=> 'XTP',		// XTP
			'37'	=> 'DDP',		// Datagram Delivery Protocol
			'38'	=> 'IDPR-CMTP',		// IDPR Control Message Transport Proto
			'39'	=> 'TP++',		// TP++ Transport Protocol
			'40'	=> 'IL',		// IL Transport Protocol
			'41'	=> 'IPv6',		// Ipv6
			'42'	=> 'SDRP',		// Source Demand Routing Protocol
			'43'	=> 'IPv6-Route',	// Routing Header for IPv6
			'44'	=> 'IPv6-Frag',		// Fragment Header for IPv6
			'45'	=> 'IDRP',		// Inter-Domain Routing Protocol
			'46'	=> 'RSVP',		// Reservation Protocol
			'47'	=> 'GRE',		// General Routing Encapsulation
			'48'	=> 'DSR',		// Dynamic Source Routing Protocol
			'49'	=> 'BNA',		// BNA
			'50'	=> 'ESP',		// Encap Security Payload
			'51'	=> 'AH',		// Authentication Header
			'52'	=> 'I-NLSP',		// Integrated Net Layer Security TUBA
			'53'	=> 'SWIPE',		// IP with Encryption
			'54'	=> 'NARP',		// NBMA Address Resolution Protocol
			'55'	=> 'MOBILE',		// IP Mobility
			'56'	=> 'TLSP',		// Transport Layer Security Protocol using Kryptonet key management
			'57'	=> 'SKIP',		// SKIP
			'58'	=> 'IPv6-ICMP',		// ICMP for IPv6
			'59'	=> 'IPv6-NoNxt',	// No Next Header for IPv6
			'60'	=> 'IPv6-Opts',		// Destination Options for IPv6
			'61'	=> '',			// any host internal protocol
			'62'	=> 'CFTP',		// CFTP
			'63'	=> '',			// any local network
			'64'	=> 'SAT-EXPAK',		// SATNET and Backroom EXPAK
			'65'	=> 'KRYPTOLAN',		// Kryptolan
			'66'	=> 'RVD',		// MIT Remote Virtual Disk Protocol
			'67'	=> 'IPPC',		// Internet Pluribus Packet Core
			'68'	=> '',			// any distributed file system
			'69'	=> 'SAT-MON',		// SATNET Monitoring
			'70'	=> 'VISA',		// VISA Protocol
			'71'	=> 'IPCV',		// Internet Packet Core Utility
			'72'	=> 'CPNX',		// Computer Protocol Network Executive
			'73'	=> 'CPHB',		// Computer Protocol Heart Beat
			'74'	=> 'WSN',		// Wang Span Network
			'75'	=> 'PVP',		// Packet Video Protocol
			'76'	=> 'BR-SAT-MON',	// Backroom SATNET Monitoring
			'77'	=> 'SUN-ND',		// SUN ND PROTOCOL-Temporary
			'78'	=> 'WB-MON',		// WIDEBAND Monitoring
			'79'	=> 'WB-EXPAK',		// WIDEBAND EXPAK
			'80'	=> 'ISO-IP',		// ISO Internet Protocol
			'81'	=> 'VMTP',		// VMTP
			'82'	=> 'SECURE-VMTP',	// SECURE-VMTP
			'83'	=> 'VINES',		// VINES
			'84'	=> 'TTP',		// TTP
			'85'	=> 'NSFNET-IGP',	// NSFNET-IGP
			'86'	=> 'DGP',		// Dissimilar Gateway Protocol
			'87'	=> 'TCF',		// TCF
			'88'	=> 'EIGRP',		// EIGRP
			'89'	=> 'OSPFIGP',		// OSPFIGP
			'90'	=> 'Sprite-RPC',	// Sprite RPC Protocol
			'91'	=> 'LARP',		// Locus Address Resolution Protocol
			'92'	=> 'MTP',		// Multicast Transport Protocol
			'93'	=> 'AX.25',		// AX.25 Frames
			'94'	=> 'IPIP',		// IP-within-IP Encapsulation Protocol
			'95'	=> 'MICP',		// Mobile Internetworking Control Pro.
			'96'	=> 'SCC-SP',		// Semaphore Communications Sec. Pro.
			'97'	=> 'ETHERIP',		// Ethernet-within-IP Encapsulation
			'98'	=> 'ENCAP',		// Encapsulation Header
			'99'	=> '',			// any private encryption scheme
			'100'	=> 'GMTP',		// GMTP
			'101'	=> 'IFMP',		// Ipsilon Flow Management Protocol
			'102'	=> 'PNNI',		// PNNI over IP
			'103'	=> 'PIM',		// Protocol Independent Multicast
			'104'	=> 'ARIS',		// ARIS
			'105'	=> 'SCPS',		// SCPS
			'106'	=> 'QNX',		// QNX
			'107'	=> 'A/N',		// Active Networks
			'108'	=> 'IPComp',		// IP Payload Compression Protocol
			'109'	=> 'SNP',		// Sitara Networks Protocol
			'110'	=> 'Compaq-Peer',	// Compaq Peer Protocol
			'111'	=> 'IPX-in-IP',		// IPX in IP
			'112'	=> 'VRRP',		// Virtual Router Redundancy Protocol
			'113'	=> 'PGM',		// PGM Reliable Transport Protocol
			'114'	=> '',			// any 0-hop protocol
			'115'	=> 'L2TP',		// Layer Two Tunneling Protocol
			'116'	=> 'DDX',		// D-II Data Exchange (DDX)
			'117'	=> 'IATP',		// Interactive Agent Transfer Protocol
			'118'	=> 'STP',		// Schedule Transfer Protocol
			'119'	=> 'SRP',		// SpectraLink Radio Protocol
			'120'	=> 'UTI',		// UTI
			'121'	=> 'SMP',		// Simple Message Protocol
			'122'	=> 'SM',		// SM
			'123'	=> 'PTP',		// Performance Transparency Protocol
			'124'	=> 'ISIS over IPv4',
			'125'	=> 'FIRE',
			'126'	=> 'CRTP',		// Combat Radio Transport Protocol
			'127'	=> 'CRUDP',		// Combat Radio User Datagram
			'128'	=> 'SSCOPMCE',
			'129'	=> 'IPLT',
			'130'	=> 'SPS',		// Secure Packet Shield
			'131'	=> 'PIPE',		// Private IP Encapsulation within IP
			'132'	=> 'SCTP',		// Stream Control Transmission Protocol
			'133'	=> 'FC',		// Fibre Channel
			'134'	=> 'RSVP-E2E-IGNORE',
			'135'	=> 'Mobility Header',
			'136'	=> 'UDPLite',
			'137'	=> 'MPLS-in-IP',
			'138'	=> 'manet',		// MANET Protocols
			'139'	=> 'HIP',		// Host Identity Protocol
//140-252                   Unassigned                               [IANA]
			'253'	=> '',			// Use for experimentation and testing
			'254'	=> '',			// Use for experimentation and testing
			'255'	=> '', 			// Reserved
		);

		$this->setName($name);

		if ((!empty($name) || is_numeric($port)) && empty($portList)) {
			$this->setPort($port, $name);
		} else if (!empty($portList) && (empty($name) && empty($port))) {
			$this->setPortList($portList);
		}

		if (!empty($protocol)) {
			$this->setProtocol($protocol);
			$this->setIanaProtocol($protocol);
		}

		if (!empty($attributes['ip_version'])) {
			$this->setIpVersion($attributes['ip_version']);
		}
	}

	public function setIanaProtocol($protocol) {
		if (is_numeric($protocol)) {
			if (isset($this->ianaProtocols[$protocol])) {
				$this->ianaProtocolNumber = $protocol;
				$this->ianaProtocolName = $this->ianaProtocols[$protocol];
			}
		} else {
			$result = array_search($protocol, $this->ianaProtocols);
			if ($result !== false) {
				$this->ianaProtocolNumber = $result;
				$this->ianaProtocolName = $protocol;
			}
		}
	}

	public function setIdent($ident) {
		if (!empty($ident)) {
			$this->ident = $ident;
		}
	}

	public function setName($name) {
		if (!empty($name)) {
			$this->name = $name;
		}
	}

	public function setIpVersion($version) {
		if ($version == '4' || $version == '6') {
			$this->ipVersion = $version;
		}
	}

	public function setPort($port = false, $name = false) {
		if ($port === false && $name === false) {
			throw new Idmef_Support_Exception('Both port and name cannot be empty');
		} else {
			$this->port = $port;
			$this->name = $name;
			$this->portList = false;
		}
	}

	public function setPortList($portList) {
		$this->name = false;
		$this->port = false;

		if (is_array($portList)) {
			$this->portList = implode(',', $$portList);
		} else {
			$this->portList = $portList;
		}
	}

	public function setProtocol($protocol) {
		if (!empty($protocol)) {
			$this->protocol = $protocol;
		}
	}

	public function toXml() {
		$document = new DOMDocument('1.0', 'UTF-8');
		$service = $document->createElement('Service');

		if (!empty($this->ident)) {
			$service->setAttribute('ident', $this->ident);
		}

		if (!empty($this->ipVersion)) {
			$service->setAttribute('ip_version', $this->ipVersion);
		}

		if (!empty($this->ianaProtocolNumber)) {
			$service->setAttribute('iana_protocol_number', $this->ianaProtocolNumber);
		}

		if (!empty($this->ianaProtocolName)) {
			$service->setAttribute('iana_protocol_name', $this->ianaProtocolName);
		}

		if (!empty($this->portList)) {
			$portlist = $document->createElement('portlist', $this->portList);
			$service->appendChild($portlist);
		} else if (!empty($this->name) || is_numeric($this->port)) {
			$name = $document->createElement('name', $this->name);
			$port = $document->createElement('port', $this->port);
			$service->appendChild($name);
			$service->appendChild($port);
		}

		if (!empty($this->protocol)) {
			$protocol = $document->createElement('protocol', $this->protocol);
			$service->appendChild($protocol);
		}

		$document->appendChild($service);
		return $document->saveXML();
	}
}

?>
