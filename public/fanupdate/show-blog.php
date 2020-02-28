<div id="fanupdate">
<?php

require_once('blog-config.php');
require_once('functions.php');

$fu =& FanUpdate::instance();
$fu->addOptFromDb();

// ____________________________________________________________ ARCHIVES

if (isset($_GET['view']) && $_GET['view'] == 'archive') {

?>
<h2>Month Archives</h2>
<ul>
<?php

$query = "SELECT DATE_FORMAT( added, '%M %Y' ) AS get_month,
DATE_FORMAT( added, '%Y%m' ) AS get_monthx,
COUNT(entry_id) AS num_entry
FROM ".$fu->getOpt('blog_table')."
WHERE is_public > 0 AND added <= DATE_ADD(CURRENT_TIMESTAMP(), INTERVAL ".(0-$fu->getOpt('_server_tz_offset'))." HOUR)
GROUP BY get_monthx DESC";

$fu->db->Execute($query);

while ($row = $fu->db->ReadRecord()) {
    echo '<li><a href="'.$fu->getCleanSelf().'?m='.$row['get_monthx'].'">'.$row['get_month'].'</a> ('.$row['num_entry'].")</li>\n";
}
$fu->db->FreeResult();

?>
</ul>

<h2>Category Archives</h2>
<ul>
<?php

$query = "SELECT j.cat_id, c.".$fu->getOpt('col_subj')." AS cat_name, COUNT(b.entry_id) AS num_entry
FROM ".$fu->getOpt('catjoin_table')." j
JOIN ".$fu->getOpt('blog_table')." b ON j.entry_id=b.entry_id
LEFT JOIN ".$fu->getOpt('collective_table')." c ON j.cat_id=c.".$fu->getOpt('col_id')."
WHERE b.is_public > 0 AND b.added <= DATE_ADD(CURRENT_TIMESTAMP(), INTERVAL ".(0-$fu->getOpt('_server_tz_offset'))." HOUR)
GROUP BY j.cat_id
ORDER BY c.".$fu->getOpt('col_subj')." ASC";

$fu->db->Execute($query);

while ($row = $fu->db->ReadRecord()) {

    if (empty($row['cat_name'])) {
        $row['cat_name'] = 'Whole Collective';
    }

    echo '<li><a href="'.$fu->getCleanSelf().'?c='.$row['cat_id'].'">'.$row['cat_name'].'</a> ('.$row['num_entry'].")</li>\n";
}

$fu->db->FreeResult();

?>
</ul>
<?php

} else {

// ____________________________________________________________ DISPLAY ENTRIES QUERY

    $single_page = false;

    $query = "SELECT * FROM ".$fu->getOpt('blog_table')." b";

    if (isset($_GET['c'])) {
        $c = (int)$_GET['c'];
        $query .= " JOIN ".$fu->getOpt('catjoin_table')." j ON b.entry_id=j.entry_id";
    }

    $query .= " WHERE b.is_public > 0 AND b.added <= DATE_ADD(CURRENT_TIMESTAMP(), INTERVAL ".(0-$fu->getOpt('_server_tz_offset'))." HOUR)";

    if (isset($_GET['c'])) {

        $query .= " AND j.cat_id=$c";

    } elseif (!empty($_GET['q'])) {

        $q = clean_input($_GET['q']);
        $sql_q = $fu->db->Escape($q);

        $query .= " AND MATCH (b.title, b.body) AGAINST ('$sql_q' IN BOOLEAN MODE)";

    } elseif (!empty($_GET['id'])) {

        $id = (int)$_GET['id'];
        $query .= ' AND b.entry_id='.$id;

        $single_page = true;

    } elseif (!empty($_GET['m'])) {

        $m = (int)$_GET['m'];
        $query .= " AND DATE_FORMAT(b.added, '%Y%m')='$m'";
    }

    $query .= " ORDER BY b.added DESC";

    if (!empty($q)) {
        echo '<p>Search results for <strong>'.$q."</strong></p>\n";
    }

    $fu->printBlog($query, $main_limit, $single_page);
}

$fu->printBlogFooter();

?>
</div><!-- END #fanupdate -->