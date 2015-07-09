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
			
			if(strpos($file[$i], '"off"'))
			{
				$rep_type = 1;
			}
			if(strpos($file[$i], '"defcon"'))
			{
				$rep_type = 2;
			}
						
			if(strpos($file[$i], 'option'))
			{
				$split = explode('value="', $file[$i]);
				$number = explode('"', $split[1]);
				$number = $number[0];
				
				unset($compare);
				if($rep_type == 0)
				{
					$compare = $this->module_recdata['comm'];
				}
				elseif($rep_type == 1)
				{
					$compare = $this->module_recdata['off'];
				}
				else
				{
					$compare = $this->module_recdata['defcon'];
				}
				
				if($number == $compare)
				{
					$file[$i] = str_replace('<option', '<option selected="selected"', $file[$i]);
				}
			}
			
			if(strpos($file[$i], '"nox"'))
			{
				if($this->module_recdata['nox'])
				{
					$file[$i] = str_replace('name=', 'checked="checked" name=', $file[$i]); 
				}
			}
			
			if(strpos($file[$i], '"banner"'))
			{
				if($this->module_recdata['banner'])
				{
					$file[$i] = str_replace('name=', 'checked="checked" name=', $file[$i]); 
				}
			}
					
			$construct .= $file[$i];
		}
		
		return $construct;			
	}
	
	public function calculate() // Usually taking correct post vars and start parsing, just a data collector
	{
		$this->module_data['inctype'] = $this->module_recdata['inctype'] * 1;
				
		$this->module_data['uu'] = $this->module_reg_functions->remove_comma($this->module_recdata['uu']) * 1;
		$this->module_data['miners'] = $this->module_reg_functions->remove_comma($this->module_recdata['miners']) * 1;	
		
		$this->module_data['race'] = $this->module_recdata['race'] * 1;
		$this->module_data['comm'] = $this->module_recdata['comm'] * 1;
		$this->module_data['off'] = $this->module_recdata['off'] * 1;
		$this->module_data['defcon'] = $this->module_recdata['defcon'] * 1;
		$this->module_data['nox'] = $this->module_recdata['nox'];
		$this->module_data['banner'] = $this->module_recdata['banner'];

		$this->module_data['strength'] = $this->module_reg_functions->remove_comma($this->module_recdata['strength']) * 1;	
		$this->module_data['count'] = $this->module_reg_functions->remove_comma($this->module_recdata['count']) * 1;
		$this->module_data['points'] = $this->module_reg_functions->remove_comma($this->module_recdata['points']) * 1;	
		
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
			if($this->module_data['inctype'] == 0)
			{
				$income = ($this->module_data['uu'] * $this->module_gamedata[0]['uuincome']) + ($this->module_data['miners'] * $this->module_gamedata[0]['minerincome']);
				$income = $income * $this->module_gamedata[0]['race'][$this->module_data['race']]['incomevar'];
				
				if($this->module_data['comm'] == 0)
				{
					$income = $income * 1.1;
				}
				
				if($this->module_data['off'] != 0)
				{
					$perc = 1 - ($this->module_data['off'] / 100);
					$income = $income * $perc;
				}
				
				if($this->module_data['defcon'] != 0)
				{
					$perc = 1 - ($this->module_data['defcon'] / 100);
					$income = $income * $perc;
				}
				
				if(isset($this->module_data['nox']))
				{
					$income = $income * 0.9;
				}
				
				if(isset($this->module_data['banner']))
				{
					$income = $income * 1.01;
				}	
				
				$bank = $income * 0.75 * 48 * 2;
				
				$detail = $messages['msg_1'] . number_format($income) . $messages['msg_2'] . '.<br />';
				$detail .= $messages['msg_3'] . number_format($bank) . $messages['msg_4'] . '.<br />';
			}
			else
			{
				if($this->module_data['strength'] != 0)
				{
					$price_per_point = round( max((1000 / $this->module_data['strength']), 1) + 5) * $this->module_data['count'];
					$price = $price_per_point * $this->module_data['points'];
					$detail = $messages['msg_5'] . number_format($price) . $messages['msg_6'] . number_format($this->module_data['count']) . $messages['msg_7'] . $this->module_data['strength'] . $messages['msg_8'] . $this->module_data['points'] . '.<br />';
					$detail .= $messages['msg_9'] . number_format($price_per_point) . $messages['msg_4'] . '.<br />';
				}	
			}
		}
		
		$this->module_detail = $detail;
	}
}

?>