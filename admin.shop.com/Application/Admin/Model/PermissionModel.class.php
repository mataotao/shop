<?php
/**
 * Created by PhpStorm.
 * User: mttm
 * Date: 2016/7/1
 * Time: 16:23
 */

namespace Admin\Model;


use Admin\Logic\NestedSets;
use Think\Model;

class PermissionModel extends Model
{
    /**
     * @var array 设置自动验证
     */
    protected $_validate=[
      ['name','require','权限名不能为空']
    ];

    /**
     * 显示权限信息
     * @return mixed 获取所有数据
     */
    public function getList(){
        //返回通过条件查询后的数据
        return $this->where(['status'=>1])->order('lft')->select();
    }

    /**
     * 添加权限信息
     * @return bool 返回执行结果
     */
    public function addpermission(){
        //删除没有过滤掉的主键
        unset($this->data[$this->getPk()]);
        //实例化
        $orm = D('MySQL','Logic');
        //实例化
        $nestedsets = new NestedSets($orm,$this->trueTableName,'lft','rght','parent_id','id','level');
        //插入获取到的数据
        if($nestedsets->insert($this->data['parent_id'],$this->data,'bottom')===false){
            //返回错误信息
            $this->error='添加失败';
            return false;
        }
        return true;

    }

    /**
     * 修改权限信息
     * @return bool 返回执行结果
     */
    public function editpermission(){
        //获取要修改的id
        $id = $this->data['id'];
        //获取要修改权限历史父级id
        $parent_id = $this->getFieldById($id,'parent_id');
        //判断要修改的和历史的相不相同
        if($this->data['parent_id']!=$parent_id){
            //实例化
            $orm = D('MySQL','Logic');
            //实例化
            $nestedsets = new NestedSets($orm,$this->trueTableName,'lft','rght','parent_id','id','level');
            //修改并判断
            if($nestedsets->moveUnder($id,$this->data['parent_id'],'bottom')===false){
                //错误信息
                $this->error='不能移动到自身或者后代中';
                return false;
            }
        }
        //返回保存基本信息的结果
        return $this->save();
    }

    /**
     * 删除权限数据
     * @param $id integer 要删除的id
     * @return bool 返回的执行结果
     */
    public function deletepermision($id){
        //开启事务
        $this->startTrans();
        //获取所有的后代权限,一次性删除
        $permission_info = $this->field(['lft','rght'])->find($id);
        //设置条件
        $cond=[
            'lft'=>['egt',$permission_info['lft']],
            'rght'=>['elt',$permission_info['rght']],
        ];
        //通过条件获取到所有符合条件的权限id
        $permissions = $this->where($cond)->getField('id',true);
        //实例化权限权限表
        $role_permission_model = M('RolePermission');
        //删除权限权限表对应的数据
        if($role_permission_model->where(['permission_id'=>['in',$permissions]])->delete()===false){
            //错误信息
            $this->error='删除关联权限失败';
            //回滚
            $this->rollback();
            return false;
        }
        //实例化
        $orm = D('MySQL','Logic');
        //实例化
        $nestedsets = new NestedSets($orm,$this->trueTableName,'lft','rght','parent_id','id','level');
        if($nestedsets->delete($id)===false){
            //回滚
            $this->rollback();
            //错误信息
            $this->error='删除失败';
            return false;
        }
        //提交
        $this->commit();
        return true;
    }
}