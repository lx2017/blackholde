<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
	<meta content="yes" name="apple-mobile-web-app-capable">
	<meta content="black" name="apple-mobile-web-app-status-bar-style">
	<title>全部诊所</title>
	<script src="http://cdn.bootcss.com/zepto/1.2.0/zepto.min.js"></script>
	<link href="http://cdn.bootcss.com/weui/1.0.2/style/weui.min.css" rel="stylesheet">
	<link rel="stylesheet" href="/Public/Home/SaleManager/css/stylesheets/index.css">
</head>
<body>
	<div class="allClinic">
    <header class = "all_header">
      <a class = "h_back" href="javascript:history.back();"><img src="/Public/Home/SaleManager/images/back_icon.png" alt=""></a>
      <span class = "h_title">全部诊所</span>
      <a class = "h_add" href="javascript:void(0)"></a>
    </header>
    <div class="weui-panel__bd">

      <?php if(!$clinics): ?><a href="javascript:void(0);" class="weui-media-box weui-media-box_appmsg">
              <div style="margin:auto">
                  无
              </div>
          </a>
      <?php else: ?>

      <?php if(is_array($clinics)): $i = 0; $__LIST__ = $clinics;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$clinic): $mod = ($i % 2 );++$i;?><a href="javascript:void(0);" class="weui-media-box weui-media-box_appmsg">
        <div class="weui-media-box__hd">
          <img class="weui-media-box__thumb" src="<?php echo ($clinic["clinic_pic"]); ?>" alt="">
        </div>
        <div class="weui-media-box__bd">
        	<div class="cli_w_name">
            <h4 class="weui-media-box__title"><?php echo ($clinic["clinic_name"]); ?></h4>
          </div>
          <div class="cli_w_num">
          	<span>预约量：<span>123</span></span>
          	<span>评分：<span><?php echo ($clinic["clinic_score"]); ?></span></span>
          </div>	
          <div class="weui-media-box__desc">由各种物质组成的巨型球状天体，叫做星球。星球有一定的形状，有自己的运行轨道。
          </div>
        </div>
      </a><?php endforeach; endif; else: echo "" ;endif; endif; ?>
    </div>
	</div>
	<script src = "../js/index.js"></script>
</body>
</html>