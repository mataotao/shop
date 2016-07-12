<?php
/**
 * Created by PhpStorm.
 * User: mttm
 * Date: 2016/7/8
 * Time: 17:02
 */

namespace Home\Model;


use Think\Model;

class ShoppingCarModel extends Model{
	/**
	 * 通过good_id和member_id获取购物和对应商品的数据
	 * @param $id number 用户id
	 * @param $good_id string 商品id
	 * @return mixed
	 */
	public function getmer($id , $good_id){
		return $this->where(['member_id' => $id , 'goods_id' => $good_id])->getField('amount');
	}

	//更新购物车的数据
	public function saveshop($user_id , $goods_id , $amount){
		$data = [
			'member_id' => $user_id ,
			'goods_id' => $goods_id ,
		];

		return $this->where($data)->setField(['amount' => $amount]);
	}

	//添加购物车数据
	public function addshop($user_id , $goods_id , $amount){
		$data = [
			'member_id' => $user_id ,
			'goods_id' => $goods_id ,
			'amount' => $amount
		];
		$this->add($data);
	}

	public function cookie2db(){
		$userinfo  = login();
		$cookieCar = cookie(C('SHOPPING_CAR_COOKIE_KEY'));
		cookie(C('SHOPPING_CAR_COOKIE_KEY') , null);
		if( !$cookieCar){
			return true;
		}
		if($this->where(['member_id' => $userinfo['id'] , 'goods_id' => ['in' , array_keys($cookieCar)]])->delete() === false){
			return false;
		}
		$cond = [];

		foreach($cookieCar as $key => $value){
			$cond[] = [
				'member_id' => $userinfo['id'] ,
				'goods_id' => $key ,
				'amount' => $value
			];
		}
		return $this->addAll($cond);
	}

	/**
	 * 获取购物车的数据
	 * @return array
	 */
	public function getShoppingCarList(){
		$userinfo = login();
		if($userinfo){
			$car_list = $this->where(['member_id' => $userinfo['id']])->getField('goods_id,amount');
		}else{
			$car_list = cookie(C('SHOPPING_CAR_COOKIE_KEY'));

		}
		if( !$car_list){
			return [
				'total_price' => '0.00' ,
				'goods_info_list' => [] ,
			];
		}
		$goods_model     = M('Goods');
		$cand            = [
			'id' => ['in' , array_keys($car_list)] ,
			'is_on_sale' => 1 ,
			'status' => 1
		];
		$goods_info_list = $goods_model->where($cand)->getField('id,logo,name,shop_price');
		$total_price     = 0.00;
		//获取会员积分
		$member_model = M('Member');
		$score        = $member_model->getFieldById($userinfo['id'] , "score");
		$cond         = [
			'bottom' => ['elt' , $score] ,
			'top' => ['egt' , $score]
		];
		//获取会员等级
		$member_level    = M('MemberLevel')->where($cond)->field('id,discount')->find();
		$member_level_id = $member_level['id'];
		$discount        = $member_level['discount'];
		foreach($car_list as $key => $value){
			$cond         = [
				'goods_id' => $key ,
				'member_level_id' => $member_level_id ,
			];
			$member_price = M('MemberGoodsPrice')->where($cond)->field('price')->find();
			if($member_price){
				$goods_info_list[$key]['shop_price'] = locate_number_format($member_price['price']);
			}else{
				$goods_info_list[$key]['shop_price'] = locate_number_format($goods_info_list[$key]['shop_price']*$discount/100);
			}

			$goods_info_list[$key]['sub_total'] = locate_number_format($goods_info_list[$key]['shop_price']*$value);
			$goods_info_list[$key]['amount']    = $value;
			$total_price += $goods_info_list[$key]['sub_total'];
		}
		$total_price = locate_number_format($total_price);
		return compact('goods_info_list' , 'total_price');
	}

	/**
	 * 清空购物车
	 * @return mixed
	 */
	public function clerShoppingCar(){
		$userInfo = login();
		return $this->where(['member_id' => $userInfo['id']])->delete();
	}
}