<?php
namespace app\admin\model;
use think\Model;
/**
* 权限模型
*/
class Rule extends Model{
	public function getRules($id=0){
		$data = Rule::all();
		//var_dump($data);
		$list = model('Category')->getCate_1($data,$id,$clearTree=false,$exclude=null);
		return $list;
	}

	//删除
	public function delRul($id){
		if($id<=0){
			$this -> error ='参数错误';
			return false;
		}
		$result = model('rule')->where(['parent_id'=>$id])->select();
		if($result){
			$this -> error = '存在子类';
			return false;
		}
		model('rule')->where(['id'=>$id])->delete();
	}

	public function edit(){
		$data = input();
		$tree = $this -> getRules($data['id']);
		//var_dump($tree);exit;
		foreach ($tree as $key => $value) {
			//不能设置他的子类为父类
			if($value['id'] == $data['parent_id']){
				$this -> error = '上级权限设置错误';
				return false;
			}
			//不可以设置上级为自己
			if($data['parent_id']==$data['id']){
				$this -> error = '上级权限设置错误';
				return false;
			}
			$this -> update($data);
		}
		//var_dump($value);exit;
	}

	
}