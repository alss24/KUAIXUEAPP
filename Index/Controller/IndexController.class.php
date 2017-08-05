<?php
class IndexController extends Controller{
	public function __empty(){
		echo "__empty";
	}
	public function index(){
		
		$data = M('admin')->where('id=15')->delete();
		p($data);
	}
}
?>