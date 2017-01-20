<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
	<meta content="yes" name="apple-mobile-web-app-capable">
	<meta content="black" name="apple-mobile-web-app-status-bar-style">
	<title>诊所主页</title>
	<script src="http://cdn.bootcss.com/zepto/1.2.0/zepto.min.js"></script>
	<link href="http://cdn.bootcss.com/weui/1.0.2/style/weui.min.css" rel="stylesheet">
	<link rel="stylesheet" href="/Public/Home/SaleManager/stylesheets/index.css">
</head>
<body>
	<div class="clinic_home">
    <header class = "all_header">
      <a class = "h_back" href="javascript:void(0)"><img src="/Public/Home/SaleManager/images/back_icon.png" alt=""></a>
      <span class = "h_title">诊所主页</span>
      <a class = "h_add" href="javascript:void(0)"></a>
    </header>
		<div class="clinic_img">
			<img src="/Public/Home/SaleManager/images/demo.jpg" alt="">
		</div>
		<div class="weui-cells">
      <div class="weui-cell">
          <div class="weui-cell__bd">
              <p>诊所负责人</p>
          </div>
          <div class="weui-cell__ft"><?php echo ($clinic["manager_name"]); ?></div>
      </div>
      <div class="weui-cell">
        <div class="weui-cell__bd">
          <p>诊所名称</p>
        </div>
        <div class="weui-cell__ft"><?php echo ($clinic["clinic_name"]); ?></div>
      </div>
       <div class="weui-cell">
        <div class="weui-cell__bd">
          <p>医生:</p>
        </div>
        <div class="weui-cell__ft"><a style = "color:#999" href="javascript:void(0);"><?php echo ($clinic["manager_name"]); ?></a></div>
      </div>
      <div class="weui-cell">
        <div class="weui-cell__bd">
          <p>手机号</p>
        </div>
        <div class="weui-cell__ft"><a style = "color:#999" href="javascript:void(0);"><?php echo ($clinic["manager_mobile"]); ?></a></div>
      </div>
      <div class="weui-cell">
        <div class="weui-cell__bd">
          <p>擅长</p>
        </div>
        <div class="weui-cell__ft intro_info"><?php echo ($clinic["clinic_specialty"]); ?></div>
      </div>
      <div class="weui-cell">
        <div class="weui-cell__bd" style = "flex:3">
          <p>诊所资质</p>
          <p style = "color: #999;font-size: 14px;"><?php echo ($clinic["clinic_licence"]); ?></p>
        </div>
        <div class="weui-cell__ft clinic_nature"><span></span></div>
      </div>
      <div class="weui-cell">
      <?php if($clinic["clinic_pic"] != ''): ?><div class="weui-cell__hd"><img src="<?php echo ($clinic["clinic_pic"]); ?>" alt="" style="width:20px;margin-right:5px;display:block"></div>
      <?php else: ?><div class="weui-cell__hd"><img src="/Public/Home/SaleManager/images/address.png" alt="" style="width:20px;margin-right:5px;display:block"></div><?php endif; ?>
        
        <div class="weui-cell__bd">
          <p>地址</p>
        </div>
        <div class="weui-cell__ft"><?php echo ($clinic["clinic_address"]); ?></div>
      </div>
      <div class="weui-cell">
        <div class="weui-cell__hd"><img src="/Public/Home/SaleManager/images/tel_btn.png" alt="" style="width:20px;margin-right:5px;display:block"></div>
        <div class="weui-cell__bd">
          <p>电话</p>
        </div>
        <div class="weui-cell__ft"><?php echo ($clinic["clinic_phone"]); ?></div>
      </div>
      <div class="abstract">
        <div>简介</div>
        <div class="summary"><?php echo ($clinic["clinic_introduction"]); ?></div>
      </div>
    </div>

    <div class="weui-cells">
    <?php if(is_array($doctorList)): $i = 0; $__LIST__ = $doctorList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$d): $mod = ($i % 2 );++$i;?><div class="weui-panel__bd">
	        <a href="javascript:void(0);" class="weui-media-box weui-media-box_appmsg">
	          <div class="weui-media-box__hd">
	           <?php if($d["image"] != ''): ?><img class="weui-media-box__thumb" src="<?php echo ($d["image"]); ?>" alt="">
	      		<?php else: ?><img class="weui-media-box__thumb" src="/Public/Home/SaleManager/images/clinic.png" alt=""><?php endif; ?>
	          </div>
	          <div class="weui-media-box__bd">
	          	<div class = "clinic_doc">
	          		<div class="doc_name">
		            	<h4 class="weui-media-box__title"><?php echo ($d["name"]); ?></h4>
		            	<span class = "doc_adress"><?php echo ($clinic["address"]); ?></span>
	            	</div>
	            	<div class="clinic_score">
	            		<span class = "clinic_s"><?php echo ($d["score"]); ?></span><span>分</span>
	            	</div>
	            </div>
	            <div class="doc_cont">
	            	<p class="weui-media-box__desc"><?php echo ($d["good"]); ?></p>
	            </div>
	          </div>
	        </a>
      </div><?php endforeach; endif; else: echo "" ;endif; ?>
    </div>
	</div>
	<script src = "/Public/Home/SaleManager/js/index.js"></script>
</body>
</html>