<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />
    <meta content="zh-CN" http-equiv="Content-Language" />
    <title>用户注册</title>
    <meta content="mbaobao.com" name="Copyright" />
    <meta content="关键字" name="Keywords" />
    <meta content="描述" name="Description" />
    <meta content="1GdE-bOwtaftzUH2-g1IcuNL7VYPf7t0qkI0vSpSPo0" name="google-site-verification" />
    <meta property="qc:admins" content="1041326176652172176375" />


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
        <!-- 登陆状态 -->

        <div class="logined-wrap">
            <i class="pic"><img src="/Public/Home/images/UserCenter/userlogo.jpg" width="44" height="44" /></i>
            <span class="welcome"><?php if($userinfo["nickname"] != ''): echo ($userinfo["nickname"]); else: echo ($userinfo["mobile"]); endif; ?></span>
            <span class="square"></span>
            <div class="use-info">
                <ul>
                    <li><a href="<?php echo U('/Home/UserCenter/UserCenter/Personal',array('x'=>'nosecrect','f'=>'nojson','t'=>'pc'));?>">个人中心</a></li>
                    <li><a href="<?php echo U('/Home/UserCenter/UserCenter/exitlogin');?>">退出登录</a></li>
                </ul>
            </div>
        </div>

        <!-- 登陆状态 -->
    </div>
</div>
<!-- 头部 -->
<!-- 注册模块 -->
<div class="wrap register-title">
    <p class="title">欢迎你的加入</p>
    <div class="login-go">已有账号?<a href="<?php echo U('/Home/LoginAndRegister/Login/Index',array('x'=>'secrect','f'=>'json','t'=>'pc'));?>">直接登录</a></div>
</div>
<div class="register-wrap wrap">
    <h6 class="register-left">
        <img src="/Public/Home/images/UserCenter/banner.jpg" width="440" height="500" />
    </h6>
    <div class="input-wrap">
        <form name="form_register" action="<?php echo U('/Home/LoginAndRegister/Register/InsertsRegisterInfo',array('x'=>'nosecrect','f'=>'nojson','t'=>'pc'));?>" method="post" id="form_register" class="form-wrap">
            <div class="form-input bottom-content">
                <label class="label-titlex" for="number">手机号:</label>
                <input name="number" class="input-text number" id="number" _href= "<?php echo U('/Home/LoginAndRegister/Register/CheckMobile',array('x'=>'nosecret','f'=>'nojson','t'=>'pc'));?>" type="text" value="" placeholder="请输入您的手机号">
                <a href="javascript:" class="get-code" id="get-code" _href="<?php echo U('/Home/LoginAndRegister/Register/SendYzm',array('x'=>'nosecret','f'=>'json','t'=>'pc'));?>">获取短信验证码</a>
                <span class="post_error"></span>
            </div>
            <div class="form-input">
                <label class="label-titlex" for="code-num">短信验证码:</label>
                <input name="code-num" class="input-text code-num" id="code-num" _href="<?php echo U('/Home/LoginAndRegister/Register/CheckYzm',array('x'=>'nosecret','f'=>'json','t'=>'pc'));?>" type="text" value="" placeholder="请输入您的手机验证码" disabled="disabled">
                <span class="post_error"></span>
            </div>
            <div class="form-input">
                <label class="label-titlex" for="userpassword">创建密码:</label>
                <input id="userpassword" type="password" name="userpassword" maxlength="100" minlength="3" value="" placeholder="请设置您的登录密码">
                <span class="post_error"></span>
            </div>
            <div class="form-input bottom-content">
                <label class="label-titlex" for="userpassword2">确认密码:</label>
                <input id="userpassword2"  type="password" name="userpassword2" maxlength="100" minlength="3" value="" placeholder="请再次输入密码">
                <span class="agree-txt">注册即同意<strong>&lt;猿码头用户协议&gt;</strong></span>
                <span class="post_error"></span>
            </div>
            <div class="form-input">
                <input type="submit" class="submit-btn register-btn" value="注册" id="register-btn">
            </div>
        </form>
    </div>
</div>
<!-- 注册模块 -->
<!-- 底部公用模块 -->
<include file="UserCenter/footer">
<!-- 底部公用模块 -->
</body>
<script src="/Public/Home/js/UserCenter/jquery.js"></script>
<script src="/Public/Home/js/UserCenter/register.js"></script>
</html>