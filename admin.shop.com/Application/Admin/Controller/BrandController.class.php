<?php
/**
 * Created by PhpStorm.
 * User: mttm
 * Date: 2016/6/25
 * Time: 9:08
 */

namespace Admin\Controller;


use Think\Controller;

class BrandController extends Controller
{
    /**
     * @var \Admin\Model\BrandModel
     */
    private $_model = null;
    protected function _initialize(){
        $this->_model = D('Brand');
    }
    
    public function index(){
        $name=I('get.name');
        $cond['status']=['egt',0];
        if(!empty($name)){
            $cond['name']=$name;
        }
        $rows = $this->_model->selectBrand($cond);
        $this->assign($rows);
        $this->display();
    }

    public function add(){
        if(IS_POST){
            if($this->_model->create()===false){
                $this->error(get_error($this->_model));
            }
            if($this->_model->add()===false){
                $this->error(get_error($this->_model));
            }else{
                $this->success('添加成功',U('index'));
            }
        }else{
            $this->display();
        }
        
    }

    public function edit($id){
        if(IS_POST){
            if($this->_model->create()===false){
                $this->error(get_error($this->_model));
            }
            if($this->_model->editBrand($_FILES['logo'])===false){
                $this->error(get_error($this->_model));
            }else{
                $this->success('修改成功',U('index'));
            }
        }else{
            $row = $this->_model->find($id);
            $this->assign('row',$row);
            $this->display('add');
        }
    }

    public function remove($id){
        $data =[
            'id'=>$id,
            'status'=>-1,
            'name' =>['exp','concat(name,"_del")']
        ];
        if($this->_model->setField($data)===false){
            $this->error(get_error($this->_model));
        }else{
            $this->success('删除成功',U('index'));
        }
    }


}