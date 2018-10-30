<?php
namespace app\index\controller;
/**
* 下单
*/
class Order extends Common{
	//显示确认订单页面
	public function check(){
		$result = check_is_login();
		if(!$result){
			$url = input('url');
			$this -> error('请先登录',url('user/login').'?url='.$url);
			return false;
		}
		//显示收货人列表
		$userinfo = cookie('_userinfo');
		$user_id = $userinfo['id'];
		$address_ids = db('adress')->field('adress_id')->where(['user_id'=>$user_id])->select();
		$address = [];
		foreach ($address_ids as $key => $value) {
			$address[] = $value['adress_id'];
		}
		//dump($address);exit;
		$address_list = db('adress_detail')->where('id','in',$address)->select();
		//dump($address_list);exit;
		$this ->assign('address',$address_list);
		//获取购物车信息
		$cart_list = model('cart')->listData();
		//获取总额数
		$totalMoney = model('cart')->totalMoney($cart_list);
		$this -> assign('list',$cart_list);
		$this -> assign('total',$totalMoney);
		//dump($cart_list);exit;
		return $this -> fetch();
	}

	//保存收货地址
	public function address(){
		//dump(input());
		$userInfo= cookie('_userinfo');
		$user_id = $userInfo['id'];
		//dump($user_id);exit;
		db('adress_detail')->insert(input());
		$address_id = db('adress_detail')->getLastInsID();
		db('adress')->insert(['user_id'=>$user_id,'adress_id'=>$address_id]);
	}

	//获取地址中的一些信息
	public function getAddress(){
		$id = input('id');
		$selected = db('adress_detail')->where(['id'=>$id])->find();
		//dump($selected);exit;
		$this -> assign('selected',$selected);
		return  $this ->fetch();
	}

	//设置默认地址
	public function setDefault(){
		$id = input('id');
		db('adress_detail')->where(['default'=>1])->setField(['default'=>0]);
		db('adress_detail')->where(['id'=>$id])->setField(['default'=>1]);
		return true;
	}

	//支付
	public function pay(){
		//检查有没有登录
		//dump(input());exit;
		$result = check_is_login();
		if(!$result){
			$this -> error('请先登录','user/login');
			return false;
		}
		//订单号
		$order_id = time('Ymdmis').rand(100000,999999);
		//用户id
		$userinfo = cookie('_userinfo');
		$user_id = db('user')->field('id')->where(['username'=>$userinfo['username']])->find();
		$user_id = $user_id['id'];
		//获取订单列表
		$cart = model('Cart')->listData();
		//总金额
		$total = model('Cart')->totalMoney($cart);
		$data = [
			'user_id'=>$user_id,
			'order_id'=>$order_id,
			'address_id'=>input('address_id'),
			'pay'=>input('pay'),
			'money'=>$total['money'],
			'addtime'=>time()
		];
		db('order')->strict(false)->insert($data);
		$id = db('order')->getLastInsId();
		$list = [];
		foreach ($cart as $key => $value) {
			$list[] = [
				'order_id'=>$id,
				'goods_id'=>$value['goods_id'],
				'goods_count'=>$value['goods_count'],
				'goods_attr_ids'=>$value['goods_attr_ids'],
			];
		}
		if($list){
			db('order_detail')->strict(false)->insertAll($list);
		}
		require_once '../extend/pay/config.php';
		require_once '../extend/pay/pagepay/service/AlipayTradeService.php';
		require_once '../extend/pay/pagePay/buildermodel/AlipayTradePagePayContentBuilder.php';
		//商户订单号，商户网站订单系统中唯一订单号，必填
	    $out_trade_no = $order_id;

	    //订单名称，必填
	    $subject = 'test-pay';

	    //付款金额，必填
	    $total_amount = $total['money'];

	    //商品描述，可空
	    $body = 'desc-pay';
	    //构造参数
		$payRequestBuilder = new \AlipayTradePagePayContentBuilder();
		$payRequestBuilder->setBody($body);
		$payRequestBuilder->setSubject($subject);
		$payRequestBuilder->setTotalAmount($total_amount);
		$payRequestBuilder->setOutTradeNo($out_trade_no);

		$aop = new \AlipayTradeService($config);
		/**
	 * pagePay 电脑网站支付请求
	 * @param $builder 业务参数，使用buildmodel中的对象生成。
	 * @param $return_url 同步跳转地址，公网可以访问
	 * @param $notify_url 异步通知地址，公网可以访问
	 * @return $response 支付宝返回的信息
 	*/
	$response = $aop->pagePay($payRequestBuilder,$config['return_url'],$config['notify_url']);

	//输出表单
	var_dump($response);
	}

}




 ?>