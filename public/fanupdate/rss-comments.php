<?php

require_once('blog-config.php');
require_once('functions.php');

$fu =& FanUpdate::instance();
$fu->addOptFromDb();

require_once('class/FeedWriter.php');
$feed = new FeedWriter(RSS2);

if (isset($_GET['id'])) {

    $id = (int)$_GET['id'];

    $query = "SELECT b.* FROM ".$fu->getOpt('blog_table')." b WHERE b.entry_id=".$id." LIMIT 1";

    $fu->db->Execute($query);
	$row = $fu->db->GetRecord();
    $post = new FanUpdate_Post($row, $fu, $fu->getOpt('blog_page'));

	$feed->setTitle($fu->getOpt('site_name').': Comments on '.$post->getTitle());
	$feed->setLink($post->getCommentsUrl());
	$feed->setDescription('The latest comments on "'.$post->getTitle().'" from '.$fu->getOpt('site_name').'.');

    $query = "SELECT c.*
    FROM ".$fu->getOpt('comments_table')." c
    WHERE c.entry_id=$id AND c.approved > 0
    ORDER BY c.added DESC LIMIT 20";

	$query_added = "SELECT c.added
	FROM ".$fu->getOpt('comments_table')." c
	WHERE c.entry_id=$id AND c.approved > 0
	ORDER BY c.added DESC LIMIT 1";

} else {
	
	$feed->setTitle($fu->getOpt('site_name').': Comments');
	$feed->setLink($fu->getOpt('blog_page'));
	$feed->setDescription('The latest comments from '.$fu->getOpt('site_name').'.');

    $query = "SELECT c.*
    FROM ".$fu->getOpt('comments_table')." c
    WHERE c.approved > 0
    ORDER BY c.added DESC LIMIT 20";

	$query_added = "SELECT c.added
	FROM ".$fu->getOpt('comments_table')." c
	WHERE c.approved > 0
	ORDER BY c.added DESC LIMIT 1";
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

$fu->db->Execute($query);

while ($row = $fu->db->ReadRecord()) {

    $cmt = new FanUpdate_Comment($row, $fu, $fu->getOpt('blog_page'));

	$item = $feed->createNewItem();
  
	$item->setTitle($cmt->getAbstract());
	$item->setLink($cmt->getLinkUrl(), true);
	$item->setDate($cmt->getDate().' GMT');
	$item->setDescription(strip_tags($cmt->getBody()));
	$item->addElement('content:encoded', $cmt->getBodyFormatted());
	$item->setAuthor($cmt->getEmail(), $cmt->getName());
	
	$feed->addItem($item);
}

$feed->generateFeed();

?>