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

class FanUpdate_Post
{
    public $params = array();
    public $fu;

    public function __construct($params, FanUpdate $fu, $self = null)
    {
        $this->params = $params;
        $this->fu = $fu;

        if (empty($this->params['entry_id'])) {
            $this->params['entry_id'] = 0;
        }

        if (empty($this->params['num_comments'])) {
            $this->params['num_comments'] = 0;
        }

        if (empty($this->params['category'])) {
            $this->params['category'] = array();
        }

        if (empty($this->params['title'])) {
            $this->params['title'] = '';
        }

        if (empty($this->params['body'])) {
            $this->params['body'] = '';
        }

        if (empty($this->params['added'])) {
            $this->params['added'] = gmdate('Y-m-d H:i:s');
        }

        if (!empty($self)) {
            $this->params['url'] = $self;
        } else {
            $this->params['url'] = $this->fu->GetCleanSelf();
        }
        $this->params['url'] .= '?id=' . $this->params['entry_id'];

        if (!isset($this->params['is_public']) || $this->params['is_public'] == 1) {
            $this->params['is_public'] = true;
        } else {
            $this->params['is_public'] = false;
        }

        if (!isset($this->params['comments_on']) || $this->params['comments_on'] == 1) {
            $this->params['comments_on'] = true;
        } else {
            $this->params['comments_on'] = false;
        }

        // comments must be allowed globally AND on this post to be ON
        if ($this->fu->getOpt('comments_on') && $this->params['comments_on']) {
            $this->params['allow_comments'] = true;
        } else {
            $this->params['allow_comments'] = false;
        }
    }

    public function addParam($key, $value)
    {
        $this->params[$key] = $value;
    }

    public function addCategory($key, $val)
    {
        $this->params['category'][$key] = $val;
    }

    public function getID()
    {
        return $this->params['entry_id'];
    }

    public function isPublic()
    {
        return $this->params['is_public'];
    }

    public function commentsOn()
    {
        return $this->params['comments_on'];
    }

    public function getTitle()
    {
        return $this->params['title'];
    }

    public function getUrl()
    {
        return $this->params['url'];
    }

    public function getCommentsUrl()
    {
        return $this->params['url'] . '#comments';
    }

    public function getCommentsFeedUrl()
    {
        return $this->fu->getOpt('install_url') . '/rss-comments.php?id=' . $this->getID();
    }

    public function getYear()
    {
        return substr($this->params['added'], 0, 4);
    }

    public function getDate()
    {
        return $this->params['added'];
    }

    public function getDateFormatted($format = false)
    {
        if (!$format) {
            $format = $this->fu->getOpt('date_format');
        }
        return date($format, strtotime($this->params['added']) + ($this->fu->getOpt('timezone_offset') * 3600));
    }

    public function allowComments()
    {
        return $this->params['allow_comments'];
    }

    public function getBody()
    {
        return $this->params['body'];
    }

    public function getBodyFormatted()
    {
        return wpautop($this->fu->replaceSmilies($this->params['body']), true, true);
    }

    public function getCommentLink()
    {

        if ($this->allowComments()) {

            if ($this->params['num_comments'] == 0) {
                return '<a href="' . $this->getCommentsUrl() . '">Leave a comment?</a>';
            }

            if ($this->params['num_comments'] == 1) {
                return '<a href="' . $this->getCommentsUrl() . '">1 comment</a>.';
            }

            return '<a href="' . $this->getCommentsUrl() . '">' . $this->params['num_comments'] . ' comments</a>.';
        }
        return '';
    }

    public function getCatFromDb($cat_ids = null)
    {
        if (is_array($cat_ids)) {
            $query = 'SELECT c.' . $this->fu->getOpt('col_id') . ' AS cat_id, c.' . $this->fu->getOpt('col_subj') . ' AS cat_name
	        FROM ' . $this->fu->getOpt('collective_table') . ' c
			WHERE c.' . $this->fu->getOpt('col_id') . ' IN(' . implode(',', $cat_ids) . ')';
        } else {
            $query = 'SELECT c.' . $this->fu->getOpt('col_id') . ' AS cat_id, c.' . $this->fu->getOpt('col_subj') . ' AS cat_name
	        FROM ' . $this->fu->getOpt('catjoin_table') . ' j
	        LEFT JOIN ' . $this->fu->getOpt('collective_table') . ' c ON j.cat_id=c.' . $this->fu->getOpt('col_id') . '
        	WHERE j.entry_id=' . $this->getID();
        }

        $this->fu->db->Execute($query);

        while ($row = $this->fu->db->ReadRecord()) {
            $this->params['category'][$row['cat_id']] = $row['cat_name'];
        }
        $this->fu->db->FreeResult();
    }

    public function getCategoryArray()
    {
        return $this->params['category'];
    }

    public function getCategoryString()
    {
        return implode(', ', $this->params['category']);
    }

    public function getCategoryLink()
    {
        $tmp = array();
        foreach ($this->params['category'] as $key => $val) {
            $tmp[] = '<a href="' . $this->fu->getCleanSelf() . '?c=' . $key . '" title="all posts about ' . $val . '">' . $val . '</a>';
        }
        return implode(', ', $tmp);
    }

    public function getRMLink()
    {
        return '<a href="' . $this->getUrl() . '">Read more of ' . $this->getTitle() . '.</a>';
    }

    public function printPost($doSummary = false)
    {

        if ($doSummary) {

            if (strpos($this->params['body'], '<!-- MORE -->')) {

                [$this->params['body']] = explode('<!-- MORE -->', $this->params['body'], 2);
                $this->params['body'] .= ' ' . $this->getRMLink();

            } else if ($this->fu->getOpt('abstract_word_count') > 0) {

                $text_chop = strip_tags($this->params['body']);
                $text_chop = truncate_wc($text_chop, $this->fu->getOpt('abstract_word_count'));

                // if text was longer than abstract_word_count, truncate_wc appends &#8320;
                // thus a test for whether a read more link is needed
                if (!empty($text_chop) && substr($text_chop, -7) === '&#8230;') {
                    $this->params['body'] = $text_chop;
                    $this->params['body'] .= ' <a href="' . $this->getUrl() . '">Read more of ' . $this->getTitle() . '.</a>';
                }
            }
        }

        $text = str_replace('{{title}}', wptexturize($this->getTitle()), $this->fu->getOpt('entry_template'));
        $text = str_replace(array('{{id}}', '{{url}}'), array($this->getID(), $this->getUrl()), $text);
        $text = str_replace(array('{{date}}', '{{category}}'), array($this->getDateFormatted(), $this->getCategoryLink()), $text);
        $text = str_replace(array('{{comment_link}}', '{{body}}'), array($this->getCommentLink(), $this->getBodyFormatted()), $text);

        echo $text;
    }
}
