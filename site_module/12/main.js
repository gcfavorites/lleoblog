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
*/

//function basename(path) { return path.replace(/^.*[\/\\]/g,''); }


var comnum=0;
var hid=1;
var mHelps={};
var hotkey=[]; //code,(ctrlKey,shiftKey,altKey,metaKey),func
keycodes={right:0x27,left:0x25,up:0x26,down:0x28,esc:0x1B,enter:0x0D,home:0x24};
keykeys={ctrl:8,shift:4,alt:2,meta:1};
function setkey(k,v,f,o){ // повесть функцию на нажатие клавиши
        if(keycodes[k]) k=keycodes[k]; else k=k.toUpperCase().charCodeAt();
        var e=0; for(var i in keykeys) if(v.indexOf(i)>=0) e+=keykeys[i];
for(var i in hotkey) if(hotkey[i][0]==k && hotkey[i][1]==e){ if(f=='') delete hotkey[i]; else
hotkey[i]=[k,e,f,o]; //hotkey[i][2]=f; - А ТАК НЕ РАБОТАЕТ СУКА ЙОБАНЫЙ ГЛЮЧНЫЙ JS!!!
return;}
        if(!e) hotkey.unshift([k,e,f,o]); else hotkey.push([k,e,f,o]);
}
function rel_redirect(id){ var e=idd(id); if(e && e.href && !isHelps()) document.location.href=e.href; }
function hotkey_reset() {
hotkey=[];
setkey('right','ctrl',function(e){rel_redirect('NextLink')},true);
setkey('left','ctrl',function(e){rel_redirect('PrevLink')},true);
setkey('up','ctrl',function(e){rel_redirect('UpLink')},true);
setkey('down','ctrl',function(e){rel_redirect('DownLink')},true);
setkey('home','ctrl',function(e){document.location.href='/'},true);
setkey('esc','',function(e){clean(isHelps())},true); // закрыть последнее окно
setkey('enter','ctrl',function(e){if(!isHelps()) helper_go()},true); // если не открыто окон - окно правки
} hotkey_reset();

// 1 - Браузеры. 2 - IE. 3 - Неизвестно.
function browser(){ return (document.createRange) ? 1 : (-[1,]) ? 3 : 2; }

function idd(id) { return document.getElementById(id); }
function zabil(id,text) { if(idd(id)) idd(id).innerHTML=text; }
//function zabilc(cla,s) { var p=getElementsByClass(cla); for(var i in p) p[i].innerHTML=s; }

function doclass(cla,f,s) { var p=getElementsByClass(cla); for(var i in p) if(p[i].className==cla) f(p[i],s); }
function zabilc(cla,s) { doclass(cla,function(e,s){e.innerHTML=s;},s); }

function vzyal(id) { return idd(id)?idd(id).innerHTML:''; }
function zakryl(id) { idd(id).style.display='none'; }
function otkryl(id) { idd(id).style.display='block'; }

function cphash(a) { var b={}; for(var k in a) b[k]=a[k]; return b; }
function cpmas(a) { var b=[]; for(var i=0;i<a.length;i++) b[i]=a[i]; return b; }

function isHelps(){ var c=0; for(var k in mHelps) c++; return c?k:false; }

function print_r(a) { var s=''; for(var k in a) { var v=a[k]; s='\n'+k+'='+v+s; } return s; }
function in_array(s,a){ var l; for(l in a) if(a[l]==s) return l; return false; }

function clean(id) {
if(mHelps[id]!=undefined){ 
//alert('now2 id='+id+':'+print_r(mHelps[id]));
hotkey=cpmas(mHelps[id]); delete(mHelps[id]); }
if(isHelps()==false) hotkey_reset();
if(idd(id)) { zakryl(id); setTimeout("var s=idd('"+id+"'); if(s) s.parentNode.removeChild(s);", 40); }
}

var JSload={};

function inject(src){ src=www_ajax+src; var s=document.createElement('script'); s.setAttribute('type','text/javascript');
s.setAttribute('src',src); var head=document.getElementsByTagName('head').item(0); head.insertBefore(s,head.firstChild);}

function loadScript(src){ src=www_js+src;
	if(JSload[src]=='load') return;
        var s = document.createElement('script');
        s.setAttribute('type', 'text/javascript');
        s.setAttribute('charset', wwwcharset);
        s.setAttribute('src', src);
        // IE crashes on using appendChild before the head tag has been closed.
        var head = document.getElementsByTagName('head').item(0);
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


if(document.getElementsByClassName) getElementsByClass=function(classList,node){return (node||document).getElementsByClassName(classList)};
else getElementsByClass=function(classList,node){ var list=(node||document).getElementsByTagName('*'), length=list.length, classArray=classList.split(/\s+/), classes=classArray.length,result=[],i,j;
for(i=0;i<length;i++) for(j=0;j<classes;j++){ if(list[i].className.search('\\b'+classArray[j]+'\\b')!=-1){ result.push(list[i]); break; } } return result; };


function mkdiv(id,s,cls,paren){ if(idd(id)) { idd(id).innerHTML=s; idd(id).className=cls; return; }
        var div=document.createElement('DIV'); div.className=cls; div.id=id; div.innerHTML=s; div.style.display='none';
        if(paren==undefined) paren=document.body; paren.insertBefore(div, paren.lastChild);
}

function mkdiv2(id,s,cls,parent,relative) { if(idd(id)) { idd(id).innerHTML=s; idd(id).className=cls; return; }
	var div=document.createElement('DIV'); div.className=cls; div.id=id; div.innerHTML=s; div.style.display='none';
	r=relative.nextSibling; if(r) parent.insertBefore(div,r); else parent.appendChild(div);
}


function posdiv(id,x,y) { // позиционирование с проверкой на вылет, если аргумент '-1' - по центру экрана
	otkryl(id);
        var e=idd(id);
        var W=getWinW(); var H=getWinH();
        var w=e.clientWidth; var h=e.clientHeight;
	if(x==-1) x=(W-w)/2+getScrollW();
	if(y==-1) y=(H-h)/2+getScrollH();
	var DH=W-10; if(w<DH && x+w>DH) x=DH-w; if(x<0) x=0; 
	DH=getDocH()-10; if(h<DH && y+h>DH) y=DH-h; if(y<0) y=0;
        e.style.top=y+'px'; e.style.left=x+'px';
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

function helps(id,s,pos) { 
s=s+"<div onclick=\"clean('"+id+"')\" class='can' title='cancel'></div>";

s=s+"<div style='position:absolute;bottom:0px;right:8px;cursor:pointer;'><a href='?module="+encodeURIComponent(majax_lastu.replace('.php',''));
for(var k in majax_lasta) s=s+'&'+encodeURIComponent(k)+'='+encodeURIComponent(majax_lasta[k]);
s=s+"'><img src='"+www_design+"e3/reload_page.png' border=0></a></div>";

// img src='"+www_design+"e3/reload_page.png'

if(!idd(id)) {
	mkdiv(id,"<div class='corners'><div class='inner'><div class='content' id='"+id+"_body' align=left>"+s+"</div></div></div>",'popup');

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
} else zabil(id+'_body',s);
mHelps[id]=cpmas(hotkey);
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

function ajax(name,value,func) { ajaxon();
JsHttpRequest.query(www_ajax+name,value,function(responseJS,responseText){if(responseJS.status){ajaxoff();eval(func);}},true);
}

var majax_lastu,majax_lasta;
function majax(url,a) { majax_lasta=cphash(a); majax_lastu=url; a['up']=up; ajax(url,a,'if(responseJS.modo){try{eval(responseJS.modo)}catch(e){ if(!(e instanceof SyntaxError)) throw e; alert(e.name+"\\n\\n"+responseJS.modo)}}');}

function setOpacity(e, n) { var o=getOpacityProperty(); if(!e || !o) return;
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

if(typeof page_onstart == 'undefined') var page_onstart=[]; page_onstart.push('unic_rest(0)');

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
var comnum=0;
function kus(unic) { if(unic) majax('login.php',{action:'getinfo',unic:unic}); }// личная карточка
function kd(e) { if(confirm('Точно удалить?')) majax('comment.php',{a:'del',id:ecom(e).id}); } // del
function ked(e) { majax('comment.php',{a:'edit',id:ecom(e).id}); } // edit
function ksc(e) { majax('comment.php',{a:'scr',id:ecom(e).id}); } // screen/unscreen
function ko(e) { majax('comment.php',{a:'ans',id:ecom(e).id}); } // ans-0-1-undef
function rul(e) { majax('comment.php',{a:'rul',id:ecom(e).id}); } // rul-не rul
function ka(e) { e=ecom(e); majax('comment.php',{a:'comform',id:e.id,lev:e.style.marginLeft,comnu:comnum}); } // answer
function kpl(e) { majax('comment.php',{a:'plus',id:ecom(e).id}); } // +
function kmi(e) { majax('comment.php',{a:'minus',id:ecom(e).id}); } // -
function opc(e) { e=ecom(e); majax('comment.php',{a:'pokazat',oid:e.id,lev:e.style.marginLeft,comnu:comnum}); } // показать
function ecom(e){while((e.id==''||e.id==undefined)&&e.parentNode!=undefined) e=e.parentNode; return e.id==undefined?0:e;}


// bigfoto - заебался отдельно пристыковывать

var bigfoto_onload=1;

function bigfoto_pos(){ ajaxoff(); e=idd('bigfotoimg'); posdiv('bigfoto',-1,-1);
var H=(getWinH()-20); if(e.height>H && H>480) { e.height=H; posdiv('bigfoto',-1,-1); posdiv('bigfoto',-1,-1);}
var W=(getWinW()-50); if(e.width>W && W>640) { e.width=W; posdiv('bigfoto',-1,-1); posdiv('bigfoto',-1,-1);}
}

function bigfoto(e){ ajaxon(); bigfoto_onload=1; var s=(e.href == undefined ? e : e.href);
setTimeout("if(bigfoto_onload) bigfoto_pos();", 2000);
helps('bigfoto',"<img id='bigfotoimg' onclick=\"clean('bigfoto')\" onload=\"bigfoto_onload=0;bigfoto_pos()\" src='"+s+"'>",1);
return false;
}
