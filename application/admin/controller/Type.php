<?php 
namespace app\admin\controller;
use think\Db;
class Type extends Common{
	//显示添加页面
	public function add(){
		if($this -> request -> isGet()){
			return $this -> fetch();
		}

		$result = Db::name('Type') -> insert(['type_name'=>input('type_name')]);
		$this -> success('ok','index');
	}

	//显示属性展示页面
	public function index(){
		$list = Db::name('type') -> select();
		//var_dump($list);exit;
		$this -> assign('list',$list);
		return $this -> fetch();
	}

	//删除
	public function del(){
		$id = input('id');
		Db::name('type')->where(['id'=>$id])->delete();
		$this -> success('ok','index');
	}

	//编辑
	public function edit(){
		$obj = Db::name('type');
		if($this -> request -> isGet()){
			$info = $obj -> where(['id'=>input('id')])->find();
			$this -> assign('info',$info);
			return $this -> fetch();
		}
		$obj -> where(['id'=>input('id')])->update(['type_name'=>input('type_name')]);
		$this -> success('ok','index');
	}
}




 ?>