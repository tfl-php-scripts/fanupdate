<?php
/*****************************************************************************
 * Universal Feed Writer
 *
 * FeedItem class - Used as feed element in FeedWriter class
 *
 * Copyright (c) Anis uddin Ahmad <anisniit@gmail.com> http://www.ajaxray.com/projects/rss
 * Copyright (c) 2008 by Jenny Ferenc (contributor) <jenny@prism-perfect.net>
 * Copyright (c) 2020 by Ekaterina (contributor) http://scripts.robotess.net
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 ******************************************************************************/

class FeedItem
{
    public $elements = array();    //Collection of feed elements
    public $version;

    /**
     * Constructor
     *
     * @param string        (RSS2/ATOM) RSS2 is default.
     */
    public function __construct($version = RSS2)
    {
        $this->version = $version;
    }

    /**
     * Add an element to elements array
     *
     * @access    public
     * @param string    The tag name of an element
     * @param string    The content of tag
     * @param array    Attributes(if any) in 'attrName' => 'attrValue' format
     * @return    void
     */
    public function addElement($elementName, $content, $attributes = null)
    {
        $this->elements[$elementName]['name'] = $elementName;
        $this->elements[$elementName]['content'] = $content;
        $this->elements[$elementName]['attributes'] = $attributes;
    }

    /**
     * Set multiple feed elements from an array.
     * Elements which have attributes cannot be added by this method
     *
     * @access    public
     * @param array    array of elements in 'tagName' => 'tagContent' format.
     * @return    void
     */
    public function addElementArray($elementArray)
    {
        if (!is_array($elementArray)) {
            return;
        }
        foreach ($elementArray as $elementName => $content) {
            $this->addElement($elementName, $content);
        }
    }

    /**
     * Return the collection of elements in this feed item
     *
     * @access    public
     * @return    array
     */
    public function getElements()
    {
        return $this->elements;
    }

    // Wrapper functions ------------------------------------------------------

    /**
     * Set the 'description' element of feed item
     *
     * @access    public
     * @param string    The content of 'description' element
     * @return    void
     */
    public function setDescription($description)
    {
        $tag = ($this->version == ATOM) ? 'summary' : 'description';
        $this->addElement($tag, $description);
    }

    /**
     * Set the 'title' element of feed item
     *
     * @access    public
     * @param string    The content of 'title' element
     * @return    void
     */
    public function setTitle($title)
    {
        $this->addElement('title', $title);
    }

    /**
     * Set the 'date' element of feed item
     *
     * @access    public
     * @param string    The content of 'date' element
     * @return    void
     */
    public function setDate($date)
    {
        if (!is_numeric($date)) {
            $date = strtotime($date);
        }

        if ($this->version == ATOM) {
            $tag = 'updated';
            $value = date(DATE_ATOM, $date);
        } elseif ($this->version == RSS2) {
            $tag = 'pubDate';
            $value = date(DATE_RSS, $date);
        } else {
            $tag = 'dc:date';
            $value = date('Y-m-d', $date);
        }

        $this->addElement($tag, $value);
    }

    /**
     * Set the 'link' element of feed item
     *
     * @access    public
     * @param string    The content of 'link' element
     * @param bool $guid
     * @return    void
     */
    public function setLink($link, $guid = false)
    {
        if ($this->version == RSS2) {
            $this->addElement('link', $link);
            if ($guid) {
                $this->addElement('guid', $link, array('isPermaLink' => 'true'));
            }
        } elseif ($this->version == ATOM) {
            $this->addElement('link', '', array('href' => $link));
            $this->addElement('id', FeedWriter::uuid($link, 'urn:uuid:'));
        }
    }

    public function setAuthor($email, $name = '')
    {
        if ($this->version == RSS2) {
            if (empty($email)) {
                $email = 'nobody@example.com';
            }
            $author = $email;
            if (!empty($name)) {
                $author .= ' (' . $name . ')';
            }
            $this->addElement('author', $author);
        } else {
            $this->addElement('author', array('name' => $name, 'email' => $email));
        }
    }

    /**
     * Set the 'enclosure' element of feed item
     * For RSS 2.0 only
     *
     * @access    public
     * @param string    The url attribute of Enclosure tag
     * @param string    The length attribute of Enclosure tag
     * @param string    The type attribute of Enclosure tag
     * @return    void
     */
    public function setEnclosure($url, $length, $type)
    {
        $attributes = array('url' => $url, 'length' => $length, 'type' => $type);
        $this->addElement('enclosure', '', $attributes);
    }

}
