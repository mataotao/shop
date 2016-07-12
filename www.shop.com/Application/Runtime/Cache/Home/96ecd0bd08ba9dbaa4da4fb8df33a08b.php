<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
    <title><?php echo ($meta_title); ?></title>
    <link rel="stylesheet" href="http://www.shop.com/Public/Home/css/base.css" type="text/css"/>
    <link rel="stylesheet" href="http://www.shop.com/Public/Home/css/global.css" type="text/css"/>
    <link rel="stylesheet" href="http://www.shop.com/Public/Home/css/header.css" type="text/css"/>
    <link rel="stylesheet" href="http://www.shop.com/Public/Home/css/login.css" type="text/css"/>
    <link rel="stylesheet" href="http://www.shop.com/Public/Home/css/footer.css" type="text/css"/>
    
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
        <h2 class="fl"><a href="<?php echo U('Index/index');?>"><img src="http://www.shop.com/Public/Home/images/logo.png" alt="啊咿呀哟"></a></h2>
    </div>
</div>
<!-- 页面头部 end -->


	<!-- 登录主体部分start -->
	<div class="login w990 bc mt10">
		<div class="login_hd">
			<h2>用户登录</h2>
			<b></b>
		</div>
		<div class="login_bd">
			<div class="login_form fl">
				<form action="<?php echo U();?>" method="post">
					<ul>
						<li>
							<label for="">用户名：</label>
							<input type="text" class="txt" name="username" />
						</li>
						<li>
							<label for="">密码：</label>
							<input type="password" class="txt" name="password" />
							<a href="">忘记密码?</a>
						</li>
						<li class="checkcode">
							<label for="">验证码：</label>
							<input type="text"  name="checkcode" />
							<img src="<?php echo U('Captcha/captcha',['nocache'=>NOW_TIME]);?>" alt="" id='img_captcha'/>
							<span>看不清？<a href="javascript:;" onclick="change_img_captcha()">换一张</a></span><span class="error"></span>
						</li>
						<li>
							<label for="">&nbsp;</label>
							<input type="checkbox" class="chb"  name="remember"/> 保存登录信息
						</li>
						<li>
							<label for="">&nbsp;</label>
							<input type="submit" value="" class="login_btn" />
						</li>
					</ul>
				</form>

				<div class="coagent mt15">
					<dl>
						<dt>使用合作网站登录商城：</dt>
						<dd class="qq"><a href=""><span></span>QQ</a></dd>
						<dd class="weibo"><a href=""><span></span>新浪微博</a></dd>
						<dd class="yi"><a href=""><span></span>网易</a></dd>
						<dd class="renren"><a href=""><span></span>人人</a></dd>
						<dd class="qihu"><a href=""><span></span>奇虎360</a></dd>
						<dd class=""><a href=""><span></span>百度</a></dd>
						<dd class="douban"><a href=""><span></span>豆瓣</a></dd>
					</dl>
				</div>
			</div>

			<div class="guide fl">
				<h3>还不是商城用户</h3>
				<p>现在免费注册成为商城用户，便能立刻享受便宜又放心的购物乐趣，心动不如行动，赶紧加入吧!</p>

				<a href="<?php echo U('reg');?>" class="reg_btn">免费注册 >></a>
			</div>

		</div>
	</div>
	<!-- 登录主体部分end -->


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
<script>
    $(function () {
        var url = '<?php echo U("Member/userinfo");?>';
        $.getJSON(url,function (username) {
            if(username){
                var html = '您好'+username+'，欢迎来到京西！[<a href="<?php echo U('Member/logout');?>">退出</a>]';
            }else{
                var html = '您好，欢迎来到京西！[<a href="<?php echo U('Member/login');?>">登录</a>] [<a href="<?php echo U('Member/reg');?>">免费注册</a>]';$('#userinfo').html(html);
            }
            $('#userinfo').html(html);
        })
    });
</script>

	<script type="text/javascript">
		function change_img_captcha() {
			$('#img_captcha').attr('src', '<?php echo U("Captcha/captcha");?>?nocache=' + new Date().getTime());
		}
	</script>

</body>
</html>