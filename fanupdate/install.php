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

$clean = array();

$fu->getHeader('Install');

?>

<h2>Install v<?php echo $fu->getOpt('version'); ?></h2>

<?php

$fu->db->Connect();

$version_installed = 0;
$installer_version = '2.2.1b2'; // may be less than script version if no db changes needed

// check for current version installed

$query_cat = 'SELECT * FROM ' . $fu->getOpt('blog_table') . ' WHERE listingid=0 LIMIT 1';

if (@$fu->db->Execute($query_cat)) {
    $version_installed = 1.5;
} else {

    $query_opt = 'SELECT * FROM ' . $fu->getOpt('options_table') . ' WHERE id=1 LIMIT 1';
    @$fu->db->Execute($query_opt);

    if ($fu->db->NumRows() > 0) {
        $version_installed = 1.6;
        $row_opt = $fu->db->GetRecord();
    } else {

        @$fu->addOptFromDb();

        if (!$version_installed = $fu->getOpt('_db_version')) {
            if ($fu->getOpt('comment_form_template')) {
                $version_installed = 2.1;
            } elseif ($fu->getOpt('admin_email')) {
                $version_installed = 2.0;
            }
        }
    }
}

// auto-detect paths

$dir = __DIR__;
// correct for weird DreamHost .name thingy
$dir = preg_replace('#/\.([a-z]+)#', '', $dir);
// fix Windows dir slashes
$dir = str_replace('\\', '/', $dir);

$row_opt['install_path'] = $dir;

// figure absolute URL w/out trailing slash
$dir = str_replace($_SERVER['DOCUMENT_ROOT'], '', $dir);

// The URL where you've uploaded this script. NO TRAILING SLASH!
$row_opt['install_url'] = 'http://' . $_SERVER['SERVER_NAME'] . $dir;

// settings

$setting[0]['optkey'] = 'admin_email';
$setting[0]['optvalue'] = (!empty($row_opt['admin_email'])) ? $row_opt['admin_email'] : 'me@example.com';
$setting[0]['optdesc'] = 'Your email address, so you can be notified of new comments.';
$setting[0]['version'] = 2.0;

$setting[1]['optkey'] = 'site_name';
$setting[1]['optvalue'] = (!empty($row_opt['collective_name'])) ? $row_opt['collective_name'] : 'My Site';
$setting[1]['optdesc'] = 'The name of your site or blog.';
$setting[1]['version'] = 2.0;

$setting[2]['optkey'] = 'blog_page';
$setting[2]['optvalue'] = (!empty($row_opt['collective_updates_page'])) ? $row_opt['collective_updates_page'] : 'http://example.com/updates.php';
$setting[2]['optdesc'] = 'The URL of your blog page (needed for RSS feeds).';
$setting[2]['version'] = 2.0;

$setting[3]['optkey'] = 'install_path';
$setting[3]['optvalue'] = $row_opt['install_path'];
$setting[3]['optdesc'] = 'The full server path to your FanUpdate directory. NO trailing slash. Ex: /home/username/public_html/fanupdate';
$setting[3]['version'] = 2.0;

$setting[4]['optkey'] = 'install_url';
$setting[4]['optvalue'] = $row_opt['install_url'];
$setting[4]['optdesc'] = 'The URL to your FanUpdate directory. NO trailing slash. Ex: http://example.com/fanupdate';
$setting[4]['version'] = 2.0;

$setting[5]['optkey'] = 'comments_on';
$setting[5]['optvalue'] = (!empty($row_opt['comments_on'])) ? $row_opt['comments_on'] : 'y';
$setting[5]['optdesc'] = 'Allow comments? y or n';
$setting[5]['version'] = 2.0;

$setting[6]['optkey'] = 'date_format';
$setting[6]['optvalue'] = 'M j, Y';
$setting[6]['optdesc'] = 'PHP date format string. See options here: http://php.net/date';
$setting[6]['version'] = 2.0;

$setting[7]['optkey'] = 'email_new_comments';
$setting[7]['optvalue'] = 'y';
$setting[7]['optdesc'] = 'Email you about new comments? y or n';
$setting[7]['version'] = 2.0;

$setting[8]['optkey'] = 'comment_moderation';
$setting[8]['optvalue'] = 'n';
$setting[8]['optdesc'] = 'Hold new comments for moderation? y or n';
$setting[8]['version'] = 2.0;

$setting[9]['optkey'] = 'captcha_on';
$setting[9]['optvalue'] = 'n';
$setting[9]['optdesc'] = 'Protect comments with captcha? y or n';
$setting[9]['version'] = 2.0;

$setting[10]['optkey'] = 'gravatar_on';
$setting[10]['optvalue'] = 'y';
$setting[10]['optdesc'] = 'Use Gravatars? y or n';
$setting[10]['version'] = 2.0;

$setting[11]['optkey'] = 'gravatar_default';
$setting[11]['optvalue'] = $row_opt['install_url'] . '/gravatar.png';
$setting[11]['optdesc'] = 'URL of default Gravatar image. Ex: http://example.com/fanupdate/gravatar.png';
$setting[11]['version'] = 2.0;

$setting[12]['optkey'] = 'gravatar_size';
$setting[12]['optvalue'] = 80;
$setting[12]['optdesc'] = 'Gravatar image dimension in pixels (80 max).';
$setting[12]['version'] = 2.0;

$setting[13]['optkey'] = 'gravatar_rating';
$setting[13]['optvalue'] = 'G';
$setting[13]['optdesc'] = 'Highest allowable Gravatar image rating: G, PG, R, X';
$setting[13]['version'] = 2.0;

$setting[14]['optkey'] = 'entry_template';
$setting[14]['optvalue'] = '<h2><a href="{{url}}" title="permanent link to this post">{{title}}</a></h2>

<p class="catfile">Posted {{date}}. Filed under {{category}}. {{comment_link}}</p>

{{body}}';
$setting[14]['optdesc'] = 'See readme.txt for template variables.';
$setting[14]['version'] = 2.0;

$setting[15]['optkey'] = 'comment_template';
$setting[15]['optvalue'] = '{{gravatar}}

<p class="commenter">On 
<a href="#comment{{id}}" title="permanent link to this comment">{{date}}</a>
{{name}} said:</p>

{{body}}';
$setting[15]['optdesc'] = 'See readme.txt for template variables.';
$setting[15]['version'] = 2.0;

$setting[16]['optkey'] = 'num_per_page';
$setting[16]['optvalue'] = '20';
$setting[16]['optdesc'] = 'Number of items displayed per page for pagination.';
$setting[16]['version'] = 2.0;

$setting[17]['optkey'] = 'abstract_word_count';
$setting[17]['optvalue'] = '0';
$setting[17]['optdesc'] = 'The number of words to display for post summaries. 0 to turn off.';
$setting[17]['version'] = 2.0;

$setting[18]['optkey'] = 'comment_form_template';
$setting[18]['optvalue'] = '<h3 id="postcomment">Post A Comment</h3>

<!-- MODERATION -->
<p id="cmt-moderation"><strong>Comment moderation is currently turned on.</strong> Your comment will not be displayed until it has been approved by the site owner.</p>
<!-- END MODERATION -->

<p><label for="name">Name:</label>
<input type="text" id="name" name="name" maxlength="20" size="25" value="{{fanuname}}" />
<label for="remember_me" class="checkbox"><input type="checkbox" id="remember_me" name="remember_me" value="1" checked="checked" /> Remember?</label></p>

<p><label for="email">Email:</label>
<input type="text" id="email" name="email" maxlength="70" size="25" value="{{fanuemail}}" /></p>

<p><label for="url">URL:</label>
<input type="text" id="url" name="url" maxlength="70" size="25" value="{{fanuurl}}" /></p>

<p><label for="myta">Comment:</label>
<textarea id="myta" name="comment" cols="50" rows="8"></textarea></p>

<!-- CAPTCHA -->
<p><label for="captcha">Captcha:</label>
<img id="captcha-img" src="{{captcha_image}}" alt="" />
<input type="text" id="captcha" name="captcha" /></p>
<!-- END CAPTCHA -->

<p><input type="submit" id="submit" name="submit_comment" value="Post Comment" class="submit" /></p>

<p id="cmt-rules">Your email is only for accessing <a href="http://www.gravatar.com/">gravatar.com</a>. No <abbr title="HyperText Markup Language">HTML</abbr> allowed; some formatting can be applied via the buttons above the textarea.</p>';
$setting[18]['optdesc'] = 'Customize your comment form. Don\'t change the names of the inputs or the CAPTCHA and MODERATION comments, but everything else may be modified. See readme.txt for template variables.';
$setting[18]['version'] = 2.1;

$setting[19]['optkey'] = 'footer_template';
$setting[19]['optvalue'] = '<div class="archivelink">
<form action="{{main_url}}" method="get">
<p>
<a href="{{main_url}}">main</a> &middot;
<a href="{{archive_url}}">archive</a> &middot;
<a class="rss" href="{{rss_url}}">feed</a> &middot;
<input type="text" name="q" value="" />
<input type="submit" value="Search" class="button" />
</p>
</form>
</div><!-- END .archivelink -->

<div class="credit">
<p>Powered by <a href="{{fanupdate_url}}" target="_blank" class="ext">FanUpdate {{fanupdate_version}}</a> / Original script by <a href="{{fanupdate_original_url}}" target="_blank" class="ext">{{fanupdate_original_url}}</a></p></p>
</div><!-- END .credit -->';
$setting[19]['optdesc'] = 'Put your footer blog navigation here. See readme.txt for template variables.';
$setting[19]['version'] = 2.1;

$setting[20]['optkey'] = '_last_update_check';
$setting[20]['optvalue'] = date('Y-m-d');
$setting[20]['optdesc'] = 'Last time prism-perfect.net was checked for latest release.';
$setting[20]['version'] = 2.0;

$setting[21]['optkey'] = '_last_update_version';
$setting[21]['optvalue'] = $fu->getOpt('version');
$setting[21]['optdesc'] = 'Most recent stable version.';
$setting[21]['version'] = 2.0;

$setting[22]['optkey'] = 'points_scoring';
$setting[22]['optvalue'] = 'y';
$setting[22]['optdesc'] = 'Use point scoring system to block spam. y or n';
$setting[22]['version'] = '2.2b';

$setting[23]['optkey'] = 'points_approval_threshold';
$setting[23]['optvalue'] = 1;
$setting[23]['optdesc'] = 'Minimum points for automatic comment approval. Default: 1';
$setting[23]['version'] = '2.2b';

$setting[24]['optkey'] = 'points_pending_threshold';
$setting[24]['optvalue'] = -4;
$setting[24]['optdesc'] = 'Minimum points for comment to be moderated. Any less points and it is spam. Default: -4';
$setting[24]['version'] = '2.2b';

$setting[25]['optkey'] = '_db_version';
$setting[25]['optvalue'] = $fu->getOpt('version');
$setting[25]['optdesc'] = 'Version of current database schema.';
$setting[25]['version'] = '2.2b';

$setting[26]['optkey'] = 'timezone_offset';
$setting[26]['optvalue'] = 0;
$setting[26]['optdesc'] = 'Hours difference between your location and GMT. Default: 0';
$setting[26]['version'] = '2.2b3';

$setting[27]['optkey'] = '_server_tz_offset';
$setting[27]['optvalue'] = date('Z') / 3600;
$setting[27]['optdesc'] = 'Hours difference between server time and GMT.';
$setting[27]['version'] = '2.2b3';

$setting[28]['optkey'] = 'ajax_comments';
$setting[28]['optvalue'] = 'y';
$setting[28]['optdesc'] = 'Use ajax commenting? Turn off if you have problems. y or n';
$setting[28]['version'] = '2.2.1b2';

// check that tables don't exist for fresh install

if ($version_installed == 0) {
    $tables = array();
    $query = 'SHOW TABLES';
    $fu->db->Execute($query);
    while ($row = $fu->db->ReadRecord()) {
        $table = array_shift($row);
        if (!$fu->getOpt('collective_script') || $table != $fu->getOpt('collective_table')) {
            if (in_array($table, $fu->getOpt('tables'), true)) {
                $fu->addErr('Table <strong>' . $table . '</strong> already exists. Please choose a different name in your FanUpdate blog-config.php');
            }
        }
    }
}

// check that collective_table is configured correctly

if ($fu->getOpt('collective_script')) {

    $query = "SHOW TABLES LIKE '" . $fu->getOpt('collective_table') . "'";

    if (!$fu->db->GetFirstCell($query)) {
        $fu->addErr('Could not find collective_table <strong>' . $fu->getOpt('collective_table') . '</strong>. Please check your settings in blog-config.php');
    }
}

// installed version is current

if (version_compare($version_installed, $installer_version, '>=')) {
    $fu->addErr('You currently have FanUpdate version <strong>' . $version_installed . '</strong> installed, so you <strong>do not</strong> need to run this installation script; your database should already be configured to run FanUpdate ' . $fu->getOpt('version') . '. <strong>DELETE</strong> this file from your server and continue on <a href="index.php">to the ADMIN panel.</a>');
}

if (!$fu->noErr()) {
    $fu->reportErrors();
} else if (isset($_POST['upgrade']) || isset($_POST['fresh_install'])) {

// _______________________________________________ FRESH INSTALL

    if ($version_installed == 0) {

        // ___________________________________________ create blog table

        $query = 'CREATE TABLE ' . $fu->getOpt('blog_table') . " (
      `entry_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
      `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      `title` varchar(50) NOT NULL DEFAULT '',
      `body` text NOT NULL,
      `is_public` tinyint(1) unsigned NOT NULL DEFAULT '1',
      `comments_on` tinyint(1) unsigned NOT NULL DEFAULT '1',
      PRIMARY KEY (`entry_id`),
      FULLTEXT (title,body)
    ) ENGINE=MyISAM";

        if ($fu->db->Execute($query)) {
            $fu->AddSuccess('Table <strong>' . $fu->getOpt('blog_table') . '</strong> created.');
        } else {
            $fu->AddErr('Table <strong>' . $fu->getOpt('blog_table') . '</strong> not created!');
        }

        // ___________________________________________ create comments table

        $query = 'CREATE TABLE ' . $fu->getOpt('comments_table') . " (
      `comment_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
      `entry_id` int(6) unsigned NOT NULL DEFAULT '0',
      `name` varchar(30) NOT NULL DEFAULT '',
      `email` varchar(100) NOT NULL DEFAULT '',
      `url` varchar(100) NOT NULL DEFAULT '',
      `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      `comment` text NOT NULL,
      `approved` tinyint(1) NOT NULL DEFAULT '0',
	  `points` int(4) NOT NULL DEFAULT '0',
      PRIMARY KEY (`comment_id`),
      KEY `entry` (`entry_id`),
      KEY `approved` (`approved`),
      FULLTEXT (name,comment)
    ) ENGINE=MyISAM";

        if ($fu->db->Execute($query)) {
            $fu->AddSuccess('Table <strong>' . $fu->getOpt('comments_table') . '</strong> created.');
        } else {
            $fu->AddErr('Table <strong>' . $fu->getOpt('comments_table') . '</strong> not created!');
        }

        // ___________________________________________ create category table

        if (!$fu->getOpt('collective_script')) {
            $query = 'CREATE TABLE ' . $fu->getOpt('collective_table') . ' (
          `' . $fu->getOpt('col_id') . '` int(6) unsigned NOT NULL AUTO_INCREMENT,
          `' . $fu->getOpt('col_subj') . "` varchar(50) NOT NULL DEFAULT '',
          PRIMARY KEY (`" . $fu->getOpt('col_id') . '`)
        ) ENGINE=MyISAM';

            if ($fu->db->Execute($query)) {
                $fu->AddSuccess('Table <strong>' . $fu->getOpt('collective_table') . '</strong> created.');
            } else {
                $fu->AddErr('Table <strong>' . $fu->getOpt('collective_table') . '</strong> not created!');
            }
        }
    }

// _______________________________________________ FRESH INSTALL / UPGRADE FROM 1.5

    if (version_compare($version_installed, 1.5, '<=')) {

        // ___________________________________________ create blog2category table

        $query = 'CREATE TABLE ' . $fu->getOpt('catjoin_table') . " (
      `entry_id` int(6) unsigned NOT NULL DEFAULT '0',
      `cat_id` int(6) unsigned NOT NULL DEFAULT '0',
      PRIMARY KEY (`entry_id`,`cat_id`)
    ) ENGINE=MyISAM";

        if ($fu->db->Execute($query)) {
            $fu->AddSuccess('Table <strong>' . $fu->getOpt('catjoin_table') . '</strong> created.');
        } else {
            $fu->AddErr('Table <strong>' . $fu->getOpt('catjoin_table') . '</strong> not created!');
        }
    }

// _______________________________________________ UPGRADE FROM 1.5 ONLY

    if (version_compare($version_installed, 1.5, '=')) {

        // ___________________________________________ convert 1.5 blog to new category scheme

        $query = 'SELECT * FROM ' . $fu->getOpt('blog_table');

        if ($fu->db->Execute($query)) {
            while ($row = $fu->db->ReadRecord()) {
                $id = $row['id'];
                $listingid = $row['listingid'];

                $query = 'INSERT INTO ' . $fu->getOpt('catjoin_table') . " (entry_id,cat_id) VALUES ($id,$listingid)";
            }
            $fu->db->FreeResult();
            $fu->AddSuccess('Category relationships transfered.');
        } else {
            $fu->AddErr('Category relationships not transfered!');
        }

        $query = 'ALTER TABLE ' . $fu->getOpt('blog_table') . ' DROP `listingid`';

        if ($fu->db->Execute($query)) {
            $fu->AddSuccess('Listingid column dropped.');
        } else {
            $fu->AddErr('Listingid column not dropped!');
        }

        if (!$fu->getOpt('collective_script')) {
            $query = 'ALTER TABLE ' . $fu->getOpt('collective_table') . ' DROP INDEX `fl_id`, ADD PRIMARY KEY ( `fl_id` )';

            if ($fu->db->Execute($query)) {
                $fu->AddSuccess('Old index fl_id changed to PK.');
            } else {
                $fu->AddErr('Old index fl_id not changed to PK!');
            }
        }
    }

// _______________________________________________ UPGRADE FROM 1.6/1.5

    if ($version_installed > 0 && version_compare($version_installed, 1.6, '<=')) {

        // ___________________________________________ modify old category table scheme

        if (!$fu->getOpt('collective_script')) {
            $query = 'ALTER TABLE ' . $fu->getOpt('collective_table') . '
			CHANGE `fl_id` `' . $fu->getOpt('col_id') . '` INT( 6 ) UNSIGNED NOT NULL AUTO_INCREMENT,
			CHANGE `fl_subject` `' . $fu->getOpt('col_subj') . "` VARCHAR( 50 ) NOT NULL DEFAULT ''";

            if ($fu->db->Execute($query)) {
                $fu->AddSuccess('Old table <strong>' . $fu->getOpt('collective_table') . '</strong> modified.');
            } else {
                $fu->AddErr('Old table <strong>' . $fu->getOpt('collective_table') . '</strong> not modified!');
            }
        }

        // ___________________________________________ modify old blog table scheme

        $query = 'ALTER TABLE ' . $fu->getOpt('blog_table') . "
      ADD `is_public` TINYINT( 1 ) NOT NULL DEFAULT '1',
      ADD `comments_on` TINYINT( 1 ) NOT NULL DEFAULT '1',
      ADD FULLTEXT (title,body),
      CHANGE `id` `entry_id` INT( 6 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
      CHANGE `title` `title` VARCHAR( 50 ) NOT NULL ,
      CHANGE `entry` `body` TEXT NOT NULL,
      ADD `added` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `timestamp`";

        if ($fu->db->Execute($query)) {
            $fu->AddSuccess('Old table <strong>' . $fu->getOpt('blog_table') . '</strong> modified.');
        } else {
            $fu->AddErr('Old table <strong>' . $fu->getOpt('blog_table') . '</strong> not modified!');
        }

        $query = 'UPDATE ' . $fu->getOpt('blog_table') . ' SET `added`=FROM_UNIXTIME(`timestamp`)';

        if ($fu->db->Execute($query)) {
            $fu->AddSuccess('Timestamps converted to datetime');
        } else {
            $fu->AddErr('Timestamps not converted to datetime!');
        }

        $query = 'ALTER TABLE ' . $fu->getOpt('blog_table') . ' DROP `timestamp`';

        if ($fu->db->Execute($query)) {
            $fu->AddSuccess('Timestamp column dropped.');
        } else {
            $fu->AddErr('Timestamp column not dropped!');
        }

        // ___________________________________________ modify old comment table scheme

        $query = 'ALTER TABLE ' . $fu->getOpt('comments_table') . "
      ADD FULLTEXT (name,comment),
      CHANGE `id` `comment_id` INT( 6 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
      CHANGE `entry` `entry_id` INT( 6 ) UNSIGNED NOT NULL DEFAULT '0',
      CHANGE `approved` `approved` TINYINT( 1 ) NOT NULL DEFAULT '0',
      ADD `added` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `timestamp`";

        if ($fu->db->Execute($query)) {
            $fu->AddSuccess('Old table <strong>' . $fu->getOpt('comments_table') . '</strong> modified.');
        } else {
            $fu->AddErr('Old table <strong>' . $fu->getOpt('comments_table') . '</strong> not modified!');
        }

        $query = 'UPDATE ' . $fu->getOpt('comments_table') . ' SET `added`=FROM_UNIXTIME(`timestamp`)';

        if ($fu->db->Execute($query)) {
            $fu->AddSuccess('Timestamps converted to datetime');
        } else {
            $fu->AddErr('Timestamps not converted to datetime!');
        }

        $query = 'ALTER TABLE ' . $fu->getOpt('comments_table') . ' DROP `timestamp`';

        if ($fu->db->Execute($query)) {
            $fu->AddSuccess('Timestamp column dropped.');
        } else {
            $fu->AddErr('Timestamp column not dropped!');
        }

        // _______________________________________________ UPGRADE FROM 1.6 ONLY

        if (version_compare($version_installed, 1.6, '=')) {

            // ___________________________________________ modify old blog2category table scheme

            $query = 'ALTER TABLE ' . $fu->getOpt('catjoin_table') . '
          DROP `id`,
          DROP INDEX `entry`,
          CHANGE `entry` `entry_id` INT( 6 ) UNSIGNED NOT NULL,
          CHANGE `listingid` `cat_id` INT( 6 ) UNSIGNED NOT NULL,
          ADD PRIMARY KEY ( `entry_id` , `cat_id` )';

            if ($fu->db->Execute($query)) {
                $fu->AddSuccess('Old table <strong>' . $fu->getOpt('catjoin_table') . '</strong> modified.');
            } else {
                $fu->AddErr('Old table <strong>' . $fu->getOpt('catjoin_table') . '</strong> not modified!');
            }
        }

        // ___________________________________________ delete old options table

        $query = 'DROP TABLE ' . $fu->getOpt('options_table');

        if ($fu->db->Execute($query)) {
            $fu->AddSuccess('Old table <strong>' . $fu->getOpt('options_table') . '</strong> dropped.');
        } else {
            $fu->AddErr('Old table <strong>' . $fu->getOpt('options_table') . '</strong> not dropped!');
        }
    }

// _______________________________________________ FRESH INSTALL OR UPGRADE FROM < 2

    if (version_compare($version_installed, 2.0, '<=')) {

        // ___________________________________________ create new options table

        $query = 'CREATE TABLE ' . $fu->getOpt('options_table') . ' (
      `optkey` varchar(30) NOT NULL,
      `optvalue` TEXT NOT NULL,
      `optdesc` TEXT NOT NULL,
      PRIMARY KEY (`optkey`)
    ) ENGINE=MyISAM';

        if ($fu->db->Execute($query)) {
            $fu->AddSuccess('Table <strong>' . $fu->getOpt('options_table') . '</strong> created.');
        } else {
            $fu->AddErr('Table <strong>' . $fu->getOpt('options_table') . '</strong> not created!');
        }

        // ___________________________________________ create blacklist table

        $query = 'CREATE TABLE ' . $fu->getOpt('blacklist_table') . ' (
      `badword` varchar(50) NOT NULL,
      PRIMARY KEY (`badword`)
    ) ENGINE=MyISAM';

        if ($fu->db->Execute($query)) {
            $fu->AddSuccess('Table <strong>' . $fu->getOpt('blacklist_table') . '</strong> created.');
        } else {
            $fu->AddErr('Table <strong>' . $fu->getOpt('blacklist_table') . '</strong> not created!');
        }

        $query = 'INSERT INTO ' . $fu->getOpt('blacklist_table') . " (`badword`) VALUES 
      ('blackjack'),
      ('casino'),
      ('cialis'),
      ('diazepam'),
      ('gambling'),
      ('hoodia'),
      ('hydrocodone'),
      ('kasino'),
      ('levitra'),
      ('phentermine'),
      ('ringtones'),
      ('viagra'),
      ('webcam')";

        if ($fu->db->Execute($query)) {
            $fu->AddSuccess('Blacklist words added.');
        } else {
            $fu->AddErr('Blacklist words not added!');
        }

        // ___________________________________________ create category options table

        $query = 'CREATE TABLE ' . $fu->getOpt('catoptions_table') . ' (
      `cat_id` INT( 6 ) UNSIGNED NULL,
      `comments_on` TINYINT( 1 ) UNSIGNED NULL,
      `date_format` VARCHAR( 30 ) NULL,
      `gravatar_on` TINYINT( 1 ) UNSIGNED NULL,
      `gravatar_default` VARCHAR( 100 ) NULL,
      `gravatar_size` VARCHAR( 2 ) NULL,
      `gravatar_rating` VARCHAR( 2 ) NULL,
      `entry_template` TEXT NULL ,
      `comment_template` TEXT NULL ,
      PRIMARY KEY ( `cat_id` )
    ) ENGINE = MYISAM';

        if ($fu->db->Execute($query)) {
            $fu->AddSuccess('Table <strong>' . $fu->getOpt('catoptions_table') . '</strong> created.');
        } else {
            $fu->AddErr('Table <strong>' . $fu->getOpt('catoptions_table') . '</strong> not created!');
        }
    }

// _______________________________________________ UPGRADE FROM < 2.1

    if ($version_installed > 0 && version_compare($version_installed, 2.1, '<')) {

        // ___________________________________________ clean up blog2category table

        $query = 'DELETE j.* FROM ' . $fu->getOpt('catjoin_table') . ' j LEFT JOIN ' . $fu->getOpt('collective_table') . ' c ON j.cat_id=c.' . $fu->getOpt('col_id') . ' WHERE c.' . $fu->getOpt('col_id') . ' IS NULL';

        if ($fu->db->Execute($query)) {
            $fu->AddSuccess('<strong>' . $fu->db->AffectedRows() . '</strong> old post relations for deleted categories cleaned up.');
        } else {
            $fu->AddErr('Old post relations for deleted categories not cleaned up!');
        }
    }

// _______________________________________________ FRESH INSTALL OR UPGRADE FROM < 2.1

    if (version_compare($version_installed, 2.1, '<')) {

        // ___________________________________________ create smilies table

        $query = 'CREATE TABLE ' . $fu->getOpt('smilies_table') . ' (
      `smiley` VARCHAR( 10 ) NOT NULL,
      `image` VARCHAR( 50 ) NOT NULL,
      PRIMARY KEY (`smiley`)
    ) ENGINE = MYISAM';

        if ($fu->db->Execute($query)) {
            $fu->AddSuccess('Table <strong>' . $fu->getOpt('smilies_table') . '</strong> created.');
        } else {
            $fu->AddErr('Table <strong>' . $fu->getOpt('smilies_table') . '</strong> not created!');
        }

        $query = 'INSERT INTO ' . $fu->getOpt('smilies_table') . " (`smiley`, `image`) VALUES 
      (':)', 'emoticon_smile.png'),
      (':D', 'emoticon_grin.png'),
      ('XD', 'emoticon_evilgrin.png'),
      (':O', 'emoticon_surprised.png'),
      (':P', 'emoticon_tongue.png'),
      (':(', 'emoticon_unhappy.png'),
      (';D', 'emoticon_wink.png')";

        if ($fu->db->Execute($query)) {
            $fu->AddSuccess('Default smilies added.');
        } else {
            $fu->AddErr('Default smilies not added!');
        }
    }

// _______________________________________________ UPGRADE FROM < 2.2b

    if ($version_installed > 0 && version_compare($version_installed, '2.2b', '<')) {

        // ___________________________________________ rename some stupid option names

        $query = 'UPDATE ' . $fu->getOpt('options_table') . " SET optkey='site_name' WHERE optkey='collective_name'";

        if ($fu->db->Execute($query)) {
            $fu->AddSuccess('Updated option name for <strong>site_name</strong>');
        } else {
            $fu->AddErr('Failed to update option name for <strong>site_name</strong>.');
        }

        $query = 'UPDATE ' . $fu->getOpt('options_table') . " SET optkey='blog_page' WHERE optkey='collective_updates_page'";

        if ($fu->db->Execute($query)) {
            $fu->AddSuccess('Updated option name for <strong>blog_page</strong>');
        } else {
            $fu->AddErr('Failed to update option name for <strong>blog_page</strong>.');
        }

        $query = 'UPDATE ' . $fu->getOpt('options_table') . " SET optkey='install_path' WHERE optkey='install_folder'";

        if ($fu->db->Execute($query)) {
            $fu->AddSuccess('Updated option name for <strong>install_path</strong>');
        } else {
            $fu->AddErr('Failed to update option name for <strong>install_path</strong>.');
        }

        // add spam points

        $query = 'ALTER TABLE ' . $fu->getOpt('comments_table') . "
      ADD `points` INT(4) NOT NULL DEFAULT '0' AFTER `approved`";

        if ($fu->db->Execute($query)) {
            $fu->AddSuccess('Old table <strong>' . $fu->getOpt('comments_table') . '</strong> modified.');
        } else {
            $fu->AddErr('Old table <strong>' . $fu->getOpt('comments_table') . '</strong> not modified!');
        }

        // remove {{wysiwyg}} template tag

        $query = 'UPDATE ' . $fu->getOpt('options_table') . " SET optvalue=REPLACE(optvalue, '{{wysiwyg}}<br />', '') WHERE optkey='comment_form_template'";

        if ($fu->db->Execute($query)) {
            if ($fu->db->AffectedRows() > 0) {
                $fu->AddSuccess('Removed old {{wysiwyg}} template tag.');
            }
        } else {
            $fu->AddErr('Old {{wysiwyg}} template tag not removed!');
        }

        $query = 'UPDATE ' . $fu->getOpt('options_table') . " SET optvalue=REPLACE(optvalue, '{{wysiwyg}}', '') WHERE optkey='comment_form_template'";

        if ($fu->db->Execute($query)) {
            if ($fu->db->AffectedRows() > 0) {
                $fu->AddSuccess('Removed old {{wysiwyg}} template tag.');
            }
        } else {
            $fu->AddErr('Old {{wysiwyg}} template tag not removed!');
        }
    }

// _______________________________________________ UPGRADE FROM < 2.2b3

    if ($version_installed > 0 && version_compare($version_installed, '2.2b3', '<')) {

        // ___________________________________________ convert to GMT

        $query = 'UPDATE ' . $fu->getOpt('blog_table') . ' SET added=DATE_ADD(added, INTERVAL ' . (0 - $setting[27]['optvalue']) . ' HOUR)';

        if ($fu->db->Execute($query)) {
            if ($fu->db->AffectedRows() > 0) {
                $fu->AddSuccess('Blog times converted to GMT.');
            }
        } else {
            $fu->AddErr('Failed to convert blog times to GMT!');
        }

        $query = 'UPDATE ' . $fu->getOpt('comments_table') . ' SET added=DATE_ADD(added, INTERVAL ' . (0 - $setting[27]['optvalue']) . ' HOUR)';

        if ($fu->db->Execute($query)) {
            if ($fu->db->AffectedRows() > 0) {
                $fu->AddSuccess('Comment times converted to GMT.');
            }
        } else {
            $fu->AddErr('Failed to convert comment times to GMT!');
        }

    }

// _______________________________________________ UPGRADE FROM < 2.2.1b2

    if ($version_installed > 0 && version_compare($version_installed, '2.2.1b2', '<')) {

        // ___________________________________________ modify old comment table scheme

        $query = 'ALTER TABLE ' . $fu->getOpt('comments_table') . "
      CHANGE `added` `added` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'";

        if ($fu->db->Execute($query)) {
            $fu->AddSuccess('Old table <strong>' . $fu->getOpt('comments_table') . '</strong> modified.');
        } else {
            $fu->AddErr('Old table <strong>' . $fu->getOpt('comments_table') . '</strong> not modified!');
        }

    }

// _______________________________________________ UPGRADE FROM ANY VERSION

    if ($version_installed > 0 && version_compare($version_installed, $installer_version, '<')) {

        // ___________________________________________ delete old files

        $files[] = 'add-fl.php';
        $files[] = 'blog-admin.php';
        $files[] = 'comments-admin.php';
        $files[] = 'admin-wysiwyg.php';
        $files[] = 'path.php';
        $files[] = 'gravatar.gif';

        // v1.6 files
        $files[] = 'formatText.js';
        $files[] = 'sortTable.js';
        $files[] = 'fanupdate.gif';
        $files[] = 'formatText.js';

        // v2.0 files -- mostly moved around
        $files[] = 'approve-comment.php';
        $files[] = 'changelog.txt';
        $files[] = 'fanupdate.js';
        $files[] = 'fanupdate.png';
        $files[] = 'fanupdate.svg';
        $files[] = 'fanupdate-logo.svg';
        $files[] = 'new-entry-button.png';
        $files[] = 'new-entry-button.svg';
        $files[] = 'new-entry-button-active.png';
        $files[] = 'SqlConnection.php';
        $files[] = 'standardista-table-sorting.js';

        // delete for 2.1 -- mostly moved around
        $files[] = 'nav.php';
        $files[] = 'protect.php';
        $files[] = 'readme.txt';
        $files[] = 'show-post.inc.php';
        $files[] = 'style.css';

        // delete for 2.2 -- mostly moved around
        $files[] = 'wysiwyg.php';
        $files[] = 'wysiwyg-admin.php';
        $files[] = 'img/cross.png';
        $files[] = 'img/tick.png';

        foreach ($files as $file) {
            if (file_exists($file)) {
                if (unlink($file)) {
                    $fu->AddSuccess('Old file <strong>' . $file . '</strong> deleted.');
                } else {
                    $fu->AddErr('Old file <strong>' . $file . '</strong> not deleted! Please delete it manually.');
                }
            }
        }

        foreach ($setting as $val) {
            $optkey = $fu->db->Escape($val['optkey']);
            $optdesc = $fu->db->Escape($val['optdesc']);

            $query = 'UPDATE ' . $fu->getOpt('options_table') . " SET optdesc='$optdesc' WHERE optkey='$optkey'";

            if ($fu->db->Execute($query)) {
                if ($fu->db->AffectedRows() > 0) {
                    $fu->AddSuccess('Updated description for <strong>' . $optkey . '</strong>');
                }
            } else {
                $fu->AddErr('Failed to update description for <strong>' . $optkey . '</strong>.');
            }
        }
    }

// _______________________________________________ FRESH INSTALL OR UPGRADE FROM ANY VERSION

    if (version_compare($version_installed, $installer_version, '<')) {

        // ___________________________________________ add new options

        $clean = clean_input($_POST, true);

        foreach ($clean['key'] as $key => $val) {

            $optkey = $fu->db->Escape($setting[$val]['optkey']);
            $optvalue = $fu->db->Escape($clean['value'][$key]);
            $optdesc = $fu->db->Escape($setting[$val]['optdesc']);

            $query = 'INSERT INTO ' . $fu->getOpt('options_table') . " SET optkey='$optkey', optvalue='$optvalue', optdesc='$optdesc'";

            if ($fu->db->Execute($query)) {
                $fu->AddSuccess('Set <strong>' . $optkey . '</strong> = ' . nl2br(htmlspecialchars($clean['value'][$key])));
            } else {
                $fu->AddErr('Failed to insert <strong>' . $optkey . '</strong>');
            }
        }
    }

    $query = 'UPDATE ' . $fu->getOpt('options_table') . " SET optvalue='" . $fu->getOpt('version') . "' WHERE optkey='_db_version'";

    if (!$fu->db->Execute($query)) {
        $fu->AddErr('Failed to set current db version.');
    }

// _____________________________________________ REPORT SUCCESS

    $fu->reportSuccess();

// _____________________________________________ REPORT ERRORS

    $fu->reportErrors();

    echo '<p>Now <strong>DELETE</strong> this file from your server and start posting!</p>' . "\n";
    echo '<p><a href="index.php">To the ADMIN panel.</a></p>' . "\n";

} else {

    ?>

    <p>To install FanUpdate <?php echo $fu->getOpt('version'); ?>, edit these options for your site, then click the
        button below. <?php if (version_compare($version_installed, $setting[3]['version'], '<')) {
            echo ' The auto-detected path settings should be correct, but please double-check them.';
        } ?></p>

    <form id="options" action="install.php" method="post">
        <?php

        foreach ($setting as $id => $row) {

            if (version_compare($version_installed, $row['version'], '<')) {

                if (strpos($row['optkey'], '_') == 0) { // hide private vars

                    ?>
                    <input type="hidden" id="<?php echo $row['optkey']; ?>" name="value[]"
                           value="<?php echo $row['optvalue']; ?>"/>
                    <input type="hidden" name="key[]" value="<?php echo $id; ?>"/>
                    <?php

                } else {

                    $class = (isset($class) && $class == 'even') ? 'odd' : 'even';

                    ?>
                    <div class="option <?php echo $class; ?>">
                        <p><label for="<?php echo $row['optkey']; ?>"><?php echo $row['optkey']; ?>:
                                <span class="help"><?php echo $row['optdesc']; ?></span></label>
                            <?php if (strpos($row['optkey'], 'template') !== false) { ?>
                                <textarea id="<?php echo $row['optkey']; ?>" name="value[]" cols="80"
                                          rows="15"><?php echo htmlspecialchars($row['optvalue']); ?></textarea>
                            <?php } else { ?>
                                <input type="text" id="<?php echo $row['optkey']; ?>" name="value[]" size="50"
                                       maxlength="255"
                                       value="<?php echo $row['optvalue']; ?>"/>
                            <?php } ?>
                            <input type="hidden" name="key[]" value="<?php echo $id; ?>"/></p>
                    </div><!-- END .option -->
                    <?php

                }

            }

        }

        ?>

        <p id="new-button">
            <?php if ($version_installed > 0) { ?>
                <input type="submit" name="upgrade" value="Upgrade from version <?php echo $version_installed; ?>"
                       class="update"/>
            <?php } else { ?>
                <input type="submit" name="fresh_install" value="Create fresh installation" class="update"/>
            <?php } ?>
        </p>

    </form>

    <script type="text/javascript">

        if (timezone_offset = $('timezone_offset')) {
            d = new Date();
            timezone_offset.value = 0 - (d.getTimezoneOffset() / 60); // stupid backward JS
        }
    </script>

    <?php

}

$fu->getFooter();
