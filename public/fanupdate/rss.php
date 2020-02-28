<?php

require_once('blog-config.php');
require_once('functions.php');

$fu =& FanUpdate::instance();
$fu->addOptFromDb();

require_once('class/FeedWriter.php');
$feed = new FeedWriter(RSS2);

require_once('class/FileHandler.php');
$fh = new FileHandler();

if (isset($_GET['c'])) {

    $c = (int)$_GET['c'];

    if ($c == 0) {
        $subject = 'Collective';
    } else {

        $query_cat = "SELECT c.".$fu->getOpt('col_subj')." AS cat_name
        FROM ".$fu->getOpt('catjoin_table')." j
        JOIN ".$fu->getOpt('collective_table')." c ON j.cat_id=c.".$fu->getOpt('col_id')."
        WHERE j.cat_id=".$c;

        $subject = $fu->db->GetFirstCell($query_cat);
    }

	$feed->setTitle($fu->getOpt('site_name').': '.$subject);
	$feed->setLink($fu->getOpt('blog_page').'?c='.$c);
	$feed->setDescription('The latest updates for '.$subject.' from '.$fu->getOpt('site_name').'.');

    $query = "SELECT b.*, COUNT(c.comment_id) AS num_comments
    FROM ".$fu->getOpt('blog_table')." b
    JOIN ".$fu->getOpt('catjoin_table')." j ON b.entry_id=j.entry_id
	LEFT JOIN ".$fu->getOpt('comments_table')." c ON b.entry_id=c.entry_id
    WHERE b.is_public > 0 AND b.added <= DATE_ADD(CURRENT_TIMESTAMP(), INTERVAL ".(0-$fu->getOpt('_server_tz_offset'))." HOUR) AND j.cat_id=$c
	GROUP BY b.entry_id
    ORDER BY b.added DESC LIMIT 20";

	$query_added = "SELECT b.added
	FROM ".$fu->getOpt('blog_table')." b
	JOIN ".$fu->getOpt('catjoin_table')." j ON b.entry_id=j.entry_id
    WHERE b.is_public > 0 AND b.added <= DATE_ADD(CURRENT_TIMESTAMP(), INTERVAL ".(0-$fu->getOpt('_server_tz_offset'))." HOUR) AND j.cat_id=$c
	ORDER BY b.added DESC LIMIT 1";

} else {
	
	$feed->setTitle($fu->getOpt('site_name'));
	$feed->setLink($fu->getOpt('blog_page'));
	$feed->setDescription('The latest updates from '.$fu->getOpt('site_name').'.');
	
	$query = "SELECT b.*, COUNT(c.comment_id) AS num_comments
    FROM ".$fu->getOpt('blog_table')."  b
	LEFT JOIN ".$fu->getOpt('comments_table')." c ON b.entry_id=c.entry_id
	WHERE b.is_public > 0 AND b.added <= DATE_ADD(CURRENT_TIMESTAMP(), INTERVAL ".(0-$fu->getOpt('_server_tz_offset'))." HOUR)
	GROUP BY b.entry_id
    ORDER BY b.added DESC LIMIT 20";

	$query_added = "SELECT b.added
	FROM ".$fu->getOpt('blog_table')." b
	WHERE b.is_public > 0 AND b.added <= DATE_ADD(CURRENT_TIMESTAMP(), INTERVAL ".(0-$fu->getOpt('_server_tz_offset'))." HOUR)
	ORDER BY b.added DESC LIMIT 1";
}

$fu->db->Execute($query_added);
if ($added = $fu->db->GetFirstCell()) {
	$feed->setPubDate($added.' GMT');
}

$feed->setBuildDate(time());

$feed->setChannelElement('language', 'en-us');
$feed->setAuthor($fu->getOpt('admin_email'), $fu->getOpt('site_name'));
$feed->setChannelElement('generator', 'FanUpdate '.$fu->getOpt('version'));

$feed->addNamespace('content');
$feed->addNamespace('slash');
$feed->addNamespace('wfw');

$fu->db->Execute($query);

while ($row = $fu->db->ReadRecord()) {

    $post = new FanUpdate_Post($row, $fu, $fu->getOpt('blog_page'));

	$html_text = $post->getBodyFormatted();
	$html_text = str_replace('href="/', 'href="'.$_SERVER['SERVER_NAME'].'/', $html_text);
	$html_text = str_replace('src="/', 'src="'.$_SERVER['SERVER_NAME'].'/', $html_text);

	$item = $feed->createNewItem();
  
	$item->setTitle($post->getTitle());
	$item->setAuthor($fu->getOpt('admin_email'), $fu->getOpt('site_name'));
	$item->setLink($post->getUrl(), true);
	$item->setDate($post->getDate().' GMT');
	$item->setDescription(strip_tags($post->getBody()));
	$item->addElement('content:encoded', $html_text);
	if ($post->allowComments()) {
		$item->addElement('comments', $post->getCommentsUrl());
		$item->addElement('wfw:commentRss', $post->getCommentsFeedUrl());
		if ($row['num_comments'] > 0) {
			$item->addElement('slash:comments', $row['num_comments']);
		}
	}
	
	// find enclosures
	preg_match_all('/<a\s[^>]*href=(\"??)(([^\" >]*?)\.(mov|m4a|mp4|m4v|zip|mp3|pdf))\\1[^>]*>(.*)<\/a>/siU', $html_text, $matches, PREG_SET_ORDER);
	
	foreach ($matches as $match) {
		$url = $match[2];
		$filename = basename($match[2]);
		$type = $fh->getMimeType($filename);
		$size = 0;
		$url_p = parse_url($url);
		$filepath = $_SERVER['DOCUMENT_ROOT'].$url_p['path'];
		if (file_exists($filepath)) {
			$size = $fh->getSize($filepath);
		}
		$item->setEnclosure($url, $size, $type);
	}
	
	$feed->addItem($item);
}

$feed->generateFeed();

?>