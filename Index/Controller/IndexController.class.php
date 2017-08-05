<?php
class IndexController extends Controller{
	public function __empty(){
		echo "__empty";
	}
	public function index(){
		if(IS_POST){
			M('admin')->where()->update();
			$this->success('添加成功');
		}
		$this->display();
	}
}
?>