<?php
class IndexController extends Controller{
	public function __empty(){
		echo "__empty";
	}
	public function index(){
	
		/*$smarty = new Smarty();
		p($smarty);*/
		$this->assign('var','test');
		$this->display();
	}
}
?>