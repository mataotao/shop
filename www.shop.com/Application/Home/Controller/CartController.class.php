<?php
/**
 * Created by PhpStorm.
 * User: mttm
 * Date: 2016/7/8
 * Time: 16:57
 */

namespace Home\Controller;


use Think\Controller;

class CartController extends Controller{

	protected $_model = null;

	protected function _initialize(){
		$this->_model = D('ShoppingCar');
	}

	/**购物车
	 * @param $id int 商品的id
	 * @param $amount int 商品的数量
	 */
	public function add2car($id , $amount){
		$userinfo = login();
		//判断有没有登录
		if( !$userinfo){
			$key      = 'USER_SHOPPING_CAR';
			$car_list = cookie($key);
			//检测cookie中是否有这个商品
			if(isset($car_list[$id])){
				$car_list[$id] += $amount;
			}else{
				$car_list[$id] = $amount;
			}
			//存入cookie
			cookie($key , $car_list , 604800);
		}else{
			//获取购物车的商品
			//查询改商品的数量
			$merc = $this->_model->getmer($userinfo['id'] , $id);
			if(isset($merc)){
				$merc = $merc + $amount;
				$this->_model->saveshop($userinfo['id'] , $id , $merc);
			}else{
				$merc = $amount;
				$this->_model->addshop($userinfo['id'] , $id , $merc);
			}
		}

		$this->success('添加成功' , U('flow1'));
	}


	public function flow1(){
		$car_list = $this->_model->getShoppingCarList();
		$this->assign($car_list);
		$this->display();
	}

	/**
	 * 1收货人信息
	 * 2送货方式
	 * 3支付方式
	 * 4购物车数据
	 */
	public function flow2(){
		$userinfo = login();
		if( !$userinfo){
			cookie('__FORWARD__' , __SELF__);
			$this->error('请先登录' , U('Member/login'));
		}else{
			//收货人信息
			$address_model = D('Address');
			$this->assign('addresses',$address_model->getList());
			//送货方式
			$delivery_model = D('Delivery');
			$this->assign('deliveries',$delivery_model->getList());
			//支付方式
			$payment_model = D('payment');
			$this->assign('payments',$payment_model->getList());
			//购物车
			$shopping_car_model = D('ShoppingCar');
			$this->assign($shopping_car_model->getShoppingCarList());
			$this->display();
		}
	}
}