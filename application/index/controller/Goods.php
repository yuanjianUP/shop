<?php
namespace app\index\controller;
use think\Db;
/**
* 
*/
class Goods extends Common{
	//商品详情
	public function detail(){
		$goods_id = input('goods_id');
		$goods_list = model('goods')->where(['id'=>$goods_id])->find();
		if(!$goods_list){
			$this -> error('非法请求');
		}
		$goods_list = $goods_list->toArray();
		if($goods_list['is_sale']==0 || $goods_list['is_del']==0){
			$this -> error('商品已下架');
			return false;
		}
		$this -> assign('data',$goods_list);
		$img = Db::name('goods_img')->where(['goods_id'=>$goods_id])->select();
		//$img = $img['goods_img'];
		//dump($img);exit;
		$this -> assign('imgs',$img);

		//获取商品属性
		$attr = Db::query("select a.*,b.attr_name,b.attr_type from shop_goods_attr a left join shop_attribute b on a.attr_id = b.id where a.goods_id=$goods_id");
		$this -> assign('attr',$attr);
		//dump($attr);exit;
		//分离唯一属性
		 $unique = [];
		 $redio = [];
		foreach ($attr as $key => $value) {
			if($value['attr_type']==1){
				//唯一属性
				$unique[] = $value;
			}else{
				//多选
				$redio[$value['attr_id']][] = $value;
			}
		}
		//dump($redio);exit;
		$this -> assign('unique',$unique);
		$this -> assign('redio',$redio);
		return $this -> fetch();
	}
	//筛选商品
	public function list(){
		$model = model('goods');
		$data = $model -> select();
		$this -> assign('data',$data);
		//获取品牌名
		$Brand = $model -> getBrand();
		$this -> assign('brand',$Brand);
		return $this -> fetch();
	}
	//
}





 ?>