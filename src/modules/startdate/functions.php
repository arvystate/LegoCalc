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
	const module_vars = 'http://www.stargatewars.com/recruit.php?uniqid=;;;;http://stargatewars.com/recruit.php?uniqid=';
	
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
		$vars = explode(';;;;', self::module_vars);
	
		$link = str_replace($vars, '', $this->module_recdata['data']);
		$link = substr($link, 2);
		
		if(!is_numeric($link)) //Trying second parse, giving more wider range of sites to be used
		{
			$link = explode('=', $this->module_recdata['data']);
			$link = substr($link[count($link)-1], 2);
		}

		$this->module_data = $link * 1;
		$this->module_core();
		
		return $this->module_detail;
	}
	
	private function module_core() // The most calculations, this is where you should put the main part
	{
		include_once $this->module_dir . '/' . $this->module_lang['name'] . '/messages.php';
	
		if(!$this->module_data)
		{
			$detail = $messages['error_module'];
		}
		else
		{
			$difference = time() - $this->module_data;
			if(!is_numeric($this->module_data) || $difference < 0)
			{
				$detail = $messages['error_1'];
			}
	
			$days = floor ($difference / 60 / 60 / 24);
			$hours = floor (($difference - $days * 60 * 60 * 24) / 60 / 60);
			$minutes = floor (($difference - $days * 60 * 60 * 24 - $hours * 60 * 60) / 60);
			$seconds = floor (($difference - $days * 60 * 60 * 24 - $hours * 60 * 60 - $minutes * 60));
	
			$detail = $messages['msg_1'];
			$detail .= date("H:i:s" , $this->module_data);
			$detail .= $messages['msg_2'] . date("j. ", $this->module_data) . $this->module_lang['month'][date("n", $this->module_data)] . date(" Y", $this->module_data);
			$detail .= $messages['msg_3'] . $days . ' ' . $messages['days'] . ', ' . $hours . ' ' . $messages['hours'] . ', ' . $minutes . ' ' . $messages['minutes'] . ', ' . $seconds . ' ' . $messages['seconds'] . '.';
			$detail .= '<br /><br />';
			$detail .= $messages['msg_4'];
			$detail .= $this->module_recdata['data'] . '<br /><br />';
		}
		
		$this->module_detail = $detail;
	}
}

?>