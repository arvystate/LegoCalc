<?
if(!defined('IN_APP'))
{
	die('And you thought you found something?');
}

class reg_function
{
	public function remove_comma ($text = '')
	{
		$text = str_replace(",", "", $text);
		$text = str_replace(".", "", $text);
		return $text;
	}
	
	public function cost ($start = 0, $end = 0, $price = 0)
	{
		$multiplier = $end - $start;
		
		$startup = $start;
		$pris = 0;
		for($i = 0; $i < $multiplier; $i++)
		{
			$pris += ( ($startup * $price) + $price);
			$startup++;
		}
		return $pris;
	
	}
	
	public function upgrade ($start = 0, $naq = 0, $price = 0)
	{
	
		$up = $start;
	
		while($naq > 0)
		{
			if($naq - (($up * $price) + $price) < 0)
			{
				break;
			}
			else
			{
				$naq -= (($up * $price) + $price);
				$up++;
			}
		}
		
		$quan = $up - $start;
		return $quan;
	}
}

$global['functions'] = new reg_function;
?>