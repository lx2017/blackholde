<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
        <meta content="yes" name="apple-mobile-web-app-capable">
        <meta content="black" name="apple-mobile-web-app-status-bar-style">
        <title>药品详情</title>
        <script src="http://cdn.bootcss.com/zepto/1.2.0/zepto.min.js"></script>
        <link href="http://cdn.bootcss.com/weui/1.0.2/style/weui.min.css" rel="stylesheet">
        <link rel="stylesheet" href="/Public/Home/Saleman/stylesheets/index.css">
    </head>
    <body>
        <div class="medicine_info">
            <header class = "all_header">
                <a class = "h_back" href="/Home/Saleman/Saleman/orderMedicine"><img src="/Public/Home/Saleman/images/back_icon.png" alt=""></a>
                <span class = "h_title">药品详情</span>
                <a class = "h_add" href="javascript:void(0)"></a>
            </header>
            <div class="medicine">
                <a href="javascript:void(0);" class="weui-media-box weui-media-box_appmsg">
                    <div class="weui-media-box__hd">
                        <img class="weui-media-box__thumb" src="<?php echo ($drugs['cover_img']); ?>" alt="">
                    </div>
                    <div class="weui-media-box__bd">
                        <h4 class="weui-media-box__title"><?php echo ($drugs['name']); ?></h4>
                        <div class="medicine_price">
                            <p class="weui-media-box__desc">¥<span><?php echo ($drugs['price']); ?></span></p>
                            <span class = "add_cart_btn add_drugs_cart" pos_title="确定加入购物车" pos_id="<?php echo ($drugs['id']); ?>">加入购物车</span>
                        </div>
                    </div>
                </a>
            </div>
            <div class="medicine_info_w">
                <div class="info_list">
                    <div class="info_name">
                        <span class = "info_icon">|</span>
                        <span>批准文号</span>
                    </div>
                    <div class="info_intro">
                        <?php echo ($drugs['approval_number']); ?>
                    </div>
                </div>
                <div class="info_list">
                    <div class="info_name">
                        <span class = "info_icon">|</span>
                        <span>主要成分</span>
                    </div>
                    <div class="info_intro">
                        <?php echo ($drugs['ingredients']); ?>
                    </div>
                </div>
                <div class="info_list">
                    <div class="info_name">
                        <span class = "info_icon">|</span>
                        <span>规格</span>
                    </div>
                    <div class="info_intro">
                        <?php echo ($drugs['specifications']); ?>
                    </div>
                </div>
                <div class="info_list">
                    <div class="info_name">
                        <span class = "info_icon">|</span>
                        <span>生产厂家</span>
                    </div>
                    <div class="info_intro">
                        <?php echo ($drugs['anufacturer']); ?>
                    </div>
                </div>
                <div class="info_list">
                    <div class="info_name">
                        <span class = "info_icon">|</span>
                        <span>经营企业</span>
                    </div>
                    <div class="info_intro">
                        <?php echo ($drugs['enterprise']); ?>
                    </div>
                </div>
                <div class="info_list">
                    <div class="info_name">
                        <span class = "info_icon">|</span>
                        <span>功能主治</span>
                    </div>
                    <div class="info_intro">
                        <?php echo ($drugs['attending']); ?>
                    </div>
                </div>
                <div class="info_list">
                    <div class="info_name">
                        <span class = "info_icon">|</span>
                        <span>用法用量</span>
                    </div>
                    <div class="info_intro">
                        <?php echo ($drugs['usage']); ?>
                    </div>
                </div>
                <div class="info_list">
                    <div class="info_name">
                        <span class = "info_icon">|</span>
                        <span>产品类型</span>
                    </div>
                    <div class="info_intro">
                        <?php echo ($drugs['drugs_category']); ?>
                    </div>
                </div>
                <div class="info_list">
                    <div class="info_name">
                        <span class = "info_icon">|</span>
                        <span>产品优势</span>
                    </div>
                    <div class="info_intro">
                        <?php echo ($drugs['advantage']); ?>
                    </div>
                </div>
            </div>
        </div>
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
        $('.add_drugs_cart').on('click', function () {
            var title = $(this).attr('pos_title');
            $('.confir_content').html(title);
            $("#iosDialog1").show(200);

        });
        $('.ok-btn').on('click', function () {
            var drugs_id = $('.add_drugs_cart').attr('pos_id');
            $.ajax({
                url: "/Home/Saleman/Saleman/medicineInfo",
                type: 'post',
                data: {drugs_id: drugs_id, num: 1},
                cache: false,
                dataType: "json",
                success: function (result) {
                    $("#iosDialog1").hide(200);
                    $('#iosDialog2').find('.sure-info').html(result['msg']);
                    $('#iosDialog2').show(200);
                    window.location.href="/Home/Saleman/Saleman/orderMedicine";
                }
            });
        });
    </script>
</body>
</html>