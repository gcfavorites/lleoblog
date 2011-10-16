<?php // Про
// $user="lleo-kaganov";
// $pass="fdfdk";

/*

$subj="Проверка связи!";
$body="<p><font size=1><i>Я проверняю свой новый журнал!</i></font>";
$ans=LJ_post($user,$pass,$subj,$body,array('prop_opt_noemail'=>1));
file_put_contents('otvet',$ans['itemid']);
die("ok");

//$ans=LJ_get($user,$pass,3); - заметка номер 3
//$ans=LJ_edit($user,$pass,$itemid,$subject,$event,array('security'=>'private'));
//if($ans['security']!='public') $ans=LJ_edit($user,$pass,$item,$ans['subject'],$ans['event'],array('security'=>'public'));
//$ans=LJ_getlast($user,$pass);
// print print_r($ans,true);
// exit();
*/


// =============================================== LJ functions =========================================
function LJ_edit($user,$pass,$item,$subj,$body,$opts,$flat="http://www.livejournal.com/interface/flat") { if(gettype($opts)!='array') $opts=array();
$options = array(
        'http'=>array(
        'method'=>"POST",
        'header'=>
                "Accept-language: ru-ru,ru\r\n",
                "Content-type: application/x-www-form-urlencoded\r\n",
                "USER_AGENT: Mozilla/5.0 (compatible; MSIE 6.0; Windows NT 5.1)\r\n",
        'content'=>http_build_query(array_merge(array(
'mode'=>'editevent',
'user'=>$user,
'password'=>$pass,
'itemid'=>$item,
'subject'=> $subj,
'event'=> $body,
'ver'=>'1'
),$opts))
)); $context = stream_context_create($options);
$fp = fopen($flat,'r',false,$context);
$ans = ''; while (!feof($fp)) $ans .= fread($fp, 8192); fclose($fp);
preg_match_all("/([^\n]+)\n([^\n]+)\n/si",$ans,$m); unset($ans); for($i=0;$i<sizeof($m[1]);$i++) $ans[$m[1][$i]]=$m[2][$i];
//print "<hr>".print_r($ans,true);
return($ans);
}


function LJ_get($user,$pass,$item,$flat="http://www.livejournal.com/interface/flat") {
$options = array(
        'http'=>array(
        'method'=>"POST",
        'header'=>
                "Accept-language: ru-ru,ru\r\n",
                "Content-type: application/x-www-form-urlencoded\r\n",
                "USER_AGENT: Mozilla/5.0 (compatible; MSIE 6.0; Windows NT 5.1)\r\n",
        'content'=>http_build_query(array(
'mode'=>'getevents',
'user'=>$user,
'password'=>$pass,
'selecttype'=>'one',
'itemid'=> $item,
'ver'=>'1'
))
)); $context = stream_context_create($options);
$fp = fopen($flat,'r',false,$context);
$ans = ''; while (!feof($fp)) $ans .= fread($fp, 8192); fclose($fp);
preg_match_all("/([^_\n]+)([_\d]*)([^_\n]+)\n([^\n]+)\n/si",$ans,$m); unset($ans); $ans=array();
for($i=0;$i<sizeof($m[1]);$i++) $ans[intval(str_replace("_","",$m[2][$i]))][$m[3][$i]]=urldecode($m[4][$i]);
//print "<hr>".print_r($ans[1],true);
return($ans[1]);
}


function LJ_getlast($user,$pass,$flat="http://www.livejournal.com/interface/flat") {
$options = array(
        'http'=>array(
        'method'=>"POST",
        'header'=>
                "Accept-language: ru-ru,ru\r\n",
                "Content-type: application/x-www-form-urlencoded\r\n",
                "USER_AGENT: Mozilla/5.0 (compatible; MSIE 6.0; Windows NT 5.1)\r\n",
        'content'=>http_build_query(array(
'mode'=>'getevents',
'user'=>$user,
'password'=>$pass,
'selecttype'=>'one',
'itemid'=> '-1',
'ver'=>'1'
))
)); $context = stream_context_create($options);
$fp = fopen($flat,'r',false,$context);
$ans = ''; while (!feof($fp)) $ans .= fread($fp, 8192); fclose($fp);
preg_match_all("/([^_\n]+)([_\d]*)([^_\n]+)\n([^\n]+)\n/si",$ans,$m); unset($ans); $ans=array();
for($i=0;$i<sizeof($m[1]);$i++) $ans[intval(str_replace("_","",$m[2][$i]))][$m[3][$i]]=urldecode($m[4][$i]);
//print "<hr>".print_r($ans[1],true);
return($ans[1]);
}



function LJ_post($user,$pass,$subj,$body,$opts,$flat="http://www.livejournal.com/interface/flat") {
if(gettype($opts)!='array') $opts=array();
$options = array(
        'http'=>array(
        'method'=>"POST",
        'header'=>
                "Accept-language: ru-ru,ru\r\n",
                "Content-type: application/x-www-form-urlencoded\r\n",
                "USER_AGENT: Mozilla/5.0 (compatible; MSIE 6.0; Windows NT 5.1)\r\n",
        'content'=>http_build_query(array_merge(array(
'mode'=>'postevent',
'user'=>$user,
'password'=>$pass,
'subject'=> $subj,
'event'=> $body,
'ver'=>'1',
'year'=>date("Y"),
'mon'=>date("m"),
'day'=>(date("d")),
'hour'=>date("H"),
'min'=>date("i")
),$opts))
)); $context = stream_context_create($options);
$fp = fopen($flat,'r',false,$context);
$ans = ''; while (!feof($fp)) $ans .= fread($fp, 8192); fclose($fp);
preg_match_all("/([^\n]+)\n([^\n]+)\n/si",$ans,$m); unset($ans); for($i=0;$i<sizeof($m[1]);$i++) $ans[$m[1][$i]]=$m[2][$i];
//print "<hr>".print_r($ans,true);
return($ans);
}
?>