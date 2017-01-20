<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />
    <meta content="zh-CN" http-equiv="Content-Language" />
    <title>标题</title>
    <meta content="mbaobao.com" name="Copyright" />
    <meta content="关键字" name="Keywords" />
    <meta content="描述" name="Description" />
    <meta content="1GdE-bOwtaftzUH2-g1IcuNL7VYPf7t0qkI0vSpSPo0" name="google-site-verification" />
   <!-- <meta property="qc:admins" content="1041326176652172176375" />-->
    <meta property="qc:admins" content="1461364427635331446727" />
    <meta property="wb:webmaster" content="5228654728a81639" />


    <link rel="stylesheet" type="text/css" href="/Public/Home/css/UserCenter/member.css" media="all" />

</head>
<body class="register-box">
<!-- 头部 -->
<div class="g-head">
    <div class="wrap">
        <h1 class="g-name">
            <p class="big-name">猿码头</p>
            <p class="litter-name">助力于中小企业的发展</p>
        </h1>
        <h2 class="logo">
            <img src="/Public/Home/images/UserCenter/logo.png" width="61" height="61" />
        </h2>
    </div>
</div>
<!-- 头部 -->
<!-- 注册模块 -->
<div class="wrap register-title">
    <p class="title">欢迎你的加入</p>

</div>
<div class="login-wrap wrap">
    <h6 class="register-left">
        <img src="/Public/Home/images/UserCenter/login-left.jpg" width="664" height="548" />
    </h6>
    <div class="input-wrap">
        <div class="login-box-info">
            <h6 class="login-status">
                猿码头会员
                <a class="go-res" href="<?php echo U('/Home/LoginAndRegister/Register/Index',array('x'=>'nosecret','f'=>'json','t'=>'pc'));?>">新用户注册</a>
            </h6>
            <form name="form_login" action="<?php echo U('/Home/LoginAndRegister/Login/Login_Act',array('x'=>'nosecret','f'=>'page','t'=>'pc'));?>" method="post" id="form_login" class="form-wrap">
                <div class="error-wrap" id="error-wrap"></div>
                <div class="form-input l-g-1">
                    <input type="text" id='number' value="" name="user_name" class="input-text number" placeholder="请输入您的手机号或者邮箱" _href ="<?php echo U('/Home/LoginAndRegister/Login/CheckUser');?>">
                </div>
                <div class="form-input l-g-2">
                    <input id="userpassword" type="password" name="userpassword" maxlength="100" minlength="3" value="" placeholder="请输入您的登录密码">
                </div>
                <!-- 多次点击状态 -->
                <!--   <div class="form-input l-g-3">
                     <input class="itxt input-text" name="captcha" tabindex="1" id="captcha" maxlength="4" type="text" value="" placeholder="请输入验证码">
                     <img id="auth-image" src="http://www.daishusale.com/user/vcode" height="50" width="90" class="auth-image captcha_img">
                     <span class="resh-agin">点击刷新</span>
                 </div> -->
                <!-- 多次点击状态 -->
                <div class="form-input">
                    <input id="un-login" type="checkbox"  value ="remember" name="un-login" class="un-login">
                    <div class="txt-wraps">
                        <label for="un-login">一月内免登陆</label>
                        <span class="line">|</span>
                        <a href="<?php echo U('/Home/LoginAndRegister/Login/FindPasswordpc');?>">忘记密码?</a>
                    </div>
                </div>
                <div class="form-input bottom-margin">
                    <input type="submit" class="ui_submit login-btn" id="btnsubmit" value="登录">
                </div>
            </form>
            <div class="other-login">
                <p>无需注册,选择以下方式登录:</p>
                <div class="login-select">
                    <a href="javascript:void(0);" onclick ="toQzoneLogin()" class="qq">qq</a>
                  <!--  <a href="<?php echo ($weixin_url); ?>" class="wx">wx</a>-->
                   <!-- <a href="javascript:void(0);" class="wx">wx</a>-->
                    <a href="<?php echo ($wburl); ?>" class="wb">wb</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- 注册模块 -->
<!-- 底部公用模块 -->
<div class="g-fotter">
    <div class="wrap">
        <h2 class="logo">
            <img src="/Public/Home/images/UserCenter/logo.png" width="68" height="68" />
        </h2>
        <div class="my-nav">
            <ul class="nav-list">
                <li><a href="#" title="首页">首页</a></li>
                <li><a href="#" title="产品中心">产品中心</a></li>
                <li><a href="#" title="发布需求">发布需求</a></li>
                <li><a href="#" title="开发者入口">开发者入口</a></li>
                <li><a href="#" title="成功案例">成功案例</a></li>
                <li><a href="#" title="关于我们">关于我们</a></li>
            </ul>
            <h6 class="f-honor">Copyright © 京ICP备14013455号-</h6>
        </div>
        <div class="tell-wrap">
            <i class="arrow"></i>
            400 008 9495
        </div>

    </div>
</div>
<!-- 底部公用模块 -->
</body>
<script src="/Public/Home/js/jquery-2.0.0/jquery.min.js"></script>
<script src="/Public/Home/js/UserCenter/login.js"></script>
<script scr ="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script><!--引入微信js接口-->
<script src="http://res.wx.qq.com/connect/zh_CN/htmledition/js/wxLogin.js"></script><!--微信js登录-->
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

</html>