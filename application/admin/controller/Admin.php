<?php 
namespace app\admin\controller;
use think\Db;
/**
* 
*/
class Admin extends Common{
	public function add(){
		if($this -> request-> isGet()){
			$data = Db::name('role')->where('id','gt',1)->select();
			$this -> assign('data',$data);
			return $this -> fetch();
		}
		//接收添加用户
		$adminModel = model('Admin');
		$result = $adminModel->addUser();
		//var_dump($result);exit;
		if($result===false){
			$this -> error($adminModel->getError());
		}
		$this -> success('ok','index');
	}

	//显示列表
	public function index(){
		$adminModel = model('Admin');
		$data = $adminModel->listData();
		/*echo '<pre>';
		var_dump($data);exit;*/
		return view('index',['data'=>$data]);
	}

	//删除
	public function del(){
		$admin_id = input('id/d');
		if($admin_id<1){
			$this -> error('非法操作');
		}
		Db::name('admin')->where(['id'=>$admin_id])->delete();
		Db::name('admin_role')->where(['id'=>$admin_id])->delete();
		$this -> success('ok');
	}

	public function edit(){
		$user_id = input('id');
		if($user_id<=1){
			$this -> error('数据错误');
			return false;
		}
		if($this -> request -> isGet()){
			$role = Db::name('role')->select();
			$this -> assign('role',$role);
			$sql = "select a.userName,c.role_name,b.role_id from shop_admin a left join shop_admin_role b on a.id=b.admin_id left join shop_role c on b.role_id=c.id where a.id=$user_id";
			$data = Db::query($sql);
			$this -> assign('data',$data[0]);
			return $this -> fetch();
		}
		$_user = input();
		if($_user['passWord']){
			$_user['passWord']=md5($_user['passWord']);
		}else{
			unset($_user['passWord']);
		}
		$role_id = $_user['role_id'];
		unset($_user['role_id']);
		//var_dump($_user);exit;
		Db::name('admin')->where(['id'=>$_user['id']])->update($_user);
		Db::name('admin_role')->where(['admin_id'=>$_user['id']])->update(['role_id'=>$role_id]);
		$this -> success('ok','index');
	}

}






 ?>