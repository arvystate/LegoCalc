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
			unset($rep);
			unset($rep2);
			if(strpos($file[$i], '{LC_MODULE_RACEOPTIOND}'))
			{
				for($x = 0; $x < count($this->module_gamedata[0]['race']); $x++)
				{
				
					$tmp = $file[$i];
					if($x == $this->module_recdata['racedef'])
					{
						$tmp = str_replace('<option', '<option selected="selected"', $tmp);
					}
					$rep = array('{LC_MODULE_RACEOPTIOND}', '{LC_MODULE_RACENAMED}');
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
					if($this->module_gamedata[0]['attackweapon'][$x] == $this->module_recdata['wstr'])
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
					if($this->module_gamedata[0]['defweapon'][$x] == $this->module_recdata['wstrdef'])
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
		$this->module_data['stattype'] = $this->module_recdata['stattype'] * 1;
		$this->module_data['race'] = $this->module_recdata['race'] * 1;
		$this->module_data['racedef'] = $this->module_recdata['racedef'] * 1;

		$this->module_data['wstr'] = $this->module_reg_functions->remove_comma($this->module_recdata['wstr']) * 1;
		$this->module_data['wcount'] = $this->module_reg_functions->remove_comma($this->module_recdata['wcount']) * 1;
		$this->module_data['supers'] = $this->module_reg_functions->remove_comma($this->module_recdata['supers']) * 1;
		$this->module_data['trained'] = $this->module_reg_functions->remove_comma($this->module_recdata['trained']) * 1;
		$this->module_data['mercs'] = $this->module_reg_functions->remove_comma($this->module_recdata['mercs']) * 1;
		
		$this->module_data['wstrdef'] = $this->module_reg_functions->remove_comma($this->module_recdata['wstrdef']) * 1;
		$this->module_data['wcountdef'] = $this->module_reg_functions->remove_comma($this->module_recdata['wcountdef']) * 1;
		$this->module_data['supersdef'] = $this->module_reg_functions->remove_comma($this->module_recdata['supersdef']) * 1;
		$this->module_data['traineddef'] = $this->module_reg_functions->remove_comma($this->module_recdata['traineddef']) * 1;
		$this->module_data['mercsdef'] = $this->module_reg_functions->remove_comma($this->module_recdata['mercsdef']) * 1;


		$this->module_data['lvl'] = $this->module_reg_functions->remove_comma($this->module_recdata['lvl']) * 1;
		$this->module_data['spies'] = $this->module_reg_functions->remove_comma($this->module_recdata['spies']) * 1;

		$this->module_data['antilvl'] = $this->module_reg_functions->remove_comma($this->module_recdata['antilvl']) * 1;
		$this->module_data['antispies'] = $this->module_reg_functions->remove_comma($this->module_recdata['antispies']) * 1;
				
		$this->module_data['weapons'] = $this->module_reg_functions->remove_comma($this->module_recdata['weapons']) * 1;
		$this->module_data['shields'] = $this->module_reg_functions->remove_comma($this->module_recdata['shields']) * 1;
		$this->module_data['fleets'] = $this->module_reg_functions->remove_comma($this->module_recdata['fleets']) * 1;
		
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
			$detail = '';
			if($this->module_data['stattype'] == 0)
			{
				//Strike calculator
				if($this->module_data['supers'] >= $this->module_data['wcount'])
				{
					$strike = $this->module_data['wcount'] * 10;
				}
				else
				{
					if(($this->module_data['wcount'] - $this->module_data['supers']) >= ($this->module_data['trained'] + $this->module_data['mercs']))
					{
						$strike = ($this->module_data['supers'] * 10) + (($this->module_data['trained'] + $this->module_data['mercs']) * 5);
					}
					else
					{
						$strike = ($this->module_data['supers'] * 10) + (($this->module_data['wcount']-$this->module_data['supers']) * 5);
					}
				}
				
				$strike = $strike * $this->module_data['wstr'] * $this->module_gamedata[0]['race'][$this->module_data['race']]['attackvar'];
				
				$detail .= $messages['strike'] . $messages['msg_1'] . number_format($strike) . '.<br />';
				
				//Defense calculator
				if($this->module_data['supersdef'] >= $this->module_data['wcountdef'])
				{
					$defense = $this->module_data['wcountdef'] * 10;
				}
				else
				{
					if(($this->module_data['wcountdef'] - $this->module_data['supersdef']) >= ($this->module_data['traineddef'] + $this->module_data['mercsdef']))
					{
						$defense = ($this->module_data['supersdef'] * 10) + (($this->module_data['traineddef'] + $this->module_data['mercsdef']) * 5);
					}
					else
					{
						$defense = ($this->module_data['supersdef'] * 10) + (($this->module_data['wcountdef']-$this->module_data['supersdef']) * 5);
					}
				}
				
				$defense = $defense * $this->module_data['wstrdef'] * $this->module_gamedata[0]['race'][$this->module_data['racedef']]['defvar'];
				
				$detail .= $messages['defense'] . $messages['msg_1'] . number_format($defense) . '.<br />';
				
			}
			elseif($this->module_data['stattype'] == 1)
			{
				//Covert calculator
				
				$covert = ((sqrt(pow(2, $this->module_data['lvl'])) * $this->module_data['spies'] * $this->module_gamedata[0]['race'][$this->module_data['race']]['covertvar']) + $this->module_data['spies']) * 10;
				
				$detail = $messages['covert'] . $messages['msg_1'] . number_format($covert) . '.<br />';
		
				//Anticovert calculator
				
				$anticovert = ((sqrt(pow(2, ($this->module_data['antilvl'] + 2))) * $this->module_data['antispies'] * $this->module_gamedata[0]['race'][$this->module_data['racedef']]['anticovertvar']) + $this->module_data['antispies']) * 10;
		
				$detail .= $messages['anticovert'] . $messages['msg_1'] . number_format($anticovert) . '.<br />';
			}
			else
			{
				//Mothership calculator
				$mothership[0] = $this->module_data['weapons'] * $this->module_gamedata[0]['race'][$this->module_data['race']]['msweapon'];
				$mothership[1] = $this->module_data['shields'] * $this->module_gamedata[0]['race'][$this->module_data['race']]['msshield'];
				$mothership[2] = $this->module_data['fleets'] * $this->module_gamedata[0]['race'][$this->module_data['race']]['msfleet'];
				$mothership[3] = $mothership[0] + $mothership[1] + $mothership[2];
				
				$detail = $messages['mothership'] . ': ' . $messages['msg_2'] . ' ' . number_format($mothership[0]) . '.<br />';
				$detail .= $messages['mothership'] . ': ' . $messages['msg_3'] . ' ' . number_format($mothership[1]) . '.<br />';
				$detail .= $messages['mothership'] . ': ' . $messages['msg_4'] . ' ' . number_format($mothership[2]) . '.<br />';
				$detail .= $messages['mothership'] . $messages['msg_1'] . ' ' . number_format($mothership[3]) . '.<br />';
			}
		}
		
		$this->module_detail = $detail;
	}
}

?>