<?php
namespace app\admin\controller;
use think\Controller;
class Common extends Controller{
	public $request;//保存request类对象
	public $is_check_rule = true;//设置标识看是否检查权限
	public $_user = [];//保存用户相关的信息
	public function __construct(){
		parent::__construct();
		
		if(!session('_user')){
			$this -> error('非法登录','admin/login/login');
		}
		//获取用户id
		$this -> _user['admin_id'] = session('_user');
		$role_info = db('admin_role') -> where(['admin_id'=>$this->_user['admin_id']])->find();
		if(!$role_info){
			$this -> error('角色信息错误','login/login');
		}
		$this -> _user['role_id'] = $role_info['role_id'];
		$rules = [];
		if($this -> _user['role_id']==1){
			//设置不效验
			$this -> is_check_rule = false;
			//查询所有的权限信息是否显示导航菜单
			$rules = db('rule')->select();
		}else{
			$rule_list = db('role_rule')->where(['role_id'=>$this -> _user['role_id']])->select();
			//根据权限id获取所有的的权限信息
			foreach ($rule_list as $key => $value) {
				$rule_ids[]=$value['rule_id'];
			}
			//找出$rule_ids内的权限
			$rules = db('rule')->where(['id'=>['in',$rule_ids]])->select();
		}
		foreach ($rules as $key => $value) {
			//将权限中需要显示的权限保存
			$this -> _user['rules'][] = strtolower($value['controller_name'].'/'.$value['action_name']);
			//将权限中需要显示的菜单保存到属性中
			if($value['is_show']==1){
				//??value
				$this -> _user['menus'][] = $value;
			}
		}
		if($this->is_check_rule){
			//后台首页的访问权限任何用户都可以访问
			$this -> _user['rules'][]='index/index';
			$this -> _user['rules'][]='index/top';
			$this -> _user['rules'][]='index/menu';
			$this -> _user['rules'][]='index/main';
			$action = strtolower(request()->controller().'/'.request()->action());
			if(!in_array($action,$this -> _user['rules'])){
				$this -> error('无权限访问','login/login');
			}
		}
	}
}




 ?>