<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
        <meta content="yes" name="apple-mobile-web-app-capable">
        <meta content="black" name="apple-mobile-web-app-status-bar-style">
        <title>工作详情</title>
        <link href="http://cdn.bootcss.com/weui/1.0.2/style/weui.min.css" rel="stylesheet">
        <link rel="stylesheet" href="/Public/Home/Saleman/stylesheets/index.css">
        <link rel="stylesheet" href="/Public/Home/Saleman/stylesheets/dropload.css">
    </head>
    <body>
        <div class="work_argument">
            
            <div class="weui-tab">
                <div class="weui-navbar">
                   
                    <div class="weui-navbar__item">
                        工作详情
                    </div>
                </div>
                <div class="weui-tab__panel">
                    <!--今日完成工作-->
                    <div class="weui-list cur_cont today_box" style = "display: block;">
                        <?php if(!empty($tripTodayList)): ?><div class="weui-cells weui-cells_checkbox work_lists">
                                <?php if(is_array($tripTodayList)): $i = 0; $__LIST__ = $tripTodayList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tripTodayVo): $mod = ($i % 2 );++$i;?><label class="weui-cell weui-check__label" for="s11">
                                        <div class="weui-cell__bd" style = "flex: 2">
                                            <p class = "cur_intro"></p>
                                        </div>
                                        <div class="weui-cell__bd cur_date">
                                            <p></p>
                                        </div>
                                    </label><?php endforeach; endif; else: echo "" ;endif; ?>
                            </div>
                            <?php else: ?>
                            <div class="no_work today_no_work" style="display: block;">
                              <?php if(($result == NULL)): ?><div class="no_tips">
                                    暂无工作日程
                                </div>
                              <?php else: ?>
                                <div class="no_tips">
                                <?php if(is_array($result)): $i = 0; $__LIST__ = $result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$data): $mod = ($i % 2 );++$i;?><label class="weui-cell weui-check__label" for="s1211">
                                <div class="weui-cell__bd" style = "flex: 2">
                                <p class = "pre_intro" style="float:left"><?php echo ($data["content"]); ?></p>
                                <p class = "pre_intro" style="float:right"><?php echo (date('Y-m-d H:i:s',$data["add_time"])); ?></p>
                                </div>
                                
                                </label><?php endforeach; endif; else: echo "" ;endif; ?>
                                </div><?php endif; ?>
                            </div><?php endif; ?>
                        
                    </div>
                    <!--明日安排-->
                    <div class="weui-list next_cont tomorrow_box">
                        <?php if(!empty($tripTomorrowList)): ?><div class="weui-cells weui-cells_checkbox next_w_lists">
                                <?php if(is_array($tripTomorrowList)): $i = 0; $__LIST__ = $tripTomorrowList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tripTomorrowVo): $mod = ($i % 2 );++$i;?><label class="weui-cell weui-check__label" for="s1111">
                                        <div class="weui-cell__bd" style = "flex: 2">
                                            <p class = "next_intro"><?php echo ($tripTomorrowVo['content']); ?></p>
                                        </div>
                                        <div class="weui-cell__bd cur_date">
                                            <p><?php echo ($tripTomorrowVo['work_day']); ?></p>
                                        </div>
                                    </label><?php endforeach; endif; else: echo "" ;endif; ?>
                            </div>
                            <?php else: ?>
                            <div class="no_work tomorrow_no_work" style="display: block;">
                                
                                <div class="no_tips">
                                    暂无工作日程
                                </div>
                            </div><?php endif; ?>
                       
                                
                            </div>
                        </div>
                    </div>
                    <!--已发送的今日完成工作记录-->
                    <div class="weui-list pre_cont sendlog">
                        <div class="weui-cells weui-cells_checkbox pre_w_lists">

                        </div>
                        <div class="pre_sel_w">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="/Public/Home/Saleman/js/zepto.min.js"></script>


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

    <script src = "/Public/Home/Saleman/js/index.js"></script>
    <script src = "/Public/Home/Saleman/js/dropload.min.js"></script>

   
</body>
</html>