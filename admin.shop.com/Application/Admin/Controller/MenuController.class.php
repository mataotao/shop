<?php
/**
 * Created by PhpStorm.
 * User: mttm
 * Date: 2016/7/2
 * Time: 15:25
 */

namespace Admin\Controller;


use Think\Controller;

class MenuController extends Controller
{
    /**
     * @var \Admin\Model\MenuModel
     */
    private $_model=null;

    public function _initialize(){
        $this->_model = D('Menu');
    }

    /**
     * 显示列表
     */
    public function index(){
        $rows = $this->_model->getList();
        $this->assign('rows',$rows);
        $this->display();
    }

    /**
     * 添加数据
     */
    public function add(){
        if(IS_POST){
            if($this->_model->create()===false){
                $this->error(get_error($this->_model));
            }
            if($this->_model->addMenu()===false){
                $this->error(get_error($this->_model));
            }else{
                $this->success('添加成功',U('index'));
            }
        }else{
            //调用获取权限和菜单信息的方法
            $this->_before_view();
            $this->display();
        }
    }

    /**
     * 修改数据
     * @param $id int 修改数据的id
     */
    public function edit($id){
        If(IS_POST){
            if($this->_model->create()===false){
                $this->error(get_error($this->_model));
            }
            if($this->_model->editMenu($id)===false){
                $this->error(get_error($this->_model));
            }else{
                $this->success('修改成功',U('index'));
            }
        }else{
            //调用获取要修改数据的方法
            $row = $this->_model->getEditinfo($id);
            $this->assign('row',$row);
            //调用获取权限和菜单信息的方法
            $this->_before_view();
            $this->display('add');
        }
    }

    /**
     * 删除数据
     * @param $id int 删除数据的id
     */
    public function remove($id){
        if($this->_model->deleteMenu($id)==false){
            $this->error(get_error($this->_model));
        }else{
            $this->success('删除成功',U('index'));
        }
        $this->display();
    }

    /**
     * 调用获取权限和菜单信息
     */
    private function _before_view(){
        //权限
        $permission_model = D('Permission');
        $permissions = $permission_model->getList();
        $this->assign('permissions',json_encode($permissions));
        //菜单
        $menus = $this->_model->getList();
        array_unshift($menus,['id'=>0,'name'=>'顶级菜单','parent_id'=>null]);
        $this->assign('menus',json_encode($menus));
    }
}