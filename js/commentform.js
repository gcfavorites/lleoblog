loadCSS('commentform.css');

function cm_mail_validate(p) { var l=p.value; return l; }

function cmsend_edit(t,comnu,id) { majax('comment.php',{a:'editsend',text:t['txt'].value,comnu:comnu,id:id}); return false; }

function cmsend(t,comnu,id,dat,lev) { 

	var ara = new Array();
	ara['a']='comsend';
	if(t['mail']) ara['mail']=t['mail'].value;
	if(t['nam']) ara['name']=t['nam'].value;
	if(t['txt']) ara['text']=t['txt'].value;
	if(t['capcha']) ara['capcha']=t['capcha'].value;
	if(t['capcha_hash']) ara['capcha_hash']=t['capcha_hash'].value;
	ara['comnu']=comnu;
	ara['id']=id;
	ara['dat']=dat;
	ara['lev']=lev;

	majax('comment.php',ara); 
	return false;
}


/*

{action:'comsend',text:txt,name:nam,comnu:comnu,dat:dat,id:id,lev:lev}

var logintext='';
var lastpolename;
var lastpolevalue;

function login_validate(p,n) { var l=p.value;

	var e=l.replace(/http:/gi,''); if(e!=l) {
		zabil('openidotvet','<div class=e>без http://, пожалуйста</div>'); polese(p); return e;
	}

	var e=l.replace(/[^0-9a-z\-\_\.\/\~\=\@]/gi,''); if(e!=l) {
		zabil('openidotvet','<div class=e>—имволы и русские буквы нельз€!</div>'); polese(p); return e;
        }

	if(n) {
		var e=l.replace(/[^0-9a-z\-\_]/gi,''); if(e!=l) {
			zabil('openidotvet','<div class=o>логинимс€ по openid</div>'); zakryl('openidpass'); polese(p); return l;
	        }

        	otkryl('openidpass'); logintext=l;
	}

	polese(p); return l;
}


function polese(p) { lastpolename=p.name; lastpolevalue=p.value; }

function mail_validate(p) { polese(p); var l=p.value; return l; }
function site_validate(p) { polese(p); var l=p.value; return l; }
function realname_validate(p) { polese(p); var l=p.value; return l; }

function setbirth(y,m,d) { var e=document.getElementById('birth'); e.value=y.value+'-'+m.value+'-'+d.value; polesend(e); }

function login_go(log,pas) {
        zabil('openidotvet','<div class=o>идет соединение</div>');
	majax('login.php',{ 'action': 'openid_logpas', 'rpage': mypage, 'log': log, 'pas': pas });
	return false;
}

function openid_go(log) {
	if(log.replace(/\./g,'')==log) zabil('openidotvet','<div class=e>разве ж это openid?</div>');
	else { zabil('openidotvet','<div class=o>идет соединение</div>');
	majax('login.php',{ 'action': 'openid_logpas', 'rpage': mypage, 'log': log });
	}
	return false;
}

function polesend(p) { return polesend0(p.name,p.value); }

function polesend_all() { 
	if(lastpolename=='openid') return openid_go(lastpolevalue);
	return polesend0(lastpolename,lastpolevalue);
}

function polesend0(name,value) {
	zabil('openidotvet','<div class=o>'+name+': '+value+'</div>');
	majax('login.php',{'action': 'polesend', 'name': name, 'value': value});
	return false;
}
*/
// ============================  последн€€ строка скрипта должна быть всегда такой: ========================
var src='commentform.js'; ajaxoff(); var r=JSload[src]; JSload[src]='load'; if(r && r!='load') eval(r);