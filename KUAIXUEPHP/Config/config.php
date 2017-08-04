<?php

//返回系统配置项
return array(
	//验证码位数
	"CODE_LEN"=>4,
	//默认时区
	"DEFAULT_TIME_ZONE"=>"PRC",
	//session自动开启
	"SESSION_AUTO_START"=>TRUE,
	//url中对应的控制器标识符
	"VAR_CONTROLLER"=>"c",
	//url中对应的方法名标识符
	"VAR_ACTION"=>"a",
	//是否开启日志
	"SAVE_LOG"=>TRUE,
	//错误跳转地址
	'ERROR_URL'=>'',
	//错误提示信息
	'ERROR_MSG'=>'网站出错了，请稍候再试。。。',
	//自动加载Common/Lib目录下的文件，可以载入多个
	'AUTO_LOAD_FILE'=>array(),

	//数据库配置
	'DB_CHARSET'=>'utf-8',
	'DB_HOST'=>'127.0.0.1',
	'DB_PORT'=>3306,
	'DB_USER'=>'root',
	'DB_PASSWORD'=>'123456',
	'DB_DATABASE'=>'db_maven',
	'DB_PREFIX'=>'t_'
	);