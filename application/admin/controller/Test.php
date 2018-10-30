<?php 
namespace app\admin\controller;
use think\Db;
use think\Cache;
class Test extends Common
{
	// 设置缓存
	public function testCache()
	{
		// 第一个参数指定名称
		// 第二个参数指定值
		// 第三个参数指定有效时间
		Cache::set('name','leo',3600);
		cache('name2',['name'=>'abcdef'],300);
	}
	public function testCacheG()
	{
		dump(Cache::get('name'));
		dump(cache('name2'));
	}


	public function uploadImages(){
		$goods_id = 67;
		$list = [];//保存组装要写入数据表的数据
		//$files = request()->file('pic');
		//dump(11);exit;
		if(true){
			foreach ($files as $file) {
				//$info = $file->validate(['ext'=>'jpg,png'])->move('uploads');
				
				/*if(!$info){
					return FALSE;
				}*/
				// 组装上传到的地址
				$goods_img = "uploads/20180731/c289271c704daba01d0748a1d1b8b636.jpg";
				// 组装缩略图的地址
				//$goods_thumb = 'uploads/'.date('Ymd').'/thumb_'.$info->getFileName();
				//$img = \think\Image::open($goods_img);
				//$img->thumb(150,150)->save($goods_thumb);
				// 将资源文件转移到服务器上
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


	function upload_img_to_cdn($newpath=''){
		include_once '../extend/ftp.php';
		$image = "uploads/20180731/c289271c704daba01d0748a1d1b8b636.jpg";
		//dump(file_exists($image));exit;
		//读取配置
		$config = config('cdn');
		/*$ftp = new \ftp($config['host'],$config['port'],$config['user'],$config['pass']);*/
		$ftp = new \ftp($config['host'],$config['port'],$config['user'],$config['pass']);
		$newpath = $newpath?$newpath:$image;
		//dump($newpath);exit;
		$ftp -> up_file($image,$newpath);
	}

	function test2(){
		$savepath = file_exists('../../../');
		 dump($savepath);exit;
	
	}


}



 ?>