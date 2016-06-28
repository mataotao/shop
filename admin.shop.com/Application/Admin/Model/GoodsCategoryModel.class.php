<?php
/**
 * Created by PhpStorm.
 * User: mttm
 * Date: 2016/6/27
 * Time: 19:24
 */

namespace Admin\Model;
use Admin\Logic\NestedSets;
use Think\Model;
class GoodsCategoryModel extends Model
{
    protected $patchValidate = true; //开启批量验证
   
    protected $_validate     = [  //自动验证条件
        ['name', 'require', '商品分类名称不能为空'],
    ];

    /**
     * 获取所有商品的分类
     * @return mixed array
     */
    public function getList() {
        return $this->where(['status'=>['egt',0]])->order('lft')->select();
    }

    /**
     * 添加商品分类
     * @return false|int
     */
    public function addGoods(){
        //删除主键
        unset($this->data[$this->getPk()]);
        //实例化 orm对象
        $orm = D('MySQL','Logic');
        //实例化nestedsets对象
        $nestedsets = new NestedSets($orm,$this->trueTableName,'lft','rght','parent_id','id','level');
        return $nestedsets->insert($this->data['parent_id'],$this->data,'bottom');

    }

    /**
     * 编辑商品分类 不能移动到后代下面
     * @return bool
     */
    public function editGategory(){
        //通过id获取本身的父级id
        $parent_id = $this->getFieldById($this->data['id'],'parent_id');
        //通过父级id来判断有没有修改层级
        if($this->data['parent_id']!=$parent_id){
            //实例化orm
            $orm = D('MySQL','Logic');
            //实例化nestedsets
            $nestedsets = new NestedSets($orm,$this->trueTableName,'lft','rght','parent_id','id','level');
            //调用移动层级关系的方法，自动计算左右节点，只修改左右层级和层级
            if($nestedsets->moveUnder($this->data['id'],$this->data['parent_id'],'bottom')===false){
                //错误信息
                $this->error='不能移动到后代分类下';
                return false;
            }
        }
        //返回状态
        return $this->save();
    }
    public function deleteGategory($id){
        //实例化orm
        $orm = D('MySQL','Logic');
        //实例化nestedsets
        $nestedsets = new NestedSets($orm,$this->trueTableName,'lft','rght','parent_id','id','level');
        //返回删除的状态
        return $nestedsets->delete($id);
    }

}