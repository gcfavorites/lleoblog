function tree(id) { var element = idd(id);

    function hasClass(elem, className) { return new RegExp("(^|\\s)"+className+"(\\s|$)").test(elem.className); }

    function toggleNode(node) { // ���������� ����� ����� ��� ����
        var newClass = hasClass(node, 'ExpandOpen') ? 'ExpandClosed' : 'ExpandOpen'
        // �������� ������� ����� �� newClass
	// ������� ������� �������� ������� open|close � ������ �� newClass
        var re =  /(^|\s)(ExpandOpen|ExpandClosed)(\s|$)/;
        node.className = node.className.replace(re, '$1'+newClass+'$3');
	zabil('albumdir',node.id);
    }

function load(node) {

	function showLoading(on) { var expand = node.getElementsByTagName('DIV')[0]; expand.className = on ? 'ExpandLoading' : 'Expand'; }
	function onLoadError(error) { var msg = "������ "+error.errcode; if(error.message) msg = msg + ' :'+error.message; alert(msg); }

    function onLoaded(data) {

	for(var i=0; i<data.length; i++) {
	    var li = document.createElement('LI');
	    li.id = data[i]['i'];
	    li.className = "Node Expand" + (data[i]['f']==1 ? 'Closed' : 'Leaf');
	    if(i == data.length-1) li.className += ' IsLast';
	    li.innerHTML = '<div class="Expand"></div><div class="Content">'+data[i]['t']+'</div>';
	    if(data[i]['f']==1) { li.innerHTML += '<ul class="Container"></ul>'; }
	    node.getElementsByTagName('UL')[0].appendChild(li);
	}
	node.isLoaded = true;
	toggleNode(node);
    }

    showLoading(true);

	JsHttpRequest.query('/blog/ajax/foto.php', { a: 'albumgo', id: node.id },
	function(responseJS, responseText) {
		if(responseJS.status) { onLoaded(responseJS.data); showLoading(false);
// zabil('albumdir',responseJS.albumdir);
}
	},true);
}

element.onclick = function(event) {
    event = event || window.event
    var clickedElem = event.target || event.srcElement;
    if(!hasClass(clickedElem, 'Expand')) return; // ���� �� ���

    // Node, �� ������� ��������
    var node = clickedElem.parentNode;

    if(hasClass(node, 'ExpandLeaf')) return; // ���� �� �����
    if(node.isLoaded || node.getElementsByTagName('LI').length) { toggleNode(node); return; } // ���� ��� �������� ����� AJAX(�������� �� ����)

    if(node.getElementsByTagName('LI').length) {
	    // ���� �� ��� �������� ��� ������ AJAX, �� � ���� ������-�� ���� �������
	    // ��������, ��� ���� ���� � DOM ������ �� ������ tree()
	    // ��� �������, ��� "�����������" ����
	    // ������ ���������� �� ����
	    toggleNode(node); return;
    }

    // ��������� ����
    load(node);
}

// load(idd('/'));
}

tree('root');
