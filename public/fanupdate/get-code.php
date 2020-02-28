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


require_once('blog-config.php');
require_once('functions.php');

$fu =& FanUpdate::instance();
$fu->addOptFromDb();
$fu->login();

$fu->getHeader('Snippet');

// ____________________________________________________________ DISPLAY CODE

if (!empty($_GET['id'])) {

    $sql_id = (int)$_GET['id'];

    $query = 'SELECT ' .$fu->getOpt('col_subj'). ' AS cat_name
      FROM ' .$fu->getOpt('collective_table'). '
      WHERE ' .$fu->getOpt('col_id')."=$sql_id LIMIT 1";

    $subject = $fu->db->GetFirstCell($query);

} else {
    $subject = 'all posts';
}

?>

<div class="col12">

<h2>Display Code</h2>

<p>Edit the variables in this snippet according to the instructions below, then copy an paste it into the PHP-enabled page where you'd like to display the blog for <strong><?php echo $subject; ?></strong>.</p>

<p><textarea cols="90" rows="11">&lt;?php

// FanUpdate <?php echo $fu->getOpt('version'); ?> blog
// subject: <?php echo $subject; ?>


<?php if (!empty($_GET['id'])) { ?>
$listingid = <?php echo $sql_id; ?>;
<?php } ?>
<?php if (!empty($_GET['id'])) { ?>
$main_limit = 1;
<?php } else { ?>
$main_limit = 5;
<?php } ?>

<?php if (isset($_GET['id'])) { ?>
require_once('<?php echo $fu->getOpt('install_path'); ?>/show-cat.php');
<?php } else { ?>
require_once('<?php echo $fu->getOpt('install_path'); ?>/show-blog.php');
<?php } ?>

?&gt;</textarea></p>

<h3>Explanation of Variables</h3>

<ul>
<?php if (isset($_GET['id'])) { ?>
<li><strong>$listingid</strong> is the unique ID for this particular category.</li>
<?php } ?>
<li><strong>$main_limit</strong> is the number of posts you'd like displayed on the main updates page.</li>
</ul>

</div><!-- END .col12 -->

<div class="col22">

<h2>CSS Rules</h2>

<p>You may want to include these <abbr title="Cascading Style Sheet">CSS</abbr> rules in your site style sheet if you are using the default templates. You can of course <a href="templates.php">change the templates</a> and/or modify this styling however you see fit.</p>

<p><textarea cols="90" rows="16">
/*	====	FanUpdate <?php echo $fu->getOpt('version'); ?>	====	*/

div#fanupdate {}

h2 {}
h3 {}
p {}

/*	====	Entries	====	*/

div.post {}

p.catfile {padding: 0 0 0 3em;}

/*	====	Comments	====	*/

div#comments {}

div.comment {
	clear: both;
	padding: 0.5em;
}

div.comment p {padding: 0 0 0 3em;}

div.comment p.commenter {padding: 0;}

div.odd {background: #f3f3f3;}

div.author {}

div#newComment {}

img.gravatar {
	float: right;
	margin: 0 1em 1em 0;
}

/*	====	Comment Form	====	*/

div.comments-form {}

form#comments-form {}

p#cmt-rules {}
p#cmt-moderation {}

label {
	display: block;
	float: left;
	width: 9em;
	text-align: right;
	margin: 0 0.5em 0 0;
}

input, textarea, select, option {
	font-family: Arial, sans-serif;
	font-size: 1em;
}

textarea {
	width: 100%;
}

.wysiwygmenu {
	display: block;
}
.wysiwygmenu a {
	padding: 0.125em 0.25em;
}

/*	====	Footer	====	*/

a.rss {}

div.archivelink {
	text-align: right;	
	margin: 2em 0 0 0;
}

div.credit {
	clear: both;
	text-align: center;
	border-top: 6px solid #eee;
	margin: 1em 0 0 0;
}

div.credit p {margin: 0;}
</textarea></p>

</div><!-- END .col22 -->

<?php $fu->getFooter(); ?>
