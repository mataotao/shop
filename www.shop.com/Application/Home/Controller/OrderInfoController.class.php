<?php
/**
 * Created by PhpStorm.
 * User: mttm
 * Date: 2016/7/12
 * Time: 18:44
 */

namespace Home\Controller;


use Think\Controller;

class OrderInfoController extends Controller{

	/**
	 * @var \Home\Model\OrderInfoModel
	 */
	protected $_model = null;

	public function _initialize(){
		$this->_model = D('OrderInfo');
	}


	public function add(){
		if(IS_POST){
			if($this->_model->create() === false){
				$this->error(get_error($this->_model));
			}
			if($this->_model->addOrder() === false){
				$this->error(get_error($this->_model));
			}
			$this->success('创建订单成功' , U('Cart/flow3'));
		}else{
			$this->error('拒绝直接访问');
		}
	}

	public function index(){

	}
}