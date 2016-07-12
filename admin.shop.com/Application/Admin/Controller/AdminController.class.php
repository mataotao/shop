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

    /**
     * 显示列表页面
     */
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

    /**
     * 添加信息
     */
    public function add(){
        if(IS_POST){
            //设置create的场景方便自动完成和自动验证
            if($this->_model->create("",'register')===false){
                $this->error(get_error($this->_model));
            }
            if($this->_model->addAdmin()===false){
                $this->error(get_error($this->_model));
            }else{
                $this->success('添加成功',U('index'));
            }
        }else{
            //调用获取所有角色的方法
            $this->_before_view();
            $this->display();
        }
    }

    /**
     * 修改数据
     * @param $id int 要修改的id
     */
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
            //获取要修改的数据
            $row = $this->_model->getInfo($id);
            $this->assign('row',$row);
            //调用获取所有角色的方法
            $this->_before_view();
            $this->display('add');
        }
    }

    /**
     * 删除数据
     * @param $id int 删除的id
     */
    public function remove($id){
        if($this->_model->deleteAdmin($id)===false){
            $this->error(get_error($this->_model));
        }else{
            $this->success('删除成功',U('index'));
        }
    }

    /**
     * 重置密码
     * @param $id int 重置密码的id
     */
    public function reset($id){
        if(IS_POST){
            //设置create的场景方便自动完成和自动验证
            if($this->_model->create("",'resetpassword')===false){
                $this->error(get_error($this->_model));
            }
            if(($row=$this->_model->resetAdmin($id))===false){
                $this->error(get_error($this->_model));
            }else{
                echo "<script>alert('密码修改成功密码为{$row['password']}')</script>";
                $this->success("",U('index/main'),0);
            }
        }else{
            $this->assign('id',$id);
            $this->display();
        }
    }

    /**
     * 获取所有角色的数据
     */
    private function _before_view(){
        $role_model = D('Role');
        $roles = $role_model->getList();
        $this->assign('roles',json_encode($roles));
    }
    
    public function login(){
        if(IS_POST){
            if($this->_model->create()===false){
                $this->error(get_error($this->_model));
            }
            if($this->_model->login()===false){
                $this->error(get_error($this->_model));
            }else{
                $this->success('登录成功',U('Index/index'));
            }
        }else{
            $this->display();
        }
    }

    public function logout(){
        session(null);
        cookie(null);
        $this->success('退出成功',U('login'));
    }
}