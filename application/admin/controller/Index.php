<?php 
namespace app\admin\controller;
class Index extends Common{
	public function index(){
		$top = url('top');
		$main = url('main');
		$menu = url('menu');
		$this -> assign('top',$top);
		$this -> assign('menu',$menu);
		$this -> assign('main',$main);
		return $this -> fetch();
	}
	public function top(){
		return $this -> fetch();
	}
	public function main(){
		return $this -> fetch();
	}
	public function menu(){
		$this -> assign('menus',$this -> _user['menus']);
		return $this -> fetch();
	}
}




 ?>
