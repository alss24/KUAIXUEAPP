<?php

//错误提示信息
function halt($error,$level='ERROR',$type=3,$dest=NULL){
	if(is_array($error)){
		Log::write($error['message'],$level,$type,$dest);
	}else{
		Log::write($error,$level,$type,$dest);
	}

	$e = array();
	if(DEBUG){
		//开启调试模式
		if(!is_array($error)){
			$trace = debug_backtrace();
			$e['message'] = $error;
			$e['file'] = $trace[0]['file'];
			$e['line'] = $trace[0]['line'];
			$e['class'] = isset($trace[0]['class']) ? $trace[0]['class'] : '';
			$e['function'] = isset($trace[0]['function']) ? $trace[0]['function'] : '';

			ob_start();//开启缓冲区
			debug_print_backtrace();//打印错误信息
			$e['trace'] = htmlspecialchars(ob_get_clean());//实体化从缓冲区获取的信息
		}else{
			$e = $error;
		}
	}else{
		if($url = C('ERROR_URL')){
			go($url);
		}else{
			$e['message'] = C('ERROR_MSG');
		}
	}

	include DATA_PATH.'/Tpl/halt.html';
	die;
}

//打印用户自定义的常量
function print_const(){
	$const = get_default_constants(true);//true表示返回带键值的数组
	p($const['user']);

}






//打印函数
function p($arr){
	if(is_bool($arr)){
		var_dump($arr);
	}else if(is_null($arr)){
		var_dump($arr);
	}else{
		echo "<pre style='padding:10px;border-radius:5px;background:#f5f5f5;border:1px solid #ccc;font-size:15px;'>".print_r($arr,true)."</pre>";
	}
}



//跳转函数
function go($url,$time=0,$msg=''){
	/*headers_sent() 函数检查 HTTP 标头是否已被发送以及在哪里被发送。
	如果报头已发送，则返回 true，否则返回 false。*/
	if(!headers_sent()){
		$time==0?header("Location:".$url):header("refresh:{$time};url={$url}");
		die($msg);
	}else{
		echo "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
		if($time) die($msg);
	}
}
/*加载配置项
1.首先加载系统默认配置项C($sysConfig) 之后加载用户自定义配置项C($userConfig),用户自定义的配置项优先级高于系统默认配置项
2.读取配置项
C('CODE_LEN')
3.临时动态改变配置项
C('CODE_LEN',10)
4.读取所有配置项
C()
*/
function C($var = NULL,$value = NULL){
	static $config = array();
	//加载配置项
	if(is_array($var)){
		$config = array_merge($config,array_change_key_case($var,CASE_UPPER));
		return;
	}

	if(is_string($var)){
		$var = strtoupper($var);
		//两个参数传递
		if(!is_null($value)){
			//临时动态改变配置项
			$config[$var] = $value;
			return;
		}
		//读取配置项
		return isset($config[$var])?$config[$var]:NULL;
	}
	//读取所有配置项
	if(is_null($var)&&is_null($value)){
		return $config;
	}
}
?>