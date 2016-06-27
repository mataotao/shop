<?php
/**
 * Created by PhpStorm.
 * User: mttm
 * Date: 2016/6/25
 * Time: 12:40
 */

namespace Admin\Controller;


use Think\Controller;

class ArticleController extends Controller
{
    /**
     * @var \Admin\Model\ArticleModel
     */
    private $_model = null;
    protected function _initialize(){
        $this->_model = D('Article');
    }
    public function index($id){
        $name = I('get.name');
        $cond['status'] = ['egt',0];
        $cond['article_category_id'] = $id;
        if($name){
            $cond['name']=['like','%'.$name.'%'];
        }
        $rows= $this->_model->selectArticle($cond);
        $this->assign('id',$id);
        $this->assign($rows);
        $this->display();
    }
    public function getparent(){
        $articleCategory_model = D('ArticleCategory');
        return $articleCategory_model->where(['status'=>['egt',0]])->field(['name','id'])->select();
    }
    public function add(){
        if(IS_POST){
            if($this->_model->create()===false){
                $this->error(get_error($this->_model));
            }
            if($this->_model->addArticle(I('post.'))===false){
                $this->error(get_error($this->_model));
            }else{
                $this->success('添加成功',U('ArticleCategory/index'));
            }
        }else{
            $parent = $this->getparent();
            $this->assign('parent',$parent);
            $this->display();
        }

    }
    public function edit($id){
        if(IS_POST){
            if($this->_model->create()===false){
                $this->error(get_error($this->_model));
            }
            if($this->_model->editArticle(I('post.'))===false){
                $this->error(get_error($this->_model));
            }else{
                $this->success('修改成功',U('ArticleCategory/index'));
            }
        }else{
            $row = $this->_model->editSelectArticle($id);
            $parent = $this->getparent();
            $this->assign('parent',$parent);
            $this->assign('row',$row);
            $this->display('add');

        }
    }
    public function remove($id){
        $data = [
            'id'=>$id,
            'status'=>-1,
            'name'=>['exp','concat(name,"_del")']
        ];
        if($this->_model->deleteArticle($data)===false){
            $this->error(get_error($this->_model));
        }else{
            $this->success('删除成功',U('ArticleCategory/index'));
        }
    }
}