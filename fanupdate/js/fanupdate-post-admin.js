// requires css, getEventTarget, confirmDelete, makeRequest, addEvent

var fuPost = {

    init : function () {

        var deletes = css.getElementsByClass(document, 'delete', '*');

        for (var i = 0; i < deletes.length; i++) {
            var post_id = deletes[i].form.id.value;
            deletes[i].id = 'delete_' + post_id;
            deletes[i].onclick = this.deletePost;
        }
    },

    deletePost : function (e) {

        var button = getEventTarget(e);
        var tr = button.parentNode.parentNode.parentNode;
        var post_id = parseInt(button.id.replace('delete_', ''), 10);

        if (confirmDelete(e)) {
            var qs = 'id=' + encodeURIComponent(post_id) + '&action=delete&mode=ajax';
            makeRequest(button.form.attributes['action'].value, tr, 'POST', qs, deletePostCallback);
        }

        return false;
    }

};

function deletePostCallback(responseText, target) {
    target.parentNode.removeChild(target);
}

function fuPostInit() {
    fuPost.init();
}

addEvent(window, 'load', fuPostInit);