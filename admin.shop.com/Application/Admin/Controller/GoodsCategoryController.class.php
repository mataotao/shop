<?php
/**
 * Created by PhpStorm.
 * User: mttm
 * Date: 2016/6/27
 * Time: 19:22
 */

namespace Admin\Controller;


use Think\Controller;

class GoodsCategoryController extends Controller
{
    /**
     *  @var \Admin\Model\GoodsCategoryModel
     */
    private $_model = null;
    protected function _initialize(){
        $this->_model = D('GoodsCategory');
    }
    public function index(){
        $rows = $this->_model->getList();
        $this->assign('rows',$rows);
        $this->display();
    }
    public function add(){
        if(IS_POST){
            
        }else{
            $goods_categories = json_encode($this->_model->getList());
            $this->assign('goods_categories',$goods_categories);
            $this->display();
        }
    }
    public function edit($id){
        if(IS_POST){

        }else{
            $row = $this->_model->find($id);
            $goods_categories = json_encode($this->_model->getList());
            $this->assign('goods_categories',$goods_categories);
            $this->assign('row',$row);
            $this->display('add');
        }
    }
}