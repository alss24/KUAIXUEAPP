<?php
final class Application{
	public static function run(){
		
		self::_init();
		self::_set_url();
		//载入用户自定义的库文件
		self::_user_import();
		//自动载入
		spl_autoload_register(array(__CLASS__,'_autoload'));
		//创建默认控制器
		self::_create_demo();

		self::_app_run();
	}

	//初始化框架
	private static function _init(){
		//加载配置项
		C( include CONFIG_PATH.'/config.php' );


		//公共配置项
		$commonPath = COMMON_CONFIG_PATH.'/config.php';
		$commonConfig = <<<str
<?php
return array(
	//配置项=>配置值
);
?>
str;
	
		//查看是否已经有自定义的公共配置项文件了，如果没有则为之创建一个默认的
		is_file($commonPath)||file_put_contents($commonPath, $commonConfig);

		//加载公告的配置项
		C( include $commonPath );





		//用户配置项
		$userPath = APP_CONFIG_PATH.'/config.php';
		$userConfig = <<<str
<?php
return array(
	//配置项=>配置值
);
?>
str;
	
		//查看用户是否已经有自定义的配置项文件了，如果没有则为之创建一个默认的
		is_file($userPath)||file_put_contents($userPath, $userConfig);

		//加载用户的配置项
		C( include $userPath );

		//设置默认时区
		date_default_timezone_set(C('DEFAULT_TIME_ZONE'));
		//是否开启session
		C('SESSION_AUTO_START') && session_start();

	}
	//设置外部路径
	private static function _set_url(){
		$path = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
		$path = str_replace('\\', '/', $path);
		define('__APP__',$path);//http://localhost/KUAIXUEAPP/index.php
		define('__ROOT__',dirname($path));
		
		define('__TPL__',__ROOT__.'/'.APP_NAME.'/Tpl');
		define('__PUBLIC__',__TPL__.'/Public');
	}
	//自动载入
	private static function _autoload($className){
		//echo $className;//IndexController
		include APP_CONTROLLER_PATH.'/'.$className.'.class.php';
	}
	//创建默认控制器
	private static function _create_demo(){
		$path = APP_CONTROLLER_PATH . '/IndexController.class.php';
		$str = <<<str
<?php
class IndexController extends Controller{
	public function index(){
		echo "ok";
	}
}
?>
str;
		is_file($path) || file_put_contents($path, $str);
	}
	//实例化应用控制器
	private static function _app_run(){
		$c = isset($_GET['VAR_CONTROLLER'])?$_GET['VAR_CONTROLLER']:"Index";
		$a = isset($_GET['VAR_ACTION'])?$_GET['VAR_ACTION']:"index";
		define("CONTROLLER",$c);
		define("ACTION",$a);
		$c .= 'Controller';
		$obj = new $c();
		$obj->$a(); 
	}

	
	//载入用户自定义的库文件
	private static function _user_import(){
		$fileArr = C('AUTO_LOAD_FILE');
		if(is_array($fileArr)&& !empty($fileArr)){
			foreach ($fileArr as $v) {
				require_once COMMON_LIB_PATH.'/'.$v;
			}
		}
	}
	
	
	
}
?>