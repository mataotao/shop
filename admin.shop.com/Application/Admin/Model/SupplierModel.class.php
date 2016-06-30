<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Model;
class SupplierModel extends \Think\Model{
    
    protected $patchValidate = true;

    protected $_validate = [
        ['name','require','供货商名称不能为空'],
        ['name','','供货商已存在',self::EXISTS_VALIDATE,'unique'],
        ['status','0,1','供货商状态不合法',self::EXISTS_VALIDATE,'in'],
        ['sort','number','排序必须为数字'],
    ];

    public function getPageResult(array $cond=[]) {
        $page_setting = C('PAGE_SETTING');
        $count = $this->where($cond)->count();
        $page = new \Think\Page($count,$page_setting['PAGE_SIZE']);
        $page->setConfig('theme', $page_setting['PAGE_THEME']);
        $page_html = $page->show();
        $rows = $this->where($cond)->page(I('get.p',1),$page_setting['PAGE_SIZE'])->select();
        return compact(['rows','page_html']);
        
    }

    public function getList(){
        return $this->where(['status' => ['gt', 0]])->select();
    }
}
