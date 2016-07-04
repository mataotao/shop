<?php
/**
 * Created by PhpStorm.
 * User: mttm
 * Date: 2016/7/2
 * Time: 15:31
 */

namespace Admin\Model;


use Admin\Logic\NestedSets;
use Think\Model;

class MenuModel extends Model
{
    protected $_validate=[
      ['name','require','菜单名必须填写']  
    ];

    /**
     * 获取显示的数据
     * @return mixed 数据
     */
    public function getList(){
        return $this->where(['status' => ['egt', 0]])->order('lft')->select();
    }

    /**
     * 添加数据
     * @return bool 执行结果
     */
    public function addMenu(){
        $this->startTrans();
        //删除主键
        unset($this->data[$this->getPk()]);
        //ztree添加
        $orm = D('MySQL', 'Logic');
        $nestedsets = new NestedSets($orm, $this->getTableName(), 'lft', 'rght', 'parent_id', 'id', 'level');
        if (($id = $nestedsets->insert($this->data['parent_id'], $this->data, 'bottom')) === false) {
            $this->error = '添加菜单失败';
            $this->rollback();
            return false;
        }
        //菜单权限表
        $permission_ids = I('post.permission_id');
        $cond=[];
        foreach ($permission_ids as $permission_id){
            $cond[]=[
                'menu_id'=>$id,
                'permission_id'=>$permission_id
            ];
        }
        if($cond){
            $menu_permission_model = M("MenuPermission");
            if($menu_permission_model->addAll($cond)===false){
                $this->error='添加菜单权限关联失败';
                $this->rollback();
                return false;
            }
        }
        
        $this->commit();
        return true;
    }

    /**
     * 获取要编辑的信息
     * @param $id int 获取信息的id
     * @return mixed 数据
     */
    public function getEditinfo($id){
        $row = $this->find($id);
        $menu_permission_model = M("MenuPermission");
        $row['permission_ids'] = json_encode($menu_permission_model->where(['menu_id'=>$id])->getField('permission_id',true));
        return $row;
    }

    /**
     * 修改数据
     * @param $id int 编辑的id
     * @return bool 执行结果
     */
    public function editMenu($id){
        $this->startTrans();
        //通过id获取原来的父级id
        $parent_id = $this->getFieldById($id,'parent_id');
        //判断有没有修改父级菜单
        if($this->data['parent_id']!=$parent_id){
            $orm = D('MySQL', 'Logic');
            $nestedsets = new NestedSets($orm, $this->getTableName(), 'lft', 'rght', 'parent_id', 'id', 'level');
            if($nestedsets->moveUnder($id,$this->data['parent_id'],'bottom')===false){
                $this->error='不能移动到自身或者后代中';
                $this->rollback();
                return false;
            }
        }
        
        //菜单权限关联表
        $permission_ids = I('post.permission_id');
        $cond=[];
        $menu_permission_model = M("MenuPermission");
        if($menu_permission_model->where(['menu_id'=>$id])->delete()===false){
            $this->error='删除历史权限失败';
            $this->rollback();
            return false;
        }

        foreach ($permission_ids as $permission_id){
            $cond[]=[
                'menu_id'=>$id,
                'permission_id'=>$permission_id
            ];
        }
        if($cond){
            $menu_permission_model = M("MenuPermission");
            if($menu_permission_model->addAll($cond)===false){
                $this->error='添加菜单权限关联失败';
                $this->rollback();
                return false;
            }
        }
        
        if($this->save()===false){
            $this->error='角色基本信息修改失败';
            $this->rollback();
            return false;
        }
        $this->commit();
        return true;

    }

    /**
     * 删除数据
     * @param $id int 要删除的id
     * @return bool 执行结果
     */
    public function deleteMenu($id){
        $this->startTrans();
        //菜单权限关联表
        $menu_permission_model = M("MenuPermission");
        //获取要删除的本身和子类
        $info = $this->field('lft,rght')->find($id);
        //设置查找条件
        $cond=[
            'lft'=>['egt',$info['lft']],
            'rght'=>['elt',$info['rght']]
        ];
        //查找对应的id
        $menu_ids = $this->where($cond)->getField('id',true);
        //删除所有对应的数据
        if($menu_permission_model->where(['menu_id'=>['in',$menu_ids]])->delete()===false){
            $this->error = '删除历史关联失败';
            $this->rollback();
            return false;
        }
        
        //ztree
        $orm = D('MySQL','Logic');
        $nestedsets = new NestedSets($orm,$this->trueTableName,'lft','rght','parent_id','id','level');
        if($nestedsets->delete($id)===false){
            $this->error='角色信息删除失败';
            $this->rollback();
            return false;
        }
        $this->commit();
        return true;
    }

    /**
     * @return array 登录用户所拥有的菜单
     */
    public function getMenuList()
    {
        //获取登录信息
        $userInfo = login();

        if($userInfo['username']=='admin'){
            //获取所有菜单
           $menus = $this->distinct(true)->field('id,name,parent_id,path')->alias('m')->join('__MENU_PERMISSION__ mp on m.id=mp.menu_id')->select();
        }else{
            //获取权限用户所拥有的权限id
            $pids = permission_pids();
            if($pids){
                //获取对应的菜单信息
                $menus= $this->distinct(true)->field('id,name,parent_id,path')->alias('m')->join('__MENU_PERMISSION__ mp on m.id=mp.menu_id')->where(['permission_id'=>['in',$pids]])->select();
            }else{
                $menus=[];
            }
        }
       return $menus;
    }
    
}