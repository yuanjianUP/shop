<?php  
namespace app\index\model;
use think\Model;
/**
* 
*/
class User extends Model{
	//注册用户
	public function addUser(){
		$user = input();
		//获取验证码
		$telcode = session('telcode');
		//dump($telcode);exit;
		if($telcode['code'] != $user['telcode']){
			$this -> error = '短信验证码错误';
			return false;
		}

		//是否失效
		if(time()-$telcode['time']>600){
			//失效销毁
			session('telcode',null);
			$this -> error = '验证码失效';
			return false;
		}

		//电话号是否为注册电话
		if($telcode['tel']!=$user['tel']){
			$this -> error = '非法注册';
			return false;
		}
		if(User::get(['username'=>$user['username']])){
			$this -> error = '用户名重复';
			return false;
		}
		//验证手机号是否重复
		if(User::get(['tel'=>$user['tel']])){
			$this -> error = '电话号已注册';
			return false;
		}

		$user['salt'] = rand(100000,999999);
		$user['status'] = 1;
		$user['password'] = md6($user['password'],$user['salt']);
		User::allowField(true)->isUpdate(false)->save($user);
	}

	//登录
	public function login(){
		$user = input();
		$data = User::get(['username'=>$user['username']]);
		$user['id'] = $data['id'];
		if(!$data){
			$this -> error = '账号错误';
			return false;
		}
		if($data['password'] != md6($user['password'],$data['salt'])){
			$this -> error = '密码错误';
			return false;
		}
		if($data['status']==0){
			$this -> error = '账号未激活';
			return false;
		}
		$time = input('remember')?3600*3600*24*7:0;
		unset($user['password']);
		//$string = _encrypt(json_encode($user));
		cookie('_userinfo',$user,$time);
		//合并购物车
		model('Cart')->localCart();
	}

	//邮箱注册
	public function registEmail(){
		
		$user = input();
		//用户名唯一
		if(User::get(['username'=>$user['username']])){
			$this -> error = '用户名重复';
			return false;
		}
		//邮箱唯一
		if(User::get(['email'=>$user['email']])){
			$this -> error = '邮箱重复';
			return false;
		}
		//处理密码
		$user['salt'] = rand(100000,999999);
		$user['password'] = md6($user['password'],$user['salt']);
		User::allowField(true)->isUpdate(false)->save($user);
		$user_id = User::getLastInsId();
		$key = uniqid();
		$url = 'http://www.jshop.com/index/user/active.html?key='.$key;
		require_once('../extend/Mem.php');
		$obj = \Mem::getObj();
		$obj -> set($key,$user_id,1800);
		send_email($user['email'],$url);
	}
}





?>