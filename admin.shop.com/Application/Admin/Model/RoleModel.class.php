<?php
/**
 * Created by PhpStorm.
 * User: mttm
 * Date: 2016/7/1
 * Time: 18:05
 */

namespace Admin\Model;


use Think\Model;
use Think\Page;

class RoleModel extends Model
{
    /**
     * @param array $cond 分页搜索的条件
     * @return mixed 执行结果
     */
    public function getPageResult(array $cond=[]){
        //合并条件
        $cond = array_merge(['status'=>1],$cond);
        //获取分页配置
        $pageSetting = C('PAGE_SETTING');
        //通过条件获取总条数
        $count = $this->where($cond)->count();
        //实例化
        $page = new Page($count,$pageSetting['PAGE_SIZE']);
        //设置要显示的内容
        $page->setConfig('theme',$pageSetting['PAGE_THEME']);
        //获取html代码
        $page_html = $page->show();
        //获取分页的数据
        $rows = $this->where($cond)->page(I('get.p',1),$pageSetting['PAGE_SIZE'])->select();
        //返回结果
        return compact('rows','page_html');
    }

    /**
     * 添加角色方法
     * @return bool 执行结果
     */
    public function addRole(){
        //开启事务
        $this->startTrans();
        //角色表
        $id = $this->add();
        //判断执行结果
        if($id===false){
            //回滚
            $this->rollback();
            return false;
        }
        //角色权限表
        //设置条件数组
        $data=[];
        //获取所有要添加的权限id
        $permission_ids=I('post.permission_id');
        //遍历设置条件
        foreach($permission_ids as $permission_id){
            $data[]=[
                'role_id'=>$id,
                'permission_id'=>$permission_id
            ];
        }
        //判断有没有值
        if($data){
            //实例化
            $role_permission_model = M('RolePermission');
            //添加获取到的权限到角色数据表
            if($role_permission_model->addAll($data)===false){
                //错误信息
                $this->error='权限添加失败';
                //回滚
                $this->rollback();
                return false;
            }
        }
        //提交
        $this->commit();
        return true;
    }

    /**
     * 获取要修改角色对应的数据
     * @param $id integer 要修改的角色id
     * @return bool|mixed 执行结果
     */
    public function getPermissionInfo($id){
        //开启事务
        $this->startTrans();
        //角色表
        //通过id获取数据
        $row = $this->find($id);
        //判断
        if($row===false){
            //回滚
            $this->rollback();
            return false;
        }
        //角色权限表
        //实例化
        $role_permission_model = M('RolePermission');
        //获取角色对应的所有权限
        $permission_ids=$role_permission_model->where(['role_id'=>$id])->getField('permission_id',true);
        if($permission_ids===false){
            //回滚
            $this->rollback();
            return false;
        }
        //把获取的权限id转化成json并添加的数组
        $row['permission_ids'] = json_encode($permission_ids);
        //返回结果
        return $row;
    }

    /**
     * @param $id integer 要修改的id
     * @return bool 执行结果
     */
    public function editRole($id){
        //开启事务
        $this->startTrans();
        //角色表
        //保存角色信息并判断
        if($this->save()===false){
            //错误信息
            $this->error='角色信息修改失败';
            //回滚
            $this->rollback();
            return false;
        }

        //角色权限表
        //实例化
        $role_permission_model = M('RolePermission');
        //删除历史权限并判断
        if($role_permission_model->where(['role_id'=>$id])->delete()===false){
            //错误信息
            $this->error='角色历史权限删除失败';
            //回滚
            $this->rollback();
            return false;
        }
        //设置条件数组
        $data=[];
        //获取所有选中的id
        $permission_ids=I('post.permission_id');
        //遍历添加条件
        foreach($permission_ids as $permission_id){
            $data[]=[
                'role_id'=>$id,
                'permission_id'=>$permission_id
            ];
        }
        //判断有没有值
        if($data){
            //实例化
            $role_permission_model = M('RolePermission');
            //添加权限并判断
            if($role_permission_model->addAll($data)===false){
                //错误信息
                $this->error='权限添加失败';
                //回滚
                $this->rollback();
                return false;
            }
        }
        //提交
        $this->commit();
        return true;

    }

    /**
     * 删除角色 物理删除
     * @param $id integer 要删除的项目
     * @return bool 返回执行结果 
     */
    public function deleteRole($id){
        //开启事务
        $this->startTrans();
        //删除角色并判断
        if($this->delete($id)===false){
            //错误信息
            $this->error='角色删除失败';
            //回滚
            $this->rollback();
            return false;
        }
        //实例化
        $role_permission_model = M('RolePermission');
        //删除对应的权限
        if($role_permission_model->where(['role_id'=>$id])->delete()===false){
            //错误信息
            $this->error='角色权限删除失败';
            //回滚
            $this->rollback();
            return false;
        }
        //提交
        $this->commit();
        return true;
    }
}