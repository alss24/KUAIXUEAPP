<?php
class IndexController extends Controller{
	public function __empty(){
		echo "__empty";
	}
	public function index(){
		$link = new Model('user');
		$sql = 'select * from t_user';
		$data = $link->query($sql);
		p($data);
	}
}
?>