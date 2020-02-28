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
$cat_array = array();
$showform = false;

// _____________________________________________ GET MySQL DATA

if (isset($_GET['action']) && $_GET['action'] == 'new') {

    $post = new FanUpdate_Post(array(), $fu, $fu->getOpt('blog_page'));

    $showform = true;

} elseif (!empty($_GET['id'])) {

    $sql_id = (int)$_GET['id'];

    $query = 'SELECT * FROM ' . $fu->getOpt('blog_table') . ' WHERE entry_id=' . $sql_id . ' LIMIT 1';
    $fu->db->Execute($query);

    $row = $fu->db->GetRecord();

    $post = new FanUpdate_Post($row, $fu, $fu->getOpt('blog_page'));
    $post->getCatFromDb();

    $showform = true;

// _____________________________________________ CLEAN POST DATA

} elseif (isset($_POST['action'])) {

    $clean = clean_input($_POST);
    $clean['body'] = clean_input($_POST['body'], true);

    if (isset($clean['date_to_now']) || empty($clean['added'])) {
        $clean['added'] = gmdate('Y-m-d H:i:s');
    } else {
        $clean['added'] = gmdate('Y-m-d H:i:s', strtotime($clean['added']));
    }

    if (!isset($clean['is_public'])) {
        $clean['is_public'] = 0;
    }
    if (!isset($clean['comments_on'])) {
        $clean['comments_on'] = 0;
    }

    $clean['entry_id'] = $clean['id'];

    $post = new FanUpdate_Post($clean, $fu, $fu->getOpt('blog_page'));

// _____________________________________________ VALIDATE POST DATA

    if ($_POST['action'] == 'add' || $_POST['action'] == 'update' || $_POST['action'] == 'preview') {

        $showform = true;

        if (!is_array($clean['cat']) && empty($clean['new_cat'])) {
            $fu->addErr('The category is empty!');
        } elseif (!is_array($clean['cat'])) {
            $clean['cat'] = array();
        } else {
            $post->getCatFromDb($clean['cat']);
        }

        if (!is_array($clean['new_cat'])) {
            $clean['new_cat'] = array();
        }

        if (empty($clean['title'])) {
            $fu->addErr('The title is blank!');
        }

        if (empty($clean['body'])) {
            $fu->addErr('The entry is blank!');
        }

        if ($fu->noErr()) {

            foreach ($clean['new_cat'] as $val) {
                $val = clean_input($val);
                if (!empty($val)) {
                    $sql_val = $fu->db->Escape($val);

                    $query = 'INSERT INTO ' . $fu->getOpt('collective_table') . ' (' . $fu->getOpt('col_subj') . ")
	                  VALUES ('$sql_val')";

                    if ($fu->db->Execute($query)) {

                        $fu->addSuccess('Category <strong>' . $val . '</strong> added.');
                        $sql_id = mysql_insert_id();
                        $clean['cat'][] = $sql_id;
                        $post->addCategory($sql_id, $val);

                        $query = 'INSERT INTO ' . $fu->getOpt('catoptions_table') . " (cat_id)
	                      VALUEs ($sql_id)";

                        $fu->db->Execute($query);
                    }
                }
            }
            $clean['new_cat'] = array();

            $sql_title = $fu->db->Escape($clean['title']);
            $sql_body = $fu->db->Escape($clean['body']);
            $sql_added = $fu->db->Escape($clean['added']);
            $sql_is_public = ($clean['is_public'] == 1) ? 1 : 0;
            $sql_comments_on = ($clean['comments_on'] == 1) ? 1 : 0;

// _____________________________________________ ADD QUERY

            if ($_POST['action'] == 'add') {

                $query = 'INSERT INTO ' . $fu->getOpt('blog_table') . " (title, body, is_public, comments_on, added)
                  VALUES ('$sql_title', '$sql_body', $sql_is_public, $sql_comments_on, '$sql_added')";

                if ($fu->db->Execute($query)) {
                    $sql_id = mysql_insert_id();
                    $fu->addSuccess('Entry <strong>' . $post->getTitle() . '</strong> added.');
                    $showform = false;

                    $num_success = 0;

                    foreach ($clean['cat'] as $cat_id) {
                        $sql_cat_id = (int)$cat_id;

                        $query_cat = 'INSERT INTO ' . $fu->getOpt('catjoin_table') . " (entry_id, cat_id)
                          VALUES ($sql_id, $sql_cat_id)";

                        if ($fu->db->Execute($query_cat)) {
                            $num_success++;
                        }
                    }

                    if ($num_success > 0) {
                        $fu->addSuccess('<strong>' . $num_success . '</strong> categories added.');
                    }

                    $clean = array();
                }

// _____________________________________________ UPDATE QUERY

            } elseif ($_POST['action'] == 'update') {

                $sql_id = (int)$clean['id'];

                $query = 'UPDATE ' . $fu->getOpt('blog_table') . "
                  SET title='$sql_title', body='$sql_body',
                  is_public=$sql_is_public, comments_on=$sql_comments_on,
                  added='$sql_added'
                  WHERE entry_id=" . $sql_id;

                if ($fu->db->Execute($query)) {
                    $fu->addSuccess('Entry <strong>' . $post->getTitle() . '</strong> updated.');

                    $cat_array = array();

                    if (!is_array($clean['cat'])) {
                        $clean['cat'] = array();
                    }

                    $query_old_cat = 'SELECT cat_id FROM ' . $fu->getOpt('catjoin_table') . ' WHERE entry_id=' . $sql_id;
                    $fu->db->Execute($query_old_cat);
                    $num_old_cat = $fu->db->NumRows();

                    if ($num_old_cat > 0) {

                        $preserve_cat = array();

                        $num_success = 0;

                        while ($row_old_cat = $fu->db->ReadRecord()) {

                            if (!in_array($row_old_cat['cat_id'], $clean['cat'], true)) {

                                $sql_old_cat = (int)$row_old_cat['cat_id'];

                                $query_delete_cat = 'DELETE FROM ' . $fu->getOpt('catjoin_table') . '
                                  WHERE entry_id=' . $sql_id . ' AND cat_id=' . $sql_old_cat;

                                if ($fu->db->Execute($query_delete_cat)) {
                                    $num_success++;
                                }

                            } else {
                                $preserve_cat[] = $row_old_cat['cat_id'];
                            }
                        }

                        if ($num_success > 0) {
                            $fu->addSuccess('<strong>' . $num_success . '</strong> old categories removed.');
                        }

                        $cat_array = array_diff($clean['cat'], $preserve_cat);

                    } else {
                        $cat_array = $clean['cat'];
                    }

                    $num_success = 0;

                    foreach ($cat_array as $cat_id) {
                        $sql_cat_id = (int)$cat_id;

                        $query_cat = 'INSERT INTO ' . $fu->getOpt('catjoin_table') . " (entry_id, cat_id) VALUES ($sql_id, $sql_cat_id)";

                        if ($fu->db->Execute($query_cat)) {
                            $num_success++;
                        }
                    }

                    if ($num_success > 0) {
                        $fu->addSuccess('<strong>' . $num_success . '</strong> categories added.');
                    }

                    $showform = false;
                    $clean = array();
                }
            }
        }

// _____________________________________________ DELETE QUERY

    } elseif ($_POST['action'] == 'delete') {

        $sql_id = (int)$clean['id'];

        $query = 'DELETE FROM ' . $fu->getOpt('blog_table') . " WHERE entry_id=$sql_id";

        if ($fu->db->Execute($query)) {

            $query_cat = 'DELETE FROM ' . $fu->getOpt('catjoin_table') . " WHERE entry_id=$sql_id";

            $fu->db->Execute($query_cat);

            if (isset($_POST['mode']) && $_POST['mode'] == 'ajax') {
                echo 'SUCCESS';
                exit;
            }

            $fu->addSuccess('Entry #<strong>' . $post->getID() . '</strong> deleted.');
            $fu->addSuccess('All <strong>' . $fu->db->AffectedRows() . '</strong> categories unregistered.');

            $clean = array();
        }
    }
}

$fu->getHeader('Entries');

?>

<h2>Entries<?php

    if ($showform && $post->getID()) {
        echo ': Edit';
    } elseif ($showform) {
        echo ': New';
    }

    ?></h2>

<ul class="subnav">
    <li><a href="blog.php?action=new" accesskey="n" title="new entry, accesskey n">New Entry</a></li>
    <li class="form">
        <form action="blog.php" method="get">
            <input type="text" name="search" value="<?php if (!empty($_GET['search'])) {
                echo clean_input($_GET['search']);
            } ?>" accesskey="q"/>
            <select name="cat">
                <option value="">All</option>
                <?php

                $query = 'SELECT ' . $fu->getOpt('col_id') . ' AS cat_id, ' . $fu->getOpt('col_subj') . ' AS cat_name
  FROM ' . $fu->getOpt('collective_table') . '
  ORDER BY cat_name ASC';

                $fu->db->Execute($query);

                if ($fu->db->NumRows() > 0) {
                    while ($row = $fu->db->ReadRecord()) {
                        echo '<option value="' . $row['cat_id'] . '"';
                        if (isset($_GET['cat']) && $_GET['cat'] == $row['cat_id']) {
                            echo ' selected="selected"';
                        }
                        echo '>' . $row['cat_name'] . "</option>\n";
                    }
                }

                ?>
                <option value="0">Whole Collective</option>
            </select>
            <input type="submit" value="Search" class="button" title="seach entries, accesskey q"/>
        </form>
    </li>
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
            <?php $post->printPost(); ?>
        </div>
    </div><!-- END #preview -->

    <?php

}

// _____________________________________________ ADD/EDIT FORM

if ($showform) {

    ?>

    <form action="blog.php" method="post">

        <div class="col1">

            <p class="title"><label for="title">Title:</label>
                <input type="text" id="title" name="title" size="35" maxlength="50"
                       value="<?php echo $post->getTitle(); ?>"/></p>

            <p><textarea id="body" class="wysiwyg" name="body" cols="80"
                         rows="18"><?php echo htmlspecialchars($post->getBody()); ?></textarea></p>

        </div><!-- END .col1 -->

        <div class="col2" id="entry-meta">
            <fieldset>
                <legend>Meta</legend>

                <p><label for="added">Date:</label>
                    <input type="text" id="added" name="added" maxlength="50" size="18"
                           value="<?php echo $post->getDateFormatted('Y-m-d g:i a'); ?>"/>

                    <span><input type="checkbox" id="now" name="date_to_now" value="y"
<?php if (!$post->getID() || (!empty($clean['date_to_now']) && $clean['date_to_now'] == 'y')) {
    echo ' checked="checked"';
} ?> />
<label for="now" class="checkbox">NOW</label></span></p>

                <p><input type="checkbox" id="is_public" name="is_public" value="1"
                        <?php if ($post->isPublic()) {
                            echo ' checked="checked"';
                        } ?> />
                    <label for="is_public" class="checkbox">Published</label></p>

                <p><input type="checkbox" id="comments_on" name="comments_on" value="1"
                        <?php if ($post->commentsOn()) {
                            echo ' checked="checked"';
                        } ?> />
                    <label for="comments_on" class="checkbox">Comments allowed</label></p>

            </fieldset>

            <fieldset>
                <legend>Category</legend>

                <ul id="categories" class="catlist select_all">
                    <?php if ($fu->getOpt('collective_script')) { ?>
                        <li><input type="checkbox" id="cat0" name="cat[]" value="0"
                                <?php if (isset($clean['cat']) && in_array(0, $clean['cat'], true)) {
                                    echo ' checked="checked"';
                                } ?> />
                            <label for="cat0" class="checkbox">Collective</label></li>
                    <?php }

                    $query_cat = 'SELECT ' . $fu->getOpt('col_id') . ' AS cat_id, ' . $fu->getOpt('col_subj') . ' AS cat_name
  FROM ' . $fu->getOpt('collective_table') . '
  ORDER BY cat_name ASC';

                    $fu->db->Execute($query_cat);

                    if ($fu->db->NumRows() > 0) {
                        $i = 1;
                        while ($row_cat = $fu->db->ReadRecord()) {

                            echo '<li><input type="checkbox" id="cat' . $i . '" name="cat[]" value="' . $row_cat['cat_id'] . '"';
                            if (array_key_exists($row_cat['cat_id'], $post->getCategoryArray())) {
                                echo ' checked="checked"';
                            }
                            echo " />\n";
                            echo '<label for="cat' . $i . '" class="checkbox">' . $row_cat['cat_name'] . "</label></li>\n";

                            $i++;
                        }
                    }
                    $fu->db->FreeResult();

                    if (!empty($clean['new_cat'])) {
                        foreach ($clean['new_cat'] as $val) {
                            echo '<li><input type="text" name="new_cat[]" value="' . $val . '" /></li>' . "\n";
                        }
                    }

                    ?>
                </ul>

                <script type="text/javascript" src="js/select-all.js"></script>

            </fieldset>
        </div><!-- END .col2 -->

        <div class="col1">
            <fieldset id="action">
                <legend>Action</legend>
                <div class="primary">
                    <input type="submit" name="action" value="preview" class="button"
                           title="Preview changes, accesskey p" accesskey="p"/>
                    <?php if ($post->getID()) { ?>
                    <input type="hidden" name="id" value="<?php echo $post->getID(); ?>"/>
                    <input type="submit" name="action" value="update" class="update" title="Save changes, accesskey s"
                           accesskey="s"/>
                </div>
                <div class="secondary">
                    <input type="submit" name="action" value="delete" class="delete"
                           title="Delete this entry, accesskey x" accesskey="x"/>
                    <?php } else { ?>
                        <input type="submit" name="action" value="add" class="add" title="Add this entry, accesskey s"
                               accesskey="s"/>
                    <?php } ?>
                </div>
            </fieldset>
        </div>

    </form>

    <?php

// _____________________________________________ LIST ALL

} else {

?>

<script type="text/javascript" src="js/fanupdate-post-admin.js"></script>

<?php

$query = 'SELECT b.entry_id, b.title, b.is_public, b.comments_on, b.added, COUNT(DISTINCT com.comment_id) AS num_comments
FROM ' . $fu->getOpt('blog_table') . ' b
LEFT JOIN ' . $fu->getOpt('catjoin_table') . ' j ON b.entry_id=j.entry_id
LEFT JOIN ' . $fu->getOpt('comments_table') . ' com ON b.entry_id=com.entry_id';

if (isset($_GET['search'])) {
    $q = $fu->db->Escape(clean_input($_GET['search']));
}
unset($cat);
if (isset($_GET['cat']) && $_GET['cat'] !== '') {
    $cat = (int)$_GET['cat'];
}
if (isset($cat)) {
    $query .= ' JOIN ' . $fu->getOpt('catjoin_table') . ' cat ON b.entry_id=cat.entry_id';
}
if (!empty($q) || isset($cat)) {
    $query .= ' WHERE ';
    if (!empty($q)) {
        $query .= " MATCH (title,body) AGAINST ('$q' IN BOOLEAN MODE)";
        if (isset($cat)) {
            $query .= ' AND';
        }
    }
    if (isset($cat)) {
        $query .= " j.cat_id=$cat";
    }
}

$query .= ' GROUP BY b.entry_id ORDER BY b.added DESC';

$fu->db->ExecutePaginate($query, $fu->getOpt('num_per_page'));

if ($fu->db->NumRows() > 0) {

?>

<table class="sortable" summary="Table of blog entries, newest first.">

    <thead>
    <tr>
        <th scope="col">Date</th>
        <th scope="col">Title</th>
        <th scope="col">Category</th>
        <th scope="col" colspan="2">Comments</th>
        <th scope="col">Published</th>
        <th scope="col">Edit</th>
        <th scope="col">Delete</th>
    </tr>
    </thead>

    <tbody>
    <?php

    while ($row = $fu->db->ReadRecordPaginate()) {

        $post = new FanUpdate_Post($row, $fu);
        $post->getCatFromDb();

        $class = (isset($class) && $class == 'even') ? 'odd' : 'even';

        echo '<tr class="' . $class . '">';
        echo '<td class="date">' . $post->getDateFormatted() . '</td>';
        echo '<td>' . $post->getTitle() . '</td>';
        echo '<td>' . $post->getCategoryString() . '</td>';
        if ($post->commentsOn()) {
            $img = 'tick.png';
            $alt = 'Yes';
            $img_title = 'Comments allowed';
        } else {
            $img = 'cross.png';
            $alt = 'No';
            $img_title = 'Comments not allowed';
        }
        echo '<td class="number"><a href="comment.php?entry=' . $post->getID() . '" title="see comments">' . $row['num_comments'] . '</a></td>';
        echo '<td><img src="css/' . $img . '" alt="' . $alt . '" title="' . $img_title . '" /></td>';
        if ($post->isPublic()) {
            $img = 'tick.png';
            $alt = 'Yes';
            $img_title = 'Published';
        } else {
            $img = 'cross.png';
            $alt = 'No';
            $img_title = 'Draft';
        }
        echo '<td><img src="css/' . $img . '" alt="' . $alt . '" title="' . $img_title . '" /></td>';
        echo '<td class="form"><form action="blog.php" method="get">';
        echo '<input type="submit" value="edit" class="edit" title="Edit entry &#8216;' . $post->getTitle() . '&#8217;" />';
        echo '<input type="hidden" name="id" value="' . $post->getID() . '" />';
        echo '</form></td>';
        echo '<td class="form"><form action="blog.php" method="post">';
        echo '<input type="submit" name="action" value="delete" class="delete" title="Delete entry &#8216;' . $post->getTitle() . '&#8217;" />';
        echo '<input type="hidden" name="id" value="' . $post->getID() . '" />';
        echo '</form></td>';
        echo "</tr>\n";
    }

    $fu->db->FreeResult();

    echo "</tbody>\n";

    echo "</table>\n";

    echo '<p class="paginate">';
    $fu->db->PrintPaginate();
    echo "</p><!-- END .paginate -->\n";

    } else {
        echo "<p>No entries found.</p>\n";
    }

    }

    $fu->getFooter();

    ?>
