<?php

/**
 * Created by PhpStorm.
 * User: mttm
 * Date: 2016/7/4
 * Time: 15:51
 */
namespace Common\Behaviors;
use Think\Behavior;

class CheckPermissionBehavior extends Behavior
{
    public function run(&$params)
    {
        //获取访问的url
        $url = MODULE_NAME."/".CONTROLLER_NAME."/".ACTION_NAME;
        //取出忽略列表
        $ignore_setting = C('ACCESS_IGNORE');
        //所有用户都可以访问的url
        $ignore = $ignore_setting['IGNORE'];
        if(in_array($url,$ignore)){
            return true;
        }
        //获取用户信息
        $userInfo = login();
        if(isset($userInfo['username']) && $userInfo['username']=='admin' ){
            return true;
        }
        //判断有没有,没有就进行自动登录
        if(!$userInfo){
            $userInfo = D('Admin')->autologin();
        }
        //获取所有的权限地址
        $paths = permission_pathes();
        //登陆用户可见页面
        $user_ignore = $ignore_setting['USER_IGNORE'];
        $urls =$paths;
        if($userInfo){
            //登陆用户可见页面还要额外加上登陆后的忽略列表
            $urls = array_merge($urls,$user_ignore);
        }

        if(!in_array($url, $urls)){
            header('Content-Type: text/html;charset=utf-8');
            echo '<script type="text/javascript">top.location.href="'.U('Admin/Admin/login').'"</script>';
        }
    }

}