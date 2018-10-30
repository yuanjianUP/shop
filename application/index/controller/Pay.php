<?php 
namespace app\index\controller;
use think\Controller;
/**
* 支付回调
*/
class Pay extends Controller
{
	public function notifyUrl(){
		require_once '../extend/pay/config.php';
		require_once '../extend/pay/pagepay/service/AlipayTradeService.php';
		$arr=$_POST;
		$alipaySevice = new \AlipayTradeService($config); 
		$alipaySevice->writeLog(var_export($_POST,true));
		$result = $alipaySevice->check($arr);
		if(!$result){
			//验证失败
    		echo "fail";
		}
		//商户订单号
		$out_trade_no = $_POST['out_trade_no'];

		//支付宝交易号
		$trade_no = $_POST['trade_no'];

		//交易状态
		$trade_status = $_POST['trade_status'];

		$oderInfo = db('order')->where(['order_id'=>$out_trade_no])->find();
		if(!$oderInfo){
			echo 'fail';exit;
		}
		if($oderInfo){
			db('order')->where(['order_id'=>$out_trade_no])->setField('status',1);
		}
		echo 'success';exit;
	}
	//同步回调
	public function returnUrl(){
		require_once '../extend/pay/config.php';
		require_once '../extend/pay/pagepay/service/AlipayTradeService.php';
		$arr=$_GET;
		$alipaySevice = new \AlipayTradeService($config);
		$result = $alipaySevice->check($arr);
		if(!$result){
			echo '验证失败';
		}
		//商户订单号
		$out_trade_no = htmlspecialchars($_GET['out_trade_no']);
		//支付宝交易号
		$trade_no = htmlspecialchars($_GET['trade_no']);
		db('order')->where(['order_id'=>$out_trade_no])->setField('status',1);
		$this -> success('支付成功','cart/index');
	}
}





 ?>