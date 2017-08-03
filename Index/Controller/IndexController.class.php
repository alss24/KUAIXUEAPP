<?php
class IndexController extends Controller{
	public function index(){
		$var= 'qiyun';
		$this->assign('var',$var);
		$this->display();
	}
}
?>