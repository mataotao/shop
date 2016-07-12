<?php
/**
 * Created by PhpStorm.
 * User: mttm
 * Date: 2016/7/8
 * Time: 13:04
 */

namespace Home\Controller;


use Think\Controller;

class GoodsController extends Controller
{
    /**
     * 记录浏览次数到数据库
     * @param $id int
     */
    public function clickTimes($id){
        $goods_click_model = M('GoodsClick');
        //获取到该商品的浏览次数
        $num = $goods_click_model->getFieldByGoodsId($id,"click_times");
        if($num){
            ++$num;
            $data=[
              'goods_id'=>$id,
               'click_times' =>$num
            ];
            //保存到数据库
            $goods_click_model->save($data);
        }else{
            $num=1;
            $data=[
                'goods_id'=>$id,
                'click_times' =>$num
            ];
            //添加到数据库
            $goods_click_model->add($data);
        }
        //ajax传送数据
        $this->ajaxReturn($num);

    }

    /**
     * 浏览次数存到redis
     * @param $id int 商品的id
     */
    public function getClickTimes($id){
        $redis = get_redis();
        $key = 'goods_clicks';
        //存到redis,没有就添加
        $this->ajaxReturn($redis->zIncrBy($key,1,$id));
    }

    /**
     * 把redis的数据取出存到数据库
     * @return bool
     */
    public function syncGoodsClicks(){
        $redis = get_redis();
        $key = 'goods_clicks';
        //查找出所有的key对应的值
        //0为第一个,-1为最后一个
        $rows = $redis->zRange($key,0,-1,true);
        if(empty($rows)){
            return true;
        }
        //array_chunk可以将一个数组分成多个,适合大量数据插入
        $goods_click_model = M('GoodsClick');
        //把一个数组的所有键取出,放到一个新的数组里array_keys
        $keys = array_keys($rows);
        $goods_click_model->where(['goods_id'=>['in',$keys]])->delete();
        $data=[];
        foreach ($rows as $key=>$value){
            $data[]=[
              'goods_id'=>$key,
              'click_times'=>$value
            ];
        }
        //echo "<script>window.close();</script>";
        return $goods_click_model->addAll($data);

    }
}