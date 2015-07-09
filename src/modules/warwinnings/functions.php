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
			
			if(strpos($file[$i], 'option'))
			{
				$split = explode('value="', $file[$i]);
				$number = explode('"', $split[1]);
				$number = $number[0];
				
				$compare = $this->module_recdata['relation'];
				
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
		$this->module_data['relation'] = $this->module_recdata['relation'] * 1;

		$this->module_data['strike'] = $this->module_reg_functions->remove_comma($this->module_recdata['strike']) * 1;
		$this->module_data['defense'] = $this->module_reg_functions->remove_comma($this->module_recdata['defense']) * 1;
		$this->module_data['naq'] = $this->module_reg_functions->remove_comma($this->module_recdata['naq']) * 1;
		$this->module_data['uu'] = $this->module_reg_functions->remove_comma($this->module_recdata['uu']) * 1;
		$this->module_data['turns'] = $this->module_reg_functions->remove_comma($this->module_recdata['turns']) * 1;
		
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
			if( ($this->module_data['turns'] < 0) || ($this->module_data['turns'] > 15) )
			{
				$this->module_data['turns'] = 15;
			}
		
			$strike_max = $this->module_data['strike'];
			$strike_min = $this->module_data['strike'] * 0.75;
			
			$defense_max = $this->module_data['defense'];
			$defense_min = $this->module_data['defense'] * 0.75;
			
			if($strike_max < $defense_min)
			{
				$detail = $messages['msg_1'];
			}
			else
			{
				if( ($strike_max > $defense_min) && ($strike_max <= $defense_max) ) 
				{
					$detail = $messages['msg_2'];
				}
				else
				{
					$detail = $messages['msg_3'];
				}
				$detail .= '!<br />';
				
				$naq = $this->module_data['naq'] * 0.75;
				$uu = $this->module_data['uu'] * 0.015;
				
				$turns = $this->module_data['turns'] / 15;
				
				$naq = $naq * $turns * $this->module_data['relation'];
				$uu = $uu * $turns * $this->module_data['relation'];
				
				$naq = round($naq);
				$uu = round($uu);
				
				if($naq > $this->module_data['naq'])
				{
					$naq = $this->module_data['naq'];
				}
				if($uu > $this->module_data['uu'])
				{
					$uu = $this->module_data['uu'];
				}
				
				$detail .= $messages['msg_4'] . number_format($naq) . $messages['msg_5'] . number_format($uu) . $messages['msg_6'] . '.<br />';
			}
		}
		
		$this->module_detail = $detail;
	}
}

?>