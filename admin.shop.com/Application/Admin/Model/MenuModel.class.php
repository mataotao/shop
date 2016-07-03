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

    public function getList(){
        return $this->where(['status' => ['egt', 0]])->order('lft')->select();
    }

    public function addMenu(){
        $this->startTrans();
        unset($this->data[$this->getPk()]);
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

    public function getEditinfo($id){
        $row = $this->find($id);
        $menu_permission_model = M("MenuPermission");
        $row['permission_ids'] = json_encode($menu_permission_model->where(['menu_id'=>$id])->getField('permission_id',true));
        return $row;
    }

    public function editMenu($id){
        $this->startTrans();
        $parent_id = $this->getFieldById($id,'parent_id');
        if($this->data['parent_id']!=$parent_id){
            $orm = D('MySQL', 'Logic');
            $nestedsets = new NestedSets($orm, $this->getTableName(), 'lft', 'rght', 'parent_id', 'id', 'level');
            if($nestedsets->moveUnder($id,$this->data['parent_id'],'bottom')===false){
                $this->error='不能移动到自身或者后代中';
                $this->rollback();
                return false;
            }
        }

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

    public function deleteMenu($id){
        $this->startTrans();
        $menu_permission_model = M("MenuPermission");
        $info = $this->field('lft,rght')->find($id);
        $cond=[
            'lft'=>['egt',$info['lft']],
            'rght'=>['elt',$info['rght']]
        ];

        $menu_ids = $this->where($cond)->getField('id',true);
        if($menu_permission_model->where(['menu_id'=>['in',$menu_ids]])->delete()===false){
            $this->error = '删除历史关联失败';
            $this->rollback();
            return false;
        }

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
}