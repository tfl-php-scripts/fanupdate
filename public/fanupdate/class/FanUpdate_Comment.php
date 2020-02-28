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

class FanUpdate_Comment
{
    public $params = array();
    public $fu;

    public function __construct($params, FanUpdate $fu, $self = null)
    {
        $this->params = $params;
        $this->fu = $fu;

        if (empty($this->params['comment_id'])) {
            $this->params['comment_id'] = 0;
        }

        if (empty($this->params['entry_id'])) {
            $this->params['entry_id'] = 0;
        }

        if (empty($this->params['name'])) {
            $this->params['name'] = 'Anonymous';
        }

        if (empty($this->params['email'])) {
            $this->params['email'] = '';
        }

        if (empty($this->params['url'])) {
            $this->params['url'] = '';
        }

        if (empty($this->params['comment'])) {
            $this->params['comment'] = '';
        }

        if (empty($this->params['added'])) {
            $this->params['added'] = date('Y-m-d H:i:s');
        }

        if (!empty($self)) {
            $this->params['link_url'] = $self;
        } else {
            $this->params['link_url'] = $this->fu->GetCleanSelf();
        }
        $this->params['link_url'] .= '?id=' . $this->params['entry_id'] . '#comment' . $this->params['comment_id'];

        if (empty($this->params['points'])) {
            $this->params['points'] = 0;
        }

        if (isset($this->params['approved']) && $this->params['approved'] == 1) {
            $this->params['approved'] = true;
        } else {
            $this->params['approved'] = false;
        }
    }

    public function getID()
    {
        return $this->params['comment_id'];
    }

    public function getLinkUrl()
    {
        return $this->params['link_url'];
    }

    public function isApproved()
    {
        return $this->params['approved'];
    }

    public function getPoints()
    {
        return $this->params['points'];
    }

    public function getName()
    {
        return $this->params['name'];
    }

    public function getEmail()
    {
        return $this->params['email'];
    }

    public function getUrl()
    {
        return $this->params['url'];
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

    public function getAbstract($words = 10, $doSmile = false)
    {
        $text = truncate_wc(strip_tags($this->params['comment']), $words);
        if ($doSmile) {
            $text = $this->fu->replaceSmilies($text);
        }
        return $text;
    }

    public function getBody()
    {
        return $this->params['comment'];
    }

    public function getBodyFormatted()
    {
        return wpautop($this->fu->replaceSmilies($this->params['comment']));
    }

    public function getCommenterLink()
    {

        if (empty($this->params['url'])) {
            return '<span class="commenter">' . $this->params['name'] . '</span>';
        }

        return '<a class="commenter" href="' . $this->params['url'] . '" title="' . $this->params['name'] . '&#8217;s website">' . $this->params['name'] . '</a>';
    }

    public function getGravatarUrl($size = 80, $rating = 'G', $default = null)
    {

        if ($this->fu->getOpt('gravatar_on')) {

            if ($default === null) {
                $default = $this->fu->getOpt('gravatar_default');
            }

            if (is_email($this->params['email'])) {

                $grav_url = 'http://www.gravatar.com/avatar.php?gravatar_id=' . md5($this->params['email']) .
                    '&amp;rating=' . $rating .
                    '&amp;default=' . urlencode($default) .
                    '&amp;size=' . $size;

            } else {
                $grav_url = $default;
            }

            return $grav_url;

        }

        return '';
    }

    public function getGravatar($size = 80, $rating = 'G', $default = null)
    {
        if ($this->fu->getOpt('gravatar_on')) {

            $grav_url = $this->getGravatarUrl($size, $rating, $default);

            return '<img class="gravatar" src="' . $grav_url . '" alt="' . $this->params['name'] . '&#8217;s gravatar" width="' . $size . '" height="' . $size . '" />';

        }

        return '';
    }

    public function printComment()
    {
        $gravatar = $this->getGravatar($this->fu->getOpt('gravatar_size'), $this->fu->getOpt('gravatar_rating'));

        $text = str_replace('{{gravatar}}', $gravatar, $this->fu->getOpt('comment_template'));
        $text = str_replace(array('{{id}}', '{{name}}'), array($this->getID(), $this->getCommenterLink()), $text);
        $text = str_replace(array('{{date}}', '{{body}}'), array($this->getDateFormatted(), $this->getBodyFormatted()), $text);

        echo $text;
    }

}
