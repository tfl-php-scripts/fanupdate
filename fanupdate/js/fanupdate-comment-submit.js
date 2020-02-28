// requires makeRequest

function submitCmt() {

    var f = this;
    var targetID = 'newComment';

    var name = f.name.value;
    var email = f.email.value;
    var url = f.url.value;
    var remember_me = (f.remember_me.checked) ? 'y' : '';
    var comment = f.comment.value;
    if (f.captcha != null) {
        var captcha = f.captcha.value;
    } else {
        var captcha = '';
    }
    var entry = f.entry.value;

    if (comment.length == 0) {
        alert('Your comment is blank!');
        return false;
    }

    var qs = 'name='+encodeURIComponent(name)
            +'&email='+encodeURIComponent(email)
            +'&url='+encodeURIComponent(url)
            +'&remember_me='+encodeURIComponent(remember_me)
            +'&comment='+encodeURIComponent(comment)
            +'&captcha='+encodeURIComponent(captcha)
            +'&entry='+encodeURIComponent(entry)
            +'&submit_comment=ajax';

    makeRequest(f.action, targetID, 'POST', qs, submitCmtCallback);

    location.hash = targetID;

    return false;
}

function submitCmtCallback(responseText, targetID, url) {

    el = $(targetID);
    el.innerHTML = responseText;

    success = /^<!-- SUCCESS/;

    if (responseText.match(success)) {
        toRemove = $('comments-form');
        toRemove.parentNode.removeChild(toRemove);

    } else {

        // failed, regenerate captcha if enabled
        if (captcha = $('captcha-img')) {
            path = url.replace('process.php', '');
            captcha.src = path+'captcha.php?'+Math.random();
        }
    }
    return true;
}

addEvent($('comments-form'), 'submit', submitCmt);

var myWysiwyg = new Array();
myWysiwyg[0] = new wysiwygButton('bold', fu_url + '/css/text_bold.png', 'make bold', function (e) {
	formatText(getEventTarget(e).className, '[B]', '[/B]');
	return false;
});
myWysiwyg[1] = new wysiwygButton('italic', fu_url + '/css/text_italic.png', 'make italic', function (e) {
	formatText(getEventTarget(e).className, '[I]', '[/I]');
	return false;
});
myWysiwyg[2] = new wysiwygButton('link', fu_url + '/css/link.png', 'insert link', function (e) {
	insertLink(getEventTarget(e).className, true);
	return false;
});

addEvent(window, 'load', function () {
	var ta = $('comments-form').getElementsByTagName('textarea')[0];
	makeWysiwyg(ta);
});