<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class Goods extends Model{
	//添加商品
	public function goodsAdd(){
		$data = input();
		//验证商品号
		if($this->checkGoodsSn($data) === false){
		    $this -> error = '商品需要唯一';
			return false;
		}
		//验证提交的数据
		$validate = validate('Goods');
		$result = $validate -> check($data);
		if(!$result){
			$this -> error = $validate -> getError();
			return false;
		}
		//处理上传图片
		if($this -> uploadImage($data)===false){
			return false;
		};
		//添加商品时间
		$data['addtime'] = time();
		Db::startTrans();
		try{
			Db::name('goods')->strict(false)->insert($data);
			$goods_id = Goods::getLastInsId();
			//var_dump(input('attr/d'));exit;
			$list = $this -> insertAttr($goods_id,input('attr/a'));

			$img = $this -> uploadImages($goods_id);
			//return $img;
			Db::commit();

		}catch(\Exception $e){
			Db::rollback();
			$this -> error = '写入异常';
			return false;
		}

	}

	//上传商品的相册
	public function uploadImages($goods_id){

		$list = [];//保存组装要写入数据表的数据
		$files = request()->file('pic');

		if($files){
			foreach ($files as $file) {
				$info = $file->validate(['ext'=>'jpg,png'])->move('uploads');
				//return 1;exit;
				if(!$info){
					return FALSE;
				}
				// 组装上传到的地址
				$goods_img = str_replace('\\','/','uploads/'.$info->getSaveName());
				//return $goods_img;
				// 组装缩略图的地址
				$goods_thumb = 'uploads/'.date('Ymd').'/thumb_'.$info->getFileName();
				$img = \think\Image::open($goods_img);
				$img->thumb(150,150)->save($goods_thumb);
				// 将资源文件转移到服务器上
				//return $goods_img;
				upload_img_to_cdn($goods_img);
				upload_img_to_cdn($goods_thumb);
				$list[]=[
					'goods_id'=>$goods_id,
					'goods_img'=>$goods_img,
					'goods_thumb'=>$goods_thumb
				];
			}
			
			if($list){
				 Db::name('goods_img')->insertAll($list);
			}
		}
	}

	//写入属性
	public function insertAttr($goods_id,$arr){
		$list = [];
		foreach ($arr as $key => $value) {
			$value = array_unique($value);
			foreach ($value as $k => $v) {
				$list[] = [
					'goods_id'	=>	$goods_id,
					'attr_id' 	=>	$key,
					'attr_value'	=>	$v,
				];
			}
		}
		if($list){
			Db::name('goods_attr')->insertAll($list);
		}
	}
	//编辑属性值
	public function getGoodsAttr($id){
		$sql = "SELECT a.attr_value,b.* FROM shop_goods_attr a LEFT JOIN shop_attribute b on a.attr_id=b.id WHERE a.goods_id=$id";
		$data = $this -> query($sql);
		//格式化数据
		foreach ($data as $key => $value) {
			if($value['attr_input_type']==2){
				$value['attr_values'] = explode(',',$value['attr_values']);
			}
			$list[] = $value;
		}
		$lists = [];
		foreach ($list as $key => $value) {
			$lists[$value['id']][] = $value;
		}
		return $lists;
	}


	//自定义验证上传数据
	public function checkGoodsSn(&$data,$isEdit=false){
		if(!$data['goods_sn']){
			$data['goods_sn'] = uniqid();
		}
		$map = ['goods_sn'=>$data['goods_sn']];
		if($isEdit){
			$map['id'] = ['neq',$data['id']];
		}
		if(Goods::get($map)){
			return false;
		}
	}
	//处理图片
	public function uploadImage(&$data){
		$file = request() -> file('goods_img');
		if($file == null){
			$this -> error = '图片必须';
			return false;
		}
		$info = $file ->validate(['ext'=>'png,jpg'])->move('uploads');
		if(!$info){
			$this -> error = $file -> getError();
			return false;
		}
		//linux中识别'/'所以要转换
		$data['goods_img'] = str_replace('\\','/','uploads/'.$info->getSaveName());
		
		//缩略图
		$image = \think\Image::open($data['goods_img']);
		$data['goods_thumb'] = 'uploads/'.date('Ymd').'/thumb_'.$info->getFileName();
		$image -> thumb(150,150)->save($data['goods_thumb']);
		//将图片保存到
		upload_img_to_cdn($data['goods_img']);
		upload_img_to_cdn($data['goods_thumb']);
	}

	//获取商品列表
	public function getList($recycle=1){
		$where = [];
		$where['is_del'] = $recycle;
		$intro_type = input('intro_type');
		if($intro_type){
			$where['intro_type'] = 1;
		}
		$is_sale = input('is_sale');
		if($is_sale){
			$where['is_sale'] = $is_sale-1;
		}
		$cat_id = input('cat_id');
		if($cat_id){
			$model = model('Category');
			//给getCate方法加参数判断,是否清空列表。才不会重复显示类别
			$data = $model ->getCate($cat_id,true);
			$list = [];
			foreach ($data as $key => $value) {
				$list = $value['id'];
			}
			//追加自己
			$list = $cat_id;
			$where['cate_id'] = ['in',$list];
		}
		$keyword = input('keyword');
		if($keyword){
			$where['goods_name'] = '%'.$keyword.'%';
		}
		//var_dump($where);exit;
		return Db::name('goods')->where($where)->paginate(1,false,['query'=>input()]);

	}

	public function changeState(){
		$data = input();
		//var_dump($data['filed']);exit;
		$role = ['is_sale','is_rec','is_hot','is_new'];
		if(!in_array($data['filed'],$role)){
			$this -> error = '数据错误';
			return false;
		}
		//获得数据1  0 来判断取反
		$arr = Goods::get(1)->toArray();
		//var_dump($goodsInfo);exit;
		$status = $arr[$data['filed']]?0:1;
		Goods::where(['id'=>$data['goods_id']])->setField($data['filed'],$status);
		//echo $status;exit;
		return $status;
	}

	//编辑商品信息
	public function editGoods(){
		 $data = input();
		 $validate = validate('Goods');
		 if(!$validate->check($data)){
		 	$this -> error =  $validate -> getError();
		 	return false;
		 }
		 if($this -> checkGoodsSn($data,true) === false){
		 	$this -> error = '货号重复';
		 	return false;
		 }
		 if($_FILES['goods_img']['size']>0){
		 	if($this -> uploadImage($data) === false){
		 		return false;
		 	}
		 }
		 //var_dump($data);exit;
		 Goods::allowField(true)->isUpdate(true)->save($data,['id'=>$data['id']]);
		 //添加属性值
		//先删除再添加
		Db::name('goods_attr')->where(['goods_id'=>$data['id']])->delete();
		$this -> insertAttr($data['goods_id'],input('attr/a'));
	}
	/*//搜索
	public function searchData(){
		$cat_id = input('cate_id');
		$is_sale = input('is_sale');
		$keyword = input('keyword');
		//$sql = 'select * from shop_goods';
		$where = Goods::where('id','>',0);
		//$where = 'where id>0';
		//原生写法
		if(!empty($cat_id)){
			$where = $where -> whereOr('cate_id','=',$cat_id);
			//$where .= "or cat_id = $cate_id";
		}
		if(!empty($keyword)){
			$where = $where -> whereOr("goods_name","=","%$keyword%");
			//$where .= "or goods_name = '%$keyword%'";
		}
		if(!empty($is_sale)){
			$where = $where -> whereOr('is_sale','=',$is_sale);
			//$where .= "or is_sale = $is_sale";
		}
		 $result = $where -> select();
		 if(!$result){
		 	$this -> error = '没找到';
		 	return false;
		 }
		 return $result;
		 //return Goods::getLastSql();
		//$sql = $sql.$where;
	}*/
}







 ?>