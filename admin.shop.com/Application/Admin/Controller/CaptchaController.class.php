<?php
/**
 * Created by PhpStorm.
 * User: mttm
 * Date: 2016/7/4
 * Time: 12:37
 */

namespace Admin\Controller;


use Think\Controller;
use Think\Verify;

class CaptchaController extends Controller
{
    public function captcha(){
        $setting = [
            'length'=>4,
        ];
        $verify = new Verify($setting);
        $verify->entry();
    }
}