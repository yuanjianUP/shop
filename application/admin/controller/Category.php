<?php 
namespace app\admin\controller;
use think\Db;
//use app\admin\model\Category;
class Category extends Common{
	//显示列表页
	public function index(){
		$model = model('Category');
		$data = $model -> getCate();
		$this -> assign('data',$data);
		return $this -> fetch();
	}
	//编辑
	public function edit(){
		$model = model('Category');
		if($this -> request -> isGet()){
			$info = $model -> get(input('id'));
			$this -> assign('info',$info);
			$result = $model -> getCate(input('id'));
			$this -> assign('cate',$result);
			return $this -> fetch();
		}
		$result = $model -> edit();
		if($result===false){
			$this -> error($model -> getError());
			return false;
		}
		$this->success('成功添加','index');

	}
	//删除分类
	public function del(){
		$id = input('id',0);
		$model = model('Category');
		$result = $model -> del($id);
		if($result === false){
			$this ->error($model -> getError());
			return false;
		}
		$this -> success('成功','index');
	}
	//显示添加页
	public function add(){
		$model = Model('Category');
		$insert = url('insert');
		$cateDate = $model -> getCate();
		$this -> assign('cateDate',$cateDate);
		/*echo '<pre>';
		print_r($cateDate);exit;*/
		$this -> assign('insert',$insert);
		return $this -> fetch();
	}
	//插入数据
	public function insert(){
		$name = input('cname');
		$parent_id = input('parent_id');
		$recomment = input('isrec');
		$data = [
			'cname'=>$name,
			'parent_id'=>$parent_id,
			'recomment'=>$recomment
		];
		$result = Db::name('category')->insert($data);
		if($result){
			$this -> success('添加成功','add');
		}else{
			$this -> error('添加失败','add');
		}
	}
}




 ?>