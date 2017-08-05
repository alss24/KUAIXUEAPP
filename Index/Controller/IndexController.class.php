<?php
class IndexController extends Controller{
	public function __empty(){
		echo "__empty";
	}
	public function index(){
		
		$data = M('admin')->field('id,email')->where('id>12')->limit('5')->order('id asc')->all();
		p($data);
	}
}
?>