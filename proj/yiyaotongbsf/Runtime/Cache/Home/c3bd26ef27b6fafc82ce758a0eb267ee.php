<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
        <meta content="yes" name="apple-mobile-web-app-capable">
        <meta content="black" name="apple-mobile-web-app-status-bar-style">
        <title>我的订单</title>
        <script src="http://cdn.bootcss.com/zepto/1.2.0/zepto.min.js"></script>
        <link href="http://cdn.bootcss.com/weui/1.0.2/style/weui.min.css" rel="stylesheet">
        <link rel="stylesheet" href="/Public/Home/Saleman/stylesheets/index.css">
    </head>
    <body>
        <div class="clinic">
            <header class = "all_header">
                <a class = "h_back" href="/Home/Saleman/Saleman/index"><img src="/Public/Home/Saleman/images/back_icon.png" alt=""></a>
                <span class = "h_title">我的订单</span>
                <a class = "h_add" href="javascript:void(0)"></a>
            </header>
             <div class="weui-tab">
                <div class="weui-navbar">
                    <div class="weui-navbar__item weui-bar__item_on" id="one">
                        全部
                    </div>
                    <div class="weui-navbar__item" id="two">
                      待处理
                    </div>
                    <div class="weui-navbar__item" id="three">
                        已完成
                    </div>
                    <div class="weui-navbar__item" id="ab">
                        退货
                    </div>
                </div><br/><br/><br/>
            <div class="weui-panel__bd">
              
                <?php if(is_array($result)): $i = 0; $__LIST__ = $result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$data): $mod = ($i % 2 );++$i;?><a href="/Home/Saleman/Saleman/clinicHome/id/" class="weui-media-box weui-media-box_appmsg">
                            <div class="weui-media-box__hd clinic_img">
                                <img class="weui-media-box__thumb" src="<?php echo ($data['heading']); ?>" alt="">
                            </div>
                            <div class="weui-media-box__bd">
                                <div class="cli_w_name">
                                    <h4 class="weui-media-box__title"><?php echo ($clinicVo['clinic_name']); ?></h4>
                                   
                                </div>
                                <div class="cli_w_num">
                                    <span>业务员：<span><?php echo ($data['real_name']); ?></span>  <span><?php echo (date('Y-m-d',$data['add_time'])); ?></span>&nbsp;
                                    <span>订单号：<span><?php echo ($data['id']); ?></span><br/>
                                    <span>总价:</span><?php echo ($data['total_price']); ?></span><br/>
                                    <span>状态:</span><?php if($data['status'] == 0): ?>待处理<?php elseif($data['status'] == 1): ?>已完成<?php else: ?>退货<?php endif; ?></span>
                                </div>  
                              
                            </div>
                        </a><?php endforeach; endif; else: echo "" ;endif; ?>
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
                    <a href="javascript:;" class="weui-dialog__btn weui-dialog__btn_primary sure-btn">知道了</a>
                </div>
            </div>
        </div>

      
     
    </body>
</html>
<script>
$(function(){

    $("#one").click(function(){

        window.location.href="/Home/Saleman/Saleman/Myorder";
    })
    $("#two").click(function(){
        window.location.href="/Home/Saleman/Saleman/Myorder/type/0";
    })
    $("#three").click(function(){
        window.location.href="/Home/Saleman/Saleman/Myorder/type/1";

    })
    $("#ab").click(function(){

        window.location.href="/Home/Saleman/Saleman/Myorder/type/2";
    })
})
</script>