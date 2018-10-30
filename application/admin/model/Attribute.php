<?php 
namespace app\admin\model;
use think\Model;
use think\Db;
class Attribute extends Model{
	//获取属性列表
	public function getList(){
		$type = Db::name('type') -> select();
		$attr = Attribute::all();
		
		foreach ($type as $k => $v) {
			$info[$v['id']] = $v;
		}
		//var_dump($info);exit;
		foreach ($attr as $k => $v) {
			$value = $v->toArray();
			$value['type_name']=$info[$v['type_id']]['type_name'];
			$data[] = $value;
		}
		return $data;
	}

	//获取属性
	public function getType($type_id){
		$data = Attribute::where(['type_id'=>$type_id])->select();
		/*echo '<pre>';
		var_dump($data);exit;*/
		
		foreach ($data as $k => $v) {
			$value = $v -> toArray();
			if($v['attr_input_type']==2){
				$value['attr_values'] = explode(',',$v['attr_values']);
			}
			$list[] = $value;
		}
		return $list;
	}
}





 ?>