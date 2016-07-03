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

    public function index(){
        $rows = $this->_model->getList();
        $this->assign('rows',$rows);
        $this->display();
    }

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
            $this->_before_view();
            $this->display();
        }
    }

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
            $row = $this->_model->getEditinfo($id);
            $this->assign('row',$row);
            $this->_before_view();
            $this->display('add');
        }
    }

    public function remove($id){
        if($this->_model->deleteMenu($id)==false){
            $this->error(get_error($this->_model));
        }else{
            $this->success('删除成功',U('index'));
        }
        $this->display();
    }

    private function _before_view(){
        $permission_model = D('Permission');
        $permissions = $permission_model->getList();
        $this->assign('permissions',json_encode($permissions));

        $menus = $this->_model->getList();
        array_unshift($menus,['id'=>0,'name'=>'顶级菜单','parent_id'=>null]);
        $this->assign('menus',json_encode($menus));
    }
}