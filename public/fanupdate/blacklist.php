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

$fu = FanUpdate::instance();
$fu->addOptFromDb();
$fu->login();

$clean = array();
$showform = false;

if (isset($_POST['action'])) {

    $clean = clean_input($_POST);

// _____________________________________________ ADD QUERY

    if ($_POST['action'] == 'add') {

        $showform = true;

        if (empty($clean['badword'])) {
            $fu->addErr('The blacklist word is blank!');
        }

        if ($fu->noErr()) {

            // normalize to lower case
            $sql_badword = $fu->db->Escape(strtolower($clean['badword']));

            $query = 'INSERT INTO ' . $fu->getOpt('blacklist_table') . " (badword)
              VALUES ('$sql_badword')";

            if ($fu->db->Execute($query)) {
                $fu->addSuccess('Word <strong>' . $clean['badword'] . '</strong> blacklisted.');
                $clean = array();
            }
        }

// _____________________________________________ DELETE QUERY

    } elseif ($_POST['action'] == 'delete') {

        $cat_count = count($clean['badwords']);

        foreach ($clean['badword'] as $key) {

            $sql_id = $fu->db->Escape($key);

            $query = 'DELETE FROM ' . $fu->getOpt('blacklist_table') . "
              WHERE badword='" . $sql_id . "'";

            if ($fu->db->Execute($query)) {
                $fu->addSuccess('Word <strong>' . $sql_id . '</strong> un-blacklisted.');
            }
        }
        $clean = array();
    }
} // end if post action

$fu->getHeader('Blacklist');

?>

    <h2>Options: Blacklist</h2>
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

    <form action="blacklist.php" method="post">
        <p><input type="text" id="badword" name="badword" size="20" maxlength="50" accesskey="n"/>
            <input type="submit" id="action" name="action" value="add" class="add"
                   title="blacklist this word, accesskey s"
                   accesskey="s"/></p>
    </form>

<?php

// ____________________________________________________________ LIST Words

$query = 'SELECT *
  FROM ' . $fu->getOpt('blacklist_table') . '
  ORDER BY badword ASC';

$fu->db->Execute($query);

$num_cat = $fu->db->NumRows();

if ($num_cat > 0) {

    ?>
    <form action="blacklist.php" method="post">

        <table class="sortable" summary="Table of blacklisted words, sorted alphabetically.">

            <thead>
            <tr>
                <th scope="col">Word</th>
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
                    <td><label for="badword<?php echo $i; ?>"><?php echo $clean['badword']; ?></label></td>
                    <td><input type="checkbox" id="badword<?php echo $i; ?>" name="badword[<?php echo $i; ?>]"
                               value="<?php echo $clean['badword']; ?>"/></td>
                </tr>
                <?php

                $i++;
            }

            ?>
            </tbody>

            <tfoot>
            <tr>
                <td>&nbsp;</td>
                <td><input type="submit" name="action" value="delete" class="delete"
                           title="Delete checked words from the blacklist"/></td>
            </tr>
            </tfoot>

        </table>

    </form>

    <?php

}

$fu->db->FreeResult();

$fu->getFooter();
