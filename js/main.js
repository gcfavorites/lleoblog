/*
var hashpage='".$GLOBALS['hashpage']."';
var wwwhost='".$GLOBALS['wwwhost']."';
var admin=".($GLOBALS['admin']?1:0).";
var mypage='".$GLOBALS['httpsite'].$GLOBALS['mypage']."';
var uc='".$GLOBALS['uc']."';
var www_js='".$GLOBALS['www_js']."';
var www_css='".$GLOBALS['www_css']."';
var wwwcharset='".$GLOBALS['wwwcharset']."';
var www_design='".$GLOBALS['www_design']."';
var www_ajax='".$GLOBALS['www_ajax']."';
var page_onstart=[];
function nokey(){ page_onstart.push("mHelps['nokey']=1;"); }
*/

//function basename(path) { return path.replace(/^.*[\/\\]/g,''); }


var hid=1;
var mHelps={};
var hotkey=[]; //code,(ctrlKey,shiftKey,altKey,metaKey),func

keycodes={right:0x27,left:0x25,up:0x26,down:0x28,esc:0x1B,enter:0x0D,home:0x24,tab:9,del:46,
'А':'1040','а':'1072','Б':'1041','б':'1073','В':'1042','в':'1074','Г':'1043','г':'1075','Д':'1044','д':'1076',
'Е':'1045','е':'1077','Ё':'1025','ё':'1105','Ж':'1046','ж':'1078','З':'1047','з':'1079','И':'1048','и':'1080',
'Й':'1049','й':'1081','К':'1050','к':'1082','Л':'1051','л':'1083','М':'1052','м':'1084','Н':'1053','н':'1085',
'О':'1054','о':'1086','П':'1055','п':'1087','Р':'1056','р':'1088','С':'1057','с':'1089','Т':'1058','т':'1090',
'У':'1059','у':'1091','Ф':'1060','ф':'1092','Х':'1061','х':'1093','Ц':'1062','ц':'1094','Ч':'1063','ч':'1095',
'Ш':'1064','ш':'1096','Щ':'1065','щ':'1097','Ъ':'1066','ъ':'1098','Ы':'1067','ы':'1099','Ь':'1068','ь':'1100',
'Э':'1069','э':'1101','Ю':'1070','ю':'1102','Я':'1071','я':'1103'};
keykeys={ctrl:8,shift:4,alt:2,meta:1};

function setkey(k,v,f,o){ if(typeof k == 'string') var k=[k]; for(var i in k) 
if(typeof k[i] == 'string') // какой-то немыслимый йобанный патч от prototype, который навешивает говна
setkey0(k[i],v,f,o); }
function setkey0(k,v,f,o){ // повесть функцию на нажатие клавиши
	k=(!isNaN(k) && k.length>1) ? k : keycodes[k] ? keycodes[k] : k.toUpperCase().charCodeAt();
        var e=0; for(var i in keykeys) if(v.indexOf(i)>=0) e+=keykeys[i];
	for(var i in hotkey)if(hotkey[i][0]==k && hotkey[i][1]==e){ // если уже есть - изменить
		if(typeof f == 'undefined' || f=='') delete hotkey[i];
		else hotkey[i]=[k,e,f,o]; //hotkey[i][2]=f; - А ТАК НЕ РАБОТАЕТ СУКА ЙОБАНЫЙ ГЛЮЧНЫЙ JS!!!
		return;
	} if(typeof f == 'undefined' || f=='') return;
	if(e) hotkey.push([k,e,f,o]); else hotkey.unshift([k,e,f,o]); // иначе - задать
}

function rel_redirect(id){ var e=idd(id); if(e && e.href && !isHelps()) { 
if(id=='PrevLink'){ var b=document.body,i=curX-startX; if(i<0)i=-i; b.style.left=i+'px'; setOpacity(b,0.5); }
else if(id=='NextLink'){ var b=document.body,i=curX-startX; if(i<0)i=-i; b.style.right=i+'px'; setOpacity(b,0.5); }
// function pre_redirect(){document.body.style.left=(curX-startX)+'px'; setOpacity(document.body,0.5);}
//      if(swipeDirection=='right') { rel_redirect('PrevLink',pre_redirect); }
//      if(swipeDirection=='left') { rel_redirect('NextLink',pre_redirect); }
// if(swipeDirection=='up') if(swipeDirection=='down')
document.location.href=e.href; } }
function hotkey_reset() { hotkey=[];
if(admin) {
	setkey('x','alt',function(e){
alert('\ngetScrollH()='+getScrollH()+'\ngetScrollW()='+getScrollW()+'\ngetWinW()='+getWinW()+'\ngetWinH()='+getWinH()+'\ngetDocH()='+getDocH()+'\ngetDocW()='+getDocW()+'\n\n = '+(getWinW()-getDocW()));
},false);
	setkey(['E','У','у'],'',function(e){majax('editor.php',{a:'editform',num:num,comments:(idd('commpresent')?1:0)})},false); // редактор заметки
	setkey(['N','Т','т'],'',function(e){majax('editor.php',{a:'newform',hid:hid})},false); // новая заметка
}

setkey(['ctrl+A','ctrl+А','ctrl+а'],'alt shift ctrl',function(e){keyalert=1;talert('Скан клавиш включен',1000);},false); // включение сканкодов
setkey(['U','Г','г'],'',function(e){majax('login.php',{action:'openid_form'})},true); // личная карточка
setkey(['D','В','в'],'',function(e){document.location.href=wwwhost;},true); // в блог
setkey(['K','Л','л'],'',function(e){document.location.href=wwwhost+'comms';},true); // комментарии
setkey(['right','7'],'',function(e){rel_redirect('NextLink')},true);
setkey(['left','4'],'',function(e){rel_redirect('PrevLink')},true);
setkey([' ','A','Ф','ф'],'',function(e){ if(admin && !isHelps()) ipadfinger4() },true);
setkey('up','ctrl',function(e){rel_redirect('UpLink')},true);
setkey('down','ctrl',function(e){rel_redirect('DownLink')},true);
setkey('home','ctrl',function(e){document.location.href='/'},true);
setkey('esc','',function(e){clean(isHelps())},true); // закрыть последнее окно
setkey('enter','ctrl',function(e){if(!isHelps()) helper_go()},true); // если не открыто окон - окно правки
} hotkey_reset();


// 1 - Браузеры. 2 - IE. 3 - Неизвестно.
function browser(){ return (document.createRange) ? 1 : (-[1,]) ? 3 : 2; }

function idd(id) { return document.getElementById(id); }
function zabil(id,text) { if(idd(id)) { idd(id).innerHTML=text; init_tip(idd(id)); } }
function doclass(cla,f,s) { var p=getElementsByClass(cla); for(var i in p) if(p[i].className==cla) f(p[i],s); }
function zabilc(cla,s) { doclass(cla,function(e,s){e.innerHTML=s;},s); }

function vzyal(id) { return idd(id)?idd(id).innerHTML:''; }
function zakryl(id) { idd(id).style.display='none'; if(id!='tip') zakryl('tip'); }
function otkryl(id) { idd(id).style.display='block'; }
function tudasuda(id) { if(idd(id).style.display=='none') otkryl(id); else zakryl(id); }

function cphash(a) { var b={}; for(var k in a) b[k]=a[k]; return b; }
function cpmas(a) { var b=[]; for(var i=0;i<a.length;i++){ if(typeof a[i] !='undefined') b[i]=a[i]; } return b; }

// var oknon=0; if(oknon) return 1; 
function isHelps(){ var c=0; for(var k in mHelps) c++; return c?k:false; }

function print_r(a) { var s=''; for(var k in a) { var v=a[k]; s='\n'+k+'='+v+s; } return s; }
function in_array(s,a){ var l; for(l in a) if(a[l]==s) return l; return false; }

function clean(id) {
if(typeof mHelps[id]!='undefined'){ hotkey=cpmas(mHelps[id]); delete(mHelps[id]); }
if(isHelps()==false) hotkey_reset();
if(idd(id)) { zakryl(id); setTimeout("var s=idd('"+id+"'); if(s) s.parentNode.removeChild(s);", 40); }
zakryl('tip');
}

var JSload={};

function inject(src){ if(src.indexOf('://')<0) src=www_ajax+src; loadScr(src); }
function loadScript(src){ if(src.indexOf(':')<0) src=www_js+src; if(JSload[src]=='load') return; loadScr(src); }

function loadScr(src){
        var s = document.createElement('script');
        s.setAttribute('type','text/javascript');
        s.setAttribute('charset', wwwcharset);
        s.setAttribute('src',src);
        // IE crashes on using appendChild before the head tag has been closed.
        var head=document.getElementsByTagName('head').item(0);
        head.insertBefore(s, head.firstChild);
	ajaxon();
}




function loadScriptBefore(src,runtext){
	if(JSload[src]=='load') return eval(runtext); if(JSload[src]) return; JSload[src]=runtext; loadScript(src);
}

function loadCSS(src){ src=www_css+src;
	if(JSload[src]=='load') return; JSload[src]='load';
	var headID = document.getElementsByTagName('head')[0];
	var s = document.createElement('link');
	s.type = 'text/css';
	s.rel = 'stylesheet';
	s.href = src;
	s.media = 'screen';
	headID.appendChild(s);
	ajaxon();
}


if(document.getElementsByClassName) getElementsByClass=function(classList,node){
return (node||document).getElementsByClassName(classList)};
else {
    getElementsByClass = function(classList, node) {
	        var node = node || document, list = node.getElementsByTagName('*'),
	        length = list.length, classArray = classList.split(/\s+/),
	        classes = classArray.length, result = [], i,j;

//	        for(i = 0; i < length; i++) { list[i].className='r'; alert(i+': '+list[i].className); }
//	alert('#'+print_r(list[16]));
//	return;

	        for(i = 0; i < length; i++) {
	            for(j = 0; j < classes; j++) {
	                if(list[i].className.search('\\b' + classArray[j] + '\\b') != -1) { // alert(1);
	                    result.push(list[i]);
	                    break;
	                }
	            }
	        }
	        return result;
    };

//page_onstart.push("alert('#');");
//eeeeeeeeeeeeeee
// alert(print_r(getElementsByClass('p')));
// getElementsByClass('r');

}

// создать новый <DIV class='cls' id='id'>s</div> в элементе paren (если не указан - то просто в документе)
// есть указан relative - то следующим за relative, инае - просто последним
function mkdiv(id,s,cls,paren,relative){ if(idd(id)) { idd(id).innerHTML=s; idd(id).className=cls; return; }
        var div=document.createElement('DIV'); div.className=cls; div.id=id; div.innerHTML=s; div.style.display='none';
        if(paren==undefined) paren=document.body;
	var r = relative==undefined ? 0 : relative.nextSibling; // paren.lastChild
	if(r) paren.insertBefore(div,r); else paren.appendChild(div);
}

function posdiv(id,x,y) { // позиционирование с проверкой на вылет, если аргумент '-1' - по центру экрана
        var e=idd(id);
	if(e.style.display!='block') otkryl(id);
        var W=getWinW(); var H=getWinH();
        var w=e.clientWidth; var h=e.clientHeight;
	if(x==-1) x=(W-w)/2+getScrollW();
	if(y==-1) y=(H-h)/2+getScrollH();
	var DH=W-10; if(w<DH && x+w>DH) x=DH-w; if(x<0) x=0; 
	DH=getDocH()-10; if(h<DH && y+h>DH) y=DH-h; if(y<0) y=0;
        e.style.top=y+'px'; e.style.left=x+'px';
	otkryl(id);
}

function addEvent(e,evType,fn) {
	if(e.addEventListener) { e.addEventListener(evType,fn,false); return true; }
	if(e.attachEvent) { var r = e.attachEvent('on' + evType, fn); return r; }
	e['on' + evType] = fn;
}

function removeEvent(e,evType,fn){
	if(e.removeEventListener) { e.removeEventListener(evType,fn,false); return true; }
	if(e.detachEvent) { e.detachEvent('on'+evType, fn) };
}

function helps_cancel(id,f) { getElementsByClass('can',idd(id))[0].onclick=f; }
function helpc(id,s) { helps(id,s); posdiv(id,-1,-1); }
function ohelpc(id,z,s) { helpc(id,"<fieldset><legend>"+z+"</legend>"+s+"</fieldset>"); }
function idie(s) { ohelpc('idie','Error',s) }

function helps(id,s,pos) {
s=s+"<div onclick=\"clean('"+id+"')\" class='can' title='cancel'></div>";
/*
s=s+"<div style='position:absolute;bottom:0px;right:8px;cursor:pointer;'><a href='?module="+encodeURIComponent(majax_lastu.replace('.php',''));
for(var k in majax_lasta) s=s+'&'+encodeURIComponent(k)+'='+encodeURIComponent(majax_lasta[k]);
s=s+"'><img src='"+www_design+"e3/reload_page.png' border=0></a></div>";
*/

// img src='"+www_design+"e3/reload_page.png'

if(!idd(id)) {
	mkdiv(id,"<div class='corners'><div class='inner'><div class='content' id='"+id+"_body' align=left>"+s+"</div></div></div>",'popup');
	init_tip(idd(id));

// (c)mkm Вот рецепт локального счастья, проверенный в Опера10, ИЕ6, ИЕ8, FF3, Safari, Chrome.
// Таскать окно можно за 'рамку' - элементы от id до id+'_body', исключая body (и всех его детей).

var e_body=idd(id+'_body'); // За тело не таскаем
var hmov=false; // Предыдущие координаты мыши
var e=idd(id);
var pnt=e; while(pnt.parentNode) pnt=pnt.parentNode; //Ищем Адама

var mmFunc=function(ev) { if(!ev) ev=window.event;
	if(hmov) {
		e.style.left = parseFloat(e.style.left)+ev.clientX-hmov.x+'px';
		e.style.top = parseFloat(e.style.top)+ev.clientY-hmov.y+'px';
		hmov={ x:ev.clientX, y:ev.clientY };
		if(ev.preventDefault) ev.preventDefault();
		return false;
	}
};

var muFunc=function(){
	if(hmov){
		hmov=false;
		removeEvent(pnt,'mousemove',mmFunc);
		removeEvent(pnt,'mouseup',muFunc);
		e.style.cursor='auto';
	}
};

addEvent(e,'mousedown', function(ev){ if(hmov) return;
	if(!ev) ev=window.event;
	var lbtn=(window.addEventListener?0:1); //Если ИЕ, левая кнопка=1, иначе 0
	if(!ev.target) ev.target=ev.srcElement;
	if((lbtn!==ev.button)) return; //Это была не левая кнопка или 'тело' окна, ничего не делаем
	var tgt=ev.target;
	while(tgt){
		if(tgt==e_body) return;
		if(tgt==e) break;
		tgt=tgt.parentNode;
	};
	//Начинаем перетаскивать
	e.style.cursor='move';
	hmov={ x:ev.clientX, y:ev.clientY };
	addEvent(pnt,'mousemove',mmFunc);
	addEvent(pnt,'mouseup',muFunc);
	if(ev.preventDefault) ev.preventDefault();
	return false;
});
// ===========================================================================

hid++;
if(!pos) posdiv(id,mouse_x,mouse_y);
mHelps[id]=cpmas(hotkey);

} else zabil(id+'_body',s);
hotkey=[]; // обнулить для окна все шоткеи
setkey('esc','',function(e){clean(isHelps())},true); // закрыть последнее окно
setkey('enter','ctrl',function(e){if(!isHelps()) helper_go()},true); // если не открыто окон - окно правки

//alert('now1 id='+id+':'+print_r(mHelps));
}

// координаты мыши
var mouse_x=mouse_y=0; 
document.onmousemove = function(e){ if(!e) e=window.event;
  if(e.pageX || e.pageY) { mouse_x=e.pageX; mouse_y=e.pageY; }
  else if(e.clientX || e.clientY) {
    mouse_x = e.clientX + (document.documentElement.scrollLeft || document.body.scrollLeft) - document.documentElement.clientLeft;
    mouse_y = e.clientY + (document.documentElement.scrollTop || document.body.scrollTop) - document.documentElement.clientTop;
  }
};

function ajaxon(){ var id='ajaxgif'; mkdiv(id,"<img src="+www_design+"img/ajax.gif>",'popup'); posdiv(id,mouse_x,mouse_y);}
function ajaxoff(){ clean('ajaxgif'); }

function ajax(name,value,func) { ajaxon(); if(name.indexOf('://')<0) name=www_ajax+name;
JsHttpRequest.query(name,value,function(responseJS,responseText){if(responseJS.status){ajaxoff();eval(func);}},true);
}

var majax_lastu='',majax_lasta,majax_err=1; // =0

function tryer(er,e,js){
//if(!(e instanceof SyntaxError)) throw e; 
alert(er+': '+e.name+'\n\n'+js);}

function majax(url,a,js) { majax_lasta=cphash(a); majax_lastu=url; a['up']=up;
	ajax(url,a,"if(responseJS.modo){ if(majax_err) eval(responseJS.modo); else {try{eval(responseJS.modo)}catch(e){tryer('majax error',e,responseJS.modo)}}\
"+(typeof js=='undefined'?'':"try{eval(\""+js+"\")}catch(e){tryer('majax post-js error',e,\""+js+"\")}")+"\
}");}

function mijax(u,a) { a['up']=up; if(u.indexOf('://')<0) u=www_ajax+u; u+='?minj='+(new Date()).getTime();
	for(var i in a) u+='&'+encodeURIComponent(i)+'='+encodeURIComponent(a[i]);
	loadScr(u);
}

function setOpacity(e,n) { var o=getOpacityProperty(); if(!e || !o) return;
	if(o=='filter') { n *= 100; // Internet Exploder 5.5+
	// Если уже установлена прозрачность, то меняем её через коллекцию filters, иначе добавляем прозрачность через style.filter
		var oAlpha = e.filters['DXImageTransform.Microsoft.alpha'] || e.filters.alpha;
		if(oAlpha) oAlpha.opacity=n;
		else e.style.filter += 'progid:DXImageTransform.Microsoft.Alpha(opacity='+n+')'; // чтобы не затереть другие фильтры +=
	} else e.style[o]=n; // Другие браузеры
}

function getOpacityProperty() {
	if(typeof document.body.style.opacity == 'string') return 'opacity'; // CSS3 compliant (Moz 1.7+, Safari 1.2+, Opera 9)
	else if(typeof document.body.style.MozOpacity == 'string') return 'MozOpacity'; // Mozilla 1.6 и младше, Firefox 0.8 
	else if(typeof document.body.style.KhtmlOpacity == 'string') return 'KhtmlOpacity'; // Konqueror 3.1, Safari 1.1
	else if(document.body.filters && navigator.appVersion.match(/MSIE ([\d.]+);/)[1]>=5.5) return 'filter'; // IE 5.5+
	return false;
}

function getScrollH(){ return (document.documentElement.scrollTop || document.body.scrollTop); }
function getScrollW(){ return (document.documentElement.scrollLeft || document.body.scrollLeft); }

function getWinW(){ return window.innerWidth?window.innerWidth : document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientWidth:document.body.clientWidth; }
function getWinH(){ return window.innerHeight?window.innerHeight : document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientHeight:document.body.clientHeight; }
function getDocH(){ return document.compatMode!='CSS1Compat' ? document.body.scrollHeight : document.documentElement.scrollHeight; }
function getDocW(){ return document.compatMode!='CSS1Compat' ? document.body.scrollWidth : document.documentElement.scrollWidth; }


// --- процедуры pins ---
function insert_n(e) { var v = e.value;
var t1 = v.substring(0,e.selectionStart); // текст перед
var t2 = v.substring(e.selectionEnd,v.length); // текст после
var pp=GetCaretPosition(e);
e.value=t1.replace(/\s+$/,'') + "\n" + t2.replace(/^\s+/,'');
setCaretPosition(e, pp);
}

function ti(id,tmpl) {
var e=idd(id); var v=e.value; var ss=e.selectionStart; var es=e.selectionEnd;
var s=tmpl.replace(/\{select\}/g,v.substring(ss,es));
GetCaretPosition(e); e.value = v.substring(0,ss)+s+v.substring(es,v.length); setCaretPosition(e,ss+s.length);
e.selectionStart=ss; e.selectionEnd=ss+s.length;
}

var scrollTop=0;

function GetCaretPosition(e) { var p=0; // IE Support
if(document.selection){ e.focus(); var s=document.selection.createRange(); s.moveStart('character',-e.value.length); p=s.text.length; } // Firefox support
else if(e.selectionStart || e.selectionStart=='0') p=e.selectionStart;
scrollTop=e.scrollTop; return p;
}

function setCaretPosition(e,p) {
if(e.setSelectionRange){ e.focus(); e.setSelectionRange(p,p); }
else if(e.createTextRange){ var r=e.createTextRange(); r.collapse(true); r.moveEnd('character',p); r.moveStart('character',p); r.select(); }
e.scrollTop = scrollTop;
}





//======================================== jog
function valid_up(l) { var u=('#'+l).replace(/^#(\d+)\-[0-9ABCDEF]{32}$/gi,"$1"); return isNaN(u)||u==0?false:l; }

var unic_rest_flag=0; function unic_rest(i) { if(unic_rest_flag) return;
	var upo=valid_up(i?fc_read('up'):f5_read('up')); // прочитать из одного или другого хранилища
	if(up!=upo && upo!==false) {
		unic_rest_flag=1; return majax('restore_unic.php',{up:up,upo:upo,num:num,i:i}); // восстановить!
	}
	if(up!='candidat') return (i?fc_save('up',up):f5_save('up',up));
}

// if(typeof page_onstart == 'undefined') var page_onstart=[]; 
page_onstart.push('unic_rest(0)');

function c_save(n,v) { if(v===false||v===null) return false; var N=new Date(); N.setTime(N.getTime()+(v==''?-1:3153600000000)); document.cookie=n+'='+encodeURIComponent(v)+';expires='+N.toGMTString()+';path=/;'; }
function c_read(n) { var a=' '+document.cookie+';'; var c=a.indexOf(' '+n+'='); if(c==-1) return false; a=a.substring(c+n.length+2); return decodeURIComponent(a.substring(0,a.indexOf(';')))||false; }

var jog=false; function setIsReady(){
jog=(navigator.appName.indexOf("Microsoft")!=-1?window:document)["kuki"];
// jog=(browser()==2)?window['kuki']:document['kuki'];
if(!jog.flashcookie_read) jog=false;
if(!page_onstart.length) unic_rest(1); else page_onstart.push('unic_rest(1)');
}

function fc_read(n){ return jog?jog.flashcookie_read(n):false; }
function fc_save(n,v){ return (jog&&v!==false&&v!==null)?jog.flashcookie_save(n,v):false; }
function fc_saveif(n,v){ if(fc_read(n)!=v) fc_save(n,v); }

var f5s=('localStorage' in window) && window['localStorage']!==null ? window['localStorage'] : false;
function f5_read(n){ var v=f5s?f5s[n]:''; return (v==''||v==null)?false:v; }
function f5_save(n,v) { return f5s?(f5s[n]=v):false; }
function f5_saveif(n,v){ if(f5_read(n)!=v) f5_save(n,v); }

function f_save(n,v){ f5_saveif(n,v); fc_saveif(n,v); } // if(v.length<500) c_save(n,v); 
function f_read(n){ return f5_read(n)||fc_read(n)||c_read(n); }



// comments
var komsel_n=0,komsel_v='';
var comnum=0;
function kus(unic) { if(unic) majax('login.php',{action:'getinfo',unic:unic}); }// личная карточка
function kd(e) { if(confirm('Точно удалить?')) majax('comment.php',{a:'del',id:ecom(e).id}); } // del
function ked(e) { majax('comment.php',{a:'edit',comnu:comnum,id:ecom(e).id}); } // edit
function ksc(e) { majax('comment.php',{a:'scr',id:ecom(e).id}); } // screen/unscreen
function ko(e) { majax('comment.php',{a:'ans',id:ecom(e).id}); } // ans-0-1-undef
function rul(e) { majax('comment.php',{a:'rul',id:ecom(e).id}); } // rul-не rul
function ka(e) { e=ecom(e); majax('comment.php',{a:'comform',id:e.id,lev:e.style.marginLeft,comnu:comnum}); } // answer
function kpl(e) { majax('comment.php',{a:'plus',id:ecom(e).id}); } // +
function kmi(e) { majax('comment.php',{a:'minus',id:ecom(e).id}); } // -
function kl(e) { if(komsel_n!==0) idd(komsel_n).style.border=komsel_v;
	komsel_n=ecom(e).id; komsel_v=idd(komsel_n).style.border;
	idd(komsel_n).style.border='5px dotted red'; return true; } // link
function opc(e,num) { e=ecom(e); majax('comment.php',{a:'pokazat',dat:num,oid:e.id,lev:e.style.marginLeft,comnu:comnum}); } // показать
function ecom(e){while((e.id==''||e.id==undefined)&&e.parentNode!=undefined) e=e.parentNode; return e.id==undefined?0:e;}

// bigfoto - заебался отдельно пристыковывать
// BigLoadImg("http://lleo.aha.ru/tmp/img.php?text="+Math.random());

var BigImgMas={},bigtoti=0,bigtotp=0;
function bigfoto(i,p){ var Z=(typeof p == 'undefined');
	var n= Z ? i : n=i+','+p;
	if(typeof BigImgMas[n] == 'undefined'){ if(!Z && !idd("bigfot"+p+"_"+i)) return false;
	ajaxon(); BigImgMas[n]=new Image(); BigImgMas[n].src= Z ? n : idd("bigfot"+p+"_"+i).href; }
	if(!Z) { bigtoti=i; bigtotp=p; }
	if(BigImgMas[n].width*BigImgMas[n].height==0) { setTimeout('bigfoto('+(Z ? '"'+n+'"' : n)+')',200); return false; }
	ajaxoff();
	if(Z) var tt="<div id='bigfostr' class=br>"+n+"</div>"; else {
		var g=i; while(idd('bigfot'+p+'_'+g)) g++;
		var tt=(g>1?"<span id='bigfotn' onclick='fotos_prev()' title='&lt;--предыдущая'>"+(i+1)+"</span> / <span onclick='fotos_next()' title='следующая-&gt;'>"+g+"</span>":'')+(idd('bigfott'+p+'_'+i)?"&nbsp; &nbsp; <div style='display:inline' title='предыдущая/следующая: стрелки клавиатуры' id='bigfottxt'>"+vzyal('bigfott'+p+'_'+i)+'</div>':'');
		if(tt!='') tt="<div id='bigfostr' class=br"+(admin?" onclick=\"majax('editor.php',{a:'bigfotoedit',num:"+vzyal('bigfotnum'+p)+",i:"+i+",p:"+p+"})\"":"")+">"+tt+"</div>";
	}
	helps('bigfoto',"<img id='bigfotoimg' src='"+BigImgMas[n].src+"' onclick=\"clean('bigfoto')\">"+tt,1);
	var w=BigImgMas[n].width,h=BigImgMas[n].height,e=idd('bigfotoimg');
	var H=(getWinH()-20); if(h>H && H>480) { w=w*(H/h); h=H; e.style.height=H+'px'; }
	var W=(getWinW()-50); if(w>W && W>640) { h=h*(W/w); w=W; e.style.width=W+'px'; }
	if(idd('bigfostr')) idd('bigfostr').style.width=w+'px';
	posdiv('bigfoto',-1,-1);
	if(!Z) {
		setkey(['left','4'],'',function(){bigfoto(bigtoti-1,bigtotp)},false);
		setkey(['right','7'],'',function(){bigfoto(bigtoti+1,bigtotp)},false);
		// ipad
//	        ipadset(idd('bigfoto'),function(){
//		        if(swipeDirection=='right') return bigfoto(bigtoti-1,bigtotp);
//	        	if(swipeDirection=='left') return bigfoto(bigtoti+1,bigtotp);
//		});
		// ipad
	}
	return false;
}

// tip

function init_tip(w) { var tip_x=0,tip_y=15; if(!idd('tip')) mkdiv('tip','','qTip');
	var a,s,e; var el=['a','label','input','img','span','div'];
	for(var j=0;j<el.length;j++){ e=w.getElementsByTagName(el[j]); if(e){ for(var i=0;i<e.length;i++){ a=e[i];
		s=a.getAttribute('title')||a.getAttribute('alt');
		if(s && a.onMouseOver==undefined){ a.setAttribute('tiptitle',s);
		a.removeAttribute('title'); a.removeAttribute('alt');
		addEvent(a,'mouseover',function(){ idd('tip').innerHTML=this.getAttribute('tiptitle'); posdiv('tip',mouse_x+tip_x,mouse_y+tip_y); } );
		addEvent(a,'mouseout',function(){ zakryl('tip') } );
		addEvent(a,'mousemove',function(){ posdiv('tip',mouse_x+tip_x,mouse_y+tip_y) } );
//		a.onMouseOver=function(){ idd('tip').innerHTML='#########'; otkryl('tip'); };
//		a.onMouseOut=function(){zakryl('tip')};
//		a.onMouseMove=function(e){posdiv('tip',mouse_x+tip_x,mouse_y+tip_y)}
		}
	}}}
}

page_onstart.push("init_tip(document)");

//==========
// процедура правки v2.1
//
// (с)LLeo 2009 для проекта блогодвижка http://lleo.aha.ru/blog/
//
// за бесценные советы, дизайн вспывающего окошка и процедуры работы с выделением - спасибо Михаилу Валенцеву http://valentsev.ru

var opecha;
var opechanew;
var opecha_id;
var opecha_id_go;
var leftHelper;
var topHelper;
var site_id;
var textarea_cols=40;
var Nx = 630;
var helper_napomni=1;
if(!hashpresent) var hashpresent='1';
var eventkey,lastkeycode,lastkeykey,keyalert=0;

window.onload = function() {

// === KEYBOARD === http://www.asquare.net/javascript/tests/KeyCode.html
document.onkeypress = function(e){ lastkeycode=(e.keyCode ? e.keyCode :e.which ? e.which : null); };

document.onkeyup = function(e){ if(keyalert) var T=setTimeout('keyprint()',50);
if(eventkey!==0 || lastkeycode==0) return; return keydo(e,lastkeycode);
};

document.onkeydown = function(e) { if(!e) e=window.event; eventkey=0; var k=(e.keyCode ? e.keyCode : e.which ? e.which : 0);
if(k===0) return; eventkey=e; lastkeykey=k; return keydo(e,k);
};


// === KEYBOARD ===

// screenWidth=document.body.clientWidth;

window.onresize=function(){
	screenWidth=document.body.clientWidth;
	if((getWinW()-getDocW())<15) mHelps['Wscroll']=1; else delete(mHelps['Wscroll']);
};

window.onresize();

// === MOUSE ===
document.onmouseup=function(e){ if(!e) e = window.event;
	if(isHelps()) return; // Если уже есть открытые окна - нах правку!

	opecha=(document.selection) ? document.selection.createRange().text : window.getSelection(); opecha += '';

        switch(browser()) { // Браузеры
            case 1: if(window.getSelection().anchorNode) testSelection = window.getSelection().anchorNode; break;
            case 2: var testSelection = document.selection.createRange().parentElement(); break; // IE
	    }
        if(browser() != 3 && testSelection && opecha!='') { // Поиск автора выделенного текста.
            while( ( testSelection.tagName != 'DIV' || testSelection.id == '' || testSelection.id == undefined ) 
			&& testSelection.parentNode != undefined) { testSelection=testSelection.parentNode; }
		if(testSelection.id == undefined) { opecha_id=0; return; }

		//alert("DIV=" + testSelection.id); return;
		opecha_id=testSelection.id;

		if(!helper_napomni) return helper_go();
		else { if(++helper_napomni < 3) {
				salert("Опечатка? Нажмите Ctrl+Enter",2000);
				setkey('enter','ctrl',function(e){clean('salert');helper_go()},false);
				idd('salert').onclick=function(){clean('salert');helper_go();};
				}
 		}
	}
};

for(var i in page_onstart) eval(page_onstart[i]); page_onstart=[];
};

function helper_go() { if(opecha_id==0 || opecha=='' || opecha_id==undefined) return; // Сам обработчик опечаток
	var body = stripp(vzyal(opecha_id));
	if(body.length <1024) { /* opecha=brp2nl(body); */ }
	if(opecha.length>1024) { /* salert('Много текста. Выделите поменьше.',2000); */ return; }
	var opecha_html = stripp(nl2brp(opecha));
	var n=scount(body.replace(/onclick="cut\(this,\'.*?\',\d\)">/gi,"") ,opecha_html);
if(n>1) { return salert('Строк "'+opecha+'" в блоке "'+opecha_id+'" содержится '+n+'!<br>Попробуйте выделить более длинный кусок.',3000); }
if(n<1) { return; /* salert('Ошибка: возможно, попался абзац?<br>Попробуйте выделить словосочетание без абзаца.',3000);*/ }
	opecha_id_go=opecha_id;
	return stextarea(opecha,opecha_id);
}

function page(l) { return (l.length / textarea_cols + ('\n'+l).match(/\n/g).length + 1); }

function salert(l,t) {
helpc('salert',"<div style='padding:20px; border: 1px dotted #cccccc'>"+l+"</div>"); if(t) setTimeout("clean('salert')",t); return false;
}

function stextarea(opecha,id) {
	helps('opechatku','\<table border=0 cellspacing=0 cellpadding=0><tr valign=top><td rowspan=2>\
'+(admin?'':'<span style="font-size: 9px;">исправь опечатку и нажми Enter:</span><br>')+'\
<textarea class="pravka_textarea" id="message" name="message" class=t cols='+textarea_cols+' rows=' + page(opecha) + '>'+opecha+'</textarea>\
</td></tr><tr><td align=right valign=center>\
'+(admin?'<div class=ll onclick=\'clean("opechatku");majax("editor.php",{a:"editform",num:'+num+'})\'>editor</div>':'')+'\
<a onclick=\'insert_n(idd("message"));\'><div class=fmn></div></a>\
<a onclick=\'ti("message","\251{select}")\'><div class=fmcopy></div></a>\
<a onclick=\'ti("message","\227{select}")\'><div class=fmmdash></div></a>\
<a onclick=\'ti("message","\253{select}\273")\'><div class=fmltgt></div></a>\
</td></tr></table>');
setkey('esc','',function(a,b){helper_napomni=3; clean('opechatku')}); // убирать по ESC
setkey('enter','',function(a,b){sendoshibka()},false); // отправлять по Enter
setkey('enter','ctrl',function(a,b){sendoshibka()},false); // отправлять по Ctrl+Enter
helps_cancel('opechatku',function(){ helper_napomni=3; clean('opechatku')});
idd('message').focus();
return false;
}

function scount(str,s) { var i=0,c=0; while((i=str.indexOf(s,++i))>0) c++; return c; }
function nl2brp(s) { s=s.replace(/\n\n/gi,"<p>"); s=s.replace(/\n/gi,"<br>"); return s; }
function brp2nl(s) { s=s.replace(/<p>/gi,"\n\n"); s=s.replace(/<br>/gi,"\n"); return s; }
function stripp(s) { return s.replace(/<\/p>/gi,""); }

function sendoshibka() {
	opecha=stripp(idd('message').defaultValue);
	opechanew=stripp(idd('message').value);
	clean('opechatku');

	if(opecha==opechanew) return;

if(opecha_id_go=='Body') var data='@dnevnik_zapisi@Body@Date@'+dnevnik_data;
else if(opecha_id_go=='Header') var data='@dnevnik_zapisi@Header@Date@'+dnevnik_data;
else if(opecha_id_go.substring(0,1)=='a') var data='@dnevnik_comment@Answer@id@'+opecha_id_go.replace(/^a/,'');
else if(opecha_id_go.indexOf('Body_')!=-1) var data='@dnevnik_zapisi@Body@num@'+opecha_id_go.substr(5);
else if(opecha_id_go.indexOf('Header_')!=-1) var data='@dnevnik_zapisi@Header@num@'+opecha_id_go.substr(7);

else var data='@dnevnik_comm@Text@id@'+opecha_id_go;

if(opechanew && opechanew.length!=0) {
	var body=stripp(vzyal(opecha_id_go));
	var ss=body.indexOf(nl2brp(opecha));
	var es=ss+nl2brp(opecha).length;
majax('ajax_pravka.php',{action:'opechatka',
opecha_id_go:opecha_id_go,ss: ss,es: es,
data:data,hash:hashpage,hashpresent:hashpresent,text:opecha,textnew:opechanew});
helper_napomni=0;
} else { salert('совсем пустое - нельзя',3000); }
}



function keydo(e,k) { var ct=e.metaKey+2*e.altKey+4*e.shiftKey+8*e.ctrlKey;

	if(typeof mHelps['nonav'] !== 'undefined') return true;

	// не обрабатывать коды браузера:
	if(k==keycodes.right && ct==keykeys.alt) return true;
	if(k==keycodes.left && ct==keykeys.alt) return true;

	for(var i in hotkey) if(hotkey[i][0]==k && hotkey[i][1]==(hotkey[i][1]&ct))
	{ var T=setTimeout('hotkey['+i+'][2](eventkey)',50); return hotkey[i][3]; }
}

function keyprint(){ talert("code: "+lastkeycode+' &nbsp; key: '+lastkeykey,800); }

function talert(s,t){ mkdiv('talert',s,'qTip'); posdiv('talert',-1,-1); if(t) setTimeout("clean('talert')",t); }

function gethash_c(){ return 1*document.location.href.replace(/^.*?#(\d+)$/g,'$1'); }

function get_pole_ara(w) { var k=0,ara={names:''}; var el=['input','textarea','checkbox','select'];
        for(var j=0;j<el.length;j++){
                var e=idd(w).getElementsByTagName(el[j]); for(i=0;i<e.length;i++)
                        if(typeof e[i].name != 'undefined' && e[i].name!='')
                                { ara[e[i].name]=e[i].value; ara['names']+=' '+e[i].name; k++; }
        }

        return (k==0?false:ara);
}

function nokey(){ hotkey=[]; mHelps['nokey']=1; }

// ----------
// функция постит объект-хэш content в виде формы с нужным action, target
// напр. postToIframe({a:5,b:6}, '/count.php', 'frame1')
function postToIframe(ara,url,iframeID){
    if(typeof phonyForm == 'undefined'){ // временную форму создаем, если нет
        phonyForm=document.createElement("form"); phonyForm.style.display="none";
        phonyForm.enctype="application/x-www-form-urlencoded"; phonyForm.method="POST";
        document.body.appendChild(phonyForm);
    }
    phonyForm.action=url; phonyForm.target=iframeID; phonyForm.setAttribute("target",iframeID);
    // убить все содержание из временной формы
    while(phonyForm.firstChild){ phonyForm.removeChild(phonyForm.firstChild); }
    // заполнить форму данными из объекта
    for(var x in ara){ var tn;
        if(browser.isIE){
	  tn=document.createElement("<input type='hidden' name='"+x+"' value='"+ara[x]+"'>"); phonyForm.appendChild(tn);
        }else{
            tn=document.createElement("input"); phonyForm.appendChild(tn);
            tn.type="hidden"; tn.name=x; tn.value=ara[x];
        }
    }
    phonyForm.submit();
}
// ----------