<?php
/**
 * Created by PhpStorm.
 * User: mttm
 * Date: 2016/6/26
 * Time: 19:18
 */

namespace Admin\Model;


use Think\Model;

class ArticleContentModel extends Model
{
    public function selectArticleContent($id){
        return $this->field('article_content.content,article.name')->join('article on article_content.article_id = article.id ')->where(['article_id'=>$id])->find();
    }

}