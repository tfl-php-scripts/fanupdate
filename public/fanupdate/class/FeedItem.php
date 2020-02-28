<?php
/**
* Universal Feed Writer
*
* FeedItem class - Used as feed element in FeedWriter class
*
* @package		   UniversalFeedWriter
* @author		   Anis uddin Ahmad <anisniit@gmail.com>
* @link			   http://www.ajaxray.com/projects/rss
* Updated substantially 2008-03-13 by Jenny Ferenc
*/
class FeedItem
{
	var $elements = array();	//Collection of feed elements
	var $version;

	/**
	* Constructor
	*
	* @param	string		(RSS2/ATOM) RSS2 is default.
	*/
	function FeedItem($version = RSS2)
	{
		$this->version = $version;
	}

	/**
	* Add an element to elements array
	*
	* @access	public
	* @param	string	The tag name of an element
	* @param	string	The content of tag
	* @param	array	Attributes(if any) in 'attrName' => 'attrValue' format
	* @return	void
	*/
	function addElement($elementName, $content, $attributes = null)
	{
		$this->elements[$elementName]['name']		= $elementName;
		$this->elements[$elementName]['content']	= $content;
		$this->elements[$elementName]['attributes'] = $attributes;
	}

	/**
	* Set multiple feed elements from an array.
	* Elements which have attributes cannot be added by this method
	*
	* @access	public
	* @param	array	array of elements in 'tagName' => 'tagContent' format.
	* @return	void
	*/
	function addElementArray($elementArray)
	{
		if (! is_array($elementArray)) return;
		foreach ($elementArray as $elementName => $content)
		{
			$this->addElement($elementName, $content);
		}
	}

	/**
	* Return the collection of elements in this feed item
	*
	* @access	public
	* @return	array
	*/
	function getElements()
	{
		return $this->elements;
	}

	// Wrapper functions ------------------------------------------------------

	/**
	* Set the 'description' element of feed item
	*
	* @access	public
	* @param	string	The content of 'description' element
	* @return	void
	*/
	function setDescription($description)
	{
		$tag = ($this->version == ATOM)? 'summary' : 'description';
		$this->addElement($tag, $description);
	}

	/**
	* Set the 'title' element of feed item
	*
	* @access	public
	* @param	string	The content of 'title' element
	* @return	void
	*/
	function setTitle($title)
	{
		$this->addElement('title', $title);
	}

	/**
	* Set the 'date' element of feed item
	*
	* @access	public
	* @param	string	The content of 'date' element
	* @return	void
	*/
	function setDate($date)
	{
		if (! is_numeric($date))
		{
			$date = strtotime($date);
		}

		if ($this->version == ATOM)
		{
			$tag	= 'updated';
			$value	= date(DATE_ATOM, $date);
		}
		elseif ($this->version == RSS2)
		{
			$tag	= 'pubDate';
			$value	= date(DATE_RSS, $date);
		}
		else
		{
			$tag	= 'dc:date';
			$value	= date("Y-m-d", $date);
		}

		$this->addElement($tag, $value);
	}

	/**
	* Set the 'link' element of feed item
	*
	* @access	public
	* @param	string	The content of 'link' element
	* @return	void
	*/
	function setLink($link, $guid = false)
	{
		if ($this->version == RSS2)
		{
			$this->addElement('link', $link);
			if ($guid) {
				$this->addElement('guid', $link, array('isPermaLink'=>'true'));
			}
		}
		elseif ($this->version == ATOM)
		{
			$this->addElement('link','',array('href'=>$link));
			$this->addElement('id', FeedWriter::uuid($link,'urn:uuid:'));
		}
	}

	function setAuthor($email, $name = '')
	{
		if ($this->version == RSS2)
		{
			if (empty($email)) {
				$email = 'nobody@example.com';
			}
			$author = $email;
			if (!empty($name)) {
				$author .= ' ('.$name.')';
			}
			$this->addElement('author', $author);
		}
		else
		{
			$this->addElement('author', array('name'=>$name, 'email'=>$email));
		}
	}

	/**
	* Set the 'enclosure' element of feed item
	* For RSS 2.0 only
	*
	* @access	public
	* @param	string	The url attribute of Enclosure tag
	* @param	string	The length attribute of Enclosure tag
	* @param	string	The type attribute of Enclosure tag
	* @return	void
	*/
	function setEnclosure($url, $length, $type)
	{
		$attributes = array('url'=>$url, 'length'=>$length, 'type'=>$type);
		$this->addElement('enclosure', '', $attributes);
	}

} // end of class FeedItem

?>