<?php
/**
 * Plugin Name: 转换博客园(cnblogs.com)数据
 * Plugin URI: http://www.yiiku.com
 * Description: 将博客园(cnblogs.com)数据转换至wordpress中
 * Author: Levi
 * Version: 1.0
 * Author URI: http://www.yiiku.com
 */

define('BASENAME', plugBase());
add_action('admin_menu', 'my_plugin_menu');

function my_plugin_menu() 
{
	add_management_page('Cnblog数据转换', 'Cnblog数据转换', 8, 'lv_cnblog', 'my_plugin_options');
}

function my_plugin_options()
{
	include 'LvConfigUI.php';
}

function plugBase($name = '')
{
	$dirname = dirname(__FILE__).'/';
	!empty($name) && $dirname.= $name.'/';
	return strstr($dirname, '\\') ? str_replace('\\', '/', $dirname) : $dirname;
}