<?php // тестовое решение

include "../config.php";
require_once $include_sys."JsHttpRequest.php"; $JsHttpRequest =& new JsHttpRequest($wwwcharset);
include $include_sys."_autorize.php"; // сперва JsHttpRequest, затем autorize

//=================================================================================================
if(isset($_REQUEST['onload'])) otprav(''); // все дальнейшие опции будут запрещены для GET-запроса
//=================================================================================================

// "majax('module.php',{mod:'YANDEXCOUNT',a:'update',num:'{num}'})\">"

$mod=preg_replace("/[^a-z]/si",'',$_REQUEST['mod']);

$file=$site_mod.$mod.'.php'; if(!file_exists($file)) {
	$file=$site_module.$mod.'.php'; if(!file_exists($file)) idie("Module not found: $mod");
} include_once($file);
if(!function_exists($mod.'_ajax')) idie("Function not found: ".$mod."_ajax");
otprav(call_user_func($mod.'_ajax'));

?>
