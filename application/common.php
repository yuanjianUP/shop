<?php
//function_exists用来判断这个函数是否存在，不存在则执行
if(!function_exists('upload_img_to_cdn')){
	/**
	 * 转义文件到资源服务器
	 * @param  string $image   要转资源的本地地址
	 * @param  string $newpath 上传到资源服务器上的目录(不带家地址)
	 * @return bollean
	 */
	function upload_img_to_cdn($image,$newpath=''){
		include_once '../extend/ftp.php';
		//读取配置
		$config = config('cdn');
		/*$ftp = new \ftp($config['host'],$config['port'],$config['user'],$config['pass']);*/
		$ftp = new \ftp($config['host'],$config['port'],$config['user'],$config['pass']);
		$newpath = $newpath?$newpath:$image;
		return $ftp -> up_file($image,$newpath);
	}
}
if(!function_exists('get_tree')){
	/**
     * 无限极数据格式化
     * @param array    $data 格式化的数据
     * @param int    $id 需要寻找某个分类下的子分类 0表示所有
     * @param int    $lev 层次
     * @return array
     */
	function get_tree($arr,$pid,$lv=0,$exclude=null,$clearTree=false){
		static $tree=[];
		if($clearTree){
			$tree = [];
		}
		foreach ($arr as $v) {
			if($v['id']==$exclude){
				continue;
			}
			if($v['parent_id']==$pid){
				$v['lv']=$lv;
				$tree[]=$v;
				get_tree($arr,$v['id'],$lv+1);
			}
		}
		//dump($tree);
		return $tree;
	}
}
//实现加密
if(!function_exists('md6')){
	function md6($password,$salt='123456'){
		return md5(md5($password).$salt);
	}
}


//加密解密函数
if(!function_exists('_encrypt')){
	/**
      * 加密
      *
      * @param string $plainText 未加密字符串
      * @param string $key        密钥
      */
     function _encrypt($plainText,$key = null){
         $key = $key === null ? config('DATA_AUTH_KEY') : $key;
         $ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
         $iv = mcrypt_create_iv($ivSize, MCRYPT_RAND);
         $encryptText = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $plainText, MCRYPT_MODE_ECB, $iv);
         return trim(base64_encode($encryptText));
     }
 
}

//解密
if(!function_exists('_decrypt')){
	/**
       * 解密
       * 
       * @param string $encryptedText 已加密字符串
       * @param string $key  密钥
       * @return string
       */
      function _decrypt($encryptedText,$key = null)
     {
         $key = $key === null ? config('DATA_AUTH_KEY') : $key;
         $cryptText = base64_decode($encryptedText);
         $ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
         $iv = mcrypt_create_iv($ivSize, MCRYPT_RAND);
         $decryptText = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $cryptText, MCRYPT_MODE_ECB, $iv);
         return trim($decryptText);
     }
}

//获取用户信息
if(!function_exists('get_user_info')){
	 function get_user_info(){
		$userInfo = cookie('_userinfo');
		if(!$userInfo){
			return false;
		}
		//var_dump($userInfo);exit;
		$result = _decrypt($userInfo);
		if(!$result){
			return false;
		}
		return json_decode($result,true);
	}
}

//获取远程地址
if(!function_exists('make_img_url')){
	function make_img_url($url){
		return config('rec_domain').''.$url;
	}
}

//判断有没有登录
if(!function_exists('check_is_login')){
	function check_is_login(){
		$result = cookie('_userinfo');
		if(!$result){
			return false;
		}
		return true;
	}

}

//发送信息
if(!function_exists('send_sms')){
	function send_sms($to,$datas,$tempId){
		include_once("../extend/CCPRestSDK.php");
		 //主帐号
		$accountSid= '8a216da864da60ef0164e5c88f38049b';

		//主帐号Token
		$accountToken= '23b286ea898d4eabbf8dc63a9b0c96d5';

		//应用Id
		$appId='8a216da864da60ef0164e5c88f9904a2';

		//请求地址，格式如下，不需要写https://
		$serverIP='sandboxapp.cloopen.com';

		//请求端口
		$serverPort='8883';

		//REST版本号
		$softVersion='2013-12-26';

		$rest = new \REST($serverIP,$serverPort,$softVersion);
	    $rest->setAccount($accountSid,$accountToken);
	    $rest->setAppId($appId);
	    $result = $rest->sendTemplateSMS($to,$datas,$tempId);
	    if($result == NULL ) {
          return false;
	    }
	    if($result->statusCode!=0) {
	     	return false;
	         //下面可以自己添加错误处理逻辑
	    }
	    return true;
    }
}


//发送短信
if(!function_exists('send_email')){
	/**
	 * [send_email description]
	 * @param  [type] $to      [目标地址]
	 * @param  [type] $body    [内容]
	 * @param  string $subject [主题]
	 * @return [type]          [description]
	 */
	function send_email($to,$body,$subject='邮箱注册'){
		require '../extend/PHPMailer/class.phpmailer.php';
	$mail             = new PHPMailer();
	/*服务器相关信息*/
	$mail->IsSMTP();   //启用smtp服务发送邮件
	$mail->SMTPAuth   = true;  //设置开启认证
	$mail->Host       = 'smtp.163.com';   	 //指定smtp邮件服务器地址
	$mail->Username   = 'yuanjianblue';  	//指定用户名
	$mail->Password   = 'yuanjianblue123';		//邮箱的第三方客户端的授权密码
	/*内容信息*/
	$mail->IsHTML(true);
	$mail->CharSet    ="UTF-8";
	$mail->From       = 'yuanjianblue@163.com';
	$mail->FromName   ="王尼玛";	//发件人昵称
	$mail->Subject    = $subject; //发件主题
	$mail->MsgHTML($body);	//邮件内容 支持HTML代码


	$mail->AddAddress($to);  //收件人邮箱地址
	//$mail->AddAttachment("test.png"); //附件
	$mail->Send();		//发送邮箱
	}
}
