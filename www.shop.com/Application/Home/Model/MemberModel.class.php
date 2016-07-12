<?php

/**
 * Created by PhpStorm.
 * User: mttm
 * Date: 2016/7/5
 * Time: 17:06
 */
namespace Home\Model;

use Think\Model;
use Think\Verify;

class MemberModel extends Model{

	protected $patchValidate = true;
	/**用户名唯一必填
	 * 密码必填,6-16
	 * 确认密码：必填
	 * 邮箱 必填,合不合法
	 * 手机号码：长度 合不合法 必填
	 * 验证码必填 合不合法
	 * 手机验证码必填 合不合法
	 * @var array
	 */
	protected $_validate = [
		['username' , 'require' , '用户名不能为空'] ,
		['username' , '' , '用户名已存在' , self::EXISTS_VALIDATE , 'unique' , 'reg'] ,
		['password' , 'require' , '密码不能为空'] ,
		['repassword' , 'require' , '确认密码不能为空'] ,
		['password' , '6,16' , '密码长度6-16' , self::EXISTS_VALIDATE , 'length' , 'reg'] ,
		['repasword' , 'password' , '两次密码不一样' , self::EXISTS_VALIDATE , 'confirm'] ,
		['email' , 'require' , '邮箱必填'] ,
		['email' , 'email' , '用邮箱不合法'] ,
		['email' , '' , '邮箱已存在' , self::EXISTS_VALIDATE , 'unique'] ,
		['tel' , 'require' , '手机号不能为空'] ,
		['tel' , '' , '手机号已存在' , self::EXISTS_VALIDATE , 'unique'] ,
		['tel' , '/^1[3578]\d{9}$/' , '手机号不合法' , self::EXISTS_VALIDATE , 'unique'] ,
		['captcha' , 'require' , '手机验证码必填'] ,
		//['checkcode', 'checkImgCode', '图片验证码不正确', self::EXISTS_VALIDATE, 'callback'],
		['captcha' , 'checkCheckCode' , '手机验证码不合法' , self::EXISTS_VALIDATE , 'callback'] ,
	];
	protected $_auto     = [
		['salt' , '\Org\Util\String::randString' , 'reg' , 'function'] ,
		['add_time' , NOW_TIME , 'reg'] ,
		['register_token' , '\Org\Util\String::randString' , 'reg' , 'function' , 32] ,
		['status' , 0 , 'reg']
	];

	/**
	 * 验证图片验证码
	 * @param $code string 输入的验证码
	 * @return bool
	 *
	 */
	protected function checkImgCode($code){
		$verify = new Verify();
		return $verify->check($code);
	}

	/**检测手机验证码
	 * @param $code number 输入的手机验证码
	 * @return bool
	 */
	protected function checkCheckCode($code){
		$telCode = session('reg_tel_code');
		if($code == $telCode){
			session('reg_tel_code' , null);
			return true;
		}else{
			return false;
		}
	}

	/**
	 * 用户注册
	 * @return bool
	 */
	public function addMember(){
		//加盐加密
		$this->data['password'] = salt_mcrypt($this->data['salt'] , $this->data['password']);
		$email                  = $this->data['email'];
		$register_token         = $this->data['register_token'];
		if($this->add() === false){
			return false;
		}
		//邮件激活的url
		$url     = U('Member/active' , ['email' => $email , 'register_token' => $register_token] , true , true);
		$subject = '欢迎注册啊咿呀哟母婴商城';
		$content = '欢迎您注册我们的网站,请点击<a href="' . $url . '">链接</a>激活账号.如果无法点击,请复制以下链接粘贴到浏览器窗口打开!<br />' . $url;
		//发送邮件 sendMail 邮件的函数
		$rst = sendMail($email , $subject , $content);
		if($rst['status']){
			return true;
		}else{
			$this->error = $rst['msg'];
			return false;
		}

	}

	/**
	 *登录
	 */
	public function login(){
		$username = $this->data['username'];
		$password = $this->data['password'];
		$userInfo = $this->where(['status' => 1 , 'username' => $username])->find();
		if($userInfo === false){
			$this->error = '用户名或密码错误';
			return false;
		}
		if(salt_mcrypt($userInfo['salt'] , $password) != $userInfo['password']){
			$this->error = '用户名或密码错误';
			return false;
		}

		$data = [
			'id' => $userInfo['id'] ,
			'last_login_time' => NOW_TIME ,
			'last_login_ip' => get_client_ip(1)
		];

		$this->setField($data);
		$shopping_car_model = D('ShoppingCar');
		login($userInfo);
		$shopping_car_model->cookie2db();
		return $userInfo;
	}

}