<?php
/**
 * Created by PhpStorm.
 * User: mttm
 * Date: 2016/6/25
 * Time: 12:41
 */

namespace Admin\Controller;


use Think\Controller;

class ArticleContentController extends Controller
{
    /**
     * @var \Admin\Model\ArticleContentModel
     */
    private $_model = null;
    protected function _initialize(){
        $this->_model = D('ArticleContent');
    }
    public function index($id){
        $row = $this->_model->selectArticleContent($id);
        $this->assign('row',$row);
        $this->display();
    }

}