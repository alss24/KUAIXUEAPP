<?php
class Controller{
	public function __construct(){
		echo "父类的构造函数必须执行";
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
}