<?php

class FileHandler {

	var $_allowed_ext = array();
	
	function FileHandler($allowed_ext = array()) {
		if (!empty($allowed_ext)) {
			$this->_allowed_ext = $allowed_ext;
		}
	}
	
	function getExtension($filename) {
		return (strpos($filename, '.') === false) ? '' : strtolower(substr(strrchr($filename, '.'), 1));
	}
	
	function getMimeType($filename) {
		$file_extension = $this->getExtension($filename);
		switch ($file_extension) {
			case "mp3": $ctype="audio/mpeg"; break;
			case "m4a": $ctype="audio/x-m4a"; break;
			case "mp4": $ctype="video/mp4"; break;
			case "m4v": $ctype="video/x-m4v"; break;
			case "mov": $ctype="video/quicktime"; break;
			case "avi": $ctype="video/x-msvideo"; break;
			case "rtf": $ctype="application/rtf"; break;
			case "pdf": $ctype="application/pdf"; break;
			case "exe": $ctype="application/octet-stream"; break;
			case "zip": $ctype="application/x-zip-compressed"; break;
			case "doc": $ctype="application/msword"; break;
			case "xls": $ctype="application/vnd.ms-excel"; break;
			case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
			case "gif": $ctype="image/gif"; break;
			case "png": $ctype="image/png"; break;
			case "jpe": case "jpeg":
			case "jpg": $ctype="image/jpg"; break;
			default: $ctype="application/force-download";
		}
		return $ctype;
	}

	// get rid of bad characters
	function fixFilename($orig_name) {
		$orig_name = str_replace(' ', '_', $orig_name);
		return preg_replace('/[^-_\.0-9a-zA-Z]/', '', $orig_name);
	}

	function getDimensions($path) {
		list($width, $height) = getimagesize($path);
		return $width.'x'.$height;
	}
	
	function getSize($path) {
		return filesize($path);
	}

	function getHumanSize($path) {
		if (file_exists($path)) {
			$filesizename = array(' Bytes', ' KB', ' MB', ' GB', ' TB', ' PB', ' EB', ' ZB', ' YB');
			$size = $this->getSize($path);
			return round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i];
		} else {
			return 'File Not Found';
		}
	}
	
	function checkExtension($filename) {
		$filenameext = $this->getExtension($filename);

		$count = count($this->_allowed_ext);
		for ($x=0; $x<$count; $x++) {
			if ($filenameext == $this->_allowed_ext[$x]) {
				return true;
			}
		}
			
		return false;
	}

} // end class FileHandler

?>