<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends Controller {
    /**
     * 框架
     */
    public function index(){
        $this->display();
    }

    /**
     * 头部
     */
    public function top(){
        //获取用户数据
        $userinfo = login();
        $this->assign('userinfo',$userinfo);
        $this->display();
    }

    /**
     * 菜单
     */
    public function menu(){
        $menu_model = D('Menu');
        $userinfo = login();
        $this->assign('userinfo',$userinfo);
        //获取登录用户所拥有的菜单
        $menus = $menu_model->getMenuList();
        $this->assign('menus',$menus);
        $this->display();
    }

    /**
     * 主体
     */
    public function main(){
        $this->display();
    }
}