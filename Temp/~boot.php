<?php 
//打印函数
function p($arr){
	echo "<pre>";
	print_r($arr);
	echo "</pre>";
}

/*加载配置项
1.首先加载系统默认配置项C($sysConfig) 之后加载用户自定义配置项C($userConfig),用户自定义的配置项优先级高于系统默认配置项
2.读取配置项
C('CODE_LEN')
3.临时动态改变配置项
C('CODE_LEN',10)
4.读取所有配置项
C()
*/
function C($var = NULL,$value = NULL){
	static $config = array();
	//加载配置项
	if(is_array($var)){
		$config = array_merge($config,array_change_key_case($var,CASE_UPPER));
		return;
	}

	if(is_string($var)){
		$var = strtoupper($var);
		//两个参数传递
		if(!is_null($value)){
			//临时动态改变配置项
			$config[$var] = $value;
			return;
		}
		//读取配置项
		return isset($config[$var])?$config[$var]:NULL;
	}
	//读取所有配置项
	if(is_null($var)&&is_null($value)){
		return $config;
	}
}//系统日志类
class Log{
	public static function write($msg, $level='ERROR',$type=3,$dest=NULL){
		if(!C('SAVE_LOG')) return;
		if(is_null($dest)){
			$dest = LOG_PATH.'/'.date('Y_m_d').".log";
		}

		if(is_dir(LOG_PATH)){
			error_log("[TIME]:".date("Y-m-d H:i:s")."{$level}:{$msg}\r\n",$type,$dest);//type=3表示以文本的形式保存
		}
	}
}class Controller{
	public function __construct(){

		if(method_exists($this, '__init')){
			$this->__init();
		}
		if(method_exists($this, '__auto')){
			$this->__auto();
		}
	}
	protected function success($msg,$url=NULL,$time=3){
		$url = $url?"window.location.href='".$url."'":"window.history.back(-1)";
		include APP_TPL_PATH."/success.html";
		die;
	}
}final class Application{
	public static function run(){
		
		self::_init();
		self::_set_url();
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

		$c .= 'Controller';
		$obj = new $c();
		$obj->$a(); 
	}

	

	
	
	
}
