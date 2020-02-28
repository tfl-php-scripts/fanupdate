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

// DON'T CHANGE THESE!
$fanupdate = $fanupdate ?? [];
$fanupdate['tables'] = [$fanupdate['blog_table'], $fanupdate['collective_table'], $fanupdate['catjoin_table'], $fanupdate['comments_table'], $fanupdate['options_table'], $fanupdate['blacklist_table'], $fanupdate['catoptions_table'], $fanupdate['smilies_table']];

// requires SqlConnection class, clean_input

class FanUpdate
{
    public $_cfg = array();
    public $_clean_self;
    /**
     * @var SqlConnection
     */
    public $db;
    public $errors = array();
    public $success = array();
    public $smilies = array();
    public $smiley_imgs = array();
    public $observers = array();

    private $config = [
        'version' => '2.3',
        'url' => 'http://prism-perfect.net/fanupdate',
        'newUrl' => 'https://scripts.robotess.net',
    ];

    /**
     * @var self
     */
    private static $instance;

    private function __construct()
    {
        global $fanupdate, $coltable;

        $this->_cfg = array_merge($fanupdate, $this->config);
        $this->_cfg['col_id'] = $coltable[$this->getOpt('collective_script', true)]['id'];
        $this->_cfg['col_subj'] = $coltable[$this->getOpt('collective_script', true)]['subject'];
        $this->db = SqlConnection::instance($this->getOpt('dbhost'), $this->getOpt('dbuser'), $this->getOpt('dbpass'), $this->getOpt('dbname'));
        $this->_clean_self = clean_input($_SERVER['PHP_SELF']);

        $this->_cfg['entry_template'] = '<h2><a href="{{url}}" title="permanent link to this post">{{title}}</a></h2>' . "\n"
            . '<p class="catfile">Posted {{date}}. Filed under {{category}}. {{comment_link}}</p>' . "\n"
            . '{{body}}';

        $this->_cfg['comment_template'] = '{{gravatar}}' . "\n"
            . '<p class="commenter">On '
            . '<a href="#comment{{id}}" title="permanent link to this comment">{{date}}</a> '
            . '{{name}} said:</p>' . "\n"
            . '{{body}}';
    }

    public static function instance()
    {
        if (!isset(self::$instance)) {
            $object = __CLASS__;
            self::$instance = new $object;
        }
        return self::$instance;
    }

    public function getCleanSelf()
    {
        return $this->_clean_self;
    }

    public function addOptFromDb()
    {
        $query = 'SELECT * FROM ' . $this->getOpt('options_table');

        if ($this->db->Execute($query)) {
            while ($row = $this->db->ReadRecord()) {
                if ($row['optvalue'] != '') {
                    $this->AddOpt($row['optkey'], $row['optvalue']);
                }
            }
            $this->db->FreeResult();

            $query = 'SELECT * FROM ' . $this->getOpt('smilies_table');
            $this->db->Execute($query);
            while ($row = $this->db->ReadRecord()) {
                $this->smilies[$row['smiley']] = $row['image'];
            }
            $this->db->FreeResult();
        }

        return false;
    }

    public function addOpt($key, $value)
    {
        $this->_cfg[$key] = $value;
    }

    public function getOpt($opt_name, $returnLiteral = false)
    {
        if (isset($this->_cfg[$opt_name])) {
            $opt = $this->_cfg[$opt_name];
        } else {
            return false;
        }

        if (!$returnLiteral) {
            if ($opt === 0 || $opt == 'n' || $opt == 'no') {
                return false;
            }
            if ($opt === 1 || $opt == 'y' || $opt == 'yes') {
                return true;
            }
        }

        return $opt;
    }

    public function getHeader($pageTitle = null)
    {
        require_once('header.php');
    }

    public function getFooter($showNav = true)
    {
        require_once('footer.php');
    }

    public function attach($observer_in, $type = '')
    {
        $this->observers[$type] = $observer_in;
    }

    public function detach($observer_in)
    {
        foreach ($this->observers as $okey => $oval) {
            if ($oval == $observer_in) {
                unset($this->observers[$okey]);
            }
        }
    }

    public function notify($type = '')
    {
        foreach ($this->observers as $key => $obs) {
            if (empty($type) || $type == $key) {
                $obs->update($this);
            }
        }
    }

    public function reportErrors($err = '')
    {
        if (!empty($err)) {
            $this->addErr($err);
        }

        if (!empty($this->errors)) {

            if (count($this->errors) == 1 && empty($this->success)) {
                echo '<p class="error">' . array_pop($this->errors) . "</p>\n";
            } else {

                echo '<ul class="error">' . "\n";
                foreach ($this->errors as $msg) {
                    $msg = trim($msg);
                    if (!empty($msg)) {
                        echo '<li>' . $msg . "</li>\n";
                    }
                }
                echo "</ul>\n";
            }

            $this->errors = array();
        }
    }

    public function reportSuccess($success = '')
    {

        if (!empty($success)) {
            $this->addSuccess($success);
        }

        if (!empty($this->success)) {

            if (count($this->success) == 1 && empty($this->errors)) {
                echo '<p class="success">' . array_pop($this->success) . "</p>\n";
            } else {

                echo '<ul class="success">' . "\n";
                foreach ($this->success as $msg) {
                    $msg = trim($msg);
                    if (!empty($msg)) {
                        echo '<li>' . $msg . "</li>\n";
                    }
                }
                echo "</ul>\n";
            }

            $this->success = array();
        }
    }

    public function addErr($msg, $key = null, $type = '')
    {
        $this->errors[] = $msg;
        $this->notify($type);
    }

    public function getLastErr()
    {
        $value = end($this->errors);
        $key = key($this->errors);
        return array($value, $key);
    }

    public function addSuccess($msg, $key = null)
    {
        $this->success[] = $msg;
    }

    public function noErr()
    {
        return !(count($this->errors) > 0);
    }

    public function checkUpdates()
    {

        // check prism-perfect.net only once a week
        if ($this->getOpt('_last_update_check') <= date('Y-m-d', strtotime('-1 week'))) {

            if ($fsock = @fsockopen('prism-perfect.net', 80, $errno, $errstr, 10)) {

                @fwrite($fsock, "GET /fanupdate.txt HTTP/1.1\r\n");
                @fwrite($fsock, "Host: prism-perfect.net\r\nUser-Agent: FanUpdate/" . $this->getOpt('version') . "\r\n");
                @fwrite($fsock, "Connection: close\r\n\r\n");

                $get_info = false;
                while (!@feof($fsock)) {
                    if ($get_info) {
                        $version_info = @fread($fsock, 1024);
                        break;
                    }

                    if (@fgets($fsock, 1024) == "\r\n") {
                        $get_info = true;
                    }
                }
                @fclose($fsock);

            } else if ($errstr) {
                $this->ReportErrors('Connect socket error. Cannot check for updates.');
                return false;
            } else {
                $this->ReportErrors('Socket functions disabled. Cannot check for updates');
                return false;
            }

            // it comes from prism-perfect.net but it might still be unsafe!
            $version_info = clean_input($version_info);
            $this->AddOpt('_last_update_check', date('Y-m-d'));
            $this->AddOpt('_last_update_version', $version_info);
            $sql_version_info = $this->db->Escape($version_info);
            $this->db->Execute('UPDATE ' . $this->getOpt('options_table') . " SET optvalue='" . date('Y-m-d') . "' WHERE optkey='_last_update_check'");
            $this->db->Execute('UPDATE ' . $this->getOpt('options_table') . " SET optvalue='$sql_version_info' WHERE optkey='_last_update_version'");

        }

        if (version_compare($this->getOpt('version'), $this->getOpt('_last_update_version'), '<')) {
            $this->ReportErrors('Oh dear! This is not the latest version of FanUpdate. Please visit <a href="' . $this->getOpt('url') . '">' . $this->getOpt('url') . '</a> to download <strong>FanUpdate version ' . $this->getOpt('_last_update_version') . '</strong>.');
            return false;
        }

        //$this->ReportSuccess('This version of FanUpdate is <strong>up-to-date</strong>.<br />Last checked: '.date($this->getOpt('date_format'), strtotime($this->getOpt('_last_update_check'))));

        return true;
    }

    public function makeSmiley($txt, $img)
    {
        return '<img src="' . $this->getOpt('install_url') . '/img/' . $img . '" alt="' . $txt . '" />';
    }

    public function replaceSmilies($txt)
    {
        foreach ($this->smilies as $key => $value) {
            $pat = '!(^|\s)(' . preg_quote($key) . ')(\s|$)!m';
            $txt = preg_replace($pat, '$1' . $this->makeSmiley($key, $value) . '$3', $txt);
        }
        return $txt;
    }

    public function findSmileyImgs()
    {

        if ($handle = opendir($this->getOpt('install_path') . '/img/')) {

            /* This is the correct way to loop over the directory. */
            while (false !== ($file = readdir($handle))) {
                if (preg_match('/(\.gif|\.png|\.jpg)$/', $file)) {
                    $this->smiley_imgs[] = $file;
                }
            }

            closedir($handle);
        }
    }

    public function printSmileyImgDropdown($name, $this_img = '')
    {

        if (empty($this->smiley_imgs)) {
            $this->findSmileyImgs();
        }

        echo '<select id="' . $name . '" name="' . $name . '">';
        foreach ($this->smiley_imgs as $img) {
            echo '<option value="' . $img . '"';
            echo ' style="background: url(img/' . $img . ') no-repeat; padding: 0 0 0 20px;"';
            if ($this_img == $img) {
                echo ' selected="selected"';
            }
            echo '>' . $img . "</option>\n";
        }
        echo "</select>\n";
    }

    public function printCommentForm()
    {

        $clean = clean_input($_COOKIE);
        if (!isset($clean['fanuurl'])) {
            $clean['fanuurl'] = '';
        }
        if (!isset($clean['fanuemail'])) {
            $clean['fanuemail'] = '';
        }
        if (!isset($clean['fanuname'])) {
            $clean['fanuname'] = '';
        }

        $text = str_replace('{{fanuurl}}', $clean['fanuurl'], $this->getOpt('comment_form_template'));
        $text = str_replace(array('{{fanuemail}}', '{{fanuname}}'), array($clean['fanuemail'], $clean['fanuname']), $text);

        if ($this->getOpt('captcha_on')) {
            $text = str_replace('{{captcha_image}}', $this->getOpt('install_url') . '/captcha.php', $text);
        } else {
            $text = preg_replace('/<!(?:--[\s]*?CAPTCHA[\s]*?--\s*)?>[\s\S]*?<!(?:--[\s]*?END[\s]*?CAPTCHA[\s]*?--\s*)?>\s*/', '', $text);
        }

        if (!$this->getOpt('comment_moderation')) {
            $text = preg_replace('/<!(?:--[\s]*?MODERATION[\s]*?--\s*)?>[\s\S]*?<!(?:--[\s]*?END[\s]*?MODERATION[\s]*?--\s*)?>\s*/', '', $text);
        }

        echo $text;
    }

    public function printBlogFooter($listingid = null)
    {

        if (isset($listingid)) {
            $rss_url = $this->getOpt('install_url') . '/rss.php?c=' . $listingid;
        } else {
            $rss_url = $this->getOpt('install_url') . '/rss.php';
        }

        $text = str_replace('{{main_url}}', $this->getCleanSelf(), $this->getOpt('footer_template'));
        $text = str_replace(array('{{archive_url}}', '{{rss_url}}'), array($this->getCleanSelf() . '?view=archive', $rss_url), $text);
        $text = str_replace(array('{{fanupdate_url}}', '{{fanupdate_version}}'), array($this->getOpt('url'), $this->getOpt('version')), $text);

        echo $text;
    }

    public function login()
    {

        session_start();
        header('Cache-control: private');

        if (isset($_GET['action']) && $_GET['action'] == 'logout') {
            $_SESSION = array();
            setcookie('fu_username', '', time() - 36000, '/');
            setcookie('fu_password', '', time() - 36000, '/');
            session_destroy();
            header('Location: ' . $this->getOpt('install_url') . '/index.php');
            exit;
        }

        $showform = true;

        if (isset($_POST['action']) && $_POST['action'] == 'login') {

            $test_user = $_POST['username'];
            $test_pass = md5($_POST['password']);

        } elseif (isset($_SESSION['username'], $_SESSION['password'])) {

            $test_user = $_SESSION['username'];
            $test_pass = $_SESSION['password'];

        } elseif (isset($_COOKIE['fu_username'], $_COOKIE['fu_password'])) {

            $test_user = $_COOKIE['fu_username'];
            $test_pass = $_COOKIE['fu_password'];
        }

        if (isset($test_user, $test_pass)) {

            if (($test_user == $this->getOpt('admin_username')) && ($test_pass == $this->getOpt('admin_password'))) {
                $showform = false;
                $_SESSION['username'] = $test_user;
                $_SESSION['password'] = $test_pass;

                if (isset($_POST['remember_me'])) {
                    $cookie_life = time() + 31536000; // Life of one year
                    setcookie('fu_username', $test_user, $cookie_life, '/');
                    setcookie('fu_password', $test_pass, $cookie_life, '/');
                }

            } else {
                $showform = true;
                $this->addErr('Invalid login! Please try again.');
                setcookie('fu_username', '', time() - 36000, '/');
                setcookie('fu_password', '', time() - 36000, '/');
            }

        }

        if ($showform) {

            $this->getHeader('Login');

            ?>

            <form action="<?php echo clean_input($_SERVER['REQUEST_URI']); ?>" method="post">

                <?php $this->reportErrors(); ?>

                <p><label for="username">Username:</label>
                    <input type="text" id="username" name="username" size="20" tabindex="1"/></p>

                <p><label for="password">Password:</label>
                    <input type="password" id="password" name="password" size="20" tabindex="2"/></p>

                <p id="loginButton"><input type="submit" name="action" value="login" class="submit" tabindex="4"/>
                    <span><input type="checkbox" id="remember_me" name="remember_me" value="1" tabindex="3"/>
<label for="remember_me" class="checkbox">Remember?</label></span></p>

            </form>

            <script type="text/javascript">
                $('username').focus();
            </script>

            <?php

            $this->getFooter(false);

            exit;

        }
    }

    public function printCredits()
    {
        ?>
        Powered by <a href="<?= $this->GetOpt('newUrl') ?>" target="_blank"
                      title="PHP scripts collection: Enthusiast, CodeSort, SiteSkin for PHP 7">CodeSort <?= $this->GetOpt('version') ?></a> (2020 - ...) /
        Original script by <a href="<?= $this->GetOpt('url') ?>" target="_blank"><?= $this->GetOpt('url') ?></a>
        <?php
    }


    public function printBlog($query, $main_limit = 5, $single_page = false)
    {

        $this->db->ExecutePaginate($query, $main_limit);

        if ($this->db->NumRows() > 0) {

            while ($row = $this->db->ReadRecordPaginate()) {

                $post = new FanUpdate_Post($row, $this);
                $post->getCatFromDb();

                if ($post->allowComments()) {

                    $query_comments = 'SELECT * FROM ' . $this->getOpt('comments_table') . '
                      WHERE entry_id=' . $post->getID() . ' AND approved > 0
                      ORDER BY added ASC';

                    $this->db->Execute($query_comments);
                    $num_comments = $this->db->NumRows();

                    $post->addParam('num_comments', $num_comments);
                }

                echo '<div id="post' . $post->getID() . '" class="post">' . "\n";
                if ($single_page) {
                    $post->printPost();
                } else {
                    $post->printPost(true);
                }
                echo "</div><!-- END .post -->\n";

                // ____________________________________________________________ COMMENTS

                if ($single_page && $post->allowComments()) {

                    echo '<div id="comments">' . "\n";

                    if (!empty($num_comments)) {

                        if ($num_comments == 1) {
                            echo "<h3>1 Comment</h3>\n";
                        } else {
                            echo '<h3>' . $num_comments . " Comments</h3>\n";
                        }

                        while ($comment = $this->db->ReadRecord()) {

                            $cmt = new FanUpdate_Comment($comment, $this);

                            $class = (isset($class) && $class == 'even') ? 'odd' : 'even';
                            $class_str = 'comment ' . $class;

                            if (strtolower($cmt->getEmail()) == strtolower($this->getOpt('admin_email'))) {
                                $class_str .= ' author';
                            }

                            echo '<div id="comment' . $cmt->getID(), '" class="' . $class_str . '">' . "\n";
                            $cmt->printComment();
                            echo "</div><!-- END .comment -->\n";
                        }

                    } else {
                        echo "<h3>Comments</h3>\n";
                        echo "<p>No comments yet.</p>\n";
                    }

                    echo '<div id="newComment" class="comment"></div>' . "\n";

                    echo '<p><a class="rss" href="' . $post->getCommentsFeedUrl() . '">Feed for comments on this post.</a></p>' . "\n";
                    echo "</div><!-- END #comments -->\n";

                    ?>

                    <div class="comments-form">

                        <form id="comments-form" action="<?php echo $this->getOpt('install_url'); ?>/process.php"
                              method="post">
                            <input type="hidden" name="entry" value="<?php echo $post->getID(); ?>"/>
                            <input type="hidden" name="returnto"
                                   value="http://<?php echo clean_input($_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']); ?>"/>

                            <?php $this->printCommentForm(); ?>

                        </form><!-- END #comments-form -->

                        <?php if ($this->getOpt('ajax_comments')) { ?>
                            <script type="text/javascript">
                                var fu_url = '<?php echo $this->getOpt('install_url'); ?>';
                            </script>
                            <script type="text/javascript"
                                    src="<?php echo $this->getOpt('install_url'); ?>/js/fanupdate.js"></script>
                            <script type="text/javascript"
                                    src="<?php echo $this->getOpt('install_url'); ?>/js/fanupdate-comment-submit.js"></script>
                        <?php } ?>

                    </div><!-- END .comments-form -->

                    <?php

                }

                if ($post->allowComments()) {
                    $this->db->FreeResult();
                }
            }

            echo '<p class="paginate">';
            $this->db->PrintPaginate(false, '&#171; Previous page', 'Next page &#187;');
            echo '</p><!-- END .paginate -->';

        } else {
            echo "<p>No entries found.</p>\n";
        }

        $this->db->FreeResult();

    }

}
