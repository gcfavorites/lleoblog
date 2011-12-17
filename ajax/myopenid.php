<?php
/**
 * phpMyID - A standalone, single user, OpenID Identity Provider
 *
 * @package phpMyID
 * @author CJ Niemira <siege (at) siege (dot) org>
 * @copyright 2006-2008
 * @license http://www.gnu.org/licenses/gpl.html GNU Public License
 * @url http://siege.org/projects/phpMyID
 * @version 2
 */

//print '############'.md5('test:phpMyID:test');

/**
 * User profile
 * @name $profile
 * @global array $GLOBALS['profile']
 */
$GLOBALS['profile'] = array(
	# Basic Config - Required
        'auth_username' =>      'lleo',
        'auth_password' =>      'ce37989800ec87f824c26b50cf6ad4eb',

	# Optional Config - Please see README before setting these
#	'microid'	=>	array('mailto:user@site', 'http://delegator'),
#	'pavatar'	=>	'http://your.site.com/path/pavatar.img',

	# Advanced Config - Please see README before setting these
#	'allow_gmp'	=>	false,
#	'allow_test'	=> 	false,
#	'allow_suhosin'	=>	false,
#	'auth_realm'	=>	'phpMyID',
#	'force_bigmath'	=>	false,
#	'idp_url'	=>	'http://your.site.com/path/MyID.config.php',
#	'lifetime'	=>	1440,
#	'paranoid'	=>	false, # EXPERIMENTAL

	# Debug Config - Please see README before setting these
#	'debug'		=>	false,
#	'logfile'	=>	'/tmp/phpMyID.debug.log',
);

/**
 * Simple Registration Extension
 * @name $sreg
 * @global array $GLOBALS['sreg']
 */
$GLOBALS['sreg'] = array (
	'nickname'		=> 'LLeo',
	'email'			=> 'lleo@aha.ru',
	'fullname'		=> 'Leonid Kaganov',
	'dob'			=> '1972-05-21',
	'gender'		=> 'M',
#	'postcode'		=> '22000',
	'country'		=> 'RU',
	'language'		=> 'ru',
	'timezone'		=> 'Europa/Moscow'
);

require('../include_sys/MyID.php');
?>