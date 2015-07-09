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
			
			$construct .= $file[$i];
		}
		
		return $construct;			
	}
	
	public function calculate() // Usually taking correct post vars and start parsing, just a data collector
	{
		$this->module_data['start'] = $this->module_reg_functions->remove_comma($this->module_recdata['start']) * 1;
		
		if($this->module_recdata['type'] == 0)
		{
			$this->module_data['end'] = $this->module_reg_functions->remove_comma($this->module_recdata['end']) * 1;
		}
		else
		{
			$this->module_data['naq'] = $this->module_reg_functions->remove_comma($this->module_recdata['naq']) * 1;
		}
		
		$this->module_data['type'] = $this->module_recdata['type'];	

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
			if($this->module_data['type'] == 0)
			{
				if( (!isset($this->module_data['start'])) || (!is_numeric($this->module_data['start'])) || ($this->module_data['start'] < 0) )
				{
					$detail = $messages['error_1'];
				}
				
				elseif( (!isset($this->module_data['end'])) || (!is_numeric($this->module_data['end'])) || ($this->module_data['end'] <= 0) )
				{
					$detail = $messages['error_2'];
				}
				else
				{
					$startspy = $this->module_data['start'];
					$multiplier = $this->module_data['end'] - $this->module_data['start'];
						
					$price = 0;
					for($i = 0; $i < $multiplier; $i++)
					{
						$startspy++;
						$price += (pow(2, $startspy) * 3000);
					}

					$lastprice = pow(2, $startspy) * 3000;
								
					$detail = $messages['msg_1'] . $this->module_data['start'] . $messages['msg_2'] . $this->module_data['end'] . $messages['msg_3'] . number_format($price) . '.<br />';
					$detail .= $messages['msg_4'] . ': ' . number_format($lastprice) . '.<br />';
					$detail .= $messages['msg_7'] . ': ' . number_format(($price * 0.9)) . '.<br />';
					$detail .= $messages['msg_8'] . ': ' . number_format(($lastprice * 0.9)) . '.<br />';
				}
			}
			else
			{
				if( (!isset($this->module_data['start'])) || (!is_numeric($this->module_data['start'])) || ($this->module_data['start'] < 0) )
				{
					$detail = $messages['error_1'];
				}
				
				elseif( (!isset($this->module_data['naq'])) || (!is_numeric($this->module_data['naq'])) || ($this->module_data['naq'] <= 0) )
				{
					$detail = $messages['error_3'];
				}
				else
				{
					$naq = $this->module_data['naq'];
					$up = $this->module_data['start'];
					
					$startnaq = $naq;
				
					$spy = $this->module_data['start'];
					$spyrepli = $this->module_data['start'];
		
					while($naq > 0)
					{
						if(($naq - (pow(2, $spy) * 3000)) < 0)
						{
							break;
						}
						else
						{
							$spy++;
							$naq -= (pow(2, $spy) * 3000);
						}
					}
					$lastprice = pow(2, $spy) * 3000;
		
					$naq = $startnaq;
		
					while($naq > 0)
					{
						if(($naq - (pow(2, $spyrepli) * 3000 * 0.9)) < 0)
						{
							break;
						}
						else
						{
							$spyrepli++;
							$naq -= (pow(2, $spyrepli) * 3000 * 0.9);
						}
					}
					
					$detail = $messages['msg_5'] . number_format($this->module_data['naq']) . $messages['msg_6'] . $this->module_data['start'] . $messages['msg_2'] . $spy . '.<br />';
					$detail .= $messages['msg_4'] . ': ' . number_format($lastprice) . '.<br />';
					$detail .= $messages['msg_5'] . number_format($this->module_data['naq']) . $messages['msg_9'] . $this->module_data['start'] . $messages['msg_2'] . $spyrepli . '.<br />';
					$detail .= $messages['msg_8'] . ': ' . number_format(($lastprice * 0.9)) . '.<br />';
				}
			}
		}
		
		$this->module_detail = $detail;
	}
}

?>