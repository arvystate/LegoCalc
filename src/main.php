<?

if(isset($_GET['module']))
{
	$mod_nr = $_GET['module'];
	$global['page'] = 'Module ' . $mod_nr;
}

if(isset($_POST['module_nr']))
{
	$mod_nr = $_POST['module_nr'];
	$global['page'] = 'Module ' . $mod_nr;
}

if(!$global['page'])
{
	$global['page'] = 'main';
}

if(isset($_POST['mainmenu_submit']))
{
	header("Location: main.php");
}

include "system/core.php";

if(isset($_POST['login_submit']))
{
	$_SESSION['name'] = $_POST['login_user'];
	$_SESSION['email'] = $_POST['login_email'];
	$_SESSION['slang'] = $_POST['login_lang'];
	$_SESSION['theme'] = $_POST['login_theme'];
	
	if( ($_POST['login_save'] == 1) && (!strpos($_POST['login_user'], 'guest')) && (preg_match("/^.+?@.+?\..+?/i", $_SESSION['email'])) )
	{
		$m_explode = explode('@', $_SESSION['email']);
				
		if(!strpos($m_explode[0], 'guest'))
		{
			$cookie_string = $_SESSION['name'] . ';' . $_SESSION['email'] . ';' . $_SESSION['slang'] . ';' . $_SESSION['theme'];
			setcookie("LC4Notepad", " ",  time() + 31536000, "/calc2/", "www.arvystate.net");
			setcookie("LC4Settings", $cookie_string, time() + 31536000, "/calc2/", "www.arvystate.net");
		}
	}
	header("Location: main.php");
}

if(isset($_POST['logout_submit']))
{
	unset($_SESSION['name']);
	unset($_SESSION['email']);
	unset($_SESSION['slang']);
	unset($_SESSION['theme']);
	
	if($_COOKIE['LC4Settings'])
	{
		setcookie("LC4Settings", "", time() - 3600);
	}
	
	if($_COOKIE['LC4Notepad'])
	{
		setcookie("LC4Notepad", "", time() - 3600);
	}
	
	header("Location: index.php");
}

if(isset($_POST['notepad_save']))
{
	if($_POST['notepad'] == '')
	{
		$_POST['notepad'] = ' ';
	}
	
	if($_COOKIE['LC4Notepad'])
	{
		setcookie("LC4Notepad", $_POST['notepad'],  time() + 31536000, "/calc2/", "www.arvystate.net");
	}
	
	$_SESSION['notepad'] = $_POST['notepad'];

	if($mod_nr)
	{
		header("Location: main.php?module=" . $mod_nr);
	}
	else
	{
		header("Location: main.php");
	}
}

if(isset($_POST['notepad_clr']))
{
	if($_COOKIE['LC4Notepad'])
	{
		setcookie("LC4Notepad", " ",  time() + 31536000, "/calc2/", "www.arvystate.net");
	}
	
	$_SESSION['notepad'] = ' ';

	if($mod_nr)
	{
		header("Location: main.php?module=" . $mod_nr);
	}
	else
	{
		header("Location: main.php");
	}
}

if($_SESSION['name'] == '' || !preg_match("/^.+?@.+?\..+?/i", $_SESSION['email']))
{
	header("Location: index.php");
}


d_header();

$show_modules = 1;

if(array_key_exists($mod_nr, $modules))
{
	$show_modules = 0;
	
	$games = 0;
	
	if($modules[$mod_nr]->games != $lang['all'])
	{
		$game_load = explode(',', $modules[$mod_nr]->games);
		
		for($i = 0; $i < count($game_load); $i++)
		{
			if(is_dir('games/' . $game_load[$i]))
			{
				include_once('games/' . $game_load[$i] . '/array.php');
			}
		}
	}
	
	$load_dir = 'modules' . '/' . $modules[$mod_nr]->filename;
	
	include_once $load_dir . '/functions.php';
	
	$module = new calc_module;
	
	echo $module->construct_input($mod_nr, $_POST, $lang, $load_dir, $global['functions'], $games);
	
	if(isset($_POST['module_nr']))
	{
		echo '<div class="module-retdata">' . $lang['returned_data'] . ':</div><br />';
		echo $module->calculate();		
	}
}

if($show_modules == 1)
{
?>
<table cellpadding="0" cellspacing="2" border="0" width="100%">
<tbody>
<tr align="center">
	<td><h3><?=$lang['loaded_modules']?></h3></td>
</tr>
<tr>
	<td class="module-top">&nbsp;</td>
</tr>
<?
for($i = count($modules) - 1; $i >= 0; $i--)
{
?>
<tr align="center">
	<td class="module-bottom"><a href="main.php?module=<?=$i?>"><b><?=$modules[$i]->name?></b></a><br />
	<div class="module-req">
	<?=$lang['required']?>: <?=$modules[$i]->reqs?><br />
	<?=$lang['avail_for_games']?>: <?=$modules[$i]->games?>
	</div>
	</td>
</tr>
<?
}
?>
</tbody>
</table>
<?
}

d_footer();
?>