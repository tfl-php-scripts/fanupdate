<?php

require_once('blog-config.php');
require_once('functions.php');

$fu =& FanUpdate::instance();
$fu->addOptFromDb();
$fu->login();

$fu->getHeader('Dashboard');

?>

<div class="col13">
<p id="newUpdate"><a href="blog.php?action=new" title="New entry, accesskey n" accesskey="n"><span>Post a New Entry</span></a></p>

<?php $fu->checkUpdates(); ?>

<?php

// ____________________________________________________________ COMMENTS AWAITING APPROVAL

$query_comments = "SELECT COUNT(comment_id) FROM ".$fu->getOpt('comments_table')." c WHERE c.approved=0 AND c.points >= ".$fu->getOpt('points_pending_threshold', true);

$unapproved_comments = $fu->db->GetFirstCell($query_comments);

if ($unapproved_comments > 0) {
    echo '<p class="error"><a href="comment.php?show=pending"><strong>'.$unapproved_comments.'</strong> comments awaiting approval</a>.</p>';
}

?>

<p><a href="<?php echo $fu->getOpt('blog_page'); ?>">View your blog</a></p>

<h3>Stats</h3>

<?php

$query_entry_count = "SELECT COUNT(entry_id) FROM ".$fu->getOpt('blog_table')." WHERE is_public>0";
$entry_count = $fu->db->GetFirstCell($query_entry_count);

$query_comment_count = "SELECT COUNT(comment_id) FROM ".$fu->getOpt('comments_table')." WHERE approved > 0";
$comment_count = $fu->db->GetFirstCell($query_comment_count);

?>

<ul>
<li><strong><?php echo $entry_count; ?></strong> entries</li>
<li><strong><?php echo $comment_count; ?></strong> comments</li>
</ul>

<h3>Help</h3>
<ul>
<li><a href="get-code.php">How to display your blog</a></li>
<li><a href="docs/readme.txt">View readme.txt</a></li>
<li><a href="<?php echo $fu->getOpt('url'); ?>">Documentation &amp; FAQs</a></li>
</ul>
</div><!-- END .col13 -->

<div class="col23">
<h3>Last Entries
<a href="<?php echo $fu->getOpt('install_url'); ?>/rss.php" class="feed" title="RSS feed for entries">Feed</a></h3>
<ul>
<?php

$query = "SELECT b.*, COUNT(com.comment_id) AS num_comments
FROM ".$fu->getOpt('blog_table')." b
LEFT JOIN ".$fu->getOpt('comments_table')." com ON b.entry_id=com.entry_id
WHERE b.is_public > 0 AND (com.approved>0 OR com.approved IS NULL)
GROUP BY b.entry_id
ORDER BY b.added DESC LIMIT 5";

$fu->db->Execute($query);

while ($row = $fu->db->ReadRecord()) {
	$post = new FanUpdate_Post($row, $fu, $fu->getOpt('blog_page'));
	echo '<li><a href="'.$post->getUrl().'">'.$post->getTitle().'</a><br />';
	echo $post->getDateFormatted().'<br />';
	echo $row['num_comments'].' comments</li>';
}

?>
</ul>
</div><!-- END .col23 -->

<div class="col33">
<h3>Last Comments
<a href="<?php echo $fu->getOpt('install_url'); ?>/rss-comments.php" class="feed" title="RSS feed for comments">Feed</a></h3>
<ul>
<?php

$query = "SELECT c.*, b.title
FROM ".$fu->getOpt('comments_table')." c
JOIN ".$fu->getOpt('blog_table')." b ON c.entry_id=b.entry_id
WHERE c.approved>0
ORDER BY c.added DESC LIMIT 5";

$fu->db->Execute($query);

while ($row = $fu->db->ReadRecord()) {
	$cmt = new FanUpdate_Comment($row, $fu, $fu->getOpt('blog_page'));
	echo '<li>'.$cmt->getGravatar(42).' '.$cmt->getDateFormatted().' on <a href="'.$cmt->getLinkUrl().'">'.$row['title'].'</a><br />';
	echo $cmt->getAbstract(12, true).'</li>';
}

?>
</ul>
</div><!-- END .col33 -->

<div class="col13">&nbsp;</div><!-- END .col13 -->

<div class="col23">
<?php

$query = "SELECT b.*, COUNT(c.comment_id) AS num_comments
FROM ".$fu->getOpt('blog_table')." b
JOIN ".$fu->getOpt('comments_table')." c ON b.entry_id=c.entry_id
WHERE c.approved>0
GROUP BY b.entry_id
ORDER BY num_comments DESC LIMIT 5";

$fu->db->Execute($query);

if ($fu->db->NumRows() > 0) {
	
	echo "<h3>Top Entries</h3>\n";
	echo "<ul>\n";

	while ($row = $fu->db->ReadRecord()) {
		$post = new FanUpdate_Post($row, $fu, $fu->getOpt('blog_page'));
		echo '<li><a href="'.$post->getUrl().'">'.$post->getTitle().'</a><br />';
		echo $post->getDateFormatted().'<br />';
		echo $row['num_comments'].' comments</li>';
	}

	echo "</ul>\n";
}

?>
</div><!-- END .col23 -->

<div class="col33">
<?php

$query = "SELECT c.*, COUNT(c.comment_id) AS num_comments
FROM ".$fu->getOpt('comments_table')." c
WHERE c.approved>0 AND c.email!='".$fu->getOpt('admin_email')."'
GROUP BY c.name, c.email
ORDER BY num_comments DESC LIMIT 5";

$fu->db->Execute($query);

if ($fu->db->NumRows() > 0) {
	
	echo "<h3>Top Commenters</h3>\n";
	echo "<ul>\n";
	
	while ($row = $fu->db->ReadRecord()) {
		$cmt = new FanUpdate_Comment($row, $fu, $fu->getOpt('blog_page'));
		echo '<li>'.$cmt->getGravatar(42).' '.$cmt->getCommenterLink().'<br />';
		echo $row['num_comments'].' comments</li>';
	}

	echo "</ul>\n";
}

?>
</div><!-- END .col33 -->

<?php

$fu->getFooter();

?>