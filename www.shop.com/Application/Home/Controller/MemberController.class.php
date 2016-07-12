<?php
/**
 * Created by PhpStorm.
 * User: mttm
 * Date: 2016/7/5
 * Time: 16:28
 */

namespace Home\Controller;

use Think\Controller;

class MemberController extends Controller{

	/**
	 * @var \Home\Model\MemberModel
	 */
	private $_model = null;

	protected function _initialize(){
		$this->_model = D('Member');
		$mete_titles  = [
			'reg' => '用户注册' ,
			'login' => '用户登录'
		];
		//检测方法显示标题
		$mete_titles = (isset($mete_titles[ACTION_NAME]) ? $mete_titles[ACTION_NAME] : '用户登录');
		$this->assign('meta_title' , $mete_titles);
	}

	public function index(){
		$this->display();
	}

	/**
	 * 用户注册
	 */
	public function reg(){
		if(IS_POST){
			//自动检测,自动完成 场景为reg
			if($this->_model->create("" , 'reg') === false){
				$this->error(get_error($this->_model));
			}
			if($this->_model->addMember() === false){
				$this->error(get_error($this->_model));
			}else{
				$this->success('注册成功' , U('index'));
			}
		}else{

			$this->display();
		}
	}

	public function edit(){
		$this->display();
	}

	public function remove(){
		$this->display();
	}

	/**
	 * 邮件激活
	 * @param $email string 激活的email
	 * @param $register_token string 验证的令牌
	 */
	public function active($email , $register_token){
		$cond = [
			'email' => $email ,
			'register_token' => $register_token ,
			'status' => 0 ,
		];
		if($this->_model->where($cond)->count()){
			//如果有就设置状态
			$this->_model->where($cond)->setField(['status' => 1]);
			$this->success('激活成功' , U('Index/index'));
		}else{
			$this->error('激活失败' , U('Index/index'));
		}
	}

	/**
	 * 验证邮箱手机号用户名唯一
	 */
	public function checkByParam(){
		$cond = I('get.');
		if($this->_model->where($cond)->count()){
			$this->ajaxReturn(false);
		}else{
			$this->ajaxReturn(true);
		}

	}

	/**
	 * 用户登录
	 */
	public function login(){
		if(IS_POST){
			if($this->_model->create() === false){
				$this->error(get_error($this->_model));
			}
			if($this->_model->login() === false){
				$this->error(get_error($this->_model));
			}else{
				$url = cookie('__FORWARD__');
				cookie('__FORWARD__' , null);
				if($url){
					$this->success('登录成功' , $url);
				}else{
					$this->success('登录成功' , U('Index/index'));
				}
			}
		}else{
			$this->display();
		}
	}

	/**
	 * 退出登录
	 */
	public function logout(){
		session(null);
		cookie(null);
		$this->success('退出成功' , U('Index/index'));
	}

	/**
	 * 通过ajax获取登录的用户信息
	 */
	public function userinfo(){
		$userinfo = login();
		if($userinfo){
			$this->ajaxReturn($userinfo['username']);
		}else{
			$this->ajaxReturn(false);
		}
	}

	/**
	 * 显示收货地址和添加收货地址
	 * 1.获取所有的省
	 * 2.获取当前登录用户的所有收货地址
	 */
	public function locationIndex(){
		//获取所有的省
		$location_model = D('Locations');
		$provinces      = $location_model->getListByParentId();
		$this->assign('provinces' , $provinces);

		//获取所有的地址
		$address_model = D('Address');
		$addresses     = $address_model->getList();
		$this->assign('addresses' , $addresses);
		$this->display();
	}

	/**
	 * 获取下级城市列表然后用json返回
	 * 1.接传来的父级id
	 * 2.调用方法通过父级id来查询
	 * 3.因为是ajax请求所有返回对应数据的json形式  ajaxReturn 默认是json方式
	 * @param $parent_id integer 传来的父级id
	 */
	public function getLocationListByParentId($parent_id){
		$location_model = D('Locations');
		$row            = $location_model->getListByParentId($parent_id);
		$this->ajaxReturn($row);
	}

	/**
	 * 添加地址
	 * 1.接受数据并检测
	 * 2.把没有问题的数据调用方法添加
	 * 3.判断并跳转对应的页面
	 */
	public function addLocation(){
		$Address_model = D('Address');
		if($Address_model->create() === false){
			$this->error(get_error($Address_model));
		}
		if($Address_model->addAddress() === false){
			$this->error(get_error($Address_model));
		}
		$this->success('添加完成' , U('locationIndex'));
	}

	/**
	 * 修改地址
	 * 1.接受传来的id 并且实例化address (后面要用)
	 * 2.检测是不是post方式提交
	 *
	 * 2.1 是post方式提交
	 * 2.1.1检测数据的合法性
	 * 2.1.2调用修改方法并传入id
	 * 2.1.3判断并跳转对应的页面
	 *
	 * 2.2 不是post
	 * 2.2.1 调用方法获取对应的数据
	 * 2.2.2实例化Locations并且调用获取所有省的方法
	 * 2.2.3赋值并加载模板
	 * @param $id integer 要修改的id
	 */
	public function modifyLocation($id){
		$Address_model = D('Address');
		if(IS_POST){
			if($Address_model->create() === false){
				$this->error(get_error($Address_model));
			}
			if($Address_model->editAddress($id) === false){
				$this->error(get_error($Address_model));
			}
			$this->success("修改成功" , U('locationIndex'));
		}else{
			$row = $Address_model->getAddressInfo($id);
			$this->assign('row' , $row);
			//获取所有的省
			$location_model = D('Locations');
			$provinces      = $location_model->getListByParentId();
			$this->assign('provinces' , $provinces);
			$this->display();
		}
	}

	/**
	 * 删除地址
	 * 1.接受id
	 * 2调用方法删除
	 * 3判断并跳转对应的页面
	 * @param $id integer 要删除的id
	 */
	public function removeLocation($id){
		$Address_model = D('Address');
		if($Address_model->delete($id) === false){
			$this->error(get_error($Address_model));
		}
		$this->success("删除成功" , U('locationIndex'));
	}

	/**
	 * 设置默认地址
	 * 1.接受要修改的id
	 * 2.调用方法并设置
	 * @param $id int 要修改的id
	 */
	public function defaultLocation($id){
		$Address_model = D('Address');
		if($Address_model->defaultAddress($id) === false){
			$this->error(get_error($Address_model));
		}
		$this->success("设置成功" , U('locationIndex'));
	}
}