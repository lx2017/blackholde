<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
        <meta content="yes" name="apple-mobile-web-app-capable">
        <meta content="black" name="apple-mobile-web-app-status-bar-style">
        <title>工作安排</title>
        <link href="http://cdn.bootcss.com/weui/1.0.2/style/weui.min.css" rel="stylesheet">
        <link rel="stylesheet" href="/Public/Home/Saleman/stylesheets/index.css">
        <link rel="stylesheet" href="/Public/Home/Saleman/stylesheets/dropload.css">
    </head>
    <body>
        <div class="work_argument">
            <header class = "all_header">
                <a class = "h_back" href="/Home/Saleman/Saleman/index"><img src="/Public/Home/Saleman/images/back_icon.png" alt=""></a>
                <span class = "h_title">工作安排</span>
                <a class = "h_add btn_send" href="javascript:;" pos_title="确定发送给上级">发送</a>
            </header>
            <div class="weui-tab">
                <div class="weui-navbar">
                    <div class="weui-navbar__item weui-bar__item_on">
                        今日完成工作
                    </div>
                    <div class="weui-navbar__item">
                        明日计划
                    </div>
                    <div class="weui-navbar__item">
                        记录
                    </div>
                </div>
                <div class="weui-tab__panel">
                    <!--今日完成工作-->
                    <div class="weui-list cur_cont today_box" style = "display: block;">
                        <?php if(!empty($tripTodayList)): ?><div class="weui-cells weui-cells_checkbox work_lists">
                                <?php if(is_array($tripTodayList)): $i = 0; $__LIST__ = $tripTodayList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tripTodayVo): $mod = ($i % 2 );++$i;?><label class="weui-cell weui-check__label" for="s11">
                                        <div class="weui-cell__bd" style = "flex: 2">
                                            <p class = "cur_intro"><?php echo ($tripTodayVo['content']); ?></p>
                                        </div>
                                        <div class="weui-cell__bd cur_date">
                                            <p><?php echo ($tripTodayVo['work_day']); ?></p>
                                        </div>
                                    </label><?php endforeach; endif; else: echo "" ;endif; ?>
                            </div>
                            <?php else: ?>
                            <div class="no_work today_no_work" style="display: block;">
                                <div class="no_img">
                                    <img src="images/" alt="">
                                </div>
                                <div class="no_tips">
                                    暂无工作日程
                                </div>
                            </div><?php endif; ?>
                        <div class="cur_sel_w">
                            <div class="cur_add_w">
                                <div class="weui-cell">
                                    <div class="weui-cell__bd">
                                        <input class="weui-input cur_ipt" type="text" placeholder="请输入文本">
                                    </div>
                                </div>
                                <div class="cur_btn_w">
                                    <a href="javascript:;" class="weui-btn weui-btn_primary">添加</a>
                                </div>
                            </div>
                        </div>
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
                                <div class="no_img">
                                    <img src="images/" alt="">
                                </div>
                                <div class="no_tips">
                                    暂无工作日程
                                </div>
                            </div><?php endif; ?>
                        <div class="next_sel_w">
                            <div class="next_add_w">
                                <div class="weui-cell">
                                    <div class="weui-cell__bd">
                                        <input class="weui-input next_ipt" type="text" placeholder="请输入文本">
                                    </div>
                                </div>
                                <div class="next_btn_w">
                                    <a href="javascript:;" class="weui-btn weui-btn_primary">添加</a>
                                </div>
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

    <script>
        $('.btn_send').on('click', function () {
            var title = $(this).attr('pos_title');
            $('.confir_content').html(title);
            $("#iosDialog1").show(200);
        });

        $('.ok-btn').on('click', function () {
            //发送给上级
            $.ajax({
                url: "/Home/Saleman/Saleman/workArgument/op/send",
                type: 'post',
                dataType: "json",
                success: function (result) {
                    $("#iosDialog1").hide(200);
                    $('#iosDialog2').find('.sure-info').html(result['msg']);
                    $('#iosDialog2').show(200);
                }
            });
        });


        //发送记录

        var page = 0;
        var itemIndex = 0;
        // dropload
        var dropload = $('.sendlog').dropload({
            scrollArea: window,
            loadDownFn: function (me) {
                // 加载菜单一的数据
                if (itemIndex == '2') {
                    $.ajax({
                        type: 'post',
                        url: '/Home/Saleman/Saleman/workArgument/op/sendlog',
                        data: {page: page},
                        dataType: 'json',
                        success: function (result) {
                            page++;
                            var data = result['data'];

                            if (data == null) {
                                // 锁定
                                me.lock();
                                // 无数据
                                me.noData();
                                me.resetload();
                                return;
                            }

                            var str = '';
                            for (var i = 0; i < data.length; i++) {
                                str += '<label class="weui-cell weui-check__label" for="s1211">';
                                str += '<div class="weui-cell__bd" style = "flex: 2">';
                                str += '<p class = "pre_intro">' + data[i]['content'] + '</p>';
                                str += '</div>';
                                str += '<div class="weui-cell__bd cur_date">';
                                str += '<p>' + data[i]['work_day'] + '</p>';
                                str += '</div>';
                                str += '</label>';
                            }
                            $('.pre_w_lists').append(str);
                            me.resetload();
                        },
                        error: function (xhr, type) {
                            alert('刷新失败');
                            // 即使加载出错，也得重置
                            me.resetload();
                        }
                    });
                }
            }
        });
        $(".weui-navbar__item").on("click", function () {
            var $this = $(this);
            itemIndex = $this.index();
            // 如果选中菜单3
            if (itemIndex == '2') {
                // 重置
                $.ajax({
                    type: 'post',
                    url: '/Home/Saleman/Saleman/workArgument/op/sendlog',
                    data: {page: page},
                    dataType: 'json',
                    success: function (result) {
                        page++;
                        var data = result['data'];

                        if (data == null) {
                            return;
                        }

                        var str = '';
                        for (var i = 0; i < data.length; i++) {
                            str += '<label class="weui-cell weui-check__label" for="s1211">';
                            
                           
                            str += '<p><a href="/Home/Saleman/Saleman/workrecord/day/'+data[i]['work_day']+'">' + data[i]['work_day'] + '</a></p>';
                           
                            str += '</label>';
                        }
                        $('.pre_w_lists').append(str);
                    },
                    error: function (xhr, type) {
                        alert('刷新失败');
                    }
                });
            }

        });

    </script>
</body>
</html>