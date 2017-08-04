<?php
class IndexController extends Controller{
	public function __empty(){
		echo "__empty";
	}
	public function index(){
		
		$data = M('user')->all();
		p($data);
	}
}
?>