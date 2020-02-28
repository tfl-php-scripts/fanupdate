// requires css, addEvent, and getEventTarget

function confirmDelete(e) {
    var button = getEventTarget(e);
    if (!confirm(button.title + '?')) {
        return false;
    }
    return true;
}

function confirmDeleteInit() {
    var deletes = css.getElementsByClass(document, 'delete', '*');

    for (i = 0; i < deletes.length; i++) {
        deletes[i].onclick = confirmDelete;
    }
}

var myWysiwyg = new Array();
myWysiwyg[0] = new wysiwygButton('bold', 'css/text_bold.png', 'make bold', function (e) {
	formatText(getEventTarget(e).className, '<strong>', '</strong>');
	return false;
});
myWysiwyg[1] = new wysiwygButton('italic', 'css/text_italic.png', 'make italic', function (e) {
	formatText(getEventTarget(e).className, '<em>', '</em>');
	return false;
});
myWysiwyg[2] = new wysiwygButton('h3', 'css/text_heading_2.png', 'make heading level 2', function (e) {
	formatText(getEventTarget(e).className, '<h2>', '</h2>');
	return false;
});
myWysiwyg[3] = new wysiwygButton('h3', 'css/text_heading_3.png', 'make heading level 3', function (e) {
	formatText(getEventTarget(e).className, '<h3>', '</h3>');
	return false;
});
myWysiwyg[4] = new wysiwygButton('quote', 'css/text_signature.png', 'make blockquote', function (e) {
	formatText(getEventTarget(e).className, '<blockquote>', '</blockquote>');
	return false;
});
myWysiwyg[5] = new wysiwygButton('rm', 'css/text_horizontalrule.png', 'insert read more break', function (e) {
	formatText(getEventTarget(e).className, '<!-- MORE -->', '');
	return false;
});
myWysiwyg[6] = new wysiwygButton('link', 'css/link.png', 'insert link', function (e) {
	insertLink(getEventTarget(e).className);
	return false;
});

addEvent(window, 'load', function () {
	confirmDeleteInit();
	
	var tas = css.getElementsByClass(document, 'wysiwyg', 'textarea');
	for (i = 0; ta = tas[i]; i++) {
		makeWysiwyg(ta);
	}
});