<?
if(!$global)
{
	die('And you thought you found something?');
}

$global['microtime'] = microtime();
$global['time'] = time();

session_start();

define('IN_APP', TRUE);

//
// Cookie load
//

if($_COOKIE['LC4Settings'])
{
	$cookie_array = explode(';', $_COOKIE['LC4Settings']);
	
	if($cookie_array[0] && $cookie_array[1] && $cookie_array[2] && $cookie_array[3])
	{
		$_SESSION['name'] = $cookie_array[0];
		$_SESSION['email'] = $cookie_array[1];
		$_SESSION['slang'] = $cookie_array[2];
		$_SESSION['theme'] = $cookie_array[3];
	}
}

if($_COOKIE['LC4Notepad'])
{
	$_SESSION['notepad'] = $_COOKIE['LC4Notepad'];
}

//
// Config load
//

include_once "config/config.php";

//
// Language load
//

include_once "system/l.lang.php";

//
// Module load
//

include_once "system/l.module.php";

//
// Regular Function load
//

include_once "system/f.functions.php";

//
// Site construct
//

include_once "system/l.template.php";

?>