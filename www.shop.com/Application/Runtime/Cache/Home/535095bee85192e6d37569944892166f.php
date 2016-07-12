<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>购物车页面</title>
    <link rel="stylesheet" href="http://www.shop.com/Public/Home/css/base.css" type="text/css">
    <link rel="stylesheet" href="http://www.shop.com/Public/Home/css/global.css" type="text/css">
    <link rel="stylesheet" href="http://www.shop.com/Public/Home/css/header.css" type="text/css">
    <link rel="stylesheet" href="http://www.shop.com/Public/Home/css/footer.css" type="text/css">
    
    <link rel="stylesheet" href="http://www.shop.com/Public/Home/css/fillin.css" type="text/css">


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
    <div class="fillin w990 bc mt15">
        <div class="fillin_hd">
            <h2>填写并核对订单信息</h2>
        </div>

        <form action="<?php echo U('OrderInfo/add');?>" method="post">
            <div class="fillin_bd">
                <!-- 收货人信息  start-->
                <div class="address">
                    <h3>收货人信息</h3>
                    <div class="address_info">

                        <?php if(is_array($addresses)): foreach($addresses as $key=>$address): ?><p><input type="radio" value="<?php echo ($address["id"]); ?>"
                                      name="address_id"
                                <?php if(($address["is_default"]) == "1"): ?>checked="checked"<?php endif; ?>
                                /><?php echo ($address["name"]); ?> <?php echo ($address["tel"]); ?>
                                <?php echo ($address["province_name"]); ?> <?php echo ($address["city_name"]); ?>
                                <?php echo ($address["area_name"]); ?> <?php echo ($address["detail_address"]); ?>
                            </p><?php endforeach; endif; ?>
                    </div>
                </div>
                <!-- 收货人信息  end-->

                <!-- 配送方式 start -->
                <div class="delivery">
                    <h3>送货方式</h3>
                    <div class="delivery_info">
                        <?php if(is_array($deliveries)): foreach($deliveries as $key=>$delivery): ?><p>
                                <input type="radio" value="<?php echo ($delivery["id"]); ?>"
                                       price="<?php echo ($delivery["price"]); ?>"
                                       name="delivery_id"
                                <?php if(($delivery["is_default"]) == "1"): ?>checked="checked"<?php endif; ?>
                                /><?php echo ($delivery["name"]); ?> <?php echo ($delivery["price"]); ?>
                                <?php echo ($delivery["intro"]); ?>
                            </p><?php endforeach; endif; ?>
                    </div>
                </div>
                <!-- 配送方式 end -->

                <!-- 支付方式  start-->
                <div class="pay">
                    <h3>支付方式</h3>
                    <div class="pay_info">
                        <?php if(is_array($payments)): foreach($payments as $key=>$payment): ?><p>
                                <input type="radio" value="<?php echo ($payment["id"]); ?>"
                                       name="pay_type_id"
                                <?php if(($payment["is_default"]) == "1"): ?>checked="checked"<?php endif; ?>
                                /><?php echo ($payment["name"]); ?> <?php echo ($payment["intro"]); ?>
                            </p><?php endforeach; endif; ?>
                    </div>

                </div>
                <!-- 支付方式  end-->

                <!-- 发票信息 start-->
                <div class="receipt">
                    <h3>发票信息
                    </h3>
                    <div class="receipt_info">
                        <ul>
                            <li>
                                <label for="">发票抬头：</label>
                                <input type="radio" name="receipt_type"
                                       checked="checked" class="personal" value="1"/>个人
                                <input type="radio" name="receipt_type"
                                       class="company" value="2"/>单位
                                <input type="text" class="txt company_input"
                                       disabled="disabled" name="company_name"/>
                            </li>
                            <li>
                                <label for="">发票内容：</label>
                                <input type="radio" name="receipt_content_type"
                                       checked="checked" value="1"/>明细
                                <input type="radio" name="receipt_content_type" value="2"/>办公用品
                                <input type="radio" name="receipt_content_type" value="3"/>体育休闲
                                <input type="radio" name="receipt_content_type" value="4"/>耗材
                            </li>
                        </ul>
                    </div>

                </div>
                <!-- 发票信息 end-->

                <!-- 商品清单 start -->
                <div class="goods">
                    <h3>商品清单</h3>
                    <table>
                        <thead>
                        <tr>
                            <th class="col1">商品</th>
                            <th class="col3">价格</th>
                            <th class="col4">数量</th>
                            <th class="col5">小计</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if(is_array($goods_info_list)): foreach($goods_info_list as $key=>$goods): ?><tr>
                                <td class="col1"><a href=""><img
                                        src="<?php echo ($goods["logo"]); ?>" alt=""/></a>
                                    <strong><a
                                            href="<?php echo U('Index/goods',['id'=>$goods['id']]);?>"><?php echo ($goods["name"]); ?></a></strong>
                                </td>
                                <td class="col3">￥<?php echo ($goods["shop_price"]); ?></td>
                                <td class="col4"> <?php echo ($goods["amount"]); ?></td>
                                <td class="col5">
                                    <span>￥<?php echo ($goods["sub_total"]); ?></span></td>
                            </tr><?php endforeach; endif; ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="5">
                                <ul>
                                    <li>
                                        <span>总商品金额：</span>
                                        <em>￥<?php echo ($total_price); ?></em>
                                    </li>
                                    <li>
                                        <span>运费：</span>
                                        <em class="yunfei">￥10.00</em>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <!-- 商品清单 end -->

            </div>

            <div class="fillin_ft">
                <a href="javascript:;" onclick="do_submit()"><span>提交订单</span></a>
                <p>应付总额：<strong class="zongji">￥<?php echo ($total_price); ?>元</strong></p>

            </div>
        </form>

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

    <script type="text/javascript" src="http://www.shop.com/Public/Home/js/cart2.js"></script>
    <script type="text/javascript">
        $(function () {
            var total_price = <?php echo ($total_price); ?>;
            calc_price();
            $('.delivery_info input').change(calc_price);
            function calc_price() {
                var delivery_price = parseFloat($('.delivery_info input:checked').attr('price'));
                var zongji = parseFloat(total_price + delivery_price);
                $('.yunfei').text('￥' + delivery_price.toFixed(2));
                $('.zongji').text('￥' + zongji.toFixed(2) + '元');
            }
        });
        function do_submit(){
            $('form').get(0).submit();
        }
    </script>

</body>
</html>