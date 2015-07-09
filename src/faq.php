<?
$global['page'] = 'faq';

include "system/core.php";

if(is_file($global['language_dir'] . '/' . $global['lang'] . '/faq.php'))
{
	include_once $global['language_dir'] . '/' . $global['lang'] . '/faq.php';
}
else
{
	include_once $global['language_dir'] . '/' . $global['default_lang'] . '/faq.php';
}

d_header();
?>
<div id="faq">
<h3><?=$lang['faq_top']?></h3>
<br />
<?
$all_c = 0;
for($i = 0; $i < count($faq); $i++)
{
	echo '<a href="#' . $all_c . '"><b>' . $faq[$i]['name'] . '</b></a><br />';
	$all_c++;
	
	if($faq[$i]['name'] == 'Modules')
	{
		$module_faq = array();
		for($x = 0; $x < count($modules); $x++)
		{
			echo '<span class="faq-module-name">' . $modules[$x]->name . '</span><br />';
			
			echo '<ul>';
						
			if(is_file($global['module_dir'] . '/' . $modules[$x]->filename . '/' . $global['lang'] . '/faq.php'))
			{
				include_once $global['module_dir'] . '/' . $modules[$x]->filename . '/' . $global['lang'] . '/' . 'faq.php';
			}
			elseif(is_file($global['module_dir'] . '/' . $modules[$x]->filename . '/' . $global['default_lang'] . '/faq.php'))
			{
				include_once $global['module_dir'] . '/' . $modules[$x]->filename . '/' . $global['default_lang'] . '/' . 'faq.php';
			}
			else
			{
				echo '<li>' . $lang['no_faq'] . '</li>';
				echo '</ul>';
				continue;
			}
			
			$module_faq[$x] = $mod_faq;
			

			
			for($y = 0; $y < count($module_faq[$x]['question']); $y++)
			{
				echo '<li>&bull; <a href="#' . $all_c . '">' . $module_faq[$x]['question'][$y] . '</a></li>';
				$all_c++;
			}
			echo '</ul>';
		}
		continue;
	}
	
	echo '<ul>';
	
	for($z = 0; $z < count($faq[$i]['question']); $z++)
	{
		echo '<li>&bull; <a href="#' . $all_c . '">' . $faq[$i]['question'][$z] . '</a></li>';
		$all_c++;
	}
	
	if(count($faq[$i]['question']) == 0)
	{
		echo '<li>' . $lang['no_faq'] . '</li>';
	}
	
	echo '</ul>';
}
?>
<table cellpadding="0" cellspacing="2" border="0" width="100%">
<tbody>
<tr>
	<td class="faq-top">&nbsp;</td>
</tr>
<?
$all_c = 0;
for($i = 0; $i < count($faq); $i++)
{
?>
<tr>
	<td class="faq-name">
	<a name="<?=$i?>"><?=$faq[$i]['name']?></a><br />
	</td>
</tr>
<?
	$all_c++;
	
	if($faq[$i]['name'] == 'Modules')
	{
		for($x = 0; $x < count($modules); $x++)
		{
			echo '<tr><td class="faq-module">' . $modules[$x]->name . '</td></tr>';

			if(!$module_faq[$x])
			{
				echo '<tr><td class="faq-empty">' . $lang['no_faq'] . '</td></tr>';
				continue;
			}			

			for($y = 0; $y < count($module_faq[$x]['question']); $y++)
			{
				echo '<tr><td class="faq-q-a"><a name="' . $all_c . '">' . $module_faq[$x]['question'][$y] . '</a><br />' . $module_faq[$x]['answer'][$y] . '</td></tr>';
				$all_c++;
			}
		}
		continue;
	}
	
	for($z = 0; $z < count($faq[$i]['question']); $z++)
	{
		echo '<tr><td class="faq-q-a"><a name="' . $all_c . '">' . $faq[$i]['question'][$z] . '</a><br />' . $faq[$i]['answer'][$z] . '</td></tr>';
		$all_c++;
	}
	
	if(count($faq[$i]['question']) == 0)
	{
		echo '<tr><td class="faq-q-a">' . $lang['no_faq'] . '</td></tr>';
	}
}
?>
</tbody>
</table>
</div>
<?
d_footer();
?>