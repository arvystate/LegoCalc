<?
if(!defined('IN_APP'))
{
	die('And you thought you found something?');
}

// Checking modules

$all_modules = scandir($global['module_dir']);
$modules = array();

for($i = 2, $x = 0; $i < count($all_modules); $i++)
{
	if(is_dir($global['module_dir'] . '/' . $all_modules[$i] . '/' . $global['default_lang'])) // Checking if loaded file is a module and has default language file
	{
		if(is_dir($global['module_dir'] . '/' . $all_modules[$i] . '/' . $global['lang'])) // Checking if loaded file has language for loaded language
		{
			include_once $global['module_dir'] . '/' . $all_modules[$i] . '/' . $global['lang'] . '/' . 'system.php';
		}
		else // Loading default language (usually english) as all modules must have default translation, otherwise they're not loaded
		{
			include_once $global['module_dir'] . '/' . $all_modules[$i] . '/' . $global['default_lang'] . '/' . 'system.php';
		}
		
		$modules[$x]->name = $module_name;
		$modules[$x]->reqs = $module_requirements;
		$modules[$x]->games = $module_games;
		$modules[$x]->filename = $all_modules[$i];
		$x++;
	}
}

$module_temp = $modules;
for($i = 0; $i < count($modules); $i++)
{
	$modules[$i] = $module_temp[(count($modules) - 1) - $i];
}
unset($module_temp);
?>