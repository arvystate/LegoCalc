<?
$global['page'] = 'home';

include "system/core.php";

d_header();

if(is_file($global['language_dir'] . '/' . $global['lang'] . '/' . $global['page'] . '.lpa'))
{
	$loaded = file($global['language_dir'] . '/' . $global['lang'] . '/' . $global['page'] . '.lpa');
}
else
{
	$loaded = file($global['language_dir'] . '/' . $global['default_lang'] . '/' . $global['page'] . '.lpa');
}

for($i = 0; $i < count($loaded); $i++)
{
	echo $loaded[$i];
}

d_footer();
?>