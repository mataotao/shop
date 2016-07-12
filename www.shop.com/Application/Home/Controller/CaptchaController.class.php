<?php
/**
 * Created by PhpStorm.
 * User: mttm
 * Date: 2016/7/5
 * Time: 17:23
 */

namespace Home\Controller;


use Think\Controller;
use Think\Verify;

class CaptchaController extends Controller
{
    public function captcha(){
        $verify = new Verify(['length'=>4]);
        $verify->entry();
    }
}