// requires css, addEvent, and getEventTarget

var selectAll = {

    checkboxes_container: null,

    selectAll : function (e) {
        var linkEl = getEventTarget(e);
        var j = parseInt(linkEl.getAttribute('j'), 10);
        var checkboxes = checkboxes_container[j].getElementsByTagName('input');
        for (var i = 0; i < checkboxes.length; i++) {
			if (checkboxes[i].type == 'checkbox') {
            	checkboxes[i].checked = true;
				highlightChecked(checkboxes[i]); 
			}
        }
        return false;
    },

    selectNone : function (e) {
        var linkEl = getEventTarget(e);
        var j = parseInt(linkEl.getAttribute('j'), 10);
        var checkboxes = checkboxes_container[j].getElementsByTagName('input');
        for (var i = 0; i < checkboxes.length; i++) {
			if (checkboxes[i].type == 'checkbox') {
            	checkboxes[i].checked = false;
				highlightChecked(checkboxes[i]);
			}
        }
        return false;
    },

    init : function () {

        checkboxes_container = css.getElementsByClass(document, 'select_all', '*');

        for (var i = 0; i < checkboxes_container.length; i++) {

            var all = document.createElement('a');
            var none = document.createElement('a');

            all.setAttribute('j', i);
            none.setAttribute('j', i);

            all.className = 'all';
            none.className = 'none';

            all.href = '#';
            none.href = '#';

            all.onclick = this.selectAll;
            none.onclick = this.selectNone;

            all.appendChild(document.createTextNode('Select All'));
            none.appendChild(document.createTextNode('Select None'));

            var container = document.createElement('ul');
            container.className = 'catlist';

            var li_all = document.createElement('li');
            var li_none = document.createElement('li');

            li_all.appendChild(all);
            li_none.appendChild(none);

            container.appendChild(li_all);
            container.appendChild(li_none);

            insertAfter(container, checkboxes_container[i]);
        }
    }
};

function selectAllInit() {
    selectAll.init();
}

addEvent(window, 'load', selectAllInit);

var categories = $('categories');

function addCat(last_cat) {
	if (last_cat != undefined) {
		last_cat.onchange = undefined;
	}
	var input = document.createElement('input');
	input.type = 'text';
	input.name = 'new_cat[]';
	input.value = '';
	input.className = 'new';
	input.onchange = function() { addCat(this); };
	var li = document.createElement('li');
	li.appendChild(input);
	categories.appendChild(li);
}

addCat();

var t;

function setNow() {
	if ($('now').checked) {
		var nd = new Date();
		$('added').value = nd.strftime('%Y-%m-%d %g:%i %a %O');
		t = setTimeout("setNow()", 1000);
	} else {
		clearTimeout(t);
	}
}

addEvent($('now'), 'click', setNow);