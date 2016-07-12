<?php
/**
 * Created by PhpStorm.
 * User: mttm
 * Date: 2016/7/7
 * Time: 17:48
 */

namespace Home\Model;


use Think\Model;

class GoodsCategoryModel extends Model{
	/**
	 * 获取商品详情
	 * @param string $field 获取的字段
	 * @return mixed
	 */
	public function getList($field = "*"){
		return $this->where(['status' => 1])->field($field)->select();
	}
}