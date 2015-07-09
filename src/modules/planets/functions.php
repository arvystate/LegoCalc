<?
if(!defined('IN_APP'))
{
	die('And you thought you found something?');
}

class calc_module
{
	private $module_recdata; // Data send to module by main file (POST array usually)
	private $module_gamedata; // Race data sent in from another file
	private $module_data; // Data stored and parsed in module
	private $module_detail; // Returned details from parser
	private $module_dir; // Module directory
	private $module_reg_functions;
	private $module_lang;
	
	// Static module variables, get array later, divide them with ;;;;
	const module_vars = NULL;
	
	public function construct_input($module_nr, $data, $lang, $dir, $reg_functions, $gamedata = 0) // Module calculation page constructor
	{
		$this->module_reg_functions = $reg_functions;
		$this->module_recdata = $data;
		$this->module_gamedata = $gamedata;
		
		if(!is_dir($dir . '/' . $lang['name']))
		{
			$lang['name'] = 'en';
		}
		
		$this->module_dir = $dir;
		$this->module_lang = $lang;
				
		$file = file($this->module_dir . '/' . $this->module_lang['name'] . '/construct.lpa');
		
		$construct = '';
		
		$rep_type = 0;
		for($i = 0; $i < count($file); $i++)
		{
			$file[$i] = str_replace('{LC_MODULE_NR}', $module_nr, $file[$i]);
			
			if(strpos($file[$i], '{LC_MODULE_RECDATA['))
			{
				$str = explode('{LC_MODULE_RECDATA[', $file[$i]);
				$str = explode(']}', $str[1]);
				$key = $str[0];
				
				if(!$this->module_recdata[$key])
				{
					$replace = 0;
				}
				else
				{
					$replace = $this->module_recdata[$key];
				}
								
				$file[$i] = str_replace('{LC_MODULE_RECDATA[' . $key . ']}', $replace, $file[$i]);
			}
			
			if(strpos($file[$i], '"size"'))
			{
				$rep_type = 0;
			}			
			if(strpos($file[$i], '"type"'))
			{
				$rep_type = 1;
			}
			
			if(strpos($file[$i], '<option'))
			{
				$split = explode('value="', $file[$i]);
				$number = explode('"', $split[1]);
				$number = $number[0];
				
				unset($compare);
				if($rep_type == 0)
				{
					$compare = $this->module_recdata['size'];
				}
				else
				{
					$compare = $this->module_recdata['type'];
				}
				
				if($number == $compare)
				{
					$file[$i] = str_replace('<option', '<option selected="selected"', $file[$i]);
				}
			}
			
			$construct .= $file[$i];
		}
		
		return $construct;			
	}
	
	public function calculate() // Usually taking correct post vars and start parsing, just a data collector
	{
		$this->module_data['pl_type'] = $this->module_recdata['pl_type'] * 1;
		$this->module_data['type'] = $this->module_recdata['type'] * 1;
		$this->module_data['size'] = $this->module_recdata['size'] * 1;

		$this->module_data['start'] = $this->module_reg_functions->remove_comma($this->module_recdata['start']) * 1;
		$this->module_data['end'] = $this->module_reg_functions->remove_comma($this->module_recdata['end']) * 1;
		$this->module_data['naq'] = $this->module_reg_functions->remove_comma($this->module_recdata['naq']) * 1;
		
		$this->module_data['defenses'] = $this->module_reg_functions->remove_comma($this->module_recdata['defenses']) * 1;
		
		$this->module_core();
		
		return $this->module_detail;
	}
	
	private function module_core() // The most calculations, this is where you should put the main part 
	{
		// This function should NOT use $this->module_recdata variable
		include_once $this->module_dir . '/' . $this->module_lang['name'] . '/messages.php';
	
		if(!$this->module_data)
		{
			$detail = $messages['error_module'];
		}
		else
		{
			if($this->module_data['size'] == 0)
			{
				$detail = $messages['error_1'];
			}
			else
			{
				if($this->module_data['size'] == 0)
				{
					$this->module_data['size'] = 1;
				}
				
				$type = array($messages['attack'], $messages['defense'], $messages['covert'], $messages['income'], $messages['up']);
					
				$base[0] = 150000;
				$base[1] = 120000;
				$base[2] = 905096;
				$base[3] = 192000;
				$base[4] = 7005000;
					
				$increase[0] = 3000;
				$increase[1] = 3000;
				$increase[2] = 9000;
				$increase[3] = 5000;
				$increase[4] = 5000;

				if( ($this->module_data['type'] > 4) || ($this->module_data['type'] < 0) )
				{
					$this->module_data['type'] = 0;
				}
				
				if($this->module_data['pl_type'] == 0)
				{				
					if($this->module_data['type'] == 4)
					{
						$size = ($this->module_data['size'] / 10) + 1;
						$gap = $this->module_data['end'] - $this->module_data['start'];
						$totalLvls = round($gap / $size);
						$cost4intLvl = ((floor($this->module_data['start'] / $size)-1) * $increase[$this->module_data['type']]) + $base[$this->module_data['type']];
							
						$totalEndCost = 0;
						for($x = 1; $x <= $totalLvls; $x++)
						{
							$totalEndCost = $totalEndCost + ($cost4intLvl + ($increase[$this->module_data['type']] * $x));
						}
					}		
					else
					{					
						$minus = 0;
						if($this->module_data['type'] == 3)
						{
							$minus = 490000;
						}		
	
						$totalEndCost = 0;
						$size = $this->module_data['size'];
						$currentlvls = $this->module_data['start'] / ($base[$this->module_data['type']] * $size);
						$finallvls = round($this->module_data['end'] / ($base[$this->module_data['type']] * $size));
						$totalLvls = $finallvls - $currentlvls;
	
						for($x = 1; $x <= $totalLvls; $x++)
						{
							$totalEndCost = $totalEndCost + ((($x + $currentlvls) * $increase[$this->module_data['type']]) - $minus);
						}
					}
	
					$detail = $messages['msg_1'] . number_format($this->module_data['end']) . $messages['msg_2'] . number_format($this->module_data['start']) . $messages['msg_3'] . number_format($totalEndCost) . '.<br />';
					$detail .= $messages['msg_4'] . $type[$this->module_data['type']] . $messages['msg_5'] . number_format($totalLvls) . $messages['msg_6'] . '.<br />';
				}
				elseif($this->module_data['pl_type'] == 1)
				{
					if($this->module_data['type'] == 4)
					{
						$size = ($this->module_data['size'] / 10) + 1;
						$cost4intLvl = (floor($this->module_data['start'] / $size) * $increase[$this->module_data['type']]) + $base[$this->module_data['type']];
						
						$naq = $this->module_data['naq'];
						$counter = 1;
						
						while($naq > 0)
						{
							$current_cost = ($cost4intLvl + ($increase[$this->module_data['type']] * $counter));
							if( ($naq - $current_cost) < 0)
							{
								break;
							}
							else
							{
								$naq = $naq - $current_cost;
								$counter++;
							}
						}
					}		
					else
					{					
						$minus = 0;
						if($this->module_data['type'] == 3)
						{
							$minus = 490000;
						}		
	
						$totalEndCost = 0;
						$size = $this->module_data['size'];
						$currentlvls = $this->module_data['start'] / ($base[$this->module_data['type']] * $size);
						
						$naq = $this->module_data['naq'];
						$counter = 1;
						
						while($naq > 0)
						{
							$current_cost = ((($counter + $currentlvls) * $increase[$this->module_data['type']]) - $minus);
							if( ($naq - $current_cost) < 0)
							{
								break;
							}
							else
							{
								$naq = $naq - $current_cost;
								$counter++;
							}
						}
					}
	
					$detail = $messages['msg_14'] . number_format($this->module_data['naq']) . $messages['msg_15'] . number_format($counter-1) . $messages['msg_16'] . '.<br />';
					$detail .= $messages['msg_17'] . $type[$this->module_data['type']] . ' ' . $messages['msg_18'] . ': ' . number_format($current_cost) . '.<br />';			
				}
				else
				{
					$defense_power = 3000000;
					if($this->module_data['naq'] > 0)
					{
						$upg = floor($this->module_data['naq'] / (10000000 * $this->module_data['size']));
						$detail = $messages['msg_7'] . number_format($this->module_data['naq']) . $messages['msg_8'] . number_format($upg) . $messages['msg_9'] . '.<br />';
						$detail .= $messages['msg_13'] . ' ' . $messages['msg_18'] . ': ' . number_format($defense_power * $this->module_data['defenses']) . '.<br />';
					}
					else
					{
						if($this->module_data['defenses'] <= 0)
						{
							$this->module_data['defenses'] = 1;
						}
						
						$price = 10000000 * $this->module_data['size'] * $this->module_data['defenses'];
						$detail = $messages['msg_10'] . number_format($this->module_data['defenses']) . $messages['msg_11'] . number_format($price) . $messages['msg_12'] . '.<br />';
						$detail .= $messages['msg_13'] . ' ' . $messages['msg_18'] . ': ' . number_format($defense_power * $this->module_data['defenses']) . '.<br />';
					}
				}
			}
		}
		
		$this->module_detail = $detail;
	}
}

?>