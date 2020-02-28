<?php

require_once('blog-config.php');
require_once('functions.php');

$fu =& FanUpdate::instance();
$fu->addOptFromDb();
$fu->login();

$clean = array();

if (isset($_POST['action'])) {

    $clean = clean_input($_POST);

// _____________________________________________ ADD QUERY

    if ($_POST['action'] == 'add') {

        if (empty($clean['smiley'])) {
            $fu->addErr('The smiley text is blank!');
        }

        if ($fu->noErr()) {

           $sql_smiley = $fu->db->Escape($clean['smiley']);
           $sql_image = $fu->db->Escape($clean['image']);

            $query = "INSERT INTO ".$fu->getOpt('smilies_table')." (smiley, image)
              VALUES ('$sql_smiley', '$sql_image')";

            if ($fu->db->Execute($query)) {
                $fu->addSuccess('Smiley <strong>'.$clean['smiley'].' = '.$fu->makeSmiley($clean['smiley'], $clean['image']).'</strong> added.');
                $clean = array();
            }
        }

// _____________________________________________ UPDATE QUERY

    } elseif ($_POST['action'] == 'update') {

        foreach ($clean['smiley'] as $key => $value) {

           $sql_smiley = $fu->db->Escape($value);
           $sql_image = $fu->db->Escape($clean['image'][$key]);

            $query = "UPDATE ".$fu->getOpt('smilies_table')."
              SET image='$sql_image' WHERE smiley='$sql_smiley'";

            if ($fu->db->Execute($query) && $fu->db->AffectedRows() > 0) {
                $fu->addSuccess('Smiley <strong>'.$clean['smiley'][$key].' = '.$fu->makeSmiley($clean['smiley'][$key], $clean['image'][$key]).'</strong> updated.');
            }
        }

// _____________________________________________ DELETE QUERY

    } elseif ($_POST['action'] == 'delete') {

        foreach ($clean['smiley_delete'] as $key) {

            $sql_id = $fu->db->Escape($key);

            $query = "DELETE FROM ".$fu->getOpt('smilies_table')."
              WHERE smiley='".$sql_id."'";

            if ($fu->db->Execute($query)) {
                $fu->addSuccess('Smiey <strong>'.$sql_id.'</strong> deleted.');
            }
        }
        $clean = array();
    }
} // end if post action

$fu->getHeader('Smilies');

?>

<h2>Options: Smilies</h2>
<ul class="subnav">
<li><a href="options.php">Main</a></li>
<li><a href="blacklist.php">Blacklist</a></li>
<li><a href="smilies.php">Smilies</a></li>
<li><a href="templates.php">Templates</a></li>
</ul>

<?php

// _____________________________________________ REPORT SUCCESS

$fu->reportSuccess();

// _____________________________________________ REPORT ERRORS

$fu->reportErrors();

// _____________________________________________ ADD FORM

?>

<form action="smilies.php" method="post">
<p><input type="text" id="smiley" name="smiley" size="10" maxlength="10" accesskey="n" /> =
<?php $fu->printSmileyImgDropdown('image'); ?>
<input type="submit" id="action" name="action" value="add" class="add" title="Add smiley, accesskey s" accesskey="s" /></p>
</form>

<?php

// ____________________________________________________________ LIST Words

$query = "SELECT *
  FROM ".$fu->getOpt('smilies_table');

$fu->db->Execute($query);

$num_cat = $fu->db->NumRows();

if ($num_cat > 0) {

?>
<form action="smilies.php" method="post">

<table class="sortable" summary="Table of smiliey images.">

<thead>
<tr>
<th scope="col">Text</th>
<th scope="col">Image</th>
<th scope="col">New Image</th>
<th scope="col">Delete?</th>
</tr>
</thead>

<tbody>
<?php

$i = 0;

while ($clean = $fu->db->ReadRecord()) {

    $class = (isset($class) && $class == 'even') ? 'odd' : 'even';

?>
<tr class="<?php echo $class; ?>">
<td><label for="smiley<?php echo $i; ?>"><?php echo $clean['smiley']; ?></label></td>
<td><?php echo $fu->makeSmiley($clean['smiley'], $clean['image']); ?></td>
<td><?php $fu->printSmileyImgDropdown('image['.$i.']', $clean['image']); ?></td>
<td><input type="checkbox" id="smiley<?php echo $i; ?>" name="smiley_delete[<?php echo $i; ?>]" value="<?php echo $clean['smiley']; ?>" />
<input type="hidden" name="smiley[<?php echo $i; ?>]" value="<?php echo $clean['smiley']; ?>" /></td>
</tr>
<?php

    $i++;
}

?>
</tbody>

<tfoot>
<tr>
<td colspan="2">&nbsp;</td>
<td><input type="submit" name="action" value="update" class="update" title="Update all smiley images" /></td>
<td><input type="submit" name="action" value="delete" class="delete" title="Delete checked smilies" /></td>
</tr>
</tfoot>

</table>

</form>

<?php

}

$fu->db->FreeResult();

$fu->getFooter();

?>