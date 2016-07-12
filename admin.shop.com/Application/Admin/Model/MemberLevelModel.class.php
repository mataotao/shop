<?php
/**
 * Created by PhpStorm.
 * User: mttm
 * Date: 2016/7/12
 * Time: 19:55
 */

namespace Admin\Model;


use Think\Model;

class MemberLevelModel extends Model{

	public function getList(){
		return $this->where(['status'=>1])->order('sort')->select();
	}
}