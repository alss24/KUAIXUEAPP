<?php 
//此类用于将smarty将本框架进行相关联
class SmartyView{
	private static $smarty = NULL;
	public function __construct(){
		if(!is_null(self::$smarty)) return;
		$smarty = new Smarty();
		//配置模版目录
		$smarty->template_dir = APP_TPL_PATH.'/'.CONTROLLER.'/';

		//编译目录
		$smarty->compile_dir = APP_COMPILE_PATH;
		//缓存目录
		$smarty->cache_dir = APP_CACHE_PATH;
	
		//定界符
		$smarty->left_delimiter = C('LEFT_DELIMITER');
		$smarty->right_delimiter = C('RIGHT_DELIMITER');
		//是否开启缓存
		$smarty->caching = C('CACHE_ON');
		//缓存时间
		$smarty->cache_lifetime = C('CACHE_TIME');
		
		self::$smarty = $smarty;
	}


	protected function display($tpl){
		self::$smarty->display($tpl,$_SERVER['REQUEST_URI']);
	}

	//模版赋值
	protected function assign($var, $value){
	
		self::$smarty->assign($var,$value);
	}
}