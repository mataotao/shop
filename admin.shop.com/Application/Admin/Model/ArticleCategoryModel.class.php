<?php
/**
 * Created by PhpStorm.
 * User: mttm
 * Date: 2016/6/25
 * Time: 13:03
 */

namespace Admin\Model;


use Think\Model;
use Think\Page;

class ArticleCategoryModel extends Model
{
    protected $patchValidate = true;

    protected $_validate = [
        ['name','require','分类名称不能为空'],
        ['name','','分类已存在',self::EXISTS_VALIDATE,'unique'],
        ['sort','number','排序必须为数字'],
        ['status','0,1','分类状态不合法',self::EXISTS_VALIDATE,'in'],
        ['is_help','0,1','是否是帮助文档?',self::EXISTS_VALIDATE,'in'],
    ];

    public function selectArticleCategory($cond){
        $pageSeting = C("PAGE_SETTING");
        $count = $this->where($cond)->count();
        $page = new Page($count,$pageSeting['PAGE_SIEZ']);
        $page->setConfig('theme',$pageSeting['PAGE_THEME']);
        $html = $page->show();
        $rows = $this->where($cond)->page(I('get.p',1),$pageSeting['PAGE_SIEZ'])->order('sort')->select();
        return compact(['rows','html']);
    }
}