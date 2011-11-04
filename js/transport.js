var hashtime=0,hashtime_step=51,hashtime_max=1000;

if(typeof message_func == 'undefined') var message_func={
'#':function(r){if(oldhash==window.location.hash) setHash(' ')},
'%23':function(r){if(oldhash==window.location.hash) setHash(' ')},
'default':function(r){/*dier(r,'UNKNOWN')*/},
'dier':function(r){dier(r)},
'idie':function(r){idie(r)},
'RESIZE':function(r){resize_id(r.id,r.w,r.h)},
'WIN':function(r){ohelpc(h(r.id),h(r.head),h(r.text));setHash(' ');return 1;}
};

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
// salert('ping',200);
	if(hashtime<hashtime_max) hashtime+=hashtime_step; setTimeout(hashDaemon,hashtime);
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
//	if( window.top !== window.self && (typeof r.MYID == 'undefined' || r.ADR != IMBLOAD_MYID)) return; // не мое дело
// salert('ping:'+oldhash,200000);

	var k=0; for(var i in r) { if(r[i]=='function'&& message_func[i]) { k++; if(1===message_func[i](r)) return; } }
	if(!k) message_func['default'](r);
	return;
}

//------- протоколы передачи -------
function sendm(s){ /*if(typeof w == 'undefined')*/ var w=window.top;
  if(0 && typeof postMessage == 'function') { alert(1); return w.postMessage(s,'*'); } // транспорт postMessage в браузере есть
  if(''==w.location.hash.replace(/([\s\#]|\%20|\%34)+/g,'')) w.location.hash=s; else setTimeout("sendm(\""+s+"\")",500);
}

function resize_me(){ 

// window.top.location.hash='RESIZE;id='+IMBLOAD_MYID+';w='+getDocW()+';h='+getDocH()+';#';
// window.top.location.hash="RESIZE;w="+getDocW()+";h="+getDocH()+";id="+IMBLOAD_MYID+";#";
sendm("RESIZE;w="+getDocW()+";h="+getDocH()+";id="+IMBLOAD_MYID+";#"); 

}



//setTimeout("sendm('WIN;head=1;text=2;id=monti')",2000);