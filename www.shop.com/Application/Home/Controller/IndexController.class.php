<?php
namespace Home\Controller;

use Think\Controller;

class IndexController extends Controller
{
    protected function _initialize()
    {
        //title标记
        if (ACTION_NAME == 'index') {
            $show_category = true;
        } else {
            $show_category = false;
        }
        $this->assign('show_category', $show_category);
        S('goods_categories',null);
        //商品分类信息
        if (!$goods_categories = S('goods_categories')) {
            $goods_categories_model = D('GoodsCategory');
            //获取商品的信息
            $goods_categories = $goods_categories_model->getList('id,name,parent_id');
           // dump($goods_categories);exit;
            //存入缓存
            S('goods_categories', $goods_categories, 3600);
        }
        $this->assign('goods_categories', $goods_categories);
        //文章数据
        if (!$help_article_list = S('help_article_list')) {
            $help_article_list_model = D('Article');
            //获取文章数据
            $help_article_list = $help_article_list_model->getHelpList();
            //存入缓存
            S('help_article_list', $help_article_list, 3600);
        }
        $this->assign('help_article_list', $help_article_list);
        $this->assign('userinfo', login());
    }

    /**
     * goods_best_list 精品
     * goods_new_list 新品
     * goods_hot_list 热销
     */
    public function index()
    {
        $goods_model = D('Goods');
        //取出商品的状态
        $data = [
            'goods_best_list' => $goods_model->getListByGoodsStatus(1),
            'goods_new_list' => $goods_model->getListByGoodsStatus(2),
            'goods_hot_list' => $goods_model->getListByGoodsStatus(4),
        ];
        $this->assign($data);
        $this->display();
    }

    /**
     * 获取要查看的商品信息
     * @param $id int
     */
    public function goods($id)
    {
        $goods_model = D('Goods');
        if (!$row = $goods_model->getGoodsInfo($id)) {
            $this->error('您查看的商品离家出走了,下次动作快点哟', U('index'));
        }
        //dump($row);exit;
        $this->assign('row',$row);
        $this->display();
    }

}