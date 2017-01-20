<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <title>工作汇总</title>
    <script src="/Public/Home/SaleManager/js/zepto.min.js"></script>
    <link href="/Public/Home/SaleManager/css/stylesheets/weui.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/Public/Home/SaleManager/css/stylesheets/index.css">
</head>
<body>
<div class="workSummary">
    <header class = "all_header">
        <a class = "h_back" href="javascript:history.back();"><img src="/Public/Home/SaleManager/images/back_icon.png" alt=""></a>
        <span class = "h_title">工作汇总</span>
        <a class = "h_add" href="javascript:void(0)">
            <form id="form">
            <select class="weui-select" name="year" onchange="getLists(this)">
            
            <?php if(is_array($year_data)): $i = 0; $__LIST__ = $year_data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$data): $mod = ($i % 2 );++$i;?><option value="<?php echo $data;?>" <?php if($year == $data): ?>selected<?php endif; ?>><?php echo ($data); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
            </form>
        </a>
    </header>
    <div class="weui-cells">
        <?php if($role === 4): ?><a class="weui-cell weui-cell_access" href="<?php echo U('/Home/WorkSummary/SaleSummary/salelist',array('sale_id'=>$sale_id,'year'=>$year));?>">
            <div class="weui-cell__bd">
                <p>业务员数量</p>
            </div>
            <div class="">
                <span class = "work_num"><?php echo ($salemanNum); ?></span>
                <span>个</span>
            </div>
        </a><?php endif; ?>
        <?php if($role === 3): ?><a class="weui-cell weui-cell_access" href="<?php echo U('/Home/WorkSummary/SaleSummary/countrylist',array('sale_id'=>$sale_id,'year'=>$year));?>">
                <div class="weui-cell__bd">
                    <p>县总数量</p>
                </div>
                <div class="">
                    <span class = "work_num"><?php echo ($countryCount); ?></span>
                    <span>个</span>
                </div>
            </a>
            <a class="weui-cell weui-cell_access" href="<?php echo U('/Home/WorkSummary/SaleSummary/salelist',array('sale_id'=>$sale_id,'year'=>$year));?>">
                <div class="weui-cell__bd">
                    <p>业务员数量</p>
                </div>
                <div class="">
                    <span class = "work_num"><?php echo ($salemanNum); ?></span>
                    <span>个</span>
                </div>
            </a><?php endif; ?>
        <?php if($role === 2): ?><a class="weui-cell weui-cell_access" href="<?php echo U('/Home/WorkSummary/SaleSummary/arealist',array('sale_id'=>$sale_id,'year'=>$year));?>">
                <div class="weui-cell__bd">
                    <p>地总数量</p>
                </div>
                <div class="">
                    <span class = "work_num"><?php echo ($areaCount); ?></span>
                    <span>个</span>
                </div>
            </a>
            <a class="weui-cell weui-cell_access" href="<?php echo U('/Home/WorkSummary/SaleSummary/countrylist',array('sale_id'=>$sale_id,'year'=>$year));?>">
                <div class="weui-cell__bd">
                    <p>县总数量</p>
                </div>
                <div class="">
                    <span class = "work_num"><?php echo ($countryCount); ?></span>
                    <span>个</span>
                </div>
            </a><?php endif; ?>
        <a class="weui-cell weui-cell_access" href="<?php echo U('/Home/WorkSummary/SaleSummary/clinicsum',array('sale_id'=>$sale_id,'year'=>$year));?>">
            <div class="weui-cell__bd">
                <p>目标诊所数量统计</p>
            </div>
            <div class="">
                <span class = "work_num"><?php echo ($clinicSumNum); ?></span>
                <span>个</span>
            </div>
        </a>
        <a class="weui-cell weui-cell_access" href="<?php echo U('/Home/WorkSummary/SaleSummary/allclinic',array('sale_id'=>$sale_id,'year'=>$year));?>">
            <div class="weui-cell__bd">
                <p>诊所数量</p>
            </div>
            <div class="">
                <span class = "work_num"><?php echo ($clinicNum); ?></span>
                <span>个</span>
            </div>
        </a>
        <a class="weui-cell weui-cell_access" href="<?php echo U('/Home/WorkSummary/SaleSummary/alldoctor',array('sale_id'=>$sale_id,'year'=>$year));?>">
            <div class="weui-cell__bd">
                <p>医生数量</p>
            </div>
            <div class="">
                <span class = "work_num"><?php echo ($doctorNum); ?></span>
                <span>个</span>
            </div>
        </a>
        <a class="weui-cell weui-cell_access" href="<?php echo U('/Home/WorkSummary/SaleSummary/visitalltimes',array('sale_id'=>$sale_id,'year'=>$year));?>">
            <div class="weui-cell__bd">
                <p>拜访次数</p>
            </div>
            <div class="">
                <span class = "work_num"><?php echo ($salmanVisitNum); ?></span>
                <span>次</span>
            </div>
        </a>
        <a class="weui-cell weui-cell_access" href="<?php echo U('/Home/WorkSummary/SaleSummary/applicatemeet',array('sale_id'=>$sale_id,'year'=>$year));?>">
            <div class="weui-cell__bd">
                <p>申请统计</p>
            </div>
            <div class="">
                <span class = "work_num"><?php echo ($salemanApplyNum); ?></span>
                <span>次</span>
            </div>
        </a>
        <a class="weui-cell weui-cell_access" href="<?php echo U('/Home/WorkSummary/SaleSummary/salesumary',array('sale_id'=>$sale_id,'year'=>$year));?>">
            <div class="weui-cell__bd">
                <p>销售统计</p>
            </div>
            <div class="">
                <span class = "work_num"><?php echo ($salemanOrderNum); ?></span>
                <span>元</span>
            </div>
        </a>
        <a class="weui-cell weui-cell_access" href="<?php echo U('/Home/WorkSummary/SaleSummary/orderlist',array('sale_id'=>$sale_id,'year'=>$year));?>">
            <div class="weui-cell__bd">
                <p>订货统计</p>
            </div>
            <div class="">
                <span class = "work_num"><?php echo ($salemanPrice); ?></span>
                <span>元</span>
            </div>
        </a>
        <a class="weui-cell weui-cell_access" href="<?php echo U('/Home/WorkSummary/SaleSummary/quitsale',array('sale_id'=>$sale_id,'year'=>$year));?>">
            <div class="weui-cell__bd">
                <p>退货统计</p>
            </div>
            <div class="">
                <span class = "work_num"><?php echo ($quitePrice); ?></span>
                <span>元</span>
            </div>
        </a>
        <a class="weui-cell weui-cell_access" href="<?php echo U('/Home/WorkSummary/SaleSummary/salemap',array('sale_id'=>$sale_id,'year'=>$year));?>">
            <div class="weui-cell__bd">
                <p>区域诊所分布地图</p>
            </div>
            <div class="weui-cell__ft">
            </div>
        </a>
    </div>
</div>
<script>
    function getLists(obj)
    {
        var val = $(obj).val();
        $("#form").submit();
    }
</script>
</body>
</html>