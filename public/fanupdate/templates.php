<?php

require_once('blog-config.php');
require_once('functions.php');

$fu =& FanUpdate::instance();
$fu->addOptFromDb();
$fu->login();

$clean = array();

// _____________________________________________ UPDATE QUERY

if (isset($_POST['action']) && $_POST['action'] == 'update') {

    $clean = clean_input($_POST, true);

    foreach ($clean['key'] as $id => $key) {

        $key = $fu->db->Escape($key);
        $value = $fu->db->Escape($clean['value'][$id]);

        $query = "UPDATE ".$fu->getOpt('options_table')." SET optvalue='$value' WHERE optkey='$key'";

        if (! $fu->db->Execute($query)) {
            $fu->addErr('Failed to update <strong>'.$key.'</strong>');
        } else {
			if ($fu->db->AffectedRows() > 0) {
            	$fu->addSuccess('Set <strong>'.$key.'</strong> = '.nl2br(htmlspecialchars($clean['value'][$id])));
			}
        }
    }
}

$fu->getHeader('Templates');

?>

<h2>Options: Templates</h2>
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

// _____________________________________________ OPTIONS FORM

?>

<form action="templates.php" method="post">
<?php

$query = "SELECT * FROM ".$fu->getOpt('options_table')." WHERE optkey LIKE '%template%' ORDER BY optkey ASC";
$fu->db->Execute($query);

while ($row = $fu->db->ReadRecord()) {

    if (strpos($row['optkey'], '_') != 0) { // hide private vars

    $class = (isset($class) && $class == 'even') ? 'odd' : 'even';

    ?>
<div class="option <?php echo $class; ?>">
<p><label for="<?php echo $row['optkey']; ?>"><?php echo $row['optkey']; ?>:
<span class="help"><?php echo $row['optdesc']; ?></span></label>
<textarea id="<?php echo $row['optkey']; ?>" name="value[]" cols="80" rows="15"><?php echo htmlspecialchars($row['optvalue']); ?></textarea>
<input type="hidden" name="key[]" value="<?php echo $row['optkey']; ?>" /></p>
</div><!-- END .option -->
<?php

    }

}

$fu->db->FreeResult();

?>

<p><input type="hidden" name="action" value="update" />
<input type="submit" value="Save Changes" class="update" title="Save changes, accesskey s" accesskey="s" id="primary_action" /></p>

</form>

<?php $fu->getFooter(); ?>