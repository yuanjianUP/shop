<?php 
namespace app\admin\model;
use think\Model;
use think\Db;
class Category extends Model{
	public function getCate_1($arr,$id=0,$clearTree=false,$exclude=null){
		//$arr  = Db::name('category')->where('id','<>','0')->select();
		return  $this -> getCateTree($arr,$id,0,$exclude,$clearTree);
	}
	//获取分类
	public function getCate($id=0,$clearTree=false,$exclude=null){
		$arr  = Db::name('category')->where('id','<>','0')->select();
		/*echo '<pre>';
		print_r($arr);exit;*/
		return  $this -> getCateTree($arr,$id,0,$exclude,$clearTree);
	}
	//无限极分类
	 function getCateTree($arr,$pid,$lv=0,$exclude=null,$clearTree=false){
		static $tree=[];
		if($clearTree){
			$tree = [];
		}
		foreach ($arr as $v) {
			if($v['id']==$exclude){
				continue;
			}
			if($v['parent_id']==$pid){
				$v['lv']=$lv;
				$tree[]=$v;
				$this -> getCateTree($arr,$v['id'],$lv+1);
			}
		}
		return $tree;
	}
	//删除分类
	public function del($id){
		if($id<=0){
			$this -> error = '参数错误';
		}
		$cateChild = Category::get(['parent_id'=>$id]);
		if($cateChild){
			$this -> error = '参数下有子分类不能删除';
			return false;
		}
		Category::where('id','=',$id)->delete();
	}
	//编辑分类
	public function edit(){
		$id = input('id');
		$cateID = input('parent_id');
		$result = Category::where('id',$id)->update(['parent_id'=>$cateID]);
		if(!$result){
			$this -> error = '修改失败';
			return false;
		}
	}
}





 ?>