<?php
namespace app\admin\controller;
use think\Db;
/**
*
*/
class Role extends Common{
	//添加页面
	public function add(){
		if($this->request->isGet()){
			return $this -> fetch();
		}
		//return input('role_name');exit;
		$result = Db::name('role')->insert(['role_name'=>input('role_name')]);
		if(!$result){
			$this -> error('用户名插入失败');
			return false;
		}
		$this -> success('ok','index');
	}
	//显示展示页面
	public function index(){
		$data = Db::name('role')->select();
		$this -> assign('data',$data);
		return $this -> fetch();
	}

	//删除
	public function del(){
		$role_id = input('id/d',0);
		if($role_id<=1){
			$this -> error('admin不能删除');
		}
		Db::name('role')->where(['id'=>$role_id])->delete();
		$this -> success('ok',index);
	}

	//编辑
	public function edit(){
		$role_id = input('id/d',0);
		if($role_id<=0){
			$this -> error('非法操作');
		}
		if($this -> request->isGet()){
			$data = Db::name('role')->where(['id'=>$role_id])->find();
			//var_dump($data);exit;
			$this -> assign('data',$data);
			return $this -> fetch();
		}
		Db::name('role')->where(['id'=>$role_id])->update(['role_name'=>input('role_name')]);
		$this -> success('ok','index');
	}

	//分配角色
	public function disfetch(){
		if($this ->request->isGet()){
			$rules = model('rule') -> getRules();
			$this -> assign('rules',$rules);
			$hasRule = db('role_rule')->field('rule_id')->where(['role_id'=>input('id')])->select();
			foreach ($hasRule as $key => $value) {
				$rule_ids[] = $value['rule_id'];
			}
			$rule_ids = implode(',',$rule_ids);
			$this -> assign('hasRule',$rule_ids);
			return $this -> fetch();
		}
		//var_dump(input());exit;
		$role_id = input('id/d',0);
		if($role_id<=1){
			$this -> error = "参数错误";
		}
		$rule_list = input('rule/a');
		$list = [];
		foreach ($rule_list as $key => $value) {
			$list[] = ['role_id'=>$role_id,'rule_id'=>$value];
		}
		db('role_rule')->where(['role_id'=>$role_id])->delete();
		if($list){
			db('role_rule')->insertAll($list);
		}
		$this -> success('ok','index');
	}

}






 ?>