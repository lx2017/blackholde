<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>用户登陆</title>
</head>

<body>
<link rel="stylesheet" type="text/css" href="/Public/Home/css/LoginAndRegister/zc.css">
<script src="/Public/Home/js/jquery-2.0.0/jquery.min.js"></script>
<script scr ="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script><!--引入微信js接口-->
<script src="/Public/Home/js/UserCenter/zepto.min.js"></script>
<script src="/Public/Home/js/UserCenter/zc.js"></script>
<article class="M_main">
    <header>
        <div class="tou">
            <a href="javascript:history.go(-1);" class="goback"><i class="iconfont1">&#xe606;</i></a>
            <p>登陆</p>
        </div>
    </header>
    <div class="content">
        <form action="<?php echo U('/Home/LoginAndRegister/Login/Login_Act',array('x'=>'nosecret','f'=>'nojson','t'=>'wap'));?>" method="post" onsubmit="return dl()">
            <!-- 手机号输入 -->
            <section>
                <div class="phone-sr">
                    <i class="iconfont2">&#xe600;</i>
                    <input type="text" name="user_name" id ="phone" _href="<?php echo U('/Home/LoginAndRegister/Login/CheckUser',array('x'=>'nosecret','f'=>'nojson'));?>" maxlength="11" placeholder="请输入手机号" onkeydown="onlyNum();"/>
                </div>
            </section>
            <!-- 手机号输入 -->
            <!-- 密码输入 -->
            <section>
                <div class="mima-code">
                    <i class="iconfont2">&#xe603;</i>


                    <input type="password" name="password" maxlength="20" placeholder="请输入6-20的字母和数字组合密码"/>
                </div>
            </section>
            <!-- 密码输入 -->
            <!-- 登陆按钮 -->
            <section>
                <input type="submit" value="登陆" class="dl-en">
            </section>
            <!-- 登陆按钮 -->
        </form>
        <section>
            <div style="overflow: hidden;margin-top:20px;">
                <p class="wj-mima"><a href="/Home/LoginAndRegister/Login/FindPassword">忘记密码？</a></p>
                <p class="zc-zhanghao"><a href="<?php echo U('/Home/LoginAndRegister/Register/Index',array('x'=>'nosecret','f'=>'json','t'=>'wap'));?>">注册账号</a></p>
            </div>
        </section>
        <!-- 分享 -->
        <section>
            <div class="share">
                <div style="width:33%;padding-top:5px;"><a href="javascript:void(0);" onclick ="toQzoneLogin()" style="float:right;"><i class="iconfont3">&#xe602;</i></a></div>
                <div style="width:34%;text-align: center;"><a href="<?php echo ($weixin_url); ?>"><i class="iconfont3">&#xe601;</i></a></div>
                <div style="width:33%;padding-top:5px;"><a href="<?php echo ($wburl); ?>" style="float:left;"><i class="iconfont3">&#xe605;</i></a></div>
            </div>
        </section>
        <!-- 分享 -->

    </div>
</article>
<script type="text/javascript">
    var childWindow;
    function toQzoneLogin()
    {
        childWindow = window.open("<?php echo U('/Home/LoginAndRegister/Login/Qq_Login');?>","TencentLogin","width=450,height=320,menubar=0,scrollbars=1, resizable=1,status=1,titlebar=0,toolbar=0,location=1");
    }
    function closeChildWindow()
    {
        childWindow.close();
    }
</script>

</body>
</html>