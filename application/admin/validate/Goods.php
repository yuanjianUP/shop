<?php
namespace app\admin\validate;
use think\Validate;
class Goods extends Validate{
	protected $rule = [
		'goods_name|商品名称唯一'	=>	'require',
		'cate_id|商品类别'		=>	'require|gt:0',
		'shop_price|商品价格'	=>	'require|gt:0',
		'market_price|市场价格'	=>	'require|checkGoodsMk',
	];
	public function checkGoodsMk($value,$rule,$data){
		if($value>$data['shop_price']){
			return true;
		}else{
			return false;
		}
	}
}


 ?>