<?php // INSTALL

include_once $GLOBALS['include_sys']."_files.php"; // �������� � �������

$GLOBALS["installname"]='install'; // ��� ����� �����

ini_set("display_errors","0"); ini_set("display_startup_errors","0");

if(!empty($_SERVER['QUERY_STRING'])&&!strstr($_SERVER['QUERY_STRING'],'=')) {
	$file=urldecode($_SERVER['QUERY_STRING']); if(is_vetofile($file)) die("ERROR: Veto");
	$fhost=realpath($GLOBALS['filehost'].$file);
	if(!file_exists($fhost)) die("ERROR: File not found: ".h($file));
	Exit_SendFILE($file);
}

// if(!function_exists('h')) die("Error 404");

/*
���������� ��� ������:
	site_module/install
	- index.php
	- ajax/module.php
	include_sys/jrhttprequest?.php
	-? include_sys/_autorize.php
	- config.php
	- template/blank.html

������-���:
	js/main.js
	design/JsHttpRequest.js
	css/blog.css

// AD(); // ������ ��� ������

// $GLOBALS['admin']=1;

// idie('#'.$GLOBALS['admin']);

addstyle=function(c,s){ var i=0,a=document.styleSheets[0],u=a.cssRules;
	for(;i<u.length;i++){ if(u[i].selectorText==c) return; }
	if(typeof a.insertRule(c+' '+s,0)!='number') a.addRule(c+' '+s,0);
}
replaceblockstyle=function(c,s){ var i,a=document.styleSheets[0],u=a.cssRules;
	for(i=0;i<u.length;i++){ if(u[i].selectorText==c) break; }
	if(u.style.�olor) u.style.�olor='#555'; else a.rules[0].style.�olor='#555';
}

addstyle('.ulin',\"{text-decoration:line-through}\");
addstyle('.ulin:before',\"{content:'���'}\");
addstyle('.ulin:after',\"{content:'���'}\");
*/

//--------------------------------------------------------------------------------
// ������� ��������

$GLOBALS['selectjs']="
i_toggle_visible_d=1;
/*
i_ser
i_url
i_pack
*/

i_dir=function(e){ e=e.firstChild.innerHTML; return e=='/'?'':e };
i_div=function(e){ return e.lastChild.getElementsByTagName('DIV') };
i_tr=function(e){ return idd('i_selectfiles').getElementsByTagName('TR') };

i_fil=function(e){ return e.innerHTML.replace(/<[^>]*>/g,'') };

i_srav=function(e){ e=i_dir(e.parentNode.parentNode.parentNode)+i_fil(e.parentNode);
	mijax(i_ser+'/ajax/midule.php',{mod:'INSTALL',a:'install_far_cmp',url:i_url,file:e});
};

i_toggle_visible=function(){ for(var tr=i_tr(),i=0;i<tr.length;i++){ for(var p=i_div(tr[i]),z=g=p.length,j=0;j<g;j++){
			if(i_toggle_visible_d && !i_tst(p[j])) { p[j].style.display='none'; z--; }
			else p[j].style.display='block';
		} tr[i].style.display=(i_toggle_visible_d && !z)?'none':'block';
	}
i_toggle_visible_d=i_toggle_visible_d?0:1;
};

i_get_selected=function(){ for(var s='',tr=i_tr(),i=0;i<tr.length;i++){
	for(var dir=i_dir(tr[i]),p=i_div(tr[i]),j=0;j<p.length;j++){ if(i_tst(p[j])) s='\\n'+dir+p[j].innerHTML+s; }
 } return s;
};

i_submit=function(){ inst_MAS_DEL=[]; inst_MAS_UPD=[]; inst_MAS_NON=[];
  for(var c,f,s='',tr=i_tr(),i=0;i<tr.length;i++){ for(var dir=i_dir(tr[i]),p=i_div(tr[i]),j=0;j<p.length;j++){
			if(dir=='config.php:') {
				f=p[j].innerHTML.replace(/^\\\$([^\s\=]+)\s*=\s*(.*?)$/g,'$1=$2');
				var inp=p[j].getElementsByTagName('INPUT');
				if(inp.length==1) f=f.replace(/<input.*?>/gi,inp[0].value);
			} else f=i_fil(p[j]);

			/*if(dir=='config.php:') f=f.replace(/^\\\$/g,''); alert(f);*/
		f=dir+f;
		if(i_tst(p[j])) {
			c=p[j].className.split(' ')[0];
			if(c=='iUPD'||c=='iADD') inst_MAS_UPD.push(f);
			else if(c=='iDEL') inst_MAS_DEL.push(f);
			else ohelpc('errError option','Error option','Error option: `'+c+'` / '+f);
	  	} else inst_MAS_NON.push(f);
	}
 }
i_process();
};

i_selectall=function(){ for(var z=7,tr=i_tr(),i=0;i<tr.length;i++){
  for(var p=i_div(tr[i]),j=0;j<p.length;j++){ if(z==7) z=i_tst(p[j]); i_chan(p[j],z); }
} if(!z && !i_toggle_visible_d) i_toggle_visible();
};

i_find=function(id){
		if(id.indexOf('config.php:')>=0) id=id.replace(/^([^\s\=]+).*?$/g,'$1');
	for(var v,tr=i_tr(),i=0;i<tr.length;i++){ for(var dir=i_dir(tr[i]),p=i_div(tr[i]),j=0;j<p.length;j++){
		v=i_fil(p[j]);
			if(dir=='config.php:') v=v.replace(/^\\\$([^\s\=]+).*?$/g,'$1');
		if(id==dir+v) return p[j];
	}
} alert('not find: `'+id+'`');
};

go_install=function(id){ var x,d,itit={iDEL:'del',iADD:'add new',iUPD:'update'};
	for(var tr=i_tr(),i=0;i<tr.length;i++){ d=tr[i].firstChild;
		d.onclick=function(){i_chand(this)}; d.setAttribute('title','Invert selected');
		for(var p=i_div(tr[i]),j=0;j<p.length;j++){
			if(itit[p[j].className]) p[j].setAttribute('title',itit[p[j].className]);
			p[j].onclick=function(e){e=e.target.tagName;if(e!='INPUT'&&e!='IMG')i_chan(this,i_tst(this))};
		}
	}
i_toggle_visible(); posdiv(id,-1,-1);
};

i_chand=function(e){ for(var c=7,p=i_div(e.parentNode),i=0;i<p.length;i++) { if(c==7) c=i_tst(p[i]); i_chan(p[i],c); }};
i_tst=function(e){ var c=e.className.split(' '); if(c.length!=1) return (c[1]=='iOK'?true:false); return (c[0]=='iYES'?true:false); }
i_chan=function(e,i){ var c=e.className.split(' '); e.className=c.length!=1?c[0]+(i?' iSS':' iOK'):(i?'iNON':'iYES'); }

inst_MAS_UPD=[]; inst_MAS_DEL=[]; inst_MAS_NON=[];

i_process=function(){
	if(inst_MAS_NON.length) return majax('module.php',{mod:'INSTALL',a:'install_update_NON',d:inst_MAS_NON.join('\\n'),mode:'post',pack:i_pack});
	if(inst_MAS_DEL.length) return majax('module.php',{mod:'INSTALL',a:'install_update_DEL',file:inst_MAS_DEL[0],mode:'post'});
	if(inst_MAS_UPD.length) return majax('module.php',{mod:'INSTALL',a:'install_update_UPD',file:inst_MAS_UPD[0],mode:'post'});
	clean('install2');
}
";

function UPDATE_file($name,$temp) {
	$f=realpath($GLOBALS['filehost'].$name);
	if(is_vetofile($name)) return "Disabled file: ".h($name); // veto?
	backupfile($f); // ���������� ������ ����
	testdir(dirname($f)); // ������� �����, ���� ����
        move_uploaded_file($temp,$f); filechmod($f);

	if(getras($f)=='css' && !empty($GLOBALS['www_design'])) {
		$s=file_get_contents($f);
		//---------------------------- ���� �� ���� �������� -------------
		$s=preg_replace("/url\([\'\"]*[^\s\'\"\)]+\/design\/(.*?)[\'\"]*\)/si",'url('.$GLOBALS['www_design']."$1)",$s);
		$s=preg_replace("/\@charset\s[\'\"][^\s\'\"]+[\'\"]*/si",'@charset "'.$GLOBALS['wwwcharset'].'"',$s);
		$s=str_replace('{www_design}',$GLOBALS['www_design'],$s);
		//----------------------------------------------------------------
		fileput($f,$s);
	}

	return 1; //dirname($f)."|$f| name: $name data: ".strlen($data)." bytes";
}

function UPDATE_testkey($key){ // ������������: �������� ����� �����������
	$f=$GLOBALS['filehost'].'binoniq/instlog/install_key.php'; $k=file_get_contents($f); unlink($f);
	return ( preg_replace("/^.+?\"([0-9a-z]{40})\".+?$/si","$1",$k) != $key ? 0:1);
}

function UPDATE_select($rrr,$pack) { $r=unserialize($rrr); // return "<pre>".print_r($r,1);

	$s="<input type='button' onclick='i_submit(this)' value='INSTALL'>"
."&nbsp; &nbsp; <span class='ll r' onclick='i_toggle_visible();'>Hide/Show</span>"
."&nbsp; &nbsp; <span class='ll r' onclick='i_selectall()'>select</span>";
	$otstup=''; $lastdir='';

	// 1. �������������� ������
	$Uconf=array(); // ��� ����� ���������� ����������
	$Ulang=array(); // ��� ����� �������� ����������
	$Ufile=array(); // ��� ����� �����
	foreach($r as $n=>$l) { list($file,$val)=explode(' ',$l,2); unset($r[$n]);
		if(strstr($file,':')) { // ������ ��� ����
			list($tt,$ff)=explode(':',$file,2);
			if($tt=='config') { $Uconf[$ff]=$val; continue; }
			if($tt=='lang') { $Ulang[$ff]=$val; continue; }
		}
		$Ufile[$file]=$val;
	}

$obnovle=0;

//return "<pre>".print_r($Uconf,1)."</pre>";

// ����� ��� ������
$veto=unserialize(file_get_contents($GLOBALS['filehost']."binoniq/instlog/veto.my")); if(empty($veto)) $veto=array(); // �� ������ ������

//========================================================= // onclick=\"setTimeout('this.parentNode.click()',100)\" 
	function vtoinput($t){ return $t[1]."<input type='text' value=\"".$t[2]."\" size='".(strlen($t[2])?strlen($t[2]):1)."'>".$t[3]; }

	// 1. ��� � ��������?
	// config:msq_login $msq_login = ""; // "lleo";
	//$s.="<pre>".print_r($Uconf,1)."</pre>";

	$con=file_get_contents('config.php'); preg_match_all("/\n\s*".'\$'."([0-9a-z\_\-]+)\s*\=\s*([^\n]+)/si",$con,$m);
	$con=array(); foreach($m[1] as $i=>$n) $con[$n]=$m[2][$i]; // ��� ����
	$s.="<table><tr valign=top><td class='iDIR iOK'>config.php:</td><td class='iT'>"; // ���������
	foreach($Uconf as $n=>$v) { if(isset($con[$n])) { unset($con[$n]); continue; }
			$v=h($v);
			$v=preg_replace_callback("/^([\'\"])([^\'\"]*)([\'\"];)/s","vtoinput",$v);
			$v=preg_replace_callback("/^([\'\"]*)(\d+)([\'\"]*;)/s","vtoinput",$v);
			$s.="<div class='iADD ".(in_array('config.php:'.$n,$veto)?'iSS':'iOK')."'>$".$n." = $v</div>"; // ��������
	} foreach($con as $n=>$l) $s.="<div class='iDEL ".(in_array('config.php:'.$n,$veto)?'iSS':'iOK')."'>$".$n."=".h($l)."</div>"; // �������
	unset($con);
	$s.="</td></tr></table>";
//=========================================================
/*
	// 2. ��� � ������?
	// lang:fido/ru:Comments:empty_comm Comments:empty_comm	� ��� �� �����������?
	$lan=array(); $allan=array();
	foreach($Ulang as $n=>$v) { list($ll,$per)=explode(':',$n,2); $allan[$ll]='';

		if(!isset($lan[$ll])) { $lan[$ll]=array(); // �������� ����� ���� ��� �� ��������
		$nf=$GLOBALS['filehost'].'binoniq/lang/'.$ll.".lang";
		if(is_file($nf)&&($li=file($nf))!='') foreach($li as $c) {
			list($cn,$cv)=explode("\t",$c,2); if(($cn=trim($cn))=='') continue; $lan[$ll][$cn]=trim($cv);
		}}

		if(isset($lan[$ll][$per])) { unset($lan[$ll][$per]); continue; }
		$allan[$ll].="<div>".'A'.$per."</div>";
	}
	foreach($lan as $ll=>$arper) foreach($arper as $cn=>$cv) $allan[$ll].="<div>".'D'.$cn." = ".h($cv)."</div>"; // ������������ �������

	foreach($allan as $ll=>$oo) $s.="</td></tr></table><table><tr valign=top><td><b>LANG:$ll:</b></td><td><br>".$oo;
*/
//=========================================================
	// 3. ��� � �������?
	$DDDIR=array();

	$ruf=get_dfiles_r($pack);

//return "<pre>".print_r($ruf,1)."</pre>";
//return "<pre>".print_r($Ufile,1)."</pre>";

	foreach($Ufile as $f=>$d) { // return $d; 
//return "<pre>".print_r($Ufile,1);
		$fdir=($d!='0 0'?dirname($f).'/':$f); if($fdir=='./') $fdir='/'; // ��� �����
		if(!isset($DDDIR[$fdir])) $DDDIR[$fdir]=array(); // ������� ����� �����
		if($d=='0 0') continue;

		if(!isset($ruf[$f])) { // ���� ������ � ��� �� ���� � ��������������� ������
			$fh=$GLOBALS['filehost'].$f;
			if(!is_file($fh)) $o='iADD'; // ��������
			else { // ���� ���� ����
				list(,$d1)=explode(' ',$d,2);
				if(calcfile_md5($fh,getras($f))!=$d1) $o='iADD'; // ��������
				else $o='';
			}
		} else {
			list(,$d1)=explode(' ',$d,2); list(,$d2)=explode(' ',$ruf[$f],2); // �� ���������� �����!
			if($d1==$d2) $o=''; // ���� ��� �� - ��
			else $o='iUPD'; // U ���� �� ��� - ��������
			unset($ruf[$f]); // � ����� ������ �������
		}
		if($o!='') $DDDIR[$fdir][basename($f)]=$o;
	}

	// ������� ��� ���������
	foreach($ruf as $f=>$d) { // � ���������� ��� ������ ���������
		$fdir=($d!='0 0'?dirname($f).'/':$f); if($fdir=='./') $fdir='/'; // ��� �����
		if(!isset($DDDIR[$fdir])) $DDDIR[$fdir]=array(); // ������� ����� �����
		if($d=='0 0') continue;
		$DDDIR[$fdir][basename($f)]='iDEL';
	}

	// � ����������

	foreach($DDDIR as $dir=>$val) if(sizeof($val)) {
		$s.="<table><tr valign=top><td class='iDIR iOK'>".h($dir)."</td><td class='iT'>";
		foreach($val as $n=>$o) {
$s.="<div class='".$o.' '.(in_array($dir.$n,$veto)?'iSS':'iOK')."'>".$n
.($o=='iUPD'?"<img onclick='i_srav(this)' src='".$GLOBALS['www_design']."e3/kontact_journal.png'>":'')."</div>"; $obnovle++; }
		$s.="</td></tr></table>";
	}

	// return "<pre>".print_r($DDDIR,1)."</pre>";
// $pack!='ALL' && 
		// $s.="<div>".$o.$fname."</div>";
		//if($fdir!=$lastdir){ $s.="</td></tr></table><table><tr valign=top><td><b>$fdir</b></td><td>"; $lastdir=$fdir; }
		//if($fdir!=$lastdir){ $s.="</td></tr></table><table><tr valign=top><td><b>$fdir</b></td><td>"; $lastdir=$fdir; }
		//$s.="<div>".'D'.basename($f)."</div>";



//=========================================================
	if(!$obnovle) return false;
	return "<div id='i_selectfiles'>$s</div>";
}


//----------------------------
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 
// POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST POST 

if(sizeof($_POST)!=0 && !empty($_POST['post_act'])) { $a=$_POST['post_act'];
	if(!UPDATE_testkey($_POST['key'])) die("ohelpc('install2','post',\"error key\");"); // ������������: ���� �����������

if($a=='check_pack') { // ����� ������ ��� �����������
	$p=strtr($_POST['pack'],'+',' ');
	$s=UPDATE_select(urldecode($_POST['ara']),$p);
	if($s===false) die("salert('Nothing to do!',500);");
	die($GLOBALS['selectjs']."
i_ser='".$GLOBALS['httphost']."';
i_url='".urldecode($_POST['url'])."';
i_pack='$p';
ohelpc('install2','post',\"".njsn($s)."\");
go_install('install2');");
}

if($a=='update_file') { // ����� ������ ��� �����������
	$name=urldecode($_POST['file']);
	if(count($_FILES)!=1) die("alert('Error transfer - files: ".count($_FILES)); // ������ �� ������
	$s=''; foreach($_FILES as $f) {
		if(!is_uploaded_file($f["tmp_name"])) die("alert('Error upload: `".h($f["name"])."` as `".h($f["tmp_name"])."`')"); // ������ �����
		if($f['error']!=0) die("alert('Error upload: ".h($f["error"])."')"); // ������ �����
		$s.=UPDATE_file($name,$f["tmp_name"]);
	}
	if($s!=1) die("ohelpc('file_install2','post',\"".njsn($s)."\");");
	die("var s=inst_MAS_UPD.shift(); s=i_find(s); s.parentNode.removeChild(s); i_process();");
}


// idie('1');
	$a=$_POST;
//	idie($_POST['ara']);
	if(isset($a['ara'])) $a['ara']=unserialize(urldecode($a['ara']));
	if(count($_FILES)>0) {
		foreach($_FILES as $n=>$FILE) if(is_uploaded_file($FILE["tmp_name"])){
		$a["file: `$n`"]=$FILE;
		}
		// idie('Files: '.count($_FILES));
	}
	dier($a);
}


function AD2() { if(!isset($_COOKIE["adm2"]) || $_COOKIE["adm2"]!=$GLOBALS['admin_hash1']) { idie('Admin only!'); } }







function INSTALL_ajax() { $a=RE('a');
//=========================================================================
if($a=='login') { // ������������
	if($GLOBALS['admin_hash1']==broident2(c(RE('v')).$GLOBALS['koldunstvo1'])) return "
c_save('adm2','".$GLOBALS['admin_hash1']."');
window.location='".$GLOBALS['$mypage']."?reboot='+Math.random();";
	sleep(5); return "salert('Wrong password',2000)";
}

if($a=='setup_password') { // �������������� ������
// ���� ���������� ������ � �� �����������, ��������� �������������� � ������� �����, ���� �� �� ���
if(isset($GLOBALS['admin_hash1']) && preg_match("/^[0-9a-z]{40}$/",$admin_hash1)) AD2();
	if(preg_match("/^[0-9a-z]{40}$/",$GLOBALS['admin_hash1'])) return "salert('Error 666')";
	if(!isset($GLOBALS['koldunstvo1'])) config_change('koldunstvo1',hash_generate()); // ���� �� ���� - �������
	$pass=RE('v');
	if($pass!=c($pass)) return "salert('Don`t use space in 1 or last letter!',4000);";
	$pass=c($pass);	if($pass=='') return "salert('Where the password?!',2000);";
	config_change('admin_hash1',broident2($pass.$GLOBALS['koldunstvo1']));
	return "salert('Password: $pass',2000); window.location='".$GLOBALS['$mypage']."?reboot='+Math.random();";
}


//======================= MIJAX �� �������� ������� - ��� ���������� ������! =====================
// ������-�����

if($a=='install_far_cmp') { // ������ POST - ��� ���������� ��� �� ����� �������-�����
	$file=rpath(RE('file')); if(is_vetofile($file)) return "alert('Disabled file: ".h($file)."')"; // veto?
	$file_my=file_get_contents(RE('url').$GLOBALS["installname"].'?'.urlencode($file)); // ������� ��� ����
		if(substr($file_my,0,6)=='ERROR:') return "alert('".h($file_my)."')"; // veto?
	$file_ser=file_get_contents(realpath($GLOBALS['filehost'].$file));
		include_once $GLOBALS['include_sys']."_podsveti.php"; // ��������� ������ ������ � ����� �������
//		$s=highlight_string(podsveti($file_my,$file_ser),1);
//		$s=podsveti(h($file_my),h($file_ser),"\n");
		$s=podsveti(h($file_my),h($file_ser));

//	fileput($GLOBALS['filehost'].'n1',$file_my);
//	fileput($GLOBALS['filehost'].'n2',$file_ser);
//	$s=podsveti('ffdfd dfdf','dfdfd sdfdfd');

//$s='fsdfsdf';
//return "idie(\"".njsn(nl2br(h($file_ser)))."\")";

//return("alert(082)");

/*fgf*/
	return "idie(\"".njsn(nl2br($s))."\")";
}

if($a=='install_update_far') { // ������ POST - ��� ���������� ��� �� ����� �������-�����
	$file=RE('file'); $fhost=realpath($GLOBALS['filehost'].$file);
	if(is_vetofile($file)) return "alert('Disabled file: ".h($file)."')"; // veto?
	if(empty($fhost) || !is_file($fhost)) return "alert('File not found: ".h($file)."')";
	return POST_file($fhost,RE('url').$GLOBALS["installname"],array('post_act'=>'update_file','file'=>$file,'key'=>RE('key')));
}

if($a=='install_far_check') { // ������ POST - ��� ���������� ��� �� ����� �������-�����
	$pack=trim(RE('pack')); $r=get_pack_r($pack);
	return POST_file('',RE('url').$GLOBALS["installname"],array('post_act'=>'check_pack','url'=>$GLOBALS['httphost'],'pack'=>$pack,'key'=>RE('key'),'ara'=>serialize($r)));
}

// �������� ��-������ ������ ��������� ������� �� ���� ������� - ������-�����:
if($a=='install_get_packs') { // ������� ������ �������
	$packs=explode(' ',trim(RE('pack')));
	$dir=$GLOBALS['filehost'].'binoniq/instlog/'; $pacdir=$dir.'instpack/';
	$ft=$dir."all_md5.tmp"; $lasttime=(is_file($ft)?date("Y-m-d h:i:s",filemtime($ft)):"- no -");
	$s="<div class=r>Server: <b>".$GLOBALS['httphost']."</b>"
."<br>Admin: <a title='mail:&nbsp;".$GLOBALS['admin_mail']
.(isset($GLOBALS['admin_mobile'])?"<br>mob:&nbsp;".$GLOBALS['admin_mobile']:'')
."' href='mailto:".$GLOBALS['admin_mail']."'>".$GLOBALS['admin_name']."</a>"
."<br>Last update: <b>".$lasttime."</b></div><p>";
	foreach(get_my_packlist() as $l) $s.="<div><input class='cb' name=\"$l\" type='checkbox'".(in_array($l,$packs)?' checked':'').">$l</div>";
	return "zabil('epacks',\"".njsn($s)."\")";
}

AD2();
//=========================================================================


// dier($_REQUEST,1);

if($a=='testmod') { // �������� ������
	$m=RE('module');
		$mod=$GLOBALS['host_module']."install/".$m; include_once($mod);
		$r=installmod_init();
		if($r!=strtr($r,"\n<>",'---')) $s="
			clean('module__$m');
			zabil('mesto_otvet',vzyal('mesto_otvet')+\"<hr color='red'>".njsn($r)."\");
		";
		else {
			$s='';
			if($r!==false) $s.= "zabil('module__$m',\"<input type=button style='font-size:8px;' value='$r' onclick=\\\"dodo('$m',0,0,0)\\\">\");";
			else $s.="clean('module__$m');";
		}
	$s.="check_mod_do();";
	return $s;
}

if($a=='do') { // ������ ������
	global $skip,$allwork,$time,$o,$delknopka,$script; $o=$delknopka=$script='';
	$time=RE0('time'); $skip=RE0('skip'); $allwork=RE0('allwork');
	$m=RE('module'); $mod=$GLOBALS['host_module']."install/".$m; include_once($mod);
	if(installmod_init()===false) return "clean('module__$m'); salert('not nessesary',2000);";
	if(!$allwork) { $allwork=(function_exists('installmod_allwork')?installmod_allwork():0); }
        $r=installmod_do();
                $script=(empty($script)?'':$script);
			if(intval($r)==0 and $r!==0) $o=$r;
	                $o=($o==''?'':"zabil('mesto_otvet',\"".njs($o)."\");");
        	        $delknopka=(isset($delknopka)?"clean('module__$m');":'');
                if($r===0) return $script."clean('percent');".$o.$delknopka;
                if(intval($r)==0) return $script."clean('percent');".$o.$delknopka;
                return $script.$o."
var z=(idd('percent')?0:1);
helps('percent',\"<fieldset><legend>$m &nbsp; &nbsp; \"+parseInt((100/$allwork)*$skip)+\"% <span class='timet'>
if(z) posdiv('percent',-1,-1);
dodo('$m',$allwork,$time,$r);";
}


//------------ ��� ����� editfile ------------------
if($a=='edit_file'){ $file=RE('file'); return "save_and_close=function(){save_no_close();clean('editor')};
save_no_close=function(){ if(idd('edit_text').value==idd('edit_text').defaultValue) return salert('save_not_need',500);
majax('module.php',{mod:'INSTALL',a:'save_file',file:\"".njs($file)."\",text:idd('edit_text').value});
idd('edit_text').defaultValue=idd('edit_text').value;
};

ohelpc('editor','Edit: ".h($file)."',\"<table><tr><td>"
."<textarea style='width:\"+(getWinW()-100)+\"px;height:\"+(getWinH()-100)+\"px;' id='edit_text'>"
.h(njsn(file_get_contents($file)))."</textarea>"
."<br><input title='ctrl+Enter' type='button' value='Save+exit' onclick='save_and_close()'> <input title='shift+Enter' type='button' value='Save' onclick='save_no_close()'>"
."</td></tr></table>\");
idd('edit_text').focus();

setkey('esc','',function(e){ if(idd('edit_text').value==idd('edit_text').defaultValue || confirm('exit no save?')) clean('editor'); },false);
setkey('enter','ctrl',save_and_close,false);
setkey('enter','shift',save_no_close,false);
setkey('tab','shift',function(){ti('edit_text','\\t{select}')},false);
";
}
if($a=='save_file'){ fileput(RE('file'),RE('text')); return "salert('saved',500)"; }

//------------ login ------------------
if($a=='logout') { // �������������
	return "c_save('adm2',''); c_save('adm',''); window.location='".$GLOBALS['$mypage']."?reboot='+Math.random();";
}

if($a=='passchange') { // �������� ������ (�������� ������ ������)
	return "
helpc('oldpass',\"<fieldset><legend>Change Password</legend>".njsn(
"Old password: <input type='text' size='15' id='old_pass' value=''>"
."<br>New password: <input type='text' size='15' id='new_pass' value=''>"
."<br><input type='button' value='Setup' onclick=\"majax('module.php',{mod:'INSTALL',a:'passchange_',old:idd('old_pass').value,new:idd('new_pass').value})\">"
)."</fieldset>\");
";
}

if($a=='passchange_') { // �������� ������ (�������� ������ ������)
	if($GLOBALS['admin_hash1']!=broident2(c(RE('old')).$GLOBALS['koldunstvo1'])) {
		 sleep(1); return "clean('oldpass'); salert('Wrond old password!',4000);";
	}
	$pass=c(RE('new'));
	config_change('admin_hash1',broident2($pass.$GLOBALS['koldunstvo1']));
	return "salert('Password: $pass',2000); window.location='".$GLOBALS['$mypage']."?reboot='+Math.random();";
}


//------------ install ------------------
function fileget_save($file,$s='') {
	$a=explode('/',$file); $file=array_pop($a); $dir=implode('/',$a)."/";
	if(($o=file_get_contents($dir.$file))===false || $o==''&&$s!='') {
		testdir($dir); fileput($dir.$file,$s);
		if(file_get_contents($dir.$file)!=$s) idie("Cann't save: ".h($dir.$file)."<p>Check permissions.");
		return $s;
	} return $o;
}


$maj="majax('module.php',{mod:'INSTALL',a:";
$dir=$GLOBALS['filehost'].'binoniq/instlog/';

if($a=='install') { // �����������

	$serv=fileget_save($dir."servers.txt","http://lleo.me/blog Beta
http://lleo.me/dnevnik Stable
http://lleo.me Super Stable
http://binoniq.net Server Stable");

	$select_serv=fileget_save($dir."server.my","http://lleo.me/blog\n+basic");

// �����������!!!!!11
if( ($l=str_replace('lleo.aha.ru','lleo.me',$serv)) != $serv) { fileput($dir."servers.txt",$l); $serv=$l; }
if( ($l=str_replace('lleo.aha.ru','lleo.me',$select_serv)) != $select_serv) { fileput($dir."server.my",$l); $select_serv=$l; }
// �����������!!!!!11

	$select_serv=explode("\n",$select_serv);

	$o=array(); foreach(explode("\n",$serv) as $l) { $l=trim($l,"\n\r\t "); if($l=='') continue;
		list($ser,$ver)=explode(' ',$l,2); $o[$ser]=$ver.': '.$ser;
	}

	$s="server: ".selecto('servs',$select_serv[0],$o,"onchange=\"zabil('epacks','');"
."mijax(this.value+'/ajax/midule.php',{mod:'INSTALL',a:'install_get_packs',pack:'".implode(' ',get_my_packlist())."'});"
."\" id")."
<br><input type='button' value='Check Update' onclick='servselect(this)'>
<div id='epacks' style='margin: 20px; border: 1px dotted #ccc'>

".implode(' ',get_my_packlist())."
";


unset($select_serv[0]);
foreach($select_serv as $l) { $w=substr($l,1);
	$s.="<div><input class='cb' name=\"$w\" type='checkbox'".($l[0]=='+'?' checked':'').">$w</div>";
}
$s.="</div>";
	$s.="<div id='mypan' style='position:relative;font-size: 14px; margin: 20px; padding: 20px; border: 1px dotted #ccc'>"
."<img id='expert_knop' onclick=\"majax('module.php',{mod:'INSTALL',a:'expert_options_panel'})\""
." title='Other options<br>(expert mode)' src='".$GLOBALS['www_design']."e3/system.png' style='position:absolute;display:inline;right:0px;top:0px;cursor: pointer;'>"
."<p>installed:<div id='mypacks' style='padding-left:50px;'>".get_my_pack()."</div></div>";

	return "
servselect=function(e){ var s='',e=getElementsByClass('cb');
	for(var i=0;i<e.length;i++) s+=' '+(e[i].checked?'+':'-')+e[i].name;
	if(s=='') { alert('Select packet'); return; }
	$maj'install_check',s:idd('servs').value,pack:s});
};
ohelpc('install','Select server',\"".njsn($s)."\");";
//	return "zabil('mesto_otvet',\"<div class=r>".njs(nl2br(print_r($r,1)))."</div>\");";
//	mijax(idd('servs').value+'/ajax/midule.php',{mod:'INSTALL',a:'install_check',s:idd('servs').value,pack:s});
//	alert(idd('servs').value+'/ajax/midule.php?mod=INSTALL&a=install_check&s='+idd('servs').value+'&pack='+encodeURIComponent(s));
//
}

if($a=='expert_options_panel') { // ������ �����

$s="<input type='button' value='Clean *.old' onclick=\"$maj'install_clean',s:idd('servs').value})\">
<input type='button' value='Back' onclick=\"$maj'install_back',s:idd('servs').value})\">
<input type='button' value='TEST' onclick=\"$maj'install_test',s:idd('servs').value})\">
<span title='Create my inctallpack!' class='l' onclick=\"majax('module.php',{mod:'INSTALL',a:'install_edit_pack',name:''})\" style='margin-left:20px'>[new]</span>
";

foreach(glob($dir."*.txt") as $l) { $l0=basename($l); $s.="<div class='l' onclick=\"majax('module.php',{mod:'INSTALL',a:'edit_file',file:'$l'})\">$l0</div>"; }

return "

zabil('mypan',\"<div style='border:1px dotted #ccc; width:100%;'>".njs($s)."</div>\"+vzyal('mypan'));
zabil('mypacks',\"".njs(get_my_pack(0))."\");
clean('expert_knop');
";
}


if($a=='install_edit_pack') { // ����� �������������� ������ ��� �������� ������ (name='')
	$name=RE('name');

	$p=array(); if($name!='' && ($r=file($dir."instpack/".$name.".pack"))!==false) {
		foreach($r as $l) { $m=explode(' ',$l); $p[$m[0]]=array($m[1],$m[2]); } // [0] => template/adminpanel.htm 1303587256 d866bd70d3d53450fd3b82243d32fe36
	}

	//-----
	$s=''; $lastdir=''; foreach(get_dfiles() as $l) { list($file,$ftime,$fkey)=explode(' ',$l,3);
		$fhost=$GLOBALS['filehost'].$file; // ���������� ����
		$fname=basename($file); // ��� ���
		$fdir=($ftime.$fkey!='00'?dirname($file).'/':$file); if($fdir=='./') $fdir='/'; // ��� �����
		if($fdir!=$lastdir) { $s.=($s==''?'':"</td></tr></table>")."<table><tr><td class='iDIR iOK'>$fdir</td><td>"; $lastdir=$fdir; }
			if($ftime.$fkey=='00') continue;
			$s.="<div class='".(isset($p[$file])?'iYES':'iNON')."'>".$fname."</div>";
	}
	$s="<div id='i_selectfiles'>$s".($s!=''?'</td></tr></table>':'')."</div>";
	//-----

$subm="<input type='button' value='Save' onclick='i_packsave()'>"
."&nbsp; &nbsp; <span class='ll' onclick=\"i_selectall()\">select</span>"
."&nbsp; &nbsp; <span class='ll' onclick=\"i_toggle_visible()\">show/hidden</span>"
."&nbsp; &nbsp; <img src='".$GLOBALS['www_design']."e3/remove.png' title='Delete' onclick=\"packdel()\">";

	return $GLOBALS['selectjs'].($name==''?"i_toggle_visible_d=0;":'')."

packdel=function(){ if(confirm('Delete pack `".$name.".pack`?')) majax('module.php',{mod:'INSTALL',a:'install_pack_del',name:idd('newpack_name').value}); };

i_packsave=function(){
	majax('module.php',{mod:'INSTALL',a:'install_pack_save',s:i_get_selected(),name:idd('newpack_name').value});
};

ohelpc('pack','Edit pack: $name',\"".njsn(
($name==''?"<b>name: </b><input type='text' value='' size='10' maxlength='20' id='newpack_name'>":
"<input type='hidden' value='$name' id='newpack_name'>")
.$subm
."<div id='packs'><tt>$s</tt></div>$subm")."\"); go_install('pack');";
}

if($a=='install_pack_del') { // �������� ������
	$name=RE('name'); unlink($dir."instpack/".$name.".pack");
	return "clean('pack'); zabil('mypacks',\"".njsn(get_my_pack(0))."\"); salert('Pack <b>$name</b> deleted!',1000);";
}

if($a=='install_pack_save') { // ������� �������� ������ ������ majax('module.php',{mod:'INSTALL',a:'install_pack_save',s:s,name:idd('newpack_name').value});
	$name=preg_replace("/[^0-9a-z\_\-\.]+/s",'',strtolower(RE('name'))); if(empty($name)) return "idd('newpack_name').value='$name'; idie('Name error! Only: 0-9a-z_-.');";
	$s=''; $r=get_dfiles_r(); foreach(explode("\n",trim(RE('s'),"\n")) as $l) {
		if(isset($r[$l])) $time_md5=$r[$l];
		else { $ras=getras($l); $time_md5=filemtime($l)." ".calcfile_md5($l,$ras); }
		$s.="$l $time_md5\n";
	}
	if($s=='') return "salert('Empty pack!',1000);";
	testdir($dir."instpack"); fileput($dir."instpack/".$name.".pack",$s);
	return "clean('pack'); zabil('mypacks',\"".njsn(get_my_pack(0))."\"); salert('Pack <b>$name</b> saved!',1000);";
}

// ������� ������ �� ����������� �������
if($a=='install_check') { // ����������� - ��� ���������� ��� �� ����������� �������
	$ser=RE('s'); $pack=RE('pack');
	$e=explode(' ',$pack); $w=array(); foreach($e as $l){ if($l[0]=='+') $w[]=substr($l,1); }
	fileput($dir."server.my",$ser.strtr($pack,' ',"\n"));
	// ������ ������ �� ������-�����
	return "mijax('".$ser."/ajax/midule.php',{mod:'INSTALL',a:'install_far_check',url:'".$GLOBALS['httphost']."',pack:'".implode(' ',$w)."',key:'".createkey()."'})";
} // � ��� � �� - ������-�����

// ������������ ������� �� �����������
if($a=='install_update_NON') { // NON - �������� ����� ���������� ���
	$f=$dir."veto.my";
	if(($s=file_get_contents($f))!==false) { $s=unserialize($s);
		$r=get_dfiles_r(RE('pack')); // ����� ��� ����� ��� ���� �������
		foreach($s as $n=>$l) { $l=trim($l); if(isset($r[$l])) unset($s[$n]); } // ������������ ��� ��� ���� �������
	} else $s=array();
	$s=array_merge($s,explode("\n",RE('d'))); // �������� �����
	fileput($f,serialize($s));
	return "for(var i in inst_MAS_NON){ var s=i_find(inst_MAS_NON[i]); s.parentNode.removeChild(s); } inst_MAS_NON=[]; i_process();";
}

if($a=='install_update_DEL') { // DEL - ������� 1 ����
	$file=html_entity_decode(RE('file'));
		if(preg_match("/^(config\.php)\:([^\:\=]+)\=(.+?)$/s",$file,$m)) { config_del($m[2]);
		return "var s=inst_MAS_DEL.shift(); s=i_find(s); s.parentNode.removeChild(s); i_process();";
		}
	$f=$GLOBALS['filehost'].$file;
	if(is_file($f)) { backupfile($f); unlink($f); } elseif(is_dir($f)) rmdir($f); else idie('Not found: '.h($f));
	return "var s=inst_MAS_DEL.shift(); s=i_find(s); s.parentNode.removeChild(s); i_process();";
}

if($a=='install_update_UPD') { // UPD - �������� 1 ����
	$file=html_entity_decode(RE('file'));
		if(preg_match("/^(config\.php)\:([^\:\=]+)\=(.+?)$/s",$file,$m)) { config_add($m[2],$m[3]);
		return "var s=inst_MAS_UPD.shift(); s=i_find(s); s.parentNode.removeChild(s); i_process();";
		}
	return "mijax('".getmatka()."/ajax/midule.php',{mod:'INSTALL',a:'install_update_far',url:'".$GLOBALS['httphost']."',key:'".createkey()."',file:'$file'})";
}


/***********/
if($a=='install_cmpfile') { // �������� ��� ����� PHP
	$file=html_entity_decode(RE('file'));
	return "mijax('".getmatka()."/ajax/midule.php',{mod:'INSTALL',a:'install_get_far_file',key:'".createkey()."',file:'$file'})";
}
//====================================================================


if($a=='install_test') { // ����������� POST_file($filepath,$url,$fields,$port=80,$scheme='http');

	$r=getpack('basic',array());

	return("idie(\"path: ".implode('<br>',$r)."\");");

//	return("alert(\"path: ".rpath('//this/\\\\//is/../a/./test/.///is//')."\");");

/*
	return "mijax('http://lleo.me/blog/ajax/midule.php',{mod:'INSTALL',a:'install_update_far',url:'".$GLOBALS['httphost']."',key:'".createkey()."',file:'binoniq/melok/mp3.swf'})";

	$pack='';
	dier(explode(' ',$pack));


	$r=get_dfiles_r();
	dier($r);

	$t=POST_file(array(
$GLOBALS['filehost']."re.png",
$GLOBALS['filehost']."re.php",
$GLOBALS['filehost']."install.zip",
$GLOBALS['filehost']."gg.zip"
),'http://lleo.me/blog/install',array('post_act'=>'do','aaa'=>'123','key'=>'rrr'));
	idie($t);
*/
}


}
//=========================================================================
//=========================================================================
//=========================================================================
//=========================================================================
//=========================================================================
//=========================================================================
//function sr($s){ return "<font color=red>$s</font>"; }
//function sg($s){ return "<font color=green>$s</font>"; }

// ��������� �� ����� �� ����� �������� � ����������
function calcfile_md5($l,$ras) { $o=file_get_contents($l);
	if($ras=='php') $o=preg_replace("/[\n\r]+\/\*\s*lleo\s*\*\/[^\n\r]+/si","\n",$o);
	if($ras=='pack') $o=preg_replace("/((^|\n)[^ ])+.+?$/s","$1",$o);
	if($ras=='css') {
	        $o=preg_replace("/url\([\'\"]*[^\s\'\"\)]+[\'\"]*\)/si",'#',$o);
        	$o=preg_replace("/\@charset\s[\'\"][^\s\'\"]+[\'\"]*/si",'#',$o);
	        $o=str_replace('{www_design}','#',$o);
	}
	return md5($o);
}

// ����� ������ �� ������ $pack (���� ALL - �� �������������� ��) � �������� � ������� $e
function getpack($pack,$e) { global $filehost; $save=0;
	$dir=$filehost."binoniq/instlog/instpack/"; testdir($dir); // ��������� ����� ��� �������
	if($pack='ALL') $r=get_dfiles(); // ���������� �����
	else if(is_file($dir.$pack.".pack")) { $r=array();  $s=file($dir.$pack.".pack");
		foreach($s as $l) { list($name,$time,$md5)=explode(' ',trim($l));
			$l=$filehost.$name; if(!is_file($l)) { $save=1; continue; } // ���� ��� ������
			$tim=filemtime($l); if($time!=$tim) { $save=1; $md5=calcfile_md5($l,getras($l)); } // ���������
			$r[]="$name $tim $md5";
		}
	}
	if($save) fileput($dir.$pack.".pack",implode("\n",$r)); // ��������� �����, ���� ���� ���������
	foreach($r as $n=>$l) { if(in_array($l,$e)) unset($r[$n]); } // �������� �����
	return array_merge($e,$r);
}




function is_vetofile($f) { global $vetomas; // ���� �� �������� ��� ��� $vetomas - ���������
	if(!isset($vetomas)) { $vetomas=array(); if(($s=file($GLOBALS['filehost']."binoniq/instlog/system_veto.txt"))!==false) foreach($s as $l) { $l=trim($l); if($l!='' && substr($l,0,1)!='#') $vetomas[]=$l; } }
	$f=trim(rpath($f),'/'); // ���������
		if(array_pop(explode('/',$f))=='config.php.tmpl') return 0;
	foreach($vetomas as $l) { if(strtolower(substr($f,0,strlen($l)))==$l) return 1; }
	return 0;
}

// �������� ������ �� ���� ������ ������ (������� ��������� � system_dir.txt)
function get_dfiles() { global $stop,$md5mas,$filehostn,$filehost,$allmd5change; $stop=1000;
	if(!isset($filehostn)) $filehostn=strlen($filehost);
	$dir=$GLOBALS['filehost']."binoniq/instlog/"; testdir($dir);
	// ����� $md5mas - ������ ������ �� ����� ������
	$md5mas=array(); $allmd5change=1; if(($s=file_get_contents($dir."all_md5.tmp"))!==false) { $allmd5change=0; $md5mas=unserialize($s); }
	// ����� $all - ������ ������ �� ����� ������
	$all=array(); $s=file($dir."system_dir.txt"); foreach($s as $l) { $l=trim($l); if($l!='' && substr($l,0,1)!='#') $all[]=$l; }
	// ���������� �� ������
	$r=array(); foreach($all as $l) $r=array_merge($r,get_dfiles2($l));
	// ����������� ���������, ���� ����
	if($allmd5change) fileput($dir."all_md5.tmp",serialize($md5mas));
	return $r;
}

function get_dfiles2($files) { global $stop,$md5mas,$filehostn,$filehost,$allmd5change; if(!--$stop) die('stop error');
	$r=array(); $a=$filehost.$files; if(is_file($a)) $a=array($a); else {
		$l=$a; $a=glob($a."/*"); $h=$l."/.htaccess"; if(is_file($h)) $a[]=$h;
		if(!sizeof($a)) return array(c(substr($l,$filehostn))."/ 0 0"); // ���� ������ �����
	}

	// ������ ������� �����
	foreach($a as $n=>$l) { if(is_dir($l)) continue; $name=c(substr($l,$filehostn));
		$ras=getras($l); if(!is_vetofile($name) && $ras!='old' && $ras!='off' && substr($ras,0,6)!='old---') { $time=filemtime($l);
			if(isset($md5mas[$name]) && $md5mas[$name][0]==$time) $md5=$md5mas[$name][1]; // ��� ���������
			else { $md5=calcfile_md5($l,$ras); $md5mas[$name]=array($time,$md5); $allmd5change=1; }
			$r[]="$name $time $md5";
		}
	        unset($a[$n]); 
	}
	// ����� ������� �����
        foreach($a as $l) { if(!is_vetofile($l)) { $name=c(substr($l,$filehostn)); $r=array_merge($r,get_dfiles2($name)); } }
        return $r;
}

function get_dfiles_r($pack='') { // ����� ����� � ������� �������
	$r=array(); foreach(explode(' ',$pack) as $p) {
		foreach(getpack($p,array()) as $l) { list($f,$time,$md5)=explode(' ',$l,3); $r[$f]=$time." ".$md5; }
	} return $r;
}
//=========================================================================

// ������ � ��������

// �������� � ������
function config_add($name,$value){ if(($s=config_get())===false) return $s;
	$str="\$".$name.'='.((strstr($value,'"')||strstr($value,"'")||preg_match("/^\d+\;/s",$value))?$value:'"'.$value.'";');
	return config_put(preg_replace("/\n\s*\?>\s*$/s","\n".$str." // added ".date("Y-m-d")."\n?>\n",$s));
}
// ������� �� �������
function config_del($name){ if(($s=config_get())===false) return $s;
	return config_put(preg_replace("/\n(\s*[\$]".$name."\s*=[^\n]+)/s","\n// deleted ".date("Y-m-d").": $1",$s));
}
// �������� � ������� (���� �� ���� - �� ��������)
function config_change($name,$value){ if(($s=config_get())===false) return $s;
	if(!isset($GLOBALS[$name])) return config_add($name,$value);
	return config_put(preg_replace("/([\n\r]+\s*[\$]".$name."\s*=\s*)[\'\"][^\'\"]*[\'\"]\s*;([^\n]*)/si","$1\"".$value."\";$2",$s));
}
function config_get(){ $f=config_name(); if(($s=file_get_contents($f))===false) return false; return $s; }
function config_put($s){ $f=config_name(); fileput($f,$s); }
function config_name(){ global $ajax,$filehost;
	if(isset($filehost)) return $filehost."config.php";
	if($ajax) return "../config.php";
	return "config.php";
}

// ������������� hash-������
function rando($x,$y){ $s='';
//	$k=10; while((--$k)&&!strlen($s)){ if(($g=fopen("/dev/random","rb"))===false) break; $s=fgets($g); fclose($g); }
	if(!strlen($s)) { // /dev/random �� ��������, ������� ������������ �������
		list($t,)=explode(" ",microtime()); mt_srand($t+mt_rand()); $a=mt_rand(0,$y)+$t;
	} else { for($f=1,$a=$j=0;$j<min(strlen($s),3);$j++,$f*=256) $a+=ord($s[$j])*$f; }
	return $x+$a%($y-$x);
}

function hash_generate(){ // idie('5');
	$A='ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz01234567890';
	for($s='',$i=0,$n=strlen($A);$i<128;$i++) $s.=$A[rando(0,$n)]; return $s; //convert_uuencode($s);
}

// ������ � ���������

// �������� ���� � �������
function msq_change_pole($table,$pole,$s){ if(msq_pole($table,$pole)!==false) msq("ALTER TABLE `".$table."` CHANGE `$pole` `$pole` $s"); }
// �������� ���� �������
function msq_add_pole($table,$pole,$s){ if(msq_pole($table,$pole)===false) msq("ALTER TABLE `".$table."` ADD `".$pole."` ".$s." NOT NULL"); }
// ������� ���� �� �������
function msq_del_pole($table,$pole){ if(msq_pole($table,$pole)!==false) msq("ALTER TABLE `".$table."` DROP `".$pole."`"); }
// �������� ������ � �������
function msq_add_index($table,$pole,$s){ if(msq_pole($table,$pole)!==false && !msq_index($table,$pole)) msq("ALTER TABLE `".$table."` ADD INDEX `".$pole."` ".$s); }
// ������� ������ �� �������
function msq_del_index($table,$pole){ if(msq_index($table,$pole)) msq("ALTER TABLE `".$table."` DROP INDEX `".$pole."`"); }
// ������� �������
function msq_add_table($s){ msq($s); }
// ������� �������
function msq_del_table($table){ if(msq_table($table)) msq("DROP TABLE `".$table."`"); }

//======================================================================================
// ������������ �������� ����������
function admin_pohvast() { return "<center><div id=soobshi><input type=button value='������������ �������� ����������' onclick=\"document.getElementById('soobshi').innerHTML = '<img src=http://lleo.me/blog/stat?link={httphost}>';\"></div></center>"; }

//======================================================================================
// ������ ������
function admin_login() { global $mypage,$koldunstvo,$admin,$admin_hash1; $s='';

// �������� ��� ������ ������
if(!preg_match("/^[0-9a-z]{40}$/",$admin_hash1)) return "<font color='red'>Admin's password is not setup! Setup it now! Please, try to be first :)</font>
<p>Create admin password: <input type='text' name='admin_pass' size='15' id='admin_pass' onchange=\"idd('submitpass').click()\" value=''> <input id='submitpass' type='button' value='Setup' onclick=\"majax('module.php',{mod:'INSTALL',a:'setup_password',v:idd('admin_pass').value})\">";

// ����� - ������������� (��� �������� ������)
if(
// $admin
isset($_COOKIE["adm2"]) && $_COOKIE["adm2"]==$GLOBALS['admin_hash1']
) return "<input type='submit' value='Logout' onclick=\"majax('module.php',{mod:'INSTALL',a:'logout'})\">
<p><input class='br' type='submit' value='Change Admin Password' onclick=\"majax('module.php',{mod:'INSTALL',a:'passchange'})\">";

// �� ���������

return "Admin password: <input type='text' size='15' name='admin_pass' id='admin_pass' onchange=\"idd('submitpass').click()\"> <input id='submitpass' type='button' value='Login' onclick=\"majax('module.php',{mod:'INSTALL',a:'login',v:idd('admin_pass').value})\">";
}

//=========================================================================
//=========================================================================
//=========================================================================
//=========================================================================
//=========================================================================
//=========================================================================
//=========================================================================
//=========================================================================
//=========================================================================
function INSTALL($e) { $s=$im='';

if($GLOBALS['admin']) {

STYLES("mod","
.iDIR,.iYES,.iNON,.iDEL,.iUPD,.iADD { cursor:pointer; clear:left;float:left; }
.iNON {color: #aaa}
.iDEL {color: red}
.iYES,.iUPD {color: green}
.iADD {color: rgb(0,255,0)}
.iNON,.iSS {text-decoration:line-through}
.iNON:before,.iNON:after,.iSS:before,.iSS:after {content:'�'}
.iYES,.iOK {text-decoration:none}

.iDIR {font-weight: bold; float:left; valign:top; }
.iT {float:left;margin-top:20pt;}

.p1 { color: #3F3F3F; text-decoration: line-through; background: #DFDFDF; } /* ����������� */
.p2 { background: #FFD0C0; } /* ����������� */

"); //.mod {font-size:11px;} .iDDR {font-weight: bold; color:green;}


        $upgrade=glob($GLOBALS['host_module']."install/*.php");
        foreach($upgrade as $l) { $m=array_pop(explode('/',$l));
		$im.="'$m',";
		$s.="<div class='mod' id='module__$m'>".$m."</div>";
	}

SCRIPTS("mod","
var install_modules_n=0;
function check_mod_do() { if(typeof install_modules[install_modules_n] == 'undefined') { install_modules_n=0; return; }
	var m=install_modules[install_modules_n++];
	zabil('module__'+m,'<img src='+www_design+'img/ajax.gif>'+vzyal('module__'+m));
	majax('module.php',{mod:'INSTALL',a:'testmod',module:m});
}
var install_modules=[".trim($im,',')."];

var timestart;
function dodo(m,allwork,time,skip) {
	if(skip) {
		var timenow = new Date();
		var t=timenow.getTime()-timestart.getTime();
		var e=parseInt((t/skip)*allwork)-t;
		zabilc('timet',' &nbsp; &nbsp; &nbsp; ��������: '+pr_time(e)+' ���');
	} else { timestart = new Date(); }
	majax('module.php',{mod:'INSTALL',a:'do',module:m,allwork:allwork,time:time,skip:skip});
}

function pr_time(t) { var N=new Date(); N.setTime(t); var s=pr00(N.getUTCSeconds());
	if(N.getUTCMinutes()) s=pr00(N.getUTCMinutes())+':'+s;
	if(N.getUTCHours()) s=pr00(N.getUTCHours())+':'+s;
	return s;
} function pr00(n){return ((''+n).length<2?'0'+n:n)}


page_onstart.push('check_mod_do()');

");

// $s.="<p><input type='button' value='check' onclick='check_mod_do()'>";
}


SCRIPTS("page_onstart.push('hotkey_reset=function(){}; hotkey=[];');"); // ��������� ������

return "<table width=100% style='border: 1px dotted red'>
<tr valign=top>
	<td>
		<p><input type='button' value='INSTALL' onclick=\"majax('module.php',{mod:'INSTALL',a:'install'})\"><p>
		<div id='mesto_module'>$s</div>
	</td>
	<td width='100%'><div id='mesto_otvet'>".admin_login()."</div></td>
</tr></table>";

}

//==================================================================================================
function getconf($l){ $r=array(); $a=file($l); unset($a[0]); unset($a[sizeof($a)]);
	foreach($a as $l) { $l=trim($l);
		if($l=='' || preg_match("/^\s*(#|\/\/)/s",$l)) continue; // ���� ��� �����������
		$per=preg_replace("/^\s*".'\$'."([a-z0-9\_\-]+).*?$/si","$1",$l); if($per==$l) continue;
		$r[]="config:$per ".preg_replace("/^\s*".'\$'."[a-z0-9\_\-]+\s*\=\s*(.*?)$/si","$1",$l);
	}
	return $r;
}

// ��������� �����
function getlang($f){ $la=$GLOBALS['filehost'].'binoniq/lang/'; $nla=strlen($la); if(substr($f,0,$nla)!=$la) return array();
		$la=substr($f,$nla); $la=substr($la,0,strlen($la)-5);
		$r=array(); foreach(file($f) as $l) { $l=trim($l,"\n\r\t "); if(!strstr($l,"\t")) continue;
		list($per,$val)=explode("\t",$l,2);
		$r[]="lang:".$la.":$per ".trim($val,"\r\n\t ");
	}
	return $r;
}
//==================================================================================================

function get_my_pack($i=1) { $p=get_my_packlist(); if(!sizeof($p)) return 'not found'; $s='';
foreach($p as $w) $s.=($i?"<div>":"<div class='l' onclick=\"majax('module.php',{mod:'INSTALL',a:'install_edit_pack',name:'$w'})\">").h($w)."</div>";
return $s;
}

function get_my_packlist() { $pd=$GLOBALS['filehost'].'binoniq/instlog/instpack/'; if(!is_dir($pd)) return array();
	$p=glob($pd."*.pack"); foreach($p as $n=>$l) $p[$n]=basename($l,'.pack'); return $p;
}

function createkey() { $key=sha1(hash_generate()); // ������������ ����
	fileput($GLOBALS['filehost']."binoniq/instlog/install_key.php",'<?php die("Error 404"); $key="'.$key.'"; ?>');
	return $key;
}

function getmatka(){ $s=file($GLOBALS['filehost']."binoniq/instlog/server.my"); return trim($s[0]); } // ������� ������

function get_pack_r($pack='') {
	$r=array(); foreach(explode(' ',$pack) as $l) $r=getpack($l,$r); // ����� ��� ��������� ������
	$o=$r; foreach($o as $n=>$l) { list($l,)=explode(' ',$l,2); $url=$GLOBALS['filehost'].$l;
		if($l=='config.php.tmpl') { $r=array_merge(getconf($url),$r); } // ���������� ������
		if(getras($l)=='lang') { $r=array_merge(getlang($url),$r); unset($r[$n]); } // ���������� ����, ��� �� �����
	} return $r;
}

function backupfile($f) { if(is_file($f) && substr(getras($f),0,6)!='old---') rename($f,$f.".old---".date("Y-m-d_h-i-s")); }

?>