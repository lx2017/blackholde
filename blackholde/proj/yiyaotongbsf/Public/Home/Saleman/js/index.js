$(function () {
    // tabbar
    var newMedicine = {
        init: function () {
            this.handleTab();
            this.handleClinic();
            this.handleWork();
            this.handleSign();
            this.handleOrder();
            this.handleApplicate();
            this.handleOrderMedice();
            this.handleJump();
            this.handlePush();
        },
        handleTab: function () {
            $('.weui-tabbar__item').on('click', function () {
                var index = $(this).index();

                switch (index) {
                    case 0:
                        $(this).find("span").removeClass("work_icon").addClass("work_active_icon");
                        $(".weui-tabbar__item").eq(1).find(".common_icon").removeClass("info_active_icon").addClass("info_icon");
                        $(".weui-tabbar__item").eq(2).find(".common_icon").removeClass("person_active_icon").addClass("person_icon");
                        break;
                    case 1:
                        $(this).find(".common_icon").removeClass("info_icon").addClass("info_active_icon");
                        $(".weui-tabbar__item").eq(0).find(".common_icon").removeClass("work_active_icon").addClass("work_icon");
                        $(".weui-tabbar__item").eq(2).find(".common_icon").removeClass("person_active_icon").addClass("person_icon");
                        break;
                    case 2:
                        $(this).find(".common_icon").removeClass("person_icon").addClass("person_active_icon");
                        $(".weui-tabbar__item").eq(1).find(".common_icon").removeClass("info_active_icon").addClass("info_icon");
                        $(".weui-tabbar__item").eq(0).find(".common_icon").removeClass("work_active_icon").addClass("work_icon");
                        break;
                }
                $(this).addClass('weui-bar__item_on').siblings('.weui-bar__item_on').removeClass('weui-bar__item_on');
                $(".info_nav").hide().eq(index).show();
            });
        },
        // 我的诊所
        handleClinic: function () {
            var showTooltips = $(".showTooltips"),
                    lead_tel = $(".lead_tel"),
                    lead_name = $(".lead_name"),
                    lead_adress = $(".lead_adress");

            $(".clinic").on("click", ".cli_del", function (e) {
                e.preventDefault();
                var title = $(this).attr('pos_title');
                var id = $(this).attr('pos_id');
                $('.confir_content').html(title);
                $('.weui-dialog__btn_primary').attr('pos_id', id);
                $("#iosDialog1").show(200);
            });
            // 添加诊所
            lead_tel.on('keyup', function () {
                var lead_name_num = lead_name.val().trim().length,
                        lead_adress_num = lead_adress.val().trim().length;
                if ($(this).val().trim().length != 0) {
                    showTooltips.removeClass("weui-btn_plain-disabled").addClass("show_btn");
                    $(".tel_del").show();
                } else if (lead_name_num == 0 && lead_adress_num == 0) {
                    showTooltips.addClass("weui-btn_plain-disabled").removeClass("show_btn");
                }
                ;

                if ($(this).val().trim().length == 0) {
                    $(".tel_del").hide();
                }
            });
            lead_name.on('keyup', function () {
                var lead_tel_num = lead_tel.val().trim().length,
                        lead_adress_num = lead_adress.val().trim().length;

                if ($(this).val().trim().length != 0) {
                    showTooltips.removeClass("weui-btn_plain-disabled").addClass("show_btn");
                    $(".name_del").show();
                } else if (lead_tel_num == 0 && lead_adress_num == 0) {
                    showTooltips.addClass("weui-btn_plain-disabled").removeClass("show_btn");
                }
                ;

                if ($(this).val().trim().length == 0) {
                    $(".name_del").hide();
                }
            });
            lead_adress.on('keyup', function () {
                var lead_tel_num = lead_tel.val().trim().length,
                        lead_name_num = lead_name.val().trim().length;

                if ($(this).val().trim().length != 0) {
                    showTooltips.removeClass("weui-btn_plain-disabled").addClass("show_btn");
                    $(".adress_del").show();
                } else if (lead_name_num == 0 && lead_tel_num == 0) {
                    showTooltips.addClass("weui-btn_plain-disabled").removeClass("show_btn");
                }
                ;

                if ($(this).val().trim().length == 0) {
                    $(".adress_del").hide();
                }
            });

            $(".tel_del").on("click", function () {
                $(".lead_tel").val('');
                $(this).hide();
            });
            $(".adress_del").on("click", function () {
                $(".lead_adress").val('');
                $(this).hide();
            });
            $(".name_del").on("click", function () {
                $(".lead_name").val('');
                $(this).hide();
            });
        },
        // 添加的内容
        addStr: function (val, date) {
            var uuid = Math.random().toFixed(4);
            // var str = '<label class="weui-cell weui-check__label" for="s11_'+uuid+'">\
            //               <div class="weui-cell__hd">\
            //                 <input type="checkbox" class="weui-check" name="checkbox1" id="s11_'+uuid+'">\
            //                 <i class="weui-icon-checked"></i>\
            //               </div>\
            //               <div class="weui-cell__bd" style = "flex: 2">\
            //                 <p class = "cur_intro">'+val+'</p>\
            //               </div>\
            //               <div class="weui-cell__bd cur_date">\
            //                 <p>'+date+'</p>\
            //               </div>\
            //           	</label>';
            var str = '<label class="weui-cell weui-check__label" for="s11_' + uuid + '">\
			              <div class="weui-cell__bd" style = "flex: 2">\
			                <p class = "cur_intro">' + val + '</p>\
			              </div>\
			              <div class="weui-cell__bd cur_date">\
			                <p>' + date + '</p>\
			              </div>\
			          	</label>';
            return str;
        },
        preStr: function (val, date) {
            var uuid = Math.random().toFixed(4);
            // var preStr = '<label class="weui-cell weui-check__label" for="s11_'+uuid+'">\
            //               <div class="weui-cell__hd">\
            //                 <input type="checkbox" class="weui-check" checked name="checkbox1" id="s11_'+uuid+'">\
            //                 <i class="weui-icon-checked"></i>\
            //               </div>\
            //               <div class="weui-cell__bd" style = "flex: 2">\
            //                 <p class = "cur_intro">'+val+'</p>\
            //               </div>\
            //               <div class="weui-cell__bd cur_date">\
            //                 <p>'+date+'</p>\
            //               </div>\
            //           	</label>';
            var preStr = '<label class="weui-cell weui-check__label" for="s11_' + uuid + '">\
			              <div class="weui-cell__bd" style = "flex: 2">\
			                <p class = "cur_intro">' + val + '</p>\
			              </div>\
			              <div class="weui-cell__bd cur_date">\
			                <p>' + date + '</p>\
			              </div>\
			          	</label>';
            return preStr;
        },
        // 日期转换
        changeDate: function (date) {
            date = new Date(date);

            let
            y = date.getFullYear();
            let
            m = date.getMonth() + 1;
            m = m < 10 ? ('0' + m) : m;
            let
            d = date.getDate();
            d = d < 10 ? ('0' + d) : d;
            let
            h = date.getHours();
            let
            minute = date.getMinutes();
            minute = minute < 10 ? ('0' + minute) : minute;
            let
            second = date.getSeconds();
            second = second < 10 ? ('0' + second) : minute;

            return (
                    m + '月' + d
                    )
        },
        // 工作安排
        handleWork: function () {
            var that = this;
            $('.work_argument').on('click', ".weui-navbar__item", function () {
                $(this).addClass('weui-bar__item_on').siblings('.weui-bar__item_on').removeClass('weui-bar__item_on');
                $(".weui-list").hide().eq($(this).index()).show();
            });

            //今日完成工作添加
            $(".cur_btn_w").on("click", function () {
                var content = $(".cur_ipt").val();
                if (content.length == 0) {
                    alert("请输入添加的内容");
                    return;
                }

                $.ajax({
                    url: "/Home/Saleman/Saleman/workArgument/op/add",
                    type: 'post',
                    data: {type: 1, content: content},
                    cache: false,
                    dataType: "json",
                    success: function (result) {
                        if (result['code'] != 1) {
                            alert(result['msg']);
                            return;
                        }
                        var data = result['data'];

                        var str = '';
                        str += '<label class="weui-cell weui-check__label" for="s11">';
                        str += '<div class="weui-cell__bd" style = "flex: 2">';
                        str += '<p class = "cur_intro">' + data['content'] + '</p>';
                        str += '</div>';
                        str += '<div class="weui-cell__bd cur_date">';
                        str += '<p>' + data['work_day'] + '</p>';
                        str += '</div>';
                        str += '</label>';

                        if ($('.today_no_work').length > 0) {
                            $('.today_no_work').remove();
                        }
                        if ($('.work_lists').length <= 0) {
                            var box = '<div class="weui-cells weui-cells_checkbox work_lists"></div>';
                            $('.today_box').append(box);
                        }

                        $(".work_lists").append(str);
                        $(".cur_ipt").val('');
                    }
                });
            });

            // 明日安排工作添加
            $(".next_btn_w").on("click", function () {
                var content = $(".next_ipt").val();
                if (content.length == 0) {
                    alert("请输入添加的内容");
                    return;
                }
                $.ajax({
                    url: "/Home/Saleman/Saleman/workArgument/op/add",
                    type: 'post',
                    data: {type: 2, content: content},
                    cache: false,
                    dataType: "json",
                    success: function (result) {
                        if (result['code'] != 1) {
                            alert(result['msg']);
                            return;
                        }
                        var data = result['data'];

                        var str = '';
                        str += '<label class="weui-cell weui-check__label" for="s1111">';
                        str += '<div class="weui-cell__bd" style = "flex: 2">';
                        str += '<p class = "next_intro">' + data['content'] + '</p>';
                        str += '</div>';
                        str += '<div class="weui-cell__bd cur_date">';
                        str += '<p>' + data['work_day'] + '</p>';
                        str += '</div>';
                        str += '</label>';

                        if ($('.tomorrow_no_work').length > 0) {
                            $('.tomorrow_no_work').remove();
                        }
                        if ($('.next_w_lists').length <= 0) {
                            var box = '<div class="weui-cells weui-cells_checkbox next_w_lists"></div>';
                            $('.tomorrow_box').append(box);
                        }

                        $(".next_w_lists").append(str);
                        $(".next_ipt").val('');
                    }
                });
            });
        },
        // 签到拜访,申请支持
        handleSign: function () {
            $(".sign_w").on("click", '.weui-navbar__item', function () {
                $(this).addClass('weui-bar__item_on').siblings('.weui-bar__item_on').removeClass('weui-bar__item_on');
                $(".sign_list").hide().eq($(this).index()).show();
            });


            // 申请支持
            $(".appli_ipt").on("keyup", function () {
                if ($(this).val().length == 0) {
                    $(".appli_confirm").addClass("weui-btn_plain-disabled").removeClass("show_btn");
                    $(".appli_confirm").unbind('click');
                } else {
                    $(".appli_confirm").addClass("show_btn").removeClass("weui-btn_plain-disabled");
                    bindAddApply();
                }
            });
            function bindAddApply() {
                $(".appli_confirm").unbind('click').bind('click', function () {
                    var title = $(this).attr('pos_title');
                    $('.confir_content').html(title);
                    $("#iosDialog1").show(200);
                });
            }
        },
        // 诊所订单
        handleOrder: function () {
            $(".clinicOrder").on("click", ".weui-navbar__item", function () {
                $(this).addClass('weui-bar__item_on').siblings('.weui-bar__item_on').removeClass('weui-bar__item_on');
                $(".order_list").hide().eq($(this).index()).show();
            });
            // 我的业务员
            $(".mySaleMan").on("click", '.sale_del', function () {
                $(this).parents(".weui-cell").remove();
            });

            // 审批纪录
            $(".approvalRecord").on("click", ".weui-navbar__item", function () {
                $(this).addClass('weui-bar__item_on').siblings('.weui-bar__item_on').removeClass('weui-bar__item_on');
                $(".record_list").hide().eq($(this).index()).show();
            });
            // 支持审批
            $(".supportRecord").on("click", ".weui-navbar__item", function () {
                $(this).addClass('weui-bar__item_on').siblings('.weui-bar__item_on').removeClass('weui-bar__item_on');
                $(".record_list").hide().eq($(this).index()).show();
            });
            // 订单详情
            $(".orderInfo").on("click", ".order_del", function () {
                var totalNum = Number($(".total_num").html()),
                        listNum = Number($(this).parents(".order_list").find(".all_num").html());
                totalNum = totalNum - listNum;
                totalNum = totalNum.toFixed(2);

                $(this).parents(".order_list").remove();
                $(".total_num").html(totalNum);
            });
            // 提交订单
            $(".setelOrder").on("click", ".order_del", function () {
                var title = $(this).attr('pos_title');
                var pos_id = $(this).attr('pos_id');
                $('.confir_content').html(title);
                $('.ok-btn').attr('pos_id', pos_id);
                $('.ok-btn').attr('pos_op', 'delete');
                $("#iosDialog1").show(200);
            });
        },
        // 申请支持，添加目标诊所，找回密码,登录
        handleApplicate: function () {
            $(".applicate_head").on("click", function () {
                $(".other_meet").toggle();
            });

            $(".other_meet").on("click", ".meet_opt", function () {
                var val = $(this).html();
                $(".applicateMeet_w .weui-cell__ft").html(val);
            });

            // 目标诊所
            $(".add_btn").on("click", function () {
                var clinicName = $(".clinicName").val(),
                        clinicPeople = $(".clinicPeople").val(),
                        clinicTel = $(".clinicTel").val(),
                        clinicAdress = $(".clinicAdress").val();

                if (clinicName.trim().length == 0) {
                    $("#confirm_dialog").show().find(".weui-dialog__bd").html("请输入姓名");
                }
            });
            $(".weui-dialog__btn_primary").on("click", function () {
                $("#confirm_dialog").hide();
            });
            // 找回密码
            $(".modify_btn").on("click", function () {
                var tel = $(".find_tel").val(),
                        pwd = $(".find_pwd").val();

                if (tel.trim().length == 0) {
                    $("#find_tips_dialog").show().find(".weui-dialog__bd").html("请输入手机号");
                }
                ;
                if (pwd.trim().length == 0) {
                    $("#find_tips_dialog").show().find(".weui-dialog__bd").html("请输入密码");
                }
                ;
            });

            $(".find_btns").on("click", function () {
                $("#find_tips_dialog").hide();
            });

            // 登录
            $(".login_btn").on("click", function () {
                var tel = $(".login_tel").val(),
                        pwd = $(".login_pwd").val();

                if (tel.trim().length == 0) {
                    $("#login_dialog").show().find(".weui-dialog__bd").html("请输入手机号");
                }

                if (pwd.trim().length == 0) {
                    $("#login_dialog").show().find(".weui-dialog__bd").html("请输入密码");
                }
            });

            $(".login_dia_btns").on("click", function () {
                $("#login_dialog").hide();
            });
            var isShow = true;
            $(".pas_show").on("click", function () {
                if (isShow) {
                    isShow = false;
                    $(".login_pwd").prop("type", "text");
                } else {
                    isShow = true;
                    $(".login_pwd").prop("type", "password");
                }
            });
        },
        // 订购药品
        orderStr: function (name, price) {
            var orderStr = '<div class = "order_medices_w">\
								        <div style = "margin-top: 8px;">' + name + '</div>\
								        <div class = "carts_price" style = "margin-top: 8px;">' + price + '</div>\
								        <div>\
								          <span class = "order_reduce">-</span>\
								          <span class = "order_counts">1</span>\
								          <span class = "order_add">+</span>\
								        </div>\
								      </div>';
            return orderStr;
        },
        handleOrderMedice: function () {
            var that = this,
                    allPrice = $(".cart_all_price"),
                    allPrices = $(".allPrices"),
                    good_num = $(".good_num"),
                    num = 0;

            $(".orderMedicine").on("click", ".add_cart", function (e) {
                e.preventDefault();
                var title = $(this).attr('pos_title');
                var pos_id = $(this).attr('pos_id');
                $('.confir_content').html(title);
                $('.ok-btn').attr('pos_id', pos_id);
                $("#iosDialog1").show(200);
            });

            $(".orderMedicine").on("click", ".order_reduce", function () {
                var sibling = $(this).parents(".order_medices_w").find(".order_counts"),
                        val = Number(sibling.html()),
                        price = $(this).parents(".order_medices_w").find(".carts_price").html(),
                        allPriceNum = Number(allPrice.html()) - Number(price);

                val -= 1;
                if (val >= 0) {
                    sibling.html(val);
                    allPrice.html(allPriceNum);
                    allPrices.html(allPriceNum);
                    num -= 1;
                    good_num.html(num);
                }
                ;
                if (val == 0) {
                    $(this).parents(".order_medices_w").remove();
                }
            });
            $(".orderMedicine").on("click", ".order_add", function () {
                var sibling = $(this).parents(".order_medices_w").find(".order_counts"),
                        val = parseInt(sibling.html()),
                        price = $(this).parents(".order_medices_w").find(".carts_price").html(),
                        allPriceNum = Number(allPrice.html()) + Number(price);
                allPrice.html(allPriceNum);
                allPrices.html(allPriceNum);

                val += 1;
                sibling.html(val);
                num += 1;
                good_num.html(num);
            });
            $(".cart_price").on("click", function () {
                $(".cart_modal").toggle();
            });


            // 目标诊所
            $(".targetClinic_w").on("click", ".clinic_del", function () {
                $(this).parents(".weui-media-box").remove();
            })
        },
        handlePush: function () {
            $(".pushHistory").on("click", ".weui-navbar__item", function () {
                $(this).addClass('weui-bar__item_on').siblings('.weui-bar__item_on').removeClass('weui-bar__item_on');
                $(".push_list").hide().eq($(this).index()).show();
            });
            $(".pushHistory").on("click", ".nav_btn", function () {
                $(this).parents(".weui-panel").remove();
            })
        },
        // 跳转
        handleJump: function () {
            $(".mySaleMan").on("click", ".saleSingle", function () {
                window.location.href = '../xianzong1/workPlan.html';
            });
            $(".clinic").on("click", ".clinic_img", function () {
                window.location.href = "../xianzong1/clinicHome.html";
            });
            $(".approvalRecord").on("click", ".record_w", function () {
                window.location.href = '../xianzong1/approvalInfo.html';
            });
            $(".mySaleMan").on("click", '.change_saleman', function () {
                window.location.href = "../xianzong1/changeSale.html";
            });
            $(".mySaleMan").on("click", '.sale_imgs', function () {
                window.location.href = "../xianzong1/saleInfos.html";
            });
            $(".workAllSumary").on("click", ".work_header", function () {
                window.location.href = "../xianzong1/saleInfos.html";
            });
            $(".visitClinic").on("click", ".cli_times", function () {
                window.location.href = "../yewuyuan/visitAllTimes.html";
            });
        },
    }

    newMedicine.init();
})