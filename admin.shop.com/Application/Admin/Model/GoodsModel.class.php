<?php
/**
 * Created by PhpStorm.
 * User: mttm
 * Date: 2016/6/29
 * Time: 11:39
 */

namespace Admin\Model;


use Think\Model;
use Think\Page;

class GoodsModel extends Model
{
    protected $patchValidate = true;

    protected $_validate =[
        ['name','require','商品名称不能为空'],
        ['sn','','货号已存在',self::VALUE_VALIDATE],
        ['goods_category_id','require','商品分类不能为空'],
        ['brand_id','require','商品品牌不能为空'],
        ['supplier_id','require','供应商不能为空'],
        ['market_price','require','市场售价不能为空'],
        ['market_price','currency','市场不合法'],
        ['supplier_id','require','本店售价不能为空'],
        ['supplier_id','currency','本店不合法'],
        ['stock','require','库存不能为空'],

    ];
    protected $_auto=[
      ['sn','createSn',self::MODEL_INSERT,"callback"],
      ['goods_status','array_sum',self::MODEL_INSERT,"function"],
      ['inputtime',NOW_TIME,self::MODEL_INSERT],
    ];

    protected function createSn($sn){
        $this->startTrans();
        if($sn){
            return $sn;
        }
        $date = date('ymd');
        $goods_num_model=M('GoodsNum');
        if($num = $goods_num_model->getFieldBydate($date,'num')){
            ++$num;
            $data = ['date'=>$date,'num'=>$num];
            $flag = $goods_num_model->save($data);
        }else{
            $num=1;
            $data = ['date'=>$date,'num'=>$num];
            $flag = $goods_num_model->add($data);
        }
        if($flag===false){
            $this->rollback();
        }
        $sn = "SN".$date.str_pad($num,5,0,STR_PAD_LEFT);
        return $sn;
    }
    public function addGoods(){
        $id = $this->add();
        if($id===false){
            $this->rollback();
            return false;
        }
        $content = I('post.content',"",false);
        $data = [
            'goods_id'=>$id,
            'content' =>$content
        ];
        $goods_intro_model = M('GoodsIntro');
        if($goods_intro_model->add($data)===false){
            $this->rollback();
            return false;
        }
        //相册
        $goods_gallery_model = M('GoodsGallery');
        $data=[];
        $paths = i('post.path');
        foreach ($paths as $path) {
            $data[] = [
                'goods_id' => $id,
                'path' => $path
            ];
        }
        if($data &&($goods_gallery_model->addAll($data)===false)){
            $this->rollback();
            return false;
        }
        $this->commit();
        return true;

    }

    public function getPageResult(array $cond=[]){
        $cond = array_merge(['status'=>1],$cond);
        $pagesetting = C("PAGE_SETTING");
        $count = $this->where($cond)->count();
        $page = new Page($count,$pagesetting['PAGE_SIZE']);
        $page->setConfig('theme',$pagesetting['PAGE_THEME']);
        $page_html = $page->show();
        $rows = $this->where($cond)->page(I('page.p',1),$pagesetting['PAGE_SIZE'])->select();
        foreach ($rows as $key => $value){
            $value['is_best'] = $value['goods_status'] & 1? true:false;
            $value['is_new'] = $value['goods_status'] & 2? true:false;
            $value['is_hot'] = $value['goods_status'] & 4? true:false;
            $rows[$key]=$value;
        }
        return compact('rows','page_html');
    }

    public function getGoodsedit($id){
        $row = $this->find($id);
        $tmp = [];
        if($row['goods_status']&1){
            $tmp[]=1;
        }
        if($row['goods_status']&2){
            $tmp[]=2;
        }
        if($row['goods_status']&4){
            $tmp[]=4;
        }
        $row['goods_status'] = json_encode($tmp);
        $goods_content_model = M('GoodsIntro');
        $row['content'] = $goods_content_model->getFieldByGoodsId($id,'content');
        $goods_gallery_model = M('GoodsGallery');
        $row['galleries']=$goods_gallery_model->getFieldByGoodsId($id,'id,path');
        return $row;
    }
    public function saveGoods(){
        $id = $this->data['id'];
        $this->startTrans();
        if($this->data['goods_status']===NUll){
            $this->data['goods_status']=0;
        }else{
            $this->data['goods_status']=array_sum($this->data['goods_status']);
        }
        if($this->save()===false){

            $this->rollback();
            return false;
        }
        $content = I('post.content',"",false);
        $goods_content_model = M('GoodsIntro');
        $data=[
            'goods_id'=>$id,
            'content'=>$content
        ];
        if($goods_content_model->save($data)===false){
            $this->rollback();
            return false;
        }

        $paths = I('post.path');
        if($paths){
            $goods_gallery_model = M('GoodsGallery');
            $data=[];
            foreach ($paths as $path){
                $data[]=[
                    'goods_id'=>$id,
                    'path'=>$path
                ];
            }
            if($goods_gallery_model->addAll($data)===false){
                $this->rollback();
                return false;
            }
        }
        $this->commit();
        return true;
    }

    public function deleteGoods($data){
        $this->startTrans();
        if($this->setField($data)===false){
            $this->rollback();
            return false;
        }
        $goods_content_model = M('GoodsIntro');
        if($goods_content_model->delete($data['id'])===false){
            $this->rollback();
            return false;
        }
        $goods_gallery_model=D('GoodsGallery');
        if($goods_gallery_model->where(['goods_id'=>$data['id']])->delete()===false){
            $this->rollback();
            return false;
        }
        $this->commit();
        return true;
    }

}