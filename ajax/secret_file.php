<?php
// это процедура не аякса, я просто не нашел для него лучшего места
//
// эта штука выдает секретные файлы (работает с модулем {_SECRET_FILE: link _})
//
// файлы секретны тем, что:
// а) по прямой ссылке их открыть невозможно
// (для этого разместите их в папке, где создайте файл .htaccess, куда наколотите строчку ереси типа 'trololololo')
// б) ссылку на такой файл постороннему лицу переслать не получится - она зависит от IP и Браузера

include "../config.php";
include_once $GLOBALS['include_sys']."_files.php"; // операции с файлами

$file=$_GET['file'];

if( $_GET['o'] != md5($hashinput.$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'].$file) ) die('Error 404: ахуюшки');

Exit_SendFILE(realpath($filehost.$file));

?>
