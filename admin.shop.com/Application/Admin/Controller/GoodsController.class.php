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

    /**
     * //获取显示页面
     */
    public function index(){
        $cond=[];
        //获取条件
        //关键字
        $name = I('get.name');
        if($name){
            $cond['name']=['like','%'.$name."%"];
        }
        //分类
        $goods_category_id=I('get.goods_category_id');
        if($goods_category_id){
            $cond['goods_category_id']=$goods_category_id;
        }

        //品牌
        $brand_id=I('get.brand_id');
        if($brand_id){
            $cond['brand_id']=$brand_id;
        }

        //推荐
        $goods_status=I('get.goods_status');
        if($goods_status){
            $cond[]='goods_status &'.$goods_status;
        }

        //状态
        $is_on_sale=I('get.is_on_sale');
        if(strlen($is_on_sale)){
            $cond['is_on_sale']=$is_on_sale;
        }
        //分类
        $goods_category_model = D('GoodsCategory');
        $goods_categorys = $goods_category_model->getList();
        $this->assign('goods_categorys',$goods_categorys);
        //品牌
        $brand_model = D('Brand');
        $brands = $brand_model->getList();
        $this->assign('brands',$brands);
        //推荐
        $goodsStatus = [
          ['id'=>1,'name'=>'精品'],
          ['id'=>2,'name'=>'新品'],
          ['id'=>4,'name'=>'热销'],
        ];
        $this->assign('goodsStatus',$goodsStatus);
        //状态
        $isOnSale = [
          ['id'=>1,'name'=>'上架'],
          ['id'=>0,'name'=>'下架'],
        ];
        $this->assign('isOnSale',$isOnSale);
        //调用查看的方法
        $rows= $this->_model->getPageResult($cond);
        //赋值
        $this->assign($rows);
        //加载模板
        $this->display();
    }

    /**
     * 添加页面
     */
    public function add(){
        //检测是否是post方式
        if(IS_POST){
            //获取数据并验证
            if($this->_model->create()===false){
                //跳转并返回错误信息
                $this->error(get_error($this->_model));
            }
            //调用添加方法
            if($this->_model->addGoods()===false){
                //跳转并返回错误信息
                $this->error(get_error($this->_model));
            }else{
                //跳转并返回成功信息
                $this->success('添加成功',U('index'));
            }
        }else{
            //调用获取商品分类,商品品牌,供货商的方法
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
        //是否以post方式提交
        if(IS_POST){
            //获取数据并验证
            if($this->_model->create()===false){
                //跳转并返回错误信息
                $this->error(get_error($this->_model));
            }
            //调用修改方法
            if($this->_model->saveGoods()===false){
                //跳转并返回错误信息
                $this->error(get_error($this->_model));
            }else{
                //跳转并返回成功信息
                $this->success('修改成功',U('index'));
            }
        }else{
            //获取要显示的数据
            $row = $this->_model->getGoodsedit($id);
            //赋值
            $this->assign('row',$row);
            //用获取商品分类,商品品牌,供货商的方法
            $this->_before_view();
            //加载模板
            $this->display('add');
        }
    }

    /**
     * 逻辑删除
     * @param $id integer 要删除的id
     */
    public function remove($id){
        //设置要修改的参数
        $data = [
            'id'=>$id,
            'status'=>0,
            'name'=>['exp','concat(name,"_del")']
        ];
        //调用删除的方法
        if($this->_model->deleteGoods($data)===false){
            //错误跳转
            $this->error(get_error($this->_model));
        }else{
            //成功跳转
            $this->success('删除成功',U('index'));
        }
    }

    /**
     * 获取商品分类,获取品牌,获取供货商
     */
    public function _before_view(){
        //获取商品分类
        //实例化
        $goods_category_model = D('GoodsCategory');
        //调用获取方法
        $goods_categories = $goods_category_model->getList();
        //转化成json字符串赋值
        $this->assign('goods_categories',json_encode($goods_categories));

        //获取品牌
        //实例化
        $brand_model=D('Brand');
        //调用获取方法
        $brands = $brand_model->getList();
        //赋值
        $this->assign('brands',$brands);
        //获取供货商
        //实例化
        $supplier_model=D('Supplier');
        //调用获取方法
        $supplier = $supplier_model->getList();
        //赋值
        $this->assign('supplier',$supplier);
    }

    /**
     * ajax删除图片
     * @param $id integer 删除的id
     */
    public function removeGallery($id){
        //实例化
        $goods_gallery_model=D('GoodsGallery');
        //调用删除方法
        if($goods_gallery_model->delete($id)===false){
            //错误跳转
            $this->error('删除失败');
        } else{
            //成功跳转
            $this->success('删除成功');
        }
    }
}