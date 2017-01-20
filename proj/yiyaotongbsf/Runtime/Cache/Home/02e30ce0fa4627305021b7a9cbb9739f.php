<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
        <meta content="yes" name="apple-mobile-web-app-capable">
        <meta content="black" name="apple-mobile-web-app-status-bar-style">
        <title>index</title>
        <script src="http://cdn.bootcss.com/zepto/1.2.0/zepto.min.js"></script>
        <link href="http://cdn.bootcss.com/weui/1.0.2/style/weui.min.css" rel="stylesheet">
        <link rel="stylesheet" href="/Public/Home/Saleman/stylesheets/index.css">
    </head>
    <body>
        <div class="newMedicine">
            <div class="info_nav nav_show">
                <div class="info_head">
                    <div class="head_img">
                        <?php if(!empty($salemanInfo['head_img'])): ?><img src="<?php echo ($salemanInfo['head_img']); ?>" style="width:70px;height:70px;border-radius:50%">
                            <?php else: ?>
                            <img src="/Public/Home/Saleman/images/people.png" alt=""><?php endif; ?>

                    </div>
                    <div class = "info_name"><?php echo ($salemanInfo['real_name']); ?></div>
                </div>
                <div class="infos">
                    <div class="weui-grids">
                        <a href="/Home/Saleman/Saleman/workArgument" class="weui-grid">
                            <div class="weui-grid__icon">
                                <img src="/Public/Home/Saleman/images/work_icon.png" alt="">
                            </div>
                            <p class="weui-grid__label">工作安排</p>
                        </a>
                        <a href="/Home/SaleManager/County/targetClinic" class="weui-grid">
                            <div class="weui-grid__icon">
                                <img src="/Public/Home/Saleman/images/goad_icon.png" alt="">
                            </div>
                            <p class="weui-grid__label">目标诊所</p>
                        </a>
                        <a href="/Home/SaleManager/County/clinic" class="weui-grid">
                            <div class="weui-grid__icon">
                                <img src="/Public/Home/Saleman/images/my_clinic.png" alt="">
                            </div>
                            <p class="weui-grid__label">我的诊所</p>
                        </a>
                    </div>
                </div>
                <div class="info_list">
                    <div class="weui-cells">
                        <a class="weui-cell weui-cell_access" href="/Home/Saleman/Saleman/sign">
                            <div class="weui-cell__hd"><img src="/Public/Home/Saleman/images/visit_icon.png" alt="" style="width:35px;margin-right:10px;display:block"></div>
                            <div class="weui-cell__bd">
                                <p>签到拜访</p>
                            </div>
                            <div class="weui-cell__ft"></div>
                        </a>
                        <a class="weui-cell weui-cell_access" href="/Home/Saleman/Saleman/appliceSupport">
                            <div class="weui-cell__hd"><img src="/Public/Home/Saleman/images/support_icon.png" alt="" style="width:35px;margin-right:10px;display:block"></div>
                            <div class="weui-cell__bd">
                                <p>申请支持</p>
                            </div>
                            <div class="weui-cell__ft"></div>
                        </a>
                        <a class="weui-cell weui-cell_access" href="/Home/Saleman/Saleman/clinicOrder">
                            <div class="weui-cell__hd"><img src="/Public/Home/Saleman/images/clinic_order.png" alt="" style="width:35px;margin-right:10px;display:block"></div>
                            <div class="weui-cell__bd">
                                <p>诊所订单</p>
                            </div>
                            <div class="weui-cell__ft"></div>
                        </a>
                        <a class="weui-cell weui-cell_access" href="/Home/Saleman/Saleman/orderMedicine">
                            <div class="weui-cell__hd"><img src="/Public/Home/Saleman/images/order_medicine.png" alt="" style="width:35px;margin-right:10px;display:block"></div>
                            <div class="weui-cell__bd">
                                <p>订购药品</p>
                            </div>
                            <div class="weui-cell__ft"></div>
                        </a>
                        <a class="weui-cell weui-cell_access" href="./pushHistory.html">
                            <div class="weui-cell__hd"><img src="/Public/Home/Saleman/images/infopush_icon.png" alt="" style="width:35px;margin-right:10px;display:block"></div>
                            <div class="weui-cell__bd">
                                <p>消息推送</p>
                            </div>
                            <div class="weui-cell__ft"></div>
                        </a>
                        <a class="weui-cell weui-cell_access" href="/Home/WorkSummary/SaleSummary/index/saleId/<?php echo ($salemanId); ?>">
                            <div class="weui-cell__hd"><img src="/Public/Home/Saleman/images/work_all.png" alt="" style="width:35px;margin-right:10px;display:block"></div>
                            <div class="weui-cell__bd">
                                <p>工作汇总</p>
                            </div>
                            <div class="weui-cell__ft"></div>
                        </a>
                    </div>
                </div>
            </div>
            <!-- 消息 -->
            <div class="info_nav msgs">
                <header class = "all_header">
                    <a class = "h_back" href="javascript:void(0)"><img src="/Public/Home/Saleman/images/back_icon.png" alt=""></a>
                    <span class = "h_title">消息</span>
                    <a class = "h_add" href="javascript:void(0)"></a>
                </header>
                <div class="weui-panel__bd">
                    <a href="javascript:void(0);" class="weui-media-box weui-media-box_appmsg">
                        <div class="weui-media-box__hd">
                            <img class="weui-media-box__thumb" src="/Public/Home/Saleman/images/upInfo.png" alt="">
                        </div>
                        <div class="weui-media-box__bd">
                            <div class = "msg_tips">
                                <h4 class="weui-media-box__title">上级通知</h4>
                                <span>14:23</span>
                            </div>
                            <div class="msg_cont">
                                <p class="weui-media-box__desc">张三县总</p>
                                <span class = "info_num">1</span>
                            </div>
                        </div>
                    </a>
                </div>
                <!--                <div class="weui-panel__bd">
                                    <a href="javascript:void(0);" class="weui-media-box weui-media-box_appmsg">
                                        <div class="weui-media-box__hd">
                                            <img class="weui-media-box__thumb" src="/Public/Home/Saleman/images/downInfo.png" alt="">
                                        </div>
                                        <div class="weui-media-box__bd">
                                            <div class = "msg_tips">
                                                <h4 class="weui-media-box__title">下级通知</h4>
                                                <span>15:33</span>
                                            </div>
                                            <div class="msg_cont">
                                                <p class="weui-media-box__desc">张三医生给您发来了消息</p>
                                                <span class = "info_num">1</span>
                                            </div>
                                        </div>
                                    </a>
                                </div>-->
                <!--                <div class="weui-panel__bd">
                                    <a href="javascript:void(0);" class="weui-media-box weui-media-box_appmsg">
                                        <div class="weui-media-box__hd">
                                            <img class="weui-media-box__thumb" src="/Public/Home/Saleman/images/xitongInfo.png" alt="">
                                        </div>
                                        <div class="weui-media-box__bd">
                                            <div class = "msg_tips">
                                                <h4 class="weui-media-box__title">系统消息</h4>
                                                <span>昨天</span>
                                            </div>
                                            <div class="msg_cont">
                                                <p class="weui-media-box__desc">系统升级到了1.12</p>
                                                <span class = "info_num">1</span>
                                            </div>
                                        </div>
                                    </a>
                                </div>-->
            </div>
            <!-- 个人 -->
            <div class="info_nav set_cont">
                <header class = "all_header">
                    <a class = "h_back" href="javascript:void(0)"><img src="/Public/Home/Saleman/images/back_icon.png" alt=""></a>
                    <span class = "h_title">个人</span>
                    <a class = "h_add" href="javascript:void(0)"></a>
                </header>
                <div class="weui-cells">
                    <a class="weui-cell weui-cell_access" href="/Home/Saleman/Saleman/personData">
                        <div class="weui-cell__bd">
                            <p>个人资料</p>
                        </div>
                        <div class="weui-cell__ft">
                        </div>
                    </a>
                    <a class="weui-cell weui-cell_access" href="javascript:void(0);">
                        <div class="weui-cell__bd">
                            <p>修改密码</p>
                        </div>
                        <div class="weui-cell__ft">
                        </div>
                    </a>
                    <a class="weui-cell weui-cell_access" href="javascript:void(0);">
                        <div class="weui-cell__bd">
                            <p>新消息通知</p>
                        </div>
                        <div class="weui-cell__ft">
                        </div>
                    </a>
                    <a class="weui-cell weui-cell_access" href="javascript:void(0);">
                        <div class="weui-cell__bd">
                            <p>意见反馈</p>
                        </div>
                        <div class="weui-cell__ft">
                        </div>
                    </a>
                </div>
                <div class="weui-btn-area">
                    <a class="weui-btn quit_btn" href="/Home/SaleManager/Login/loginOut">退出登录</a>
                </div>
            </div>
            <div class="weui-tabbar">
                <a href="javascript:;" class="weui-tabbar__item weui-bar__item_on">
                    <span class = "work_active_icon common_icon"></span>
                    <!-- <img src="./images/icon_tabbar.png" alt="" class="weui-tabbar__icon"> -->
                    <p class="weui-tabbar__label">工作</p>
                </a>
                <a href="javascript:;" class="weui-tabbar__item">
                    <span class = "info_icon common_icon"></span>
                    <!-- <img src="./images/icon_tabbar.png" alt="" class="weui-tabbar__icon"> -->
                    <p class="weui-tabbar__label" style = "position: relative;">消息<?php if(!empty($msgTotalNum)): ?><span class = "new_info"><?php echo ($msgTotalNum); ?></span><?php endif; ?></p>
                </a>
                <a href="javascript:;" class="weui-tabbar__item">
                    <span class = "person_icon common_icon"></span>
                    <!-- <img src="./images/icon_tabbar.png" alt="" class="weui-tabbar__icon"> -->
                    <p class="weui-tabbar__label">个人</p>
                </a>
            </div>
        </div>
        <script src = "/Public/Home/Saleman/js/index.js"></script>
        <script>
//            $(document).on('click', '.quit_btn', function () {
//                var sessioncode;
//                var nosesstion_url;
//                var returnStatus;
//                $.ajax({
//                    url: "/Home/SaleManager/Login/loginOut",
//                    type: 'POST',
//                    data: '',
//                    async: false,
//                    cache: false,
//                    dataType: "json",
//                    success: function (returndata) {
//                        if (returndata.code == 3001) {
//                            window.location.href = returndata.data;
//                        }
//                    },
//                    error: function (returndata) {
//                        window.location.href = returndata.data;
//                    }
//                });
//
//            });
        </script>
    </body>
</html>