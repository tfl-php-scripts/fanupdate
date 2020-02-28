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

$clean = array();
$showform = false;

// _____________________________________________ GET MySQL DATA

if (!empty($_GET['id'])) {

    $sql_id = (int)$_GET['id'];

    $query = 'SELECT * FROM ' .$fu->getOpt('comments_table'). ' WHERE comment_id=' .$sql_id;
    $fu->db->Execute($query);

    $row = $fu->db->GetRecord();
    $cmt = new FanUpdate_Comment($row, $fu);
 
    $showform = true;

// _____________________________________________ CLEAN POST DATA

} elseif (isset($_POST['action'])) {

    $clean = clean_input($_POST);
 	$clean['comment'] = clean_input($_POST['comment'], true);

    if (isset($clean['date_to_now']) || empty($clean['added'])) {
        $clean['added'] = gmdate('Y-m-d H:i:s');
    } else {
        $clean['added'] = gmdate('Y-m-d H:i:s', strtotime($clean['added']));
    }

    $clean['comment_id'] = $clean['id'];

    $cmt = new FanUpdate_Comment($clean, $fu);

// _____________________________________________ VALIDATE POST DATA

    if ($_POST['action'] == 'update' || $_POST['action'] == 'preview') {

        $showform = true;

        if (empty($clean['comment'])) {
            $fu->addErr('The comment is blank!');
        }

        if ($fu->noErr()) {

            $sql_name = $fu->db->Escape($clean['name']);
            $sql_email = $fu->db->Escape($clean['email']);
            $sql_url = $fu->db->Escape($clean['url']);
            $sql_comment = $fu->db->Escape($clean['comment']);
            $sql_added = $fu->db->Escape($clean['added']);
            $sql_id = (int)$clean['id'];
            $sql_approved = ($clean['approved'] == 1) ? 1 : 0;

// _____________________________________________ UPDATE QUERY

            if ($_POST['action'] == 'update') {

                $query = 'UPDATE ' .$fu->getOpt('comments_table')."
                  SET name='$sql_name', email='$sql_email', url='$sql_url',
                  comment='$sql_comment', approved=$sql_approved, added='$sql_added'
                  WHERE comment_id=".$sql_id;

                if ($fu->db->Execute($query)) {
                    $fu->addSuccess('Comment #<strong>'.$sql_id.'</strong> updated.');
                    $clean = array();
                    $showform = false;
                }
            }
        }

// _____________________________________________ APPROVE QUERY

    } elseif ($_POST['action'] == 'approve') {

        $sql_id = (int)$_POST['id'];

        $query = 'UPDATE ' .$fu->getOpt('comments_table'). ' SET approved=1 WHERE comment_id=' .$sql_id;

        if ($fu->db->Execute($query)) {
            if (isset($_POST['mode']) && $_POST['mode'] == 'ajax') {
                echo '<img src="css/tick.png" alt="Yes" />';
                exit;
            }
            $fu->addSuccess('Comment #<strong>'.$sql_id.'</strong> approved.');
            $clean = array();
            $showform = false;
        }

// _____________________________________________ DELETE QUERY

    } elseif ($_POST['action'] == 'delete') {
	
		if ($_POST['id'] == 'spam') {
			$query = 'DELETE FROM ' .$fu->getOpt('comments_table'). ' WHERE approved=0 AND points < ' .$fu->getOpt('points_pending_threshold', true);
		} else {
        	$sql_id = (int)$_POST['id'];
        	$query = 'DELETE FROM ' .$fu->getOpt('comments_table'). ' WHERE comment_id=' .$sql_id;
		}

        if ($fu->db->Execute($query)) {
            if (isset($_POST['mode']) && $_POST['mode'] == 'ajax') {
                echo 'SUCCESS';
                exit;
            }
			if ($_POST['id'] == 'spam') {
				$fu->addSuccess('All spam comments deleted.');
			} else {
            	$fu->addSuccess('Comment #<strong>'.$sql_id.'</strong> deleted.');
			}
            $clean = array();
            $showform = false;
        }
    }
}

$fu->getHeader('Comments');

?>

<h2>Comments<?php

if ($showform && $cmt->getID()) {
    echo ': Edit';
} elseif (!empty($_GET['show'])) {
	echo ': '.ucwords(clean_input($_GET['show']));
}

?></h2>

<ul class="subnav">
<li><a href="comment.php">Approved</a></li>
<li><a href="comment.php?show=pending">Pending</a></li>
<li><a href="comment.php?show=spam">Spam</a></li>
<li class="form"><form action="comment.php" method="get">
<input type="text" name="search" value="<?php if (!empty($_GET['search'])) { echo clean_input($_GET['search']); } ?>" accesskey="q" />
<input type="submit" value="Search" class="button" title="search comments, accesskey q" />
</form></li>
</ul>

<?php

// _____________________________________________ REPORT SUCCESS

$fu->reportSuccess();

// _____________________________________________ REPORT ERRORS

$fu->reportErrors();

// _____________________________________________ PREVIEW

if (isset($_POST['action']) && $_POST['action'] == 'preview') {

?>

<div id="preview">
<h3>Preview</h3>
<div>
<?php echo $cmt->printComment(); ?>
</div>
</div><!-- END #preview -->

<?php

}

// _____________________________________________ ADD/EDIT FORM

if ($showform) {

?>

<form action="comment.php" method="post">

<div class="col1">
<p><textarea id="comment" class="wysiwyg" name="comment" cols="80" rows="18"><?php echo htmlspecialchars($cmt->getBody()); ?></textarea></p>
</div>

<div class="col2">
<fieldset style="background: url(<?php echo $cmt->getGravatarUrl(); ?>) right center no-repeat;">
<legend>Contact Info</legend>

<p><label for="name">N:</label>
<input type="text" id="name" name="name" maxlength="30" size="25"
value="<?php echo $cmt->getName(); ?>" /></p>

<p><label for="email">E:</label>
<input type="text" id="email" name="email" maxlength="100" size="25"
value="<?php echo $cmt->getEmail(); ?>" /></p>

<p><label for="url">U:</label>
<input type="text" id="url" name="url" maxlength="100" size="25"
value="<?php echo $cmt->getUrl(); ?>" /></p>

</fieldset>

<fieldset id="comment-meta">
<legend>Meta</legend>

<p><label for="added">Date:</label>
<input type="text" id="added" name="added" maxlength="20" size="18"
value="<?php echo $cmt->getDateFormatted('Y-m-d g:i a'); ?>" /></p>

<p><input type="checkbox" id="approved" name="approved" value="1"
<?php if ($cmt->isApproved()) { echo ' checked="checked"'; } ?> />
<label for="approved" class="checkbox">Approved</label></p>

</fieldset>
</div>

<div class="col1">
<fieldset id="action">
<legend>Action</legend>
<div class="primary">
<input type="submit" name="action" value="preview" class="button" title="Preview changes, accesskey p" accesskey="p" />
<input type="hidden" name="id" value="<?php echo $cmt->getID(); ?>" />
<input type="submit" name="action" value="update" class="update" title="Save changes, accesskey s" accesskey="s" />
</div>
<div class="secondary">
<input type="submit" name="action" value="delete" class="delete" title="Delete this comment, accesskey x" accesskey="x" />
</div>
</fieldset>
</div>

</form>

<?php

// _____________________________________________ LIST ALL

} else {

?>

<script type="text/javascript" src="js/fanupdate-comment-admin.js"></script>

<?php

$query = 'SELECT c.*
  FROM ' .$fu->getOpt('comments_table'). ' c
  LEFT JOIN ' .$fu->getOpt('blog_table'). ' b ON c.entry_id=b.entry_id';

if (!empty($_GET['search'])) {
    $q = $fu->db->Escape(clean_input($_GET['search']));
    $query .= " WHERE MATCH (c.name, c.comment) AGAINST ('$q' IN BOOLEAN MODE)";
} elseif (isset($_GET['show']) && $_GET['show'] == 'pending') {
	$query .= ' WHERE c.approved=0 AND c.points >= ' .$fu->getOpt('points_pending_threshold', true);
} elseif (isset($_GET['show']) && $_GET['show'] == 'spam') {
	$query .= ' WHERE c.approved=0 AND c.points < ' .$fu->getOpt('points_pending_threshold', true);
} elseif (!empty($_GET['entry'])) {
	$id = (int)$_GET['entry'];
	$query .= " WHERE b.entry_id=$id";
} else {
	$query .= ' WHERE c.approved>0';
}

$query .= ' ORDER BY c.added DESC';

$fu->db->ExecutePaginate($query, $fu->getOpt('num_per_page'));

if ($fu->db->NumRows() > 0) {
	
	if (isset($_GET['show']) && $_GET['show'] == 'spam') {
		
?>
<form action="comment.php" method="post">
<p><input type="submit" value="Delete All Spam" class="delete noajax" title="Delete all spam comments" />
<input type="hidden" name="action" value="delete" />
<input type="hidden" name="id" value="spam" /></p>
</form>
<?php
		
	}

?>

<table class="sortable" summary="Table of comments, newest first.">

<thead>
<tr>
<th scope="col">Date</th>
<th scope="col">Person</th>
<th scope="col">Comment</th>
<?php if ($fu->getOpt('points_scoring')) { ?>
<th scope="col">Points</th>
<?php } ?>
<th scope="col">Approved?</th>
<th scope="col">Edit</th>
<th scope="col">Delete</th>
</tr>
</thead>

<tbody>
<?php

while ($row = $fu->db->ReadRecordPaginate()) {

    $cmt = new FanUpdate_Comment($row, $fu);

    $class = (isset($class) && $class == 'even') ? 'odd' : 'even';

    echo '<tr class="'.$class.'">'."\n";
    echo '<td class="date">'.$cmt->getDateFormatted().'</td>'."\n";
    echo '<td>'.$cmt->getGravatar(21).' '.$cmt->getCommenterLink().'</td>'."\n";
    echo '<td>'.$cmt->getAbstract(10, true).'</td>'."\n";
	if ($fu->getOpt('points_scoring')) {
		echo '<td class="number">'.$cmt->getPoints().'</td>'."\n";
	}
    if ($row['approved'] > 0) {
        $img = 'tick.png';
        $alt = 'Yes';
        $img_class = '';
    } else {
        $img = 'cross.png';
        $alt = 'No';
        $img_class = 'approve_comment';
    }
    $img_id = 'ci_'.$cmt->getID();
    echo '<td id="c'.$cmt->getID().'"><img src="css/'.$img.'" alt="'.$alt.'" class="'.$img_class.'" id="'.$img_id.'" /></td>'."\n";
    echo '<td class="form"><form action="comment.php" method="get">';
    echo '<input type="submit" value="edit" class="edit" title="Edit comment &#8216;'.$cmt->getAbstract().'&#8217;" />';
    echo '<input type="hidden" name="id" value="'.$cmt->getID().'" />';
    echo '</form></td>';
    echo '<td class="form"><form action="comment.php" method="post">';
    echo '<input type="submit" name="action" value="delete" class="delete" title="Delete comment &#8216;'.$cmt->getAbstract().'&#8217;" />';
    echo '<input type="hidden" name="id" value="'.$cmt->getID().'" />';
    echo '</form></td>'."\n";
    echo "</tr>\n";
}

$fu->db->FreeResult();

echo "</tbody>\n";
echo "</table>\n";

echo '<p class="paginate">';
$fu->db->PrintPaginate();
echo "</p><!-- END .paginate -->\n";

} else {
    echo "<p>No comments found.</p>\n";
}

}

$fu->getFooter();

?>
