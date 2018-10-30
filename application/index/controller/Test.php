<?php
namespace app\index\controller;
use think\Controller;
/**
* 
*/
class Test extends Controller{
	public function test(){
		session('code_id',['msg'=>1]);
	}
	function test3(){
	send_email('1587229752@qq.com','这是一个测试');
}
	function test4(){
		$obj = \Mem::getObj();
		$obj -> set('jie','123',1800);
	}




}




 ?>