<?php
/**
 * Created by PhpStorm.
 * User: mttm
 * Date: 2016/7/12
 * Time: 18:25
 */

namespace Home\Model;


use Think\Model;

class DeliveryModel extends Model{

	public function getList(){
		return $this->where(['status'=>1])->order('sort')->select();
	}

	public function getDelieryInfo($id,$field="*"){
		return $this->where(['id'=>$id])->field($field)->find();
	}
}