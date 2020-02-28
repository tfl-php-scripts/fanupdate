// requires css, getEventTarget, confirmDelete, makeRequest, addEvent

var fuComment = {

    init : function () {

        var cmts = css.getElementsByClass(document, 'approve_comment', '*');

        for (var i = 0; i < cmts.length; i++) {

            var cmt_id = parseInt(cmts[i].id.replace('ci_', ''), 10);

            var a = document.createElement('a');
            a.href = '#';
            a.onclick = this.approveCmt;
            a.id = 'approve_' + cmt_id;
            a.appendChild(document.createTextNode('Approve'));

            insertAfter(a, cmts[i]);
        }

        var deletes = css.getElementsByClass(document, 'delete', '*');

        for (i = 0; i < deletes.length; i++) {
            var cmt_id = deletes[i].form.id.value;
            deletes[i].id = 'delete_' + cmt_id;
            deletes[i].onclick = this.deleteCmt;
        }
    },

    approveCmt : function (e) {

        var linkEl = getEventTarget(e);
        var cmt_id = parseInt(linkEl.id.replace('approve_', ''), 10);

        var target = linkEl.parentNode;

        var qs = 'id=' + encodeURIComponent(cmt_id) + '&action=approve&mode=ajax';

        makeRequest('comment.php', target, 'POST', qs, approveCmtCallback);

        return false;
    },

    deleteCmt : function (e) {

        var button = getEventTarget(e);

        if (confirmDelete(e)) {
			if (css.elementHasClass(button, 'noajax')) {
				return true;
			} else {
				var tr = button.parentNode.parentNode.parentNode;
		        var cmt_id = parseInt(button.id.replace('delete_', ''), 10);
	            var qs = 'id=' + encodeURIComponent(cmt_id) + '&action=delete&mode=ajax';
	            makeRequest(button.form.attributes['action'].value, tr, 'POST', qs, deleteCmtCallback);
			}
        }

        return false;
    }

};

function deleteCmtCallback(responseText, target) {
    target.parentNode.removeChild(target);
}

function approveCmtCallback(responseText, target) {
    target.innerHTML = responseText;
}

function fuCommentInit() {
    fuComment.init();
}

addEvent(window, 'load', fuCommentInit);