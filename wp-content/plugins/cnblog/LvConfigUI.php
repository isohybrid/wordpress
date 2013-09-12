<?php
set_time_limit(0);

include 'LvConvert.php';
include 'LvUpThumb.php';

class LvConfigUI
{
	public $tips = '';
	public $updata = array();
	
	public function __construct()
	{
		try 
		{
			$this->_upLoad();
		}
		catch (Exception $e)
		{
			$this->tips = (String)$e->getMessage();
		}
		
		$this->_configUI();
	}
	
	private function _configUI()
	{
		include 'tmp/main.htm';
	}
	
	private function _upLoad()
	{
		if ($_POST['upload'])
		{
			$dir = (String)plugBase('database');
			$thumb = (Object)new LvUpThumb($dir, LvUpThumb::NONE);
			$thumb->setExt(array('xml'));
			if (($thumb = $thumb->addFile('upXml')) != FALSE)
			{
				$convert = (Object)new LvConvert($dir.$thumb['file']);
				$this->updata = (Array)$convert->get();
			}
		}
	}
}

new LvConfigUI();
?>