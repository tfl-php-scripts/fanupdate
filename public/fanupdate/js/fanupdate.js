addEvent(window, 'load', function () {
	var inputs = document.getElementsByTagName('input');

	for (var i = 0; (input = inputs[i]); i++) {
	    if (input.type == 'checkbox' || input.type == 'radio') {
	        addEvent(input, 'click', highlightChecked);
			addEvent(input, 'change', highlightChecked);
	        if (input.checked) {
				css.addClassToElement(input.parentNode, 'highlight');
			} else {
				css.removeClassFromElement(input.parentNode, 'highlight');
			}
		}
	}
});

function highlightChecked(e) {
    el = getEventTarget(e);
    if (el.type == 'radio') {
        var inputs = document.getElementsByName(el.name);
        for (var i = 0; (input = inputs[i]); i++) {
            css.removeClassFromElement(input.parentNode, 'highlight');
        }
    }
    if (el.checked) {
		css.addClassToElement(el.parentNode, 'highlight');
    } else {
        css.removeClassFromElement(el.parentNode, 'highlight');
    }   
}

function $(id) {
	return document.getElementById(id);
}

function insertAfter(node, referenceNode) {
    referenceNode.parentNode.insertBefore(node, referenceNode.nextSibling);
}

function insertBefore(node, referenceNode) {
    referenceNode.parentNode.insertBefore(node, referenceNode);
}

function wysiwygButton(name, img, title, func) {
	this.name = name;
	this.img = img;
	this.func = func;
	this.title = title;
}

function makeWysiwyg(el) {
	var p = document.createElement('span');
	p.className = "wysiwygmenu";
	
	for (var i = 0; button = myWysiwyg[i]; i++) {
		var a = document.createElement('a');
		a.href = '#';
		a.title = button.title;
		a.className = el.id;
		a.onclick = button.func;
		var img = document.createElement('img');
		img.src = button.img;
		img.alt = button.name;
		img.className = el.id;
		a.appendChild(img);
		p.appendChild(a);
	}
	
	insertBefore(p, el);
}

function getXmlHttpObject() {

    var xmlHttp = false;

    try {
        xmlHttp = new XMLHttpRequest();
    } catch (trymicrosoft) {
        try {
            xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (othermicrosoft) {
            try {
                xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (failed) {
                xmlHttp = false;
            }
        }
    }

    return xmlHttp;
}

function makeRequest(url, targetID, method, qs, callback) {

    var xmlHttp = getXmlHttpObject();

    // Setup a function for the server to run when it's done
    xmlHttp.onreadystatechange = function() {
        if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
            callback(xmlHttp.responseText, targetID, url);
        }
    }

    // Open a connection to the server
    xmlHttp.open(method, url, true);

    var contentType = "application/x-www-form-urlencoded; charset=UTF-8";
    xmlHttp.setRequestHeader("Content-Type", contentType);
    // Send the request
    xmlHttp.send(qs);
}

// http://www.somacon.com/p143.php
function getCheckedValue(radioObj) {
	if(!radioObj)
		return "";
	var radioLength = radioObj.length;
	if(radioLength == undefined)
		if(radioObj.checked)
			return radioObj.value;
		else
			return "";
	for(var i = 0; i < radioLength; i++) {
		if(radioObj[i].checked) {
			return radioObj[i].value;
		}
	}
	return "";
}

function insertLink(ID) {

    url = prompt('Link destination:', '');
    // add protocol if needed
	if (url != '') {
	    if (!url.match(/^http/)) { url = 'http://'+url; }

	    if (arguments[1]) return formatText(ID,'[URL='+url+']','[/URL]');
	    return formatText(ID,'<a href="'+url+'">','</a>');
	}
}

function formatText(ID, tagstart, tagend) {

    el = $(ID);

    if (el.setSelectionRange) {

        top = el.scrollTop;
        newlen = el.selectionEnd + tagstart.length + tagend.length;

        selBefore = el.value.substr(0, el.selectionStart);
        selAfter = el.value.substr(el.selectionEnd, el.value.length);
        sel = el.value.substring(el.selectionStart, el.selectionEnd);

        el.value = selBefore + tagstart + sel + tagend + selAfter;

        el.setSelectionRange(newlen, newlen);
        el.focus();
        el.scrollTop = top;

    } else {

        var range = document.selection.createRange();
        var selectedText = range.text;
       
        if (selectedText != '') {
            var newText = tagstart + selectedText + tagend;
            range.text = newText;
            range.select();
        }
    }

    return false;
}

/* other support functions -- thanks, ecmanaut! */
var strftime_funks = {
  zeropad: function( n ){ return n>9 ? n : '0'+n; },
  a: function(t) { return this.H(t) < 12 ? 'am' : 'pm'; },
  A: function(t) { return this.H(t) < 12 ? 'AM' : 'PM'; },
  c: function(t) { return t.toString() },
  d: function(t) { return this.zeropad(t.getDate()) },
  D: function(t) { return ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'][t.getDay()] },
  F: function(t) { return ['January','February','March','April','May','June', 'July','August','September','October','November','December'][t.getMonth()] },
  g: function(t) { return (t.getHours() + 12) % 12 },
  G: function(t) { return t.getHours() },
  h: function(t) { return this.zeropad((t.getHours() + 12) % 12) },
  H: function(t) { return this.zeropad(t.getHours()) },
  i: function(t) { return this.zeropad(t.getMinutes()) },
  j: function(t) { return t.getDate() },
  l: function(t) { return ['Sunday','Monday','Tuedsay','Wednesday','Thursday','Friday','Saturday'][t.getDay()] },
  m: function(t) { return this.zeropad(t.getMonth()+1) }, // month-1
  M: function(t) { return ['Jan','Feb','Mar','Apr','May','Jun', 'Jul','Aug','Sep','Oct','Nov','Dec'][t.getMonth()] },
  n: function(t) { return (t.getMonth()+1) }, // month-1
  N: function(t) { return (t.getDay()+1) }, // day-1
// stupid JS TZ sign is backwards!
  O: function(t) { var o = 0 - (t.getTimezoneOffset() / 60); var f = (o >= 0) ? '+' : '-'; return f + this.zeropad(Math.abs(o)) + '00'; },
  s: function(t) { return this.zeropad(t.getSeconds()) },
  w: function(t) { return t.getDay() }, // 0..6 == sun..sat
  y: function(t) { return this.zeropad(this.Y(t) % 100); },
  Y: function(t) { return t.getFullYear() },
  '%': function(t) { return '%' }
};

Date.prototype.strftime = function (fmt) {
    var t = this;
    for (var s in strftime_funks) {
        if (s.length == 1 )
            fmt = fmt.replace('%' + s, strftime_funks[s](t));
    }
    return fmt;
};

if (typeof(TrimPath) != 'undefined') {
    TrimPath.parseTemplate_etc.modifierDef.strftime = function (t, fmt) {
        return new Date(t).strftime(fmt);
    }
}

function dateDiff(date1, date2) {

    datDate1 = date1.getTime();
    datDate2 = date2.getTime();
    datediff = Math.ceil((datDate1 - datDate2)/(24*60*60*1000));

    return datediff;
}

/**
 * addEvent written by Dean Edwards, 2005
 * with input from Tino Zijdel
 *
 * http://dean.edwards.name/weblog/2005/10/add-event/
 **/
function addEvent(element, type, handler) {
	// assign each event handler a unique ID
	if (!handler.$$guid) handler.$$guid = addEvent.guid++;
	// create a hash table of event types for the element
	if (!element.events) element.events = {};
	// create a hash table of event handlers for each element/event pair
	var handlers = element.events[type];
	if (!handlers) {
		handlers = element.events[type] = {};
		// store the existing event handler (if there is one)
		if (element["on" + type]) {
			handlers[0] = element["on" + type];
		}
	}
	// store the event handler in the hash table
	handlers[handler.$$guid] = handler;
	// assign a global event handler to do all the work
	element["on" + type] = handleEvent;
};
// a counter used to create unique IDs
addEvent.guid = 1;

function removeEvent(element, type, handler) {
	// delete the event handler from the hash table
	if (element.events && element.events[type]) {
		delete element.events[type][handler.$$guid];
	}
};

function handleEvent(event) {
	var returnValue = true;
	// grab the event object (IE uses a global event object)
	event = event || fixEvent(window.event);
	// get a reference to the hash table of event handlers
	var handlers = this.events[event.type];
	// execute each event handler
	for (var i in handlers) {
		this.$$handleEvent = handlers[i];
		if (this.$$handleEvent(event) === false) {
			returnValue = false;
		}
	}
	return returnValue;
};

function fixEvent(event) {
	// add W3C standard event methods
	event.preventDefault = fixEvent.preventDefault;
	event.stopPropagation = fixEvent.stopPropagation;
	return event;
};
fixEvent.preventDefault = function() {
	this.returnValue = false;
};
fixEvent.stopPropagation = function() {
	this.cancelBubble = true;
};

// end from Dean Edwards


/**
 * Creates an Element for insertion into the DOM tree.
 * From http://simon.incutio.com/archive/2003/06/15/javascriptWithXML
 *
 * @param element the element type to be created.
 *				e.g. ul (no angle brackets)
 **/
function createElement(element) {
	if (typeof document.createElementNS != 'undefined') {
		return document.createElementNS('http://www.w3.org/1999/xhtml', element);
	}
	if (typeof document.createElement != 'undefined') {
		return document.createElement(element);
	}
	return false;
}

/**
 * "targ" is the element which caused this function to be called
 * from http://www.quirksmode.org/js/events_properties.html
 **/
function getEventTarget(e) {
	var targ;
	if (!e) {
		e = window.event;
	}
	if (e.target) {
		targ = e.target;
	} else if (e.srcElement) {
		targ = e.srcElement;
	} else if (typeof e == 'object') {
		targ = e;
	} else if (typeof e == 'string') {
		targ = $(e);
	}
	if (targ.nodeType == 3) { // defeat Safari bug
		targ = targ.parentNode;
	}

	return targ;
}

/**
 * Written by Neil Crosby. 
 * http://www.workingwith.me.uk/articles/scripting/standardista_table_sorting
 *
 * This module is based on Stuart Langridge's "sorttable" code.  Specifically, 
 * the determineSortFunction, sortCaseInsensitive, sortDate, sortNumeric, and
 * sortCurrency functions are heavily based on his code.  This module would not
 * have been possible without Stuart's earlier outstanding work.
 *
 * Use this wherever you want, but please keep this comment at the top of this file.
 *
 * Copyright (c) 2006 Neil Crosby
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy 
 * of this software and associated documentation files (the "Software"), to deal 
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell 
 * copies of the Software, and to permit persons to whom the Software is 
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in 
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR 
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE 
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER 
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, 
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE 
 * SOFTWARE.
 **/

var css = {
	/**
	 * Returns an array containing references to all elements
	 * of a given tag type within a certain node which have a given class
	 *
	 * @param node		the node to start from 
	 *					(e.g. document, 
	 *						  getElementById('whateverStartpointYouWant')
	 *					)
	 * @param searchClass the class we're wanting
	 *					(e.g. 'some_class')
	 * @param tag		 the tag that the found elements are allowed to be
	 *					(e.g. '*', 'div', 'li')
	 **/
	getElementsByClass : function(node, searchClass, tag) {
		var classElements = new Array();
		var els = node.getElementsByTagName(tag);
		var elsLen = els.length;
		var pattern = new RegExp("(^|\\s)"+searchClass+"(\\s|$)");
		
		
		for (var i = 0, j = 0; i < elsLen; i++) {
			if (this.elementHasClass(els[i], searchClass) ) {
				classElements[j] = els[i];
				j++;
			}
		}
		return classElements;
	},


	/**
	 * PRIVATE.  Returns an array containing all the classes applied to this
	 * element.
	 *
	 * Used internally by elementHasClass(), addClassToElement() and 
	 * removeClassFromElement().
	 **/
	privateGetClassArray: function(el) {
		return el.className.split(' '); 
	},

	/**
	 * PRIVATE.  Creates a string from an array of class names which can be used 
	 * by the className function.
	 *
	 * Used internally by addClassToElement().
	 **/
	privateCreateClassString: function(classArray) {
		return classArray.join(' ');
	},

	/**
	 * Returns true if the given element has been assigned the given class.
	 **/
	elementHasClass: function(el, classString) {
		if (!el) {
			return false;
		}
		
		var regex = new RegExp('\\b'+classString+'\\b');
		if (el.className.match(regex)) {
			return true;
		}

		return false;
	},

	/**
	 * Adds classString to the classes assigned to the element with id equal to
	 * idString.
	 **/
	addClassToId: function(idString, classString) {
		this.addClassToElement($(idString), classString);
	},

	/**
	 * Adds classString to the classes assigned to the given element.
	 * If the element already has the class which was to be added, then
	 * it is not added again.
	 **/
	addClassToElement: function(el, classString) {
		var classArray = this.privateGetClassArray(el);

		if (this.elementHasClass(el, classString)) {
			return; // already has element so don't need to add it
		}

		classArray.push(classString);

		el.className = this.privateCreateClassString(classArray);
	},

	/**
	 * Removes the given classString from the list of classes assigned to the
	 * element with id equal to idString
	 **/
	removeClassFromId: function(idString, classString) {
		this.removeClassFromElement($(idString), classString);
	},

	/**
	 * Removes the given classString from the list of classes assigned to the
	 * given element.  If the element has the same class assigned to it twice, 
	 * then only the first instance of that class is removed.
	 **/
	removeClassFromElement: function(el, classString) {
		var classArray = this.privateGetClassArray(el);

		for (x in classArray) {
			if (classString == classArray[x]) {
				classArray[x] = '';
				break;
			}
		}

		el.className = this.privateCreateClassString(classArray);
	}
}