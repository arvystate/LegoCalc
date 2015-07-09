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
			
			if(strpos($file[$i], '"e_alert"'))
			{
				$rep_type = 0;
			}
			if(strpos($file[$i], '"relation"'))
			{
				$rep_type = 1;
			}
			
			if(strpos($file[$i], 'option'))
			{
				$split = explode('value="', $file[$i]);
				$number = explode('"', $split[1]);
				$number = $number[0];
				
				unset($compare);
				if($rep_type == 0)
				{
					$compare = $this->module_recdata['e_alert'];
				}
				else
				{
					$compare = $this->module_recdata['relation'];
				}
				
				if($number == $compare)
				{
					$file[$i] = str_replace('<option', '<option selected="selected"', $file[$i]);
				}
			}
			
			$txt = '';
			unset($rep);
			unset($rep2);
			if(strpos($file[$i], '{LC_MODULE_RACEOPTION}'))
			{
				for($x = 0; $x < count($this->module_gamedata[0]['race']); $x++)
				{
				
					$tmp = $file[$i];
					if($x == $this->module_recdata['race'])
					{
						$tmp = str_replace('<option', '<option selected="selected"', $tmp);
					}
					$rep = array('{LC_MODULE_RACEOPTION}', '{LC_MODULE_RACENAME}');
					$rep2 = array($x, $this->module_gamedata[0]['race'][$x]['name']);
					$txt .= str_replace($rep, $rep2, $tmp);
				}
				$file[$i] = $txt;
			}
			
			$txt = '';
			if(strpos($file[$i], '{LC_MODULE_WEAPONSTR}'))
			{
				for($x = 0; $x < count($this->module_gamedata[0]['attackweapon']); $x++)
				{
					$tmp = $file[$i];
					if($this->module_gamedata[0]['attackweapon'][$x] == $this->module_recdata['strength'])
					{
						$tmp = str_replace('<option', '<option selected="selected"', $tmp);
					}

					$txt .= str_replace('{LC_MODULE_WEAPONSTR}', $this->module_gamedata[0]['attackweapon'][$x], $tmp);
				}
				$file[$i] = $txt;
			}
			
			$txt = '';
			if(strpos($file[$i], '{LC_MODULE_WEAPONSTRD}'))
			{
				for($x = 0; $x < count($this->module_gamedata[0]['defweapon']); $x++)
				{
					$tmp = $file[$i];
					if($this->module_gamedata[0]['defweapon'][$x] == $this->module_recdata['strength'])
					{
						$tmp = str_replace('<option', '<option selected="selected"', $tmp);
					}
					
					$txt .= str_replace('{LC_MODULE_WEAPONSTRD}', $this->module_gamedata[0]['defweapon'][$x], $tmp);
				}
				$file[$i] = $txt;
			}

			$construct .= $file[$i];
		}
		
		return $construct;			
	}
	
	public function calculate() // Usually taking correct post vars and start parsing, just a data collector
	{
		$this->module_data['e_covert'] = $this->module_reg_functions->remove_comma($this->module_recdata['e_covert']) * 1;
		$this->module_data['y_covert'] = $this->module_reg_functions->remove_comma($this->module_recdata['y_covert']) * 1;
		$this->module_data['y_spylvl'] = $this->module_reg_functions->remove_comma($this->module_recdata['y_spylvl']) * 1;
		$this->module_data['e_alert'] = $this->module_recdata['e_alert'] * 1;
		$this->module_data['strength'] = $this->module_recdata['strength'] * 1;
		$this->module_data['relation'] = $this->module_recdata['relation'] * 1;
		$this->module_data['type'] = $this->module_recdata['type'] * 1;
		$this->module_data['race'] = $this->module_recdata['race'] * 1;
		
		$this->module_data['e_wpower'] = $this->module_reg_functions->remove_comma($this->module_recdata['e_wpower']) * 1;
		$this->module_data['e_weapons'] = $this->module_reg_functions->remove_comma($this->module_recdata['e_weapons']) * 1;
				
		$this->module_core();
		
		return $this->module_detail;
	}
	
	private function module_core() // The most calculations, this is where you should put the main part 
	{
		// This function should NOT use $this->module_recdata variable
		include_once $this->module_dir . '/' . $this->module_lang['name'] . '/messages.php';
	
		if(!$this->module_data)
		{
			$detail = $messages['error_1'];
		}
		else
		{
			$enemy = (1 + ($this->module_data['e_alert'] / 100)) * $this->module_data['e_covert'];
			if($enemy > $this->module_data['y_covert'])
			{
				$detail = $messages['msg_1'];
			}
			else
			{
				if($this->module_data['type'] == 0)
				{
					$spies = 0;
					$covert = 0;
					
					$precision = 100;
									
					while($covert < $enemy)
					{
						$spies = $spies + $precision;
						$covert = ((sqrt(pow(2, $this->module_data['y_spylvl'])) * $spies * $this->module_gamedata[0]['race'][$this->module_data['race']]['covertvar']) + $spies) * 10;
						if($spies > 50000000)
						{
							$break;
						}
					}
	
					$detail = $messages['msg_2'] . number_format($spies) . $messages['msg_3'] . '.<br />';
				}
				else
				{
					$damage = $this->module_data['y_covert'] - $enemy;
					
					if ($damage > ($this->module_data['e_wpower'] * 0.02) )
					{
						$damage = $this->module_data['e_wpower'] * 0.02;
					}
					if ($damage < 5900)
					{
						$damage = 5900;
					}
					
					$damage = $damage * $this->module_data['relation'];
					
					$wpower = $this->module_data['strength'] * $this->module_data['e_weapons'];
					
					if($damage >= $wpower)
					{
						$destroyed = $this->module_data['e_weapons'];
						$rest = $damage - $wpower;
					}
					else
					{
						$destroyed = floor($damage / $this->module_data['strength']);
						$rest = 0;
					}
					
					$detail = $messages['msg_4'] . number_format($damage) . $messages['msg_5'] . number_format($destroyed) . $messages['msg_6'] . number_format($rest) . $messages['msg_7'] . '.<br />';
				}
			}
		}
		
		$this->module_detail = $detail;
	}
}

?>