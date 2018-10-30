<?php
namespace app\admin\controller;
use app\admin\model;
use think\Db;
class Goods extends Common{

	public $_model;
	public function model(){
		if(!isset($this -> _model)){
			$this -> _model = model('Goods');
		}
		return $this -> _model;
	}
	public function del_img(){
		$img_id = input('img_id/d',0);
		//return $img_id;
		$result = db('goods_img') -> where(['id'=>$img_id]) -> delete();
		if($result===false){
			return json(['status'=>0,'msg'=>'error']);
		}
		return json(['status'=>1,'msg'=>'ok']);
	}
	//显示添加页面
	public function add(){
		if($this->request->isGET()){
			$type = Db::name('type') -> select();
			$this -> assign('type',$type);
			$cate = model('Category');
			$data = $cate -> getCate(0,true);
			$this -> assign('data',$data);
			return $this -> fetch();
		}
		$goods = model('Goods');
		$result = $goods -> goodsAdd();
		//dump($result);exit;
		if($result === false){
			$this -> error($goods -> getError());
		}else{
			$this -> success('成功添加','add');
		}
	}
	
	//显示类别页面
	public function show(){
		$model = model('goods');
		$result = $model -> getList();
		if($result === false){
			$this -> error($model -> getError());
			return false;
		}
		//var_dump($result);exit;
		$model = model('Category');
		$cate = $model -> getCate(0,true);
		$this -> assign('aim',input('cat_id'));
		$this -> assign('cate',$cate);
		$this -> assign('list',$result);
		return $this -> fetch();
	}
	//点击切换图片并修改
	public function changeState(){
		$result = $this -> model() -> changeState();
		//echo $result;exit;
		if($result === false){
			return json(['state'=>0,'msg'=>$this ->model() ->getError()]);
		}
		return json(['state'=>1,'msg'=>'ok','imgState'=>$result]);
	}
	//回收站
	public function del(){
		$id = input('id');
		$result = $this -> model() -> where(['id'=>$id]) -> setField('is_del',0);
		$this->success('成功删除','show');
	}

	//显示回收站
	public function recycle(){
		$tree = model('Category') -> getCate();
		$this -> assign('cate',$tree);
		$list = $this -> model() -> getList(0);
		$this -> assign('list',$list);
		return $this -> fetch();
	}

	//还原删除项
	public function rollBack(){
		$id = input('id');
		$this -> model()->where(['id'=>$id])->setField('is_del',1);
		$this -> success('ok','show');
	}

	//彻底删除
	public function noBack(){
		$id = input('id');
		$this -> model() -> where(['id'=>$id]) -> delete();
		$this -> success('已删除','show');
	}

	//编辑
	public function edit(){
		if($this->request->isGet()){
			$id = input('id');
			//显示相册
			$img = db('goods_img')->where(['goods_id'=>$id])->select();
			//var_dump($img);exit;
			$this -> assign('img',$img);
			//获取类型表数据
			$type = Db::name('type') -> select();
			$this -> assign('type',$type);
			//获取属性值和属性列表
			$goodsAttr = model('goods')->getGoodsAttr($id);
			/*echo '<pre>';
			var_dump($goodsAttr);exit;*/
			$this -> assign('goodsAttr',$goodsAttr);
			//获取goods_id为$id的商品数据
			$data = $this -> model() -> get($id);
			$this -> assign('data',$data);
			$cate = model('Category') -> getCate();
			$this -> assign('cate',$cate);
			//var_dump($cate);exit;
			return $this -> fetch();
		}
		$result = $this -> model() -> editGoods();
		if($result === false){
			$this -> error($this -> model() ->getError());
			return false;
		}
		$this -> success('ok','show');
	}

	//获取商品属性
	public function showAttr(){
		$type_id = input('type_id');
		if($type_id<=0){
			return '请输入正确值';
		}
			$attr = model('Attribute') -> getType($type_id);
			/*echo '<pre>';
			var_dump($attr);*/
			$this -> assign('attr',$attr);
			return $this -> fetch();
	}

	//搜索功能
	/*public function searchGoods(){
		$model = model('goods');
		$result = $model -> searchData();
		//echo '<pre>';
		//var_dump($result);exit;
		if($result === false){
			$this -> error($model -> getError());
		}
		$this -> success('查找到','show');

	}*/

}



 ?>