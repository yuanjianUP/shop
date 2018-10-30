<?php 
namespace app\admin\controller;
use think\Db;
class Rule extends Common{
	public function add(){
		$ruleModel = model('Rule');
		if($this -> request -> isGet()){
			$rule = $ruleModel -> getRules();
			$this -> assign('rule',$rule);
			return $this -> fetch();
		}
		$ruleModel -> insert(input());
		$this -> success('ok','index');
	}
	//
	public function index(){
		$rule = model('Rule') -> getRules();
		$this -> assign('rule',$rule);
		return $this -> fetch();
	}
	//删除
	public function del(){
		$id = input('id');
		$model = model('Rule');
		$result = $model -> delRul($id);
		if($result===false){
			$this -> error($model->getError());
		}
		$this -> success('ok');
	}

	//编辑
	public function edit(){
		$id = input('id');
		if($this -> request->isGet()){
			$rule = model('Rule') -> getRules();
			$this -> assign('rule',$rule);
			$data = model('rule')->where(['id'=>$id])->find();
			$this -> assign('data',$data);
			//var_dump($id);exit;
			return $this -> fetch();
		}
		$result = model('rule')->edit();
		if($result===false){
			$this -> error(model('rule')->getError());
		}
		$this -> success('ok','index');
	}

	
}










 ?>