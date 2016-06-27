<?php
/**
 * Created by PhpStorm.
 * User: mttm
 * Date: 2016/6/25
 * Time: 14:42
 */

namespace Admin\Model;


use Think\Model;
use Think\Page;

class ArticleModel extends Model
{
    protected $patchValidate = true;

    protected $_validate = [
        ['name','require','文章名称不能为空'],
        ['name','','文章已存在',self::EXISTS_VALIDATE,'unique'],
        ['sort','number','排序必须为数字'],
        ['status','0,1','分类状态不合法',self::EXISTS_VALIDATE,'in'],
        //['article_category_id','1,2,3,4,5,6,7,8','分类不合法',self::EXISTS_VALIDATE,'in'],
    ];
    protected $_auto = [
        ['inputtime',NOW_TIME]
    ];

    public function addArticle($post){
        $this->startTrans();
        $id = $this->add();
        if($id===false){
            $this->rollback();
            return false;
        }
        $content = $post['content'];
        $article['article_id']=$id;
        $article['content'] = $content;
        $article_content_model = M('ArticleContent');
        if($article_content_model->add($article)===false){
            $this->rollback();
            return false;
        }
       return $this->commit();

    }

    public function selectArticle($cond){
        $pageSeting = C("PAGE_SETTING");
        $count = $this->where($cond)->count();
        $page = new Page($count,$pageSeting['PAGE_SIEZ']);
        $page->setConfig('theme',$pageSeting['PAGE_THEME']);

//        $name = I('get.name');
//        $con['article.status'] = ['egt',0];
//        $con['article.article_category_id'] = $id;
//        if($name){
//            $con['article.name']=['like','%'.$name.'%'];
//        }
//        $rows = $this->where($con)->order('article.sort')->page(I('get.p'),$pageSeting['PAGE_SIEZ'])->join(' article_category ON article.article_category_id=article_category.id ')->select();


//        $name = I('get.name');
//        $option = [
//            'a.status'=>['egt',0],
//            'b.id'=>['exp','=a.article_category_id'],
//            'a.article_category_id'=>$id
//        ];
//        if($name){
//            $option['a.name']=['like','%'.$name.'%'];
//        }
//        $arr=[
//            'article'=>'a',
//            'article_category'=>'b'
//        ];

       // $rows = $this->field('a.*,b.name name1')->table($arr)->where($option)->order('a.sort')->page(I('get.p'),$pageSeting['PAGE_SIEZ'])->select();

        //dump($this->getLastSql());exit;
        //dump($rows);exit;
        $rows = $this->where($cond)->order('sort')->page(I('get.p'),$pageSeting['PAGE_SIEZ'])->select();
        $html = $page->show();
        $article_category_model = D('ArticleCategory');
        $classify = $article_category_model->where(['status'=>['egt',0]])->getField('id,name');
        return compact(['rows','html','classify']);
    }
    
    public function editSelectArticle($id){
        return $this->join("article_content on article_content.article_id=article.id")->find($id);
    }
    public function editArticle($post){
        $this->startTrans();
        if($this->save()===false){
            $this->rollback();
            return false;
        }
        $content = $post['content'];
        $id = $post['id'];
        $article_content_model = M('ArticleContent');
        if($article_content_model->where(['article_id'=>$id])->save(['content'=>$content])===false){
            $this->rollback();
            return false;
        }

        return $this->commit();

    }

    public function deleteArticle($data){
        $this->startTrans();
        $id = $data['id'];
        if($this->setField($data)===false){
            $this->rollback();
            return false;
        }
        $article_content_model = M('ArticleContent');
        if($article_content_model->where(['article_id'=>$id])->delete()===false){
            $this->rollback();
            return false;
        }

        return $this->commit();

    }

}