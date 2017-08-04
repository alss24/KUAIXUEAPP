<?php
class IndexController extends Controller{
	public function __empty(){
		echo "__empty";
	}
	public function index(){
		$code = new Code();
		$code->show();
	}
}
?>