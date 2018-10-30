<?php
namespace app\admin\model;
use think\Model;
class Admin extends Model{
	public function verify(){
		/*//检查验证码
		$obj = new \think\captcha\Captcha();
		if(!$obj -> check(input('captcha'))){
			$this -> error = '验证码错误' ;
			return false;
		}*/
		$username = input('username');
		$password = md5(input('password'));
		$userInfo = Admin::get(['userName'=>$username,'passWord'=>$password]);
		//var_dump($userInfo);exit;
		if(!$userInfo){
			$this -> error = '数据错误';
			return false;
		}
//var_dump($userInfo->getAttr('id'));exit;
		session('_user',$userInfo->getAttr('id'));
	}
	public function addUser(){
		$data = input();
		$rule = [
			'userName' => 'unique:admin',
		];
		$obj = new \think\Validate($rule);
		if(!$obj->check($data)){
			$this -> error = $obj->getError();
			return false;
		}
		$data['passWord']=md5($data['passWord']);
		Admin::startTrans();
		try{
			//return $data;
			Admin::allowField(true)->isUpdate(false)->save($data);
			$admin_id = Admin::getLastInsId();
			db('admin_role')->insert(['admin_id'=>$admin_id,'role_id'=>$data['role_id']]);
			Admin::commit();
		}catch(\Exception $e){
			Admin::rollBack();
		}
	}

	public function listData(){
		$sql = "SELECT a.*,b.role_id,c.role_name from shop_admin a left JOIN shop_admin_role b on a.id=b.admin_id LEFT JOIN shop_role c on c.id=b.role_id";
		return Admin::query($sql);
	}
}





 ?>