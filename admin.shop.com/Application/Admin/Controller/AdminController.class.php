<?php
/**
 * Created by PhpStorm.
 * User: mttm
 * Date: 2016/7/2
 * Time: 11:00
 */

namespace Admin\Controller;


use Think\Controller;

class AdminController extends Controller
{
    /**
     * @var \Admin\Model\AdminModel
     */
    private $_model = null;

    public function _initialize(){
        $this->_model = D('Admin');
    }

    public function index(){
        $name = I('get.name');
        $cond=[];
        if($name){
            $cond['username']=['like',"%".$name."%"];
        }
        $rows = $this->_model->getPageResult($cond);
        $this->assign($rows);
        //引入模板
        $this->display();
    }

    public function add(){
        if(IS_POST){
            if($this->_model->create("",'register')===false){
                $this->error(get_error($this->_model));
            }
            if($this->_model->addAdmin()===false){
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
        if(IS_POST){
            if($this->_model->create()===false){
                $this->error(get_error($this->_model));
            }
            if($this->_model->editAdmin($id)===false){
                $this->error(get_error($this->_model));
            }else{
                $this->success('修改成功',U('index'));
            }
        }else{
            $row = $this->_model->getInfo($id);
            $this->assign('row',$row);
            $this->_before_view();
            $this->display('add');
        }
    }

    public function remove($id){
        if($this->_model->deleteAdmin($id)===false){
            $this->error(get_error($this->_model));
        }else{
            $this->success('删除成功',U('index'));
        }
    }

    public function reset($id){
        if(IS_POST){
            if($this->_model->create("",'resetpassword')===false){
                $this->error(get_error($this->_model));
            }
            if(($row=$this->_model->resetAdmin($id))===false){
                $this->error(get_error($this->_model));
            }else{
                $this->success('重置成功',U('index'));
            }
        }else{
            $this->assign('id',$id);
            $this->display();
        }
    }
    private function _before_view(){
        $role_model = D('Role');
        $roles = $role_model->getList();
        $this->assign('roles',json_encode($roles));
    }
}