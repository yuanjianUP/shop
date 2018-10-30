<?php
namespace app\admin\controller;
use think\Db;
class Attribute extends Common{
	//添加属性
	public function add(){
		$obj = Db::name('type');
		if($this -> request -> isGet()){
			$data = $obj->select();
			$this -> assign('data',$data);
			return $this -> fetch();
		}
		$put = input();
		if($put['attr_input_type']==1){
			unset($put['attr_values']);
		}else{
			if(!$put['attr_values']){
				$this -> error('为下拉框时必须有默认值');
			}
		}
		Db::name('Attribute') -> insert($put);
		$this -> success('ok');
	}

	//属性列表
	public function index(){
		$model = model('attribute');
		$data = $model -> getList();
		$this -> assign('data',$data);
		return $this -> fetch();
	}

	//删除
	public function del(){
		Db::name('Attribute')->where(['id'=>input['id']])->delete();
		$this -> success('ok');
	}

	//编辑
	public function edit(){
		if($this -> request -> isGet()){
			$list = Db::name('type')->select();
			$this -> assign('list',$list);
			$info = model('Attribute')->get(input('id'));
			$this -> assign('info',$info);
			return $this -> fetch();
		}
		$data = input();
		if($data['attr_input_type']==1){
			unset($data['attr_values']);
		}else{
			if(!$data['attr_values']){
				$this -> error('属性默认值必须');
			}
		}
		Db::name('Attribute')->update($data);
		$this -> success('ok','index');
	}

}






 ?>