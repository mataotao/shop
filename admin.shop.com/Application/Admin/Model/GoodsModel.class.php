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
    /**
     * @var bool 开启批量验证
     */
    protected $patchValidate = true;
    /**
     * @var array 自动验证
     */
    protected $_validate =[
        ['name','require','商品名称不能为空'],
        ['sn','','货号已存在',self::VALUE_VALIDATE,'unique'],
        ['goods_category_id','require','商品分类不能为空'],
        ['brand_id','require','商品品牌不能为空'],
        ['supplier_id','require','供应商不能为空'],
        ['market_price','require','市场售价不能为空'],
        ['market_price','currency','市场不合法'],
        ['supplier_id','require','本店售价不能为空'],
        ['supplier_id','currency','本店不合法'],
        ['stock','require','库存不能为空'],

    ];
    /**
     * @var array 自动完成
     */
    protected $_auto=[
      ['sn','createSn',self::MODEL_INSERT,"callback"],
      ['goods_status','Goodssum',self::MODEL_BOTH,"callback"],
      ['inputtime',NOW_TIME,self::MODEL_INSERT],
    ];

    protected function Goodssum($status){
        if(!isset($status)){
            //设置值
            return 0;
        }else{
            //因为是数组,而且是保存到一个字段里的,所以要要把它们加起来,多选是数组  array_sum 把数组的值加起来
            return array_sum($status);
        }
    }

    /**
     * @param $sn string 输入的货号
     * @return string    货号
     */
    protected function createSn($sn){

        //开启事务
        $this->startTrans();
        //如果有值
        if($sn){
            //直接返回
            return $sn;
        }
        //获取日期
        $date = date('ymd');
        //实例化
        $goods_num_model=M('GoodsNum');
        //通过数据表date获取值并判断
        if($num = $goods_num_model->getFieldBydate($date,'num')){
            //自加
            ++$num;
            //设置要更新到数据表的数据
            $data = ['date'=>$date,'num'=>$num];
            //调用方法更新
            $flag = $goods_num_model->save($data);
        }else{
            //设置值
            $num=1;
            //设置要添加到数据表的值
            $data = ['date'=>$date,'num'=>$num];

            //调用方法添加
            $flag = $goods_num_model->add($data);
        }
        //判读如果出错
        if($flag===false){
            //就回滚
            $this->rollback();
        }
        //拼接sn  str_pad填充字符串
        $sn = "SN".$date.str_pad($num,5,0,STR_PAD_LEFT);
        //返回拼接好的sn
        return $sn;

    }

    /**
     * 添加方法
     * @return bool
     */
    public function addGoods(){

        //添加到goods表 商品基本信息
        //获取添加成功的id
        $id = $this->add();
        //判断
        if($id===false){
            //回滚  这里没有开启事务是因为,它和拼接货号是连起来的,所以不用开启
            $this->rollback();
            return false;
        }

        //添加到商品内容表
        //获取输入的内容
        $content = I('post.content',"",false);
        //设置要添加的数组
        $data = [
            'goods_id'=>$id,
            'content' =>$content
        ];
        //实例化 基础模型 商品内容表
        $goods_intro_model = M('GoodsIntro');
        //添加到数据表并判断
        if($goods_intro_model->add($data)===false){
            //回滚
            $this->rollback();
            return false;
        }
        //相册
        //实例化 基础模型 商品相册表
        $goods_gallery_model = M('GoodsGallery');
        //把$data清空
        $data=[];
        //获取传入的路径
        $paths = i('post.path');
        //有可能是多条,所以要遍历
        foreach ($paths as $path) {
            $data[] = [
                'goods_id' => $id,
                'path' => $path
            ];
        }
        //判断,后面加括号是提高优先级
        if($data &&($goods_gallery_model->addAll($data)===false)){
            //回滚
            $this->rollback();
            return false;
        }
        //提交
        $this->commit();
        return true;

    }

    /**
     * 获取分页的信息
     * @param array $cond 查询条件
     * @return mixed
     */
    public function getPageResult(array $cond=[]){
        //拼接条件 array_merge 合并数组
        $cond = array_merge(['status'=>1],$cond);
        //获取分页的配置
        $pagesetting = C("PAGE_SETTING");
        //带条件获取总条数
        $count = $this->where($cond)->count();
        //实例化分页类
        $page = new Page($count,$pagesetting['PAGE_SIZE']);
        //设置要显示的内容
        $page->setConfig('theme',$pagesetting['PAGE_THEME']);
        //获取分页代码
        $page_html = $page->show();
        //获取分页的数据
        $rows = $this->where($cond)->page(I('page.p',1),$pagesetting['PAGE_SIZE'])->select();
        //因为精品,新品,热销是保存在一个字段里 所以要遍历判断(多条数据)
        foreach ($rows as $key => $value){
            $value['is_best'] = $value['goods_status'] & 1? true:false;
            $value['is_new'] = $value['goods_status'] & 2? true:false;
            $value['is_hot'] = $value['goods_status'] & 4? true:false;
            $rows[$key]=$value;
        }
        //返回数据  compact 建立一个数组，包括变量名和它们的值
        return compact('rows','page_html');
    }

    /**
     * 获取要修改的数据 获取商品信息 商品内容 商品图片地址
     * @param $id integer 要获取的id
     * @return mixed
     */
    public function getGoodsedit($id){
        //商品表
        //通过id获取一条数据
        $row = $this->find($id);
        $tmp = [];
        //因为前端要用js回显出来,所以要判断
        if($row['goods_status']&1){
            $tmp[]=1;
        }
        if($row['goods_status']&2){
            $tmp[]=2;
        }
        if($row['goods_status']&4){
            $tmp[]=4;
        }
        //转化成json
        $row['goods_status'] = json_encode($tmp);
        
        //商品内容表
        //实例化
        $goods_content_model = M('GoodsIntro');
        //动态获取,通过goods_id获取内容
        $row['content'] = $goods_content_model->getFieldByGoodsId($id,'content');
        
        //商品相册表
        //实例化
        $goods_gallery_model = M('GoodsGallery');
        //动态获取,通过goods_id获取路径
        $row['galleries']=$goods_gallery_model->getFieldByGoodsId($id,'id,path');
        //返回数据
        return $row;
    }

    /**
     * 修改数据的方法 商品表 商品内容表 商品相册
     * @return bool
     */
    public function saveGoods(){
        //商品表
        //保存要修改的id
        $id = $this->data['id'];
        //开启事务
        $this->startTrans();
        //保存商品信息并判断
        if($this->save()===false){
            //回滚
            $this->rollback();
            return false;
        }
        
        //商品内容表
        //获取传入的内容
        $content = I('post.content',"",false);
        //实例化
        $goods_content_model = M('GoodsIntro');
        //设置要更新的数据
        $data=[
            'goods_id'=>$id,
            'content'=>$content
        ];
        //更新并判断
        if($goods_content_model->save($data)===false){
            //回滚
            $this->rollback();
            return false;
        }
        
        //商品相册
        $paths = I('post.path');
        //判断时候新添加路径,原来的图片路径不会获取到
        if($paths){
            //实例化
            $goods_gallery_model = M('GoodsGallery');
            //清空数组
            $data=[];
            //有可能添加多张图片,所以要遍历
            foreach ($paths as $path){
                //设置要添加到数据表的数据
                $data[]=[
                    'goods_id'=>$id,
                    'path'=>$path
                ];
            }
            //调用添加方法并判断
            if($goods_gallery_model->addAll($data)===false){
                //回滚
                $this->rollback();
                return false;
            }
        }
        //提交
        $this->commit();
        return true;
        
    }

    /**
     * 删除商品 逻辑删除
     * @param $data
     * @return bool
     */
    public function deleteGoods($data){
        //开启事务
        $this->startTrans();
        //商品表
        //设置数据并判断
        if($this->setField($data)===false){
            //回滚
            $this->rollback();
            return false;
        }
        
        //商品内容表
        $goods_content_model = M('GoodsIntro');
        //删除内容并判断
        if($goods_content_model->delete($data['id'])===false){
            //回滚
            $this->rollback();
            return false;
        }
        //商品相册
        //实例化
        $goods_gallery_model=D('GoodsGallery');
        //删除路径并判断
        if($goods_gallery_model->where(['goods_id'=>$data['id']])->delete()===false){
            //回滚
            $this->rollback();
            return false;
        }
        //提交
        $this->commit();
        return true;
    }

}