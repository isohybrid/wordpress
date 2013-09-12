<?php
class LvConvert
{
	private $_url = '';
	private $_xml = NULL;
	private $_data = array();
	
	public function __construct($xml)
	{
		$this->_xml = (Object)simplexml_load_file($xml);
		$this->_url = (String)(get_bloginfo('siteurl').'/');
		$this->rollXml();
	}
	
	public function get()
	{
		return (Array)$this->_data;
	}
	
	private function rollXml()
	{
		global $user_ID;
		
		foreach ($this->_xml->channel->item as $item)
		{
			$id = (Int)$this->_id++;
			$date = (Object)new DateTime($item->pubDate);
			$time = (String)$date->format('Y-m-d H:i:s');
			$title = (String)$item->title;
			
			$tags = array('<description><![CDATA[', ']]></description>');
  			$notags = array('', '');
  			$new_post = array
  			(
  				'post_title' => $title,
  				'post_content' => str_replace($tags, $notags, $item->description->asXML()),
    			'post_status' => 'publish',
  				'post_date' => $time,
  				'post_author' => $user_ID,
  				'post_type' => 'post',
  				'post_category' => array(0)
  			);
  			
  			$id = (Int)wp_insert_post($new_post);
  			$str = $id > 0 ? '日志：《%s》 ID：%d - 添加成功，<a href="%s/?p=%2$d" target="_blank">点击查看日志</a>。'
  							: '日志：《%s》 添加失败。';
  			
  			$this->_data[] = sprintf($str, $title, $id, $this->_url);
		}
	}
}
?>