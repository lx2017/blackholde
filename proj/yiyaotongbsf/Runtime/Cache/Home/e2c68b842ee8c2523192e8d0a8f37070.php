<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>用户注册</title>
</head>
<body>
<link rel="stylesheet" type="text/css" href="/pro/yiyaotongbsf/Public/Home/css/LoginAndRegister/zc.css">
<script src="/pro/yiyaotongbsf/Public/Home/js/jquery-2.0.0/jquery.min.js"></script>
<script src="/pro/yiyaotongbsf/Public/Home/js/UserCenter/zepto.min.js"></script>
<script src="/pro/yiyaotongbsf/Public/Home/js/UserCenter/zc.js"></script>
<article class="M_main">
    <header>
        <div class="tou">
            <a href="javascript:history.go(-1);" class="goback"><i class="iconfont1">&#xe606;</i></a>
            <p>注册</p>
        </div>
    </header>
    <div class="content">
        <form action="<?php echo U('/Home/LoginAndRegister/Register/YanZheng');?>" method="post" onsubmit="return zc()">
            <!-- 手机号输入 -->
            <section>
                <div class="phone-n">
                    <i class="iconfont2">&#xe600;</i>
                    <input id ="number" _href="<?php echo U('/Home/LoginAndRegister/Register/CheckYzm',array('x'=>'nosecret','f'=>'json','t'=>'wap'));?>" type="text" name="mobile" maxlength="11" placeholder="请输入手机号"/>
                </div>
            </section>
            <!-- 手机号输入 -->
            <!-- 验证码输入 -->
            <section>
                <div class="id-code">
                    <i class="iconfont2">&#xe604;</i>

                    <input type="button" class="btn_mfyzm" id="timeyzm" onclick="sendyzm()" value="获取验证码"><!--onclick="sendyzm()"-->
                    <input  id = "yzm" _href="<?php echo U('/Home/LoginAndRegister/Register/SendYzmst');?>" type="text" name="yzm" maxlength="11" placeholder="请输入验证码"  class="val-sr"/>
                </div>
            </section>

            <!-- 验证码输入 -->
            <!-- 注册按钮 -->
            <section>

                <input type="submit" value="注册" class="en">
            </section>
            <!-- 注册按钮 -->
        </form>
        <section>
            <p class="pro">我已阅读并同意<a href="#">猿码头用户协议</a></p>
        </section>
        <!-- 分享 -->
       <!-- <section>
            <div class="share">
                <div style="width:33%;padding-top:5px;"><a href="#" style="float:right;"><i class="iconfont3">&#xe602;</i></a></div>
                <div style="width:34%;text-align: center;"><a href="#"><i class="iconfont3">&#xe601;</i></a></div>
                <div style="width:33%;padding-top:5px;"><a href="#" style="float:left;"><i class="iconfont3">&#xe605;</i></a></div>
            </div>
        </section>-->
        <!-- 分享 -->
    </div>
</article>
</body>
</html>