<?php // ����������� �������������
if(!isset($admin_name)) die("Error 404"); // ����������� ����������� ������ - �����
if(isset($_GET['version'])) die("lleoblog 2.0\n".$admin_name ); // �������� ������ � ���������
// if(!$admin) redirect($wwwhost."login/"); // ����������� - �����

DESIGN('plain',"<a href=$mypage>�������� ������</a>");

$PEST=array_merge($_GET,$_POST);

STYLES("
.adminkletka { border: 1px dashed #ccc; font-size: 12px; }
.adminc { width: 70%; text-align: left; margin-top: 20pt; }
.admins { font-size: 12px; }
");

$o="<center><form method='POST' action='".$mypage."'>";

$skip=intval($PEST['skip']);
//if($skip) $o.="<input type='hidden' name='skip' value='$skip'>";

$o .= "<div class=adminc><fieldset><legend>����� ������</legend>".admin_login()."</fieldset></div>";

if($admin) { $admin_upgrade=0;
	$e .= "<div class=adminc><fieldset><legend>������� MySQL</legend>".admin_tables()."</fieldset></div>";
	if($admin_upgrade) $o .= $e;
	if(!$admin_upgrade) $o .= "<div class=adminc><fieldset><legend>��������</legend>".admin_upgrade()."</fieldset></div>";
	if(!$admin_upgrade) $o .= "<div class=adminc><fieldset><legend>������� ������������?</legend>".admin_pohvast()."</fieldset></div>";
	if(!$admin_upgrade) $o .= $e;
}

$o .= "</form></center>";

die($o);



//======================================================================================
// ������ ���������
function admin_upgrade() { global $PEST,$host_module,$admin,$mypage,$skip,$msqe,$admin_upgrade; $s='';
	$upgrade=glob($host_module."upgrade/*.php");
	foreach($upgrade as $l) { include_once $l; }
	return $s;
}


function admin_rereload($action,$Nskip,$timesec=2) { global $skip,$mypage;

        SCRIPTS("var tiktimen=".$timesec.";
        function tiktime(id) { document.getElementById(id).innerHTML = tiktimen--; setTimeout(\"tiktime('\" + id + \"')\", 1000); }");

        $path = $mypage."?skip=".($skip+$Nskip)."&action=".$action;

        return admin_kletka("action","<font color=red>
������ �� �������! �������� ����������� ����: <span id='tiktime'><script>tiktime('tiktime')</script></span>!
������������ $N ����� <blink>$skip</blink></font>
".admin_redirect($path,$timesec)."<p>");
}

function admin_redirect($path,$timesec) { return "<noscript><meta http-equiv=refresh content=\"".$timesec.";url=\"".$path."\"></noscript>
<script> setTimeout(\"location.replace('".$path."')\", ".($timesec*1000)."); </script>"; }






// �������� ���� � ����
function msq_add_pole($table,$pole,$znachenie,$text) {
	$name=$table."-".$pole;
	$kom='Add Field';
	if(msq_pole($table,$pole)===false) {
		if($GLOBALS['PEST'][$name]==$kom) {
			msq("ALTER TABLE `".$table."` ADD `".$pole."` ".$znachenie." NOT NULL");
			return admin_kletka($name,"<font color=green>� `$table` ��������� ���� `$pole`</font>".$GLOBALS['msqe']);
		} else { return admin_kletka($name,"��������� �������� � `$table` ���� `$pole`; ".$text,$kom); }
	} // else { return admin_kletka($name," ������� �� ���������"); }
}

// ������� ���� �� ����
function msq_del_pole($table,$pole,$text) {
	$name=$table."-".$pole;
	$kom='Delete Field';
        if(msq_pole($table,$pole)!==false) {
		if($GLOBALS['PEST'][$name]==$kom) {
                	msq("ALTER TABLE `".$table."` DROP `".$pole."`");
			return admin_kletka($name,"<font color=green>�� `$table` ������� ���� `$pole`</font>".$GLOBALS['msqe']);
        	} else { return admin_kletka($name,"������ ����� ������� �� `$table` ���� `$pole`; ".$text,$kom); }
	} // else { return admin_kletka($name," ������� �� ���������"); }
}


// �������� ���� � ����
function msq_add_index($table,$pole,$znachenie,$text) { if(msq_pole($table,$pole)===false) return;
	$name=$table."-".$pole;
	$kom='Add Index';
	if(!msq_index($table,$pole)) {
		if($GLOBALS['PEST'][$name]==$kom) {
			msq("ALTER TABLE `".$table."` ADD INDEX `".$pole."` ".$znachenie);
			return admin_kletka($name,"<font color=green>� `$table` �������� ������ `$pole`</font>".$GLOBALS['msqe']);
		} else { return admin_kletka($name,"��������� �������� � `$table` ������ `$pole`; ".$text,$kom); }
	} // else { return admin_kletka($name," ������� �� ���������"); }
}

// ������� ���� �� ����
function msq_del_index($table,$pole,$text) {
	$name=$table."-".$pole;
	$kom='Delete Index';
        if(msq_index($table,$pole)) {
		if($GLOBALS['PEST'][$name]==$kom) {
                	msq("ALTER TABLE `".$table."` DROP INDEX `".$pole."`");
			return admin_kletka($name,"<font color=green>�� `$table` ������ ������ `$pole`</font>".$GLOBALS['msqe']);
        	} else { return admin_kletka($name,"������ ����� ������� �� `$table` ������ `$pole`; ".$text,$kom); }
	} // else { return admin_kletka($name," ������� �� ���������"); }
}

/*
// ������� �������
function msq_add_table($table,$znachenie,$text) { $kom='Add TABLE';
	if(!msq_table($table)) { if($GLOBALS['PEST'][$table]==$kom) {
			msq("ALTER TABLE `".$table."` ADD INDEX `".$pole."` ".$znachenie);
			return admin_kletka($table,"<font color=green>� `$table` �������� ������ `$pole`</font>".$GLOBALS['msqe']);
		} else { return admin_kletka($table,"��������� �������� � `$table` ������ `$pole`; ".$text,$kom); }
	}
}
*/

// ������� �������
function msq_del_table($table,$text) { $kom='Delete TABLE';
        if(msq_table($table)) {	if($GLOBALS['PEST'][$table]==$kom) {
                	msq("DROP TABLE `".$table."`");
			return admin_kletka($table,"<font color=green>������� ������� `$table`</font>".$GLOBALS['msqe']);
        	} else { return admin_kletka($table,"������ ����� ������� ������� `$table`; ".$text,$kom); }
	}
}


function admin_kletka($name,$message,$value=0) { global $skip; if($value && $skip) $GLOBALS['admin_upgrade']=true;
return "<div class=adminkletka><b>$name</b>: $message".($value?" &nbsp; <input type='submit' name='$name' value='$value'>":"")."</div>";
}


//======================================================================================
// ������������ �������� ����������
function admin_pohvast() { return "<center><div id=soobshi><input type=button value='������������ �������� ����������' onclick=\"document.getElementById('soobshi').innerHTML = '<img src=http://lleo.aha.ru/blog/stat?link={httphost}>';\"></div></center>"; }

//======================================================================================
// ������� ���
function admin_tables() { global $filehost,$admin,$mypage;
	$s=file_get_contents($filehost."module/upgrade/sql.txt"); // ����� ������ ��� �� ��������
	$s=preg_replace("/AUTO_INCREMENT=\d+/si","AUTO_INCREMENT=0",$s); // ��������� ������ �������������
	$s=preg_replace("/\n-[^\n]+/si","","\n".$s); // ������ ������ ������������
	$s=preg_replace("/\n{2,}/si","\001",trim($s)); $a=explode("\001",$s); // ���������
	foreach($a as $l) {
		$l=c($l); if(!preg_match("/CREATE TABLE[^\n\`\(]+\`([^\`]+)\`/si",$l,$m)) continue; $table=$m[1];
		if($admin && $GLOBALS['PEST'][$table]=='create' && !msq_table($table)) { msq($l); 
$o .= $GLOBALS['msqe'];
$o .= admin_kletka($table,"<font color=green>�������</font>"); }
		else if(msq_table($table)) $o .= admin_kletka($table,"��������� ".ms("SELECT COUNT(*) FROM `$table`","_l"));
		else { $o .=  admin_kletka($table,"<font color=red>�����������!</font>",'create'); $GLOBALS['admin_upgrade']=1; }
	}
	return $o;
}
//======================================================================================
// ������ ������
function admin_login() { global $mypage,$koldunstvo,$admin,$admin_hash; $s='';

$f_logout = "<center><input type='submit' name='logout' value='�������������'></center>";
$f_login = "<center>������: <input type='text' name='login' size='10'>&nbsp;<input type='submit' value='������������'></center>";

if(!preg_match("/^[0-9abcdef]{32}$/",$admin_hash)) { // �������� ������

if($GLOBALS['PEST']['pass']!='') {

	$s.= "<p>��� ������ ������������, ������� � <b>config.php</b>: <p><center><font color=green>\$admin_hash=\"".md5($GLOBALS['PEST']['pass'].$koldunstvo)."\";</font>
<p>".$f_login."</center>";

	} else {

$s.="<p>� ����� config.conf ���������� \$admin_hash ������ ��� ������ �������. ������� �� ����������� ����� ��� ������ ��� ������. �������� ������:
<center>
<input type='text' size='10' name='pass' value='".htmlspecialchars($GLOBALS['PEST']['pass'])."'>
<input type='submit' name='create' value='������������'>
</center>";

}

} else { // ������ ����������

	if($admin) { // ���������?
		if(isset($GLOBALS['PEST']['logout'])) { // ������ �����������?
			set_cookie("adm","logoff", time()-100, "/", "", 0, true); $admin=false; 
			$s .= "<font color=green>�������������!</font> &nbsp; ".$f_login;
			} else { $s.= $f_logout; }
	} else { // ����������?
		if(isset($GLOBALS['PEST']['login'])) { // �������� ����������?
			if(md5($GLOBALS['PEST']['login'].$koldunstvo) == $admin_hash) { // ������ ������?
				$admin=true;
				set_cookie("adm", broident($admin_hash.$koldunstvo), time()+86400*365, "/", "", 0, true);
				$s .= "<center><font color=green>������������!</font> &nbsp; ".$f_logout."</center>";
			} else { // ������ ��������?
				logi("login.log","\n".date("Y/m/d h:i:s").": (".$lju." ".$sc." ".$IP." ".$BRO.")"); sleep(5);
				$s .= "<font color=red>�������� ������!</font>
<br>���� ����� ����� ������� ���� ������, ��� ���������� �������� ���������� \$admin_hash=\"\"; � config.php<p>".$f_login;
			}
		} else { $s.= "<p><center>".$f_login."</center>"; }
	}
}

return $s;
}

?>