
// var BinoniqS = {

//if(window.top === window) alert('=TOP: '+mypage);
// if(window.self === window) alert('=SELF: '+mypage);


var hashtime=0,hashtime_step=51,hashtime_max=1000;

if(typeof message_func == 'undefined') var message_func={
'#':function(r){if(oldhash==window.location.hash) setHash(' ')},
'%23':function(r){if(oldhash==window.location.hash) setHash(' ')},
'default':function(r){/*dier(r,'UNKNOWN')*/},
'dier':function(r){dier(r)},
'idie':function(r){idie(r)},
'RESIZE':function(r){resize_id(r.id,r.w,r.h)},
'WIN':function(r){ohelpc(h(r.id),h(r.head),h(r.text));setHash(' ');return 1;}
}; // token:LOGINZA.getToken,

// if(erty1==undefined) alert("erty1 is undefined"); else alert("erty1 present!");

function resize_id(id,w,h){
	if(typeof id == 'undefined') return;
	var e=document.getElementById(id); if(typeof e == 'undefined') return;
	if(typeof w !='undefined') e.style.width=(1*w+15)+'px';
	if(typeof h !='undefined') e.style.height=(1*h+15)+'px';
}

function setHash(h){ oldhash=h; window.location.hash=h; } //если нужно изменить хеш

function messageDaemon(e){ doMessage(e.data); } // слушалка message (e.origin - сайты)

if(typeof postMessage == 'function') { // транспорт postMessage в браузере есть
	if(window.addEventListener) window.addEventListener('message',messageDaemon,false);
	else window.attachEvent('onmessage',messageDaemon);
}

var oldhash=window.location.hash; // слушалка hash
function hashDaemon(){ if(window.location.hash!=oldhash){ oldhash=window.location.hash;
	if(doMessage(oldhash.substring(1))!=7) hashtime=0; // если что-то произошло, ускориться
	}
//	salert('ping: '+hashtime,200);
	if(hashtime<hashtime_max) hashtime+=hashtime_step;
//	if(hashtime>hashtime_max) { hashtime=hashtime_max; alert(1); }
	setTimeout(hashDaemon,hashtime);
} setTimeout(hashDaemon,hashtime);


function doMessage(s) { if(s==''||s==' ') return 7;
        var m=s.split(';'); if(m.length<2) m=s.split('|'); // как кому нравицо

		// старая система команд
		if(window.top === window.self) { // если я главное окно
			if(m[0]=='WW') idd(m[1]).style.width=(1*m[2]+15)+'px';
			if(m[0]=='HH') idd(m[1]).style.height=(1*m[2]+15)+'px';
			if(m[0]=='NO') clean(m[1]);
		}

	var r={}; for(var i in m) {
		var c='=',k=m[i].split(c); if(k.length<2){ c=':'; k=m[i].split(c); } // как кому нравицо
		if(k.length<2) r[k.shift()]='function'; else r[k.shift()]=k.join(c); //.replace(/#%tZ#/g,';').replace(/#%rZ#/g,'|'));
	}


	if(window.top !== window.self && (typeof r.MYID == 'undefined' || r.addr != IMBLOAD_MYID)) return; // не мое дело

//	return dier(r,'мое дело!<br>'+mypage);
//	alert(s+'<br>'+mypage); return;

	var k=0; for(var i in r) { if(r[i]=='function'&& message_func[i]) { k++; if(1===message_func[i](r)) return; } }
	if(!k) message_func['default'](r);
	return;
}

// alert(message_action);

//functs['ajax'] = function(param) { param.shift(); return;
//      var mod = param[0]; param.shift();
//      if(param.length) eval("majax('"+mod+".php',{"+implode(',',param)+"})");
//};
/*
functs['area'] = function(param) {
        var ara={mod:'fido'};
        for(var i in param) {
                var m=param[i].split(':'); if(m.length!=2) return; // правильность параметров
                if( m[0]!='area' && m[0]!='id' && m[0]!='msgid') return; // допустимые имена
                if( m[1]!=m[1].replace(/[^0-9a-z\.\$]/gi,'') ) return; // допустимые символы значений
                ara[m[0]]=m[1];
        }

        ara['a']='charea';
        if(ara['id']||ara['msgid']) { ara['nomsg']=1; if(getsearch!='') ara['search']=getsearch; }

        if(typeof are != 'undefined') majax('module.php',ara);
        // блять а вот теперь ебаться не переебаться...
        var s=[]; for(var i in ara) s.push(i+':\''+ara[i]+'\'');
        majax('module.php',{mod:'fido',a:'loadareas',allif:ara['area']},"majax('module.php',{"+implode(',',s)+",all:areasmode
};

*/



// if(window.top === window.self) { // Йа главное окно

/*
LOGINZA.hashParser = function () {
	var func, param;
	try {
		var hash = LOGINZA.widget().location.hash.substr(1);
		var commands = hash.split(';');
		// ОБВПТ СЛПТШ, ЖХОЛГЙС ДМС ПВТБВПФЛЙ ОБЦБФЙК РП УУЩМЛБН
		var callbacks = [
		    ['token:', 'getToken']
		];
		// ЕУМЙ ИЕЫ ОПЧЩК
		if (hash != LOGINZA.hash) {
			for (var k=0; k<commands.length; k++) {
				// ЧЩЪПЧ ОХЦОПЗП callback Ч ЪБЧЙУЙНПУФЙ ПФ РЕТЕДБООПЗП СЛПТС
				for (var i=0; i<callbacks.length; i++) {
					func = callbacks[i][1];
					param = commands[k].substr(callbacks[i][0].length);
					
					if (commands[k].indexOf(callbacks[i][0])===0) {
						LOGINZA[func](param);
					}
				}
			}
			LOGINZA.hash = hash;
		}
	} catch (e) {}
}
*/


//------- протоколы передачи -------

// postMessage ? это новая возможность стандарта HTML5, позволяет отсылать сообщения из одного окна в другое,
// при этом контент окон может быть с разных доменов. Примерная реализация
// targetWindow.postMessage(message,targetOrigin);
// targetWindow - окно куда шлём запроc
// message - сообщение
// targetOrigin - допускается указания '*', при этом домен может быть любой.

// window.top.postMessage('NO|'+IMBLOAD_MYID,'http://'+IMBLOAD_TOP);
// if(window.top !== window.self) { var r=window.location.hash.split('|');
// var IMBLOAD_ACT=r[0]; var IMBLOAD_TOP=r[1]; var IMBLOAD_MYID=r[2];
// if(IMBLOAD_ACT=='#IMBLOAD')
//	window.top.postMessage('HH|'+IMBLOAD_MYID+'|'+getDocH(),'http://'+IMBLOAD_TOP);
//	setTimeout("window.top.postMessage('HH|'+IMBLOAD_MYID+'|'+getDocH(),'http://'+IMBLOAD_TOP)",10000);
// page_onstart.push('raport_imbload()');

function sendm(s,w){ if(typeof w == 'undefined') w=window.top;
  if(0 && typeof postMessage == 'function') return w.postMessage(s,'*'); // транспорт postMessage в браузере есть
  if(w.location.hash.replace(/([\s\#]|\%20|\%34)+/g,'')=='') w.location.hash=s; else setTimeout("sendm(\""+s+"\")",500);
}

function resize_me(){ sendm("RESIZE;w="+getDocW()+";h="+getDocH()+";id="+IMBLOAD_MYID+";#"); }

//setTimeout("sendm('WIN;head=1;text=2;id=monti')",2000);