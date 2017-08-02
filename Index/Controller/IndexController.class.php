<?php
class IndexController extends ComController{
	public function __auto(){
		echo "子类的初始化方法";
	}
	public function index(){
		//$this->success('成功');
		Log::write('hello log');
	}
}
?>