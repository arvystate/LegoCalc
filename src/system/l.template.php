<?
if(!defined('IN_APP'))
{
	die('And you thought you found something?');
}

// Template load

function d_header()
{
	global $temp;
	for($i = 0; $i < count($temp['header']); $i++)
	{
		echo $temp['header'][$i];
	}
}

function d_footer()
{
	global $global, $temp;
	
	for($i = 0; $i < count($temp['sidebar']); $i++)
	{
		echo $temp['sidebar'][$i];
	}
		
	for($i = 0; $i < count($temp['footer']); $i++)
	{
		$temp['footer'][$i] = str_replace('{LC_VERSION}', $global['version'], $temp['footer'][$i]);
		$generation_time = microtime() - $global['microtime'];
		$temp['footer'][$i] = str_replace('{LC_GENERATION}', $generation_time, $temp['footer'][$i]);
		$temp['footer'][$i] = str_replace('{LC_MYSQL}', $global['mysql'], $temp['footer'][$i]);
		
		echo $temp['footer'][$i];
	}
}


$template_dir = scandir($global['template_dir']);

$templates = array();

for($i = 2, $x = 0; $i < count($template_dir); $i++)
{
	if(is_dir($global['template_dir'] . '/' . $template_dir[$i]))
	{
		$templates[$x] = $template_dir[$i];
		$x++;
	} 
}

unset($template_dir);

$global['template'] = $_SESSION['theme'];

if(!$global['template'] || !in_array($global['template'], $templates) )
{
	$global['template'] = $global['default_template'];
}

$global['template_dir'] .= '/' . $global['template'];

if(!is_dir($global['template_dir']))
{
	die('<b>' . $lang['fatal_error'] . '</b>: ' . $lang['error1']);
}

$file = file($global['template_dir'] . '/' . 'header.lpa');

for($i = 0; $i < count($file); $i++)
{
	$file[$i] = str_replace('{LC_LANG}', $lang['xhtml_lang'], $file[$i]);
	$file[$i] = str_replace('{LC_CHARSET}', $lang['encoding'], $file[$i]);
	$file[$i] = str_replace('{LC_APPNAME}', $global['app_name'], $file[$i]);
	$file[$i] = str_replace('{LC_PAGENAME}', $global['pagename'], $file[$i]);
}

$temp['header'] = $file;

if($_SESSION['name'] && $_SESSION['email'] && preg_match("/^.+?@.+?\..+?/i",$_SESSION['email']))
{
	$global['logined'] = TRUE;
	$file = file($global['template_dir'] . '/' . 'sidebar_login.lpa');
}
else
{
	$file = file($global['template_dir'] . '/' . 'sidebar_logout.lpa');
}

unset($string);

if($global['logined'])
{
	$fl = file($global['template_dir'] . '/' . 'list.lpa');

	$string = '';
	$i = 0;
	$module_count = count($modules);	
	
	while( ($i < count($fl)) && ($module_count > 0) )
	{
		if(strpos($fl[$i], '{LC_LIST_FIRST}'))
		{
			$module_count--;
			$string .= str_replace('{LC_LIST_FIRST}', '<a href="main.php?module=' . $module_count . '">' . $modules[$module_count]->name . '</a>', $fl[$i]);
			$i++;
			continue;
		}
		
		if(strpos($fl[$i], '{LC_LIST_MIDDLE}'))
		{
			$module_count--;
			$string .= str_replace('{LC_LIST_MIDDLE}', '<a href="main.php?module=' . $module_count . '">' . $modules[$module_count]->name . '</a>', $fl[$i]);
			
			if($module_count == 1)
			{
				$i++;
			}
			continue;
		}
		
		if(strpos($fl[$i], '{LC_LIST_LAST}'))
		{
			$module_count--;
			$string .= str_replace('{LC_LIST_LAST}', '<a href="main.php?module=' . $module_count . '">' . $modules[$module_count]->name . '</a>', $fl[$i]);
			$i++;
			continue;
		}
		
		$string .= $fl[$i];
		$i++;
	}
	
	while($i < count($fl))
	{
		if(!strpos($fl[$i], '{LC_LIST_LAST}') && !strpos($fl[$i], '{LC_LIST_MIDDLE}') && !strpos($fl[$i], '{LC_LIST_FIRST}'))
		{
				$string .= $fl[$i];
		}
		$i++;
	}
}

for($i = 0; $i < count($file); $i++)
{
	$file[$i] = str_replace('{LC_LIST_LOGIN}', $lang['login'], $file[$i]);
	$file[$i] = str_replace('{LC_LOGIN_USER}', $lang['user'], $file[$i]);
	$file[$i] = str_replace('{LC_LOGIN_EMAIL}', $lang['email'], $file[$i]);
	$file[$i] = str_replace('{LC_LOGIN_LANG}', $lang['lang'], $file[$i]);
	$file[$i] = str_replace('{LC_LOGIN_ENTER}', $lang['enter'], $file[$i]);
	$file[$i] = str_replace('{LC_LOGIN_THEME}', $lang['theme'], $file[$i]);
	$file[$i] = str_replace('{LC_LOGIN_TIME}', $lang['time'], $file[$i]);
	$file[$i] = str_replace('{LC_LOGIN_SAVEQ}', $lang['save_settings'], $file[$i]);
	
	$file[$i] = str_replace('{LC_NOTEPAD}', $lang['notepad'], $file[$i]);
	$file[$i] = str_replace('{LC_NOTEPAD_TEXT}', $_SESSION['notepad'], $file[$i]);
	$file[$i] = str_replace('{LC_NOTEPAD_CLEAR}', $lang['notepad_clear'], $file[$i]);
	$file[$i] = str_replace('{LC_NOTEPAD_SAVE}', $lang['notepad_save'], $file[$i]);	
	
	$file[$i] = str_replace('{LC_CURRENT_USER}', $_SESSION['name'], $file[$i]);
	$file[$i] = str_replace('{LC_CURRENT_EMAIL}', $_SESSION['email'], $file[$i]);
	$file[$i] = str_replace('{LC_CURRENT_LANG}', $lang['fullname'], $file[$i]);
	$file[$i] = str_replace('{LC_CURRENT_THEME}', $global['template'], $file[$i]);
	$file[$i] = str_replace('{LC_CURRENT_TIME}', date("H:i:s", $global['time']) . '<br />' . date("j. ", $global['time']) . $lang['month'][date("n", $global['time'])] . date(" Y", $global['time']), $file[$i]);
	
	$file[$i] = str_replace('{LC_LIST_NAME}', $lang['main_menu'], $file[$i]);
	$file[$i] = str_replace('{LC_SYS_INFO}', $lang['sys_info'], $file[$i]);
	$file[$i] = str_replace('{LC_LOGOUT}', $lang['logout'], $file[$i]);
	$file[$i] = str_replace('{LC_LOADED_MODULES}', $lang['loaded_modules'], $file[$i]);
	$file[$i] = str_replace('{LC_MODULES_LIST}', $string, $file[$i]);
	$file[$i] = str_replace('{LC_MODULE_NR}', $mod_nr, $file[$i]);
	
	$txt = '';
	unset($rep);
	unset($rep2);
	if(strpos($file[$i], '{LC_LANG_OPTION}'))
	{
		for($x = 0; $x < count($languages); $x++)
		{
			$rep = array('{LC_LANG_OPTION}', '{LC_LANG_FULLNAME}');
			$rep2 = array($languages[$x]->directory, $languages[$x]->name);
			$txt .= str_replace($rep, $rep2, $file[$i]);
		}
		$file[$i] = $txt;
	}
	
	$txt = '';
	unset($rep);
	if(strpos($file[$i], '{LC_THEME_OPTION}'))
	{
		for($x = 0; $x < count($templates); $x++)
		{
			$rep = array('{LC_THEME_OPTION}', '{LC_THEME_NAME}');
			$txt .= str_replace($rep, $templates[$x], $file[$i]);
		}
		$file[$i] = $txt;
	}
}

$temp['sidebar'] = $file;

$file = file($global['template_dir'] . '/' . 'footer.lpa');

for($i = 0; $i < count($file); $i++)
{
	$file[$i] = str_replace('{LC_DISC_NAME}', $lang['disc_name'], $file[$i]);
	$file[$i] = str_replace('{LC_DISCLAIMER}', $lang['disclaimer'], $file[$i]);
	$file[$i] = str_replace('{LC_INDEX}', $lang['page']['home'], $file[$i]);
	$file[$i] = str_replace('{LC_MAIN}', $lang['page']['main'], $file[$i]);
	$file[$i] = str_replace('{LC_HISTORY}', $lang['page']['history'], $file[$i]);
	$file[$i] = str_replace('{LC_FAQ}', $lang['page']['faq'], $file[$i]);
}

$temp['footer'] = $file;

unset($file);
?>