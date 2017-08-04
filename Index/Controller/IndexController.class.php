<?php
class IndexController extends Controller{
	public function index(){
		$code = new Code();
		$code->show();
	}
}
?>