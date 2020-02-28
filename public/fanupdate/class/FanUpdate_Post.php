<?php

class FanUpdate_Post {

    var $params = array();
    var $fu;

    function FanUpdate_Post($params, &$fu, $self = null) {
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
        $this->params['url'] .= '?id='.$this->params['entry_id'];

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

    function addParam($key, $value) {
        $this->params[$key] = $value;
    }

	function addCategory($key, $val) {
		$this->params['category'][$key] = $value;
	}

    function getID() {
        return $this->params['entry_id'];
    }

    function isPublic() {
        return $this->params['is_public'];
    }

    function commentsOn() {
        return $this->params['comments_on'];
    }

    function getTitle() {
        return $this->params['title'];
    }

    function getUrl() {
        return $this->params['url'];
    }

	function getCommentsUrl() {
		return $this->params['url'].'#comments';
	}
	
	function getCommentsFeedUrl() {
		return $this->fu->getOpt('install_url').'/rss-comments.php?id='.$this->getID();
	}

    function getYear() {
        return substr($this->params['added'], 0, 4);
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

    function allowComments() {
        return $this->params['allow_comments'];
    }

    function getBody() {
        return $this->params['body'];
    }

    function getBodyFormatted() {
        return wpautop($this->fu->replaceSmilies($this->params['body']), true, true);
    }

    function getCommentLink() {

        if ($this->allowComments()) {

            if ($this->params['num_comments'] == 0) {
                return '<a href="'.$this->getCommentsUrl().'">Leave a comment?</a>';
            } elseif ($this->params['num_comments'] == 1) {
                return '<a href="'.$this->getCommentsUrl().'">1 comment</a>.';
            } else {
                return '<a href="'.$this->getCommentsUrl().'">'.$this->params['num_comments'].' comments</a>.';
            }
        }
        return '';
    }

	function getCatFromDb($cat_ids = null) {

		if (is_array($cat_ids)) {
			$query = "SELECT c.".$this->fu->getOpt('col_id')." AS cat_id, c.".$this->fu->getOpt('col_subj')." AS cat_name
	        FROM ".$this->fu->getOpt('collective_table')." c
			WHERE c.".$this->fu->getOpt('col_id')." IN(".implode(',', $cat_ids).")";
		} else {
			$query = "SELECT c.".$this->fu->getOpt('col_id')." AS cat_id, c.".$this->fu->getOpt('col_subj')." AS cat_name
	        FROM ".$this->fu->getOpt('catjoin_table')." j
	        LEFT JOIN ".$this->fu->getOpt('collective_table')." c ON j.cat_id=c.".$this->fu->getOpt('col_id')."
        	WHERE j.entry_id=".$this->getID();
		}

	    $this->fu->db->Execute($query);

	    while ($row = $this->fu->db->ReadRecord()) {
	        $this->params['category'][$row['cat_id']] = $row['cat_name'];
	    }
	    $this->fu->db->FreeResult();
	}

	function getCategoryArray() {
        return $this->params['category'];
    }

	function getCategoryString() {
        return implode(', ', $this->params['category']);
    }

    function getCategoryLink() {
		$tmp = array();
		foreach ($this->params['category'] as $key => $val) {
        	$tmp[] = '<a href="'.$this->fu->getCleanSelf().'?c='.$key.'" title="all posts about '.$val.'">'.$val.'</a>';
		}
		return implode(', ', $tmp);
    }

    function getRMLink() {
        return '<a href="'.$this->getUrl().'">Read more of '.$this->getTitle().'.</a>';
    }

    function printPost($doSummary = false) {

        if ($doSummary) {

            if (strpos($this->params['body'], '<!-- MORE -->')) {

                list($this->params['body']) = explode('<!-- MORE -->', $this->params['body'], 2);
                $this->params['body'] .= ' '.$this->getRMLink();

            } else {

                if ($this->fu->getOpt('abstract_word_count') > 0) {

                    $text_chop = strip_tags($this->params['body']);
                    $text_chop = truncate_wc($text_chop, $this->fu->getOpt('abstract_word_count'));

                    // if text was longer than abstract_word_count, truncate_wc appends &#8320;
                    // thus a test for whether a read more link is needed
                    if (!empty($text_chop) && substr($text_chop, -7) == '&#8230;') {
                        $this->params['body'] = $text_chop;
                        $this->params['body'] .= ' <a href="'.$this->getUrl().'">Read more of '.$this->getTitle().'.</a>';
                    }
                }
            }
        }

        $text = str_replace('{{title}}', wptexturize($this->getTitle()), $this->fu->getOpt('entry_template'));
        $text = str_replace('{{id}}', $this->getID(), $text);
        $text = str_replace('{{url}}', $this->getUrl(), $text);
        $text = str_replace('{{date}}', $this->getDateFormatted(), $text);
        $text = str_replace('{{category}}', $this->getCategoryLink(), $text);
        $text = str_replace('{{comment_link}}', $this->getCommentLink(), $text);
        $text = str_replace('{{body}}', $this->getBodyFormatted(), $text);

        echo $text;
    }

} // end class FanUpdate_Post

?>