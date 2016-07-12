<?php
/**
 * Created by PhpStorm.
 * User: mttm
 * Date: 2016/7/12
 * Time: 18:31
 */

namespace Home\Model;


use Think\Model;

class PaymentModel extends Model{

	public function getList(){
		return $this->where(['status'=>1])->order('sort')->select();
	}
	public function getPaymentInfo($id,$field="*"){
		return $this->where(['id'=>$id])->field($field)->find();
	}
}