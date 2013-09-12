<?php

class LvUpThumb
{
	const AUTHOR = 'author';
	const MAGZINE = 'magzine';
	const SLIDE = 'slide';
	const WORKS = 'works';
	
	const FILE_NULL = '无文件上传';
	
	const DAY = '1';
	const MONTH = '2';
	const NONE = '3';
	
	private $_path = '';
	private $_thumb = '';
	private $_upfile = array();
	private $_target = array();
	private $_upext = array('jpg', 'jpeg', 'gif', 'png');
	
	public function __construct($path, $dir = LvUpThumb::MONTH)
	{
		$this->_path = (String)$path;
		switch($dir)
		{
			case 1: $this->_thumb .= 'day_'.date('ymd').'/'; break;
			case 2: $this->_thumb .= 'month_'.date('ym').'/'; break;
			case 3: $this->_thumb .= ''; break;
		}
	}
	
	public function setExt($opt)
	{
		$this->_upext = (Array)$opt;
	}
	
	public function addFile($input, $target = NULL)
	{
		if (isset($_FILES[$input]))
		{
			$this->_createDir();
			$this->_target = array();
			
			$upfile = $_FILES[$input];
			$this->_upErr($upfile['error']);
			$this->_create($upfile, $target);
			$this->_exec();
			
			return (Array)$this->_target;
		}
		else throw new Exception('文件域的name错误。');
	}
	
	private function _createDir()
	{
		$dir = $this->_path.$this->_thumb;
		if(!is_dir($dir))
		{
			mkdir($dir, 0777);
			fclose(fopen($dir.'index.htm', 'w'));
		}
	}
	
	private function _exec()
	{
		$target = (String)$this->_fileName();
		$file = $this->_path.$target;
		copy($this->_upfile['file'], $file);
		chmod($file, 0755);
		
		$this->_target += array
		(
			'file' => $target, 
			'ext' => $this->_upfile['ext'], 
			'size' => filesize($file),
			'time' => time()
		);
	}
	
	private function _create($up, $target)
	{
		$temppath = $up['tmp_name'];
		if(empty($temppath) || $temppath == 'none')
		{
			throw new Exception(self::FILE_NULL);
		}
		
		$fileinfo = pathinfo($up['name']);
		$extension = $fileinfo['extension'];
		
		if ($this->_fileExt($extension))
		{
			$this->_upfile = array('file' => $temppath, 'ext' => $extension, 'target' => $target);
		}
	}
	
	private function _fileName()
	{
		$up = (Array)$this->_upfile;
		$target = $this->_path.$up['target'];
		if (is_file($target))
		{
			$info = pathinfo($target);
			$extension = $info['extension'];
			$this->_target['map'] = $up['target'];
			if ($extension == $up['ext'])
			{
				return (String)$up['target'];
			}
			
			chmod($target, 0777);
			unlink($target);
		}
		
		PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
		$file = $this->_thumb.date('YmdHis').mt_rand(1000,9999).'.'.$up['ext'];
		if (!isset($this->_target['map']))
		{
			$this->_target['map'] = $file;
		}
		
		return (String)$file;
	}
	
	private function _fileExt($ext)
	{
		foreach ($this->_upext as $upext)
		{
			if (strtolower($ext) == $upext) return TRUE;
		}
		
		throw new Exception('上传文件扩展名必需为：'.implode(', ', $this->_upext));
	}
	
	private function _upErr($err)
	{
		if(empty($err)) return ;
		switch($err)
		{
			case '1':
				$str = '文件大小超过了php.ini定义的upload_max_filesize值';
				break;
			case '2':
				$str = '文件大小超过了HTML定义的MAX_FILE_SIZE值';
				break;
			case '3':
				$str = '文件上传不完全';
				break;
			case '4':
				$str = self::FILE_NULL;
				break;
			case '6':
				$str = '缺少临时文件夹';
				break;
			case '7':
				$str = '写文件失败';
				break;
			case '8':
				$str = '上传被其它扩展中断';
				break;
			case '999':
			default:
				$str = '无有效错误代码:'.$err;
		}
		
		throw new Exception($str);
	}
}

?>