<?php 
namespace app\index\model;
use think\Model;
/**
* 
*/
class Goods extends Model{
	//查询想响应板块
	public function getRec($filed){
		$key = 'index_rec_'.$filed;
		cache($key,null);
		$goodsInfo = cache($key);
		if(!$goodsInfo){
			$goodsInfo = model('goods')->where([$filed=>1,'is_sale'=>1,'is_del'=>1])->limit(5)->select();
			cache($key,$goodsInfo);
		}
		return $goodsInfo;
	}

	//获取品牌名
	public function getBrand(){
		$cate_id = input('id');
		return db('category')->where(['parent_id'=>$cate_id])->select();
	}

}




 ?>