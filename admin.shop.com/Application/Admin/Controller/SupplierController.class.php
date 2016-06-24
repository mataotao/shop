<?php

namespace Admin\Controller;

/**
 * 本控制器类用于完成供货商的管理工作。
 */
class SupplierController extends \Think\Controller {
    
    /**
     * @var \Admin\Model\SupplierModel 
     */
    private $_model = null;
    
    protected function _initialize(){
        $this->_model = D('Supplier');
    }


    public function index() {
        $name = I('get.name');
        $cond['status'] = ['egt',0];
        if($name){
            $cond['name']=['like','%'.$name.'%'];
        }
        $data = $this->_model->getPageResult($cond);
        $this->assign($data);
        $this->display();
    }

    public function add() {
        if(IS_POST){
            $this->_model = D('Supplier');
            if($this->_model->create()===false){
                $this->error(get_error($this->_model));
            }
            if($this->_model->add() === false){
                $this->error(get_error($this->_model));
            }else{
                $this->success('添加成功',U('index'));
            }
        }else{
            $this->display();
        }
    }
    public function edit($id) {
        if(IS_POST){
            if($this->_model->create() === false){
                $this->error(get_error($this->_model));
            }
            if($this->_model->save() === false){
                $this->error(get_error($this->_model));
            }
            $this->success('修改成功',U('index'));
        }else{
            $row = $this->_model->find($id);
            $this->assign('row',$row);
            $this->display('add');
        }
    }

    public function remove($id) {
        $data = [
            'id'=>$id,
            'status'=>-1,
            'name'=>['exp','concat(name,"_del")'],
        ];
        if($this->_model->setField($data) === false){
            $this->error(get_error($this->_model));
        }else{
            $this->success('删除成功',U('index'));
        }
    }

}
