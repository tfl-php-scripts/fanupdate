<div id="fanupdate">

<?php

if (!isset($listingid) && !is_numeric($listingid)) { exit; }

require_once('blog-config.php');
require_once('functions.php');

$fu =& FanUpdate::instance();
$fu->addOptFromDb();

$query = "SELECT * FROM ".$fu->getOpt('catoptions_table')." WHERE cat_id=".(int)$listingid." LIMIT 1";

$cat = $fu->db->GetRecord($query);

if (is_array($cat)) {
    foreach ($cat as $key => $value) {
        if ($value != '') { // don't update defaults!
            $fu->AddOpt($key, $value);
        }
    }
}

// ____________________________________________________________ ARCHIVES

if (isset($_GET['view']) && $_GET['view'] == 'archive') {

?>
<h2>Archives</h2>
<?php

    $query = "SELECT b.entry_id, b.added, b.title
    FROM ".$fu->getOpt('blog_table')." b
    JOIN ".$fu->getOpt('catjoin_table')." j ON b.entry_id=j.entry_id
    WHERE b.is_public > 0 AND b.added <= DATE_ADD(CURRENT_TIMESTAMP(), INTERVAL ".(0-$fu->getOpt('_server_tz_offset'))." HOUR) AND j.cat_id=".$listingid."
    ORDER BY b.added DESC";

    $fu->db->Execute($query);

    $lastyear = '';

    while ($row = $fu->db->ReadRecord()) {

        $post = new FanUpdate_Post($row, $fu);

        $year = $post->getYear();
        if ($year != $lastyear) {
            if (!empty($lastyear)) { echo "</ul>\n"; }
            echo '<h3>'.$year."</h3>\n";
            echo "<ul>\n";
        }

        echo '<li><span class="date">'.$post->getDateForMatted().':</span> <a href="'.$post->getURL().'">'.$post->getTitle()."</a></li>\n";

        $lastyear = $year;

    }
    $fu->db->FreeResult();

    echo "</ul>\n";

} else {

// ____________________________________________________________ DISPLAY ENTRIES QUERY

    $single_page = false;

    $query = "SELECT b.*
    FROM ".$fu->getOpt('blog_table')." b
    JOIN ".$fu->getOpt('catjoin_table')." j ON b.entry_id=j.entry_id
    WHERE b.is_public > 0 AND b.added <= DATE_ADD(CURRENT_TIMESTAMP(), INTERVAL ".(0-$fu->getOpt('_server_tz_offset'))." HOUR) AND j.cat_id=".$listingid;

    if (!empty($_GET['id'])) {

        $id = (int)$_GET['id'];
        $query .= " AND b.entry_id=".$id;

        $single_page = true;

    } elseif (!empty($_GET['q'])) {

        $q = clean_input($_GET['q']);
        $sql_q = $fu->db->Escape($q);
        $query .= " AND MATCH (b.title, b.body) AGAINST ('$sql_q' IN BOOLEAN MODE)";

    }

    $query .= " ORDER BY b.added DESC";

    if (!empty($q)) {
        echo '<p>Search results for <strong>'.$q."</strong></p>\n";
    }

    $fu->printBlog($query, $main_limit, $single_page);
}

$fu->printBlogFooter($listingid);

?>

</div><!-- END #fanupdate -->