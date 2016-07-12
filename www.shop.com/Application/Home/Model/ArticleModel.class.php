<?php
/**
 * Created by PhpStorm.
 * User: mttm
 * Date: 2016/7/7
 * Time: 17:56
 */

namespace Home\Model;


use Think\Model;

class ArticleModel extends Model{
	/**
	 * 获取文章列表
	 * @return array
	 */
	public function getHelpList(){
		$article_category_model = M('ArticleCategory');
		$article_categories     = $article_category_model->where(['status' => 1 , 'is_help' => 1])->getField('id,name');
		$return                 = [];
		foreach($article_categories as $key => $article_category){
			$articles                  = $this->field('id,name')->order('sort')->limit(6)->where(['status' => 1 , 'article_category_id' => $key])->select();
			$return[$article_category] = $articles;
		}
		return $return;

	}
}