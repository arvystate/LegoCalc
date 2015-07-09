<?
if(!defined('IN_APP'))
{
	die('And you thought you found something?');
}

//
// Module main vars - required for loading - should be entered in the correct language
//

$module_name = 'Planets'; // Name of the module
$module_requirements  = 'Planet size, type, current facilities, desired facilities, wanted defenses, amount of Naquadah'; // What you need to have to work with module

$module_games = 'sgw_main'; /* Games that the module supports and needs loaded, enter 'all' if no game vars need 
to be loaded, otherwise part games with , like: $module_games = 'sgw_main,sgw_ascended,trw';     */
?>