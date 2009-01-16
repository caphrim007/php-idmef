<?php

/**
* @author Tim Rupp
*/
class Idmef_Support_File {
	/**
	* A unique identifier for this file; see Section 3.2.9 of RFC 4765
	*
	* This value is optional
	*/
	private $ident;

	/**
	* The context for the information being provided. The permitted
	* values are shown below.  There is no default value. (See also 
	* Section 10 of RFC 4765.)
	*
	* This value is required
	*/
	private $category;

	/**
	* The type of file system the file resides on. This attribute
	* governs how path names and other attributes are interpreted.
	*
	* This value is optional
	*/
	private $fstype;

	/**
	* The type of file, as a mime-type.
	*
	* This value is optional
	*/
	private $fileType;

	/**
	* The name of the file to which the alert applies, not including
	* the path to the file.
	*
	* Exactly one value allowed
	*
	* @var string
	*/
	protected $name;

	/**
	* The full path to the file, including the name. The path name
	* should be represented in as "universal" a manner as possible,
	* to facilitate processing of the alert.
	*
	* For Windows systems, the path should be specified using the
	* Universal Naming Convention (UNC) for remote files, and using a
	* drive letter for local files (e.g., "C:\boot.ini").  For Unix
	* systems, paths on network file systems should use the name of the
	* mounted resource instead of the local mount point (e.g.,
	* "fileserver:/usr/local/bin/foo").  The mount point can be provided
	* using the <Linkage> element.
	*
	* Exactly one value allowed
	*
	* @var string
	*/
	protected $path;

	/**
	* Time the file was created. Note that this is *not* the Unix
	* "st_ctime" file attribute (which is not file creation time). The
	* Unix "st_ctime" attribute is contained in the "Inode" class.
	*
	* Zero or one value allowed
	*
	* @var datetime
	*/
	protected $createTime;

	/**
	* Time the file was last modified.
	*
	* Zero or one value allowed
	*
	* @var datetime
	*/
	protected $modifyTime;

	/**
	* Time the file was last accessed.
	*
	* Zero or one value allowed
	*
	* @var datetime
	*/
	protected $accessTime;

	/**
	* The size of the data, in bytes.  Typically what is meant when
	* referring to file size.  On Unix UFS file systems, this value
	* corresponds to stat.st_size.  On Windows NTFS, this value
	* corresponds to Valid Data Length (VDL).
	*
	* Zero or one value allowed
	*
	* @var integer
	*/
	protected $dataSize;

	/**
	* The physical space on disk consumed by the file, in bytes. On
	* Unix UFS file systems, this value corresponds to 512 * stat.st_blocks.
	* On Windows NTFS, this value corresponds to End of File (EOF).
	*
	* Zero or one value allowed
	*
	* @var integer
	*/
	protected $diskSize;

	/**
	* Access permissions on the file.
	*
	* Zero or more values allowed
	*
	* @var array
	*/
	protected $fileAccess;

	/**
	* File system objects to which this file is linked (other references
	* for the file).
	*
	* Zero or more values allowed
	*
	* @var array
	*/
	protected $linkage;

	/**
	* Inode information for this file (relevant to Unix).
	*
	* Zero or one value allowed
	*
	* @var object Idmef_Support_File_Inode
	*/
	protected $inode;

	/**
	* Checksum information for this file.
	*
	* Zero or more values allowed
	*
	* @var array
	*/
	protected $checksum;

	public function __construct($name, $path, $attributes = array()) {
		$this->name = false;
		$this->path = false;

		if (empty($name)) {
			throw new Idmef_Support_File_Exception('File name must be supplied');
		} else {
			$this->setName($name);
		}

		if (empty($path)) {
			throw new Idmef_Support_File_Exception('File path must be supplied');
		} else {
			$this->setPath($path);
		}

		$this->setCategory($attributes['category']);
	}

	public function setIdent($ident) {
		if (!empty($ident)) {
			$this->ident = $ident;
		}
	}

	public function setCategory($category) {
		$validCategories = array('current', 'original');

		if (in_array($category, $validCategories)) {
			$this->category = $category;
		}
	}

	public function setFsType($type) {
		$type = strtolower($type);
		$validTypes = array(
			'ufs', 'efs', 'nfs', 'afs',
			'ntfs', 'fat16', 'fat32', 'pcfs',
			'joliet', 'iso9660'
		);

		if (in_array($type, $validTypes)) {
			$this->fstype = $type;
		}
	}

	public function setFileType($type) {
		if (!empty($type)) {
			$this->fileType = $type;
		}
	}

	public function setName($name) {
		if (!empty($name)) {
			$this->name = $name;
		}
	}

	public function setPath($path) {
		if (!empty($path)) {
			$this->path = $path;
		}
	}

	public function toXml() {
		$document = new DOMDocument('1.0', 'UTF-8');
		$file = $document->createElement('File');

		if (!empty($this->ident)) {
			$file->setAttribute('ident', $this->ident);
		}

		if (!empty($this->category)) {
			$file->setAttribute('category', $this->category);
		} else {
			throw new Idmef_Support_File_Exception('You must specify a category for the file class');
		}

		if (!empty($this->fstype)) {
			$file->setAttribute('fstype', $this->fstype);
		}

		if (!empty($this->fileType)) {
			$file->setAttribute('file-type', $this->fileType);
		}
	}
}

?>
