<?php

// RSS 0.91, 0.92, 0.93 and 0.94  Officially obsoleted by 2.0
// So, define constants for RSS 2.0 and ATOM

define('RSS2', 'RSS 2.0', true);
define('ATOM', 'ATOM', true);

if (!defined(DATE_RSS)) {
	define('DATE_RSS', 'r', true);
}
if (!defined(DATE_ATOM)) {
	define('DATE_ATOM', 'Y-m-d\TH:i:sP', true);
}

// Atom: http://www.w3.org/2005/Atom
// RSS 2: http://www.rssboard.org/rss-specification

require_once('FeedItem.php');

/**
* Universal Feed Writer class
*
* Generate RSS2.0 and ATOM Feed
*
* @package		UniversalFeedWriter
* @author		Anis uddin Ahmad <anisniit@gmail.com>
* @link		http://www.ajaxray.com/projects/rss
* Updated substantially 2008-03-13 by Jenny Ferenc
*/
class FeedWriter
{
	var $channels		= array();	// Collection of channel elements
	var $items			= array();	// Collection of items as object of FeedItem class.
	var $namespaces		= array();	// used namespaces
	var $names			= array();	// know namespaces
	var $CDATAEncoding	= array();	// The tag names which have to encoded as CDATA
	var $version		= null;

	/**
	* Constructor
	*
	* @param	string	the version constant (RSS2/ATOM).
	*/
	function FeedWriter($version = RSS2)
	{
		$this->version = $version;

		// Setting default value for essential channel elements
		$this->channels['title']		= $version . ' Feed';
		$this->channels['link']			= 'http://'.$_SERVER['SERVER_NAME'];

		$this->names['atom']	= 'http://www.w3.org/2005/Atom';
		$this->names['content']	= 'http://purl.org/rss/1.0/modules/content/';
		$this->names['itunes']	= 'http://www.itunes.com/dtds/podcast-1.0.dtd';
		$this->names['slash']	= 'http://purl.org/rss/1.0/modules/slash/';
		$this->names['wfw']		= 'http://wellformedweb.org/CommentAPI/';

		//Tag names to encode in CDATA
		$this->CDATAEncoding = array('description', 'content:encoded', 'summary');

		if ($this->version == RSS2) {
			$this->setChannelElement('docs', 'http://www.rssboard.org/rss-specification');
		} elseif ($this->version == ATOM) {
			$this->addNamespace('', $this->names['atom']);
		}
	}

	// Start # functions ---------------------------------------------

	/**
	* Set a channel element
	* @access	public
	* @param	string	name of the channel tag
	* @param	string	content of the channel tag
	* @return	void
	*/
	function setChannelElement($elementName, $content)
	{
		$this->channels[$elementName] = $content;
	}

	/**
	* Set multiple channel elements from an array. Array elements
	* should be 'channelName' => 'channelContent' format.
	*
	* @access	public
	* @param	array	array of channels
	* @return	void
	*/
	function setChannelElementsFromArray($elementArray)
	{
		if (! is_array($elementArray)) return;
		foreach ($elementArray as $elementName => $content)
		{
			$this->setChannelElement($elementName, $content);
		}
	}

	/**
	* Generate the actual RSS/ATOM file
	*
	* @access	public
	* @return	void
	*/
	function generateFeed()
	{
		header("Content-type: text/xml");

		$this->printHead();
		$this->printChannels();
		$this->printItems();
		$this->printTail();
	}

	/**
	* Create a new FeedItem.
	*
	* @access	public
	* @return	object	instance of FeedItem class
	*/
	function createNewItem()
	{
		$Item = new FeedItem($this->version);
		return $Item;
	}

	/**
	* Add a FeedItem to the main class
	*
	* @access	public
	* @param	object	instance of FeedItem class
	* @return	void
	*/
	function addItem($feedItem)
	{
		$this->items[] = $feedItem;
	}


	// Wrapper functions -------------------------------------------------------------------

	/**
	* Set the 'title' channel element
	*
	* @access	public
	* @param	string	value of 'title' channel tag
	* @return	void
	*/
	function setTitle($title)
	{
		$this->setChannelElement('title', $title);
	}

	/**
	* Set the 'description' channel element
	*
	* @access	public
	* @param	string	value of 'description' channel tag
	* @return	void
	*/
	function setDescription($desciption)
	{
		$this->setChannelElement('description', $desciption);
	}

	/**
	* Set the 'link' channel element
	*
	* @access	public
	* @param	string	value of 'link' channel tag
	* @return	void
	*/
	function setLink($link)
	{
		$this->setChannelElement('link', $link);
	}

	function setLinkSelf($link)
	{
		if ($this->version == RSS2) {
			$this->setChannelElement('atom:link', $link);
		} elseif ($this->version == ATOM) {
			$this->setChannelElement('link', $link);
		}
	}

	function setAuthor($email, $name = '')
	{
		if ($this->version == RSS2)
		{
			$author = $email;
			if (!empty($name)) {
				$author .= ' ('.$name.')';
			}
			$this->setChannelElement('managingEditor', $author);
		}
		elseif ($this->version == ATOM)
		{
			$this->setChannelElement('author', array('name'=>$name, 'email'=>$email));
		}
	}

	function setPubDate($date)
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

		$this->setChannelElement($tag, $value);
	}

	function setBuildDate($date)
	{
		if (! is_numeric($date))
		{
			$date = strtotime($date);
		}

		if ($this->version == RSS2)
		{
			$tag	= 'lastBuildDate';
			$value	= date(DATE_RSS, $date);
			$this->setChannelElement($tag, $value);
		}
	}

	function addNamespace($name, $url = null)
	{
		if (empty($url) && isset($this->names[$name])) {
			$this->namespaces[$name] = $this->names[$name];
		} else {
			$this->namespaces[$name] = $url;
		}
	}

	/**
	* Set the 'image' channel element
	*
	* @access	public
	* @param	string	title of image
	* @param	string	link url of the imahe
	* @param	string	path url of the image
	* @return	void
	*/
	function setImage($title, $link, $url)
	{
		$this->setChannelElement('image', array('title'=>$title, 'link'=>$link, 'url'=>$url));
	}

	/**
	* Genarates an UUID
	* @author		Anis uddin Ahmad <admin@ajaxray.com>
	* @param		string	an optional prefix
	* @return		string	the formated uuid
	*/
	function uuid($key = null, $prefix = '')
	{
		$key = ($key == null)? uniqid(rand()) : $key;
		$chars = md5($key);
		$uuid  = substr($chars,0,8) . '-';
		$uuid .= substr($chars,8,4) . '-';
		$uuid .= substr($chars,12,4) . '-';
		$uuid .= substr($chars,16,4) . '-';
		$uuid .= substr($chars,20,12);

		return $prefix . $uuid;
	}
	// End # functions ----------------------------------------------

	// Start # functions ----------------------------------------------

	/**
	* Prints the xml and rss namespace
	*
	* @access	private
	* @return	void
	*/
	function printHead()
	{
		header('Content-Type: text/xml; charset=UTF-8');
		$out  = '<?xml version="1.0" encoding="utf-8"?>' . PHP_EOL;

		if ($this->version == RSS2) {
			$out .= '<rss version="2.0"';
		} elseif ($this->version == ATOM) {
			$out .= '<feed';
		}

		foreach ($this->namespaces as $key => $val) {
			$out .= ' xmlns';
			if (!empty($key)) {
				$out .= ':'.$key;
			}
			$out .= '="'.$val.'"';
		}
		$out .= '>' . PHP_EOL;

		echo $out;
	}

	/**
	* Closes the open tags at the end of file
	*
	* @access	private
	* @return	void
	*/
	function printTail()
	{
		if ($this->version == RSS2)
		{
			echo '</channel>' . PHP_EOL . '</rss>';
		}
		elseif ($this->version == ATOM)
		{
			echo '</feed>';
		}
	}

	/**
	* Creates a single node as xml format
	*
	* @access	private
	* @param	string	name of the tag
	* @param	mixed	tag value as string or array of nested tags in 'tagName' => 'tagValue' format
	* @param	array	Attributes(if any) in 'attrName' => 'attrValue' format
	* @return	string	formatted xml tag
	*/
	function makeNode($tagName, $tagContent, $attributes = null)
	{
		$nodeText = '';
		$attrText = '';

		if (is_array($attributes))
		{
			foreach ($attributes as $key => $value)
			{
				$attrText .= " $key=\"$value\"";
			}
		}

		$attrText .= (in_array($tagName, $this->CDATAEncoding) && $this->version == ATOM)? ' type="html"' : '';

		$doCDATA = false;

		if (in_array($tagName, $this->CDATAEncoding) && strpos($tagContent, '<') !== false) {
			$doCDATA = true;
		}

		if (empty($tagContent)) {
			$nodeText .= "<{$tagName}{$attrText} />";
		} else {
			$nodeText .= ($doCDATA)? "<{$tagName}{$attrText}><![CDATA[" : "<{$tagName}{$attrText}>";

			if (is_array($tagContent))
			{
				$nodeText .= PHP_EOL;
				foreach ($tagContent as $key => $value)
				{
					$nodeText .= $this->makeNode($key, $value);
				}
			}
			else
			{
				$nodeText .= ($doCDATA)? $tagContent : htmlspecialchars($tagContent);
			}

			$nodeText .= ($doCDATA)? "]]></$tagName>" : "</$tagName>";
		}

		return $nodeText . PHP_EOL;
	}

	/**
	* @desc		Print channels
	* @access	private
	* @return	void
	*/
	function printChannels()
	{
		//Start channel tag
		if ($this->version == RSS2)
		{
			echo '<channel>' . PHP_EOL;
		}

		//Print Items of channel
		foreach ($this->channels as $key => $value)
		{
			if ($this->version == ATOM && $key == 'link')
			{
				// ATOM prints link element as href attribute
				echo $this->makeNode($key,'',array('href'=>$value));
				//Add the id for ATOM
				echo $this->makeNode('id',$this->uuid($value,'urn:uuid:'));
			}
			else
			{
				echo $this->makeNode($key, $value);
			}
		}
	}

	/**
	* Prints formatted feed items
	*
	* @access	private
	* @return	void
	*/
	function printItems()
	{
		foreach ($this->items as $item)
		{
			$thisItems = $item->getElements();

			//the argument is printed as rdf:about attribute of item in rss 1.0
			echo $this->startItem($thisItems['link']['content']);

			foreach ($thisItems as $feedItem)
			{
				echo $this->makeNode($feedItem['name'], $feedItem['content'], $feedItem['attributes']);
			}
			echo $this->endItem();
		}
	}

	/**
	* Make the starting tag of channels
	*
	* @access	private
	* @param	string	The vale of about tag which is used for only RSS 1.0
	* @return	void
	*/
	function startItem($about = false)
	{
		if ($this->version == RSS2)
		{
			echo '<item>' . PHP_EOL;
		}
		elseif ($this->version == ATOM)
		{
			echo "<entry>" . PHP_EOL;
		}
	}

	/**
	* Closes feed item tag
	*
	* @access	private
	* @return	void
	*/
	function endItem()
	{
		if ($this->version == RSS2)
		{
			echo '</item>' . PHP_EOL;
		}
		elseif ($this->version == ATOM)
		{
			echo "</entry>" . PHP_EOL;
		}
	}

	// End # functions ----------------------------------------------

} // end of class FeedWriter

?>