<?php
/**
 * Created by PhpStorm.
 * User: mttm
 * Date: 2016/6/29
 * Time: 11:35
 */

namespace Admin\Controller;


use Think\Controller;

class GoodsController extends Controller
{
    /**
     * @var \Admin\model\GoodsModel
     */
    private $_model=null;
    protected function _initialize(){
        $this->_model=D('Goods');
    }
    public function index(){
        $rows= $this->_model->getPageResult();
        $this->assign($rows);
        $this->display();
    }
    public function add(){
        if(IS_POST){
            if($this->_model->create()===false){
                $this->error(get_error($this->_model));
            }
            if($this->_model->addGoods()===false){
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
            if($this->_model->saveGoods()===false){
                $this->error(get_error($this->_model));
            }else{
                $this->success('修改成功',U('index'));
            }
        }else{
            $row = $this->_model->getGoodsedit($id);
            $this->assign('row',$row);
            $this->_before_view();
            $this->display('add');
        }
    }
    public function remove($id){
        $data = [
            'id'=>$id,
            'status'=>0,
            'name'=>['exp','concat(name,"_del")']
        ];
        if($this->_model->deleteGoods($data)===false){
            $this->error(get_error($this->_model));
        }else{
            $this->success('删除成功',U('index'));
        }
    }
    public function _before_view(){
        //获取商品分类
        $goods_category_model = D('GoodsCategory');
        $goods_categories = $goods_category_model->getList();
        $this->assign('goods_categories',json_encode($goods_categories));

        //获取品牌
        $brand_model=D('Brand');
        $brands = $brand_model->getList();
        $this->assign('brands',$brands);
        //获取供货商
        $supplier_model=D('Supplier');
        $supplier = $supplier_model->getList();
        $this->assign('supplier',$supplier);
    }

    public function removeGallery($id){
        $goods_gallery_model=D('GoodsGallery');
        if($goods_gallery_model->delete($id)===false){
            $this->error('删除失败');
        } else{
            $this->success('删除成功');
        }
    }
}