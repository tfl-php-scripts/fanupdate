/*
 * FanUpdate version 2.2.1
 * Author: Jenny Ferenc
 * Copyright: 2008 Jenny Ferenc
 * Date: 2005-04-01
 * Updated: 2008-06-08
 * Requirements: PHP 4.3.0+, MySQL 4 (CAPTCHA requires FreeType library)
 * Link: http://prism-perfect.net/fanupdate
 * 
 * This program is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU General Public License 
 * as published by the Free Software Foundation; either version 2 
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
 * GNU General Public License for more details: 
 * http://www.gnu.org/licenses/gpl.html
 * 
 */

It would be nice if you kept the link to http://prism-perfect.net/fanupdate 
or at least added it to a site credits page, but that is not required. 
Merely keep any credit notices intact in the code. Note that this 
is a change from previous versions.

FanUpdate is a simple blogging script designed to keep an updates log 
for an entire collective of websites. It can also be used as a lightweight 
standalone blog.

FanUpdate may work with versions of MySQL earlier than 4, but I have 
no way to test that. You may also be able to use it with a different 
database, if you change all of the mysql_ functions in SqlConnection.php 
to those for other PHP database functions, but I have also not tested this.

// ____________________________________________________________

CONTENTS

1. Installation instructions
2. Displaying FanUpdate on your site
3. Template variables
4. Adding smilies
5. New spam points system
6. Notes on upgrading
7. Credits

// ____________________________________________________________

Follow the instructions below to install FanUpdate version 2.2.1.

If you are upgrading from version 2.1 or higher, upload all the new files 
(overwriting your current files) EXCEPT blog-config.php -- you can keep 
the old file for that. Then run the installer as described in step 3 below.

If you are upgrading from version 2.0/1.6/1.5, you will need to 
enter your current config settings into the NEW blog-config.php 
file; there are some important changes to that and the old one 
won't work. Then, follow the install instructions below.

(There is currently no upgrade script for versions 1.1 
and earlier, because no one seems to be using them anymore. 
If you need it, please contact the script author.)

// ____________________________________________________________

INSTALLATION INSTRUCTIONS / VERSION 2.2-1.5 UPGRADE INSTRUCTIONS:

0. IMPORTANT: If you are upgrading from an earlier version,
BACKUP YOUR DATABASE (AND FILES) BEFORE YOU BEGIN. Just in case.

1. Edit the config file:
Open the file blog-config.php in a plain text editor. Edit the variables 
for your site. You MUST change the DATABASE VARIABLES for your MySQL database 
and the ADMIN VARIABLES for your username and password. The TABLE VARIABLES 
only need to be changed if you want to use FanUpdate in conjunction with one 
of the supported collective management scripts (Enthusiast 3, Flinx Collective, 
or Fan Admin). If so, you must install FanUpdate in THE SAME database as 
said management script.

2. Upload FanUpdate files:
Upload all of the FanUpdate files to an http-accessible directory on your webserver.
For example, http://yoursite.com/fanupdate/ 
(Overwrite your current files if you are upgrading.)

3. Run install script:
Run the install.php script by going to http://yoursite.com/FANUPDATE_FOLDER/install.php 
You'll need to set some more options for your site here, including the location 
of your main site updates page. Then submit the form to create or update the FanUpdate 
database tables.

4. If you see errors about 'old file xxx not deleted!' this is OK -- you will just need 
to delete that file manually to keep your install tidy. However, ANY OTHER ERRORS while 
installing are a serious problem. If you can't figure out what is wrong, check the 
documentation at http://prism-perfect.net/fanupdate

5. Once you have successfully run the installation script, be sure to DELETE it 
from you webserver (you won't need it anymore). Then you can begin posting entries 
from the admin panel. If you are using the script stand-alone you'll first need 
to add the names of your categories/sites in the 'Categories' tab.

6. Add PHP snippet(s) to your site(s) to display your blog (see below) if this is a 
new installation.

// ____________________________________________________________

DISPLAYING FANUPDATE ON YOUR SITE:

To display your complete blog (ALL update categories), visit the admin panel home page 
and follow the link for 'how to display your blog.'

This will generate a code snippet that you can copy-and-paste into any .php page 
to display your updates blog.

For a single category blog, visit the 'Categories' admin page to get the display code.

If you want to display ONLY 'Whole Collective' updates on your main site blog, 
use a single-category snippet and change the $listingid variable to zero:

$listingid = 0;

Please note: This snippet is not compatible with NL-ConvertToPHP/dynamic includes pages! 
This means you can't put the inclusion snippet within any PHP coding similar to 

<?php if ($_SERVER['QUERY_STRING'] == 'updates') { ?> 

If you're not sure, the best bet is to put the snippet for FanUpdate in its own .php page, 
along with whatever other content you want on your updates page.

// ____________________________________________________________

TEMPLATE VARIABLES:

ENTRY TEMPLATE:

{{id}}			ID of entry
{{title}}		title of entry
{{url}}			URL of single-post page with comments)
{{date}}		date posted (formatted according to your date_format setting)
{{category}}		categor(y|ies) of entry (as links to category archive page)
{{comment_link}}	link to comments page/section
{{body}}		complete text of entry

COMMENT TEMPLATE:

{{id}}			ID of comment
{{name}}		name of commenter (linked to their URL, if provided)
{{gravatar}}	Gravatar of commenter
{{date}}		date posted (formatted according to your date_format setting)
{{body}}		complete text of comment

COMMENT FORM TEMPLATE:

{{fanuname}}		name of commenter, if remembered
{{fanuemail}}		email of commenter, if remembered
{{fanuurl}}			url of commenter, if remembered
{{captcha_image}}	url of captcha image

FOOTER TEMPLATE:

{{main_url}}		url of blog front page (also for search action)
{{archive_url}}		url of blog archives page
{{rss_url}}			url of RSS feed
{{fanupdate_url}}	url of FanUpdate info page (for credit link)
{{fanupdate_version}}	version of FanUpdate currently running

// ____________________________________________________________

ADDING SMILIES:

To add more smiley icons, just upload them to the FanUpdate img/ directory. 
They will then appear as choices in the smiley configuration panel.

// ____________________________________________________________

POINTS SYSTEM FOR SPAM BLOCKING:

Comments are given positive points for good features and negative points 
for bad features. Comments must have at least points_approval_threshold 
to be automatically approved. Comments with less than points_pending_threshold 
are considered spam, and you are never notified of them. Spam comments can be 
seen and deleted (if you feel the need) from the Spam tab on the comments admin page. 
Ideally, the points system should block most spam, and you shouldn't need comment 
moderation or the captcha (although both are still available).

Tests performed include:
-4 for blank HTTP_ACCEPT_LANGUAGE header
- number of links for more than 2 links
+2 for no links
-2 for comment less than 20 characters
+1 for comment greater than 20 characters with no links
+ num approved comments from same email more than 7 days old
- num unapproved comments from same email more than 7 days old
-2 if ip == hostname
-1 for url (it's a link too!)
-2 for url longer than 35 characters
-2 for no email address

Currently, you can't modify the tests without changing process.php, but you can tweak 
the approval/spam thresholds if these rules are not ideal for your site.

Original points system idea from Jonathan Snook:
http://snook.ca/archives/other/effective_blog_comment_spam_blocker/

// ____________________________________________________________

NOTES ON UPGRADING:

Check changelog.txt for features and changes not mentioned here.

The {{wysiwyg}} comment form template variable has been replaced by automatic
JavaScript for adding the wysiwyg controls. The upgrade script will try to remove
it from you current comment template, but after upgrading you should go check that
it was removed -- otherwise, it will show up on your comment form.

Category-level options were previously (pre-2.0) set in the PHP code snippet you included in your site. 
For improved spam defense, and to make editing them easier, they are now set from the admin panel. 
Unfortunately, if you use different configurations for each of your sites, you will need 
to transfer the settings manually -- there's no simple way for the script to retrieve your 
settings. However, you do NOT need to generate new snippets for your sites -- your extra 
variable from previous versions will just be ignored.

Upgrading means replacing all of your current FanUpdate files. If you've made 
substantial modifications to the script, you will need to save your changed files 
and re-do the changes on the new files. (If necessary. You may be able to achieve 
your changes with the built-in templates.)

// ____________________________________________________________

I think that's about it -- please contact me if you find any bugs 
or if these instructions are unclear.

Jenny Ferenc
jenny@prism-perfect.net
http://prism-perfect.net/

// ____________________________________________________________

CREDITS:

JavaScript for table sorting and some basic functions:
(c) 2006 Neil Crosby
http://www.workingwith.me.uk/articles/scripting/standardista_table_sorting

Silk icons in img/ and css/ directories:
Mark James
http://famfamfam.com/lab/icons/silk/

Some text parsing functions in functions.php
Originally from WordPress
http://wordpress.org/

PHP Markdown
(c) 2004-2007 Michel Fortin  
http://www.michelf.com/
Based on Markdown  
(c) 2003-2006 John Gruber
http://daringfireball.net/
All rights reserved.

CaptchaSecurityImages
(c) 2006 Simon Jarvis
http://www.white-hat-web-design.co.uk/articles/php-captcha.php

SqlConnection class (heavily modified)
Originally by Jay Pipes
http://www.jpipes.com/

FeedWriter and FeedItem classes (heavily modified)
Originally by Anis uddin Ahmad
http://www.ajaxray.com/projects/rss