<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
        <meta content="yes" name="apple-mobile-web-app-capable">
        <meta content="black" name="apple-mobile-web-app-status-bar-style">
        <title>提交订单</title>
        <script src="/Public/Home/Saleman/js/zepto.min.js"></script>
        <link href="http://cdn.bootcss.com/weui/1.0.2/style/weui.min.css" rel="stylesheet">
        <link rel="stylesheet" href="/Public/Home/Saleman/stylesheets/index.css">
    </head>
    <body>
        <div class="setelOrder">
            <header class = "all_header">
                <a class = "h_back" href="/Home/Saleman/Saleman/orderMedicine"><img src="/Public/Home/Saleman/images/back_icon.png" alt=""></a>
                <span class = "h_title">提交订单</span>
                <a class = "h_add" href="javascript:void(0)"></a>
            </header>
            <div class="order">
                <div class="order_adress">
                    <a class = "adress_icon" href="javascript:void(0)"><img src="/Public/Home/Saleman/images/order_adress.png" alt=""></a>
                    <!-- <span></span> -->
                    <input class="weui-input address" style = "width:85%;"  type="text" placeholder="请输入订单地址" value = "" />
                </div>
                <?php if(is_array($cartList)): $i = 0; $__LIST__ = $cartList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$cartVo): $mod = ($i % 2 );++$i;?><div class="order_list">
                        <div class="order_name_w">
                            <span class = "order_name"><?php echo ($cartVo['drugs_name']); ?></span>
                            <span class = "order_del" pos_id="<?php echo ($cartVo['drugs_id']); ?>" pos_title="确定删除？">删除</span>
                        </div>
                        <div class="order_price_w">
                            <span class = "order_w">单价</span>
                            <span>¥<?php echo ($cartVo['price']); ?></span>
                        </div>
                        <div class="order_count">X<?php echo ($cartVo['num']); ?></div>
                        <div class="all_price_w">
                            <span class = "order_w">总价</span>
                            <span>¥<i class = "all_num"><?php echo ($cartVo['totalPrice']); ?></i></span>
                        </div>
                    </div><?php endforeach; endif; else: echo "" ;endif; ?>
                <div class="total_price_w">
                    <span class = "total_name">商品总额：</span>
                    <span>¥<i class = "total_num"><?php echo ($totalPrice); ?></i></span>
                </div>
            </div>
            <div class="weui-footer" style="margin-bottom: 81px;">
                <p class="weui-footer__text">提交订单后，将会有县总联系您送货，谢谢您的配合</p>
            </div>
            <div class="setel_order_w">
                <div class="setel_num"><span>¥</span><span class = "setle_prices"><?php echo ($totalPrice); ?></span>元</div>
                <div class="setel_btn" pos_title="确定提交订单？">提交订单</div>
            </div>
        </div>
        <script src = "/Public/Home/Saleman/js/index.js"></script>
    <div class="js_dialog" id="iosDialog1" style="display: none;">
    <div class="weui-mask"></div>
    <div class="weui-dialog">
        <div class="weui-dialog__hd"><strong class="weui-dialog__title">提示</strong></div>
        <div class="weui-dialog__bd confir_content"></div>
        <div class="weui-dialog__ft">
            <a href="javascript:;" class="weui-dialog__btn weui-dialog__btn_default cancel-btn">取消</a>
            <a href="javascript:;" class="weui-dialog__btn weui-dialog__btn_primary ok-btn">确定</a>
        </div>
    </div>
</div>

<div class="js_dialog" id="iosDialog2" style="display: none;">
    <div class="weui-mask"></div>
    <div class="weui-dialog">
        <div class="weui-dialog__bd sure-info"></div>
        <div class="weui-dialog__ft">
            <a href="javascript:;" class="weui-dialog__btn weui-dialog__btn_primary sure-btn" pos_url="location.href">知道了</a>
        </div>
    </div>
</div>
<script>
    $('.cancel-btn').on('click', function () {
        $('#iosDialog1').hide(200);
    });
    $('.sure-btn').on('click', function () {
        $('#iosDialog2').hide(200);

        var pos_url = $(this).attr('pos_url');

        if (pos_url != "") {
            location.href = location.href;
        }


    });
</script>
    <script>
        $(function () {
            $('.setel_btn').on('click', function () {
                var title = $(this).attr('pos_title');
                $('.confir_content').html(title);
                $('.ok-btn').attr('pos_op', 'submit');
                $("#iosDialog1").show(200);
            });

            $('.ok-btn').on('click', function () {
                //删除药品
                var op = $(this).attr('pos_op');
                if (op == 'delete') {
                    var drugs_id = $(this).attr('pos_id');
                    $.ajax({
                        url: "/Home/Saleman/Saleman/setelOrder/op/" + op,
                        type: 'post',
                        data: {drugs_id: drugs_id},
                        dataType: "json",
                        success: function (result) {
                            $("#iosDialog1").hide(200);
                            $('#iosDialog2').find('.sure-info').html(result['msg']);
                            $('#iosDialog2').show(200);
                        }
                    });
                } else if (op == 'submit') {
                    //提交订单
                    var address = $('.address').val();
                    if (address.length <= 0) {
                        $("#iosDialog1").hide(200);
                        $('#iosDialog2').find('.sure-info').html('请填写订单地址');
                        $('#iosDialog2').find('.sure-btn').attr('pos_url', '');
                        $('#iosDialog2').show(200);
                        return;
                    }
                    $.ajax({
                        url: "/Home/Saleman/Saleman/setelOrder/op/" + op,
                        type: 'post',
                        data: {address: address},
                        dataType: "json",
                        success: function (result) {

                            $("#iosDialog1").hide(200);
                            $('#iosDialog2').find('.sure-info').html(result['msg']);
                            
                           $('#iosDialog2').show(200);
                            
                            $(".weui-dialog__btn_primary").click(function(){
                             window.location.href="/Home/SaleManager/County/index.html";

                            })
                        }
                    });
                } else {

                }

            });

        });
    </script>
</body>
</html>