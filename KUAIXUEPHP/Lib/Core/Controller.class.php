<?php
class Controller extends SmartyView{

	//用于存放模版赋值的键值对
	private $var = array();

	//构造函数
	public function __construct(){
		if(C('SMARTY_ON')){
			//如果开启了smarty则调用父类的构造函数
			parent::__construct();
		}
		
		if(method_exists($this, '__init')){
			$this->__init();
		}
		if(method_exists($this, '__auto')){
			$this->__auto();
		}
	}
	//成功提示方法
	protected function success($msg,$url=NULL,$time=3){
		$url = $url?"window.location.href='".$url."'":"window.history.back(-1)";
		include APP_TPL_PATH."/success.html";
		die;
	}

	//模版赋值
	protected function assign($var = NULL, $value=NULL){
		$this->var[$var] = $value;
	}


	protected function get_tpl($tpl){
		if(is_null($tpl)){
			$path = APP_TPL_PATH.'/'.CONTROLLER.'/'.ACTION.'.html';
		}else{
			$suffix = strrchr($tpl,'.');// strrchr 搜索 第二个字符 在字符串中的位置，并返回从该位置到字符串结尾的所有字符
			$tpl = empty($suffix)?$tpl.'.html':$tpl;//如果没有扩展名则默认是html
			$path = APP_TPL_PATH.'/'.CONTROLLER.'/'.$tpl;

		}
		return $path;
	}
	//模版显示
	protected function display($tpl=NULL){
		$path = $this->get_tpl($tpl);
		if(!is_file($path)) halt($path.'模版文件不存在');
		if(C('SMARTY_ON')){
			//如果开启了smarty则调用父类的构造函数
			parent::display($path);
		}else{
			extract($this->var);
			include $path;
		}
		
	}

}
?>