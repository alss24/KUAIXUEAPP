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
		
		switch (true) {
			//判断是否是控制器,例如：IndexController
			case strlen($className)>10 && substr($className,-10)=='Controller':
				$path = APP_CONTROLLER_PATH.'/'.$className.'.class.php';

				if(!is_file($path)) {
					//用户可以自定义一个EmptyController文件，防止用户胡乱输入控制器名称
					$emptyPath = APP_CONTROLLER_PATH.'/EmptyController.class.php';
					if(is_file($emptyPath)){
						include $emptyPath;
						return;
					}else{
						halt($path.'控制器未找到');
					}
					
				}
				include $path;
				break;
			
			default:
				//工具类
				$path = TOOL_PATH.'/'.$className.".class.php";
				if(!is_file($path)) halt($path.'类未找到');
				include $path;
				break;
		}
		
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
	
		$c = isset($_GET[C('VAR_CONTROLLER')])?$_GET[C('VAR_CONTROLLER')]:"Index";
		$a = isset($_GET[C('VAR_ACTION')])?$_GET[C('VAR_ACTION')]:"index";
		define("CONTROLLER",$c);
		define("ACTION",$a);

		$c .= 'Controller';
		if(class_exists($c)){//先查看一下用户地址栏中要访问的控制器是否存在
			$obj = new $c();
			$obj->$a();
		}else{
			$obj = new EmptyController();
			$obj->index();
		}
		 
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