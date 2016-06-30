<?php
/**
 * Created by PhpStorm.
 * User: mttm
 * Date: 2016/6/25
 * Time: 9:38
 */

namespace Admin\Model;


use Think\Model;
use Think\Page;
use Think\Upload;

class BrandModel extends Model
{
    protected $patchValidate = true;

    protected $_validate = [
        ['name','require','品牌名称不能为空'],
        ['name','','供货商已存在',self::EXISTS_VALIDATE,'unique'],
        ['status','0,1','品牌状态不合法',self::EXISTS_VALIDATE,'in'],
        ['sort','number','排序必须为数字'],
    ];
    
    public function selectBrand(array $cond){
        $pageSetting =C('PAGE_SETTING');
        $count = $this->where($cond)->count();
        $page = new Page($count,$pageSetting['PAGE_SIZE']);
        $page->setConfig('theme',$pageSetting['PAGE_THEME']);
        $html = $page->show();
        $rows = $this->where($cond)->page(I('get.p',1),$pageSetting['PAGE_SIZE'])->order('sort')->select();
        return compact(['rows','html']);


    }

    public function editBrand($file){
        if(!empty($file['name'])){
            $upload = new Upload(C('UPLOAD_SETTING'));
            $rst = $upload->uploadOne($file);
            $filePath = $rst['savepath'].$rst['savename'];
            $this->data['logo']=$filePath;
        }
        return $this->save();
    }

    public function getList(){
        return $this->where(['status' => ['gt', 0]])->select();
    }
   

}