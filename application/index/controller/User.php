<?php
namespace app\index\controller;
use think\Controller;
/**
* 
*/
class User extends Controller{

	//发送短信测试
	public function test(){
		dump(send_sms('18007955221',[111,30],1));
	}

	//发送短信,返回数据给前端的ajax
	public function sendSms(){
		$tel = input('tel');
		$code = rand(1000,9999);
		$result = send_sms($tel,[$code,10],1);
		if(!$result){
			return json(['status'=>0,'msg'=>'fail']);
		}
		session('telcode',['code'=>$code,'time'=>time(),'tel'=>$tel]);
		return json(['status'=>1,'msg'=>'ok']);
	}

	//注册用户
	public function regist(){
		if(request()->isGet()){
			return $this -> fetch();
		}
		$model = model('User');
		$result = $model->addUser();
		if($result === false){
			$this -> error($model->getError());
			return false;
		}
		$this -> success('ok','login');
	}

	//邮箱注册
	public function registEmail(){
		if(request()->isGet()){
			return $this -> fetch();
		}
		$model = model('user');

		$result = $model->registEmail();
		//dump($result);exit;
		if($result===false){
			$this -> error($model->getError());
			return false;
		}
		$this -> success('注册完成','login');
	}


	//登录
	public function login(){

		if(request()->isGet()){
			//$url = cookie('refer');
			//dump($url);exit;
			return $this -> fetch();
		}
		$model = model('User');
		$result = $model -> login();
		if($result === false){
			$this -> error($model->getError());
		}
		model('member')->upXp();
		//dump();exit;
		if(input('url')){
			$this -> redirect(input('url'));
		}
		
	}
	//确认注册
	public function active(){
		$key = input('key');
		require_once('../extend/Mem.php');
		$obj = \Mem::getObj();
		$result = $obj->get($key);
		if(!$result){
			$this -> error('连接失效','user/registEmail');
			return false;
		}
		User::where(['id'=>$result])->serField(['status'=>1]);
		echo 'ok';
	}
}




 ?>