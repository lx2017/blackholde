<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
        <meta content="yes" name="apple-mobile-web-app-capable">
        <meta content="black" name="apple-mobile-web-app-status-bar-style">
        <title>订购药品</title>
        <link href="http://cdn.bootcss.com/weui/1.0.2/style/weui.min.css" rel="stylesheet">
        <link rel="stylesheet" href="/Public/Home/Saleman/stylesheets/index.css">
        <link rel="stylesheet" href="/Public/Home/Saleman/stylesheets/dropload.css">
        <style>
            .dropload-down{ clear: both;}
            .weui-grids{margin-bottom: 75px;}
        </style>
    </head>
    <body>
        <div class="orderMedicine">
            <header class = "all_header">
                <a class = "h_back" href="/Home/Saleman/Saleman/index"><img src="/Public/Home/Saleman/images/back_icon.png" alt=""></a>
                <span class = "h_title">订购药品</span>&nbsp;
                <span class = "h_title" style="font-size:10px;line-height:35px;" id="order">我的订单</span>
                <a class = "h_add" href="javascript:void(0)"></a>
            </header>
            <div class="weui-grids">
                <div class="list" style="clear: both;">
                </div>
            </div>
            <!--  -->
            <footer class = "cart_w">
                <div class = "cart_price">
                    <div class="balance">
                        <img src="/Public/Home/Saleman/images/cart_icon.png" alt="">
                        <span class = "good_num"><?php echo ($cartList['num']); ?></span>
                    </div>
                    <div class="cart_num">
                        <span>共计：</span>
                        <span class = "allPrices"><?php echo ($cartList['total_price']); ?></span>
                        <span>元</span>
                    </div>
                </div>
                <a class="set_btn" href="/Home/Saleman/Saleman/setelOrder">
                    去结算
                </a>
            </footer>
        </div>

        <script src="/Public/Home/Saleman/js/zepto.min.js"></script>
        <script src = "/Public/Home/Saleman/js/index.js"></script>
        <script src = "/Public/Home/Saleman/js/dropload.min.js"></script>
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
        $("#order").click(function(){

             window.location.href="/Home/Saleman/Saleman/Myorder";
        })
        $(function () {
            var page = 0;

            GetData();

            // dropload
            /*$('.weui-grids').dropload({
                scrollArea: window,
                loadDownFn: function (me) {
                    $.ajax({
                        type: 'post',
                        url: '/Home/Saleman/Saleman/orderMedicine/op/list',
                        dataType: 'json',
                        data: {page: page},
                        success: function (result) {
                            page++;
                            var data = result['data'];

                            if (data == null) {
                                // 锁定
                                me.lock();
                                // 无数据
                                me.noData();
                                me.resetload();
                                return;
                            }

                            var str = '';
                            for (var i = 0; i < data.length; i++) {
                                str += '<a href="/Home/Saleman/Saleman/medicineInfo/id/' + data[i]['id'] + '" class="weui-grid">';
                                str += '<div class="medicine_img">';
                                str += '<img class = "weui-media-box__thumb" src="' + data[i]['cover_img'] + '" alt="">';
                                str += '</div>';
                                str += '<p class="weui-grid__label">' + data[i]['name'] + '</p>';
                                str += '<p class = "meidice_price_c">$<span class="medicine_price">' + data[i]['price'] + '</span></p>';
                                str += '<span class = "add_cart" pos_title="确定加入购物车" pos_id="' + data[i]['id'] + '">加入购物车</span>';
                                str += '</a>';
                            }
                            $('.list').append(str);
                            me.resetload();
                        },
                        error: function (xhr, type) {
                            alert('刷新失败');
                            // 即使加载出错，也得重置
                            me.resetload();
                        }
                    });
                }
            });*/

            function GetData() {

                $.ajax({
                    type: 'post',
                    url: '/Home/Saleman/Saleman/orderMedicine/op/list',
                    dataType: 'json',
                    data: {page: page},
                    success: function (result) {
                        page++;
                        var data = result['data'];

                        if (data == null) {
                            return;
                        }

                        var str = '';
                        for (var i = 0; i < data.length; i++) {
                            str += '<a href="/Home/Saleman/Saleman/medicineInfo/id/' + data[i]['id'] + '" class="weui-grid">';
                            str += '<div class="medicine_img">';
                            str += '<img class = "weui-media-box__thumb" src="' + data[i]['cover_img'] + '" alt="">';
                            str += '</div>';
                            str += '<p class="weui-grid__label">' + data[i]['name'] + '</p>';
                            str += '<p class = "meidice_price_c">$<span class="medicine_price">' + data[i]['price'] + '</span></p>';
                            str += '<span style="font-size:12px;pading:3px 5px;color:#ccc;border:1px solid #ccc;border-radius:5px;background-color:#007700" pos_title="确定加入购物车" pos_id="' + data[i]['id'] + '" >加入购物车</span>';
                            str += '</a>';
                        }
                        $('.list').append(str);
                    },
                    error: function (xhr, type) {
                        alert('获取失败');
                    }
                });
            }
            $('.add_cart').on('click',function(){

                
            })
            $('.ok-btn').on('click', function () {
                var drugs_id = $(this).attr('pos_id');
                var num = 1;
                $.ajax({
                    url: "/Home/Saleman/Saleman/orderMedicine/op/cart",
                    type: 'post',
                    data: {drugs_id: drugs_id, num: num},
                    dataType: "json",
                    success: function (result) {
                        $("#iosDialog1").hide(200);
                        $('#iosDialog2').find('.sure-info').html(result['msg']);
                        $('#iosDialog2').find('.sure-btn').attr('pos_url', '');
                        if (result['data'] != null) {
                            var data = result['data'];
                            $('.good_num').html(data['totalNum']);
                            $('.allPrices').html(data['totalPrice']);
                        }
                        $('#iosDialog2').show(200);
                    }
                });
            });

        });
    </script>
</body>
</html>