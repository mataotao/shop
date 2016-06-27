<?php
/**
 * Created by PhpStorm.
 * User: mttm
 * Date: 2016/6/27
 * Time: 19:24
 */

namespace Admin\Model;
use Think\Model;
class GoodsCategoryModel extends Model
{
    protected $patchValidate = true; //开启批量验证
   
    protected $_validate     = [
        ['name', 'require', '商品分类名称不能为空'],
    ];
    
    public function getList() {
        return $this->where(['status'=>['egt',0]])->order('lft')->select();
    }
}