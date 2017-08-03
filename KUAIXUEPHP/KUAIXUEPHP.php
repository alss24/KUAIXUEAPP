<?php
//核心类
final class KUAIXUEPHP
{
	public static function run(){
		//设置常量
		self::_set_const();
		//默认关闭调试模式
		defined("DEBUG")||define("DEBUG",false);
		if(DEBUG){
			//创建应用所需文件夹
			self::_create_dir();
			//载入应用所需核心文件
			self::_import_file();
		}else{
			error_reporting(0);//屏蔽所有错误提示
			include TEMP_PATH."/~boot.php";
		}
		
		Application::run();
	}
	//设置应用所需常量
	private static function _set_const(){
		//var_dump(__FILE__);//string(52) "D:\phpStudy\WWW\KUAIXUEAPP\KUAIXUEPHP\KUAIXUEPHP.php"
		//设置框架根目录
		$path = str_replace("\\", "/", __FILE__);
		define("KUAIXUEPHP_PATH",dirname($path));//dirname() 函数返回路径中的目录部分。不带最后一个反斜杠
		//echo KUAIXUEPHP_PATH;//D:/phpStudy/WWW/KUAIXUEAPP/KUAIXUEPHP   

		define("CONFIG_PATH",KUAIXUEPHP_PATH."/Config");
		define("DATA_PATH",KUAIXUEPHP_PATH."/Data");
		define("LIB_PATH",KUAIXUEPHP_PATH."/Lib");
		define("CORE_PATH",LIB_PATH."/Core");
		define("FUNCTION_PATH",LIB_PATH."/Function");


		define("ROOT_PATH",dirname(KUAIXUEPHP_PATH));
		//临时目录
		define("TEMP_PATH",ROOT_PATH.'/Temp');
		//日志目录
		define("LOG_PATH",TEMP_PATH.'/Log');
		//应用目录
		define("APP_PATH",ROOT_PATH.'/'.APP_NAME);
		define("APP_CONFIG_PATH",APP_PATH.'/Config');
		define("APP_CONTROLLER_PATH",APP_PATH.'/Controller');
		define("APP_TPL_PATH",APP_PATH.'/Tpl');
		define("APP_PUBLIC_PATH",APP_TPL_PATH."/Public");
		//创建公共
		define("COMMON_PATH",ROOT_PATH.'/Common');
		//公共配置项文件夹
		define("COMMON_CONFIG_PATH",COMMON_PATH.'/Config');
		//公共模型文件夹
		define("COMMON_MODEL_PATH",COMMON_PATH.'/Model');
		//公共库文件夹
		define("COMMON_LIB_PATH",COMMON_PATH.'/Lib');
		define("KUAIXUEPHP_VERSION",'1.0');
		define("IS_POST",( $_SERVER['REQUEST_METHOD']=='POST'?true:false) );
		if( isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest' ){
			define('IS_AJAX',true);
		}else{
			define('IS_AJAX',false);
		}
		
	}
	//运行框架核心类系统自动创建应用目录
	private static function _create_dir(){
		$arr = array(
			COMMON_CONFIG_PATH,
			COMMON_MODEL_PATH,
			COMMON_LIB_PATH,
			APP_PATH,
			APP_CONFIG_PATH,
			APP_CONTROLLER_PATH,
			APP_TPL_PATH,
			APP_PUBLIC_PATH,
			TEMP_PATH,
			LOG_PATH
			);

		foreach($arr as $v){
			is_dir($v) || mkdir($v,0777,true);
		}

		//将系统默认的success.html和error.html复制到用户创建项目中
		is_file(APP_TPL_PATH.'/success.html')||copy(DATA_PATH.'/Tpl/success.html',APP_TPL_PATH.'/success.html');
		is_file(APP_TPL_PATH.'/error.html')||copy(DATA_PATH.'/Tpl/error.html',APP_TPL_PATH.'/error.html');
	}


	//载入应用所需核心文件
	private static function _import_file(){
		$flieArr = array(
			FUNCTION_PATH.'/function.php',
			CORE_PATH.'/Log.class.php',
			CORE_PATH.'/Controller.class.php',
			CORE_PATH.'/Application.class.php'
			);
		$str = "";//用于存储所有要引入的文件的字符串内容
		foreach($flieArr as $v){
			$str .= trim(substr(file_get_contents($v),5,-2));
			require_once $v;
		}
		$str = "<?php \r\n".$str."\r\n";
		file_put_contents(TEMP_PATH.'/~boot.php',$str)||die("access not allow");
	}



}

KUAIXUEPHP::run();