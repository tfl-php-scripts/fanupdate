<?php
/*****************************************************************************
 * FanUpdate
 * Copyright (c) Jenny Ferenc <jenny@prism-perfect.net>
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

// ------------->ADMIN VARIABLES

$fanupdate['admin_username']	= 'admin';
$fanupdate['admin_password']	= md5('pass');

// ------------->DATABASE VARIABLES

$fanupdate['dbhost']		= 'localhost';
$fanupdate['dbuser']		= 'admin';
$fanupdate['dbpass']		= 'pass';
$fanupdate['dbname']		= 'database_name';

// ------------->TABLE VARIABLES

// Which script do you use for your fanlistings collective?
// Currently, the supported options are:

// Enthusiast			'e'
// Flinx Collective		'f'
// Fan Admin			'g'

// Or if you don't use one, enter 'n'

$fanupdate['collective_script']	= 'n';

// The name of your collective table. Here's the usual defaults:

// Enthusiast			'owned'
// Flinx Collective		'flinxcol_link'
// Fan Admin			'fa_fls'

// Or else enter your custom table name.
// If using FanUpdate as a standard blog, this is you categories table.

$fanupdate['collective_table']	= 'blog_category';

// Other tables for the script.

$fanupdate['blog_table']		= 'blog';
$fanupdate['catjoin_table']		= 'blog_catjoin';
$fanupdate['comments_table']	= 'blog_comments';
$fanupdate['options_table']		= 'blog_options';
$fanupdate['blacklist_table']	= 'blog_blacklist';
$fanupdate['catoptions_table']	= 'blog_catoptions';
$fanupdate['smilies_table']		= 'blog_smilies';

// ------------->COLLECTIVE TABLE COLUMN NAMES (optional)

// If you are using some other collective script, you can use it with FanUpdate.
// IF YOU DON'T UNDERSTAND THESE INSTRUCTIONS, STOP!
// Just make an entry for it here, like this:
// The first index, 'x', is what you've put for $fanupdate['collective_script']
// The second parts are the primary ID and FL name columns of your collective table
// $coltable['x']['id'] = 'listingid';
// $coltable['x']['subject'] = 'subject';

$coltable['e']['id']		= 'listingid';
$coltable['e']['subject']	= 'subject';

$coltable['f']['id']		= 'linkID';
$coltable['f']['subject']	= 'subject';

$coltable['g']['id']		= 'fl_id';
$coltable['g']['subject']	= 'flsubject';

$coltable['n']['id']		= 'fl_id';
$coltable['n']['subject']	= 'fl_subject';
