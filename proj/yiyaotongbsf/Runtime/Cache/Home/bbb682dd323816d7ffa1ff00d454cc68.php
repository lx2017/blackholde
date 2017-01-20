<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
        <meta content="yes" name="apple-mobile-web-app-capable">
        <meta content="black" name="apple-mobile-web-app-status-bar-style">
        <title>诊所订单</title>

        <link href="http://cdn.bootcss.com/weui/1.0.2/style/weui.min.css" rel="stylesheet">
        <link rel="stylesheet" href="/Public/Home/Saleman/stylesheets/index.css">
        <link rel="stylesheet" href="/Public/Home/Saleman/stylesheets/dropload.css">
    </head>
    <body>
        <div class="clinicOrder">
            <header class = "all_header">
                <a class = "h_back" href="/Home/Saleman/Saleman/index"><img src="/Public/Home/Saleman/images/back_icon.png" alt=""></a>
                <span class = "h_title">诊所订单</span>
                <a class = "h_add" href="javascript:void(0)"></a>
            </header>
            <div class="weui-tab">
                <div class="weui-navbar">
                    <div class="weui-navbar__item weui-bar__item_on">
                        全部
                    </div>
                    <div class="weui-navbar__item">
                        未完成
                    </div>
                    <div class="weui-navbar__item">
                        已完成
                    </div>
                    <div class="weui-navbar__item">
                        退货
                    </div>
                </div>
                <div class="weui-tab__panel">
                    <div class="order_list first_order_list" style = "display: block;">

                    </div>
                    <div class="order_list second_order_list">
                    </div>
                    <div class="order_list third_order_list">
                    </div>
                    <div class="order_list four_order_list">
                    </div>
                </div>
            </div>
        </div>
        <script src="/Public/Home/Saleman/js/zepto.min.js"></script>
        <script src = "/Public/Home/Saleman/js/index.js"></script>
        <script src = "/Public/Home/Saleman/js/dropload.min.js"></script>
        <script>
            $(function () {
                var itemIndex = 0;
                var firstpage = 0;
                var secondpage = 0;
                var thirdpage = 0;
                var fourpage = 0;
                // dropload
                var dropload = $('.weui-tab__panel').dropload({
                    scrollArea: window,
                    loadDownFn: function (me) {
                        // 加载菜单一的数据
                        if (itemIndex == '0') {
                            $.ajax({
                                type: 'post',
                                url: '/Home/Saleman/Saleman/clinicOrder',
                                data: {status: '-1', page: firstpage},
                                dataType: 'json',
                                success: function (result) {
                                    firstpage++;
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
                                        str += '<div class="order">';
                                        str += '<div class="weui_title">';
                                        str += '<div class="order_id">';
                                        str += '<span>订单号：</span>';
                                        str += '<span>' + data[i]['id'] + '</span>';
                                        str += '</div>';
                                        str += '<div class="order_pre_status">';
                                        if (data[i]['status'] == 0) {
                                            str += '待处理';
                                        } else if (data[i]['status'] == 1) {
                                            str += '已完成';
                                        } else if (data[i]['status'] == 2) {
                                            str += '退货';
                                        } else {
                                        }

                                        str += '</div>';
                                        str += '</div>';
                                        str += '<div class="order_info">';
                                        str += '<a href="/Home/Saleman/Saleman/orderInfo/id/' + data[i]['id'] + '" class="weui-media-box weui-media-box_appmsg">';
                                        str += '<div class="weui-media-box__hd">';
                                        str += '<img class="weui-media-box__thumb" src="' + data[i]['clinic_pic'] + '" alt="">';
                                        str += '</div>';
                                        str += '<div class="weui-media-box__bd">';
                                        str += '<h4 class="weui-media-box__title">' + data[i]['clinic_name'] + '</h4>';
                                        str += '<p class = "order_date_clinic">' + data[i]['add_date'] + '</p>';
                                        str += '<p class="weui-media-box__desc"><span>总价：</span><span>' + data[i]['total_price'] + '</span>元</p>';
                                        str += '</div>';
                                        str += '</a>';
                                        str += '</div>';
                                        str += '</div>';
                                    }
                                    $('.first_order_list').append(str);
                                },
                                error: function (xhr, type) {
                                    alert('刷新失败');
                                    // 即使加载出错，也得重置
                                    me.resetload();
                                }
                            });
                            // 加载菜单二的数据
                        } else if (itemIndex == '1') {
                            $.ajax({
                                type: 'post',
                                url: '/Home/Saleman/Saleman/clinicOrder',
                                data: {status: '0', page: firstpage},
                                dataType: 'json',
                                success: function (result) {
                                    secondpage++;
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
                                        str += '<div class="order">';
                                        str += '<div class="weui_title">';
                                        str += '<div class="order_id">';
                                        str += '<span>订单号：</span>';
                                        str += '<span>' + data[i]['id'] + '</span>';
                                        str += '</div>';
                                        str += '<div class="order_pre_status">';
                                        if (data[i]['status'] == 0) {
                                            str += '待处理';
                                        } else if (data[i]['status'] == 1) {
                                            str += '已完成';
                                        } else if (data[i]['status'] == 2) {
                                            str += '退货';
                                        } else {
                                        }

                                        str += '</div>';
                                        str += '</div>';
                                        str += '<div class="order_info">';
                                        str += '<a href="/Home/Saleman/Saleman/orderInfo/id/' + data[i]['id'] + '" class="weui-media-box weui-media-box_appmsg">';
                                        str += '<div class="weui-media-box__hd">';
                                        str += '<img class="weui-media-box__thumb" src="' + data[i]['clinic_pic'] + '" alt="">';
                                        str += '</div>';
                                        str += '<div class="weui-media-box__bd">';
                                        str += '<h4 class="weui-media-box__title">' + data[i]['clinic_name'] + '</h4>';
                                        str += '<p class = "order_date_clinic">' + data[i]['add_date'] + '</p>';
                                        str += '<p class="weui-media-box__desc"><span>总价：</span><span>' + data[i]['total_price'] + '</span>元</p>';
                                        str += '</div>';
                                        str += '</a>';
                                        str += '</div>';
                                        str += '</div>';
                                    }
                                    $('.second_order_list').append(str);
                                },
                                error: function (xhr, type) {
                                    alert('刷新失败');
                                    // 即使加载出错，也得重置
                                    me.resetload();
                                }
                            });
                        } else if (itemIndex == '2') {
                            $.ajax({
                                type: 'post',
                                url: '/Home/Saleman/Saleman/clinicOrder',
                                data: {status: '1', page: firstpage},
                                dataType: 'json',
                                success: function (result) {
                                    thirdpage++;
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
                                        str += '<div class="order">';
                                        str += '<div class="weui_title">';
                                        str += '<div class="order_id">';
                                        str += '<span>订单号：</span>';
                                        str += '<span>' + data[i]['id'] + '</span>';
                                        str += '</div>';
                                        str += '<div class="order_pre_status">';
                                        if (data[i]['status'] == 0) {
                                            str += '待处理';
                                        } else if (data[i]['status'] == 1) {
                                            str += '已完成';
                                        } else if (data[i]['status'] == 2) {
                                            str += '退货';
                                        } else {
                                        }

                                        str += '</div>';
                                        str += '</div>';
                                        str += '<div class="order_info">';
                                        str += '<a href="/Home/Saleman/Saleman/orderInfo/id/' + data[i]['id'] + '" class="weui-media-box weui-media-box_appmsg">';
                                        str += '<div class="weui-media-box__hd">';
                                        str += '<img class="weui-media-box__thumb" src="' + data[i]['clinic_pic'] + '" alt="">';
                                        str += '</div>';
                                        str += '<div class="weui-media-box__bd">';
                                        str += '<h4 class="weui-media-box__title">' + data[i]['clinic_name'] + '</h4>';
                                        str += '<p class = "order_date_clinic">' + data[i]['add_date'] + '</p>';
                                        str += '<p class="weui-media-box__desc"><span>总价：</span><span>' + data[i]['total_price'] + '</span>元</p>';
                                        str += '</div>';
                                        str += '</a>';
                                        str += '</div>';
                                        str += '</div>';
                                    }
                                    $('.third_order_list').append(str);
                                },
                                error: function (xhr, type) {
                                    alert('刷新失败');
                                    // 即使加载出错，也得重置
                                    me.resetload();
                                }
                            });

                        } else if (itemIndex == '3') {
                            $.ajax({
                                type: 'post',
                                url: '/Home/Saleman/Saleman/clinicOrder',
                                data: {status: '2', page: firstpage},
                                dataType: 'json',
                                success: function (result) {
                                    fourpage++;
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
                                        str += '<div class="order">';
                                        str += '<div class="weui_title">';
                                        str += '<div class="order_id">';
                                        str += '<span>订单号：</span>';
                                        str += '<span>' + data[i]['id'] + '</span>';
                                        str += '</div>';
                                        str += '<div class="order_pre_status">';
                                        if (data[i]['status'] == 0) {
                                            str += '待处理';
                                        } else if (data[i]['status'] == 1) {
                                            str += '已完成';
                                        } else if (data[i]['status'] == 2) {
                                            str += '退货';
                                        } else {
                                        }

                                        str += '</div>';
                                        str += '</div>';
                                        str += '<div class="order_info">';
                                        str += '<a href="/Home/Saleman/Saleman/orderInfo/id/' + data[i]['id'] + '" class="weui-media-box weui-media-box_appmsg">';
                                        str += '<div class="weui-media-box__hd">';
                                        str += '<img class="weui-media-box__thumb" src="' + data[i]['clinic_pic'] + '" alt="">';
                                        str += '</div>';
                                        str += '<div class="weui-media-box__bd">';
                                        str += '<h4 class="weui-media-box__title">' + data[i]['clinic_name'] + '</h4>';
                                        str += '<p class = "order_date_clinic">' + data[i]['add_date'] + '</p>';
                                        str += '<p class="weui-media-box__desc"><span>总价：</span><span>' + data[i]['total_price'] + '</span>元</p>';
                                        str += '</div>';
                                        str += '</a>';
                                        str += '</div>';
                                        str += '</div>';
                                    }
                                    $('.four_order_list').append(str);
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
                    // 解锁
                    dropload.unlock();
                    dropload.noData(false);
                    // 重置
                    dropload.resetload();
                });
            })
        </script>
    </body>
</html>