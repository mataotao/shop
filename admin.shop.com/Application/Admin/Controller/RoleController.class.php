<?php
/**
 * Created by PhpStorm.
 * User: mttm
 * Date: 2016/7/1
 * Time: 18:04
 */

namespace Admin\Controller;


use Think\Controller;

class RoleController extends Controller
{
    /**
     * @var \Admin\Model\RoleModel
     */
    private $_model=null;
    protected function _initialize(){
        $this->_model = D('Role');
        
    }

    /**
     * 显示列表页面
     */
    public function index(){
        //设置条件
        $cond=[];
        //获取传来的搜索条件
        $name=I('get.name');
        //判断有没有传
        if($name){
            //拼接条件
            $cond['name']=['like',"%".$name."%"];
        }
        //调用显示方法并传入条件
        $rows = $this->_model->getPageResult($cond);
        //赋值
        $this->assign($rows);
        //引入模板
        $this->display();
        
    }

    /**
     * 添加页面
     */
    public function add(){
        //判断是否以post方式提交
        if(IS_POST){
            //获取值
            if($this->_model->create()===false){
                //错误跳转
                $this->error(get_error($this->_model));
            }
            //调用添加方法
            if($this->_model->addRole()===false){
                //错误跳转
                $this->error(get_error($this->_model));
            }else{
                //成功跳转
                $this->success('添加成功',U('index'));
            }
        }else{
            //获取权限
            $this->_before_view();
            //引入模板
            $this->display();
        }
        
    }

    /**
     * 修改页面
     * @param $id integer 要修改的id
     */
    public function edit($id){
        //判断是否以post方式提交
        if(IS_POST){
            //获取值
            if($this->_model->create()===false){
                //错误跳转
                $this->error(get_error($this->_model));
            }
            //调用修改方法
            if($this->_model->editRole($id)===false){
                //错误跳转
                $this->error(get_error($this->_model));
            }else{
                //成功跳转
                $this->success('修改成功',U('index'));
            }
        }else{
            //获取要修改的数据
            $row = $this->_model->getPermissionInfo($id);
            //赋值
            $this->assign('row',$row);
            //获取权限
            $this->_before_view();
            //引入模板
            $this->display('add');
        }
        
    }

    /**
     * 删除
     * @param $id integer 要删除的id
     */
    public function remove($id){
        //调用删除方法
        if($this->_model->deleteRole($id)===false){
            //错误跳转
            $this->error(get_error($this->_model));
        }else{
            //成功跳转
            $this->success('删除成功',U('index'));
        }
        
    }

    /**
     * 获取权限
     */
    public function _before_view(){
        //实例化
        $permission_model = D('Permission');
        //调用获取权限的方法
        $permission = $permission_model->getList();
        //转化成json并赋值
        $this->assign('permission',json_encode($permission));
    }
}