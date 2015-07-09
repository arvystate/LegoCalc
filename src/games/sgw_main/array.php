<?
if (!defined('IN_APP'))
{
	die('And you thought you found something?');
}

$games = array();

//
// Collection of StarGateWars Main game data
//

$games[0]['name'] = 'StarGateWars Main';

//
// Income
//

$games[0]['uuincome'] = 20;
$games[0]['minerincome'] = 80;

//
// Incuding races
//
include_once 'games/sgw_main/race0.php';
include_once 'games/sgw_main/race1.php';
include_once 'games/sgw_main/race2.php';
include_once 'games/sgw_main/race3.php';
include_once 'games/sgw_main/race4.php';
include_once 'games/sgw_main/race5.php';
include_once 'games/sgw_main/race6.php';
include_once 'games/sgw_main/race7.php';
include_once 'games/sgw_main/race8.php';
include_once 'games/sgw_main/race9.php';
include_once 'games/sgw_main/race10.php';
include_once 'games/sgw_main/race11.php';
include_once 'games/sgw_main/race12.php';
include_once 'games/sgw_main/race13.php';

//
// Including weapons
//

include_once 'games/sgw_main/w.attack.php';
include_once 'games/sgw_main/w.defense.php';

//
// End of file
//

//
//Debugging
//
/*
echo '<pre>';
echo 'Array source<br>';
print_r($games[0]);
echo '</pre>';
exit;*/
?>