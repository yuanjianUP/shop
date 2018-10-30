<?php 
namespace app\index\controller;
use think\Controller;
/**
* 
*/
class Common extends Controller{
	public function __construct(){
		parent::__construct();
		$refer = request()->pathinfo();
		cookie('refer',$refer);
		$user = cookie('_userinfo');
		$this -> assign('userInfo',$user);
		$category = $this -> getCateTree();
		$this -> assign('category',$category);
		//cookie('refer',requset()->url());
		//dump(cookie('refer'));exit;
	}

	//获取商品的分类
	public function getCateTree(){
		$category = cache('category');
		if(!$category){
			$data = db('category')->select();
			$category = get_tree($data,0);
			cache('category',$category);
		}
		//dump($category);
		return $category;
	}

	//判断有没有登录


}





 ?>