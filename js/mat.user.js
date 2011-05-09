// ==UserScript==
// @name mat
// @namespace none
// @description mat
// @include *
// @version 1.3
// @homepage http://lleo.aha.ru/mat/
//
// (c) killerstorm, lleo


(function() {

var words = 
[' \u043a\u0440\u0430\u0441\u0430\u0432\u0447\u0435\u0433 \u043d\u0430\u0445',
', \u0441\u0443\u043a\u0430 \u0434\u0440\u0430\u043d\u043d\u0430\u044f',
', \u043f\u0438\u0437\u0434\u0435\u0446',
', \u0435\u0431\u0430\u043d\u0443\u0442\u044c\u0441\u044f \u043a \u0445\u0443\u044f\u043c',
', \u043e\u0431\u043e\u0441\u0440\u0430\u0442\u044c\u0441\u044f',
', \u044f \u0435\u0431\u0443',
', \u0435\u0431\u0430\u043d\u043d\u044b\u0439 \u043a\u0430\u0440\u0430\u0441\u044c',
', \u0436\u043e\u043f\u0430 \u0441\u0440\u0430\u043d\u0430\u044f',
' \u0432\u043e\u043e\u0431\u0449\u0435 \u043f\u0438\u0437\u0434\u0435\u0446',
', \u0437\u0430\u043b\u0443\u043f\u0430 \u0441 \u0443\u0448\u0430\u043c\u0438',
' \u0441\u0443\u043a\u0430 \u043d\u0430\u0445',
' \u043d\u0430\u0445\u0443\u0439',
', \u0431\u043b\u044f\u0434\u0441\u043a\u0430\u044f \u043f\u0430\u0440\u0430\u0448\u0430',
', \u0431\u043b\u044f',
' \u043d\u0430 \u0445\u0443\u0439',
' \u043d\u0430 \u0445\u0443\u0439',
' \u043d\u0430 \u0445\u0443\u0439',
' \u043d\u0430 \u0445\u0443\u0439',
' \u043d\u0430 \u0445\u0443\u0439',
' \u043d\u0430 \u0445\u0443\u0439',
' \u0432 \u043f\u0438\u0437\u0434\u0443',
' \u0432 \u043f\u0438\u0437\u0434\u0443',
' \u0432 \u043f\u0438\u0437\u0434\u0443',
' \u0432 \u043f\u0438\u0437\u0434\u0443',
' \u0432 \u043f\u0438\u0437\u0434\u0443',
' \u0434\u043e \u043f\u0438\u0437\u0434\u044b',
' \u0434\u043e \u043f\u0438\u0437\u0434\u044b',
' \u0434\u043e \u043f\u0438\u0437\u0434\u044b',
' \u0435\u0431\u0430\u043d\u044b\u0439 \u0432 \u0440\u043e\u0442',
' \u0435\u0431\u0430\u043d\u044b\u0439 \u0432 \u0440\u043e\u0442',
', \u0435\u0431 \u0442\u0432\u043e\u044e \u043c\u0430\u0442\u044c',
' \u043f\u0438\u0437\u0434\u0435\u0446 \u043d\u0430 \u0445\u0443\u0439',
', \u043d\u0443 \u0445\u0443\u0439\u043b\u0438',
' \u0432 \u0436\u043e\u043f\u0443',
' \u0432 \u0436\u043e\u043f\u0443',
' \u0432 \u0436\u043e\u043f\u0443',
' \u0432 \u0436\u043e\u043f\u0443',
', \u0431\u043b\u044f\u0434\u044c',
', \u0431\u043b\u044f\u0434\u044c',
', \u0431\u043b\u044f\u0434\u044c',
' \u0431\u043b\u044f\u0434\u044c',
' \u0431\u043b\u044f\u0434\u044c',
' \u0431\u043b\u044f\u0434\u044c',
', \u0431\u043b\u044f\u0434\u044c',
', \u0445\u0443\u044f\u043a \u0437\u043d\u0430\u0447\u0438\u0442',
' \u043d\u0435\u0445\u0443\u0435\u0432\u043e \u0442\u0430\u043a',
', \u043a\u043e\u0440\u043e\u0447\u0435',
', \u043d\u0443 \u0442\u0438\u043f\u0430',
' \u0432 \u043d\u0430\u0442\u0443\u0440\u0435',
' \u0431\u0435\u0437 \u0431\u0430\u0437\u0430\u0440\u0430',
', \u0441 \u043f\u043e\u043d\u0442\u043e\u043c \u0434\u0435\u043b\u0430',
', \u0437\u043d\u0430\u0447\u0438\u0442',
', \u0443\u0441\u0441\u0430\u0442\u044c\u0441\u044f \u043c\u043e\u0436\u043d\u043e',
', \u0443\u0441\u0440\u0430\u0442\u044c\u0441\u044f \u043c\u043e\u0436\u043d\u043e',
', \u043f\u0440\u043e\u0441\u0442\u043e \u043e\u0431\u043e\u0441\u0440\u0430\u0442\u044c\u0441\u044f',
', \u044f \u0445\u0443\u0435\u044e',
', \u044f \u043e\u0445\u0443\u0435\u0432\u0430\u044e',
' \u043a \u0445\u0443\u044f\u043c',
' \u043a \u0435\u0431\u0435\u043d\u044f\u043c',
', \u0435\u0431\u0438 \u043c\u0430\u0442\u044c',
', \u0435\u0431\u0430\u043d\u0443\u0442\u044c\u0441\u044f \u043c\u043e\u0436\u043d\u043e',
', \u043f\u0438\u0434\u043e\u0440\u044b \u0435\u0431\u0443\u0447\u0438\u0435',
', \u0441\u0443\u0447\u044c\u0435 \u0432\u044b\u043c\u044f',
', \u0441\u0443\u0447\u044c\u0435 \u043f\u043b\u0435\u043c\u044f',
' \u0431\u0435\u0437 \u043f\u0438\u0437\u0434\u044b',
', \u0432 \u0433\u043e\u0432\u043d\u0435 \u043f\u043e \u0443\u0448\u0438',
', \u0437\u0430\u043b\u0443\u043f\u0430 \u043a\u043e\u043d\u0441\u043a\u0430\u044f',
', \u0441\u0443\u043a\u0438\u043d\u044b \u0434\u0435\u0442\u0438',
', \u043c\u0430\u0442\u0435\u0440\u044c \u0431\u043e\u0436\u044c\u044f',
', \u0434\u0435\u0440\u044c\u043c\u043e \u0441\u043e\u0431\u0430\u0447\u044c\u0435',
', \u043a\u0430\u043a \u0445\u0443\u0439 \u043c\u043e\u0440\u0436\u043e\u0432\u044b\u0439',
', \u0435\u0431\u0430\u043d\u044b\u0439 \u0441\u0432\u0435\u0442',
', \u0447\u0442\u043e\u0431 \u043d\u0435 \u043f\u0438\u0437\u0434\u0438\u0442\u044c',
', \u0431\u043b\u044f \u0431\u0443\u0434\u0443',
', \u0447\u0442\u043e\u0431 \u043c\u043d\u0435 \u0441\u0434\u043e\u0445\u043d\u0443\u0442\u044c',
', \u0431\u043b\u044f\u0434\u0441\u043a\u0430\u044f \u043f\u0430\u0440\u0430\u0448\u0430',
' \u0437\u0430\u0435\u0431\u0438\u0441\u044c \u043d\u0430 \u0445\u0443\u0439',
', \u043f\u0438\u0437\u0434\u0435\u0446 \u0431\u043b\u044f',
', \u0445\u0443\u0439\u043b\u043e \u0437\u0430\u043c\u043e\u0440\u0441\u043a\u043e\u0435',
', \u0435\u0431\u0430\u043b\u043e \u0431\u043b\u044f\u0434\u0441\u043a\u043e\u0435',
' \u0433\u043e\u0432\u043d\u0430 \u043a\u0443\u0441\u043e\u043a',
', \u0437\u0430\u043b\u0443\u043f\u0430 \u0441 \u0443\u0448\u0430\u043c\u0438',
', \u043f\u0438\u0437\u0434\u0430 \u0441 \u0443\u0448\u0430\u043c\u0438',
' \u0436\u043e\u043f\u0430 \u0441\u0440\u0430\u043d\u0430\u044f',
', \u0440\u0430\u0437\u0431\u0435\u0440\u0438 \u0442\u0435\u0431\u044f \u043f\u043e\u043d\u043e\u0441',
', \u0435\u0431\u0430\u0442\u044c \u043c\u043e\u0438 \u043c\u043e\u0437\u0433\u0438, \u043a\u0430\u043a \u0433\u043e\u0432\u043e\u0440\u044f\u0442 \u0444\u0440\u0430\u043d\u0446\u0443\u0437\u044b',
', \u0435\u0431\u0438\u0441\u044c \u043e\u043d\u043e \u0432\u0441\u0435 \u0440\u0430\u043a\u043e\u043c',
', \u0436\u043e\u043f\u0430 \u0432\u043e\u043b\u043e\u0441\u0430\u0442\u0430\u044f',
', \u043c\u0443\u0434\u0438\u043b\u0430 \u0433\u0440\u0435\u0448\u043d\u0430\u044f',
', \u043c\u0443\u0434\u0438\u043b\u0430 \u0433\u0440\u0435\u0448\u043d\u0430\u044f',
', \u043c\u0443\u0434\u0438\u043b\u0430 \u0433\u0440\u0435\u0448\u043d\u0430\u044f'];


function pickRandomWord() {	
	return words[Math.floor(Math.random() * words.length)];
}

function replacer (m, m1, m2) {
	if (Math.random() > 0.5) {
		return m1 + pickRandomWord() + m2;
	} else return m;
}

var substRegex = /([\u0410-\u042f\u0430-\u044f]\s*)([.,?!\)]+)/gi;

function xform(s) { return s.replace(substRegex, replacer); } 

// replace in title 
if(document.title) document.title = xform(document.title); 

// replace in body text 
if (document.evaluate) { 
	//with XPath support
	var textnodes = document.evaluate( "//body//text()", document, null, XPathResult.UNORDERED_NODE_SNAPSHOT_TYPE, null); 
	for (var i = 0; i < textnodes.snapshotLength; i++) { 
		 node = textnodes.snapshotItem(i); 
		 node.data = xform(node.data);
	}
} else {
	// no XPath -- do recursive
	function processNode(node) {
	    // is this a text node?
    	if (node.nodeType == 3) {
        	node.data = xform(node.data);
	    } else  if (node.nodeType == 1) {
		    var i;                   
			for (i = 0; i < node.childNodes.length; i++) {
	        	processNode(node.childNodes[i]);
	    	}
		}
	}
	processNode(document.body);
}

})();
