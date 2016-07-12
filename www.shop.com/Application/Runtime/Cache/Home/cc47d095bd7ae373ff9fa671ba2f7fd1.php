<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>购物车页面</title>
    <link rel="stylesheet" href="http://www.shop.com/Public/Home/css/base.css" type="text/css">
    <link rel="stylesheet" href="http://www.shop.com/Public/Home/css/global.css" type="text/css">
    <link rel="stylesheet" href="http://www.shop.com/Public/Home/css/header.css" type="text/css">
    <link rel="stylesheet" href="http://www.shop.com/Public/Home/css/footer.css" type="text/css">
    
	<link rel="stylesheet" type="text/css" href="http://www.shop.com/Public/Home/css/success.css" />


</head>
<body>
<!-- 顶部导航 start -->
<div class="topnav">
    <div class="topnav_bd w990 bc">
        <div class="topnav_left">

        </div>
        <div class="topnav_right fr">
            <ul>
                <li id="userinfo"></li>
                <li class="line">|</li>
                <li>我的订单</li>
                <li class="line">|</li>
                <li>客户服务</li>

            </ul>
        </div>
    </div>
</div>
<!-- 顶部导航 end -->

<div style="clear:both;"></div>

<!-- 页面头部 start -->
<div class="header w990 bc mt15">
    <div class="logo w990">
        <h2 class="fl"><a href="<?php echo U('Index/index');?>"><img src="http://www.shop.com/Public/Home/images/logo.png" alt="京西商城"></a></h2>
        <div class="flow fr <?php echo (ACTION_NAME); ?>">
            <ul>
                <li <?php if((ACTION_NAME) == "flow1"): ?>class="cur"<?php endif; ?>>1.我的购物车</li>
                <li <?php if((ACTION_NAME) == "flow2"): ?>class="cur"<?php endif; ?>>2.填写核对订单信息</li>
                <li <?php if((ACTION_NAME) == "flow3"): ?>class="cur"<?php endif; ?>>3.成功提交订单</li>
            </ul>
        </div>
    </div>
</div>
<!-- 页面头部 end -->

<div style="clear:both;"></div>

<!-- 主体部分 start -->

	<!-- 主体部分 start -->
	<div class="success w990 bc mt15">
		<div class="success_hd">
			<h2>订单提交成功</h2>
		</div>
		<div class="success_bd">
			<p><span></span>订单提交成功，我们将及时为您处理</p>

			<p class="message">完成支付后，你可以 <a href="<?php echo U('OrderInfo/index');?>">查看订单状态</a>  <a href="<?php echo U('Index/index');?>">继续购物</a> <a href="<?php echo U('Index/index');?>">问题反馈</a></p>
		</div>
	</div>
	<!-- 主体部分 end -->

<!-- 主体部分 end -->

<div style="clear:both;"></div>
<!-- 底部版权 start -->
<div class="footer w1210 bc mt15">
    <p class="links">
        <a href="">关于我们</a> |
        <a href="">联系我们</a> |
        <a href="">人才招聘</a> |
        <a href="">商家入驻</a> |
        <a href="">千寻网</a> |
        <a href="">奢侈品网</a> |
        <a href="">广告服务</a> |
        <a href="">移动终端</a> |
        <a href="">友情链接</a> |
        <a href="">销售联盟</a> |
        <a href="">京西论坛</a>
    </p>
    <p class="copyright">
        © 2005-2013 京东网上商城 版权所有，并保留所有权利。  ICP备案证书号:京ICP证070359号
    </p>
    <p class="auth">
        <a href=""><img src="http://www.shop.com/Public/Home/images/xin.png" alt="" /></a>
        <a href=""><img src="http://www.shop.com/Public/Home/images/kexin.jpg" alt="" /></a>
        <a href=""><img src="http://www.shop.com/Public/Home/images/police.jpg" alt="" /></a>
        <a href=""><img src="http://www.shop.com/Public/Home/images/beian.gif" alt="" /></a>
    </p>
</div>
<!-- 底部版权 end -->
<script type="text/javascript" src="http://www.shop.com/Public/Home/js/jquery.min.js"></script>
<script type="text/javascript">
    $(function(){
        //当页面加载完毕,动态获取当前的用户名
        //您好<?php echo ($userinfo["username"]); ?>，欢迎来到京西！[<a href="<?php echo U('Member/login');?>">登录</a>] [<a href="<?php echo U('Member/reg');?>">免费注册</a>]
        var url = '<?php echo U("Member/userinfo");?>';
        $.getJSON(url,function(username){
            if(username){
                var html1 = '您好'+username+'，欢迎来到京西！[<a href="<?php echo U('Member/logout');?>">退出</a>]';
            } else{
                var html1 = '您好，欢迎来到京西！[<a href="<?php echo U('Member/login');?>">登录</a>] [<a href="<?php echo U('Member/reg');?>">免费注册</a>]';

            }
            $('#userinfo').html(html1);
        });
    });
</script>

</body>
</html>