<?php
class IndexController extends Controller{
	public function __empty(){
		echo "__empty";
	}
	public function index(){
	
		$data = K('Admin')->get_all_data();
		$this->display();
	}
}
?>