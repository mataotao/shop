<?php
/**
 * Created by PhpStorm.
 * User: mttm
 * Date: 2016/7/12
 * Time: 18:48
 */

namespace Home\Model;


use Think\Model;

class OrderInfoModel extends Model{

	/**
	 * 1创建订单
	 * 2保存订单信息
	 * 3保存发票信息
	 */
	public function addOrder(){
		//$this->startTrans();
		//创建订单
		//收货人信息
		$address_model = D('Address');
		$address       = $address_model->getAddressInfo(I("post.address_id") , 'province_name,city_name,area_name,tel,name,detail_address,member_id');
		$this->data    = array_merge($this->data , $address);
		//配送方式
		$delivery_model = D('Delivery');
		$delivery       = $delivery_model->getDelieryInfo(I("post.delivery_id") , 'name as delivery_name,price as
delivery_price');
		$this->data     = array_merge($this->data , $delivery);
		//支付方式
		$payment_model = D('payment');
		$payment       = $payment_model->getPaymentInfo(I("post.pay_type_id") , 'name as pay_type_name');
		$this->data    = array_merge($this->data , $payment);
		//获取购物车
		$shopping_car_model   = D('ShoppingCar');
		$cart_info            = $shopping_car_model->getShoppingCarList();
		$this->data['price']  = $cart_info['total_price'];
		$this->data['status'] = 1;
		$order_id             = $this->add();
		if($id == false){
			$this->error = '订单生产失败';
			$this->rollback();
			return false;
		}

		//保存订单信息
		$order_info_item_model = M('OrderInfoItem');
		$data                  = [];
		foreach($cart_info['goods_info_list'] as $goods){
			$data[] = [
				'order_info_id' => $order_id ,
				'goods_id' => $goods['id'] ,
				'goods_name' => $goods['name'] ,
				'logo' => $goods['logo'] ,
				'price' => $goods['shop_price'] ,
				'amount' => $goods['amount'] ,
				'total_price' => $goods['sub_total']
			];
		}
		if($order_info_item_model->addAll($data) === false){
			$this->error = '订单信息保存失败';
			$this->rollback();
			return false;
		}

		//发票
		$Invoice_model = M('Invoice');
		//抬头
		$receipt_type = I('post.receipt_type');
		if($receipt_type == 1){
			$receipt_title = $address['name'];
		}else{
			$receipt_title = I('post.company_name');
		}
		//发票内容
		$receipt_content_type = I('post.receipt_content_type');
		$receipt_content      = '';
		switch($receipt_content_type){
			case 1:
				$tmp = [];
				foreach($cart_info['goods_info_list'] as $goods){
					$tmp[] = $goods['name']."\t".$goods['shop_price']."x".$goods['amount']."\t".$goods['sub_total'];
				}
				$receipt_content = implode("\r\n" , $tmp);
				break;
			case 2:
				$receipt_content = '办公用品';
				break;
			case 3:
				$receipt_content = '体育休闲';
				break;
			default:
				$receipt_content = '耗材';
				break;
		}
		$content     = $receipt_title."\r\n".$receipt_content."\r\n总计：".$cart_info['total_price'];
		$invoiCedata = [
			'name' => $address['name'] ,
			'content' => $content ,
			'price' => $cart_info['total_price'] ,
			'inputtime' => NOW_TIME ,
			'member_id' => $address['member_id'] ,
			'order_info_id' => $order_id
		];
		if($Invoice_model->add($invoiCedata) == false){
			$this->error = '保存发票失败';
			$this->rollback();
			return false;
		}
		$this->commit();
		$shopping_car_model->clerShoppingCar();
		return true;
	}
}