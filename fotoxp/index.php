<?php
/*
	XPPubWiz.php
		http://www.strehle.de/tim/xppubwiz/

	Sample PHP backend for the Microsoft Windows XP Publishing Wizard

	This standalone PHP script provides a complete backend for the Microsoft Windows XP Publishing Wizard,
	a nice tool for file uploads to any HTTP server providing such a backend.

	Requirements:
	- Any web server running PHP 4.1 or greater (with session support)
	- Clients running Microsoft Windows XP

	Getting started:
	- Copy this script anwhere on your web server.
	- Change the strings in the "General configuration" section below (optional).
	- Change the user account and directory information below (recommended).
	- Point your web browser to the URL you copied this script to, and add this querystring:
		?step=reg
	- A file download (xppubwiz.reg) will start.
	- Save the file on your harddisk and double-click it to register your server with the Publishing Wizard.
	- In the Windows Explorer, select some files and click "Publish [...] on the web" in the Windows XP task pane.
	- After confirming your file selection, your server will show up in the list of services. Go ...

	Authors:
		Tim Strehle <tim@digicol.de>
		Andr� Basse <andre@digicol.de>

	Version: 1.0b

	CVS Version: $Id: xppubwiz.php,v 1.8 2003/05/30 09:31:13 tim Exp $

	$Log: xppubwiz.php,v $

	Revision 1.8  2003/05/30 09:31:13  tim
	Fixed non-escaped backslashes in JavaScript manifest variable:
	Christian Walczyk found out that file names beginning with "u" or "x" produced
	a JavaScript error (because \u and \x mean something special).

	Revision 1.7  2003/03/14 08:40:06  tim
	Bug fixes for register_globals = off and magic_quotes_gpc = on.
	

	Based on + inspired by the Gallery (http://gallery.menalto.com/) XP Publishing Wizard implementation,
	written by
		Demian Johnston
		Bharat Mediratta

	=====================================================================
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or (at
	your option) any later version.

	This program is distributed in the hope that it will be useful, but
	WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
	General Public License for more details.
	=====================================================================

	Technical information can be found here:
		http://msdn.microsoft.com/library/default.asp?url=/library/en-us/shellcc/platform/shell/programmersguide/shell_basics/shell_basics_extending/publishing_wizard/pubwiz_intro.asp
		http://www.zonageek.com/code/misc/wizards/
*/

require ('../config.php');
include "../include_sys/_autorize.php";
include "../include_sys/_msq.php";
error_reporting(-1);
// General configuration

$protocol = 'http';
if (isset($_SERVER[ 'HTTPS' ]))
  if ($_SERVER[ 'HTTPS' ] == 'on')
	$protocol .= 's';

$cfg = array(
	'wizardheadline'    => $admin_name,
	'wizardbyline'      => $blog_name,
	'finalurl'          => $protocol . '://' . $_SERVER[ 'HTTP_HOST' ] . "${wwwhost}editor/",
	'registrykey'       => strtr($_SERVER[ 'HTTP_HOST' ], '.:', '__'),
	'wizardname'        => $_SERVER[ 'HTTP_HOST' ],
	'wizarddescription' => iconv('utf-8','cp1251',"Опубликовать фотографии на $blog_name"),
	);

// Make photo directory structure
date_default_timezone_set("Europe/Moscow");
$todaydir=date('o.m.d');
//Get photo path from config
$photopath="../$foto_small";
#$photopath=$host.$blogdir.$foto_small;
//Scan directory
$dirlist["$photopath"]=iconv('utf-8','cp1251',"[КОРЕНЬ ФОТО]");
$dirlist["$photopath/$todaydir"]="$todaydir";
$dirlist=$dirlist+GetDirectory ($photopath,'');

// User + target directory configuration
#echo $host.$blogdir.$foto_small; exit;
$users = array(

	'admin' => array(
		'password' => 'test',
		'dirs' => $dirlist
		)

	);


// Determine page/step to display, as this script contains a four-step wizard:
// "login", "options", "check", "upload" (+ special "reg" mode, see below)

$allsteps = array( 'login', 'options', 'check', 'upload', 'reg' );

$step = 'login';

if (isset($_REQUEST[ 'step' ]))
  if (in_array($_REQUEST[ 'step' ], $allsteps))
	$step = $_REQUEST[ 'step' ];


// Special registry file download mode:
// Call this script in your browser and set ?step=reg to download a .reg file for registering
// your server with the Windows XP Publishing Wizard

if ($step == 'reg')
  { header('Content-Type: application/octet-stream; name="xppubwiz.reg"');
	header('Content-disposition: attachment; filename="xppubwiz.reg"');

	echo
		'Windows Registry Editor Version 5.00' . "\n\n" .
		'[HKEY_CURRENT_USER\\Software\\Microsoft\\Windows\\CurrentVersion\\Explorer\\PublishingWizard\\PublishingWizard\\Providers\\' . $cfg[ 'registrykey' ] . ']' . "\n" .
		'"displayname"="' . $cfg[ 'wizardname' ] . '"' . "\n" .
		'"description"="' . $cfg[ 'wizarddescription' ] . '"' . "\n" .
		'"href"="' . $protocol . '://' . $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'PHP_SELF' ] . '"' . "\n" .
		'"icon"="' . $protocol . '://' . $_SERVER[ 'HTTP_HOST' ] . dirname($_SERVER[ 'PHP_SELF' ]) . '/favicon.ico"';

	exit;
  }


// Send no-cache headers

header('Expires: Mon, 26 Jul 2002 05:00:00 GMT');              // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: no-cache="set-cookie", private');       // HTTP/1.1
header('Pragma: no-cache');                                    // HTTP/1.0


// Start session

session_name('phpxppubwiz');
@session_start();

if (! isset($_SESSION[ 'authuser' ]))
  $_SESSION[ 'authuser' ] = '';


// Send character set header

header('Content-Type: text/html; charset=windows-1251');


// Set maximum execution time to unlimited to allow large file uploads

set_time_limit(0);

?>
<html>
<head>
<title>XP Publishing Wizard Server Script</title>
<style type="text/css">

body,a,p,span,td,th,input,select,textarea {
	font-family:verdana,arial,helvetica,geneva,sans-serif,serif;
	font-size:10px;
}

</style>
</head>
<body>
<?php

// Variables for the XP wizard buttons

$WIZARD_BUTTONS = 'false,true,false';
$ONBACK_SCRIPT  = '';
$ONNEXT_SCRIPT  = '';


// Authenticate

if (isset($_REQUEST[ 'user' ]) && isset($_REQUEST[ 'password' ]))
  if (isset($users[ $_REQUEST[ 'user' ] ]))
//	if ($_REQUEST[ 'password' ] == $users[ $_REQUEST[ 'user' ] ][ 'password' ])
//	  $_SESSION[ 'authuser' ] = $_REQUEST[ 'user' ];
	if ( md5($_REQUEST[ 'password' ].$koldunstvo) == $admin_hash)  
	  $_SESSION[ 'authuser' ] = $_REQUEST[ 'user' ];


// Check page/step

if ($_SESSION[ 'authuser' ] == '')
  $step = 'login';
elseif ($step == 'login')
  $step = 'options';

if ($step == 'check')
  if (! (isset($_REQUEST[ 'manifest' ]) && isset($_REQUEST[ 'dir' ])))
	$step = 'options';

if ($step == 'check')
  if (($_REQUEST[ 'manifest' ] == '') || ($_REQUEST[ 'dir' ] == ''))
	$step = 'options';

if ($step == 'check')
  if (! isset($users[ $_SESSION[ 'authuser' ] ][ 'dirs' ][ $_REQUEST[ 'dir' ] ]))
	$step = 'options';


// Step 1: Display login form

if ($step == 'login')
  { ?>

	<form method="post" id="login" action="<?php echo $_SERVER[ 'PHP_SELF' ]; ?>">

	<center>

	<h3><? echo iconv('utf8','cp1251',"Введите пароль администратора блога ").$blog_name;?></h3>

	<table border="0">
	<tr>
		<td></td>
		<td><input type="hidden" name="user" value="admin" /></td>
	</tr>
	<tr>
		<td><? echo iconv('utf8','cp1251',"Пароль:");?></td>
		<td><input type="password" name="password" value="" /></td>
	</tr>
	</table>


	</center>

	<input type="hidden" name="step" value="options" />
        
	</form>

	<?php

	$ONNEXT_SCRIPT  = 'login.submit();';
	$ONBACK_SCRIPT  = 'window.external.FinalBack();';
	$WIZARD_BUTTONS = 'true,true,false';
  }


// Step 2: Display options form (directory choosing)

if ($step == "options")
  { ?>

	<form method="post" id="options" action="<?php echo $_SERVER[ 'PHP_SELF' ]; ?>">

	<center>

	<h3><? echo iconv('utf-8','cp1251',"Выберите раздел для загрузки");?></h3>

	<select id="dir" name="dir" size="10" width="40">
	<?php
	foreach ($users[ $_SESSION[ 'authuser' ] ][ 'dirs' ] as $path => $label){
	  if ($label==$todaydir) {$option_tag='<option selected value="';} else {$option_tag='<option value="';};
	  echo $option_tag . $path . '">' . htmlspecialchars($label) . '</option>';
	};
	?>
	</select>

	</center>

	<input type="hidden" name="step" value="check" />
	<input type="hidden" name="manifest" value="" />

	<script>

	function docheck()
	{ var xml = window.external.Property('TransferManifest');
	  options.manifest.value = xml.xml;
	  options.submit();
	}

	</script>

	</form>

	<?php

   $ONNEXT_SCRIPT  = "docheck();";
   $WIZARD_BUTTONS = "false,true,false";
  }

?>

<div id="content"/>

</div>

<?php

// Step 3: Check file list + selected options, prepare file upload

if ($step == "check")
  { /* Now we're embedding the HREFs to POST to into the transfer manifest.

	The original manifest sent by Windows XP looks like this:

	<transfermanifest>
		<filelist>
			<file id="0" source="C:\pic1.jpg" extension=".jpg" contenttype="image/jpeg" destination="pic1.jpg" size="530363">
				<metadata>
					<imageproperty id="cx">1624</imageproperty>
					<imageproperty id="cy">2544</imageproperty>
				</metadata>
			</file>
			<file id="1" source="C:\pic2.jpg" extension=".jpg" contenttype="image/jpeg" destination="pic2.jpg" size="587275">
				<metadata>
					<imageproperty id="cx">1960</imageproperty>
					<imageproperty id="cy">3008</imageproperty>
				</metadata>
			</file>
		</filelist>
	</transfermanifest>

	We will add a <post> child to each <file> section, and an <uploadinfo> child to the root element.
	*/

	// stripslashes if the evil "magic_quotes_gpc" are "on" (hint by Juan Valdez <juanvaldez123@hotmail.com>)

	if (ini_get('magic_quotes_gpc') == '1')
	  $manifest = stripslashes($_REQUEST[ 'manifest' ]);
	else
	  $manifest = $_REQUEST[ 'manifest' ];

	$parser = xml_parser_create();

	xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
	xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);

	$xml_ok = xml_parse_into_struct($parser, $manifest, $tags, $index);

	$manifest = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" ?>";

	foreach ($tags as $i => $tag)
	  { if (($tag[ 'type' ] == 'open') || ($tag[ 'type' ] == 'complete'))
		  { if ($tag[ 'tag' ] == 'file')
			  $filedata = array(
				'id'                => -1,
				'source'            => '',
				'extension'         => '',
				'contenttype'       => '',
				'destination'       => '',
				'size'              => -1,
				'imageproperty_cx'  => -1,
				'imageproperty_cy'  => -1
				);

			$manifest .= '<' . $tag[ 'tag' ];

			if (isset($tag[ 'attributes' ]))
			  foreach ($tag[ 'attributes' ] as $key => $value)
				{ $manifest .= ' ' . $key . '="' . $value . '"';

				  if ($tag[ 'tag' ] == 'file')
					$filedata[ $key ] = $value;
				}

			if (($tag[ 'type' ] == 'complete') && (! isset($tag[ 'value' ])))
			  $manifest .= '/';

			$manifest .= '>';

			if (isset($tag[ 'value' ]))
			  { $manifest .= htmlspecialchars($tag[ 'value' ]);

				if ($tag[ 'type' ] == 'complete')
				  $manifest .= '</' . $tag[ 'tag' ] . '>';

				if (($tag[ 'tag' ] == 'imageproperty') && isset($tag[ 'attributes' ]))
				  if (isset($tag[ 'attributes' ][ 'id' ]))
					$filedata[ 'imageproperty_' . $tag[ 'attributes' ][ 'id' ] ] = $tag[ 'value' ];
			  }
		  }
		elseif ($tag[ 'type' ] == 'close')
		  { if ($tag[ 'tag' ] == 'file')
			  { $protocol = 'http';
				if (isset($_SERVER[ 'HTTPS' ]))
				  if ($_SERVER[ 'HTTPS' ] == 'on')
					$protocol .= 's';

				$manifest .= 
					'<post href="' . $protocol . '://' . $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'PHP_SELF' ] . '" name="userfile">' .
					'	<formdata name="MAX_FILE_SIZE">10000000</formdata>' .
					'	<formdata name="step">upload</formdata>' .
					'	<formdata name="todir">' . htmlspecialchars($_REQUEST[ 'dir' ]) . '</formdata>';

				foreach ($filedata as $key => $value)
				  $manifest .= '<formdata name="' . $key . '">' . htmlspecialchars($value) . '</formdata>';

				$manifest .= '</post>';
			  }
			elseif ($tag[ 'level' ] == 1)
			  $manifest .= '<uploadinfo><htmlui href="' . $cfg[ 'finalurl' ] . '"/></uploadinfo>';

			$manifest .= '</' . $tag[ 'tag' ] . '>';
		  }
	  }

	// Check whether we created well-formed XML ...

	if (xml_parse_into_struct($parser,$manifest,$tags,$index) >= 0)
	  { ?>

		<script>

		var newxml = '<?php echo str_replace('\\', '\\\\', $manifest); ?>';
		var manxml = window.external.Property('TransferManifest');

		manxml.loadXML(newxml);

		window.external.Property('TransferManifest') = manxml;
		window.external.SetWizardButtons(true,true,true);

		content.innerHtml = manxml;
		window.external.FinalNext();

		</script>

		<?php
	  }
  }


// Step 4: This page will be called once for every file upload

if ($step == 'upload')
  { if (isset($_FILES) && isset($_REQUEST[ 'todir' ]) && isset($_REQUEST[ 'destination' ]))
	  if (isset($_FILES[ 'userfile' ]) && ($_REQUEST[ 'todir' ] != '') && ($_REQUEST[ 'destination' ] != ''))
		if (isset($users[ $_SESSION[ 'authuser' ] ][ 'dirs' ][ $_REQUEST[ 'todir' ] ]))
//		  if (file_exists($_REQUEST[ 'todir' ]))
//			if (is_dir($_REQUEST[ 'todir' ]))
			  { $filename = $_REQUEST[ 'todir' ] . '/' . $_REQUEST[ 'destination' ];
				if (! file_exists($filename)){
				  $path_parts = pathinfo($filename);
				  $dirname=$path_parts['dirname'];
				  if (! file_exists($dirname)){ mkdir ($dirname) ;};
				  $dirname_pre=$photopath."/pre/".str_replace($photopath,'',$dirname);
				  if (! file_exists($dirname_pre)){ mkdir ($dirname_pre) ;};
				  $fname=str_replace($photopath."/",'',$filename);

  				obrajpeg($_FILES[ 'userfile' ][ 'tmp_name' ],$foto_file_small.$fname,$foto_res_small,$foto_qality_small,$foto_logo);
	                        obrajpeg($foto_file_small.$fname,$foto_file_preview.$fname,$foto_res_preview,$foto_qality_preview);
	        			
//				  move_uploaded_file($_FILES[ 'userfile' ][ 'tmp_name' ], $filename);
//			          mysql_connect($msq_host,$msq_login,$msq_pass);
//			          mysql_select_db($msq_basa);
//				  $query="INSERT INTO site VALUES ('','all','$fname','photo','','')";
//				  mysql_query($query);
				 msq_add($db_site,array(
		                 'name'=>$fname,
                                 'text'=>$fname,
                                 'type'=>'photo',
                                 'Access'=>'all',
                                 ));
                                                                         
				  };
			  }
  }


?>

<script>

function OnBack()
{ <?php echo $ONBACK_SCRIPT; ?>
}

function OnNext()
{ <?php echo $ONNEXT_SCRIPT; ?>
}

function OnCancel()
{ // Don't know what this is good for:
  content.innerHtml+='<br>OnCancel';
}

function window.onload()
{ window.external.SetHeaderText("<?php echo strtr($cfg[ 'wizardheadline' ], '"', "'"); ?>","<?php echo strtr($cfg[ 'wizardbyline' ], '"', "'"); ?>");
  window.external.SetWizardButtons(<?php echo $WIZARD_BUTTONS; ?>);
}

</script>

</body>
</html>
<?
exit;
function getDirectory( $path = '.', $level = 0 ){
    $dirlist=array();

    $ignore = array( '.svn', '.', '..' );
    // Directories to ignore when listing output. Many hosts
    // will deny PHP access to the cgi-bin.

    $dh = @opendir( $path );
    // Open the directory to the handle $dh

    while( false !== ( $file = readdir( $dh ) ) ){
    // Loop through the directory

        if( !in_array( $file, $ignore ) ){
        // Check that this file is not to be ignored

            $spaces = str_repeat( '---', ( $level * 4 ) );
            // Just to add spacing to the list, to better
            // show the directory tree.

            if( is_dir( "$path/$file" ) ){
            // Its a directory, so we need to keep reading down...
                $dirlist["$path/$file"]="$spaces $file";
                #echo "<strong>$spaces $file</strong><br />";
                $dirlist=$dirlist + getDirectory( "$path/$file", ($level+1) );
                // Re-call this same function but on a new directory.
                // this is what makes function recursive.

            } else {


            }

        }

    }
    closedir( $dh );
    // Close the directory handle

    return $dirlist;
}

function obrajpeg($from,$to,$h=150,$q=80,$s,$r=10) { // set_time_limit(0);
        $img1=ImageCreateFromJpeg($from); $W=ImagesX($img1); $H=ImagesY($img1);
        if($h<$H) { $w=$W/($H/$h);
                $img2=ImageCreateTrueColor($w,$h);
                ImageCopyResampled($img2,$img1,0,0,0,0,$w,$h,$W,$H);
        } else { $h=$H; $w=$W; $img2=$img1; }


        ImageJpeg($img2, $to, $q);
        ImageDestroy($img2);
        ImageDestroy($img1);
}

?>
