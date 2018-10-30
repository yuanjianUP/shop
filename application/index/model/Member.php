<?php
namespace app\index\model;
use think\Model;
/**
* 
*/
class Member extends Model{
	/**
	 * [upXp 经验规则]评论 购买 第一次登录
	 * @param  boolean $cart [购买触发参数]
	 * @return [type]        [description]
	 */
	public function upXp($cart=false){
		//消费额比例
		$scale = 10;
		//获取user_id
		$userinfo = cookie('_userinfo');
		$user_id = db('user')->field('id')->where(['username'=>$userinfo['username']])->find();
		$user_id = $user_id['id'];
		//dump($user_id);exit;
		//获取消费额
		$money = 300;
		//获取会员id
		$member_id = db('member')->field('id')->where(['user_id'=>$user_id])->find();
		$member_id = $member_id['id'];
		//购物付款触发
		if($cart){
			//计算经验值
			$xp = intval($money/$scale);
			//不能超过30
			if($xp<30){
				db('member_xp')->where(['member_id'=>$member_id])->setInc('xp',$xp);
			}
			//dump($xp);exit;
			exit();
		}
		//判断是不是会员
		//dump($member_id);exit;
		if($member_id){
			//是会员
			//上次登录时间
			$last_time = db('member_xp')->field('login_time')->where(['member_id'=>$member_id])->find();
			$last_time = $last_time['login_time'];
			//dump($last_time);exit;
			//零点时间
			$today = strtotime(date('Ymd'));
			//上次登录时间和零点时间比较 判断是否今天第一次登录
			if($last_time<$today){
				//今日未登录,加20
				db('member_xp')->where(['member_id'=>$member_id])->setInc('xp',20);
			}else{
				//评论加10
				db('member_xp')->where(['member_id'=>$member_id])->setInc('xp',10);
			}
			//更新最后登录时间
			$time = time();
			db('member_xp')->where(['member_id'=>$member_id])->update(['login_time'=>$time]);
		}



	}
	/**
	 * [积分规则]购买
	 * @param  boolean $cart [购物触发参数]
	 * @return [type]        [description]
	 */
	public function credits($cart=false){
		//获取消费额
		$money = 300;
		//获取user_id
		$user_id = 1;
		//消费额比例
		$scale = 10;
		//获取会员id
		$member_id = db('member')->field('id')->where(['user_id'=>$user_id])->find();
		$member_id = $member_id['id'];
		if($cart){
			$xp = intval($money/$scale);
			db('member_xp')->where(['member_id'=>$member_id])->setInc('credits',$xp);
			exit();
		}
		if($member_id){
			//是会员
			db('member_xp')->where(['member_id'=>$member_id])->setInc('credits',5);
		}
	}

}







 ?>