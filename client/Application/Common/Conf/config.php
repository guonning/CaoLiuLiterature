<?php
return array(
	//数据库配置
	'SITE_NAME' => '文学手机站',
    'DB_TYPE' => 'mysqli',
	'DB_HOST' => '127.0.0.1',
	'DB_NAME' => 'xzns',
	'DB_USER' => 'account',
	'DB_PWD' => 'dashabi123!',
	'DB_PORT'  => '3306',
	
	'TMPL_PARSE_STRING' => array(
		'__CSS__' => '/static/'.THEME_ID.'/css',
        '__JS__' => '/static/'.THEME_ID.'/js',
        '__IMG__' => '/static/'.THEME_ID.'/images',
	),
	'VIEW_PATH' => APP_PATH . '../Theme/' . THEME_ID .'/',
    'LAYOUT_ON'	=> false,
    'LAYOUT_NAME'	=> 'layout',
    'MODULE_ALLOW_LIST'    => array('Home','Manager'),
    'DEFAULT_MODULE' => 'Home',
    'PAGESIZE'  => 15,
);