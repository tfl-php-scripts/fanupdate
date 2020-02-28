<?php

class FanUpdate_Comment {

    var $params = array();
    var $fu;

    function FanUpdate_Comment($params, &$fu, $self = null) {
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
        $this->params['link_url'] .= '?id='.$this->params['entry_id'].'#comment'.$this->params['comment_id'];
		
		if (empty($this->params['points'])) {
            $this->params['points'] = 0;
        }

        if (isset($this->params['approved']) && $this->params['approved'] == 1) {
            $this->params['approved'] = true;
        } else {
            $this->params['approved'] = false;
        }
    }

    function getID() {
        return $this->params['comment_id'];
    }

	function getEntryID() {
        return $this->params['entry_id'];
    }

	function getLinkUrl() {
		return $this->params['link_url'];
	}

    function isApproved() {
        return $this->params['approved'];
    }

	function getPoints() {
		return $this->params['points'];
	}

    function getName() {
        return $this->params['name'];
    }

    function getEmail() {
        return $this->params['email'];
    }

    function getUrl() {
        return $this->params['url'];
    }

    function getDate() {
        return $this->params['added'];
    }

    function getDateFormatted($format = false) {
		if (!$format) {
			$format = $this->fu->getOpt('date_format');
		}
        return date($format, strtotime($this->params['added']) + ($this->fu->getOpt('timezone_offset') * 3600));
    }

    function getAbstract($words = 10, $doSmile = false) {
        $text = truncate_wc(strip_tags($this->params['comment']), $words);
		if ($doSmile) {
			$text = $this->fu->replaceSmilies($text);
		}
		return $text;
    }

    function getBody() {
        return $this->params['comment'];
    }

    function getBodyFormatted() {
        return wpautop($this->fu->replaceSmilies($this->params['comment']));
    }

    function getCommenterLink() {

        if (empty($this->params['url'])) {
            return '<span class="commenter">'.$this->params['name'].'</span>';
        } else {
            return '<a class="commenter" href="'.$this->params['url'].'" title="'.$this->params['name'].'&#8217;s website">'.$this->params['name'].'</a>';
        }
    }

    function getGravatarUrl($size = 80, $rating = 'G', $default = null) {

        if ($this->fu->getOpt('gravatar_on')) {

            if ($default == null) {
                $default = $this->fu->getOpt('gravatar_default');
            }

            if (is_email($this->params['email'])) {

                $grav_url = 'http://www.gravatar.com/avatar.php?gravatar_id='.md5($this->params['email']).
                    '&amp;rating='.$rating.
                    '&amp;default='.urlencode($default).
                    '&amp;size='.$size;

            } else {
                $grav_url = $default;
            }

            return $grav_url;

        } else {
            return '';
        }
    }

    function getGravatar($size = 80, $rating = 'G', $default = null) {

        if ($this->fu->getOpt('gravatar_on')) {

            $grav_url = $this->getGravatarUrl($size, $rating, $default);

            return '<img class="gravatar" src="'.$grav_url.'" alt="'.$this->params['name'].'&#8217;s gravatar" width="'.$size.'" height="'.$size.'" />';

        } else {
            return '';
        }
    }

    function printComment() {

        $gravatar = $this->getGravatar($this->fu->getOpt('gravatar_size'), $this->fu->getOpt('gravatar_rating'));

        $text = str_replace('{{gravatar}}', $gravatar, $this->fu->getOpt('comment_template'));
        $text = str_replace('{{id}}', $this->getID(), $text);
        $text = str_replace('{{name}}', $this->getCommenterLink(), $text);
        $text = str_replace('{{date}}', $this->getDateFormatted(), $text);
        $text = str_replace('{{body}}', $this->getBodyFormatted(), $text);

        echo $text;
    }

} // end class FanUpdate_Comment

?>