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

    /**
     * 显示页面
     */
    public function index(){ //显示页面
        $rows = $this->_model->getList();  //获取数据
        $this->assign('rows',$rows);  //为变量赋值
        $this->display();  //引入模板
    }

    /**
     * 添加方法
     */
    public function add(){   //添加
        if(IS_POST){  //判断是否以post方式提交
            if($this->_model->create()==false){   //自动检测
                $this->error(get_error($this->_model));  //错误跳转
            }
            if($this->_model->addGoods()===false){   //调用添加方法
                $this->error(get_error($this->_model));  //错误跳转
            }else{
                $this->success('添加成功',U('index'));   //成功跳转
            }
        }else{
            $this->_before_view();   //调用方法
            $this->display();  //引入模板

        }
    }

    /**
     * 修改页面
     * @param $id integer 要修改的id
     * 
     */
    public function edit($id){  
        if(IS_POST){  //判断是否以post方式提交
            if($this->_model->create()===false){  //自动验证
                $this->error(get_error($this->_model));  //错误跳转
            }
            if($this->_model->editGategory()===false){  //调用修改方法
                $this->error(get_error($this->_model)); //错误跳转
            }else{
                $this->success('修改成功',U('index'));  //成功跳转
            }
        }else{
            $row = $this->_model->find($id);  //通过id获取一行数据
            $this->_before_view();  //调用方法
            $this->assign('row',$row);  //为变量赋值
            $this->display('add'); //加载模板
        }
    }

    /** 
     * 删除页面
     * @param $id integer 删除的id
     *
     */
    public function remove($id){  
        if($this->_model->deleteGategory($id)===false){  //调用删除方法
            $this->error(get_error($this->_model));  //错误跳转
        }else{
            $this->success('删除成功',U('index'));  //成功跳转
        }
    }
    public function _before_view(){  //为修改和添加页面  添加顶级分类
        $goods_categories = $this->_model->getList();   //获取字段
        array_unshift($goods_categories,['id'=>0,'name'=>"顶级分类",'parent_id'=>0]); //在数组最前面添加顶级分类
        $goods_categories = json_encode($goods_categories); //转化成json对象
        $this->assign('goods_categories',$goods_categories); //为变量赋值
    }
}