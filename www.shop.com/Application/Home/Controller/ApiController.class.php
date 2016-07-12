<?php
/**
 * Created by PhpStorm.
 * User: mttm
 * Date: 2016/7/5
 * Time: 18:13
 */

namespace Home\Controller;


use Think\Controller;

class ApiController extends Controller
{
    /**
     * 阿里大鱼
     * @param $tel int 电话号码
     */
    public function regSms($tel){
        vendor('Alidayu.TopSdk');
        $ak = '23399286';
        $sk = 'c567cc1b9e35237874e9bc101b71f689';
        $c = new \TopClient($ak,$sk);
        $req = new \AlibabaAliqinFcSmsNumSendRequest;
        $req->setSmsType("normal");
        $req->setSmsFreeSignName("马涛");
        $code = \Org\Util\String::randNumber(100000, 999999);
        //存到session
        session('reg_tel_code',$code);

        $data = [
            'product'=>'啊咿呀哟',
            'code'=> $code,
        ];
        $req->setSmsParam(json_encode($data));
        $req->setRecNum($tel);
        $req->setSmsTemplateCode("SMS_11555147");
        $r= $c->execute($req);
        dump($r);
    }

}