<?php // INSTALL-update

//--------------------------------------------------------------------------------
// ФУНКЦИИ УПДЕЙТОВ

function UPDATE_testkey($key){ // безопасность: проверка ключа инсталляции
	$k=file_get_contents($GLOBALS['filehost'].'binoniq/instlog/install_key.php');
	$k=preg_replace("/^.+?\"([0-9a-z]{40})\".+?$/si","$1",$k);
	return $k!=$key?0:1;
}

function UPDATE_select($rrr) { $r=unserialize($rrr); // return "<pre>".print_r($r,1);
	$s="<table><tr><td><input type='button' onclick='i_submit()' value='INSTALL'>";
	$otstup=''; $lastdir='';


	// 1. рассортировать данные
	$Uconf=array(); // тут будут конфиговые переменные
	$Ulang=array(); // тут будут языковые переменные
	$Ufile=array(); // тут будут файлы
	foreach($r as $n=>$l) { list($file,$val)=explode(' ',$l,2); unset($r[$n]);
		if(strstr($file,':')) { // конфиг или язык
			list($tt,$ff)=explode(':',$file,2);
			if($tt=='config') { $Uconf[$ff]=$val; continue; }
			if($tt=='lang') { $Ulang[$ff]=$val; continue; }
		}
		$Ufile[$file]=$val;
	}

/*
	// 1. Что с конфигом?
	// config:msq_login $msq_login = ""; // "lleo";
	$con=file_get_contents('config.php'); preg_match_all("/\n\s*".'\$'."([0-9a-z\_\-]+)\s*\=\s*([^\n]+)/si",$con,$m);
	$con=array(); foreach($m[1] as $i=>$n) $con[$n]=$m[2][$i];
	$s.="</td></tr></table><table><tr valign=top><td><b>config.php:</b></td><td>";
	foreach($Uconf as $n=>$v) { if(isset($con[$n])) unset($con[$n]); else $s.="<div>".'A'.'$'.$n."</div>"; }
	foreach($con as $n=>$l) { $s.="<div>".'D'.'$'.$n."=".h($l)."</div>"; } // предлагается удалить
	unset($con);
*/
	// 2. Что с языком?
	// lang:fido/ru:Comments:empty_comm Comments:empty_comm	А где же комментарий?
	$lan=array();
	foreach($Ulang as $n=>$v) { list($ll,$per)=explode(':',$n,2);

		if(!isset($lanz[$ll])) { // закачать сразу язык шоб не париться
		$nf=$GLOBALS['filehost'].'binoniq/lang/'.$ll.".lang";
		if(is_file($nf)&&($li=file($nf))!='') { $lan[$ll]=array();
		  foreach($li as $c) { list($cn,$cv)=explode("\t",$c,2);
			if(($cn=trim($cn))=='') continue; $lan[$ll][$cn]=trim($cv);
			}
		}}

		return "<pre>#########".print_r($lan[$ll],1);

	}
/*

if(isset($con[$n])) unset($con[$n]); else $s.="<div>".'A'.'$'.$n."</div>"; }

	$con=file_get_contents('config.php'); preg_match_all("/\n\s*".'\$'."([0-9a-z\_\-]+)\s*\=\s*([^\n]+)/si",$con,$m);
	foreach($m[1] as $i=>$n) $con[$n]=$m[2][$i];
	$s.="</td></tr></table><table><tr valign=top><td><b>config.php:</b></td><td>";
	foreach($Uconf as $n=>$v) { if(isset($con[$n])) unset($con[$n]); else $s.="<div>".'A'.'$'.$n."</div>"; }
	foreach($con as $n=>$l) { $s.="<div>".'D'.'$'.$n."=".h($l)."</div>"; } // предлагается удалить
	unset($con);

*/









/*
	// 3. Что с файлами?
	foreach($Ufile as $f=>$d) {
		$fhost=$GLOBALS['filehost'].$f; // физический файл
		$fname=basename($f); // его имя
		$fdir=dirname($f).'/'; // имя папки

		if($fdir!=$lastdir){
			$s.="</td></tr></table><table><tr valign=top><td><b>$fdir</b></td><td>";
			$lastdir=$fdir;
			// <div class=\"$dirname ii1\" onclick='i_d(this)'>".$dirname."</div>";
		}

		if(is_file($fhost)) {
				$o='D';
		} else $o='U';
		$s.="<div>".$o.$fname."</div>";
	}
*/




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

i_submit=function(e){ var ee,v,td1,td2,dir,p,e,c,tr=idd('i_selectfiles').getElementsByTagName('TR'), s='';
	for(var i=0;i<tr.length;i++){ p=tr[i]; td1=p.firstChild; td2=p.lastChild; if(td2==td1) continue;
	dir=td1.firstChild.innerHTML;
	ee=td2.getElementsByTagName('DIV'); for(var j=0;j<ee.length;j++){ e=ee[j];
			var v=e.innerHTML.replace(/^<br>/g,'').replace(/\&nbsp;/g,' ').replace(/^ +(.+?) +$/g,'$1');
			if(dir=='config.php:') v=v.replace(/^([^\=]+)\s*\=.*?$/g,'$1');
			s=s+'<br>'+dir+v+' | ';
			if(e.style.textDecoration=='none') { s=s+e.style.color; }
			else s=s+'------';
		}
	}
	ohelpc('asd','sda',s);
};

i_sett=function(e,t){ e.style.cursor='pointer'; e.style.textDecoration='none'; e.setAttribute('tiptitle',t);
	addEvent(e,'mouseover',function(){ idd('tip').innerHTML=this.getAttribute('tiptitle');
	posdiv('tip',mouse_x+10,mouse_y+10); });
	addEvent(e,'mouseout',function(){ zakryl('tip') } );
	addEvent(e,'mousemove',function(){ posdiv('tip',mouse_x+10,mouse_y+10) } );
}

go_install=function(){ var t,c,tr=idd('i_selectfiles').getElementsByTagName('TR');
	for(var i=0;i<tr.length;i++){ var p=tr[i]; var td1=p.firstChild; var td2=p.lastChild; if(td2==td1) continue;
		var dir=td1.firstChild; dir.onclick=function(){i_chand(this)}; i_sett(dir,'Select/Unselest all files');
		var ee=td2.getElementsByTagName('DIV'); for(var j=0;j<ee.length;j++){ var x=ee[j];
			var l=x.innerHTML; var O=l.substring(0,1); l=l.substring(1,l.length);
			x.setAttribute('myl',l); x.setAttribute('myO',O);
			x.innerHTML='<br>'+l; x.onclick=function(){i_chan(this)}; x.style.display='inline';
				if(O=='U') { c='green'; t='update'; }
				else if(O=='D') { c='red'; t='del'; }
				else { c='magenta'; t='unk'; }
			x.style.color=c; i_sett(x,t);
}}};

setTimeout(go_install,500);

i_chand=function(e){var p=e.parentNode.nextSibling.getElementsByTagName('DIV');for(var i=0;i<p.length;i++) i_change(p[i]) };

i_chan=function(e){ 
	if(e.style.textDecoration=='none'){ e.style.textDecoration='line-through';
		e.innerHTML=e.innerHTML.replace(/^<br>(.+?)$/g,'<br>&nbsp;&nbsp;$1&nbsp;&nbsp;');
	}else{ e.style.textDecoration='none';
		e.innerHTML=e.innerHTML.replace(/\&nbsp;/g,' ').replace(/^<br> +(.+?) +$/g,'<br>$1');
	}
};
";
	return "<div id='i_selectfiles'>$s</td></tr></table></div>";
}

?>