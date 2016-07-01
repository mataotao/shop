<?php
/**
 * Created by PhpStorm.
 * User: mttm
 * Date: 2016/7/1
 * Time: 16:21
 */

namespace Admin\Controller;


use Think\Controller;

class PermissionController extends Controller
{
    /**
     * @var \Admin\Model\PermissionModel
     */
    private $_model=null;
    
    protected function _initialize(){
        $this->_model=D('Permission');
        
    }

    /**
     * 列表显示页面
     */
    public function index(){
        //调用获取数据的方法
        $rows = $this->_model->getList();
        //赋值
        $this->assign('rows',$rows);
        //加载模板
        $this->display();
        
    }

    /**
     * 添加数据页面
     */
    public function add(){
        //判断是否以post方式提交
        if(IS_POST){
            //调用create方法,获取值,自动验证,自动完成
            if($this->_model->create()===false){
                //错误跳转
                $this->error($this->_model);
            }
            //调用添加方法
            if($this->_model->addpermission()===false){
                //错误跳转
                $this->error($this->_model);
            }else{
                //成功跳转
                $this->success('添加成功',U('index'));
            }
        }else{
            //获取父级权限
            $this->_before_view();
            //加载模板
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
            //调用create方法,获取值,自动验证,自动完成
            if($this->_model->create()===false){
                $this->error($this->_model);
            }
            //调用修改方法
            if($this->_model->editpermission()===false){
                //错误跳转
                $this->error($this->_model);
            }else{
                //成功跳转
                $this->success('修改成功',U('index'));
            }
            
        }else{
            //获取要修改的数据通过id
            $row = $this->_model->find($id);
            //赋值
            $this->assign('row',$row);
            //加载权限
            $this->_before_view();
            //引入模板
            $this->display('add');
        }
        
    }

    /**
     * 删除数据
     * @param $id integer 要删除的id
     */
    public function remove($id){
        //调用删除方法
        if($this->_model->deletepermision($id)===false){
            //错误跳转
            $this->error($this->_model);
        }else{
            //成功跳转
            $this->success('删除成功',U('index'));
        }
    }

    /**
     * 获取所有权限
     */
    protected function _before_view(){
        //调用方法
        $permissions = $this->_model->getList();
        //array_unshift在数组最左边添加数据
        array_unshift($permissions,['id'=>0,'name'=>'顶级权限','parent_id'=>null]);
        //赋值
        $this->assign('permissions',json_encode($permissions));
    }
    

}