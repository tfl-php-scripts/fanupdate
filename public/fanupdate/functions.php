<?php

require_once('class/SqlConnection.php');
require_once('class/FanUpdate.php');

require_once('class/FanUpdate_Post.php');
require_once('class/FanUpdate_Comment.php');

// ________________________________________ ERROR REPORTING

function myErrorHandler($errno, $errstr, $errfile, $errline) {
   if (error_reporting() == 0) { return false; }
   switch ($errno) {
   case E_USER_ERROR:
       echo '<p class="error"><strong>ERROR:</strong> '.$errstr."</p>\n";
       exit(1);
       break;

   case E_WARNING:
   case E_USER_WARNING:
       echo '<p class="error"><strong>WARNING:</strong> '.$errstr.'</p>'."\n";
       break;

   case E_NOTICE:
   case E_USER_NOTICE:
       //echo '<p><strong>NOTICE:</strong> '.$errstr.'</p>'."\n";
       break;

   default:
       //echo "<p>Unknown error type: [$errno] $errstr</p>\n";
       break;
   }

   /* Don't execute PHP internal error handler */
   return true;
}

$old_error_handler = set_error_handler('myErrorHandler');



// ________________________________________ VALIDATION

function clean_input($str, $allowtags = false) {

    if (is_array($str)) {
        array_walk($str, 'clean_walk', $allowtags);
        return $str;
    }

    $str = stripslashes($str);

    if (!$allowtags) {
        $str = strip_tags($str);
    }
    $str = trim($str);

    return $str;
}

function clean_walk(&$item, $key, $allowtags) {
    $item = clean_input($item, $allowtags);
}

function is_email($email) {
    $regexp = "!^([_a-z0-9-]+)(\.[_a-z0-9-]+)*@([a-z0-9-]+)(\.[a-z0-9-]+)*(\.[a-z]{2,4})$!i";
    return preg_match($regexp, $email);
}

function is_url($url) {
    $regexp = "!^[\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]+$!";
    return preg_match($regexp, $url);
}



// ________________________________________ TEXT PARSING

function truncate_wc($phrase, $max_words) {
    $phrase_array = preg_split('/\n|\ /',$phrase);
    if (count($phrase_array) > $max_words && $max_words > 0) {
        $phrase = implode(' ', array_slice($phrase_array, 0, $max_words)).'&#8230;';
    }
    return $phrase;
}

function fancyamp($text) {
    $text = str_replace(' & ', ' &amp; ', $text);
    $text = str_replace(' &amp; ', ' <span class="amp">&amp;</span> ', $text);
    return $text;
}

// Various text parsing function from WordPress, with a few minor modifications

function zeroise($number,$threshold) { // function to add leading zeros when necessary
	return sprintf('%0'.$threshold.'s', $number);
}

function antispambot($emailaddy, $mailto=0) {
	$emailNOSPAMaddy = '';
	srand ((float) microtime() * 1000000);
	for ($i = 0; $i < strlen($emailaddy); $i = $i + 1) {
		$j = floor(rand(0, 1+$mailto));
		if ($j==0) {
			$emailNOSPAMaddy .= '&#'.ord(substr($emailaddy,$i,1)).';';
		} elseif ($j==1) {
			$emailNOSPAMaddy .= substr($emailaddy,$i,1);
		} elseif ($j==2) {
			$emailNOSPAMaddy .= '%'.zeroise(dechex(ord(substr($emailaddy, $i, 1))), 2);
		}
	}
	$emailNOSPAMaddy = str_replace('@','&#64;',$emailNOSPAMaddy);
	return $emailNOSPAMaddy;
}

function make_clickable($ret) {
	$ret = ' ' . $ret;
	// in testing, using arrays here was found to be faster
    
	$ret = preg_replace(
		array(
			'#([\s>])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]+)#is',
			'#([\s>])((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]+)#is',
			'!([\s>])([a-z0-9\-_.]+)(@)([a-zA-Z0-9\-.]+\.[a-z]{2,4})!ie',
			'!(mailto:)([a-z0-9\-_.]+)(@)([a-z0-9\-.]+\.[a-z]{2,4})!ie'),
		array(
			'$1<a href="$2">$2</a>',
			'$1<a href="http://$2">$2</a>',
			"'$1<a href=\"mailto:'.antispambot('$2@$4').'\">'.antispambot('$2@$4').'</a>'",
			"'mailto:'.antispambot('$2@$4')"),$ret);

	// this one is not in an array because we need it to run last, for cleanup of accidental links within links
	$ret = preg_replace("#(<a( [^>]+?>|>))(<a [^>]+?>)([^>]+?)</a></a>#i", "$3$4</a>", $ret);
	$ret = trim($ret);
	return $ret;
}

function wptexturize($text) {
	//global $wp_cockneyreplace;
	$next = true;
	$output = '';
	$curl = '';
	$textarr = preg_split('/(<.*>)/Us', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
	$stop = count($textarr);

	// if a plugin has provided an autocorrect array, use it
	if ( isset($wp_cockneyreplace) ) {
		$cockney = array_keys($wp_cockneyreplace);
		$cockneyreplace = array_values($wp_cockneyreplace);
	} else {
		$cockney = array("'tain't","'twere","'twas","'tis","'twill","'til","'bout","'nuff","'round","'cause");
		$cockneyreplace = array("&#8217;tain&#8217;t","&#8217;twere","&#8217;twas","&#8217;tis","&#8217;twill","&#8217;til","&#8217;bout","&#8217;nuff","&#8217;round","&#8217;cause");
	}

	$static_characters = array_merge(array('---', ' -- ', ' - ', '--', 'xn&#8211;', '...', '``', '\'s', '\'\'', ' (tm)'), $cockney);
	$static_replacements = array_merge(array('&#8212;', '&#8212;', '&#8212;', '&#8212;', 'xn--', '&#8230;', '&#8220;', '&#8217;s', '&#8221;', ' &#8482;'), $cockneyreplace);

	$dynamic_characters = array('/\'(\d\d(?:&#8217;|\')?s)/', '/(\s|\A|")\'/', '/(\d+)"/', '/(\d+)\'/', '/(\S)\'([^\'\s])/', '/(\s|\A)"(?!\s)/', '/"(\s|\S|\Z)/', '/\'([\s.]|\Z)/', '/(\d+)x(\d+)/');
	$dynamic_replacements = array('&#8217;$1','$1&#8216;', '$1&#8243;', '$1&#8242;', '$1&#8217;$2', '$1&#8220;$2', '&#8221;$1', '&#8217;$1', '$1&#215;$2');

	for ( $i = 0; $i < $stop; $i++ ) {
 		$curl = $textarr[$i];

		if (isset($curl{0}) && '<' != $curl{0} && $next) { // If it's not a tag
			// static strings
			$curl = str_replace($static_characters, $static_replacements, $curl);
			// regular expressions
			$curl = preg_replace($dynamic_characters, $dynamic_replacements, $curl);
		} elseif (strpos($curl, '<code') !== false || strpos($curl, '<pre') !== false || strpos($curl, '<kbd') !== false || strpos($curl, '<style') !== false || strpos($curl, '<script') !== false) {
			$next = false;
		} else {
			$next = true;
		}

		$curl = preg_replace('/&([^#])(?![a-zA-Z1-4]{1,8};)/', '&amp;$1', $curl);
		$output .= $curl;
	}
	
	$output = str_replace('&#8212;>', '-->', $output); // fix html comments
	
	$output = preg_replace('!(<h[1-6][^>]*>)(.*)?(&)(.*)?(</h[1-6]>)!e', "stripslashes('$1'.fancyamp('$2$3$4').'$5')", $output); // fancyamp headings
	
	$output = preg_replace('!([0-9]+)(st|nd|rd|th)!', '$1<sup>$2</sup>', $output); // superscripts

  	return $output;
}

// Accepts matches array from preg_replace_callback in wpautop()
// or a string
function clean_pre($matches) {
	if ( is_array($matches) )
		$text = $matches[1] . $matches[2] . "</pre>";
	else
		$text = $matches;

	$text = str_replace('<br />', '', $text);
	$text = str_replace('<p>', "\n", $text);
	$text = str_replace('</p>', '', $text);

	return $text;
}

function wpautop($pee, $br = 1, $allowHeading = false) {
	$pee = $pee . "\n"; // just to make things a little easier, pad the end
	$pee = preg_replace('|<br />\s*<br />|', "\n\n", $pee);
	$pee = str_replace("\r\n", "\n", $pee); // Space things out a little
	if ($allowHeading) {
		$pee = doHeaders($pee);
	}
	$pee = doLists($pee);
	$allblocks = '(?:table|thead|tfoot|caption|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|map|area|blockquote|address|math|style|script|input|p|h[1-6]|hr)';
	$pee = preg_replace('!(<' . $allblocks . '[^>]*>)!', "\n$1", $pee);
	$pee = preg_replace('!(</' . $allblocks . '>)!', "$1\n\n", $pee);
	$pee = str_replace(array("\r\n", "\r"), "\n", $pee); // cross-platform newlines
	$pee = preg_replace("/\n\n+/", "\n\n", $pee); // take care of duplicates
	$pee = preg_replace('/\n?(.+?)(?:\n\s*\n|\z)/s', "<p>$1</p>\n", $pee); // make paragraphs, including one at the end
	$pee = preg_replace('|<p>\s*?</p>|', '', $pee); // under certain strange conditions it could create a P of entirely whitespace
	$pee = preg_replace('!<p>([^<]+)\s*?(</(?:div|address|form)[^>]*>)!', "<p>$1</p>$2", $pee);
	$pee = preg_replace( '|<p>|', "$1<p>", $pee );
	$pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee); // don't pee all over a tag
	$pee = preg_replace("|<p>(<li.+?)</p>|", "$1", $pee); // problem with nested lists
	$pee = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee);
	$pee = str_replace('</blockquote></p>', '</p></blockquote>', $pee);
	$pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)!', "$1", $pee);
	$pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee);
	if ($br) {
		$pee = preg_replace('/<(script|style).*?<\/\\1>/se', 'stripslashes(str_replace("\n", "<WPPreserveNewline />", "\\0"))', $pee);
		$pee = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $pee); // optionally make line breaks
		$pee = str_replace('<WPPreserveNewline />', "\n", $pee);
	}
	$pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*<br />!', "$1", $pee);
	$pee = preg_replace('!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)!', '$1', $pee);
	if (strpos($pee, '<pre') !== false)
		$pee = preg_replace_callback('!(<pre.*?>)(.*?)</pre>!is', 'clean_pre', $pee );
	$pee = preg_replace( "|\n</p>$|", '</p>', $pee );
	//$pee = preg_replace( '|(<p>)?<img([^>]*)>(</p>)?|', '<img$2>', $pee ); // no pee img!

	return make_clickable(wptexturize($pee));
}

/*

Function from:

PHP Markdown
Copyright (c) 2004-2007 Michel Fortin  
<http://www.michelf.com/>  
All rights reserved.

Based on Markdown  
Copyright (c) 2003-2006 John Gruber   
<http://daringfireball.net/>   
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are
met:

* Redistributions of source code must retain the above copyright notice,
  this list of conditions and the following disclaimer.

* Redistributions in binary form must reproduce the above copyright
  notice, this list of conditions and the following disclaimer in the
  documentation and/or other materials provided with the distribution.

* Neither the name "Markdown" nor the names of its contributors may
  be used to endorse or promote products derived from this software
  without specific prior written permission.

This software is provided by the copyright holders and contributors "as
is" and any express or implied warranties, including, but not limited
to, the implied warranties of merchantability and fitness for a
particular purpose are disclaimed. In no event shall the copyright owner
or contributors be liable for any direct, indirect, incidental, special,
exemplary, or consequential damages (including, but not limited to,
procurement of substitute goods or services; loss of use, data, or
profits; or business interruption) however caused and on any theory of
liability, whether in contract, strict liability, or tort (including
negligence or otherwise) arising in any way out of the use of this
software, even if advised of the possibility of such damage.

*/

$tab_width = 4;
$list_level = 0;

function outdent($text) {
	#
	# Remove one level of line-leading tabs or spaces
	#
	global $tab_width;
	return preg_replace('/^(\t|[ ]{1,'.$tab_width.'})/m', '', $text);
}

function doHeaders($text) {
	# Setext-style headers:
	#	  Header 3
	#	  ========
	#  
	#	  Header 4
	#	  --------
	#
	$text = preg_replace_callback('{ ^(.+?)[ ]*\n(=+|-+)[ ]*\n+ }mx', '_doHeaders_callback_setext', $text);

	# atx-style headers:
	#	# Header 1
	#	## Header 2
	#	## Header 2 with closing hashes ##
	#	...
	#	###### Header 6
	#
	$text = preg_replace_callback('{
			^(\#{1,6})	# $1 = string of #\'s
			[ ]*
			(.+?)		# $2 = Header text
			[ ]*
			\#*			# optional closing #\'s (not counted)
			\n+
		}xm', '_doHeaders_callback_atx', $text);

	return $text;
}
function _doHeaders_callback_setext($matches) {
	$level = $matches[2]{0} == '=' ? 3 : 4;
	$block = "<h$level>".$matches[1]."</h$level>";
	return "\n" . $block . "\n\n";
}
function _doHeaders_callback_atx($matches) {
	$level = strlen($matches[1]);
	$block = "<h$level>".$matches[2]."</h$level>";
	return "\n" . $block . "\n\n";
}

function doLists($text) {
	#
	# Form HTML ordered (numbered) and unordered (bulleted) lists.
	#
	global $tab_width;
	$less_than_tab = $tab_width - 1;

	# Re-usable patterns to match list item bullets and number markers:
	$marker_ul  = '[*+-]';
	$marker_ol  = '\d+[.]';
	$marker_any = "(?:$marker_ul|$marker_ol)";

	$markers = array($marker_ul, $marker_ol);

	foreach ($markers as $marker) {
		# Re-usable pattern to match any entirel ul or ol list:
		$whole_list = '
			(								# $1 = whole list
			  (								# $2
				[ ]{0,'.$less_than_tab.'}
				('.$marker.')				# $3 = first list item marker
				[ ]+
			  )
			  (?s:.+?)
			  (								# $4
				  \z
				|
				  \n{2,}
				  (?=\S)
				  (?!						# Negative lookahead for another list item marker
					[ ]*
					'.$marker.'[ ]+
				  )
			  )
			)
		'; // mx
		
		# We use a different prefix before nested lists than top-level lists.
		# See extended comment in _ProcessListItems().
		
		global $list_level;
	
		if ($list_level) {
			$text = preg_replace_callback('{
					^
					'.$whole_list.'
				}mx',  '_doLists_callback', $text);
		}
		else {
			$text = preg_replace_callback('{
					(?:(?<=\n)\n|\A\n?) # Must eat the newline
					'.$whole_list.'
				}mx', '_doLists_callback', $text);
		}
	}

	return $text;
}
function _doLists_callback($matches) {
	# Re-usable patterns to match list item bullets and number markers:
	$marker_ul  = '[*+-]';
	$marker_ol  = '\d+[.]';
	$marker_any = "(?:$marker_ul|$marker_ol)";
	
	$list = $matches[1];
	$list_type = preg_match("/$marker_ul/", $matches[3]) ? "ul" : "ol";
	
	$marker_any = ( $list_type == "ul" ? $marker_ul : $marker_ol );
	
	$list .= "\n";
	$result = processListItems($list, $marker_any);
	
	$result = "<$list_type>\n" . $result . "</$list_type>";
	return "\n". $result ."\n\n";
}

function processListItems($list_str, $marker_any) {
	#
	# Process the contents of a single ordered or unordered list, splitting it
	# into individual list items.
	#
	# The $this->list_level global keeps track of when we're inside a list.
	# Each time we enter a list, we increment it; when we leave a list,
	# we decrement. If it's zero, we're not in a list anymore.
	#
	# We do this because when we're not inside a list, we want to treat
	# something like this:
	#
	#		I recommend upgrading to version
	#		8. Oops, now this line is treated
	#		as a sub-list.
	#
	# As a single paragraph, despite the fact that the second line starts
	# with a digit-period-space sequence.
	#
	# Whereas when we're inside a list (or sub-list), that line will be
	# treated as the start of a sub-list. What a kludge, huh? This is
	# an aspect of Markdown's syntax that's hard to parse perfectly
	# without resorting to mind-reading. Perhaps the solution is to
	# change the syntax rules such that sub-lists must start with a
	# starting cardinal number; e.g. "1." or "a.".
	
	global $list_level;
	
	$list_level++;

	# trim trailing blank lines:
	$list_str = preg_replace("/\n{2,}\\z/", "\n", $list_str);

	$list_str = preg_replace_callback('{
		(\n)?							# leading line = $1
		(^[ ]*)						# leading whitespace = $2
		('.$marker_any.') [ ]+		# list marker = $3
		((?s:.+?))						# list item text   = $4
		(?:(\n+(?=\n))|\n)				# tailing blank line = $5
		(?= \n* (\z | \2 ('.$marker_any.') [ ]+))
		}xm', '_processListItems_callback', $list_str);

	$list_level--;
	return $list_str;
}
function _processListItems_callback($matches) {
	$item = $matches[4];
	$leading_line =& $matches[1];
	$leading_space =& $matches[2];
	$tailing_blank_line =& $matches[5];

	if ($leading_line || $tailing_blank_line || 
		preg_match('/\n{2,}/', $item))
	{
		$item = wpautop(outdent($item)."\n");
	}
	else {
		# Recursion for sub-lists:
		$item = doLists(outdent($item));
		$item = preg_replace('/\n+$/', '', $item);
	}

	return "<li>" . $item . "</li>\n";
}

?>