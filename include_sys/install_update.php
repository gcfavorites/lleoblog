<?php // INSTALL-update

//--------------------------------------------------------------------------------
// ФУНКЦИИ УПДЕЙТОВ

function UPDATE_testkey($key){ // безопасность: проверка ключа инсталляции
	$k=file_get_contents($GLOBALS['filehost'].'binoniq/instlog/install_key.php');
	$k=preg_replace("/^.+?\"([0-9a-z]{40})\".+?$/si","$1",$k);
	return $k!=$key?0:1;
}

function UPDATE_select($rrr) { $r=unserialize($rrr);
	$s="<table><tr><td>files:";
	$otstup=''; $lastdir='';


	// 1. рассортировать данные
	$Uconf=array(); // тут будут конфиговые переменные
	$Ulang=array(); // тут будут языковые переменные
	$Ufile=array(); // тут будут файлы
	foreach($r as $n=>$l) { list($file,$val)=explode(' ',$l,2); unset($r[$n]);
		if(strstr($file,':')) { // конфиг или язык
			list($tt,$ff)=explode(':',$file,2);
			if($tt='config') { $Uconf[$ff]=$val; continue; }
			if($tt='lang') { $Ulang[$ff]=$val; continue; }
		}
		$Ufile[$file]=$val;
	}

	// 1. Что с файлами?
	foreach($Ufile as $f=>$d) {
		$fhost=$GLOBALS['filehost'].$f; // физический файл
		$fname=basename($f); // его имя
		$fdir=dirname($f).'/'; // имя папки

		if($fdir!=$lastdir){
			$s.="</td></tr></table><table><tr valign=top><td><b>$fdir</b></td><td>";
			$lastdir=$fdir;
			// <div class=\"$dirname ii1\" onclick='i_d(this)'>".$dirname."</div>";
		}
		if(is_file($fhost)) $o='A';
		else $o='U';
		$s.="<div>".$o.$fname."</div>";
	}


/*
	
	$f=explode(':',$file,3);
		if(sizeof($f)==1) { // это файл
			$ff=$GLOBALS['filehost'].$file;
			$filename=basename($file);
                        $dirname=dirname($file).'/';
			$otstup=str_repeat(' ',substr_count($file,'/')*10);

			if(!is_file($ff)) $ac='iia'; // add
			else {
				list($ftime,$fkey)=explode(' ',$val,2);
//				if($ftime==filemtime($ff)) continue; // все с файлом нормалек
				//else 
				$ac='iiu'; // upgrade
			}

			if($dirname!=$lastdir) { $s.="<div class=\"$dirname ii1\" onclick='i_d(this)'>".$dirname."</div>"; $lastdir=$dirname; }

			$s.="<div>".str_repeat(' ',strlen($dirname))
			."<span id='e_$file' class=\"$dirname $ac\" onclick='i_f(this)'>".$filename."</span></div>";

		} elseif($f[0]=='config') { // это конфиг
		} elseif($f[0]=='lang') { // это язык
		}

	}

*/

// nextSibling
// previousSibling
// firstChild
// lastChild
/*

className.split(' ');
        if(typeof dc[1] != 'undefined' && dc[1].substring(0,2)=='ii'){ if(k==0) k=dc[1]=='ii1'?'ii0':'ii1'; i_d(p[i],k); }
        }

alert(p.length);

for(var k=0,i=0;i<p.length;i++){ var dc=p[i].className.split(' ');
        if(typeof dc[1] != 'undefined' && dc[1].substring(0,2)=='ii'){ if(k==0) k=dc[1]=='ii1'?'ii0':'ii1'; i_d(p[i],k); }
        }


		td1.style.backgroundColor='#ccc';


*/

$GLOBALS['selectjs']="

i_seldir=function(e){ alert(e.tagName); };

i_sett=function(e,t){
	e.style.cursor='pointer';
	e.style.textDecoration='none';

	e.setAttribute('tiptitle',t);
	addEvent(e,'mouseover',function(){ idd('tip').innerHTML=this.getAttribute('tiptitle'); posdiv('tip',mouse_x+10,mouse_y+10); });
	addEvent(e,'mouseout',function(){ zakryl('tip') } );
	addEvent(e,'mousemove',function(){ posdiv('tip',mouse_x+10,mouse_y+10) } );
}

go_install=function(){ var t,tr=idd('i_selectfiles').getElementsByTagName('TR');

	for(var i=0;i<tr.length;i++){ var p=tr[i];
		var td1=p.firstChild; var td2=p.lastChild; if(td2==td1) continue;

			var dir=td1.firstChild;
				dir.onclick=function(){i_change_dir(this)};
				i_sett(dir,'Select/Unselest all files');

			var ee=td2.getElementsByTagName('DIV');
			for(var j=0;j<ee.length;j++){ var pp=ee[j];
				var l=pp.innerHTML; var O=l.substring(0,1); l=l.substring(1,l.length);

				pp.setAttribute('myl',l);
				pp.setAttribute('myO',O);

				pp.innerHTML='<br>'+l;
				pp.onclick=function(){i_change(this)};
				pp.style.display='inline';

				if(O=='U') { pp.style.color='green'; t='update'; }
				else if(O=='A') { pp.style.color='red'; t='del'; }
				else { pp.style.color='magenta'; t='unk'; }

				i_sett(pp,t);

				}
	}
	return;
};

setTimeout(go_install,500);

i_getopt=function(e){ var c=e.style.color; return e.getAttribute('tiptitle'); 
alert(e.innerHTML+' '+i_getopt(e)+' '+e.style.textDecorationLineThrough);
};


i_change_dir=function(e){ var p=e.parentNode.nextSibling.getElementsByTagName('DIV');
	for(var i=0;i<p.length;i++){i_change(p[i])}
};

i_change=function(e){ 
	if(e.style.textDecoration=='none') {
		e.style.textDecoration='line-through';
		e.innerHTML=e.innerHTML.replace(/^<br>(.+?)$/g,'<br>&nbsp;&nbsp;$1&nbsp;&nbsp;');
	} else {
		e.style.textDecoration='none';
		e.innerHTML=e.innerHTML.replace(/\&nbsp;/g,' ').replace(/^<br> +(.+?) +$/g,'<br>$1');
	}
};


i_all=function(){
var p=idd('packs').getElementsByTagName('div');
for(var k=0,i=0;i<p.length;i++){ var dc=p[i].className.split(' ');
	if(typeof dc[1] != 'undefined' && dc[1].substring(0,2)=='ii'){ if(k==0) k=dc[1]=='ii1'?'ii0':'ii1'; i_d(p[i],k); }
	}
};

i_d=function(e,k){ var dc=e.className.split(' ');
	if(typeof k == 'undefined') dc[1]=dc[1]=='ii1'?'ii0':'ii1'; else dc[1]=k;
	e.className=dc.join(' ');
	var p=getElementsByClass(dc[0]);
	for(var i=1;i<p.length;i++) { var c=p[i].className.split(' ');
		if(dc[1]=='ii0') c[2]='ii0';
		else { if(c.length>2) { delete c[2]; c.length=2; } }
		p[i].className=c.join(' ');
	}
};

i_f=function(e){ var c=e.className.split(' ');
	if(c.length>2) { delete c[2]; c.length=2; } else c[2]='ii0';
	e.className=c.join(' ');
};
";
	return "<P onclick='this.style.textDecorationLineThrough=true;'>Click this if you</p><input type='button' onclick='go_install()' value='INSTALL'><div id='i_selectfiles'>$s</div>";
}

?>