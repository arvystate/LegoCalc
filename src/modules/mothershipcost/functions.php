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
			
			$construct .= $file[$i];
		}
		
		return $construct;			
	}
	
	public function calculate() // Usually taking correct post vars and start parsing, just a data collector
	{
		$this->module_data['w_start'] = $this->module_reg_functions->remove_comma($this->module_recdata['w_start']) * 1;
		$this->module_data['w_end'] = $this->module_reg_functions->remove_comma($this->module_recdata['w_end']) * 1;
		$this->module_data['w_naq'] = $this->module_reg_functions->remove_comma($this->module_recdata['w_naq']) * 1;
		
		$this->module_data['s_start'] = $this->module_reg_functions->remove_comma($this->module_recdata['s_start']) * 1;
		$this->module_data['s_end'] = $this->module_reg_functions->remove_comma($this->module_recdata['s_end']) * 1;
		$this->module_data['s_naq'] = $this->module_reg_functions->remove_comma($this->module_recdata['s_naq']) * 1;
		
		$this->module_data['f_start'] = $this->module_reg_functions->remove_comma($this->module_recdata['f_start']) * 1;
		$this->module_data['f_end'] = $this->module_reg_functions->remove_comma($this->module_recdata['f_end']) * 1;
		$this->module_data['f_naq'] = $this->module_reg_functions->remove_comma($this->module_recdata['f_naq']) * 1;
		
		$this->module_data['race'] = $this->module_recdata['race'] * 1;
		$this->module_data['mstype'] = $this->module_recdata['mstype'];
		
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
			if($this->module_data['mstype'] == 0)
			{
			
				//Weapons
				if( ($this->module_data['w_start']) && ($this->module_data['w_end'] > 0) )
				{
					$cost = $this->module_reg_functions->cost($this->module_data['w_start'], $this->module_data['w_end'], 10000);
					$detail .= $messages['msg_1'] . $messages['weapons'] . $messages['msg_2'] . $this->module_data['w_start'] . $messages['msg_3'] . $this->module_data['w_end'] . $messages['msg_4'] . number_format($cost) . $messages['msg_5'];
				}
				elseif( ($this->module_data['w_start']) && ($this->module_data['w_naq'] > 0) )
				{
					$upg = $this->module_reg_functions->upgrade($this->module_data['w_start'], $this->module_data['w_naq'], 10000);
					$detail .= $messages['msg_6'] . number_format($this->module_data['w_naq']) . $messages['msg_7'] . number_format($upg) . $messages['msg_8'] . $messages['weapons'] . $messages['msg_9'] . ($this->module_data['w_start'] + $upg) . '.<br />';
				}
				
				//Shields
				if( ($this->module_data['s_start']) && ($this->module_data['s_end'] > 0) )
				{
					$cost = $this->module_reg_functions->cost($this->module_data['s_start'], $this->module_data['s_end'], 12000);
					$detail .= $messages['msg_1'] . $messages['shields'] . $messages['msg_2'] . $this->module_data['s_start'] . $messages['msg_3'] . $this->module_data['s_end'] . $messages['msg_4'] . number_format($cost) . $messages['msg_5'];
				}
				elseif( ($this->module_data['s_start']) && ($this->module_data['s_naq'] > 0) )
				{
					$upg = $this->module_reg_functions->upgrade($this->module_data['s_start'], $this->module_data['s_naq'], 12000);
					$detail .= $messages['msg_6'] . number_format($this->module_data['s_naq']) . $messages['msg_7'] . number_format($upg) . $messages['msg_8'] . $messages['shields'] . $messages['msg_9'] . ($this->module_data['s_start'] + $upg) . '.<br />';
				}
						
				//Fleets
				if( ($this->module_data['f_start']) && ($this->module_data['f_end'] > 0) )
				{
					$cost = $this->module_reg_functions->cost($this->module_data['f_start'], $this->module_data['f_end'], 10000);
					$detail .= $messages['msg_1'] . $messages['fleets'] . $messages['msg_2'] . $this->module_data['f_start'] . $messages['msg_3'] . $this->module_data['f_end'] . $messages['msg_4'] . number_format($cost) . $messages['msg_5'];
				}
				elseif( ($this->module_data['f_start']) && ($this->module_data['f_naq'] > 0) )
				{
					$upg = $this->module_reg_functions->upgrade($this->module_data['f_start'], $this->module_data['f_naq'], 10000);
					$detail .= $messages['msg_6'] . number_format($this->module_data['f_naq']) . $messages['msg_7'] . number_format($upg) . $messages['msg_8'] . $messages['fleets'] . $messages['msg_9'] . ($this->module_data['f_start'] + $upg) . '.<br />';
				}
				
				if($detail == '')
				{
					$detail = $messages['error_1'];
				}
			}
			else
			{
				//Weapons
				if( ($this->module_data['w_start']) && ($this->module_data['w_end'] > 0) )
				{
					$cost = ($this->module_data['w_end'] - $this->module_data['w_start']) * 2100800;
					$detail .= $messages['msg_10'] . $messages['weapons'] . $messages['msg_2'] . $this->module_data['w_start'] . $messages['msg_3'] . $this->module_data['w_end'] . $messages['msg_4'] . number_format($cost) . $messages['msg_5'];
				}
				elseif($this->module_data['w_naq'] > 0)
				{
					$upg = floor($this->module_data['w_naq'] / 2100800);
					$detail .= $messages['msg_6'] . number_format($this->module_data['w_naq']) . $messages['msg_7'] . number_format($upg) . $messages['msg_8'] . $messages['weapons'] . '.<br />';
				}
				
				//Shields
				if( ($this->module_data['s_start']) && ($this->module_data['s_end'] > 0) )
				{
					$cost = ($this->module_data['s_end'] - $this->module_data['s_start']) * 2250000;
					$detail .= $messages['msg_10'] . $messages['shields'] . $messages['msg_2'] . $this->module_data['s_start'] . $messages['msg_3'] . $this->module_data['s_end'] . $messages['msg_4'] . number_format($cost) . $messages['msg_5'];
				}
				elseif($this->module_data['s_naq'] > 0)
				{
					$upg = floor($this->module_data['s_naq'] / 2250000);
					$detail .= $messages['msg_6'] . number_format($this->module_data['s_naq']) . $messages['msg_7'] . number_format($upg) . $messages['msg_8'] . $messages['shields'] . '.<br />';
				}
							
				//Fleets
				if( ($this->module_data['f_start']) && ($this->module_data['f_end'] > 0) )
				{
					$cost = ($this->module_data['f_end'] - $this->module_data['f_start']) * $this->module_gamedata[0]['race'][$this->module_data['race']]['msfleetprice'];
					$detail .= $messages['msg_10'] . $messages['fleets'] . $messages['msg_2'] . $this->module_data['f_start'] . $messages['msg_3'] . $this->module_data['f_end'] . $messages['msg_4'] . number_format($cost) . $messages['msg_5'];
				}
				elseif($this->module_data['f_naq'] > 0)
				{
					$upg = floor($this->module_data['f_naq'] / $this->module_gamedata[0]['race'][$this->module_data['race']]['msfleetprice']);
					$detail .= $messages['msg_6'] . number_format($this->module_data['f_naq']) . $messages['msg_7'] . number_format($upg) . $messages['msg_8'] . $messages['fleets'] . '.<br />';
				}
				
				if($detail == '')
				{
					$detail = $messages['error_1'];
				}
			}
		}
		
		$this->module_detail = $detail;
	}
}

?>