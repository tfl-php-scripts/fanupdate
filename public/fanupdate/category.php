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

if (isset($_GET['action']) && $_GET['action'] == 'new') {

    $showform = true;

} elseif (isset($_GET['action']) && $_GET['action'] == 'edit') {

    $sql_id = (int)$_GET['id'];

    $query = 'SELECT c.' .$fu->getOpt('col_subj'). ' AS cat_name, co.*
      FROM ' .$fu->getOpt('collective_table'). ' c
      LEFT JOIN ' .$fu->getOpt('catoptions_table'). ' co ON c.' .$fu->getOpt('col_id'). '=co.cat_id
      WHERE c.' .$fu->getOpt('col_id'). '=' .$sql_id. ' LIMIT 1';

    $fu->db->Execute($query);

    $clean = $fu->db->GetRecord();
 
    $showform = true;

// _____________________________________________ CLEAN POST DATA

} elseif (isset($_POST['action'])) {

	$clean = clean_input($_POST);
 	$clean['comment_template'] = clean_input($_POST['comment_template'], true);
	$clean['entry_template'] = clean_input($_POST['entry_template'], true);

    if (!isset($clean['gravatar_on'])) {
        $clean['gravatar_on'] = 0;
    }
    if (!isset($clean['comments_on'])) {
        $clean['comments_on'] = 0;
    }

// _____________________________________________ VALIDATE POST DATA

    if ($_POST['action'] == 'add' || $_POST['action'] == 'update' || $_POST['action'] == 'preview') {

        $showform = true;

        if (!$fu->getOpt('collective_script')) {
        if (empty($clean['cat_name'])) {
            $fu->addErr('The category name is blank!');
        } else {

            $sql_cat_name = $fu->db->Escape($clean['cat_name']);
            $sql_id = 0;
            if (!empty($clean['cat_id'])) {
                $sql_id = (int)$clean['cat_id'];
            }

            $query_check = 'SELECT COUNT(' .$fu->getOpt('col_id'). ')
              FROM ' .$fu->getOpt('collective_table'). '
              WHERE ' .$fu->getOpt('col_subj')."='$sql_cat_name'
              AND ".$fu->getOpt('col_id')."!=$sql_id";

            $num_check = $fu->db->GetFirstCell($query_check);

            if ($num_check > 0) {
                $fu->addErr('The fanlisting <strong>'.$clean['cat_name'].'</strong> already exists! Please choose another name.');
            }
        }
        }

        if ($fu->noErr()) {

            $sql_cat_name = $fu->db->Escape($clean['cat_name']);
            $sql_entry_template = $fu->db->Escape($clean['entry_template']);
            $sql_comment_template = $fu->db->Escape($clean['comment_template']);
            $sql_date_format = $fu->db->Escape($clean['date_format']);
            $sql_gravatar_default = $fu->db->Escape($clean['gravatar_default']);
            $sql_gravatar_size = $fu->db->Escape($clean['gravatar_size']);
            $sql_gravatar_rating = $fu->db->Escape($clean['gravatar_rating']);
            $sql_gravatar_on = ($clean['gravatar_on'] == 1) ? 1 : 0;
            $sql_comments_on = ($clean['comments_on'] == 1) ? 1 : 0;

// _____________________________________________ ADD QUERY

            if ($_POST['action'] == 'add') {

                $query = 'INSERT INTO ' .$fu->getOpt('collective_table'). ' (' .$fu->getOpt('col_subj').")
                  VALUES ('$sql_cat_name')";

                if ($fu->db->Execute($query)) {

                    $showform = false;
                    $fu->addSuccess('Category <strong>'.$clean['cat_name'].'</strong> added.');
                    $sql_id = mysql_insert_id();

                    $query = 'INSERT INTO ' .$fu->getOpt('catoptions_table')." (cat_id, comments_on,
                      date_format, gravatar_on, gravatar_default, gravatar_size, gravatar_rating,
                      entry_template, comment_template)
                      VALUEs ($sql_id, $sql_comments_on, '$sql_date_format', $sql_gravatar_on,
                      '$sql_gravatar_default', '$sql_gravatar_size', '$sql_gravatar_rating',
                      '$sql_entry_template', '$sql_comment_template')";

                    if ($fu->db->Execute($query)) {
                        $fu->addSuccess('Category options added.');
                    }

                    $clean = array();
                }

// _____________________________________________ UPDATE QUERY

            } elseif ($_POST['action'] == 'update') {

                $sql_id = (int)$clean['cat_id'];

                if (!$fu->getOpt('collective_script')) {

                    $query = 'UPDATE ' .$fu->getOpt('collective_table'). '
                      SET ' .$fu->getOpt('col_subj')."='$sql_cat_name'
                      WHERE ".$fu->getOpt('col_id'). '=' .$sql_id;

                    if ($fu->db->Execute($query)) {
                        $fu->addSuccess('Category <strong>'.$clean['cat_name'].'</strong> updated.');
                    }
                }

                $query = 'UPDATE ' .$fu->getOpt('catoptions_table')."
                  SET comments_on=$sql_comments_on, date_format='$sql_date_format',
                  gravatar_on=$sql_gravatar_on, gravatar_default='$sql_gravatar_default',
                  gravatar_size='$sql_gravatar_size', gravatar_rating='$sql_gravatar_rating',
                  entry_template='$sql_entry_template', comment_template='$sql_comment_template'
                  WHERE cat_id=$sql_id";

                if ($fu->db->Execute($query)) {
                    $fu->addSuccess('Category options updated.');
                    $showform = false;
                    $clean = array();
                }
            }
        }


// _____________________________________________ DELETE QUERY

    } elseif ($_POST['action'] == 'delete') {

        $sql_id = (int)$clean['cat_id'];

        $query = 'DELETE FROM ' .$fu->getOpt('collective_table'). ' WHERE ' .$fu->getOpt('col_id')."=$sql_id";

        if ($fu->db->Execute($query)) {

            $fu->addSuccess('Category #<strong>'.$clean['cat_id'].'</strong> deleted.');

            $query_cat = 'DELETE FROM ' .$fu->getOpt('catoptions_table')." WHERE cat_id=$sql_id";

            if ($fu->db->Execute($query_cat)) {
                $fu->addSuccess('Category #<strong>'.$clean['cat_id'].'</strong> options deleted.');
            }

            $query_post = 'DELETE FROM ' .$fu->getOpt('catjoin_table')." WHERE cat_id=$sql_id";

            if ($fu->db->Execute($query_post)) {
                $fu->addSuccess('Category #<strong>'.$clean['cat_id'].'</strong> post relationships deleted.');
            }

            $clean = array();
        }
    }
}

$fu->getHeader('Categories');

echo '<h2>Categories';

if ($showform && isset($clean['cat_id'])) {
    echo ': Edit';
} elseif ($showform) {
    echo ': New';
}

echo "</h2>\n";

if (!$fu->getOpt('collective_script')) { ?>
<ul class="subnav">
<li><a href="category.php?action=new" title="new category, accesskey n" accesskey="n">New Category</a></li>
</ul>
<?php

}

// _____________________________________________ REPORT SUCCESS

$fu->reportSuccess();

// _____________________________________________ REPORT ERRORS

$fu->reportErrors();

// _____________________________________________ ADD/EDIT FORM

if ($showform) {

?>

<form action="category.php" method="post">

<div class="col1">
<p class="title"><label for="name">Category name:</label>
<?php if (!$fu->getOpt('collective_script')) { ?>
<input type="text" id="name" name="cat_name" size="40" maxlength="50"
 value="<?php if (!empty($clean['cat_name'])) { echo $clean['cat_name']; } ?>" />
<?php } else { echo '<strong>'.$clean['cat_name'].'</strong> (visit collective script admin panel to rename)'; } ?></p>
</div><!-- END .col1 -->

<div class="col2">
<p>See <a href="docs/readme.txt">readme.txt</a> for template variable explanation. Leave any setting blank to use default values.</p>
</div><!-- END .col2 -->

<div class="col2">
<fieldset>
<legend>Category Options</legend>

<p><label for="comments_on">Comments?</label>
<input type="checkbox" id="comments_on" name="comments_on" value="1"
<?php if (!isset($clean['comments_on']) || $clean['comments_on'] == 1) { echo 'checked="checked"'; } ?> /></p>

<p><label for="date_format">Date format:</label>
<input type="text" id="date_format" name="date_format" size="10" maxlength="30"
 value="<?php if (!empty($clean['date_format'])) { echo $clean['date_format']; } ?>" /></p>

</fieldset>

<fieldset>
<legend>Gravatar Options</legend>

<p><label for="gravatar_on">On?</label>
<input type="checkbox" id="gravatar_on" name="gravatar_on" value="1"
<?php if (!isset($clean['gravatar_on']) || $clean['gravatar_on'] == 1) { echo 'checked="checked"'; } ?> /></p>

<p><label for="gravatar_default">Default img:</label>
<input type="text" id="gravatar_default" name="gravatar_default" size="20" maxlength="100"
 value="<?php if (!empty($clean['gravatar_default'])) { echo $clean['gravatar_default']; } ?>" /></p>

<p><label for="gravatar_size">Size:</label>
<input type="text" id="gravatar_size" name="gravatar_size" size="2" maxlength="2"
 value="<?php if (!empty($clean['gravatar_size'])) { echo $clean['gravatar_size']; } ?>" /></p>

<p><label for="gravatar_rating">Rating:</label>
<input type="text" id="gravatar_rating" name="gravatar_rating" size="2" maxlength="2"
 value="<?php if (!empty($clean['gravatar_rating'])) { echo $clean['gravatar_rating']; } ?>" /></p>

</fieldset>

</div><!-- END .col2 -->

<div class="col1">
<fieldset>
<legend>Entry Template</legend>
<p><textarea id="template" name="entry_template" cols="80" rows="6"><?php if (!empty($clean['entry_template'])) { echo htmlspecialchars($clean['entry_template']); } ?></textarea></p>
</fieldset>

<fieldset>
<legend>Comment Template</legend>
<p><textarea id="template_c" name="comment_template" cols="80" rows="6"><?php if (!empty($clean['comment_template'])) { echo htmlspecialchars($clean['comment_template']); } ?></textarea></p>
</fieldset>
</div><!-- End .col1 -->

<div class="col1">
<fieldset id="action">
<legend>Action</legend>
<div class="primary">
<?php if (isset($clean['cat_id'])) { ?>
<input type="hidden" name="cat_id" value="<?php echo $clean['cat_id']; ?>" />
<input type="submit" name="action" value="update" class="update" title="Save changes, accesskey s" accesskey="s" />
<?php if (!$fu->getOpt('collective_script')) { ?>
</div>
<div class="secondary">
<input type="submit" name="action" value="delete" class="delete" title="Delete this category, accesskey x" accesskey="x" />
<?php } ?>
<?php } else { ?>
<input type="submit" name="action" value="add" class="add" title="Add this category, accesskey s" accesskey="s"  />
<?php } ?>
</div>
</fieldset>
</div><!-- END .col1 -->

</form>

<?php

} else {

// ____________________________________________________________ LIST Categories

$query = 'SELECT ' .$fu->getOpt('collective_table'). '.*,
  ' .$fu->getOpt('col_subj'). ' AS cname, ' .$fu->getOpt('col_id'). ' AS cid, COUNT(cat_id) AS num
  FROM ' .$fu->getOpt('collective_table'). '
  LEFT JOIN ' .$fu->getOpt('catjoin_table'). ' ON ' .$fu->getOpt('col_id'). '=cat_id
  GROUP BY ' .$fu->getOpt('col_id'). '
  ORDER BY cname ASC';

$fu->db->Execute($query);

$num_cat = $fu->db->NumRows();

if ($num_cat > 0) {

?>

<table class="sortable" summary="Table of blog entry categories, sorted alphabetically.">

<thead>
<tr>
<th scope="col">Name</th>
<th scope="col">Entries</th>
<th scope="col">Get Code</th>
<th scope="col">Edit</th>
<?php if (!$fu->getOpt('collective_script')) { ?>
<th scope="col">Delete</th>
<?php } ?>
</tr>
</thead>

<tbody>
<?php

$i = 0;

while ($clean = $fu->db->ReadRecord()) {

    $class = (isset($class) && $class == 'even') ? 'odd' : 'even';

?>
<tr class="<?php echo $class; ?>">
<td><?php echo $clean['cname']; ?></td>
<td class="number"><a href="blog.php?cat=<?php echo $clean['cid']; ?>" title="see entries"><?php echo $clean['num']; ?></a></td>
<td><a href="get-code.php?id=<?php echo $clean['cid']; ?>">Get display code?</a></td>
<td class="form"><form action="category.php" method="get">
<input type="submit" name="action" value="edit" class="edit" title="Edit category &#8216;<?php echo $clean['cname']; ?>&#8217;" />
<input type="hidden" name="id" value="<?php echo $clean['cid']; ?>" />
</form></td>
<?php if (!$fu->getOpt('collective_script')) { ?>
<td class="form"><form action="category.php" method="post">
<input type="submit" name="action" value="delete" class="delete" title="Delete category &#8216;<?php echo $clean['cname']; ?>&#8217;" />
<input type="hidden" name="cat_id" value="<?php echo $clean['cid']; ?>" />
</form></td>
<?php } ?>
</tr>
<?php

    $i++;
}

?>
</tbody>

</table>

<?php

}

$fu->db->FreeResult();

$query = 'INSERT INTO ' .$fu->getOpt('catoptions_table'). ' (cat_id)
  SELECT c.' .$fu->getOpt('col_id'). '
  FROM ' .$fu->getOpt('collective_table'). ' c
  LEFT JOIN ' .$fu->getOpt('catoptions_table'). ' co ON c.' .$fu->getOpt('col_id'). '=co.cat_id
  WHERE co.cat_id IS NULL';

$fu->db->Execute($query);

}

$fu->getFooter();

?>
