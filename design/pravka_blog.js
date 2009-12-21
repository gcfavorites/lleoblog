// процедура правки v2.0
//
// (с)LLeo 2009 для проекта блогодвижка http://lleo.aha.ru/blog/
//
// за бесценные советы, дизайн вспывающего окошка и процедуры работы с выделением - спасибо Михаилу Валенцеву http://valentsev.ru
//
// не забудьте подсоединить процедуры pins.js

var opecha;
var opecha_id;
var opecha_id_go;
var leftHelper;
var topHelper;
var site_id;
var textarea_cols=40;
var Nx = 630;
var helper_napomni=2;

window.onload = function() {

message = document.getElementById('message');
helperItem = document.getElementById('helper');
screenWidth = document.body.clientWidth; window.onresize = function() { screenWidth = document.body.clientWidth; }

  // 1 - Браузеры. 2 - IE. 3 - Неизвестно.
var testRange = (document.createRange) ? 1 : (message.createTextRange && message.createTextRange() != undefined) ? 2 : 3;

// === MOUSE ===

document.onmouseup = function(e) { if(!e) e = window.event;
	opecha=(document.selection) ? document.selection.createRange().text : window.getSelection(); opecha += '';
        switch(testRange) { // Браузеры
            case 1: if(window.getSelection().anchorNode) testSelection = window.getSelection().anchorNode; break;
            case 2: var testSelection = document.selection.createRange().parentElement(); break; // IE
	    }
        if(testRange != 3 && testSelection && opecha!='') {           // Поиск автора выделенного текста.
            while( ( testSelection.tagName != 'DIV' || testSelection.id == '' || testSelection.id == undefined ) 
			&& testSelection.parentNode != undefined) { testSelection=testSelection.parentNode; }
		if(testSelection.id == undefined) { opecha_id=0; return; }

		//alert("DIV=" + testSelection.id); return;
		opecha_id=testSelection.id; helper_pos_get(e);

		// if(helperItem.style.display!='block') {
		if(admin) { return helper_go(); }
		if(helper_napomni) { helper_napomni--; salert("Нашли опечатку? Нажмите Ctrl+Enter",1000); }
		// }
	}
}

// === KEYBOARD ===

document.onkeydown = function(e) { if(!document.getElementById) return; if(window.event) e=window.event;
	var key = (e.keyCode ? e.keyCode : e.which ? e.which : null); // alert('key:' + key);
	var link=0; switch(key) {
          case 0x27: if(e.ctrlKey && !ctrloff) link=document.getElementById('NextLink'); break;
          case 0x25: if(e.ctrlKey && !ctrloff) link=document.getElementById('PrevLink'); break;
          case 0x26: if(e.ctrlKey && !ctrloff) link=document.getElementById('UpLink'); break;
          case 0x28: if(e.ctrlKey && !ctrloff) link=document.getElementById('DownLink'); break;
          case 0x24: if(e.ctrlKey && !ctrloff) link='/'; break; // Home
	  case 0x1B: if(helperItem.style.display == 'block') { sclose(); return false; } break; // ESC
          case 0x0D:
		if(helperItem.style.display == 'block') { var T=setTimeout('sendoshibka();',1); return false; } // Enter
		if(e.ctrlKey) { var T=setTimeout('helper_go();',1); return false; }
		break; // Enter
        } if(link && link.href) document.location.href = link.href;

}

};


function helper_go() { if(opecha_id==0 || opecha=='' || opecha_id==undefined) return; // Сам обработчик опечаток
	var body = stripp(document.getElementById(opecha_id).innerHTML);
	if(body.length <1024) { /* opecha=brp2nl(body); */ }
	if(opecha.length>1024) { /* salert('Много текста. Выделите поменьше.',2000); */ return; }
	var opecha_html = stripp(nl2brp(opecha));
	var n=scount(body.replace(/onclick=\"cut\(this,\'.*?\',\d\)\">/gi,"") ,opecha_html);
if(n>1) { return salert('Строк "'+opecha+'" в блоке "'+opecha_id+'" содержится '+n+'!<br>Попробуйте выделить более длинный кусок.',3000); }
if(n<1) { return salert('Ошибка: возможно, попался абзац?<br>Попробуйте выделить словосочетание без абзаца.',3000); }
	opecha_id_go=opecha_id;
	return stextarea(opecha,opecha_id);
}

function helper_pos_get(e) { // Позиция курсора мыши
        if(e.pageX || e.pageY) {
          leftHelper = e.pageX;
          topHelper = e.pageY;
        } else {
          leftHelper = e.clientX + (document.documentElement.scrollLeft || document.body.scrollLeft) - document.documentElement.offsetLeft;
          topHelper = e.clientY + (document.documentElement.scrollTop || document.body.scrollTop) - document.documentElement.offsetTop;
        }
	return;
}

function helper_pos_set() { // Установка позиции для окна
	topHelper = topHelper; // - Nx/2;
	leftHelper = leftHelper; // - Nx/2
	if(leftHelper < 0) leftHelper = 0;
	if(topHelper < 0) topHelper = 0;
	if(leftHelper + Nx > screenWidth ) leftHelper = screenWidth - Nx;
        helperItem.style.top = topHelper + 'px'; // - 50 
        helperItem.style.left = leftHelper + 'px'; //- 5 
}


function page(l) {  return (l.length / textarea_cols + ('\n'+l).match(/\n/g).length + 1); }

function salert(l,t) { 
	document.getElementById('helper_body').innerHTML='\
<table border=0 cellspacing=0 cellpadding=0><tr valign=top><td>'+l+'</td>\
<td><div id=sert onclick="sclose()" class=canceledit title="cancel"></td>\
</tr></table>';
	helper_pos_set();
	helperItem.style.display = 'block';
//	document.getElementById('sert').focus();
	setTimeout("sclose()", t);
	return false;
}

function sclose() { document.getElementById('helper_body').innerHTML=''; helperItem.style.display = 'none'; return false;}

function stextarea(opecha,id) {
	document.getElementById('helper_body').innerHTML='\
<table border=0 cellspacing=0 cellpadding=0><tr valign=top><td rowspan=2>\
'+(admin?'':'<span style="font-size: 9px;">исправь опечатку и нажми Enter:</span><br>')+'\
<textarea class="pravka_textarea" id="message" name="message" class=t cols='+textarea_cols+' rows=' + page(opecha) + '>'+opecha+'</textarea>\
</td><td align=right><div onclick="sclose()" class=canceledit title="cancel"></div></td>\
</tr><tr><td align=right valign=center>\
'+(admin?'<a href="'+wwwhost+'editor?Date='+dnevnik_data+'"><div class=fmedit style="padding-top:10px;"></div></a>':'')+'\
<a onclick=\'insert_n(document.getElementById("message"));\'><div class=fmn></div></a>\
<a onclick=\'pins(document.getElementById("message"),"\251","");\'><div class=fmcopy></div></a>\
<a onclick=\'pins(document.getElementById("message"),"\227","");\'><div class=fmmdash></div></a>\
<a onclick=\'pins(document.getElementById("message"),"\253","\273");\'><div class=fmltgt></div></a>\
</td></tr></table>';
	helper_pos_set(); // установить окно куда надо
	helperItem.style.display = 'block';
	document.getElementById('message').focus();
	return false;
}



function scount(str,s) { var i=0,c=0; while((i=str.indexOf(s,++i))>0) c++; return c; }
function nl2brp(s) { s=s.replace(/\n\n/gi,"<p>"); s=s.replace(/\n/gi,"<br>"); return s; }
function brp2nl(s) { s=s.replace(/<p>/gi,"\n\n"); s=s.replace(/<br>/gi,"\n"); return s; }
function stripp(s) { return s.replace(/<\/p>/gi,""); }

function sendoshibka() {
	opecha=stripp(document.getElementById('message').defaultValue);
	var opechanew=stripp(document.getElementById('message').value);
	if(opecha==opechanew) { salert("",1); return; }
	document.getElementById('helper_body').innerHTML=' &nbsp; '; // отправляю
//	alert(opecha_id_go); return;
	var kuda=document.getElementById(opecha_id_go);
	var body=stripp(document.getElementById(opecha_id_go).innerHTML);

if(opecha_id_go=='Body') var data='@dnevnik_zapisi@Body@Date@'+dnevnik_data;
else if(opecha_id_go=='Header') var data='@dnevnik_zapisi@Header@Date@'+dnevnik_data;
else if(opecha_id_go.substring(0,1)=='a') var data='@dnevnik_comments@Answer@id@'+opecha_id_go.replace(/^a/,'');
else var data='@dnevnik_comments@Commentary@id@'+opecha_id_go;

//	alert(data); return;

  if(opechanew && opechanew.length != 0 ) {
    JsHttpRequest.query(ajax_pravka, {
        action: 'opechatka',
        data: data,
        hash: hashpage,
        text: opecha, textnew: opechanew },
    function(responseJS, responseText) {
        if(responseJS.newbody) {
                var ss = body.indexOf(nl2brp(opecha));
                var es = ss + nl2brp(opecha).length;
                var t1 = body.substring(0,ss); // текст перед
                var t2 = body.substring(es,body.length); // текст после
                kuda.innerHTML = t1 + nl2brp(responseJS.newbody) + t2;
		sclose();
		window.onload();
        }
        if(responseJS.otvet) { salert(responseJS.otvet,10000); }
    },true);
  } else { salert('совсем пустое - нельзя',3000); }
}


