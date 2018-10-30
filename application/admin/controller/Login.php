<?php
namespace app\admin\controller;
use think\Controller;
class Login extends Controller{
	public function login(){
	 	if($this -> request->isGet()){
	 		return $this -> fetch();
	 	}
	 	$model = model('Admin');
	 	$result = $model -> verify();
	 	//var_dump($result);exit;
	 	if($result === false){
	 		//$model是取得model下的error
	 		$this -> error($model -> getError());
	 	}
	 		$this -> success('密码正确','admin/index/index');
	 }
	 //生成验证码
	public function captcha(){
	 	$obj = new \think\captcha\Captcha(['length'=>4]);
	 	return $obj -> entry();
	 }
	 //生成秘钥
	 public function MK5(){
	 	return md5(input('key'));
	 }
}



?>