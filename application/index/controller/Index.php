<?php
namespace app\index\controller;

class Index extends Common{
   public function index(){
   		$goodsModel = model('Goods');
   		$hot = $goodsModel -> getRec('is_hot');
   		$this -> assign('hot',$hot);
   		$new = $goodsModel -> getRec('is_new');
   		$this -> assign('new',$new);
		return $this -> fetch();
	}
	//退出登录
	public function loginOut(){
		cookie('_userinfo',null);
		$this -> success('成功退出');
	}
}
