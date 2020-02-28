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

$clean['entry'] = '';
$clean['name'] = '';
$clean['email'] = '';
$clean['url'] = '';
$clean['comment'] = '';

$show_comment = false;
$points = 0;

if (isset($_POST['submit_comment'])) {

    require_once('blog-config.php');
    require_once('functions.php');

    $fu =& FanUpdate::instance();

    $clean = clean_input($_POST);

    // check for valid entry_id
    if (empty($clean['entry']) || !ctype_digit($clean['entry']) || $clean['entry'] <= 0) {

        $fu->addErr('Spam sucks, and you suck too.');

    // check for actual comment
    } elseif (empty($clean['comment'])) {

        $fu->addErr('Your comment is blank. You must have something to say!');

    } else {

        $fu->AddOptFromDb();

        // find entry

        $sql_entry = (int)$clean['entry'];

        $query_check = 'SELECT * FROM ' .$fu->getOpt('blog_table')." b
          WHERE entry_id=$sql_entry
          LIMIT 1";

        $fu->db->Execute($query_check);

        if ($fu->db->NumRows() > 0) {

            $row = $fu->db->GetRecord();
            $post = new FanUpdate_Post($row, $fu);

            // are comments even allowed?
            if ($post->allowComments()) {

                $pass_captcha = true;

                // check captcha, if enabled
                if ($fu->getOpt('captcha_on')) {
                
                    session_start();

                    if (!isset($_SESSION['security_code']) || $_POST['captcha'] != $_SESSION['security_code']) {
                        $pass_captcha = false;
                        $fu->addErr('The text you entered does not match the captcha image. Please try again.');
                    }

                    setcookie(session_name(), '', time()-36000, '/');
                    $_SESSION = array();
                    session_destroy();
                }

                if ($pass_captcha) {
	
					$pass_blacklist = true;

                    // check whole comment against blacklist
                    // normalize to lower case
                    $test_comment = strtolower($clean['name'].' '.$clean['email'].' '.$clean['url'].' '.$clean['comment']);

                    $query_check = 'SELECT * FROM ' .$fu->getOpt('blacklist_table');
                    $fu->db->Execute($query_check);

                    while ($row = $fu->db->ReadRecord()) {
                        if (strpos($test_comment, $row['badword']) !== false) {
							if ($fu->getOpt('points_scoring')) {
                            	--$points;
							} else {
								$fu->addErr('Please remove all spammy words from your comment and try again.');
	                            break;
							}
                        }
                    }
                    $fu->db->FreeResult();

					if ($pass_blacklist) {

	                    $clean['name'] = preg_replace("/\r/", '', $clean['name']);
	                    $clean['name'] = preg_replace("/\n/", '', $clean['name']);

	                    $clean['email'] = preg_replace("/\r/", '', $clean['email']);
	                    $clean['email'] = preg_replace("/\n/", '', $clean['email']);

	                    if (!empty($clean['url']) && strpos($clean['url'], 'http') !== 0) {
	                        $clean['url'] = 'http://'.$clean['url'];
	                    }

						if (!is_url($clean['url'])) { // bad URL, erase it
							$clean['url'] = '';
						}
						if (!is_email($clean['email'])) { // bad email, erase it
							$clean['email'] = '';
						}
						
						$ip_hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
						
						if ($fu->getOpt('points_scoring')) {
							
							// no host name
							if ($ip_hostname == $_SERVER['REMOTE_ADDR']) {
								$points -= 2;
							}
					
							// language header
							$lang = trim($_SERVER['HTTP_ACCEPT_LANGUAGE']);
							if (empty($lang)) {
								$points -= 4;
							}

							// number of links in comment
							// check un-cleaned comment (with tags intact)
							$num_links = preg_match_all('!([\w]+?://|www\.)[\w#$%&~/.\-;:=,?@\[\]+]+!', $_POST['comment'], $matches);

							if ($num_links > 2) {
								$points -= $num_links;
							} elseif ($num_links == 0) {
								$points += 2;
							}
							
							// it's a link...
							if (!empty($clean['url'])) {
								--$points;
								if (strlen($clean['url']) > 35) {
									$points -= 2;
								}
							}

							// length of comment
							$len = strlen($clean['comment']);
							if (($len > 20) && ($num_links == 0)) {
								++$points;
							} elseif ($len < 20) {
								$points -= 2;
							}

							// previous comments from email
							if (!empty($clean['email'])) {
								$sql_email = $fu->db->Escape($clean['email']);
								
								$num_approved = $fu->db->GetFirstCell('SELECT COUNT(*) FROM ' .$fu->getOpt('comments_table')." WHERE email='$sql_email' AND approved = 1 AND added < DATE_ADD(CURRENT_TIMESTAMP(), INTERVAL -7 DAY)");
								$num_spam = $fu->db->GetFirstCell('SELECT COUNT(*) FROM ' .$fu->getOpt('comments_table')." WHERE email='$sql_email' AND approved = 0 AND added < DATE_ADD(CURRENT_TIMESTAMP(), INTERVAL -7 DAY)");

								$points += $num_approved;
								$points -= $num_spam;
							} else {
								$points -= 2; // no email, penalize
							}
							
						} // end if use points
					
						$clean['approved'] = 1;
						$status = 'Approved';
					
						if ($fu->getOpt('comment_moderation') || ($fu->getOpt('points_scoring') && $points < $fu->getOpt('points_approval_threshold', true))) {
							$clean['approved'] = 0;
							$status = 'Pending';
						}

	                    $clean['comment'] = str_replace('[B]', '<strong>', $clean['comment']);
	                    $clean['comment'] = str_replace('[/B]', '</strong>', $clean['comment']);
	                    $clean['comment'] = str_replace('[I]', '<em>', $clean['comment']);
	                    $clean['comment'] = str_replace('[/I]', '</em>', $clean['comment']);
	                    $clean['comment'] = str_replace('[Q]', '<blockquote>', $clean['comment']);
	                    $clean['comment'] = str_replace('[/Q]', '</blockquote>', $clean['comment']);

						// Require that bbcode links be valid URLS -- no XSS -- thanks for the heads up, Jem!
	                    $clean['comment'] = preg_replace( "!\[URL=([\w]+?://[\w#$%&~/.\-;:=,?@\[\]+]+)](.*?)\[/URL]!", "<a href=\"\\1\">\\2</a>", $clean['comment'] );
	                    $clean['comment'] = preg_replace("!\[URL]([\w]+?://[\w#$%&~/.\-;:=,?@\[\]+]+)\[/URL]!", "<a href=\"\\1\">\\1</a>", $clean['comment'] );

	                    if ($clean['remember_me'] == 'y') {

	                        $cookie_life = time() + 31536000; // Life of one year

	                        setcookie('fanuname', $clean['name'], $cookie_life, '/');
	                        setcookie('fanuemail', $clean['email'], $cookie_life, '/');
	                        setcookie('fanuurl', $clean['url'], $cookie_life, '/');
	                    }
                    
	                    $clean['added'] = gmdate('Y-m-d H:i:s');

	                    $sql_entry = (int)$clean['entry'];
	                    $sql_name = $fu->db->Escape($clean['name']);
	                    $sql_email = $fu->db->Escape($clean['email']);
	                    $sql_url = $fu->db->Escape($clean['url']);
	                    $sql_comment = $fu->db->Escape($clean['comment']);
	                    $sql_approved = $clean['approved'];
	                    $sql_added = $fu->db->Escape($clean['added']);
						$sql_points = $points;

	                    $sql_agent = $fu->db->Escape($_SERVER['HTTP_USER_AGENT']);
	                    $sql_ip = ip2long($_SERVER['REMOTE_ADDR']);

	                    $query = 'INSERT INTO ' .$fu->getOpt('comments_table')." (entry_id, name, email, url, comment, approved, added, points)
	                      VALUES ($sql_entry, '$sql_name', '$sql_email', '$sql_url', '$sql_comment', $sql_approved, '$sql_added', $sql_points)";

	                    $fu->db->Execute($query);

	                    $clean['comment_id'] = $fu->db->GetLastSequence();

	                    if ($fu->getOpt('email_new_comments')) {
		
							$subject = strtoupper($status).' Comment on '.$post->getTitle();
		
							$msg = '';
	
							$msg .= "Name:\t".$clean['name']."\r\n";
						    $msg .= "Email:\t".$clean['email']."\r\n";
						    $msg .= "URL:\t".$clean['url']."\r\n\r\n";
						    $msg .= $clean['comment']."\r\n\r\n";
							
							$msg .= "==========\r\n\r\n";
							
							$msg .= "Status:\t".$status."\r\n";
							if ($fu->getOpt('points_scoring')) {
								$msg .= "Points:\t".$points."\r\n";
							}
							$msg .= "\r\n";
							
							if ($clean['approved'] == 0) {
								$msg .= "This comment has been held for moderation. Visit your admin panel to approve or delete it:\r\n";
	                            $msg .= $fu->getOpt('install_url').'/comment.php?id='.$clean['comment_id']."\r\n\r\n";
	                        } else {
								$msg .= "This comment has been approved and can be seen here:\r\n";
	                            $msg .= $fu->getOpt('blog_page').'?id='.$clean['entry'].'#comment'.$clean['comment_id']."\r\n\r\n";

								$msg .= "Admin link:\r\n";
								$msg .= $fu->getOpt('install_url').'/comment.php?id='.$clean['comment_id']."\r\n\r\n";
	                        }
	
							$msg .= "==========\r\n\r\n";
							
		                    $msg .= "Time:\t\t".date('r')."\r\n";
		                    $msg .= "Referrer:\t".$_SERVER['HTTP_REFERER']."\r\n";
		                    $msg .= "Sender IP:\t".$_SERVER['REMOTE_ADDR']."\r\n";
		                    $msg .= "Hostname:\t".$ip_hostname."\r\n";
							$msg .= "User Agent:\t".$_SERVER['HTTP_USER_AGENT']."\r\n";

	                        $mailheaders = 'From: "FanUpdate '.$fu->getOpt('version').'" <'.$fu->getOpt('admin_email').">\r\n";

							if ($points >= $fu->getOpt('points_pending_threshold', true)) {
	                        	@mail($fu->getOpt('admin_email'), $subject, $msg, $mailheaders);
							}
	                    }

	                    $result_string = "<!-- SUCCESS do not remove -->\n";

	                    if ($clean['approved'] == 0) {

	                        $result_string .= "<h3>Thank You</h3>\n";
	                        $result_string .= '<p>Thank you for your comment. In an effort to reduce spam, your comment has been held for moderation and will not appear on the site until it has been approved.</p>'."\n";

	                    } else {
                    
	                        $show_comment = true;

	                        $cmt = new FanUpdate_Comment($clean, $fu);

	                        $result_string .= "<h3>Thank you for your comment:</h3>\n";
	                    }

					} // end pass_blacklist

                } // end pass_captcha

            } else {
                // comments are off
                $fu->reportErrors('Comments are currently not permitted on this post.');
            } // end allowComments

        } // end entry exists

    }
    
    if ($_POST['submit_comment'] != 'ajax') {
		if ($clean['approved'] > 0) {
			[$return] = explode('#', $clean['returnto']);
			header('Location: '.$return.'#comment'.$clean['comment_id']);
		}
		
        $fu->getHeader('Comment Submitted');
    }
    
    $fu->reportErrors();
    
    echo $result_string;
    
    if ($show_comment) {
        $cmt->printComment();
    }

    if ($_POST['submit_comment'] != 'ajax') {
        echo '<p><a href="'.$clean['returnto'].'">Return to the previous page</a>.</p>'."\n";
        $fu->getFooter(false);
    }

}


