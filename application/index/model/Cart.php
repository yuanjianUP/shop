<?php 
namespace app\index\model;
use think\Model;
/**
* 
*/
class Cart extends Model{
	//添加购物车
	public function addCart($goods_id,$mount,$goods_attr_ids){
		$userinfo = cookie('_userinfo');
		
		if($userinfo){
			//用户已登录
			$user_id = db('user')->field('id')->where(['username'=>$userinfo['username']])->find();
			$map = [
				'goods_id'=>$goods_id,
				'goods_attr_ids'=>$goods_attr_ids,
				'user_id'=>$user_id['id'],
			];
			//dump($map);exit;
			if(Cart::get($map)){
				Cart::where($map)->setInc('goods_count',$mount);
			}else{
				$map['goods_count'] = $mount;
				Cart::isUpdate(false)->save($map);
			}
		}else{
			//没有登录
			$cate = cookie('cate')?cookie('cate'):[];
			$key = $goods_id.'-'.$goods_attr_ids;
			if(array_key_exists($key,$cate)){
				$cate[$key] += $mount;
			}else{
				$cate[$key] = $mount;
			}
			cookie('cart',$cate,3600*24*7);
		}
	}
	//购物车列表
	public function listData(){
		$userinfo = cookie('_userinfo');
		$user_id = db('user')->field('id')->where(['username'=>$userinfo['username']])->find();

		$list = [];
		if($userinfo){
			//已登录
			$list = model('cart')->where(['user_id'=>$user_id['id']])->select();
		}else{
			//未登录
			$cart = cookie('cart')?cookie('cart'):[];
			//dump($cart);exit;
			foreach ($cart as $key => $value) {
				$tmp = explode('-',$key);
				$list[] = [
					'goods_id'=>$tmp[0],
					'goods_count'=>$value,
					'goods_attr_ids'=>$tmp[1]
				];
			}
		}
		foreach ($list as $key => $value) {
			//获取商品信息
			$list[$key]['goods_info'] = db('goods')->where(['id'=>$value['goods_id']])->find();
			//获取商品属性
			$list[$key]['attr'] = db('goods_attr')->alias('a')->field('a.attr_value,b.attr_name')->join('shop_attribute b','a.attr_id=b.id','left')->where('a.id','in',$value['goods_attr_ids'])->select();
		}
		return $list;
	}

	
	/**
	 * [totalMoney 合计价格]
	 * @param  [type] $data [购物车列表]
	 * @return [type]       [返回购物车数量和总金额]
	 */
	public function totalMoney($data){
		$money = $number = 0;
		foreach ($data as $key => $value) {
			$number += $value['goods_count'];
			$money += $value['goods_count']*$value['goods_info']['shop_price'];
		}
		return ['number'=>$number,'money'=>$money];
	}
	//删除
	public function del(){
		$userinfo = cookie('_userinfo');
		$user_id = db('user')->field('id')->where(['username'=>$userinfo['username']])->find();
		$user_id = $user_id['id'];
		//dump($user_id);exit;
		$goods_id = input('goods_id/d');
		$goods_attr_ids = input('goods_attr_ids');
		if($userinfo){
			//登录状态下
			model('cart')->where(['goods_id'=>$goods_id,'user_id'=>$user_id,'goods_attr_ids'=>$goods_attr_ids])->delete();
		}else{
			$cart = cache('cart')?cache('cart'):[];
			$key = $goods_id.'-'.$goods_attr_ids;
			unset($cart[$key]);
			cookie('cart',$cart,3600*24*7);
		}
	}

	//cookie购物车
	public function localCart(){
		$cart = cookie('cart')?cookie('cart'):[];
		$userinfo = cookie('_userinfo');
		$user_id = db('user')->field('id')->where(['username'=>$userinfo['username']])->find();
		$user_id = $user_id['id'];
		//dump($userinfo);exit;
		foreach ($cart as $key => $value) {
			$tmp = explode('-',$key);
			$where = [
				'user_id'=>$user_id,
				'goods_id'=>$tmp[0],
				'goods_attr_ids'=>$tmp[1],
			];
			$info = db('cart')->where($where)->find();
			if($info){
				db('cart')->where($where)->update(['goods_count'=>$value]);
			}else{
				$where['goods_count']=$value;
				db('cart')->insert($where);
			}
		}

		cookie('cart',null);
	}

}




 ?>