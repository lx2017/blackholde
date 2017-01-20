<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
	<meta content="yes" name="apple-mobile-web-app-capable">
	<meta content="black" name="apple-mobile-web-app-status-bar-style">
	<title>我的诊所</title>
	<script src="http://cdn.bootcss.com/zepto/1.2.0/zepto.min.js"></script>
	<link href="http://cdn.bootcss.com/weui/1.0.2/style/weui.min.css" rel="stylesheet">
	<link rel="stylesheet" href="/Public/Home/SaleManager/stylesheets/index.css">
</head>
<body>
	<div class="noClinic">
		<header class = "all_header">
			<a class = "h_back" href="javascript:void(0)"><img src="/Public/Home/SaleManager/images/back_icon.png" alt=""></a>
			<span class = "h_title">我的诊所</span>
			<a class="h_add" href="./addClinic.html"></a>
		</header>
		<div class="no_img">
			<img src="/Public/Home/SaleManager/images/no_clinic.png" alt="">
		</div>
		<div class="no_tips">
			暂无诊所
		</div>
	</div>
</body>
</html>