<?
if(!defined('IN_APP'))
{
	die('And you thought you found something?');
}




$lang_dir = scandir($global['language_dir']);

$languages = array();

for($i = 2, $x = 0; $i < count($lang_dir); $i++)
{
	if(is_dir($global['language_dir'] . '/' . $lang_dir[$i]))
	{
		$file = file($global['language_dir'] . '/' . $lang_dir[$i] . '/lang.lpa');
		
		if($file[0])
		{
			$languages[$x]->name = $file[0];
			$languages[$x]->directory = $lang_dir[$i];
			$x++;
		}
		else
		{
			die('<b>' . $lang['fatal_error'] . '</b>: ' . $lang['error2']);
		}
	} 
}

unset($lang_dir);
unset($file);

$global['lang'] = $_SESSION['slang'];



$no_array = 1;
for($i = 0; $i < count($languages); $i++)
{
	if($languages[$i]->directory == $global['lang'])
	{
		$no_array = 0;
	}
}

if( (!$global['lang']) || ($no_array == 1) )
{
	$global['lang'] = $global['default_lang'];
}

unset($no_array);

if(!is_dir($global['language_dir'] . '/' . $global['lang']))
{
	die('<b>Fatal error</b>: Cannot find language file - unable to load');
}

include_once $global['language_dir'] . '/' . $global['lang'] . '/main.php';

foreach($lang['page'] as $key => $value)
{
	if($global['page'] == $key)
	{
		$global['pagename'] = $value . ' (BETA)';
	}
}

if(!$global['pagename'])
{
	$global['pagename'] = $global['page'] . '(BETA)';
}

?>