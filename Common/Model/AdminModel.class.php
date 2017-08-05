<?php 
class AdminModel extends Model{
	public $table = 'admin';

	public function get_all_data(){
		return $this->all();
	}
}