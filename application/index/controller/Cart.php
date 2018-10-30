<?php 
namespace app\index\controller;
/**
* 
*/
class Cart extends Common{
	public function addCart(){
		//dump(input());exit;
		$goods_id = input('goods_id/d',0);
		$mount = input('amount/d',0);
		$attr_ids = input('attr/a',[]);
		$goods_attr_ids = $attr_ids?implode(',',$attr_ids):'';
		//dump($goods_attr_ids);exit;
		$model = model('Cart');
		$model -> addCart($goods_id,$mount,$goods_attr_ids);
		$this -> success('ok');
	}

	//显示购物车
	public function index(){
		$model = model('cart');
		$data = $model->listData();
		//$data = $data -> toArray();
		//dump($data[0]['goods_info']);exit;
		$list = [];
		foreach ($data as $key => $value) {
			//格式化数据
			//$list[] = $value->toArray();
		}
		$result = $model -> totalMoney($data);
		//dump($result);exit;
		$this -> assign('total',$result);
		$this -> assign('list',$data);
		return $this -> fetch();
	}

	//删除
	public function del(){
		$model = model('cart');
		$result = $model -> del();
		if($result === false){
			$this -> error($model->getError());
			return false;
		}
		$this -> success('ok','cart/index');
	}

	



}




 ?>