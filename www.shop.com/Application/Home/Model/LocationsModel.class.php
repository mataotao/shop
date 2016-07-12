<?php
/**
 * Created by PhpStorm.
 * User: mttm
 * Date: 2016/7/9
 * Time: 16:14
 */

namespace Home\Model;


use Think\Model;

class LocationsModel extends Model{

    public function getListByParentId($parent_id=0) {
        return $this->where(['parent_id'=>$parent_id])->select();
    }
}